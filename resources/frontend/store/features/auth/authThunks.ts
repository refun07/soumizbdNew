import { createAsyncThunk } from '@reduxjs/toolkit';
import toast from 'react-hot-toast';
import { STORAGE_KEYS } from '../../../types';
import { IAuthPayload } from '../../../types/payload';
import { ICartProduct } from '../../../types/product';
import { AuthResponse } from '../../../types/response';
import { objectToFormData } from '../../../utils/ObjectFormData';
import localAxios from '../../../utils/axios';
import { cookies } from '../../../utils/cookie';

export const signinUser = createAsyncThunk<AuthResponse, IAuthPayload>(
    'auth/signin',
    async (body) => {
        try {
            const guestUserId =
                cookies.get(STORAGE_KEYS.GUEST_ID_KEY) || undefined;

            const res = await localAxios('/auth/login', {
                method: 'POST',
                data: objectToFormData({
                    ...body,
                    guestUserId,
                }),
            });

            return res.data;
        } catch (e: any) {
            toast.error(e.response.data.message);
        }
    },
);

export const getUser = createAsyncThunk<AuthResponse>(
    'auth/getUser',
    async () => {
        const accessToken = localStorage.getItem(STORAGE_KEYS.AUTH_KEY);

        if (!accessToken) {
            return {};
        }

        const body = objectToFormData({
            guestUserId: cookies.get(STORAGE_KEYS.GUEST_ID_KEY),
        });

        const res = await localAxios.post('/auth/user', body, {
            headers: {
                accept: 'application/json',
            },
        });

        return res.data;
    },
);

export const signupUser = createAsyncThunk<AuthResponse, IAuthPayload>(
    'auth/signup',
    async (body, { rejectWithValue }) => {
        const guestUserId = cookies.get(STORAGE_KEYS.GUEST_ID_KEY);

        try {
            const res = await localAxios('/auth/signup', {
                method: 'POST',
                data: objectToFormData({
                    ...body,
                    guestUserId,
                }),
            });

            return res.data;
        } catch (err: any) {
            return rejectWithValue(err);
        }
    },
);

export const verifyUser = createAsyncThunk<AuthResponse, IAuthPayload>(
    'auth/verify',
    async (data) => {
        try {
            const res = await localAxios('/auth/verify', {
                method: 'POST',
                data: objectToFormData(data),
            });

            return res.data;
        } catch (e: any) {
            toast.error(e.response.data.message);
        }
    },
);

export const updateZone = createAsyncThunk<
    {
        carts: ICartProduct[];
    },
    number | string
>('auth/updateZone', async (zoneId) => {
    const res = await localAxios.post(
        '/checkout/zone/update',
        objectToFormData({ newZoneId: zoneId }),
        {
            headers: {
                ZoneId: zoneId,
            },
        },
    );

    return res.data.result;
});
