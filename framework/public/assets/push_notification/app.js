/*
@copyright

Fleet Manager v6.5

Copyright (C) 2017-2023 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

document.addEventListener("DOMContentLoaded", () => {
    const applicationServerKey = "BKt+swntut+5W32Psaggm4PVQanqOxsD5PRRt93p+/0c+7AzbWl87hFF184AXo/KlZMazD5eNb1oQVNbK1ti46Y=";

    if (Notification.permission !== 'denied' && Notification.permission === "default") {
        // console.log(Notification.permission);
        Notification.requestPermission(function (permission) {
            if (permission === "granted") {
                // console.log("push_subscribe");
                push_subscribe();
            }
            else {
                // console.log("push_unsubscribe");
                push_unsubscribe();
            }
        });
    }
    else if (Notification.permission === "granted") {
        // console.log(Notification.permission);
        push_updateSubscription();
    }
    else {
        // console.log(Notification.permission);
        push_unsubscribe();
    }


    if (!('serviceWorker' in navigator)) {
        console.warn("Service workers are not supported by this browser");
        return;
    }

    if (!('PushManager' in window)) {
        console.warn('Push notifications are not supported by this browser');
        return;
    }

    if (!('showNotification' in ServiceWorkerRegistration.prototype)) {
        console.warn('Notifications are not supported by this browser');
        return;
    }
    if (Notification.permission === 'denied') {
        console.warn('Notifications are denied by the user');
        return;
    }

    navigator.serviceWorker.register(window.Laravel.serviceWorkerUrl)
        .then(() => {
            console.log('[SW] Service worker has been registered');
        }, e => {
            console.error('[SW] Service worker registration failed', e);
        });

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    function push_subscribe() {
        // console.log("push_subscribe start");
        // console.log(navigator.serviceWorker.ready);

        navigator.serviceWorker.ready
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(applicationServerKey),
            }))
            .then(subscription => {
                // console.log(subscription);
                return push_sendSubscriptionToServer(subscription, 'POST');
            })
            .then(subscription => subscription) // update your UI
            .catch(e => {
                if (Notification.permission === 'denied') {
                    // The user denied the notification permission which means we failed to subscribe and the user 
                    // will need to manually change the notification permission to subscribe to push messages
                    console.warn('Notifications are denied by the user.');
                } else {
                    // A problem occurred with the subscription; common reasons
                    // include network errors or the user skipped the permission
                    console.error('Impossible to subscribe to push notifications', e);
                }
            });

        // console.log("push_subscribe end");
    }

    function push_updateSubscription() {
        // console.log("push_updateSubscription");
        // console.log(window.$("#loggedinuser").val());
        navigator.serviceWorker.ready.then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
            .then(subscription => {

                if (!subscription) {
                    // We aren't subscribed to push, so set UI to allow the user to enable push
                    return;
                }

                // Keep your server in sync with the latest endpoint
                return push_sendSubscriptionToServer(subscription, 'PUT');
            })
            .then(subscription => subscription) // Set your UI to show they have subscribed for push messages
            .catch(e => {
                console.error('Error when updating the subscription', e);
            });
    }

    function push_unsubscribe() {

        // To unsubscribe from push messaging, you need to get the subscription object
        navigator.serviceWorker.ready
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
            .then(subscription => {
                // Check that we have a subscription to unsubscribe
                if (!subscription) {
                    // No subscription object, so set the state to allow the user to subscribe to push
                    return;
                }

                // We have a subscription, to unsubscribe Remove push subscription from server
                return push_sendSubscriptionToServer(subscription, 'DELETE');
            })
            .then(subscription => subscription.unsubscribe())
            .then(() => changePushButtonState('disabled'))
            .catch(e => {
                // We failed to unsubscribe, this can lead to an unusual state, so  it may be best to remove
                // the users data from your data store and inform the user that you have done so
                console.error('Error when unsubscribing the user', e);
            });
    }

    function push_sendSubscriptionToServer(subscription, method) {
        // console.log("push_sendSubscriptionToServer");
        const loggedinuser = window.$("#loggedinuser").val();
        const user_type = window.$("#user_type").val();
        const key = subscription.getKey('p256dh');
        const token = subscription.getKey('auth');
        const contentEncoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];

        return fetch(window.Laravel.subscription_url, {
            method,
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                publicKey: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                authToken: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
                contentEncoding,
                loggedinuser,
                user_type

            }),
        }).then(() => subscription);
    }

   

});
