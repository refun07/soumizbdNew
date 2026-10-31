import { apiSlice } from '../apiSlice';
import { IProductShort } from '../../../../types/product';
import { ICategoryShort } from '../../../../types';
import { IClientReview } from './commonHomeTypes';
import {
    IGetSingleImageBanner,
    IMainBannerCarousel,
} from './theme-one/types';

// This storefront uses the existing Theme One content editor in the admin panel.
const botanicalApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getBotanicalFlashSale: builder.query<{ products: IProductShort[]; endsAt: string | null; active: boolean }, void>({
            query: () => 'homepage/botanical/flash-sale',
            transformResponse: (response: any) => response.result,
        }),
        getBotanicalBanner: builder.query<IMainBannerCarousel, void>({
            query: () => 'homepage/theme-one/banner',
            transformResponse: (response: any) => response.result,
        }),
        getBotanicalCategories: builder.query<{ categories: ICategoryShort[] }, void>({
            query: () => 'categories',
            transformResponse: (response: any) => response.result,
        }),
        getBotanicalPromotion: builder.query<IGetSingleImageBanner, void>({
            query: () => 'homepage/theme-one/single-image-banner',
            transformResponse: (response: any) => response.result,
        }),
        getBotanicalReviews: builder.query<
            { reviews: IClientReview[]; showThemeOneReviewSection: boolean },
            void
        >({
            query: () => 'homepage/theme-one/reviews',
            transformResponse: (response: any) => response.result,
        }),
    }),
});

export const {
    useGetBotanicalFlashSaleQuery,
    useGetBotanicalBannerQuery,
    useGetBotanicalCategoriesQuery,
    useGetBotanicalPromotionQuery,
    useGetBotanicalReviewsQuery,
} = botanicalApi;
