// Open tabs may still reference asset filenames from the previous deployment.
// Reload the requested URL once, without looping on a persistent network error.
export const installChunkRecovery = () => {
    window.addEventListener('vite:preloadError', (event) => {
        try {
            const key = 'storefront-chunk-reload';
            const lastReload = Number(sessionStorage.getItem(key) || 0);
            if (Date.now() - lastReload < 30_000) return;
            sessionStorage.setItem(key, String(Date.now()));
            event.preventDefault();
            window.location.reload();
        } catch {
            // If storage is unavailable, let the page boundary offer a manual reload.
        }
    });
};
