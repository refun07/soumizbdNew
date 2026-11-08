type MetaPixelState = {
    pixels?: Array<{ id?: string }>;
};

type MetaPixelQueue = {
    getState?: () => MetaPixelState;
    set?: (setting: string, pixelId: string, value: unknown) => void;
};

const GTM_MANAGED_HOSTNAMES = new Set([
    '127.0.0.1',
    'localhost',
    '::1',
    'soumisbangladesh.com',
    'www.soumisbangladesh.com',
]);

/**
 * This storefront manages browser events through GTM. Clear stale Meta Event
 * Setup Tool rules on the known storefront hosts so Meta cannot send a second
 * codeless copy of AddToCart, Purchase, ViewContent or InitiateCheckout.
 */
export const disableMetaEventSetupRules = () => {
    if (!GTM_MANAGED_HOSTNAMES.has(window.location.hostname)) return;

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
                '[Meta Pixel] Event Setup Tool rules disabled; GTM events remain enabled.',
            );
        }
    };

    clearRules();

    // Meta loads the Pixel and remote rule configuration asynchronously. Keep
    // clearing briefly after initialization so a late config response cannot
    // restore the stale rules.
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
