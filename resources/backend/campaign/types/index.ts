import { IProductVariation } from '../../../frontend/types/product';

export interface IProductShort {
    id: number;
    name: string;
    basePrice: number;
    hasVariation: number;
    thumbnailImage: string;
    variations: IProductVariation[];
}

export interface IBrands {
    id: number;
    name: string;
}

export interface ICategory {
    id: number;
    name: string;
}

export interface IProductFilter {
    page?: number;
    limit?: number;

    searchKey?: string;

    brandId?: number | null;
    categoryId?: number | null;
}

export interface ICampaignProduct {
    id: number;
    product: IProductShort;
    discountType: 'flat' | 'percent';
    discountValue: number;
    variation: IProductVariation;
}
