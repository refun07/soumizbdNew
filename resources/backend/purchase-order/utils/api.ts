import axios from 'axios';
import { IProductWithVariations, IWarehouse, ISupplier, IPurchaseOrder } from '../types';

// Create axios instance with CSRF token
const api = axios.create({
    baseURL: '/admin/api',
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/json',
    },
});

// Add CSRF token to requests
const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
if (token) {
    api.defaults.headers.common['X-CSRF-TOKEN'] = token;
}

export interface GetProductsParams {
    warehouseId: number;
    searchKey?: string;
    limit?: number;
    selectedVariationIds?: number[];
}

export const getParentProducts = async (params: GetProductsParams): Promise<IProductWithVariations[]> => {
    const response = await api.get('/purchase-orders/products', {
        params: {
            warehouseId: params.warehouseId,
            searchKey: params.searchKey || '',
            limit: params.limit || 20,
            selectedVariationIds: params.selectedVariationIds || [],
        },
    });
    return response.data.products;
};

export const getProductVariations = async (
    productId: number,
    warehouseId: number,
    selectedVariationIds: number[] = []
): Promise<IProductWithVariations> => {
    const response = await api.get(`/purchase-orders/products/${productId}/variations`, {
        params: {
            warehouseId,
            selectedVariationIds,
        },
    });
    return response.data.product;
};

export const getFilterData = async (): Promise<{ warehouses: IWarehouse[]; suppliers: ISupplier[] }> => {
    // This will be passed from Blade template as initial data
    // But we can also fetch it if needed
    return {
        warehouses: [],
        suppliers: [],
    };
};
