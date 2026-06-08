import { PayloadAction, createSlice } from '@reduxjs/toolkit';
import { useSelector } from 'react-redux';
import { RootState } from '../../../types/state';

export type Modals =
    | 'categories'
    | 'cart'
    | 'abandoned-cart'
    | 'signin'
    | 'signup'
    | 'product-preview'
    | 'image-viewer'
    | 'coupon-details'
    | 'product-review'
    | 'search'
    | 'product-filter'
    | 'address'
    | 'dashboard-sidebar'
    | 'user-verification'
    | 'forget-password'
    | 'confirmation'
    | 'otp-verification'
    | 'confirmation-status-update'
    | 'popup-image'
    | 'stock-request';
export type ModalSize = 'sm' | 'md' | 'lg' | 'xl';

interface PopupState {
    popup: Modals | null;
    popupProps: Record<string, any>;
    size: ModalSize;
}

const initialState: PopupState = {
    popup: null,
    popupProps: {},
    size: 'md',
};

const popupSlice = createSlice({
    name: 'modal',
    initialState,
    reducers: {
        togglePopup: (
            state,
            action: PayloadAction<Modals | Partial<PopupState>>,
        ) => {
            if (typeof action.payload === 'string') {
                state.popup = action.payload;
            } else {
                return {
                    ...initialState,
                    ...action.payload,
                };
            }
        },

        closePopup: () => {
            return initialState;
        },
    },
});

export const { togglePopup, closePopup } = popupSlice.actions;
export const usePopup = () => useSelector((state: RootState) => state.popup);
export default popupSlice;
