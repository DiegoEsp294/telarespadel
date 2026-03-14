const CACHE = 'telares-v2';
const BASE  = self.registration.scope;
const OFFLINE_URL = BASE;

/* ===== INSTALL ===== */
self.addEventListener('install', e => {
    e.waitUntil(
        caches.open(CACHE).then(c => c.addAll([BASE]))
    );
    self.skipWaiting();
});

/* ===== ACTIVATE ===== */
self.addEventListener('activate', e => {
    e.waitUntil(
        caches.keys().then(keys =>
            Promise.all(keys.filter(k => k !== CACHE).map(k => caches.delete(k)))
        )
    );
    self.clients.claim();
});

/* ===== FETCH (network first, cache fallback) ===== */
self.addEventListener('fetch', e => {
    if (e.request.method !== 'GET') return;
    e.respondWith(
        fetch(e.request).catch(() => caches.match(e.request).then(r => r || caches.match(OFFLINE_URL)))
    );
});

/* ===== PUSH NOTIFICATION ===== */
self.addEventListener('push', e => {
    let data = { title: 'Telares Padel', body: 'Tenés una novedad!', url: '/torneos-telares-padel/' };
    try { data = Object.assign(data, e.data.json()); } catch(err) {}

    e.waitUntil(
        self.registration.showNotification(data.title, {
            body:    data.body,
            icon:    '/torneos-telares-padel/logo_inicio.png',
            badge:   '/torneos-telares-padel/logo_inicio.png',
            vibrate: [200, 100, 200],
            data:    { url: data.url },
            actions: [{ action: 'ver', title: 'Ver torneo' }]
        })
    );
});

/* ===== CLICK en notificación ===== */
self.addEventListener('notificationclick', e => {
    e.notification.close();
    const url = (e.notification.data && e.notification.data.url) || '/torneos-telares-padel/';
    e.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(list => {
            for (const c of list) {
                if (c.url === url && 'focus' in c) return c.focus();
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});
