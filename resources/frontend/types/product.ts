import { ICategory } from '.';
import { IBrandShort } from './brand';
import { IShop } from './shop';
import { IUser } from './state';

export interface IProductVariation {
    id: number;
    name: string;
    productId: number;
    code: string | null;
    sku: string;
    stocks: {
        id: number;
        warehouseId: number;
        stockQty: number;
    }[];
    image: string;
    basePrice: number;
    discountedBasePrice: number;
    basePriceWithTax: number;
    discountedBasePriceWithTax: number;
    tax: number;
    dealEnds: string | null;
    isPreOrder: boolean;
    isSoldOut: boolean;
    currentOrders: number;
    remainingOrders: number;
    preOrderData: {
        is_active?: boolean;
        enabled?: boolean;
        order_limit?: number;
        total_order_limit?: number;
        start_date?: string | null;
        end_date?: string | null;
        expected_release_date?: string | null;
        deposit_amount?: number | null;
        description?: string;
    } | null;
}

export interface VariationCombination {
    id: number;
    name: string;
    isColor: boolean;
    values: {
        id: number;
        name: string;
        colorCode?: string; // hex color code
        colorImage?: string | null;
        matchVariationCode: `${string | number}:${string | number}`;
    }[];
}

export interface ISizeChartTable {
    columns: string[];
    rows: string[][];
}

export interface IPromoCode {
    id: number;
    code: string;
    info: string;
    shopId: number;
    shopSlug: string;
    discountType: 'amount' | 'percentage';
    discountAmount: number;
    isFreeShipping: number;
    startDate: string;
    endDate: string;
    minSpend: number;
    maxDiscountAmount: number | null;
    banner: string;
}

export interface IBadge {
    id: number;
    name: string;
    textColor: string;
    bgColor: string;
}

export interface IProductShort {
    id: number;
    name: string;
    slug: string;
    inWishlist: boolean;
    thumbnailImg: string;
    basePrice: number;
    discountedBasePrice: number;
    unit: string;
    stockQty: number;
    hasVariation: number;
    badges: IBadge[];
    tags: { id: number; name: string }[];
    rating: {
        average: number;
        total: number;
    };
    shop_id: number;
    shipping_class_id: number;
    shop?: {
        id: number;
        name: string;
        slug: string;
    } | null;
    isPreOrder: boolean;
    preOrderData: {
        is_active: boolean;
        order_limit: number;
        total_order_limit: number;
        start_date: string | null;
        end_date: string | null;
        expected_release_date: string | null;
        deposit_amount: number | null;
        description: string;
    } | null;
    hasAvailableStock: boolean;
    maxPurchaseQty: number;
}

export interface IProduct extends IProductShort {
    deliveryHours: number;
    codAvailable: boolean;

    hasEmi: 1 | 0;
    emiInfo: string;

    hasWarranty: 1 | 0;
    warrantyInfo: string;

    rootCategory: ICategory;
    categories: ICategory[];

    description: string;
    images: string[];
    videoLink: string;
    shop: IShop;
    brand: IBrandShort;
    promoCode?: IPromoCode;
    relatedProducts: IProductShort[];
    relatedProductsSidebarEnabled: boolean;
    relatedProductsBottomEnabled: boolean;
    variations: IProductVariation[];
    variationCombinations: VariationCombination[];
    hasSizeChart: boolean;
    sizeChartImage: string | null;
    sizeChartTable: ISizeChartTable | null;
    color_image_enabled?: boolean;
}

export interface ICartProduct {
    id: number;
    userId: number;
    guestUserId: number;
    productVariationId: number;
    warehouseId: number;
    qty: number;
    product: IProductShort;
    shop: IShop;
    variation: IProductVariation;
}

export interface IReview {
    id: number;
    userId: number;
    user: IUser;
    rating: number;
    description: string;
    images: {
        id: number;
        image: string;
    }[];
    product: IProductShort;
    shop: IShop;
    createdDate: string;
}
