import { IPaginationMeta } from './response';

export interface IBlogShort {
    id: number;
    title: string;
    userName: string;
    slug: string;
    thumbnailImage: string;
    blogCategoryId: number;
    blogCategoryName: string;
    isPublished: number;
    createdAt: string;
}

export interface IBlog extends IBlogShort {
    shortDescription: string | null;
    description: string;
    banner: string;
    metaTitle: string;
    metaKeywords: string;
    metaDescription: string;
    metaImage: string;
    metaImageUrl: string;
}

export interface IBlogCategory {
    id: number;
    name: string;
    createdAt: string;
}

export interface IBlogsResponse {
    blogs: {
        data: IBlogShort[];

        meta: IPaginationMeta;
    };
    blogCategories: IBlogCategory[];
}

export interface ISingleBlogResponse {
    blog: IBlog;
    blogCategories: IBlogCategory[];
    relatedBlogs: IBlogShort[];
}
