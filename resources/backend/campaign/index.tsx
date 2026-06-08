import React from 'react';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import '../react/i18next';
import App from './App';

const root = createRoot(document.getElementById('app')!);
root.render(
    <>
        <Toaster />
        <App />
    </>,
);
