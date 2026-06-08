import { IPaginationMeta, Impressions } from '.';
import { IAddress } from './checkout';
import { IProductShort, IReview } from './product';
import { IUser } from './state';

export interface IShop {
    id: number;
    name: string;
    slug: string;
    shopInfo: null;
    rating: {
        average: number;
        total: number;
    };
    address: IAddress | null;
    minOrderAmount: number;
    defaultShippingCharge: number;
    logo: string;
    banner: string;
    tagline: string;
}

// sections of shop details page
export interface IBoxedWidthBanner {
    type: 'boxed-width-banner';
    values: {
        box1Link: string;
        box1Banners: string[];
        box2Link: string;
        box2Banners: string[];
    };
}
export interface IShopProductsSection {
    title: string;
    type: 'products';
    values: IProductShort[];
}
export interface IFullWidthBanner {
    type: 'full-width-banner';
    values: {
        link: string;
        banners: string[];
    };
}

export type IShopSection =
    | IBoxedWidthBanner
    | IShopProductsSection
    | IFullWidthBanner;

export interface IShopImpression {
    id: number;
    userId: number;
    user: IUser;
    shopId: number;
    shop: IShop;
    impression: Impressions;
    createdDate: string;
}

export interface IReviewSummary {
    average: number;
    total: number;
    fiveStarsCount: number;
    fourStarsCount: number;
    threeStarsCount: number;
    twoStarsCount: number;
    oneStarsCount: number;
}

export interface IImpressionSummary {
    total: number;
    positive: number;
    negative: number;
    neutral: number;
}

export interface IShopProfileInfo {
    bestSellingProducts: IProductShort[];
    overview: {
        age: {
            count: string;
            unit: string;
        };
        totalProducts: number;
        deliveryPercentage: string;
        overallProductReview: string;
        positiveShopReviewPercentage: string;
    };
    productReviews: {
        summary: IReviewSummary;
        allReviews: {
            data: IReview[];
            meta: IPaginationMeta;
        };
    };
    shopReviews: {
        summary: IImpressionSummary;
        allReviews: IShopImpression[];
    };
}
