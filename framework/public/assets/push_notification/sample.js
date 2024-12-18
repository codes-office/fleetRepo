/*
@copyright

Fleet Manager v6.1

Copyright (C) 2017-2022 Hyvikk Solutions <https://hyvikk.com/> All rights reserved.
Design and developed by Hyvikk Solutions <https://hyvikk.com/>

 */

document.addEventListener("DOMContentLoaded", () => {
    const applicationServerKey = window.Laravel.vapidPublicKey;
    if (Notification.permission !== 'denied' && Notification.permission === "default") {
        push_subscribe();
    } else if (Notification.permission === "granted") {
        push_updateSubscription();
    } else {
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
    navigator.serviceWorker.register(window.Laravel.sw_url)
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
        navigator.serviceWorker.ready
            .then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(applicationServerKey),
            }))
            .then(subscription => {
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
    }

    function push_updateSubscription() {
        navigator.serviceWorker.ready.then(serviceWorkerRegistration => serviceWorkerRegistration.pushManager.getSubscription())
            .then(subscription => {
                if (!subscription) {
                    // We aren't subscribed to push, so set UI to allow the user to enable push
                    return;
                }
                // Keep your server in sync with the latest endpoint
                return push_sendSubscriptionToServer(subscription, 'PATCH');
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
                if (!subscription) {
                    return;
                }
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
        const key = subscription.getKey('p256dh');
        const token = subscription.getKey('auth');
        const contentencoding = (PushManager.supportedContentEncodings || ['aesgcm'])[0];
        return fetch(window.Laravel.notification_url, {
            method,
            body: JSON.stringify({
                endpoint: subscription.endpoint,
                public_key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
                auth_token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
                contentencoding,
            }),
        }).then(() => subscription);
    }
});