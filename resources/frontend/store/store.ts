import { configureStore } from '@reduxjs/toolkit';
import { useDispatch } from 'react-redux';
import { errorHandler } from '../middleware/errorHandler';
import { apiSlice } from './features/api/apiSlice';
import authSlice from './features/auth/authSlice';
import checkoutSlice from './features/checkout/checkoutSlice';
import popupSlice from './features/popup/popupSlice';

export const store = configureStore({
    reducer: {
        [apiSlice.reducerPath]: apiSlice.reducer,
        auth: authSlice.reducer,
        popup: popupSlice.reducer,
        checkout: checkoutSlice.reducer,
    },
    middleware: (getDefaultMiddleware) =>
        getDefaultMiddleware().concat(apiSlice.middleware, errorHandler),
});

export type AppDispatch = typeof store.dispatch;
export const useAppDispatch = () => useDispatch<AppDispatch>();
