const CACHE_NAME = 'gem-opticals-v6';
const STATIC_CACHE = 'gem-static-v6';
const API_CACHE = 'gem-api-v6';

const STATIC_URLS = [
  '/',
  '/css/app.css?v=6',
  '/css/admin.css',
  '/js/admin.js',
  '/favicon.ico',
  '/manifest.json',
  '/images/icon-192.png',
  '/images/icon-512.png',
  '/images/apple-touch-icon.png',
  '/category/eyeglasses',
  '/category/sunglasses',
  '/category/kids',
  '/category/contact_lenses',
  '/category/accessories',
];

self.addEventListener('install', (event) => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(STATIC_CACHE).then((cache) =>
      Promise.allSettled(STATIC_URLS.map((url) => cache.add(url)))
    )
  );
});

self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((keys) =>
      Promise.all(
        keys
          .filter((k) => k !== STATIC_CACHE && k !== API_CACHE && k !== CACHE_NAME)
          .map((k) => caches.delete(k))
      )
    ).then(() => self.clients.claim())
  );
});

self.addEventListener('message', (event) => {
  if (event.data === 'SKIP_WAITING') self.skipWaiting();
});

self.addEventListener('fetch', (event) => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  if (url.pathname.startsWith('/api/v1/')) {
    event.respondWith(networkFirstApi(event.request));
    return;
  }

  if (event.request.mode === 'navigate') {
    event.respondWith(networkFirstPage(event.request));
    return;
  }

  event.respondWith(staticAsset(event.request));
});

async function staticAsset(request) {
  const cached = await caches.match(request);
  if (cached) return cached;

  try {
    const response = await fetch(request);
    if (response && response.ok) {
      const clone = response.clone();
      caches.open(STATIC_CACHE)
        .then((cache) => cache.put(request, clone))
        .catch(() => {});
    }
    return response;
  } catch (err) {
    const fallback = await caches.match(request);
    return fallback || new Response('', { status: 503, statusText: 'Offline' });
  }
}

async function networkFirstApi(request) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const clone = response.clone();
      caches.open(API_CACHE)
        .then((cache) => cache.put(request, clone))
        .catch(() => {});
    }
    return response;
  } catch (err) {
    const cached = await caches.match(request);
    if (cached) return cached;
    return new Response(
      JSON.stringify({ data: [], meta: { total: 0 }, offline: true }),
      { headers: { 'Content-Type': 'application/json' } }
    );
  }
}

async function networkFirstPage(request) {
  try {
    const response = await fetch(request);
    if (response.ok) {
      const clone = response.clone();
      caches.open(CACHE_NAME)
        .then((cache) => cache.put(request, clone))
        .catch(() => {});
    }
    return response;
  } catch (err) {
    const cached = await caches.match(request);
    if (cached) return cached;
    return new Response(
      '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:40px;text-align:center"><h2>No Internet Connection</h2><p>Please connect to the network to view the store.</p></body></html>',
      { headers: { 'Content-Type': 'text/html;charset=UTF-8' } }
    );
  }
}
