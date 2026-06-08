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
    shopName: string;
    newOrders: number;
    newCustomers: number;
    stockLow: number;
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
export interface ITotalEarnings {
    totalEarnings: number;
    totalEarningsComparison: number;
}
export interface ITotalProducts {
    totalProducts: number;
    totalProductsComparisonPercentage: number;
}

export interface ISaleAmountChart {
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

export interface IOrderCounts {
    orderPlaced: number;
    confirmed: number;
    processing: number;
    shipped: number;
    delivered: number;
    cancelled: number;
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

export interface IShop {
    id: number;
    user_id: number;
    is_approved: number;
    is_verified_by_admin: number;
    is_published: number;
    logo: string;
    banner: string;
    tagline: string;
    name: string;
    slug: string;
    info: string;
    rating: number;
    address: string;
    min_order_amount: number;
    admin_commission_percentage: number;
    current_balance: number;
    default_shipping_charge: number;
    manage_stock_by: string;
    monthly_goal_amount: number;
    is_cash_payout: number;
    is_bank_payout: number;
    bank_name: string;
    bank_acc_name: string;
    bank_acc_no: number;
    bank_routing_no: number;
}

export interface ICampaign {
    id: number;
    name: string;
    slug: string;
    thumbnailImage: string;
    banner: string;
    shortDescription: string;
    shopId: number;
    shop: IShop;
    startDate: string;
    endDate: string;
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

export interface ITopSellingProducts {
    mostSellingProducts: IProduct[];
    stockOut: number;
    totalCommission: number;
}
