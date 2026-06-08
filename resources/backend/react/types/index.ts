declare global {
    interface Window {
        userId: string;
    }
}

export interface IPaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    per_page: number;
    to: number;
    total: number;
}

export interface IPaginatedResponse<T> {
    data: T;
    meta: IPaginationMeta;
}

export type Variant =
    | 'primary'
    | 'secondary'
    | 'success'
    | 'warning'
    | 'yellow'
    | 'danger'
    | 'info'
    | 'light'
    | 'dark'
    | 'link'
    | 'no-color';

export type Size = 'sm' | 'md' | 'lg';

// export interface IGoal {
//     monthlyGoalAmount: number;
// }
export interface IGoal {
    title: string;
    text: string;
    goalAmount: number;
    soldAmount: number;
    amountPercentage: number;
    startDate: string;
    endDate: string;
}

export interface IAppContextStore {
    popup: {
        name: 'goal-form' | 'product-list' | 'fraud-check' | null;
        props?: any;
    };
    goal: IGoal | undefined;
}

export interface IAppContextType extends IAppContextStore {
    setPopup: (popup: IAppContextStore['popup']) => void;
    closePopup: () => void;

    updateGoal: (amount: number) => void;
}

export interface IConversation {
    id: number;
    shopId: number;
    userId: number;
    shopName: string;
    shopSlug: string;
    shopLogo: string;
    userName: string;
    userAvatar: string;
    lastMessageAt: string;
    unseenMessageCounter: number;
}

export interface IMessage {
    id: number;
    conversationId: number;
    message: string;
    isSeenByReceiver: number;
    userId: number;
    userName: string;
    userAvatar: string;
    messageAt: string;
}

export type DeliveryPartner = 'pathao' | 'steadfast' | 'redx';

export interface CourierOrderResponse {
  message: any;
  status: string;
  partner: DeliveryPartner;
  data: any;
}

export interface PathaoCity {
    city_id: number;
    city_name:string;

}
export interface PatahoZone {
    zone_id:number;
    zone_name:string;
}
export interface PathaoArea{
    area_id: number;
    area_name: string;
    home_delivery_available: boolean;
    pickup_available: boolean;
}

export interface RedxArea{
    id: number;
    name: string;
    post_code: number;
    division_name: string;
    zone_id: number
}
