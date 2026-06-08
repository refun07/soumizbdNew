import React from 'react';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import '../react/i18next';

const root = createRoot(document.getElementById('app')!);
root.render(
    <BrowserRouter basename="/seller/chats">
        <Toaster />
        <App />
    </BrowserRouter>,
);
