import { PayloadAction, createSlice } from '@reduxjs/toolkit';
import { useSelector } from 'react-redux';
import { ICheckoutData } from '../../../types/checkout';
import { RootState } from '../../../types/state';

const initialState: ICheckoutData = {
    customerDetails: {
        name: '',
        email: '',
        phone: '',
        note: '',
    },
    shippingAddress: undefined,
    billingAddress: undefined,
    paymentMethod: '',
    selectedShops: {},
    shippingCharge: 0,
    coupons: [],
    isVerified:false,
    otpExpiryTimestamp: null,
};

const checkoutSessionKey = 'arm-checkoutData';
const sessionData = JSON.parse(
    sessionStorage.getItem(checkoutSessionKey) || 'null',
);

const saveToSessionStorage = (state: ICheckoutData) => {
    sessionStorage.setItem(checkoutSessionKey, JSON.stringify(state));
};

const checkoutSlice = createSlice({
    name: 'checkout',

    // get initial state from session storage
    // if not found, use initialState
    initialState: (sessionData as ICheckoutData) || initialState,

    reducers: {
        setCheckoutData: (
            state,
            action: PayloadAction<Partial<ICheckoutData> | undefined>,
        ) => {
            const newStore = {
                ...state,
                ...action.payload,
            };
            saveToSessionStorage(newStore);
            return newStore;
        },

        clearCheckoutData: () => {
            sessionStorage.removeItem(checkoutSessionKey);
            return initialState;
        },
    },
});

export const { setCheckoutData, clearCheckoutData } = checkoutSlice.actions;
export const useCheckoutData = () =>
    useSelector((state: RootState) => state.checkout);
export default checkoutSlice;
