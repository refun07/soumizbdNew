import { IGetsQueryParams, IResponse } from '../../../types';
import { ICoupon, ICouponShort } from '../../../types/checkout';
import { IProductShort } from '../../../types/product';
import { IShop, IShopProfileInfo, IShopSection } from '../../../types/shop';
import { objectToFormData } from '../../../utils/ObjectFormData';
import { apiSlice } from './apiSlice';

export const shopApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Shop', 'ShopSection', 'Coupons'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getShops: builder.query<IResponse<IShop[]>, IGetsQueryParams>({
                query: (filters) => ({
                    url: '/shops',
                    params: { ...filters, searchKey: filters.query },
                }),
                providesTags: ['Shop'],
            }),

            getShopDetails: builder.query<IShop, string>({
                query: (slug) => `/shops/details/${slug}`,
                transformResponse: (res: any) => res.result,
                providesTags: (_result, _error, slug) => [
                    { type: 'Shop', id: slug },
                ],
            }),

            getShopProfileInfo: builder.query<IShopProfileInfo, string>({
                query: (slug) => `/shops/profile/${slug}`,
                transformResponse: (res: any) => res.result,
                providesTags: (_result, _error, slug) => [
                    { type: 'Shop', id: slug },
                ],
            }),

            getShopSections: builder.query<
                {
                    result: IShopSection[];
                    justForYouProducts: IProductShort[];
                },
                string
            >({
                query: (slug) => `shops/sections/${slug}`,
                providesTags: (_result, _error, slug) => [
                    { type: 'ShopSection', id: slug },
                ],
            }),

            // coupon
            applyCoupon: builder.mutation<
                ICouponShort | undefined,
                { code: string; shopId: number }
            >({
                query: (body) => ({
                    url: `/coupons/apply`,
                    method: 'POST',
                    body: objectToFormData(body),
                }),
                transformResponse: (res: any) => res.result,
            }),

            getCoupons: builder.query<
                IResponse<ICouponShort[]>,
                IGetsQueryParams & { shopSlug?: string }
            >({
                query: (params) => ({
                    url: '/coupons',
                    params: { ...params, searchKey: params.query },
                }),
                providesTags: ['Coupons'],
            }),

            getCouponDetails: builder.query<ICoupon, string>({
                query: (code) => `/coupons/details/${code}`,
                providesTags: (_res, _err, code) => [
                    { type: 'Coupons', id: code },
                ],
                transformResponse: (res: any) => res.result,
            }),
        }),
    });

export const {
    useGetShopsQuery,
    useLazyGetShopsQuery,
    useGetShopDetailsQuery,

    useGetShopProfileInfoQuery,

    useGetShopSectionsQuery,

    useApplyCouponMutation,
    useGetCouponsQuery,
    useGetCouponDetailsQuery,
    useLazyGetCouponDetailsQuery,
} = shopApi;
