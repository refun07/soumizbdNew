import { IBrandShort } from '../../../types/brand';
import { IProductShort } from '../../../types/product';
import { IShop } from '../../../types/shop';
import { apiSlice } from './apiSlice';

const searchApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Search'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getSearchSuggestion: builder.query<
                {
                    products: IProductShort[];
                    brands: IBrandShort[];
                    shops: IShop[];
                },
                string
            >({
                query: (query) =>
                    `/search?searchKey=${encodeURIComponent(query)}`,
                providesTags: (_r, _e, query) => [
                    { type: 'Search', id: query },
                ],
                transformResponse: (res: any) => res.result,
            }),
        }),
    });

export const { useLazyGetSearchSuggestionQuery } = searchApi;
