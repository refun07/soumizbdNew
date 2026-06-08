import { IPaginatedResponse } from '../../react/types';

export interface IWarehouse {
    id: number;
    name: string;
}

export interface ISupplier {
    id: number;
    name: string;
}

export interface IProductVariation {
    id: number;
    sku: string;
    code: string | null;
    price: number;
    stockQty: number;
    variationName: string;
}

export interface IProductWithVariations {
    id: number;
    name: string;
    thumbnailImage: string;
    unitName: string;
    hasVariation: boolean;
    variations: IProductVariation[];
}

export interface ISelectedVariation {
    variationId: number;
    productId: number;
    productName: string;
    variationName: string;
    sku: string;
    unitPrice: number;
    quantity: number;
    currentStock: number;
    unitName: string;
    discount: number;
    tax: number;
    purchasedQty?: number;
    expiryDate?: string;
}

export interface IPurchaseOrderFormData {
    warehouseId: number | null;
    supplierId: number | null;
    date: string;
    status: string;
    tax: number;
    discount: number;
    shipping: number;
    note: string;
}

export interface IVariationSelectionModalData {
    product: IProductWithVariations;
    sameQuantityForAll: boolean;
    globalQuantity: number;
    selectedVariationIds: number[];
    variationQuantities: Record<number, number>;
}

export interface IExistingOrderItem {
    id: number;
    productId: number;
    productName: string;
    variationId: number;
    variationName: string;
    sku: string;
    unitPrice: number;
    quantity: number;
    currentStock: number;
    unitName: string;
    discount: number;
    tax: number;
    grandTotal: number;
    expiryDate?: string;
    isDeletedProduct?: boolean;
}

export interface IPurchaseOrder {
    id: number;
    warehouseId: number;
    supplierId: number;
    date: number;
    status: string;
    taxPercentage: number;
    taxValue: number;
    discount: number;
    shipping: number;
    grandTotal: number;
    note: string;
    orders: IExistingOrderItem[];
}

export interface IGroupedSelection {
    productId: number;
    productName: string;
    thumbnailImage?: string;
    variations: ISelectedVariation[];
}

export interface ISummary {
    subtotal: number;
    taxValue: number;
    discount: number;
    shipping: number;
    grandTotal: number;
}
