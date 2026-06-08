import { IPage } from '../../../types';
import { apiSlice } from './apiSlice';

const pageApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getPage: builder.query<IPage, string>({
            query: (slug) => `/pages/${slug}`,
            transformResponse: (response: any) => response.result,
        }),
    }),
});

export const { useGetPageQuery } = pageApi;
