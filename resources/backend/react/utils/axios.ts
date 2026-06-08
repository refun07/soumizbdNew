import axios from 'axios';
import { setupCache } from 'axios-cache-interceptor';

declare global {
    interface Window {
        backendApiUrl: string;
    }
}

const getCsrfToken = () => {
    const token = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
    return token?.content || '';
};

const axiosClient = axios.create({
    baseURL: window.backendApiUrl,
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

// Add CSRF token to all requests
axiosClient.interceptors.request.use((config) => {
    config.headers['X-CSRF-TOKEN'] = getCsrfToken();
    return config;
});

export const axiosInstance = setupCache(
    axiosClient,
    {
        cacheTakeover: false, // don't send request if cache is available
    },
);
