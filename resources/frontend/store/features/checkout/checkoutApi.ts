import {
    IAddress,
    ICity,
    ICountry,
    IState,
} from '../../../types/checkout';
import { objectToFormData } from '../../../utils/ObjectFormData';
import { apiSlice } from '../api/apiSlice';

interface CheckoutSessionPayload {
    userId?: number;
    guestUserId?: string;
    customerDetails?: {
        name: string;
        email: string;
        phone: string;
        note: string;
    };
    shippingAddress?: IAddress;
    paymentMethod?: string;
    currentStep?: string;
    cartData?: Array<{
        id: number;
        product: {
            id: number;
            name: string;
            price: number;
            image: string;
            slug: string;
            thumbnailImg: string;
        };
        variation: {
            id: number;
            basePrice: number;
            discountedBasePrice: number;
            tax: number;
            image: string;
        };
        shop: {
            id: number;
            name: string;
            slug: string;
        };
        qty: number;
        price: number;
    }>;
    cartTotal?: number;
    shippingCharge?: number;
}

interface CheckoutSessionResponse {
    customerDetails: {
        name: string;
        email: string;
        phone: string;
        note: string;
    };
    shippingAddress: IAddress | null;
    paymentMethod: string;
    currentStep: string;
    cartData: any;
    cartTotal: number;
    shippingCharge: number;
    isConverted: boolean;
    lastUpdated: string;
}

const checkoutApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: [
            'Address',
            'Country',
            'State',
            'City',
            'Area',
            'ShippingCharges',
            'CheckoutSession',
        ],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            // manage addresses (add edit update delete || get country, state, area)
            getAddresses: builder.query<
                { addresses: IAddress[]; countries: ICountry[] },
                void
            >({
                query: () => '/user/addresses',
                providesTags: ['Address'],
                transformResponse: (res: any) => res.result,
            }),

            addAddress: builder.mutation<IAddress, Partial<AddressPayload>>({
                query: (address) => ({
                    url: '/user/address',
                    method: 'POST',
                    body: objectToFormData(address),
                }),
                invalidatesTags: ['Address'],
                transformResponse: (res: any) => res.result,
            }),

            updateAddress: builder.mutation<
                IAddress,
                Partial<AddressPayload> & { id: number }
            >({
                query: (address) => ({
                    url: `/user/address/update`,
                    method: 'POST',
                    body: objectToFormData(address),
                }),
                invalidatesTags: ['Address'],
                transformResponse: (res: any) => res.result,
            }),

            deleteAddress: builder.mutation<
                { message: string },
                { id: number }
            >({
                query: (body) => ({
                    url: `/user/address/delete`,
                    method: 'POST',
                    body: objectToFormData(body),
                }),
                invalidatesTags: ['Address'],
                transformResponse: (res: any) => res.result,
            }),

            getStates: builder.query<IState[], { countryId: number }>({
                query: ({ countryId }) => `/get-states/${countryId}`,
                providesTags: (_res, _err, { countryId }) => [
                    { type: 'State', id: countryId },
                ],
                transformResponse: (res: any) => res.result,
            }),

            getCities: builder.query<ICity[], { stateId: number }>({
                query: ({ stateId }) => `/get-cities/${stateId}`,
                providesTags: (_res, _err, { stateId }) => [
                    { type: 'City', id: stateId },
                ],
                transformResponse: (res: any) => res.result,
            }),

            // getAreas: builder.query<IArea[], { cityId: number }>({
            //     query: ({ cityId }) => `/get-areas/${cityId}`,
            //     providesTags: (_res, _err, { cityId }) => [
            //         { type: 'Area', id: cityId },
            //     ],
            //     transformResponse: (res: any) => res.result,
            // }),

            getShippingCharge: builder.query<
                number,
                {
                    cityId: number;
                    userId: number;
                    guestUserId: number;
                    shopIds: number[];
                    cartIds: number[];
                    coupons: string[];
                    items?: Array<{
                        product_id: number;
                        variation_id: number;
                        quantity: number;
                        shop_id: number;
                    }>;
                }
            >({
                query: (body) => {
                    return {
                        url: `checkout/get-shipping-charge`,
                        method: 'POST',
                        body,
                        headers: {
                            'Content-Type': 'application/json',
                        },
                    };
                },
                providesTags: ['ShippingCharges'],
                transformResponse: (res: any) => res.result,
            }),

            // Checkout session management (real-time auto-save)
            saveCheckoutSession: builder.mutation<
                { sessionId: number },
                CheckoutSessionPayload
            >({
                query: (body) => ({
                    url: 'checkout/save-session',
                    method: 'POST',
                    body,
                    headers: {
                        'Content-Type': 'application/json',
                    },
                }),
                invalidatesTags: ['CheckoutSession'],
                transformResponse: (res: any) => res.result,
            }),

            getCheckoutSession: builder.query<
                CheckoutSessionResponse | null,
                { guestUserId?: string }
            >({
                query: (params) => ({
                    url: 'checkout/session',
                    method: 'GET',
                    params,
                }),
                providesTags: ['CheckoutSession'],
                transformResponse: (res: any) => res.result,
            }),
        }),
    });

export const {
    useAddAddressMutation,
    useDeleteAddressMutation,
    useGetAddressesQuery,

    useLazyGetAddressesQuery,
    useLazyGetShippingChargeQuery,
    useUpdateAddressMutation,

    useLazyGetCitiesQuery,
    useLazyGetStatesQuery,
    // useLazyGetAreasQuery,

    // Checkout session management
    useSaveCheckoutSessionMutation,
    useGetCheckoutSessionQuery,
    useLazyGetCheckoutSessionQuery,
} = checkoutApi;
