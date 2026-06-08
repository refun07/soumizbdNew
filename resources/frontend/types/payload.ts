export interface IAuthPayload {
    name?: string;
    email?: string | null;
    phone?: string | null;
    code?: string | null;
    password?: string | null;
    passwordConfirmation?: string | null;
    agree?: boolean;
    remember?: boolean;
}

export interface AddressPayload {
    countryId?: number;
    stateId?: number;
    cityId?: number;
    areaId?: number;
    postalCode: string;
    address: string;
    direction: string;
    type: 'home' | 'office' | 'other';
}
