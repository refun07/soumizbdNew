import { apiSlice } from './apiSlice';

export interface IStockRequestPayload {
    product_id: number;
    variation_id?: number;
    quantity?: number;
    note?: string;
    guest_user_id?: number | null;
    email?: string;
    phone?: string;
}

export interface IStockRequestCheck {
    success: boolean;
    status: number;
    requested: boolean;
}

export interface IStockRequestResponse {
    success: boolean;
    status: number;
    message: string;
    already_requested?: boolean;
    result?: any;
}

const stockRequestApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        requestStock: builder.mutation<IStockRequestResponse, IStockRequestPayload>({
            query: (data) => ({
                url: '/stock-requests',
                method: 'POST',
                body: data,
            }),
        }),
        checkStockRequest: builder.query<IStockRequestCheck, { productId: string; variationId?: string; email?: string; guestUserId?: number | null }>({
            query: ({ productId, variationId, email, guestUserId }) => ({
                url: '/stock-requests/check',
                params: { product_id: productId, variation_id: variationId, email, guest_user_id: guestUserId },
            }),
        }),
    }),
    overrideExisting: true,
});

export const { useRequestStockMutation, useCheckStockRequestQuery } = stockRequestApi;
