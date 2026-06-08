import { IProductShort } from '../../../../types/product';
import { apiSlice } from '../apiSlice';
import { IGetFeaturedBrands, IGetFooter, IGetHomepagePopup } from './commonHomeTypes';

const homeApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getFeaturedBrands: builder.query<IGetFeaturedBrands, void>({
            query: () => `homepage/featured-brands`,
            transformResponse: (res: any) => res.result,
        }),

        getNewArrivals: builder.query<IProductShort[], void>({
            query: () => `homepage/new-arrivals`,
            transformResponse: (res: any) => res.result,
        }),

        getBestSellerProducts: builder.query<IProductShort[], void>({
            query: () => `homepage/best-sellers`,
            transformResponse: (res: any) => res.result,
        }),

        getHighestRatedProducts: builder.query<IProductShort[], void>({
            query: () => `homepage/highest-rated`,
            transformResponse: (res: any) => res.result,
        }),

        getFooter: builder.query<IGetFooter, void>({
            query: () => `/homepage/footer`,
            transformResponse: (res: any) => res.result,
        }),

        getHomepagePopup: builder.query<IGetHomepagePopup, void>({
            query: () => `/homepage/homepage-popup`,
            transformResponse: (res: any) => res.result,
        }),
    }),
});

export const {
    useGetFeaturedBrandsQuery,
    useGetNewArrivalsQuery,
    useGetBestSellerProductsQuery,
    useGetHighestRatedProductsQuery,
    useGetFooterQuery,
    useGetHomepagePopupQuery,
} = homeApi;
