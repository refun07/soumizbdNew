import { ICategoryShort } from '../../../../../../types';

export interface IBanner {
    showThemeTwoMainBanners: '1' | '0';
    themeTwoBannerImageOne: string;
    themeTwoBannerOneLink: string;
    themeTwoBannerOneHeadline: string;
    themeTwoBannerOneTitle: string;
    themeTwoBannerOneSubTitle: string;
    themeTwoBannerImageTwo: string;
    themeTwoBannerTwoLink: string;
    themeTwoBannerTwoHeadline: string;
    themeTwoBannerTwoTitle: string;
    themeTwoBannerOneHeadlineColor?: string;
    themeTwoBannerOneTitleColor?: string;
    themeTwoBannerOneSubTitleColor?: string;
    themeTwoBannerTwoHeadlineColor?: string;
    themeTwoBannerTwoTitleColor?: string;
}

export interface IGetFeaturedCategories {
    categories: ICategoryShort[];
    showFeaturedCategories: 0 | '1';
}

export interface IGetThreeImageBanners {
    showThemeTwoThreeImageBanner: 0 | '1';
    themeTwoThreeBannerImageOne: string;
    themeTwoThreeBannerOneLink: string;
    themeTwoThreeBannerImageTwo: string;
    themeTwoThreeBannerTwoLink: string;
    themeTwoThreeBannerImageThree: string;
    themeTwoThreeBannerThreeLink: string;
}

export interface IGetFourImageBanners {
    showThemeTwoFourImageBanner: 0 | '1';
    themeTwoFourBannerImageOne: string;
    themeTwoFourBannerOneLink: string;
    themeTwoFourBannerImageTwo: string;
    themeTwoFourBannerTwoLink: string;
    themeTwoFourBannerImageThree: string;
    themeTwoFourBannerThreeLink: string;
    themeTwoFourBannerImageFour: string;
    themeTwoFourBannerFourLink: string;
}

export interface IGetAboutUs {
    showThemeTwoAboutUs: 0 | '1';
    themeTwoAboutUsImage: string;
    themeTwoAboutUsHeadline: string;
    themeTwoAboutUsTitle: string;
    themeTwoAboutUsSubTitle: string;
    themeTwoAboutUsSectionOneIcon: string;
    themeTwoAboutUsSectionOneTitle: string;
    themeTwoAboutUsSectionOneSubTitle: string;
    themeTwoAboutUsSectionTwoIcon: string;
    themeTwoAboutUsSectionTwoTitle: string;
    themeTwoAboutUsSectionTwoSubTitle: string;
    themeTwoAboutUsSectionThreeIcon: string;
    themeTwoAboutUsSectionThreeTitle: string;
    themeTwoAboutUsSectionThreeSubTitle: string;
    themeTwoAboutUsSectionFourIcon: string;
    themeTwoAboutUsSectionFourTitle: string;
    themeTwoAboutUsSectionFourSubTitle: string;
}
