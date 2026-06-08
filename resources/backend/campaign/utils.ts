import { objectToFormData } from '../../frontend/utils/ObjectFormData';
import { IPaginatedResponse } from '../react/types/index';
import { axiosInstance } from '../react/utils/axios';
import {
    IBrands,
    ICampaignProduct,
    ICategory,
    IProductFilter,
    IProductShort,
} from './types';

export const getFilterData = async () => {
    const res = await axiosInstance.get('/campaigns/filter-data');
    const filterData: {
        brands: IBrands[];
        categories: ICategory[];
    } = res.data.result;

    return filterData;
};

// pass campaignId to get products of a campaign
// otherwise get all products
export const getProducts = async (
    filters: IProductFilter & { campaignId?: number },
) => {
    const res = await axiosInstance.get('/campaigns/products', {
        params: filters,
    });
    const products: IPaginatedResponse<IProductShort[]> = {
        data: res.data.result.data,
        meta: res.data.result.meta,
    };

    return products;
};

export const getCampaignProducts = async ({
    campaignId,
    ...filters
}: IProductFilter & { campaignId: number }) => {
    const res = await axiosInstance.get(`/campaigns/products/${campaignId}`, {
        params: filters,
    });

    const products: IPaginatedResponse<ICampaignProduct[]> = {
        data: res.data.result.data,
        meta: res.data.result.meta,
    };

    return products;
};

export const addProductToCampaign = async (
    campaignId: number,
    variationIds: number[], // product variation ids
): Promise<ICampaignProduct[]> => {
    const res = await axiosInstance.post(
        `/campaigns/products`,
        objectToFormData({
            campaignId,
            productVariationIds: variationIds,
        }),
        {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        },
    );

    return res.data.result;
};

export const updateCampaignProduct = async (data) => {
    const res = await axiosInstance.post(
        '/campaigns/products/update',
        objectToFormData(data),
        {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        },
    );

    return res.data.result as ICampaignProduct;
};

export const removeCampaignProduct = async (id: number) => {
    const res = await axiosInstance.post(
        `/campaigns/products/delete`,
        objectToFormData({ id }),
        {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        },
    );

    return res.data.result as ICampaignProduct;
};
