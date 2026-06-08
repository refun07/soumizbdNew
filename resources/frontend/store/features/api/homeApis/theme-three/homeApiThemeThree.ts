import { apiSlice } from '../../apiSlice';
import {
    IBanner,
    IBottomTwoImageBanner,
    ICategoryProducts,
    IFeaturedCategories,
    IFeaturedProducts,
    IFlashSaleProducts,
    IPopularCategories,
    ITwoImageBanner,
} from './types';

const urlPrefix = `/homepage/${window.config.defaultTheme}`;

const homeApiThemeThree = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getBanners: builder.query<IBanner[], void>({
            query: () => `${urlPrefix}/sliders`,
            transformResponse: (res: any) => res.result,
        }),

        getFeaturedCategories: builder.query<IFeaturedCategories, void>({
            query: () => `${urlPrefix}/featured-categories`,
            transformResponse: (res: any) => res.result,
        }),

        getFeaturedProducts: builder.query<IFeaturedProducts, void>({
            query: () => `${urlPrefix}/featured-products`,
            transformResponse: (res: any) => res.result,
        }),

        getPopularCategories: builder.query<IPopularCategories, void>({
            query: () => `${urlPrefix}/popular-categories`,
            transformResponse: (res: any) => res.result,
        }),

        getTwoImageBanner: builder.query<ITwoImageBanner, void>({
            query: () => `${urlPrefix}/two-image-banner`,
            transformResponse: (res: any) => res.result,
        }),

        getFlashSaleProducts: builder.query<IFlashSaleProducts, void>({
            query: () => `${urlPrefix}/flash-sale-products`,
            transformResponse: (res: any) => res.result,
        }),

        getCategoryProducts: builder.query<ICategoryProducts[], void>({
            query: () => `${urlPrefix}/category-products`,
            transformResponse: (res: any) => res.result,
        }),

        getBottomTwoImageBanner: builder.query<IBottomTwoImageBanner, void>({
            query: () => `${urlPrefix}/bottom-two-image-banner`,
            transformResponse: (res: any) => res.result,
        }),
    }),
});

export const {
    useGetBannersQuery,
    useGetFeaturedCategoriesQuery,
    useGetFeaturedProductsQuery,
    useGetPopularCategoriesQuery,
    useGetTwoImageBannerQuery,
    useGetFlashSaleProductsQuery,
    useGetCategoryProductsQuery,
    useGetBottomTwoImageBannerQuery,
} = homeApiThemeThree;
