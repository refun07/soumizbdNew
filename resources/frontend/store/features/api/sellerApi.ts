import { apiSlice } from './apiSlice';

const sellerApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Sellers'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            signup: builder.mutation({
                query: (body) => ({
                    url: `/seller/signup`,
                    method: 'POST',
                    body,
                }),
                transformResponse: (response: any) => {
                    return response.result;
                },
            }),
        }),
    });

export const { useSignupMutation } = sellerApi;
