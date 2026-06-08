import React from 'react';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { BrowserRouter } from 'react-router-dom';
import AppContextProvider from '../react/Context';
import PopupIndex from '../react/components/popup/PopupIndex';
import '../react/i18next';
import App from './App';

// collapse sidebar
const sidebar = document.querySelector('.sidebar') as HTMLDivElement | null;
if (sidebar) {
    sidebar.classList.add('in-active');
    sidebar.style.width = '90px';
}

const root = createRoot(document.getElementById('app')!);
root.render(
    <BrowserRouter>
        <AppContextProvider>
            <Toaster />
            <App />
            <PopupIndex />
        </AppContextProvider>
    </BrowserRouter>,
);
