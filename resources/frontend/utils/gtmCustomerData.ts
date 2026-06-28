import { IAddress, ICouponShort } from '../types/checkout';
import { ICartProduct } from '../types/product';
import { IUser } from '../types/state';

type Nullable<T> = T | null | undefined;

export type GTMCustomerContext = {
    visitorLoginState: 'logged-in' | 'logged-out';
    visitorType: 'customer' | 'guest';
    visitorId?: number | string;
    customerId?: number | string;
    customerTotalOrders?: number;
};

export type GTMUserData = {
    email_hash?: string;
    phone_hash?: string;
    first_name_hash?: string;
    last_name_hash?: string;
    city?: string;
    state?: string;
    country?: string;
    postal_code?: string;
};

export type GTMAddress = {
    first_name?: string;
    first_name_hash?: string;
    last_name?: string;
    last_name_hash?: string;
    company?: string;
    address_1?: string;
    address_2?: string;
    city?: string;
    state?: string;
    postcode?: string;
    country?: string;
    email?: string;
    emailhash?: string;
    email_hash?: string;
    phone?: string;
    phone_hash?: string;
};

const clean = (value: unknown): string => {
    if (value === null || value === undefined) return '';
    return String(value).trim();
};

const cleanLower = (value: unknown): string => clean(value).toLowerCase();

const toNumber = (value: unknown, fallback = 0): number => {
    const number = Number(value);
    return Number.isFinite(number) ? number : fallback;
};

const splitName = (name?: string) => {
    const parts = clean(name).split(/\s+/).filter(Boolean);
    return {
        firstName: parts[0] || '',
        lastName: parts.slice(1).join(' '),
    };
};

export const sha256Hash = async (value: unknown): Promise<string> => {
    const normalized = cleanLower(value);
    if (!normalized || !window.crypto?.subtle) return '';

    const bytes = new TextEncoder().encode(normalized);
    const hashBuffer = await window.crypto.subtle.digest('SHA-256', bytes);
    return Array.from(new Uint8Array(hashBuffer))
        .map((byte) => byte.toString(16).padStart(2, '0'))
        .join('');
};

export const buildGTMCustomerContext = (
    user?: Nullable<IUser>,
    totalOrders?: Nullable<number>,
): GTMCustomerContext => {
    const isLoggedIn = Boolean(user?.id);

    return {
        visitorLoginState: isLoggedIn ? 'logged-in' : 'logged-out',
        visitorType: isLoggedIn ? 'customer' : 'guest',
        ...(user?.id ? { visitorId: user.id, customerId: user.id } : {}),
        ...(totalOrders !== null && totalOrders !== undefined
            ? { customerTotalOrders: totalOrders }
            : {}),
    };
};

export const mapCheckoutAddressToGTMAddress = (
    address?: Nullable<IAddress>,
    customer?: {
        name?: string;
        email?: string;
        phone?: string;
    },
): GTMAddress => {
    const { firstName, lastName } = splitName(customer?.name);

    return {
        first_name: firstName,
        last_name: lastName,
        company: '',
        address_1: clean(address?.address),
        address_2: clean(address?.direction),
        city: clean(address?.city?.name || address?.area?.name),
        state: clean(address?.state?.name),
        postcode: clean(address?.postalCode),
        country: clean(address?.country?.code || address?.country?.name),
        email: clean(customer?.email),
        phone: clean(customer?.phone || address?.phone),
    };
};

const withHashedAddress = async (address: GTMAddress): Promise<GTMAddress> => ({
    ...address,
    first_name_hash: await sha256Hash(address.first_name),
    last_name_hash: await sha256Hash(address.last_name),
    emailhash: await sha256Hash(address.email),
    email_hash: await sha256Hash(address.email),
    phone_hash: await sha256Hash(address.phone),
});

export const buildGTMUserData = async (
    address?: Nullable<GTMAddress>,
): Promise<GTMUserData> => ({
    email_hash: await sha256Hash(address?.email),
    phone_hash: await sha256Hash(address?.phone),
    first_name_hash: await sha256Hash(address?.first_name),
    last_name_hash: await sha256Hash(address?.last_name),
    city: clean(address?.city),
    state: clean(address?.state),
    country: clean(address?.country),
    postal_code: clean(address?.postcode),
});

export const buildGTM4WPData = async ({
    user,
    totalOrders,
    totalOrderValue,
    billingAddress,
    shippingAddress,
}: {
    user?: Nullable<IUser>;
    totalOrders?: Nullable<number>;
    totalOrderValue?: Nullable<number>;
    billingAddress?: Nullable<GTMAddress>;
    shippingAddress?: Nullable<GTMAddress>;
}) => {
    const { firstName, lastName } = splitName(user?.name);
    const email = clean(user?.email || billingAddress?.email);
    const billingEmail = clean(billingAddress?.email || user?.email);
    const isLoggedIn = Boolean(user?.id);

    return {
        visitorLoginState: isLoggedIn ? 'logged-in' : 'logged-out',
        visitorType: isLoggedIn ? 'customer' : 'guest',
        visitorEmail: email,
        visitorEmailHash: await sha256Hash(email),
        visitorRegistrationDate: '',
        visitorUsername: clean(user?.name),
        pagePostType: 'frontpage',
        pagePostType2: 'single-page',
        pagePostAuthor: '',
        customerTotalOrders: totalOrders ?? 0,
        customerTotalOrderValue: totalOrderValue ?? 0,
        customerFirstName: firstName || billingAddress?.first_name || '',
        customerLastName: lastName || billingAddress?.last_name || '',
        customerBillingFirstName: clean(billingAddress?.first_name),
        customerBillingLastName: clean(billingAddress?.last_name),
        customerBillingCompany: clean(billingAddress?.company),
        customerBillingAddress1: clean(billingAddress?.address_1),
        customerBillingAddress2: clean(billingAddress?.address_2),
        customerBillingCity: clean(billingAddress?.city),
        customerBillingState: clean(billingAddress?.state),
        customerBillingPostcode: clean(billingAddress?.postcode),
        customerBillingCountry: clean(billingAddress?.country),
        customerBillingEmail: billingEmail,
        customerBillingEmailHash: await sha256Hash(billingEmail),
        customerBillingPhone: clean(billingAddress?.phone),
        customerShippingFirstName: clean(shippingAddress?.first_name),
        customerShippingLastName: clean(shippingAddress?.last_name),
        customerShippingCompany: clean(shippingAddress?.company),
        customerShippingAddress1: clean(shippingAddress?.address_1),
        customerShippingAddress2: clean(shippingAddress?.address_2),
        customerShippingCity: clean(shippingAddress?.city),
        customerShippingState: clean(shippingAddress?.state),
        customerShippingPostcode: clean(shippingAddress?.postcode),
        customerShippingCountry: clean(shippingAddress?.country),
    };
};

const getCartUnitPrice = (cart: ICartProduct & { price?: number }) =>
    toNumber(
        cart.price ??
            cart.variation?.discountedBasePrice ??
            cart.variation?.basePrice ??
            cart.product?.discountedBasePrice ??
            cart.product?.basePrice,
    );

const buildItems = (carts: ICartProduct[]) =>
    carts.map((cart) => {
        const product = cart.product;
        const variation = cart.variation;
        const price = getCartUnitPrice(cart);

        return {
            item_id: clean(variation?.sku || `${product?.id}-${variation?.id}`),
            id: clean(variation?.sku || variation?.id || product?.id),
            item_name: clean(
                variation?.name
                    ? `${product?.name} (${variation.name})`
                    : product?.name,
            ),
            name: clean(product?.name),
            sku: clean(variation?.sku),
            price,
            quantity: toNumber(cart.qty, 1),
            item_category: clean(product?.categories?.[0]?.name),
        };
    });

export const buildGTM4WPCartContent = ({
    carts,
    coupons,
    subtotal,
    discountTotal,
    total,
}: {
    carts: ICartProduct[];
    coupons?: ICouponShort[];
    subtotal: number;
    discountTotal: number;
    total: number;
}) => ({
    totals: {
        applied_coupons: coupons?.map((coupon) => coupon.code) ?? [],
        discount_total: discountTotal,
        subtotal,
        total,
    },
    items: buildItems(carts),
});

export const buildGTM4WPOrderData = async ({
    orderCode,
    paymentMethod,
    shippingMethod,
    status = 'processing',
    carts,
    coupons,
    subtotal,
    discountTotal,
    shippingTotal,
    tax,
    total,
    customerId,
    billingAddress,
    shippingAddress,
}: {
    orderCode: number | string;
    paymentMethod: string;
    shippingMethod?: string;
    status?: string;
    carts: ICartProduct[];
    coupons?: ICouponShort[];
    subtotal: number;
    discountTotal: number;
    shippingTotal: number;
    tax: number;
    total: number;
    customerId?: number | string;
    billingAddress: GTMAddress;
    shippingAddress: GTMAddress;
}) => {
    const couponCodes = coupons?.map((coupon) => coupon.code) ?? [];

    return {
        attributes: {
            date: new Date().toISOString(),
            order_number: orderCode,
            order_key: clean(orderCode),
            payment_method: paymentMethod,
            payment_method_title: paymentMethod,
            shipping_method: shippingMethod || '',
            status,
            coupons: couponCodes.join(','),
        },
        totals: {
            currency: window.config.currency.code,
            discount_total: discountTotal,
            discount_tax: 0,
            shipping_total: shippingTotal,
            shipping_tax: 0,
            cart_tax: tax,
            total,
            total_tax: tax,
            total_discount: discountTotal,
            subtotal,
            tax_totals: [],
        },
        customer: {
            id: customerId || '',
            billing: await withHashedAddress(billingAddress),
            shipping: shippingAddress,
        },
        items: buildItems(carts),
    };
};
