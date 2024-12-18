var CACHE_NAME = 'pwgen-cache-v1';
var urlsToCache = [
    'sw.js',
    'manifest.json',
    './frontend/css/style.css',
    './images/logo.png',
    './icon-192x192.png',
    './icon-256x256.png',
    './icon-384x384.png',
    './icon-512x512.png',
    'https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css',
    'https://fonts.googleapis.com/css?family=Karla:400,700',
    'https://fonts.googleapis.com/css?family=Rubik:500',
    'https://cdn.jsdelivr.net/npm/pretty-checkbox@3.0/dist/pretty-checkbox.min.css',
    'https://cdn.jsdelivr.net/npm/flatpickr',
];
// console.log('loading sw');

self.addEventListener('install', function(event) {
    // Perform install steps
    // console.log('installing sw');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                // console.log('Opened cache');
                var x = cache.addAll(urlsToCache);
                // console.log('cache added');
                return x;
            })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                    // Cache hit - return response
                    if (response) {
                        return response;
                    }
                    return fetch(event.request);
                }
            )
    );
});