import { ICategoryShort } from '.';
import { ICartProduct, IProductShort, IReview } from './product';
import { IShop } from './shop';

export interface OrderShort {
    id: number;
    orderGroupId: number;
    code: string;
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
    paymentStatus: 'paid' | 'unpaid';
}

export interface OrderTimeline {
    id: number;
    status: string;
    note: string;
    createdDate: string;
    createdTime: string;
}

export interface IOrder {
    id: number;
    orderGroupId: number;
    code: string;
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
    paymentStatus: 'unpaid' | 'paid';
    paymentMethod: string;
    deliveryAddress: IAddress;
    items: (Omit<ICartProduct, 'shop'> & {
        review: IReview | null;
    })[];
    shop: IShop;
    orderTimeline: OrderTimeline[];
}

export interface IOrderSuccess {
    summary: {
        customerName: string;
        shippingAddress: string;
        billingAddress?: string;
        phone: string;
        email: string;
        paymentMethod: string;
        paymentMethodToShow: string;
    };
    orders: IOrder[];
}

export interface ILocallyStoredUserAddress {
    country: {
        id: number;
        name: string;
    };
    state: {
        id: number;
        name: string;
    };
    city: {
        id: number;
        name: string;
    };
    area: {
        id: number;
        name: string;
        zone_id: number;
    };
}

export interface IAddress {
    id: number;
    countryId: number;
    country: ICountry;
    stateId: number;
    state: IState;
    cityId: number;
    city: ICity;
    areaId?: number;
    phone: string;
    area?: IArea;
    address: string;
    type: 'home' | 'office' | 'other';
    direction?: string;
    postalCode?: string;
    isDefault: 0 | 1;
    created_at: string;
    updated_at: string;
}

export interface ICountry {
    id: number;
    code: string;
    name: string;
    is_active: 0 | 1;
    created_by: null;
}

export interface IState {
    id: number;
    country_id: number;
    name: string;
    is_active: 0 | 1;
    created_by: null;
}

export interface ICity {
    id: number;
    state_id: number;
    name: string;
    is_active: 0 | 1;
    created_by: null;
}

export interface IArea {
    id: number;
    city_id: number;
    zone_id: number;
    name: string;
    is_active: 0 | 1;
    created_by: null;
}

export interface ICouponShort {
    id: number;
    code: string;
    info: string;
    shopId: number;
    shopSlug: string;
    discountType: 'amount' | 'percentage';
    discount: number;
    isFreeShipping: 0 | 1;
    startDate: string;
    endDate: string;
    minSpend: number;
    maxDiscountAmount: number | null;
    banner: string;
}

export interface ICouponCondition {
    id: number;
    coupon_id: number;
    text: string;
    created_at: string;
}

export interface ICoupon extends ICouponShort {
    conditions: ICouponCondition[];
    products: IProductShort[];
    categories: ICategoryShort[];
}

export interface IShippingCharge {
    id: number;
    shopId: number;
    zoneId: number;
    charge: number;
}

export interface IOrderPayload extends ICheckoutCustomerDetails {
    shippingAddressId: number;
    billingAddressId?: number;
    shopIds: number[];
    couponCodes: string[];
    cartIds: number[];
    paymentMethod: string;
}
export interface IManualOrderPayload extends ICheckoutCustomerDetails {
    shippingAddress: IAddress;
    billingAddress?: IAddress;
    shopIds: number[];
    couponCodes: string[];
    cartIds: number[];
    paymentMethod: string;
    password:string;
}
export interface ICheckoutCustomerDetails {
    name: string;
    email: string;
    phone: string;
    note: string;
}

export interface ICheckoutData {
    customerDetails: ICheckoutCustomerDetails;
    shippingAddress?: IAddress;
    billingAddress?: IAddress;
    paymentMethod: string;

    selectedShops: Record<string, boolean>;
    shippingCharge: number;
    coupons: ICouponShort[];
    isVerified:boolean;
    otpExpiryTimestamp?: number | null; // Unix timestamp in milliseconds

}
