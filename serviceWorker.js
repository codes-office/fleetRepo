self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const sendNotification = data => {
       // you could refresh a notification badge here with postMessage API
       var object = JSON.parse(data);
       const title = object.title;
       const icon = object.img;
       const body = object.body;
       const url = object.url;
        return self.registration.showNotification(title, {     
            icon,
            body,   
            data:{

            url 
            }       
        });
    };

    if (event.data) {
        const message = event.data.text();
        event.waitUntil(sendNotification(message));
    }
});

self.addEventListener('notificationclick', function (event) {
    
    event.notification.close();
  event.waitUntil(
    clients.openWindow(event.notification.data.url)
    );
  // console.log(event.notification.data.url);
});

