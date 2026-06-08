// src/store/index.ts
import { configureStore } from '@reduxjs/toolkit';
import courierReducer from './courierSlice';
import popupSlice from '../../frontend/store/features/popup/popupSlice';

export const store = configureStore({
  reducer: {
    courier: courierReducer,
     popup: popupSlice.reducer,
  },
});

export type RootState = ReturnType<typeof store.getState>;
export type AppDispatch = typeof store.dispatch;
