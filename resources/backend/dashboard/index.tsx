import React from 'react';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import AppContextProvider from '../react/Context';
import PopupIndex from '../react/components/popup/PopupIndex';
import '../react/i18next';
import App from './App';

const root = createRoot(document.getElementById('app')!);
root.render(
    <AppContextProvider>
        <Toaster />
        <App />
        <PopupIndex />
    </AppContextProvider>,
);
