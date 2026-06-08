import { ICategoryShort, IPaginationMeta, STORAGE_KEYS } from '../../../types';
import { IBrandShort } from '../../../types/brand';
import { ICartProduct, IProduct, IProductShort } from '../../../types/product';
import { IWishListResponse } from '../../../types/response';
import { objectToFormData } from '../../../utils/ObjectFormData';
import { cookies } from '../../../utils/cookie';
import { updateCartProducts } from '../auth/authSlice';
import { apiSlice } from './apiSlice';

export interface IFilterAttribute {
    id: number;
    name: string;
    isColor: boolean;
    variationValues: {
        id: number;
        name: string;
        colorCode: string;
    }[];
}

export interface ISearchAndFilter {
    products: {
        data: IProductShort[];
        meta: IPaginationMeta;
    };
    brands: IBrandShort[];
    rootCategories: ICategoryShort[];
    filterAttributes: IFilterAttribute[];
    selectedCategory: ICategoryShort | null;
    priceFilter: {
        minPrice: number | null;
        maxPrice: number | null;
    };
}

const onCartQueryStarted = async (
    _arg: any,
    { queryFulfilled, dispatch }: any,
) => {
    try {
        const result = await queryFulfilled;
        dispatch(updateCartProducts(result.data));
    } catch (error: any) {}
};

const productApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Search', 'Product', 'WishList'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getProducts: builder.query<ISearchAndFilter, string | void>({
                query: (query) => ({
                    url: `/products${query}`,
                }),
                providesTags: (_result, _error, query) => [
                    { type: 'Search', id: query || '' },
                ],
                transformResponse: (res: any) => res.result,
            }),

            getProductDetails: builder.query<IProduct, string>({
                query: (slug) => `/products/details/${slug}`,
                providesTags: (_result, _error, slug) => [
                    'Product',
                    { type: 'Product', id: slug },
                ],
                transformResponse: (res: any) => res.result,
            }),

            getCartProducts: builder.query<
                ICartProduct[],
                string | number | void
            >({
                query: (userId) => ({
                    url: '/carts?guestUserId=' + userId,
                    method: 'GET',
                }),
                providesTags: (_result, _error) => ['Cart'],
                transformResponse: (res: any) => res.result.carts,
                onQueryStarted: onCartQueryStarted,
            }),

            addToCart: builder.mutation<
                ICartProduct[],
                {
                    productVariationId: number;
                    warehouseId: number;
                    qty: number;
                    guestUserId?: number | null;
                }
            >({
                query: (body) => {
                    const guestUserId = cookies.get(STORAGE_KEYS.GUEST_ID_KEY);

                    const data = {
                        ...body,
                        guestUserId: body.guestUserId || guestUserId,
                    };

                    return {
                        url: '/carts/create',
                        method: 'POST',
                        body: objectToFormData(data),
                    };
                },
                transformResponse: (res: any) => res.result.carts,
                onQueryStarted: onCartQueryStarted,
            }),

            updateCartProduct: builder.mutation<
                ICartProduct[],
                {
                    id: number;
                    warehouseId: number;
                    action: 'increase' | 'decrease' | 'delete';
                    guestUserId: number | null;
                }
            >({
                query: (body) => {
                    const guestUserId = cookies.get(STORAGE_KEYS.GUEST_ID_KEY);

                    const data = {
                        ...body,
                        guestUserId: body.guestUserId || guestUserId,
                    };

                    return {
                        url: '/carts/update',
                        method: 'POST',
                        body: objectToFormData(data),
                    };
                },
                transformResponse: (res: any) => res.result.carts,
                onQueryStarted: onCartQueryStarted,
            }),

            deleteCartProduct: builder.mutation<
                ICartProduct[],
                {
                    id: number;
                    guestUserId?: number | null;
                    zoneId: number;
                }
            >({
                query: (body) => {
                    const guestUserId = cookies.get(STORAGE_KEYS.GUEST_ID_KEY);

                    const data = {
                        ...body,
                        guestUserId: body.guestUserId || guestUserId,
                    };

                    return {
                        url: '/carts/delete',
                        method: 'POST',
                        body: objectToFormData(data),
                    };
                },
                transformResponse: (res: any) => res.result.carts,
                onQueryStarted: onCartQueryStarted,
            }),

            checkAbandonedCart: builder.query<{ is_abandoned: boolean; carts: ICartProduct[] }, { guestUserId?: number }>({
                query: (params) => ({
                    url: '/carts/check-abandoned',
                    method: 'GET',
                    params,
                }),
                transformResponse: (res: any) => res.result,
            }),

            // wishlist
            getWishList: builder.query<IWishListResponse[], void>({
                query: () => '/wishlists',
                providesTags: ['WishList'],
                transformResponse: (res: any) => res.result,
            }),

            addToWishList: builder.mutation<IWishListResponse[], number>({
                query: (productId) => ({
                    url: '/wishlists/create',
                    method: 'POST',
                    body: objectToFormData({ productId }),
                }),
                transformResponse: (res: any) => res.result,
                invalidatesTags: ['WishList', 'Product'],
            }),

            deleteWishList: builder.mutation<IWishListResponse[], number>({
                query: (productId) => ({
                    url: '/wishlists/delete',
                    method: 'POST',
                    body: objectToFormData({ productId }),
                }),
                transformResponse: (res: any) => res.result,
                invalidatesTags: ['WishList'],
            }),
        }),
    });

export const {
    useLazyGetProductsQuery,

    useGetProductDetailsQuery,
    useLazyGetProductDetailsQuery,

    useLazyGetCartProductsQuery,
    useAddToCartMutation,

    useUpdateCartProductMutation,
    useDeleteCartProductMutation,

    useCheckAbandonedCartQuery,
    useLazyCheckAbandonedCartQuery,

    useGetWishListQuery,
    useLazyGetWishListQuery,
    useAddToWishListMutation,
    useDeleteWishListMutation,
} = productApi;
