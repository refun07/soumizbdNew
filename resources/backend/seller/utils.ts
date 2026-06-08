import dayjs from 'dayjs';
import { axiosInstance } from '../react/utils/axios';
import {
    ICampaign,
    ICommonQueryParams,
    INewUpdates,
    IOrder,
    IOrderCounts,
    IOrderUpdates,
    ISaleAmountChart,
    ITopSellingProducts,
    ITotalEarnings,
    ITotalOrders,
    ITotalProducts,
    ITotalSales,
    timelines,
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

export const getTotalEarnings = async (params: ICommonQueryParams | void) => {
    const res = await axiosInstance(`/dashboard/total-earnings`, { params });
    return res.data.result as ITotalEarnings;
};

export const getTotalProducts = async (params: ICommonQueryParams | void) => {
    const res = await axiosInstance(`/dashboard/total-products`, { params });
    return res.data.result as ITotalProducts;
};

export const getSaleAmountChartData = async (
    params: ICommonQueryParams | void,
) => {
    const res = await axiosInstance(`/dashboard/sales-amount`, { params });
    return res.data.result as ISaleAmountChart;
};

export const getOrderCounts = async () => {
    const res = await axiosInstance('/dashboard/order-count');
    return res.data.result as IOrderCounts;
};

export const getRecentOrders = async () => {
    const res = await axiosInstance('/dashboard/recent-orders');
    return res.data.result as IOrder[];
};

export const getOrderUpdates = async () => {
    const res = await axiosInstance('/dashboard/order-updates');
    return res.data.result as IOrderUpdates[];
};

export const getTopSellingProducts = async () => {
    const res = await axiosInstance('/dashboard/most-selling-products');

    return res.data.result as ITopSellingProducts;
};

export const getActiveCampaigns = async () => {
    const res = await axiosInstance('/dashboard/active-campaigns');
    return res.data.result.activeCampaigns as ICampaign[];
};
