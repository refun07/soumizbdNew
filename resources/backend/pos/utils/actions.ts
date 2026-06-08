import QueryString from 'qs';
import { IPaginatedResponse } from '../../react/types';
import { objectToFormData } from '../../react/utils/ObjectFormData';
import {
    IAddress,
    IArea,
    ICity,
    ICustomer,
    IFilterData,
    IFilters,
    IHoldCartGroup,
    IPosCartGroup,
    IPosProductVariation,
    IState,
} from '../types';

const urlPrefix = `/${window.location.pathname.split('/')[1]}/api/pos`;

export const getPosProductsVariations = async (params: IFilters) => {
    const query = QueryString.stringify(params);

    const response = await fetch(`${urlPrefix}/product-variations?${query}`);
    const body = await response.json();
    return body.result as Promise<IPaginatedResponse<IPosProductVariation[]>>;
};

export const getPosFilterData = async () => {
    const response = await fetch(`${urlPrefix}/filter-data`);
    return (await response.json()).result as IFilterData;
};

export const addToPosCartGroup = async (body: FormData) => {
    const response = await fetch(`${urlPrefix}/add-to-cart`, {
        method: 'POST',
        body,
    });
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message || 'Failed to add product to cart');
    }
    return result.result as IPosCartGroup;
};

/**
 * increase or decrease the quantity of a product in the cart
 */
export const updatePosCartItem = async (body: FormData) => {
    const response = await fetch(`${urlPrefix}/cart/update`, {
        method: 'POST',
        body,
    });
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message || 'Failed to update cart');
    }
    return result.result.posCarts as IPosCartGroup['posCarts'];
};

export const addCustomer = async (body: FormData) => {
    const response = await fetch(`${urlPrefix}/customer`, {
        method: 'POST',
        body,
    });
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message || 'Failed to create customer');
    }
    return result.result as {
        customers: ICustomer[];
        newCustomer: ICustomer;
    };
};

export const updateCustomer = async (id: number, body: FormData) => {
    const response = await fetch(`${urlPrefix}/customer/${id}`, {
        method: 'PUT',
        body,
    });
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message || 'Failed to update customer');
    }
    return result.result as {
        customers: ICustomer[];
        customer: ICustomer;
    };
};

export const getCustomerFilter = async () => {
    const response = await fetch(`${urlPrefix}/customers`);
    return (await response.json()).result as ICustomer[];
};

export const getStates = async (countryId: number) => {
    const response = await fetch(`${urlPrefix}/get-states/${countryId}`);
    return (await response.json()).result as IState[];
};

export const getCities = async (stateId: number) => {
    const response = await fetch(`${urlPrefix}/get-cities/${stateId}`);
    return (await response.json()).result as ICity[];
};

export const getAreas = async (cityId: number) => {
    const response = await fetch(`${urlPrefix}/get-areas/${cityId}`);
    return (await response.json()).result as IArea[];
};

export const holdOrder = async (posCartGroupId: number) => {
    const response = await fetch(`${urlPrefix}/cart/hold`, {
        method: 'POST',
        body: objectToFormData({ posCartGroupId }),
    });
    return (await response.json()).result as IPosCartGroup;
};

export const getHoldOrders = async () => {
    const response = await fetch(`${urlPrefix}/cart/hold`);
    return (await response.json()).result as IHoldCartGroup[];
};

export const addAddress = async (body: FormData) => {
    const response = await fetch(`${urlPrefix}/address`, {
        method: 'POST',
        body,
    });
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message || 'Failed to create address');
    }
    return result.result as {
        addresses: IAddress[];
    };
};

export const updateAddress = async (id: number, body: FormData) => {
    const response = await fetch(`${urlPrefix}/address/${id}`, {
        method: 'PUT',
        body,
    });
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message || 'Failed to update address');
    }
    return result.result as {
        address: IAddress;
    };
};

export const deleteAddress = async (id: number) => {
    const response = await fetch(`${urlPrefix}/address/${id}`, {
        method: 'DELETE',
    });
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message || 'Failed to delete address');
    }
    return result.result as {
        addresses: IAddress[];
    };
};

export const getAddresses = async (customerId: number) => {
    const response = await fetch(
        `${urlPrefix}/customers/address/${customerId}`,
    );
    return (await response.json()).result.addresses as IAddress[];
};

export const deleteHoldCart = async (posCartGroupId: number) => {
    const response = await fetch(
        `${urlPrefix}/cart/hold/delete/${posCartGroupId}`,
        {
            method: 'POST',
            body: objectToFormData({ posCartGroupId }),
        },
    );
    return (await response.json()).result;
};

export const submitOrder = async (body: FormData) => {
    const response = await fetch(`${urlPrefix}/cart/submit-order`, {
        method: 'POST',
        body,
    });
    const result = await response.json();
    if (!result.success) {
        throw new Error(result.message || 'Failed to submit order');
    }
    return result.result;
};

export const getRecentOrders = async () => {
    const response = await fetch(`${urlPrefix}/orders/recent`);
    return (await response.json()).result;
};

export const getOrderInvoice = async (orderId: number) => {
    const response = await fetch(`${urlPrefix}/orders/${orderId}/invoice`);
    return (await response.json()).result;
};

export const transferAbandonedCartToPos = async (body: {
    customer_id: string | number;
    customer_type: string;
    items: Array<{ variation_id: number; quantity: number }>;
}) => {
    const response = await fetch(`${urlPrefix}/cart/from-abandoned`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(body),
    });

    const result = await response.json();
    
    if (!result.success) {
        throw new Error(result.message || 'Failed to transfer cart to POS');
    }
    
    return result.result as {
        posCartGroupId: number;
        posCarts: IPosCartGroup['posCarts'];
        added_items_count: number;
        skipped_items_count: number;
        skipped_items: Array<{ variation_id: number; reason: string }>;
    };
};
