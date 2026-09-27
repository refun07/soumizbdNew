import { IGetsQueryParams, IResponse } from '../../../types';
import { IBrandGroup, IBrandShort } from '../../../types/brand';
import { apiSlice } from './apiSlice';

const brandApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Brands'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getBrands: builder.query<
                IResponse<IBrandShort[]>,
                IGetsQueryParams & { group?: string | null }
            >({
                query: (params) => ({
                    url: `/brands`,
                    params: {
                        ...params,
                        searchKey: params.query,
                        group: params.group || undefined,
                    },
                }),
                providesTags: ['Brands'],
            }),
            getBrandGroups: builder.query<IBrandGroup[], void>({
                query: () => `/brands/groups`,
                transformResponse: (res: { result: IBrandGroup[] }) =>
                    res.result,
                providesTags: ['Brands'],
            }),
            getBrandDetails: builder.query<IBrandShort | null, string>({
                query: (slug) => `/brands/details/${slug}`,
                transformResponse: (res: {
                    result: IBrandShort | null;
                }) => res.result,
                providesTags: ['Brands'],
            }),
        }),
    });

export const {
    useGetBrandsQuery,
    useGetBrandGroupsQuery,
    useGetBrandDetailsQuery,
} = brandApi;
