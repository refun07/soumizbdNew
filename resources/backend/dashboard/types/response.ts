import { orderStatusColors } from '../utils';

export const timelines = [
    'overall',
    'today',
    'yesterday',
    'thisWeek',
    'thisMonth',
    'thisYear',
] as const;

export type TGetBy = 'orderCount' | 'orderAmount';

export interface ICommonQueryParams {
    timeline?: (typeof timelines)[number];
    getBy?: TGetBy;
}

export interface INewUpdates {
    newOrders: number;
    newSellers: number;
    newCustomers: number;
}

// ! overview data types
export interface ITotalOrders {
    totalOrders: number;
    totalOrdersComparisonPercentage: number;
}

export interface ITotalSales {
    totalSalesAmount: number;
    totalSalesComparisonPercentage: number;
}

export interface ITotalSellers {
    totalSellers: number;
    totalSellersComparisonPercentage: number;
}
export interface ITotalCustomers {
    totalCustomers: number;
    totalCustomersComparisonPercentage: number;
}

export interface IRevenueUpdate {
    days: string[];
    totalAmounts: number[];
}

export interface ICategory {
    name: string;
    totalSaleCount: number;
    totalSaleAmount: number;
}

export interface ICategories {
    topCategories: ICategory[];
    totalCategories: number;
}

export interface IBrand {
    name: string;
    thumbnailImage: string;
    totalSaleCount: number;
    totalSaleAmount: number;
}

export interface IOrder {
    id: number;
    orderGroupId: number;
    code: number;
    codeToShow: string;
    createdDate: string;
    createdTime: string;
    updatedDate: string;
    updatedTime: string;
    subtotalAmount: number;
    taxAmount: number;
    shippingChargeAmount: number;
    discountAmount: number;
    couponDiscountAmount: number;
    totalAmount: number;
    deliveryStatus: string;
    deliveryStatusToShow: string;
    paymentStatus: string;
    customerName: string;
}

export interface IOrderUpdates {
    id: number;
    status: keyof typeof orderStatusColors;
    note: string;
    createdDate: string;
    createdTime: string;
}

export interface IProduct {
    name: string;
    slug: string;
    thumbnailImg: string;
    unit: string;
    basePrice: number;
    totalSaleCount: number;
}

export interface ISellers {
    id: number;
    name: string;
    slug: string;
    shopInfo: null;
    rating: {
        average: number;
        total: number;
        fiveStarsCount: number;
        fourStarsCount: number;
        threeStarsCount: number;
        twoStarsCount: number;
        oneStarsCount: number;
    };
    address: string;
    minOrderAmount: number;
    defaultShippingCharge: number;
    logo: string;
    banner: string;
    tagline: string;

    earningFromSellers: number; // Earnings From Seller card (chart)

    positiveImpressions: number; // Top Rated Sellers

    ordersAmount: number; // top seller by earning
}
