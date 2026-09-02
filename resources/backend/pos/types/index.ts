declare global {
    interface Window {
        config: {
            countries: ICountry[];
            canOverrideStock?: boolean;
            posMemoDateEnabled?: boolean;
            posMemoNoEnabled?: boolean;
            posMushakChallanNoEnabled?: boolean;
        };
    }
}

export interface IFilters {
    page: number;
    limit?: number;
    warehouseId: number | string | null;
    brandId: number | string | null;
    categoryId: number | string | null;
    searchKey: string | null;
}

export interface IFilterData {
    warehouses: {
        id: number;
        name: string;
    }[];
    brands: {
        id: number;
        name: string;
    }[];
    categories: {
        id: number;
        name: string;
    }[];
}

export interface IPosProductVariation {
    id: number;
    productId: number;
    product: {
        id: number;
        name: string;
        thumbnailImg: string;
    };
    name: string;
    code: string;
    sku: string;
    stocks: {
        id: number;
        warehouseId: number;
        stockQty: number;
    }[];
    availableStock: number;
    combinations: {
        id: number;
        name: string;
    }[];
    image: string;
    basePrice: number;
    discountedBasePrice: number;
    basePriceWithTax: number;
    discountedBasePriceWithTax: number;
    tax: number;
}

export interface ICustomer {
    id: number;
    name: string;
    email?: string;
    phone?: string;
}
export interface ICountry {
    id: number;
    code: string;
    name: string;
}
export interface IState {
    id: number;
    country_id: number;
    name: string;
    is_active: number;
}
export interface ICity {
    id: number;
    name: string;
}
export interface IArea {
    id: number;
    name: string;
}

export interface IPosCartGroup {
    posCartGroupId: number;
    posCarts: {
        id: number;
        productVariationId: number;
        warehouseId: number;
        qty: number;
        product: IPosProductVariation['product'];
        variation: {
            id: number;
            productId: number;
            product: IPosProductVariation['product'];
            name: string;
            code: string;
            sku: string;
            stocks: {
                id: number;
                warehouseId: number;
                stockQty: number;
            }[];
            combinations: {
                id: number;
                name: string;
            }[];
            image: string;
            basePrice: number;
            discountedBasePrice: number;
            basePriceWithTax: number;
            discountedBasePriceWithTax: number;
            tax: number;
            availableStock: number;
        };
    }[];
}

export interface IHoldCartGroup extends IPosCartGroup {
    onHold: boolean;
    createdAt: string;
}

export interface IAddress {
    id: number;
    userId: number;

    countryId: number;
    country: ICountry;

    stateId: number;
    state: IState;

    cityId?: number | null;
    city?: ICity | null;

    areaId?: number | null;
    area?: (IArea & { zone_id: number }) | null;

    address: string;
    fullAddress: string;
    type?: string;
    direction?: string;
    postalCode?: string;
    isDefault: number;
}

export interface IRecentOrder {
    id: number;
    order_code: string;
    created_at: string;
    customer_name: string;
    total_amount: number;
    payment_method: string;
}

export interface IPaymentSplit {
    id: number;
    method: 'cash' | 'card' | 'cash_on_delivery' | 'card_on_delivery';
    amount: number;
}

export interface InvoiceData {
    order_number: string;
    order_date: string;
    business: {
        name: string;
        address: string;
        phone: string;
        email: string;
        logo?: string;
    };
    customer: {
        name: string;
        address: string;
        phone: string;
        email: string;
    };
    items: InvoiceItem[];
    subtotal: number;
    tax_rate: number;
    tax_amount: number;
    shipping: number;
    discount: number;
    total: number;
    notes: string;
    payment_method: string;
    payment_splits: IPaymentSplit[];
    total_paid: number;
    change_return: number;
    qr_code_image?: string;
    qr_code_url?: string;
}

export interface InvoiceItem {
    name: string;
    size?: string;
    unit_price: number;
    quantity: number;
    amount: number;
}
