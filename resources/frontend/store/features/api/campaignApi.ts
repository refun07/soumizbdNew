import { IGetsQueryParams, IPaginationMeta } from '../../../types';
import { ICampaign } from '../../../types/campaign';
import { IProductShort } from '../../../types/product';
import { apiSlice } from './apiSlice';

const campaignApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Campaign'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getCampaigns: builder.query<
                {
                    megaCampaigns: ICampaign[];
                    sellerCampaigns: {
                        data: ICampaign[];
                        meta: IPaginationMeta;
                    };
                },
                (IGetsQueryParams & { shopSlug?: string }) | void
            >({
                query: (params) => ({
                    url: '/campaigns',
                    method: 'GET',
                    params: params || {},
                }),
                providesTags: ['Campaign'],
                transformResponse: (res: any) => res.result,
            }),

            getCampaignDetails: builder.query<
                {
                    campaign: ICampaign;
                    products: {
                        data: IProductShort[];
                        meta: IPaginationMeta;
                    };
                },
                IGetsQueryParams & { slug: string }
            >({
                query: ({ slug, ...params }) => ({
                    url: `/campaigns/details/${slug}`,
                    params: { ...params, searchKey: params.query },
                }),
                providesTags: (_result, _error, { slug }) => [
                    { type: 'Campaign', id: slug },
                ],
                transformResponse: (res: any) => res.result,
            }),
        }),
    });

export const { useGetCampaignsQuery, useGetCampaignDetailsQuery } = campaignApi;
