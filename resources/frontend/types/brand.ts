export interface ICollection {
    id: number;
    name: string;
    slug: string;
    image: string | null;
    sortOrder?: number;
    productsCount?: number;
}

export interface IBrandShort {
    id: number;
    name: string;
    slug: string;
    thumbnailImage: string;
    bannerImage?: string;
    metaImage?: string;
    productsCount?: number;
    collections?: ICollection[];
}
