import { ICategoryShort } from '../../../../types';
import { IBrandShort } from '../../../../types/brand';
import { ICampaign } from '../../../../types/campaign';
import { IProductShort } from '../../../../types/product';
import { IShop } from '../../../../types/shop';

export interface IGetTopProducts {
    products: IProductShort[];
    showThemeOneTopProducts: 0 | '1';
}

export interface IGetFlashSaleProducts {
    showHomeFlashSale: 0 | '1';
    flashSaleCampaign: ICampaign | null;
    flashSaleProducts: IProductShort[];
}

export interface IGetProductSectionOne {
    products: IProductShort[];
    showHomeProductSectionOne: 0 | '1';
    homeProductSectionOneTitle: string;
}

export interface IGetProductSectionTwo {
    products: IProductShort[];
    showHomeProductSectionTwo: 0 | '1';
    homeProductSectionTwoTitle: string;
}

export interface IGetFeaturedShops {
    shops: IShop[];
    showFeaturedShops: 0 | '1';
}

export interface IGetBannerRow {
    image: string;
    url: string;
}

export interface IGetFeaturedBrands {
    brands: IBrandShort[];
    showFeaturedBrands: 0 | '1';
}

export interface IGetCategoryProducts {
    showThemeOneCategoryProducts: '1' | '0';
    showThemeTwoCategoryProducts: '1' | '0';
    category: ICategoryShort;
    products: IProductShort[];
    image: string;
    url: string;
}

export interface IGetBottomImageBanners {
    showThemeOneBottomFourImageBanner: 0 | '1';
    themeOneBottomFourImageBannerTitle: string;
    themeOneBottomFourImageBannerSubTitle: string;
    themeOneBottomFourImageBannerImageOne: string;
    themeOneBottomFourImageBannerImageOneBG: string;
    themeOneBottomFourImageBannerImageOneSubTitle: string;
    themeOneBottomFourImageBannerImageOneTitle: string;
    themeOneBottomFourImageBannerImageOneDescription: string;
    themeOneBottomFourImageBannerImageOneLink: string;
    themeOneBottomFourImageBannerImageTwo: string;
    themeOneBottomFourImageBannerImageTwoBG: string;
    themeOneBottomFourImageBannerImageTwoSubTitle: string;
    themeOneBottomFourImageBannerImageTwoTitle: string;
    themeOneBottomFourImageBannerImageTwoDescription: string;
    themeOneBottomFourImageBannerImageTwoLink: string;
    themeOneBottomFourImageBannerImageThree: string;
    themeOneBottomFourImageBannerImageThreeBG: string;
    themeOneBottomFourImageBannerImageThreeSubTitle: string;
    themeOneBottomFourImageBannerImageThreeTitle: string;
    themeOneBottomFourImageBannerImageThreeDescription: string;
    themeOneBottomFourImageBannerImageThreeLink: string;
    themeOneBottomFourImageBannerImageFour: string;
    themeOneBottomFourImageBannerImageFourBG: string;
    themeOneBottomFourImageBannerImageFourSubTitle: string;
    themeOneBottomFourImageBannerImageFourTitle: string;
    themeOneBottomFourImageBannerImageFourDescription: string;
    themeOneBottomFourImageBannerImageFourLink: string;
}

export interface IGetAboutUs {
    showHomeAboutUsSection: 0 | '1';
    homeAboutUsTitle: string;
    homeAboutUsSubTitle: string;
    homeAboutUsText: string;
    homeAboutUsImage: string;
}

export interface IGetMainCategories {
    categories: ICategoryShort[];
    showHomeMainCategories: 0 | '1';
}

export interface QuickLinkPage {
    title: string;
    url: string;
}

export interface TopCategory {
    id: number;
    name: string;
    slug: string;
    thumbnailImage: string;
    parent_category: number | null;
}

export interface DefaultPage {
    id: number;
    title: string;
    slug: string;
}

export interface FooterInfoItem {
    title: string;
    description: string;
}

export interface IClientReview {
    avatar: string;
    name: string;
    designation: string;
    text: string;
    rating: string;
}

export interface IGetFooter {
    defaultFooter: string;
    homeFooterDescription: string;
    homeFooterLogo: string;
    homeFooterLogoSize?: 'small' | 'medium' | 'large';
    homeFooterCompanyInfoAlignment?: 'left' | 'center' | 'right' | 'justify';
    homeFooterCustomerSupport: string;
    homeFooterCustomerSupportSecondary: string;
    homeFooterAddress: string;
    homeFooterEmail: string;
    showHomeFooterInfoBar: string;
    homeFooterInfoItems: FooterInfoItem[];
    homeFooterInfoBgColor: string;
    homeFooterInfoIconColor: string;
    homeFooterInfoTitleColor: string;
    homeFooterInfoTextColor: string;
    showHomeFacebookLink: string;
    homeFooterFacebookLink: string;
    showHomeTwitterLink: string;
    homeFooterTwitterLink: string;
    showHomeInstagramLink: string;
    homeFooterInstagramLink: string;
    showHomeYoutubeLink: string;
    homeFooterYoutubeLink: string;
    showHomeLinkedInLink: string;
    homeFooterLinkedInLink: string;
    showHomeTikTokLink: string;
    homeFooterTikTokLink: string;
    showHomePinterestLink: string;
    homeFooterPinterestLink: string;
    quickLinkPages: QuickLinkPage[];
    topCategories: TopCategory[];
    popularTags: string[];
    defaultPages: DefaultPage[];
    showHomeFooterPlayStoreLink: string;
    homeFooterPlayStoreLink: string;
    showHomeFooterAppStoreLink: string;
    homeFooterAppStoreLink: string;
    homeFooterCopyrightText: string;
    homeFooterSecuredPayments: string;
    footer_bg_color: string;
    footer_text_color: string;
    copyright_bg_color: string;
    copyright_text_color: string;
    showHomeFooterGoogleMaps: string;
    homeFooterGoogleMapsEnabled: string;
    homeFooterGoogleMapsType: string;
    homeFooterGoogleMapsValue: string;
}

export interface IProductSectionsEnabled {
    showThemeOneProductSections: 0 | '1';
}

export interface IGetHomepagePopup {
    showHomepagePopup: 0 | '1';
    homepagePopupImage: string;
    homepagePopupLink: string;
    homepagePopupFullSize: 0 | '1';
    homepagePopupAutoHideEnabled: 0 | '1';
    homepagePopupDuration: string;
}
