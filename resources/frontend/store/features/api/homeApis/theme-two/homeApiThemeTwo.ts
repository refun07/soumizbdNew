import { IProductShort } from '../../../../../types/product';
import { apiSlice } from '../../apiSlice';
import { IClientReview, IGetCategoryProducts, IGetFlashSaleProducts } from '../commonHomeTypes';
import {
    IBanner,
    IGetAboutUs,
    IGetFeaturedCategories,
    IGetFourImageBanners,
    IGetThreeImageBanners,
} from './types';

const urlPrefix = `/homepage/${window.config.defaultTheme}`;

const homeApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getBanner: builder.query<IBanner, void>({
            query: () => `${urlPrefix}/banner`,
            transformResponse: (res: any) => res.result,
        }),

        getFeaturedCategories: builder.query<IGetFeaturedCategories, void>({
            query: () => `${urlPrefix}/featured-categories`,
            transformResponse: (res: any) => res.result,
        }),

        getCategoryProducts: builder.query<IGetCategoryProducts[], void>({
            query: () => `${urlPrefix}/category-products`,
            transformResponse: (res: any) => res.result,
        }),

        getThreeImageBanner: builder.query<IGetThreeImageBanners, void>({
            query: () => `${urlPrefix}/three-image-banner`,
            transformResponse: (res: any) => res.result,
        }),

        getFlashSaleProducts: builder.query<IGetFlashSaleProducts, void>({
            query: () => `${urlPrefix}/flash-sale-products`,
            transformResponse: (res: any) => res.result,
        }),

        getFourImageBanner: builder.query<IGetFourImageBanners, void>({
            query: () => `${urlPrefix}/four-image-banner`,
            transformResponse: (res: any) => res.result,
        }),

        getAboutUs: builder.query<IGetAboutUs, void>({
            query: () => `${urlPrefix}/about-us`,
            transformResponse: (res: any) => res.result,
        }),

        getTrendyProducts: builder.query<
            {
                products: IProductShort[];
                showThemeTwoTrendyProducts: boolean;
            },
            void
        >({
            query: () => `${urlPrefix}/trendy-products`,
            transformResponse: (res: any) => res.result,
        }),

        getReviews: builder.query<
            {
                reviews: IClientReview[];
                showThemeTwoReviewSection: boolean;
            },
            void
        >({
            query: () => `${urlPrefix}/reviews`,
            transformResponse: (res: any) => res.result,
        }),
    }),
});

export const {
    useGetBannerQuery,
    useGetFeaturedCategoriesQuery,
    useGetCategoryProductsQuery,
    useGetThreeImageBannerQuery,
    useGetFlashSaleProductsQuery,
    useGetFourImageBannerQuery,
    useGetAboutUsQuery,
    useGetTrendyProductsQuery,
    useGetReviewsQuery,
} = homeApi;
