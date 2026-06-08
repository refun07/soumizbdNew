import 'swiper/css';

import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { Provider } from 'react-redux';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import { AuthInit } from './components/auth/AuthInit';
import './i18next';
import { store } from './store/store';
import { STORAGE_KEYS } from './types';
import localAxios from './utils/axios';

// if accessToken is in the query
// set the access token to local storage
const searchParams = new URLSearchParams(window.location.search);
const accessToken = searchParams.get('accessToken');

if (accessToken) {
    localStorage.setItem(STORAGE_KEYS.AUTH_KEY, accessToken);
    localAxios.defaults.headers.common[
        'Authorization'
    ] = `Bearer ${accessToken}`;
}

const root = createRoot(document.getElementById('app')!);
root.render(
    <StrictMode>
        <Provider store={store}>
            <BrowserRouter>
                <AuthInit>
                    <App />
                </AuthInit>
                <Toaster position="bottom-center" />
            </BrowserRouter>
        </Provider>
    </StrictMode>,
);
