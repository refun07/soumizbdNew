export interface IChecklist {
    curlEnabled: boolean;
    envFileWritePermission: boolean;
    routesFileWritePermission: boolean;
}

export interface IDatabaseInfo {
    DB_HOST: string;
    DB_PORT: string;
    DB_DATABASE: string;
    DB_USERNAME: string;
    DB_PASSWORD: string;
}

export interface IAdminConfig {
    name: string;
    email: string;
    password: string;
    currencyCode: string;
    currencySymbol: string;
    countryId: number;
    appMode: 'singleVendor' | 'multiVendor';
    shopName: string;
    useInventory: 1 | 0;
}

export interface IAppContextStore {
    fullPermission: boolean;
    databaseSetup: boolean;
    shopSettings: boolean;
    countries: ICountry[];
}

export interface ICountry {
    id: number;
    code: string;
    name: string;
    is_active: 0 | 1;
    created_at: string;
}

// ! response type
export interface IMigrationResponse {
    countries: ICountry[];
}

export interface IAppContextType extends IAppContextStore {
    setState: (newState: Partial<IAppContextStore>) => void;
}
