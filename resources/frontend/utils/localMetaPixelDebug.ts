type MetaPixelState = {
    pixels?: Array<{ id?: string }>;
};

type MetaPixelQueue = {
    getState?: () => MetaPixelState;
    set?: (setting: string, pixelId: string, value: unknown) => void;
};

const LOCAL_HOSTNAMES = new Set(['127.0.0.1', 'localhost', '::1']);

/**
 * Meta Event Setup Tool rules are stored against the Pixel ID, so its button
 * rules also run on localhost. Clear only those codeless rules locally while
 * keeping the GTM-managed browser events enabled for testing.
 */
export const disableLocalMetaEventSetupRules = () => {
    if (!LOCAL_HOSTNAMES.has(window.location.hostname)) return;

    let successfulClears = 0;
    let logged = false;

    const clearRules = () => {
        const fbq = (window as typeof window & { fbq?: MetaPixelQueue }).fbq;

        if (typeof fbq?.getState !== 'function' || typeof fbq.set !== 'function') {
            return;
        }

        const pixelIds = (fbq.getState().pixels || [])
            .map((pixel) => pixel.id)
            .filter((pixelId): pixelId is string => Boolean(pixelId));

        if (!pixelIds.length) return;

        pixelIds.forEach((pixelId) => {
            fbq.set?.('estRules', pixelId, []);
        });

        successfulClears += 1;

        if (!logged) {
            logged = true;
            console.info(
                '[Meta Pixel] Event Setup Tool rules disabled on localhost; GTM events remain enabled.',
            );
        }
    };

    clearRules();

    // The Pixel and its remote configuration load asynchronously. Keep
    // clearing briefly after initialization so the remote rules cannot win a
    // race with the local development bootstrap.
    const intervalId = window.setInterval(() => {
        clearRules();

        if (successfulClears >= 100) {
            window.clearInterval(intervalId);
            window.clearTimeout(timeoutId);
        }
    }, 100);

    const timeoutId = window.setTimeout(() => {
        window.clearInterval(intervalId);
    }, 30_000);
};
