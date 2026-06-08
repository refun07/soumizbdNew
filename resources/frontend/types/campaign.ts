import { IShop } from './shop';

export interface ICampaign {
    id: number;
    name: string;
    slug: string;
    thumbnailImage: string;
    banner: string;
    shortDescription: string;
    shopId: number;
    shop: IShop;
    startDate: string;
    endDate: string;
}
