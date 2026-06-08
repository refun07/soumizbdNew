import { ICartProduct, IProductShort } from './product';
import { IDashboardInfo, IUser } from './state';

export interface AuthResponse {
    success: boolean;
    authStatus: string;
    access_token: string;
    token_type: string;
    expires_at: string;
    user: IUser;
    dashboardInfo: IDashboardInfo;
    carts: ICartProduct[];
    countries: unknown[];
    message: string;
}

export interface IPaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface IPaginatedResponse<T> {
    data: T;
    meta: IPaginationMeta;
}

export interface IWishListResponse {
    id: number;
    userId: number;
    product: IProductShort;
}
