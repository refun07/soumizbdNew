import axios from 'axios';
import { setupCache } from 'axios-cache-interceptor';

declare global {
    interface Window {
        backendApiUrl: string;
    }
}

export const axiosInstance = setupCache(
    axios.create({
        baseURL: '/api/v1/installation',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    }),
    {
        cacheTakeover: false, // don't send request if cache is available
    },
);
