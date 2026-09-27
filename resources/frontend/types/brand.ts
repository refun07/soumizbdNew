export interface IBrandGroup {
    id: number;
    name: string;
    slug: string;
    image: string | null;
    sortOrder?: number;
    brandsCount?: number;
}

export interface IBrandShort {
    id: number;
    name: string;
    slug: string;
    thumbnailImage: string;
    bannerImage?: string;
    metaImage?: string;
    productsCount?: number;
    brandGroup?: IBrandGroup | null;
}
