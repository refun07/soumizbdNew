import React from 'react';
import { createRoot } from 'react-dom/client';
import { Toaster } from 'react-hot-toast';
import { BrowserRouter } from 'react-router-dom';
import App from './App';
import AppContextProvider from './Context';

const root = createRoot(document.getElementById('app')!);
root.render(
    // <StrictMode>
    <BrowserRouter>
        <AppContextProvider>
            <App />
            <Toaster position="bottom-center" />
        </AppContextProvider>
    </BrowserRouter>,
    // </StrictMode>,
);
