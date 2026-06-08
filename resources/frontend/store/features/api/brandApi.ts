import { IGetsQueryParams, IResponse } from '../../../types';
import { IBrandShort } from '../../../types/brand';
import { apiSlice } from './apiSlice';

const brandApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Brands'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getBrands: builder.query<
                IResponse<IBrandShort[]>,
                IGetsQueryParams
            >({
                query: (params) => ({
                    url: `/brands`,
                    params: {
                        ...params,
                        searchKey: params.query,
                    },
                }),
                providesTags: ['Brands'],
            }),
        }),
    });

export const { useGetBrandsQuery } = brandApi;
