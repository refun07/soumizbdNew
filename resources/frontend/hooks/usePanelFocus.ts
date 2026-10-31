import { RefObject, useEffect } from 'react';

/** Keep keyboard navigation in an open drawer and restore its trigger on close. */
export function usePanelFocus(active: boolean, panel: RefObject<HTMLElement>) {
    useEffect(() => {
        if (!active) return;
        const trigger = document.activeElement as HTMLElement | null;
        const items = () => Array.from(panel.current?.querySelectorAll<HTMLElement>(
            'button:not([disabled]),a[href],input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex="0"]',
        ) || []).filter(node => node.getClientRects().length > 0 && getComputedStyle(node).visibility !== 'hidden');
        const focusFirst = () => (items()[0] || panel.current)?.focus({ preventScroll: true });
        focusFirst();
        const frame = requestAnimationFrame(focusFirst);
        // A drawer may still inherit hidden visibility while its opening transition starts.
        const openingFocus = window.setTimeout(focusFirst, 200);
        const trap = (event: KeyboardEvent) => {
            if (event.key !== 'Tab') return;
            const controls = items(), first = controls[0], last = controls[controls.length - 1];
            if (!first) { event.preventDefault(); panel.current?.focus(); return; }
            if (!panel.current?.contains(document.activeElement)) { event.preventDefault(); (event.shiftKey ? last : first).focus(); }
            else if (event.shiftKey && (document.activeElement === first || document.activeElement === panel.current)) { event.preventDefault(); last.focus(); }
            else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
        };
        document.addEventListener('keydown', trap);
        return () => {
            cancelAnimationFrame(frame);
            window.clearTimeout(openingFocus);
            document.removeEventListener('keydown', trap);
            if (trigger?.isConnected) trigger.focus({ preventScroll: true });
        };
    }, [active, panel]);
}
