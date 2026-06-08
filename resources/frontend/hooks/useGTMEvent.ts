import { useCallback } from 'react';

declare global {
  interface Window {
    dataLayer: any[];
  }
}

type EcommerceItem = {
  item_id: string;
  item_name: string;
  price?: number;
  quantity?: number;
  [key: string]: any;
};

type GTMEventParams =
  | {
      event: 'add_to_cart' | 'add_to_wishlist' | 'view_item' | 'view_item_list';
      ecommerce: {
        value?: number;
        currency: string;
        items: EcommerceItem[]
     };
    }
  | {
      event: 'purchase';
      ecommerce: {
        transaction_id: string;
        value: number;
        currency: string;
        items: EcommerceItem[];
      };
    }
  | {
      event: 'begin_checkout' | 'add_payment_info';
      ecommerce: {
        currency: string;
        value: number;
        items: EcommerceItem[];
      };
    }
  | {
      event: 'search';
      search_term: string;
    };

const useGTMEvent = () => {
  const sendEvent = useCallback((eventData: GTMEventParams) => {
    if (typeof window !== 'undefined') {
      window.dataLayer = window.dataLayer || [];
      window.dataLayer.push(eventData);
    }
  }, []);

  return sendEvent;
};

export default useGTMEvent;
