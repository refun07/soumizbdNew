// hooks/useFacebookPixelEvents.ts
export const useFacebookPixelEvents = () => {
    const fireEvent = (eventName: string, eventData: Record<string, any> = {}) => {
      if (typeof window !== 'undefined' && (window as any).fbq) {
        const { eventID, ...params } = eventData;

        if (eventID) {
          (window as any).fbq('track', eventName, params, { eventID });
          return;
        }

        (window as any).fbq('track', eventName, params);
      } else {
        console.warn(`fbq is not defined: Tried to track ${eventName}`, eventData);
      }
    };

    return {
      addToCart: (data: { content_ids: string[]; content_name?: string; value: number; currency: string }) =>
        fireEvent('AddToCart', data),

      purchase: (data: { value: number; currency: string; content_ids?: string[] }) =>
        fireEvent('Purchase', data),

      viewContent: (data: { content_ids: string[]; content_name?: string; value?: number; currency?: string }) =>
        fireEvent('ViewContent', data),

      addPaymentInfo: (data: { value?: number; currency?: string }) =>
        fireEvent('AddPaymentInfo', data),

      addToWishlist: (data: { content_ids: string[]; content_name?: string }) =>
        fireEvent('AddToWishlist', data),

      initiateCheckout: (data: { value: number; currency: string; content_ids?: string[];contents?: string[]; num_items?: number; eventID?: string }) =>
        fireEvent('InitiateCheckout', data),

      search: (data: { search_string: string }) =>
        fireEvent('Search', data),
    };
  };
