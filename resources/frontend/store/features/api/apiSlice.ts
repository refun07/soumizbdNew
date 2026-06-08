import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';
import { ICategoryShort, STORAGE_KEYS } from '../../../types';
import { API_URL } from '../../../utils/env';

export const apiSlice = createApi({
    reducerPath: 'api',
    baseQuery: fetchBaseQuery({
        baseUrl: API_URL,
        prepareHeaders: (headers) => {
            const token = localStorage.getItem(STORAGE_KEYS.AUTH_KEY);

            if (token) {
                headers.set('Authorization', `Bearer ${token}`);
            }
            headers.set('Accept', 'application/json');
            headers.set('Accept-Language', localStorage.getItem('i18nextLng')!);

            // ZoneId header functionality removed - location feature disabled

            return headers;
        },
    }),
    tagTypes: ['Cart', 'Order'],
    endpoints: (builder) => ({
        getAllCategories: builder.query<
            {
                categories: ICategoryShort[];
            },
            void
        >({
            query: () => '/categories',
            transformResponse: (res: any) => res.result,
        }),
    }),
});

export const { useGetAllCategoriesQuery } = apiSlice;
