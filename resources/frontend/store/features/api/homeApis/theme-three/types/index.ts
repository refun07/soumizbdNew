import { ICategoryShort } from '../../../../../../types';
import { ICampaign } from '../../../../../../types/campaign';
import { IProductShort } from '../../../../../../types/product';

export interface IBanner {
    showThemeThreeSliderSection: '1' | '0';
    image: string;
    headline: string;
    title: string;
    subtitle: string;
    url: string;
}

export interface IFeaturedCategories {
    themeThreeFeaturedCategoriesHeadline: string;
    themeThreeFeaturedCategoriesSubtitle: string;
    categories: ICategoryShort[];
    showFeaturedCategories: 0 | '1';
}

export interface IFeaturedProducts {
    products: IProductShort[];
    showthemeThreeFeaturedProducts: 0 | '1';
}

export interface IPopularCategories {
    themeThreePopularCategoriesHeadline: string;
    themeThreePopularCategoriesSubtitle: string;
    categories: ICategoryShort[];
    showPopularCategories: 0 | '1';
}

export interface ITwoImageBanner {
    showThemeThreeTwoImageBanner: 0 | '1';
    themeThreeTwoImageBannerImageOne: string;
    themeThreeTwoImageBannerImageOneTitle: string;
    themeThreeTwoImageBannerImageOneSubtitle: string;
    themeThreeTwoImageBannerImageOneLink: string;
    themeThreeTwoImageBannerImageTwo: string;
    themeThreeTwoImageBannerImageTwoTitle: string;
    themeThreeTwoImageBannerImageTwoSubtitle: string;
    themeThreeTwoImageBannerImageTwoLink: string;
}

export interface IFlashSaleProducts {
    showHomeFlashSale: 0 | '1';
    flashSaleCampaign: ICampaign;
    flashSaleProducts: IProductShort[];
}

export interface ICategoryProducts {
    showThemeThreeCategoryProducts: '1' | '0';
    category: ICategoryShort;
    products: IProductShort[];
}

export interface IBottomTwoImageBanner {
    showThemeThreeBottomTwoImageBanner: 0 | '1';
    themeThreeBottomTwoImageBannerImageOne: string;
    themeThreeBottomTwoImageBannerImageOneHeadline: string;
    themeThreeBottomTwoImageBannerImageOneTitle: string;
    themeThreeBottomTwoImageBannerImageOneSubtitle: string;
    themeThreeBottomTwoImageBannerImageOneLink: string;
    themeThreeBottomTwoImageBannerImageTwo: string;
    themeThreeBottomTwoImageBannerImageTwoHeadline: string;
    themeThreeBottomTwoImageBannerImageTwoTitle: string;
    themeThreeBottomTwoImageBannerImageTwoSubtitle: string;
    themeThreeBottomTwoImageBannerImageTwoLink: string;
}
