import { IReviewState } from '../../../components/popups/ReviewModal';
import { IGetsQueryParams, IPaginationMeta, Impressions } from '../../../types';
import { IReview } from '../../../types/product';
import { IReviewSummary, IShop, IShopImpression } from '../../../types/shop';
import { objectToFormData } from '../../../utils/ObjectFormData';
import { apiSlice } from './apiSlice';

export const reviewApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Review', 'UserReview', 'ShopReview'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getAllProductReviews: builder.query<
                {
                    summary: IReviewSummary;
                    reviews: { data: IReview[]; meta: IPaginationMeta };
                },
                IGetsQueryParams & { productId: number }
            >({
                query: ({ productId, page }) => ({
                    url: `/reviews/${productId}`,
                    method: 'GET',
                    params: {
                        page: page || 1,
                    },
                }),
                transformResponse: (res: any) => res.result,
                providesTags: (_result, _error, { productId }) => [
                    'Review',
                    { type: 'Review', id: productId },
                ],
            }),

            upsertProductReview: builder.mutation<
                IReview,
                Omit<IReviewState, 'oldImages'> & {
                    oldImages: string;
                }
            >({
                query: (body) => ({
                    url: '/reviews/user/store',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
                transformResponse: (res: any) => res.result,
                invalidatesTags: ['Order', 'Review'],
            }),

            postShopImpression: builder.mutation<
                IShop,
                { shopId: number; impression: Impressions }
            >({
                query: (body) => ({
                    url: `/reviews/shop/store`,
                    method: 'POST',
                    body: objectToFormData(body),
                }),
                invalidatesTags: (_result, _error, { shopId }) => {
                    return [{ type: 'ShopReview', id: shopId }];
                },
            }),

            getAllReviewOfUser: builder.query<
                {
                    reviews: { data: IReview[]; meta: IPaginationMeta };
                    shopReviews: {
                        data: IShopImpression[];
                        meta: IPaginationMeta;
                    };
                },
                IGetsQueryParams & { userId: number }
            >({
                query: (params) => ({
                    url: '/reviews/user/all',
                    method: 'GET',
                    params,
                }),
                providesTags: ['Review'],
                transformResponse: (res: any) => res.result,
            }),

            getUserReviewOfProduct: builder.query<IReview, number>({
                query: (productId) => {
                    return {
                        url: `/reviews/user/${productId}`,
                        method: 'GET',
                    };
                },
                providesTags: (_result, _error, productId) => [
                    { type: 'UserReview', id: productId },
                ],
                transformResponse: (res: any) => res.result.review,
            }),
            getUserReviewOfShop: builder.query<IShopImpression | null, number>({
                query: (shopId) => {
                    return {
                        url: `/reviews/shop/${shopId}`,
                        method: 'GET',
                    };
                },
                providesTags: (_result, _error, productId) => [
                    { type: 'ShopReview', id: productId },
                ],
                transformResponse: (res: any) => res.result.shopReview,
            }),
        }),
    });

export const {
    useLazyGetAllProductReviewsQuery,
    useUpsertProductReviewMutation,
    useGetAllReviewOfUserQuery,
    usePostShopImpressionMutation,
    useLazyGetUserReviewOfShopQuery,

    useGetUserReviewOfProductQuery,
    useLazyGetUserReviewOfProductQuery,
} = reviewApi;
