import { ReactNode } from 'react';
import { ICountry } from './checkout';

declare global {
    interface Window {
        config: {
            defaultTheme: 'theme-one' | 'theme-two' | 'theme-three';
            defaultCategoryCard:
                | 'category-card-one'
                | 'category-card-two'
                | 'category-card-three';
            defaultProductCard:
                | 'product-card-one'
                | 'product-card-two'
                | 'product-card-three';
            defaultHeader: 'header-one' | 'header-two' | 'header-three';
            cartVisibilityToggle: string;
            storefrontLayout?: {
                desktopSideSpacing: number;
            };
            topbarHotline: string;
            topbarHotlineLabel: string;
            topbarPromoText: string;
            headerTopbarConfig?: {
                enabled?: boolean;
                hotline_number?: string;
                hotline?: string;
                promo_text_icon?: string;
                promo_text?: string;
                bg_color?: string;
                text_color?: string;
            };
            headerNavbarConfig?: {
                custom_links_enabled?: boolean;
                custom_links?: {
                    name: string;
                    url: string;
                    icon_path: string;
                    icon_svg?: string;
                }[];
                action_button?: {
                    enabled?: boolean;
                    name?: string;
                    link?: string;
                };
                menu_style?: {
                    font_size?: number;
                    font_weight?: string;
                    text_color?: string;
                    active_border_color?: string;
                    active_border_width?: number;
                };
            };

            generalSettings: {
                appName: string;
                appMode: 'singleVendor' | 'multiVendor';
                rootUrl: string;
                apiUrl: string;
                demoMode: string;
                logo: string;
                env:string;
                showCouponOnCart: string;
                allowBackorder: boolean;
                customerLoginMethod: 'email' | 'phone';
                skipCart: boolean;
                productInfiniteScroll: boolean;
            };
            searchSettings?: {
                showProducts: boolean;
                showBrands: boolean;
                showShops: boolean;
                productPriceColor: string;
                productDiscountColor: string;
            };
            customerMenu?: {
                enabled: boolean;
                socialLinks: {
                    platform: string;
                    url: string;
                }[];
            };
            countries: ICountry[];

            currency: {
                code: string;
                symbol: {
                    position:
                        | 'symbol_first'
                        | 'amount_first'
                        | 'symbol_space'
                        | 'amount_space';
                    show: string;
                };
                thousandSeparator: string | null;
                numOfDecimals: number;
                decimalSeparator: string | null;
            };

            defaultLang: ILanguage;

            languages: ILanguage[];
            paymentMethods: IPaymentMethod[];
            socialLogins: {
                name: 'Google' | 'Facebook';
                url: string;
            }[];

            floatingChat: {
                call_enabled: boolean;
                whatsapp_enabled: boolean;
                whatsapp_number: string;
                secondary_whatsapp_number: string;
                messenger_enabled: boolean;
                messenger_url: string;
                tiktok_enabled: boolean;
                tiktok_url: string;
            };
        };
    }
}

export interface IPaymentMethod {
    name: string;
    value: string;
}

export interface ILanguage {
    name: string;
    code: string;
    flag: string;
    is_rtl: number;
}

export enum STORAGE_KEYS {
    AUTH_KEY = 'arm-auth-key',
    GUEST_ID_KEY = 'guestUserId',
    AUTH_VERIFICATION_EMAIL_KEY = 'arm-registered-email-key',
}

export type Variant =
    | 'primary'
    | 'secondary'
    | 'success'
    | 'warning'
    | 'danger'
    | 'info'
    | 'light'
    | 'dark'
    | 'link'
    | 'no-color';

export type Size = 'md' | 'lg';

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

export interface IResponse<T = unknown> {
    success: boolean;
    status: number;
    message: string;
    result: {
        data: T;
        meta: IPaginationMeta;
    };
}

export interface IGetsQueryParams {
    query?: string | null;
    page?: string | number | null;
    limit?: string | number | null;
}

export interface URLFilterParams extends IGetsQueryParams {
    categorySlug?: string | null;
    brandSlug?: string | null;
    shopSlug?: string | null;

    sortBy?: string | null;
    couponCode?: string | null;
    minPrice?: string | null;
    maxPrice?: string | null;
    sizeIds?: string[] | null;
    brandIds?: string[] | null;
    categoryIds?: string[] | null;
    variationValueIds?: string[] | null;
    discounted?: boolean;
    onSaleProducts?: boolean;
    preOrder?: string | null;
}

export interface ICategoryShort {
    id: number;
    name: string;
    slug: string;
    thumbnailImage: string;
    children_categories: ICategory[];
    productsCount: number;
    parent_category: ICategory | null;
}

export interface ICategory extends ICategoryShort {
    parent_id: number;
    level: number;
    sorting_order_level: number;
    icon: string;
    total_sale_count: number;
    meta_title: string;
    meta_image: string;
    meta_description: string;
    created_by: number;
    created_at: string;
    updated_at: string;
    updated_by: number;
    deleted_at: string;
    deleted_by: number;
    category_translations: ICategoryTranslation[];
}

export interface ICategoryTranslation {
    id: number;
    category_id: number;
    lang_key: string;
    name: string;
    thumbnailImage: string;
    meta_title: string;
    meta_image: string;
    meta_description: string;
    created_by: number;
    created_at: string;
    updated_at: string;
    updated_by: number;
    deleted_at: string;
    deleted_by: number;
}

export type Impressions = 'positive' | 'negative' | 'neutral';

export interface IPage {
    id: number;
    title: string;
    slug: string;
    content: string;
    metaTile: string;
    metaDescription: string;
    metaKeywords: string;
    metaImage: string;
}

export interface IBreadcrumbNavigation {
    icon?: ReactNode;
    name: string;
    link?: string;
}
