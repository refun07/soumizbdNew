import { PayloadAction, createSlice } from '@reduxjs/toolkit';
import { useSelector } from 'react-redux';
import { STORAGE_KEYS } from '../../../types';
import { ICartProduct } from '../../../types/product';
import { AuthState, RootState } from '../../../types/state';
import { cookies } from '../../../utils/cookie';
import {
    getUser,
    signinUser,
    signupUser,
    updateZone,
    verifyUser,
} from './authThunks';

const initialState: AuthState = {
    isLoading: true,
    user: null,
    dashboardInfo: null,
    guestUserId: null,
    carts: [],

    countries: [],
    accessToken: null,

    // get user selected language from local storage or use default language
    language: window.config.defaultLang,
};

const authSlice = createSlice({
    name: 'auth',
    initialState,
    reducers: {
        userLoggedIn: (state, action: PayloadAction<Partial<AuthState>>) => {
            if (action.payload.accessToken) {
                localStorage.setItem(
                    STORAGE_KEYS.AUTH_KEY,
                    action.payload.accessToken,
                );
            }

            return { ...state, ...action.payload };
        },
        updateCartProducts: (state, action: PayloadAction<ICartProduct[]>) => {
            state.carts = action.payload;
        },
        updateUser: (state, action) => {
            state.user = { ...state.user, ...action.payload };
        },
        userLoggedOut: () => {
            // clear local storage, and session storage
            sessionStorage.clear();
            localStorage.clear();

            return {
                ...initialState,
                isLoading: false,
                guestUserId:
                    Number(cookies.get(STORAGE_KEYS.GUEST_ID_KEY)) || null,
            };
        },

        setAuthData: (state, action: PayloadAction<Partial<AuthState>>) => {
            return { ...state, ...action.payload };
        },
    },
    extraReducers: (builder) => {
        builder
            .addCase(signinUser.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(signinUser.fulfilled, (state, action) => {
                state.isLoading = false;

                if (action.payload.authStatus === 'verify') {
                    return;
                }

                state.user = action.payload.user;
                state.dashboardInfo = action.payload.dashboardInfo;
                state.guestUserId = null;
                state.carts = action.payload.carts;
                state.countries = action.payload.countries;
                state.accessToken = action.payload.access_token;

                localStorage.setItem(
                    STORAGE_KEYS.AUTH_KEY,
                    action.payload.access_token,
                );
            })
            .addCase(signinUser.rejected, (state) => {
                state.isLoading = false;
            });

        builder
            .addCase(getUser.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(getUser.fulfilled, (state, action) => {
                state.isLoading = false;
                state.user = action.payload.user;
                state.dashboardInfo = action.payload.dashboardInfo;
                state.guestUserId = null;
                state.carts = action.payload.carts;
                state.countries = action.payload.countries;
                state.accessToken = localStorage.getItem(STORAGE_KEYS.AUTH_KEY);
            })
            .addCase(getUser.rejected, (state) => {
                state.isLoading = false;
            });

        builder
            .addCase(signupUser.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(signupUser.fulfilled, (state, action) => {
                state.isLoading = false;

                if (action.payload.authStatus === 'verify') {
                    return;
                }

                state.user = action.payload.user;
                state.guestUserId = null;
                state.carts = action.payload.carts;
                state.countries = action.payload.countries;
                state.accessToken = action.payload.access_token;

                localStorage.setItem(
                    STORAGE_KEYS.AUTH_KEY,
                    action.payload.access_token,
                );
            })
            .addCase(signupUser.rejected, (state) => {
                state.isLoading = false;
            });

        builder
            .addCase(verifyUser.pending, (state) => {
                state.isLoading = true;
            })
            .addCase(verifyUser.fulfilled, (state, action) => {
                state.isLoading = false;
                state.user = action.payload.user;
                state.dashboardInfo = action.payload.dashboardInfo;
                state.guestUserId = null;
                state.carts = action.payload.carts;
                state.countries = action.payload.countries;
                state.accessToken = action.payload.access_token;

                localStorage.setItem(
                    STORAGE_KEYS.AUTH_KEY,
                    action.payload.access_token,
                );
            })
            .addCase(verifyUser.rejected, (state) => {
                state.isLoading = false;
            });

        builder.addCase(updateZone.fulfilled, (state, action) => {
            state.carts = action.payload.carts;
        });
    },
});

export const {
    userLoggedIn,
    updateUser,
    updateCartProducts,
    userLoggedOut,
    setAuthData,
} = authSlice.actions;
export const useAuth = () => useSelector((state: RootState) => state.auth);
export default authSlice;

