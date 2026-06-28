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
const useGTMEventsNew = () => {
    const sendEvent = useCallback((eventData: GTMEventParams) => {
        if (typeof window !== 'undefined') {
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push(eventData);
        }
    }, []);

    return sendEvent;
};

export default useGTMEventsNew;
