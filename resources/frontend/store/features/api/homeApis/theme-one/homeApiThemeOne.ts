import { apiSlice } from '../../apiSlice';
import {
    IGetBottomImageBanners,
    IGetCategoryProducts,
    IGetFlashSaleProducts,
    IGetTopProducts,
} from '../commonHomeTypes';
import {
    IMainBannerCarousel,
    IGetFeaturedCategories,
    IGetFourImageBanners,
    IGetOnSaleProducts,
    IGetPromoCards,
    IGetSingleImageBanner,
} from './types';

const urlPrefix = `/homepage/${window.config.defaultTheme}`;

const homeApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getBanner: builder.query<IMainBannerCarousel, void>({
            query: () => `${urlPrefix}/banner`,
            transformResponse: (res: any) => res.result,
        }),

        getBannerSlider: builder.query<{ showThemeOneSliderSection: '1' | '0'; image: string; url: string }[], void>({
            query: () => `${urlPrefix}/sliders`,
            transformResponse: (res: any) => res.result,
        }),

        getPromoCards: builder.query<IGetPromoCards, void>({
            query: () => `${urlPrefix}/promo-cards`,
            transformResponse: (res: any) => res.result,
        }),

        getOnSaleProducts: builder.query<IGetOnSaleProducts, void>({
            query: () => `${urlPrefix}/on-sale-products`,
            transformResponse: (res: any) => res.result,
        }),

        getFeaturedCategories: builder.query<IGetFeaturedCategories, void>({
            query: () => `${urlPrefix}/featured-categories`,
            transformResponse: (res: any) => res.result,
        }),

        getTopProducts: builder.query<IGetTopProducts, void>({
            query: () => `${urlPrefix}/top-products`,
            transformResponse: (res: any) => res.result,
        }),

        getSingleImageBanner: builder.query<IGetSingleImageBanner, void>({
            query: () => `${urlPrefix}/single-image-banner`,
            transformResponse: (res: any) => res.result,
        }),

        getFlashSaleProducts: builder.query<IGetFlashSaleProducts, void>({
            query: () => `${urlPrefix}/flash-sale-products`,
            transformResponse: (res: any) => res.result,
        }),

        getFourImageBanners: builder.query<IGetFourImageBanners, void>({
            query: () => `${urlPrefix}/four-image-banner`,
            transformResponse: (res: any) => res.result,
        }),

        getCategoryProducts: builder.query<IGetCategoryProducts[], void>({
            query: () => `${urlPrefix}/category-products`,
            transformResponse: (res: any) => res.result,
        }),

        getBottomImageBanners: builder.query<IGetBottomImageBanners, void>({
            query: () => `${urlPrefix}/bottom-image-banner`,
            transformResponse: (res: any) => res.result,
        }),

        getReviews: builder.query<{ reviews: IClientReview[]; showThemeOneReviewSection: boolean }, void>({
            query: () => `${urlPrefix}/reviews`,
            transformResponse: (res: any) => res.result,
        }),
    }),
});

export const {
    useGetBannerQuery,
    useGetBannerSliderQuery,
    useGetFeaturedCategoriesQuery,
    useGetOnSaleProductsQuery,
    useGetPromoCardsQuery,
    useGetTopProductsQuery,
    useGetSingleImageBannerQuery,
    useGetFlashSaleProductsQuery,
    useGetFourImageBannersQuery,
    useGetCategoryProductsQuery,
    useGetBottomImageBannersQuery,
    useGetReviewsQuery,
} = homeApi;
