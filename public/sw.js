self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('habitpi-v1').then((cache) => {
            return cache.addAll([
                '/',
                '/login',
                '/css/app.css',
                '/js/app.js',
                '/logo.png'
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
