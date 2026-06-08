import { IGetsQueryParams } from '@/frontend/types';
import { IBlogsResponse, ISingleBlogResponse } from '@/frontend/types/blog';
import { apiSlice } from './apiSlice';

const blogSlice = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Blog'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getBlogs: builder.query<
                IBlogsResponse,
                | (IGetsQueryParams & {
                      blogCategoryId?: string;
                  })
                | void
            >({
                query: (params) => ({
                    url: '/blogs',
                    params: params || {},
                }),
                providesTags: ['Blog'],
                transformResponse: (res: any) => res.result,
            }),

            getBlog: builder.query<ISingleBlogResponse, string>({
                query: (slug) => `/blogs/details/${slug}`,
                providesTags: (_result, _error, slug) => [
                    { type: 'Blog', id: slug },
                ],
                transformResponse: (res: any) => res.result,
            }),
        }),
    });

export const { useGetBlogsQuery, useGetBlogQuery } = blogSlice;
