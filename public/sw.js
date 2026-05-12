const CACHE_NAME = 'jakpos-cache-v2';
const STATIC_URLS = [
    '/',
    '/pos',
    '/manifest.json',
    'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js'
];

self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(STATIC_URLS);
        })
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

self.addEventListener('fetch', event => {
    if (event.request.method !== 'GET') return;

    const url = new URL(event.request.url);

    // Skip API calls completely so offline queuing can catch them
    if (url.pathname.startsWith('/api/')) return;

    // Skip external tracking beacons/scripts to prevent Promise rejection errors
    if (url.hostname.includes('cloudflareinsights.com') || url.hostname.includes('google-analytics.com')) {
        return;
    }

    // Cache First for Build Assets & Fonts
    if (url.pathname.startsWith('/build/assets/') || url.hostname === 'fonts.googleapis.com' || url.hostname === 'fonts.gstatic.com') {
        event.respondWith(
            caches.match(event.request).then(cachedResponse => {
                if (cachedResponse) return cachedResponse;
                return fetch(event.request).then(response => {
                    if (response.status === 200) {
                        const responseToCache = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseToCache));
                    }
                    return response;
                }).catch(() => {});
            })
        );
        return;
    }

    // Network First, Fallback to Cache for POS and Pages
    event.respondWith(
        fetch(event.request).then(response => {
            if (response.status === 200) {
                const responseToCache = response.clone();
                caches.open(CACHE_NAME).then(cache => cache.put(event.request, responseToCache));
            }
            return response;
        }).catch(async () => {
            const cachedResponse = await caches.match(event.request);
            if (cachedResponse) return cachedResponse;
            // If offline and requesting /pos, try to serve /pos explicitly
            if (url.pathname === '/pos') {
                return caches.match('/pos');
            }
        })
    );
});
