import { ILanguage } from '.';
import { store } from '../store/store';
import { ICartProduct } from './product';

export type RootState = ReturnType<typeof store.getState>;

export interface IUser {
    id: number;
    name: string;
    email: string;
    phone: string;
    avatar: string;
}

export interface IDashboardInfo {
    timeline: {
        count: string;
        unit: string;
    };
    totalOrders: number;
    totalExpense: number;
    totalProducts: number;
}

export interface AuthState {
    isLoading: boolean;
    user: IUser | null;
    dashboardInfo: IDashboardInfo | null;
    guestUserId: number | null;
    carts: ICartProduct[];
    countries: any[];
    accessToken: string | null; // jwt token
    language?: ILanguage;
}
