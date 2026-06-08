import { ICategoryShort } from '../../../../../../types';

export interface IMainBannerItem {
    foregroundImage: string;
    showForegroundImage: '1' | '0';
    useBackgroundVideo: '1' | '0';
    isVideo: boolean;
    contentAlignment?: 'left' | 'center' | 'right';
    backgroundImage: string;
    backgroundVideoLink: string;
    backgroundVideoAudio: '1' | '0';
    link: string;
    badgeText: string;
    title: string;
    titleColor: string;
    subTitle: string;
    subTitleColor: string;
}

export interface IMainBannerCarousel {
    showThemeOneMainBanner: '1' | '0';
    showThemeOneSecondaryBanner: '1' | '0';
    banners: IMainBannerItem[];
    secondaryBanner: IMainBannerItem;
    settings: {
        autoplay: boolean;
        delay: number;
        contentAlignment: 'left' | 'center' | 'right';
    };
}

export interface IGetFourImageBanners {
    showThemeOneFourImageBanner: 0 | '1';
    themeOneFourImageBannerImageOne: string;
    themeOneFourImageBannerImageOneLink: string;
    themeOneFourImageBannerImageTwo: string;
    themeOneFourImageBannerImageTwoLink: string;
    themeOneFourImageBannerImageThree: string;
    themeOneFourImageBannerImageThreeLink: string;
    themeOneFourImageBannerImageFour: string;
    themeOneFourImageBannerImageFourLink: string;
}

export interface IGetFeaturedCategories {
    categories: ICategoryShort[];
    showFeaturedCategories: 0 | '1';
}

export interface IGetSingleImageBanner {
    showThemeOneSingleImageBanner: 0 | '1';
    themeOneSingleImageBannerForegroundImage: string;
    themeOneSingleImageBannerBackgroundImage: string;
    themeOneSingleImageBannerLink: string;
    themeOneSingleImageBannerBadgeText: string;
    themeOneSingleImageBannerBadgeTextColor: string;
    themeOneSingleImageBannerBadgeBackgroundColor: string;
    themeOneSingleImageBannerHeadline: string;
    themeOneSingleImageBannerHeadlineColor: string;
    themeOneSingleImageBannerTitle: string;
    themeOneSingleImageBannerTitleColor: string;
    themeOneSingleImageBannerSubTitle: string;
    themeOneSingleImageBannerSubTitleColor: string;
    themeOneSingleImageBannerButtonTextColor: string;
    themeOneSingleImageBannerButtonBackgroundColor: string;
}
