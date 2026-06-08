import { axiosInstance } from '../react/utils/axios';
import {
    IBrand,
    ICategories,
    ICommonQueryParams,
    INewUpdates,
    IOrder,
    IOrderUpdates,
    IProduct,
    IRevenueUpdate,
    ISellers,
    ITotalCustomers,
    ITotalOrders,
    ITotalSales,
    ITotalSellers,
} from './types/response';

export const orderStatusColors = {
    'Order Placed': '#1D9679',
    'Order Canceled': '#FF0404',
    'Order Delivered': '#1F84D2',
    'Order Shipped': '#AEB109',
    'Order Processing': '#FF7E06',
    'Order Confirmed': '#74C1FD',
};

export const getByFilterOptions = [
    {
        label: 'Order Count',
        value: 'orderCount',
    },
    {
        label: 'Order Amount',
        value: 'orderAmount',
    },
];

// ! fetch new updates
export const getNewUpdates = async () => {
    const res = await axiosInstance('/dashboard/new');
    return res.data.result as INewUpdates;
};

// ! fetch overview data
export const getTotalOrders = async (params: ICommonQueryParams | void) => {
    const res = await axiosInstance(`/dashboard/total-orders`, { params });
    return res.data.result as ITotalOrders;
};

export const getTotalSales = async (params: ICommonQueryParams | void) => {
    const res = await axiosInstance(`/dashboard/total-sales`, { params });
    return res.data.result as ITotalSales;
};

export const getTotalSellers = async (params: ICommonQueryParams | void) => {
    const res = await axiosInstance(`/dashboard/total-sellers`, { params });
    return res.data.result as ITotalSellers;
};

export const getTotalCustomers = async (params: ICommonQueryParams | void) => {
    const res = await axiosInstance(`/dashboard/total-customers`, { params });
    return res.data.result as ITotalCustomers;
};

export const getRevenueUpdate = async (params: ICommonQueryParams | void) => {
    const res = await axiosInstance(`/dashboard/sales-amount`, { params });
    return res.data.result as IRevenueUpdate;
};

export const getTopCategories = async (params: ICommonQueryParams | void) => {
    const res = await axiosInstance('/dashboard/top-categories', { params });
    return res.data.result as ICategories;
};

export const getTopBrands = async (params: ICommonQueryParams | void) => {
    const res = await axiosInstance('/dashboard/top-brands', { params });
    return res.data.result as {
        topBrands: IBrand[];
        totalBrands: number;
    };
};

export const getRecentOrders = async () => {
    const res = await axiosInstance('/dashboard/recent-orders');
    return res.data.result as IOrder[];
};

export const getOrderUpdates = async () => {
    const res = await axiosInstance('/dashboard/order-updates');
    return res.data.result as IOrderUpdates[];
};

export const getRecentProducts = async () => {
    const res = await axiosInstance('/dashboard/recent-products');
    return res.data.result as {
        totalProducts: number;
        stockOutProducts: number;
        recentProducts: IProduct[];
    };
};

export const getMostSellingProducts = async () => {
    const res = await axiosInstance('/dashboard/most-selling-products');
    return res.data.result.mostSellingProducts as IProduct[];
};

export const getEarningsFromSellers = async () => {
    const res = await axiosInstance('/dashboard/earning-from-sellers');
    return res.data.result.topShops as ISellers[];
};

export const getTopRatedSellers = async () => {
    const res = await axiosInstance('/dashboard/top-rated-sellers');
    return res.data.result.topShops as ISellers[];
};

export const getTopSellerByEarnings = async () => {
    const res = await axiosInstance('/dashboard/top-sellers');
    return res.data.result.topShops as ISellers[];
};
