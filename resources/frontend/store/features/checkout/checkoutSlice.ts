import { readStoredJson, safeSessionStorage } from '../../../utils/safeStorage';
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
    shippingZoneId: undefined,
    shippingZoneName: undefined,
    coupons: [],
    isVerified: false,
    otpExpiryTimestamp: null,
};

const checkoutSessionKey = 'arm-checkoutData';
const sessionData = readStoredJson<ICheckoutData>(safeSessionStorage, checkoutSessionKey);
const isRecord = (value: unknown): value is Record<string, any> => !!value && typeof value === 'object' && !Array.isArray(value);
const restoredState: ICheckoutData = isRecord(sessionData) ? {
    ...initialState,
    ...sessionData,
    customerDetails: { ...initialState.customerDetails, ...(isRecord(sessionData.customerDetails) ? sessionData.customerDetails : {}) },
    selectedShops: isRecord(sessionData.selectedShops) ? sessionData.selectedShops : {},
    coupons: Array.isArray(sessionData.coupons) ? sessionData.coupons : [],
} : initialState;

const saveToSessionStorage = (state: ICheckoutData) => {
    safeSessionStorage.setItem(checkoutSessionKey, JSON.stringify(state));
};

const checkoutSlice = createSlice({
    name: 'checkout',

    // get initial state from session storage
    // if not found, use initialState
    initialState: restoredState,

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
            safeSessionStorage.removeItem(checkoutSessionKey);
            return initialState;
        },
    },
});

export const { setCheckoutData, clearCheckoutData } = checkoutSlice.actions;
export const useCheckoutData = () =>
    useSelector((state: RootState) => state.checkout);
export default checkoutSlice;
