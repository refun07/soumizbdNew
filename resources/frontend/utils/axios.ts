import axios from 'axios';
import { STORAGE_KEYS } from '../types';
import { API_URL } from './env';

// Note: ZoneId header removed as location functionality has been disabled

const localAxios = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'multipart/form-data',
        // ZoneId header removed - location functionality disabled
        'Accept-Language': localStorage.getItem('i18nextLng'),
    },
});

// Set the authorization token in the header if available
const token = localStorage.getItem(STORAGE_KEYS.AUTH_KEY);
if (token) {
    localAxios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export default localAxios;
