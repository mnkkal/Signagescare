/**
 * Signage Care CRM - Offline Queue & Auto-Sync (IndexedDB)
 * Queues ticket updates, check-ins, safety checklists, and photo uploads when offline.
 */
(function () {
    const DB_NAME = 'signagecare_offline_db';
    const STORE_NAME = 'sync_queue';

    function openDb() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(DB_NAME, 1);
            req.onupgradeneeded = (e) => {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(STORE_NAME)) {
                    db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
                }
            };
            req.onsuccess = () => resolve(req.result);
            req.onerror = () => reject(req.error);
        });
    }

    async function getQueuedItems() {
        const db = await openDb();
        return new Promise((resolve) => {
            const tx = db.transaction(STORE_NAME, 'readonly');
            const req = tx.objectStore(STORE_NAME).getAll();
            req.onsuccess = () => resolve(req.result || []);
            req.onerror = () => resolve([]);
        });
    }

    async function addToQueue(item) {
        const db = await openDb();
        return new Promise((resolve, reject) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            tx.objectStore(STORE_NAME).add(item);
            tx.oncomplete = () => resolve();
            tx.onerror = () => reject(tx.error);
        });
    }

    async function removeFromQueue(id) {
        const db = await openDb();
        return new Promise((resolve) => {
            const tx = db.transaction(STORE_NAME, 'readwrite');
            tx.objectStore(STORE_NAME).delete(id);
            tx.oncomplete = () => resolve();
        });
    }

    async function updateBanner() {
        const banner = document.getElementById('offline-sync-banner');
        const statusText = document.getElementById('offline-sync-text');
        if (!banner || !statusText) return;

        const items = await getQueuedItems();
        if (!navigator.onLine) {
            banner.classList.remove('hidden');
            statusText.textContent = `Offline Mode: ${items.length} update(s) queued. Will auto-sync when signal returns.`;
        } else if (items.length > 0) {
            banner.classList.remove('hidden');
            statusText.textContent = `Connection restored — syncing ${items.length} queued update(s)...`;
        } else {
            banner.classList.add('hidden');
        }
    }

    async function flushQueue() {
        if (!navigator.onLine) return;
        const items = await getQueuedItems();
        if (items.length === 0) return;

        for (const item of items) {
            try {
                const formData = new FormData();
                for (const [k, v] of Object.entries(item.fields)) {
                    formData.append(k, v);
                }
                if (item.files) {
                    for (const [k, f] of Object.entries(item.files)) {
                        const blob = new Blob([f.buffer], { type: f.type });
                        formData.append(k, blob, f.name);
                    }
                }

                const res = await fetch(item.action, {
                    method: item.method || 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (res.ok || res.redirected) {
                    await removeFromQueue(item.id);
                }
            } catch (e) {
                console.warn('Offline sync retry scheduled:', e);
            }
        }
        await updateBanner();
    }

    // Intercept offline POST forms marked with data-offline-sync
    document.addEventListener('submit', async (e) => {
        const form = e.target;
        if (!form || !form.matches('form[data-offline-sync]') || navigator.onLine) return;

        e.preventDefault();
        const fd = new FormData(form);
        const fields = {};
        const files = {};

        for (const [k, v] of fd.entries()) {
            if (v instanceof File && v.size > 0) {
                const buffer = await v.arrayBuffer();
                files[k] = { name: v.name, type: v.type, buffer };
            } else if (typeof v === 'string') {
                fields[k] = v;
            }
        }

        await addToQueue({
            action: form.action,
            method: (form.method || 'POST').toUpperCase(),
            fields,
            files,
            createdAt: new Date().toISOString(),
        });

        await updateBanner();
        alert('You are offline. Your update & photo have been saved locally and will auto-sync when signal returns.');
    });

    window.addEventListener('online', async () => {
        await updateBanner();
        await flushQueue();
    });
    window.addEventListener('offline', updateBanner);
    document.addEventListener('DOMContentLoaded', async () => {
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js').catch(() => {});
        }
        await updateBanner();
        await flushQueue();
    });
})();
