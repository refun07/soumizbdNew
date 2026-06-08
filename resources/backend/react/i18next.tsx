import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

/**
 * Custom backend plugin for i18next that fetches from Laravel API
 */
const ApiBackend = {
    type: 'backend' as const,
    
    init(services: any, backendOptions: any, i18nextOptions: any) {
        /* no-op */
    },

    read(language: string, namespace: string, callback: (error: any, data: any) => void) {
        // Fetch translations from Laravel API
        fetch(`/api/v1/translations/${language}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                callback(null, data);
            })
            .catch(error => {
                console.error('[i18next] Failed to load translations from API:', {
                    language,
                    error: error.message,
                    timestamp: new Date().toISOString()
                });
                
                // Log to monitoring if available
                if (typeof window !== 'undefined' && (window as any).config?.env === 'production') {
                    // Could send to error tracking service here
                    console.warn('[i18next] Production environment - consider implementing error tracking');
                }
                
                // Return empty object to allow graceful degradation
                callback(null, {});
            });
    }
};

// Extend Window interface for TypeScript
declare global {
    interface Window {
        currentLocale?: string;
        config?: {
            env?: string;
        };
    }
}

/**
 * Get the initial locale from Laravel session or fallback
 */
const getInitialLocale = (): string => {
    // First priority: Laravel's session locale (set in blade)
    if (typeof window !== 'undefined' && window.currentLocale) {
        return window.currentLocale;
    }
    
    // Second priority: Browser locale
    if (typeof navigator !== 'undefined') {
        const browserLang = navigator.language || (navigator as any).userLanguage;
        if (browserLang) {
            return browserLang;
        }
    }
    
    // Fallback
    return 'en-US';
};

i18n
    .use(ApiBackend)
    .use(initReactI18next)
    .init({
        lng: getInitialLocale(),
        fallbackLng: 'en-US',
        debug: process.env.NODE_ENV === 'development',
        interpolation: {
            escapeValue: false,
        },
        react: {
            useSuspense: true,
        },
        ns: ['translations'],
        defaultNS: 'translations',
    });

export default i18n;
