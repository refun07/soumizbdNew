declare global {
    interface Window {
      fbq: (...args: any[]) => void;
    }
  }

  const fbPixel = {
    track: (event: string, params?: Record<string, any>) => {
      if (typeof window !== 'undefined' && window.fbq) {
        window.fbq('track', event, params);
      } else {
        console.warn('Facebook Pixel is not initialized');
      }
    },
    // Optional: add more helpers like ViewContent, AddToCart, etc.
  };

  export default fbPixel;
