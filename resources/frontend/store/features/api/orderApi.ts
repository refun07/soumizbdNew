import { IGetsQueryParams, IResponse } from '../../../types';
import {
    IBkashPersonalPayment,
    IBkashPersonalPaymentPayload,
    IOrder,
    IOrderPayload,
    IManualOrderPayload,
    IOrderSuccess,
    OrderShort,
} from '../../../types/checkout';
import { objectToFormData } from '../../../utils/ObjectFormData';
import { apiSlice } from './apiSlice';

interface IOrderQueryParams extends IGetsQueryParams {
    deliveryStatus?: string;
}

const orderApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: [
            'Address',
            'Country',
            'State',
            'City',
            'Area',
            'Coupons',
            'ShippingCharges',
        ],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getOrders: builder.query<
                IResponse<OrderShort[]>,
                IOrderQueryParams
            >({
                query: (query) => ({
                    url: '/orders',
                    params: { ...query, searchKey: query.query },
                }),
                providesTags: ['Order'],
            }),

            getOrderByCode: builder.query<IOrder, number | string>({
                query: (orderId) => `/orders/${orderId}`,
                providesTags: (_result, _error, orderId) => [
                    { type: 'Order', id: orderId },
                ],
                transformResponse: (res: any) => res.result,
            }),

            createOrder: builder.mutation<
                {
                    goToPayment: boolean;
                    orderCode: number;
                    paymentMethod: string;
                },
                IOrderPayload
            >({
                query: (body) => ({
                    url: `/checkout/order/store`,
                    method: 'POST',
                    body: objectToFormData(body),
                }),
                invalidatesTags: ['Order'],
                transformResponse: (res: any) => res.result,
            }),
            createManualOrder: builder.mutation<
                {
                    goToPayment: boolean;
                    orderCode: number;
                    paymentMethod: string;
                },
                IManualOrderPayload
            >({
                query: (body) => ({
                    url: `/checkout/manual-order/store`,
                    method: 'POST',
                    body: objectToFormData(body),
                }),
                invalidatesTags: ['Order'],
                transformResponse: (res: any) => res.result,
            }),

            getOrderSuccessData: builder.query<IOrderSuccess, number | string>({
                query: (orderId) => `/orders/success/${orderId}`,
                transformResponse: (res: any) => res.result,
            }),

            getBkashPersonalPayment: builder.query<
                IBkashPersonalPayment,
                string
            >({
                query: (orderCode) => `/orders/${orderCode}/bkash-personal`,
                transformResponse: (res: any) => res.result,
            }),

            submitBkashPersonalPayment: builder.mutation<
                { message: string; result: IBkashPersonalPayment },
                IBkashPersonalPaymentPayload
            >({
                query: ({ orderCode, ...body }) => ({
                    url: `/orders/${orderCode}/bkash-personal`,
                    method: 'POST',
                    body,
                }),
                invalidatesTags: ['Order'],
            }),
        }),
    });

export const {
    useGetOrdersQuery,
    useCreateOrderMutation,
    useCreateManualOrderMutation,
    useGetOrderByCodeQuery,
    useGetOrderSuccessDataQuery,
    useGetBkashPersonalPaymentQuery,
    useSubmitBkashPersonalPaymentMutation,
} = orderApi;
