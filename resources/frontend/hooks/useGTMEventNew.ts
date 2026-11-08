import { useCallback } from 'react';
import {
    GTMCustomerContext,
    GTMUserData,
} from '../utils/gtmCustomerData';

declare global {
    interface Window {
        dataLayer: any[];
    }
}

/**
 * Ecommerce item structure following GA4 + Meta best practices
 */
export type EcommerceItem = {
    item_id: string; // SKU or product ID
    item_name: string;
    price?: number;
    quantity?: number;
    [key: string]: any;
};

type GTMBaseEventParams = {
    customer?: GTMCustomerContext;
    user_data?: GTMUserData;
    [key: string]: any;
};

/**
 * Full Ecommerce GTM Event Types (Complete Funnel)
 */
export type GTMEventParams =
    | ({
          event: 'page_view';
          page_location?: string;
          page_referrer?: string;
      } & GTMBaseEventParams)
    | ({
          event: 'view_item';
          ecommerce: {
              currency: string;
              value?: number;
              items: EcommerceItem[];
          };
      } & GTMBaseEventParams)
    | ({
          event: 'view_item_list';
          ecommerce: {
              item_list_id?: string;
              item_list_name?: string;
              items: EcommerceItem[];
          };
      } & GTMBaseEventParams)
    | ({
          event: 'add_to_cart';
          ecommerce: {
              currency: string;
              value?: number;
              items: EcommerceItem[];
          };
      } & GTMBaseEventParams)
    | ({
          event: 'remove_from_cart';
          ecommerce: {
              currency: string;
              value?: number;
              items: EcommerceItem[];
          };
      } & GTMBaseEventParams)
    | ({
          event: 'add_to_wishlist';
          ecommerce: {
              items: EcommerceItem[];
          };
      } & GTMBaseEventParams)
    | ({
          event: 'remove_from_wishlist';
          ecommerce: {
              items: EcommerceItem[];
          };
      } & GTMBaseEventParams)
    | ({
          event: 'begin_checkout';
          ecommerce: {
              currency: string;
              value: number;
              items: EcommerceItem[];
          };
      } & GTMBaseEventParams)
    | ({
          event: 'add_payment_info';
          ecommerce: {
              currency: string;
              value: number;
              items: EcommerceItem[];
              payment_type?: string;
          };
      } & GTMBaseEventParams)
    | ({
          event: 'purchase';
          event_id?: string;
          ecommerce: {
              transaction_id: string;
              currency: string;
              value: number;
              tax?: number;
              shipping?: number;
              coupon?: string;
              items: EcommerceItem[];
          };
      } & GTMBaseEventParams)
    | ({
          event: 'search';
          search_term: string;
      } & GTMBaseEventParams);

/**
 * useGTMEvents — Unified Ecommerce Event Dispatcher for GTM
 */
const pushedEventIds = new Set<string>();

/**
 * Send the browser-side Meta event directly. GTM still receives the original
 * dataLayer event for analytics and any other configured tags, while Meta no
 * longer depends on the GTM container to translate the ecommerce payload.
 */
const sendMetaPixelEvent = (eventData: GTMEventParams) => {
    const fbq = (window as any).fbq;

    if (typeof fbq !== 'function') return;

    const eventId =
        eventData.event_id ||
        eventData.eventID ||
        eventData.eventId ||
        eventData.meta_event_id;
    const eventOptions =
        typeof eventId === 'string' ? { eventID: eventId } : undefined;

    if (eventData.event === 'page_view') {
        fbq('track', 'PageView', {}, eventOptions);
        return;
    }

    if (
        eventData.event !== 'view_item' &&
        eventData.event !== 'add_to_cart' &&
        eventData.event !== 'purchase'
    ) {
        return;
    }

    const ecommerce = eventData.ecommerce;
    const items = Array.isArray(ecommerce?.items) ? ecommerce.items : [];
    const contents = items.map((item) => ({
        id: String(item.item_id),
        quantity: Number(item.quantity || 1),
        item_price: Number(item.price || 0),
    }));
    const params: Record<string, any> = {
        content_ids: contents.map((item) => item.id),
        contents,
        content_type: 'product',
        currency: ecommerce.currency,
        value: Number(ecommerce.value || 0),
    };

    if (eventData.event === 'view_item') {
        params.content_name = items[0]?.item_name || '';
        fbq('track', 'ViewContent', params, eventOptions);
        return;
    }

    if (eventData.event === 'add_to_cart') {
        fbq('track', 'AddToCart', params, eventOptions);
        return;
    }

    if (ecommerce.transaction_id) {
        params.order_id = String(ecommerce.transaction_id);
    }

    fbq('track', 'Purchase', params, eventOptions);
};

const useGTMEventsNew = () => {
    const sendEvent = useCallback((eventData: GTMEventParams) => {
        if (typeof window !== 'undefined') {
            const eventId =
                eventData.event_id ||
                eventData.eventID ||
                eventData.eventId ||
                eventData.meta_event_id;

            if (typeof eventId === 'string') {
                const dedupeKey = `${eventData.event}:${eventId}`;

                if (pushedEventIds.has(dedupeKey)) return;

                pushedEventIds.add(dedupeKey);
            }

            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push(eventData);
            sendMetaPixelEvent(eventData);
        }
    }, []);

    return sendEvent;
};

export default useGTMEventsNew;
