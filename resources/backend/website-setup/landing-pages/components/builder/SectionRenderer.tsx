import React, { useState } from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Navigation, Pagination, Autoplay } from 'swiper/modules';
import { LandingPageSection, PageConfigs } from '../../hooks/useBuilderState';
import BackgroundRenderer from './BackgroundRenderer';

import 'swiper/css/bundle';

interface SectionRendererProps {
    section: LandingPageSection;
    pageConfigs: PageConfigs;
    isPreview?: boolean;
    deviceType?: 'mobile' | 'tablet' | 'desktop';
}

const getYoutubeEmbedUrl = (url: string | undefined): string | null => {
    if (!url) return null;
    let videoId: string | null = null;
    
    const watchMatch = url.match(/youtube\.com\/watch\?v=([^&]+)/);
    if (watchMatch) videoId = watchMatch[1];
    
    const shortMatch = url.match(/youtu\.be\/([^?]+)/);
    if (shortMatch) videoId = shortMatch[1];
    
    const embedMatch = url.match(/youtube\.com\/embed\/([^?]+)/);
    if (embedMatch) videoId = embedMatch[1];
    
    return videoId ? `https://www.youtube.com/embed/${videoId}` : null;
};

const getContrastColor = (hexColor: string | null | undefined): string => {
    if (!hexColor) return '#ffffff';
    const hex = hexColor.replace('#', '');
    if (hex.length !== 6) return '#ffffff';
    const r = parseInt(hex.slice(0, 2), 16);
    const g = parseInt(hex.slice(2, 4), 16);
    const b = parseInt(hex.slice(4, 6), 16);
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.5 ? '#1a1a1a' : '#ffffff';
};

const getSecondaryColor = (hexColor: string | null | undefined): string => {
    if (!hexColor) return 'rgba(255,255,255,0.7)';
    const hex = hexColor.replace('#', '');
    if (hex.length !== 6) return 'rgba(255,255,255,0.7)';
    const r = parseInt(hex.slice(0, 2), 16);
    const g = parseInt(hex.slice(2, 4), 16);
    const b = parseInt(hex.slice(4, 6), 16);
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.5 ? '#4a4a4a' : 'rgba(255,255,255,0.7)';
};

const lightenColor = (color: string, percent: number): string => {
    const num = parseInt(color.replace('#', ''), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) + amt;
    const G = (num >> 8 & 0x00FF) + amt;
    const B = (num & 0x0000FF) + amt;
    return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 + (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 + (B < 255 ? B < 1 ? 0 : B : 255)).toString(16).slice(1);
};

const darkenColor = (color: string, percent: number): string => {
    const num = parseInt(color.replace('#', ''), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) - amt;
    const G = (num >> 8 & 0x00FF) - amt;
    const B = (num & 0x0000FF) - amt;
    return '#' + (0x1000000 + (R > 0 ? R : 0) * 0x10000 + (G > 0 ? G : 0) * 0x100 + (B > 0 ? B : 0)).toString(16).slice(1);
};

const SectionRenderer: React.FC<SectionRendererProps> = ({
    section,
    pageConfigs,
    isPreview = false,
    deviceType = 'desktop'
}) => {
    const { type, section_data } = section;

    const effectiveBackgroundColor = section_data.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    const themeLight = lightenColor(effectiveBackgroundColor, 30);
    const themeDark = darkenColor(effectiveBackgroundColor, 15);

    const renderSection = () => {
        switch (type) {
            case 'centered_product_offer_hero': {
                const productOfferEmbedUrl = getYoutubeEmbedUrl(section_data.product_video_url);
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionMinHeight = isMobile ? 'min-h-[400px]' : isTablet ? 'min-h-[500px]' : 'min-h-screen';
                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const logoHeight = isMobile ? 'h-8' : isTablet ? 'h-10' : 'h-12';
                const logoMargin = isMobile ? 'mb-4' : isTablet ? 'mb-5' : 'mb-6';
                const titleSize = isMobile ? 'text-3xl' : isTablet ? 'text-4xl' : 'text-5xl';
                const titleMargin = isMobile ? 'mb-2' : isTablet ? 'mb-3' : 'mb-4';
                const subtitleSize = isMobile ? 'text-base' : isTablet ? 'text-lg' : 'text-lg';
                const subtitleMargin = isMobile ? 'mb-4' : isTablet ? 'mb-5' : 'mb-6';
                const imageMargin = isMobile ? 'mb-4' : isTablet ? 'mb-5' : 'mb-6';
                const imageRounded = isMobile ? 'rounded-xl' : 'rounded-2xl';
                const badgePadding = isMobile ? 'px-4 py-2' : isTablet ? 'px-5 py-2' : 'px-6 py-2';
                const badgeTextSize = isMobile ? 'text-xl' : isTablet ? 'text-2xl' : 'text-3xl';
                const badgeMargin = isMobile ? 'mb-4' : isTablet ? 'mb-5' : 'mb-6';
                const buttonPadding = isMobile ? 'py-3 px-4' : isTablet ? 'py-3 px-5' : 'py-3 px-6';
                const buttonTextSize = isMobile ? 'text-xs' : isTablet ? 'text-sm' : 'text-base';
                const buttonWidth = isMobile ? 'w-full' : 'w-auto';

                const titleColor = section_data.product_title_color || getContrastColor(effectiveBackgroundColor);
                const subtitleColor = section_data.product_subtitle_color || getSecondaryColor(effectiveBackgroundColor);
                const badgeTextColor = section_data.product_offer_badge_text_color || '#ffffff';
                const oldPriceColor = section_data.product_old_price_color || 'rgba(255,255,255,0.7)';
                const newPriceColor = section_data.product_new_price_color || '#ffffff';
                const buttonBgColor = section_data.product_button_bg_color || primaryColor;
                const buttonTextColor = section_data.product_button_text_color || getContrastColor(buttonBgColor);

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionMinHeight} flex flex-col items-center justify-center ${sectionPadding}`}
                    >
                        <div className={`w-full max-w-7xl mx-auto ${containerPadding} flex flex-col items-center`}>
                            {section_data.brand_logo_url && (
                                <div className={logoMargin}>
                                    <img
                                        src={section_data.brand_logo_url}
                                        alt="Brand Logo"
                                        className={`${logoHeight} w-auto object-contain`}
                                    />
                                </div>
                            )}
                            <h1 className={`${titleSize} font-bold text-center ${titleMargin}`} style={{ color: titleColor }}>
                                {section_data.product_title || 'Product Title'}
                            </h1>
                            <p className={`${subtitleSize} text-center ${subtitleMargin} max-w-md mx-auto`} style={{ color: subtitleColor }}>
                                {section_data.product_subtitle || 'Product subtitle'}
                            </p>

                            {section_data.product_image_url ? (
                                <div className={`w-full ${imageMargin}`}>
                                    <div className={`aspect-video ${imageRounded} overflow-hidden`} style={{ backgroundColor: 'var(--landing-theme-light, #f0f0f0)' }}>
                                        <img
                                            src={section_data.product_image_url}
                                            alt="Product"
                                            className="w-full h-full object-cover"
                                        />
                                    </div>
                                </div>
                            ) : productOfferEmbedUrl ? (
                                <div className={`w-full ${imageMargin}`}>
                                    <div className={`aspect-video ${imageRounded} overflow-hidden`} style={{ backgroundColor: 'var(--landing-theme-light, #f0f0f0)' }}>
                                        <iframe
                                            src={productOfferEmbedUrl}
                                            className="w-full h-full"
                                            frameBorder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowFullScreen
                                        />
                                    </div>
                                </div>
                            ) : (
                                <div className={`w-full aspect-square ${imageRounded} ${imageMargin} flex items-center justify-center`} style={{ backgroundColor: 'var(--landing-theme-light, #f0f0f0)' }}>
                                    <span className="text-sm" style={{ color: subtitleColor }}>Product Image or Video</span>
                                </div>
                            )}

                            {(section_data.product_offer_badge_text || section_data.product_new_price) && (
                                <div className={badgeMargin}>
                                    <span
                                        className={`inline-flex items-center gap-2 ${badgePadding} ${badgeTextSize} font-semibold rounded-md`}
                                        style={{ backgroundColor: primaryColor }}
                                    >
                                        {section_data.product_offer_badge_text && <span style={{ color: badgeTextColor }}>{section_data.product_offer_badge_text}</span>}
                                        {section_data.product_old_price && <span className="line-through opacity-70" style={{ color: oldPriceColor }}>{section_data.product_old_price}</span>}
                                        {section_data.product_new_price && <span style={{ color: newPriceColor }}>{section_data.product_new_price}</span>}
                                    </span>
                                </div>
                            )}

                            {section_data.product_button_text && (
                                <button
                                    type="button"
                                    className={`${buttonPadding} ${buttonWidth} font-bold ${buttonTextSize} rounded-sm flex items-center justify-center gap-2 transition-colors duration-200 whitespace-nowrap`}
                                    style={{ backgroundColor: buttonBgColor, color: buttonTextColor }}
                                >
                                    <i className="fa-solid fa-cart-shopping"></i>
                                    {section_data.product_button_text}
                                </button>
                            )}
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'centered_video_hero': {
                const videoHeroEmbedUrl = getYoutubeEmbedUrl(section_data.product_video_url);
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionMinHeight = isMobile ? 'min-h-[400px]' : isTablet ? 'min-h-[500px]' : 'min-h-screen';
                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const videoMargin = isMobile ? 'mb-4' : isTablet ? 'mb-6' : 'mb-8';
                const videoRounded = isMobile ? 'rounded-xl' : 'rounded-xl';
                const titleSize = isMobile ? 'text-3xl' : isTablet ? 'text-4xl' : 'text-4xl';
                const titleMargin = isMobile ? 'mb-2' : isTablet ? 'mb-3' : 'mb-3';
                const subtitleSize = isMobile ? 'text-base' : isTablet ? 'text-lg' : 'text-lg';
                const subtitleMargin = isMobile ? 'mb-4' : isTablet ? 'mb-6' : 'mb-8';
                const badgePadding = isMobile ? 'px-3 py-1.5' : isTablet ? 'px-5 py-2' : 'px-6 py-2';
                const badgeTextSize = isMobile ? 'text-lg' : isTablet ? 'text-2xl' : 'text-3xl';
                const badgeMargin = isMobile ? 'mb-4' : isTablet ? 'mb-5' : 'mb-6';
                const buttonPadding = isMobile ? 'py-3 px-4' : isTablet ? 'py-3 px-5' : 'py-3 px-8';
                const buttonTextSize = isMobile ? 'text-xs' : isTablet ? 'text-sm' : 'text-xs';
                const buttonWidth = isMobile ? 'w-full' : 'w-auto';

                const titleColor = section_data.product_title_color || getContrastColor(effectiveBackgroundColor);
                const subtitleColor = section_data.product_subtitle_color || getSecondaryColor(effectiveBackgroundColor);
                const badgeTextColor = section_data.product_offer_badge_text_color || '#ffffff';
                const oldPriceColor = section_data.product_old_price_color || 'rgba(255,255,255,0.7)';
                const newPriceColor = section_data.product_new_price_color || '#ffffff';
                const buttonBgColor = section_data.product_button_bg_color || primaryColor;
                const buttonTextColor = section_data.product_button_text_color || getContrastColor(buttonBgColor);

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionMinHeight} flex flex-col items-center justify-center ${sectionPadding}`}
                    >
                        <div className={`w-full max-w-7xl mx-auto ${containerPadding} flex flex-col items-center`}>
                            {videoHeroEmbedUrl ? (
                                <div className={`w-full ${videoMargin}`}>
                                    <div className={`aspect-video ${videoRounded} overflow-hidden`} style={{ backgroundColor: 'var(--landing-theme-light, #f0f0f0)' }}>
                                        <iframe
                                            src={videoHeroEmbedUrl}
                                            className="w-full h-full"
                                            frameBorder="0"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowFullScreen
                                        />
                                    </div>
                                </div>
                            ) : (
                                <div className={`w-full aspect-video ${videoRounded} ${videoMargin} flex items-center justify-center`} style={{ backgroundColor: 'var(--landing-theme-light, #f0f0f0)' }}>
                                    <span className="text-sm md:text-lg" style={{ color: subtitleColor }}>Enter YouTube Video URL</span>
                                </div>
                            )}

                            <h1 className={`${titleSize} font-bold text-center ${titleMargin}`} style={{ color: titleColor }}>
                                {section_data.product_title || 'Product Title'}
                            </h1>

                            <p className={`text-center ${subtitleSize} ${subtitleMargin} max-w-2xl`} style={{ color: subtitleColor }}>
                                {section_data.product_subtitle || 'Product subtitle'}
                            </p>

                            {(section_data.product_offer_badge_text || section_data.product_new_price) && (
                                <div className={badgeMargin}>
                                    <span
                                        className={`inline-flex items-center gap-2 ${badgePadding} ${badgeTextSize} font-semibold rounded-md`}
                                        style={{ backgroundColor: buttonBgColor }}
                                    >
                                        {section_data.product_offer_badge_text && <span style={{ color: badgeTextColor }}>{section_data.product_offer_badge_text}</span>}
                                        {section_data.product_old_price && <span className="line-through opacity-70" style={{ color: oldPriceColor }}>{section_data.product_old_price}</span>}
                                        {section_data.product_new_price && <span style={{ color: newPriceColor }}>{section_data.product_new_price}</span>}
                                    </span>
                                </div>
                            )}

                            {section_data.product_button_text && (
                                <button
                                    type="button"
                                    className={`${buttonPadding} ${buttonWidth} font-bold ${buttonTextSize} rounded-sm flex items-center justify-center gap-2 transition-colors duration-200 whitespace-nowrap`}
                                    style={{ backgroundColor: buttonBgColor, color: buttonTextColor }}
                                >
                                    <i className="fa-solid fa-cart-shopping"></i>
                                    {section_data.product_button_text}
                                </button>
                            )}
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'centered_offer_hero': {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionMinHeight = isMobile ? 'min-h-[350px]' : isTablet ? 'min-h-[400px]' : 'min-h-[400px]';
                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const badgePadding = isMobile ? 'px-3 py-1' : 'px-4 py-1';
                const badgeTextSize = isMobile ? 'text-xs' : 'text-sm';
                const badgeMargin = isMobile ? 'mb-3' : 'mb-4';
                const titleSize = isMobile ? 'text-3xl' : isTablet ? 'text-4xl' : 'text-4xl';
                const titleMargin = isMobile ? 'mb-2' : 'mb-2';
                const subtitleSize = isMobile ? 'text-sm' : 'text-base';
                const subtitleMargin = isMobile ? 'mb-4' : 'mb-6';
                const oldPriceSize = isMobile ? 'text-xl' : 'text-2xl';
                const newPriceSize = isMobile ? 'text-3xl' : 'text-4xl';
                const priceMargin = isMobile ? 'mb-4' : 'mb-6';
                const buttonPadding = isMobile ? 'py-2.5 px-6' : 'py-3 px-8';
                const buttonTextSize = isMobile ? 'text-sm' : 'text-base';
                const buttonWidth = isMobile ? 'w-full' : 'w-auto';

                const titleColor = section_data.offer_title_color || getContrastColor(effectiveBackgroundColor);
                const subtitleColor = section_data.offer_subtitle_color || getSecondaryColor(effectiveBackgroundColor);
                const badgeTextColor = section_data.offer_badge_text_color || '#ffffff';
                const oldPriceColor = section_data.old_price_color || 'rgba(255,255,255,0.7)';
                const newPriceColor = section_data.new_price_color || primaryColor;
                const buttonBgColor = section_data.centered_offer_button_bg_color || primaryColor;
                const buttonTextColor = section_data.centered_offer_button_text_color || getContrastColor(buttonBgColor);

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionMinHeight} flex flex-col items-center justify-center ${sectionPadding}`}
                    >
                        <div className={`w-full max-w-2xl mx-auto ${containerPadding} flex flex-col items-center text-center`}>
                            {section_data.offer_badge_text && (
                                <span
                                    className={`inline-block ${badgePadding} ${badgeTextSize} font-semibold rounded-full ${badgeMargin}`}
                                    style={{ backgroundColor: buttonBgColor, color: badgeTextColor }}
                                >
                                    {section_data.offer_badge_text}
                                </span>
                            )}

                            <h1 className={`${titleSize} font-bold ${titleMargin}`} style={{ color: titleColor }}>
                                {section_data.offer_title || 'Offer Title'}
                            </h1>
                            <p className={`${subtitleSize} ${subtitleMargin} max-w-md`} style={{ color: subtitleColor }}>
                                {section_data.offer_subtitle || 'Offer subtitle'}
                            </p>

                            {(section_data.old_price || section_data.new_price) && (
                                <div className={`${priceMargin} flex items-center gap-3 md:gap-4`}>
                                    {section_data.old_price && (
                                        <span className={`${oldPriceSize} line-through`} style={{ color: oldPriceColor }}>
                                            {section_data.old_price}Tk
                                        </span>
                                    )}
                                    {section_data.new_price && (
                                        <span
                                            className={`${newPriceSize} font-bold`}
                                            style={{ color: newPriceColor }}
                                        >
                                            {section_data.new_price}Tk
                                        </span>
                                    )}
                                </div>
                            )}

                            {section_data.button_text && (
                                <button
                                    type="button"
                                    className={`${buttonPadding} ${buttonWidth} font-bold ${buttonTextSize} rounded-sm transition-colors duration-200`}
                                    style={{
                                        backgroundColor: buttonBgColor,
                                        color: buttonTextColor
                                    }}
                                >
                                    {section_data.button_text}
                                </button>
                            )}
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'quality_products': {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const gridCols = isMobile || isTablet ? 'grid-cols-1' : 'lg:grid-cols-2';
                const gridGap = isMobile ? 'gap-6' : isTablet ? 'gap-8' : 'gap-12';
                const titleSize = isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-3xl';
                const contentSpacing = isMobile ? 'space-y-4' : 'space-y-6';
                const itemSpacing = isMobile ? 'space-y-3' : 'space-y-4';
                const iconSize = isMobile ? 'text-xl' : 'text-2xl';
                const textSize = isMobile ? 'text-sm' : 'text-base';
                const buttonPadding = isMobile ? 'py-2.5 px-6' : 'py-3 px-8';
                const buttonTextSize = isMobile ? 'text-sm' : 'text-base';
                const buttonWidth = isMobile ? 'w-full' : 'inline-block';
                const imageRounded = isMobile ? 'rounded-md' : 'rounded-lg';

                const titleColor = section_data.quality_title_color || getContrastColor(effectiveBackgroundColor);
                const featureTextColor = section_data.feature_text_color || getSecondaryColor(effectiveBackgroundColor);
                const buttonBgColor = section_data.quality_button_bg_color || primaryColor;
                const buttonTextColor = section_data.quality_button_text_color || getContrastColor(buttonBgColor);

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionPadding}`}
                    >
                        <div className={`max-w-7xl mx-auto ${containerPadding}`}>
                            <div className={`grid ${gridCols} ${gridGap} items-center`}>
                                <div>
                                    {section_data.quality_image_url && (
                                        <img
                                            src={section_data.quality_image_url}
                                            alt="Quality"
                                            className={`w-full h-auto ${imageRounded} shadow-lg`}
                                        />
                                    )}
                                </div>

                                <div className={contentSpacing}>
                                    <h2 className={`${titleSize} font-bold`} style={{ color: titleColor }}>
                                        {section_data.quality_title || 'Quality Products'}
                                    </h2>

                                    <div className={itemSpacing}>
                                        {[
                                            { icon: section_data.item1_icon, text: section_data.item1_text },
                                            { icon: section_data.item2_icon, text: section_data.item2_text },
                                            { icon: section_data.item3_icon, text: section_data.item3_text },
                                            { icon: section_data.item4_icon, text: section_data.item4_text },
                                        ].map((item, idx) => (
                                            item.text && (
                                                <div key={idx} className="flex items-center gap-3">
                                                    {item.icon && <span className={iconSize} style={{ color: buttonBgColor }} dangerouslySetInnerHTML={{ __html: item.icon }} />}
                                                    <span className={textSize} style={{ color: featureTextColor }}>{item.text}</span>
                                                </div>
                                            )
                                        ))}
                                    </div>

                                    {section_data.button_text && (
                                        <a
                                            href={section_data.button_url || '#'}
                                            className={`${buttonWidth} ${buttonPadding} font-bold ${buttonTextSize} rounded-sm transition-colors duration-200 text-center`}
                                            style={{
                                                backgroundColor: buttonBgColor,
                                                color: buttonTextColor
                                            }}
                                        >
                                            {section_data.button_text}
                                        </a>
                                    )}
                                </div>
                            </div>
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'quality_about': {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const gridCols = isMobile || isTablet ? 'grid-cols-1' : 'lg:grid-cols-2';
                const gridGap = isMobile ? 'gap-6' : isTablet ? 'gap-8' : 'gap-12';
                const titleSize = isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-3xl';
                const contentSpacing = isMobile ? 'space-y-4' : 'space-y-6';
                const textSize = isMobile ? 'text-sm' : 'text-base';
                const buttonPadding = isMobile ? 'py-2.5 px-6' : 'py-3 px-8';
                const buttonTextSize = isMobile ? 'text-sm' : 'text-base';
                const buttonWidth = isMobile ? 'w-full' : 'inline-block';
                const imageRounded = isMobile ? 'rounded-md' : 'rounded-lg';

                const titleColor = section_data.quality_about_title_color || getContrastColor(effectiveBackgroundColor);
                const descriptionColor = section_data.quality_about_description_color || getSecondaryColor(effectiveBackgroundColor);
                const buttonBgColor = section_data.quality_about_button_bg_color || primaryColor;
                const buttonTextColor = section_data.quality_about_button_text_color || getContrastColor(buttonBgColor);

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionPadding}`}
                    >
                        <div className={`max-w-7xl mx-auto ${containerPadding}`}>
                            <div className={`grid ${gridCols} ${gridGap} items-center`}>
                                <div>
                                    {section_data.quality_about_image_url && (
                                        <img
                                            src={section_data.quality_about_image_url}
                                            alt="About"
                                            className={`w-full h-auto ${imageRounded} shadow-lg`}
                                        />
                                    )}
                                </div>

                                <div className={contentSpacing}>
                                    <h2 className={`${titleSize} font-bold`} style={{ color: titleColor }}>
                                        {section_data.quality_about_title || 'About Our Quality'}
                                    </h2>
                                    <p className={`${textSize} leading-relaxed`} style={{ color: descriptionColor }}>
                                        {section_data.quality_about_description || 'Description goes here...'}
                                    </p>

                                    {section_data.quality_about_button_text && (
                                        <a
                                            href={section_data.quality_about_button_url || '#'}
                                            className={`${buttonWidth} ${buttonPadding} font-bold ${buttonTextSize} rounded-sm transition-colors duration-200 text-center`}
                                            style={{
                                                backgroundColor: buttonBgColor,
                                                color: buttonTextColor
                                            }}
                                        >
                                            {section_data.quality_about_button_text}
                                        </a>
                                    )}
                                </div>
                            </div>
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'multiple_products': {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const gridCols = isMobile ? 'grid-cols-1' : isTablet ? 'grid-cols-2' : 'md:grid-cols-3';
                const gridGap = isMobile ? 'gap-6' : isTablet ? 'gap-6' : 'gap-8';
                const imageHeight = isMobile ? 'h-48' : isTablet ? 'h-56' : 'h-64';
                const imageRounded = isMobile ? 'rounded-md' : 'rounded-lg';
                const titleSize = isMobile ? 'text-lg' : isTablet ? 'text-xl' : 'text-xl';
                const contentSpacing = isMobile ? 'space-y-2' : 'space-y-4';
                const subtitleSize = isMobile ? 'text-sm' : 'text-base';
                const buttonPadding = isMobile ? 'py-2 px-4' : 'py-2 px-6';
                const buttonTextSize = isMobile ? 'text-sm' : 'text-base';
                const buttonWidth = isMobile ? 'w-full' : 'inline-block';

                const products = [
                    {
                        image: section_data.product1_image,
                        title: section_data.product1_title,
                        subtitle: section_data.product1_subtitle,
                        buttonText: section_data.product1_button_text,
                        buttonUrl: section_data.product1_button_url,
                        titleColor: section_data.product1_title_color || getContrastColor(effectiveBackgroundColor),
                        subtitleColor: section_data.product1_subtitle_color || getSecondaryColor(effectiveBackgroundColor),
                        buttonBgColor: section_data.product1_button_bg_color || primaryColor,
                        buttonTextColor: section_data.product1_button_text_color || getContrastColor(section_data.product1_button_bg_color || primaryColor),
                    },
                    {
                        image: section_data.product2_image,
                        title: section_data.product2_title,
                        subtitle: section_data.product2_subtitle,
                        buttonText: section_data.product2_button_text,
                        buttonUrl: section_data.product2_button_url,
                        titleColor: section_data.product2_title_color || getContrastColor(effectiveBackgroundColor),
                        subtitleColor: section_data.product2_subtitle_color || getSecondaryColor(effectiveBackgroundColor),
                        buttonBgColor: section_data.product2_button_bg_color || primaryColor,
                        buttonTextColor: section_data.product2_button_text_color || getContrastColor(section_data.product2_button_bg_color || primaryColor),
                    },
                    {
                        image: section_data.product3_image,
                        title: section_data.product3_title,
                        subtitle: section_data.product3_subtitle,
                        buttonText: section_data.product3_button_text,
                        buttonUrl: section_data.product3_button_url,
                        titleColor: section_data.product3_title_color || getContrastColor(effectiveBackgroundColor),
                        subtitleColor: section_data.product3_subtitle_color || getSecondaryColor(effectiveBackgroundColor),
                        buttonBgColor: section_data.product3_button_bg_color || primaryColor,
                        buttonTextColor: section_data.product3_button_text_color || getContrastColor(section_data.product3_button_bg_color || primaryColor),
                    },
                ];

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionPadding}`}
                    >
                        <div className={`max-w-7xl mx-auto ${containerPadding}`}>
                            <div className={`grid ${gridCols} ${gridGap}`}>
                                {products.map((product, idx) => (
                                    (product.title || product.image) && (
                                        <div key={idx} className={`text-center ${contentSpacing}`}>
                                            {product.image && (
                                                <img
                                                    src={product.image}
                                                    alt={product.title || `Product ${idx + 1}`}
                                                    className={`w-full ${imageHeight} object-cover ${imageRounded}`}
                                                />
                                            )}
                                            <h3 className={`${titleSize} font-bold`} style={{ color: product.titleColor }}>
                                                {product.title || `Product ${idx + 1}`}
                                            </h3>

                                            {product.subtitle && (
                                                <p className={subtitleSize} style={{ color: product.subtitleColor }}>{product.subtitle}</p>
                                            )}

                                            {product.buttonText && (
                                                <a
                                                    href={product.buttonUrl || '#'}
                                                    className={`${buttonWidth} ${buttonPadding} font-bold ${buttonTextSize} rounded-sm transition-colors duration-200`}
                                                    style={{
                                                        backgroundColor: product.buttonBgColor,
                                                        color: product.buttonTextColor
                                                    }}
                                                >
                                                    {product.buttonText}
                                                </a>
                                            )}
                                        </div>
                                    )
                                ))}
                            </div>
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'product_benefits': {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const innerPadding = isMobile ? 'p-6' : isTablet ? 'p-7' : 'p-8';
                const titleSize = isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-4xl';
                const titleMargin = isMobile ? 'mb-6' : 'mb-8';
                const itemsGap = isMobile ? 'gap-3' : 'gap-4';
                const itemsMargin = isMobile ? 'mb-6' : 'mb-8';
                const iconSize = isMobile ? 'w-6 h-6' : 'w-8 h-8';
                const iconPadding = isMobile ? 'p-1' : 'p-1.5';
                const textSize = isMobile ? 'text-base' : 'text-lg';
                const buttonPadding = isMobile ? 'py-3 px-6' : isTablet ? 'py-3.5 px-7' : 'py-4 px-8';
                const buttonTextSize = isMobile ? 'text-sm' : 'text-base';

                const titleColor = section_data.highlights_title_color || getContrastColor(effectiveBackgroundColor);
                const benefitTextColor = section_data.benefit_description_color || getSecondaryColor(effectiveBackgroundColor);
                const buttonBgColor = section_data.highlights_button_bg_color || primaryColor;
                const buttonTextColor = section_data.highlights_button_text_color || getContrastColor(buttonBgColor);

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionPadding}`}
                    >
                        <div className={`max-w-7xl mx-auto ${containerPadding}`}>
                            <div className={`max-w-4xl mx-auto ${innerPadding} rounded-lg`} style={{ backgroundColor: effectiveBackgroundColor }}>
                                <h2 className={`${titleSize} font-bold text-center ${titleMargin}`} style={{ color: titleColor }}>
                                    {section_data.benefits_title || 'Product Benefits'}
                                </h2>

                                <div className={`space-y-3 ${itemsGap} ${itemsMargin}`}>
                                    {section_data.benefits_items?.map((item: any, idx: number) => (
                                        <div key={idx} className="flex items-start gap-3 md:gap-4">
                                            <div className={`flex-shrink-0 ${iconSize} rounded-full flex items-center justify-center ${iconPadding}`} style={{ backgroundColor: buttonBgColor }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </div>
                                            <p className={`${textSize} leading-relaxed`} style={{ color: benefitTextColor }}>
                                                {item.text}
                                            </p>
                                        </div>
                                    ))}
                                </div>

                                {section_data.benefits_button_text && (
                                    <div className="flex justify-center">
                                        <a
                                            href={section_data.benefits_button_url || '#'}
                                            className={`inline-flex items-center gap-2 ${buttonPadding} font-bold uppercase ${buttonTextSize} transition-opacity`}
                                            style={{
                                                backgroundColor: buttonBgColor,
                                                color: buttonTextColor
                                            }}
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                <circle cx="9" cy="21" r="1"></circle>
                                                <circle cx="20" cy="21" r="1"></circle>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                            </svg>
                                            {section_data.benefits_button_text}
                                        </a>
                                    </div>
                                )}
                            </div>
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'product_highlights': {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const titleSize = isMobile ? 'text-2xl' : 'text-3xl';
                const titleMargin = isMobile ? 'mb-6' : isTablet ? 'mb-8' : 'mb-12';
                const gridCols = isMobile ? 'grid-cols-1' : isTablet ? 'grid-cols-2' : 'lg:grid-cols-3';
                const gridGap = isMobile ? 'gap-4' : isTablet ? 'gap-6' : 'gap-8';
                const iconSize = isMobile ? 'w-12 h-12' : 'w-16 h-16';
                const itemSpacing = isMobile ? 'space-y-2' : 'space-y-3';
                const headingSize = isMobile ? 'text-base' : 'text-lg';
                const descSize = isMobile ? 'text-xs' : 'text-sm';
                const buttonMargin = isMobile ? 'mt-6' : isTablet ? 'mt-8' : 'mt-12';
                const buttonPadding = isMobile ? 'py-2.5 px-6' : 'py-3 px-8';
                const buttonTextSize = isMobile ? 'text-sm' : 'text-lg';
                const buttonWidth = isMobile ? 'w-full' : 'inline-block';

                const titleColor = section_data.highlights_title_color || getContrastColor(effectiveBackgroundColor);
                const benefitTitleColor = section_data.benefit_title_color || getContrastColor(effectiveBackgroundColor);
                const benefitDescriptionColor = section_data.benefit_description_color || getSecondaryColor(effectiveBackgroundColor);
                const buttonBgColor = section_data.highlights_button_bg_color || primaryColor;
                const buttonTextColor = section_data.highlights_button_text_color || getContrastColor(buttonBgColor);

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionPadding}`}
                    >
                        <div className={`max-w-7xl mx-auto ${containerPadding}`}>
                            <h2 className={`${titleSize} font-bold text-center ${titleMargin}`} style={{ color: titleColor }}>
                                {section_data.benefits_title || 'Product Highlights'}
                            </h2>

                            <div className={`grid ${gridCols} ${gridGap}`}>
                                {section_data.benefits_items?.map((item: any, idx: number) => (
                                    <div key={idx} className={`text-center ${itemSpacing}`}>
                                        {item.icon && (
                                            <div
                                                className={`${iconSize} mx-auto rounded-full flex items-center justify-center`}
                                                style={{ backgroundColor: `${buttonBgColor}20` }}
                                                dangerouslySetInnerHTML={{ __html: item.icon }}
                                            />
                                        )}
                                        <h3 className={`${headingSize} font-bold`} style={{ color: benefitTitleColor }}>
                                            {item.title}
                                        </h3>
                                        <p className={descSize} style={{ color: benefitDescriptionColor }}>
                                            {item.description}
                                        </p>
                                    </div>
                                ))}
                            </div>

                            {section_data.button_text && (
                                <div className={`text-center ${buttonMargin}`}>
                                    <button
                                        type="button"
                                        className={`${buttonWidth} ${buttonPadding} font-bold ${buttonTextSize} rounded-sm transition-colors duration-200`}
                                        style={{
                                            backgroundColor: buttonBgColor,
                                            color: buttonTextColor
                                        }}
                                    >
                                        {section_data.button_text}
                                    </button>
                                </div>
                            )}
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'carousel_images': {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const titleSize = isMobile ? 'text-2xl' : 'text-3xl';
                const titleMargin = isMobile ? 'mb-4' : 'mb-8';
                const slideSpace = isMobile ? 8 : 16;
                const slideRounded = isMobile ? 'rounded-md' : 'rounded-lg';

                const titleColor = section_data.carousel_title_color || getContrastColor(effectiveBackgroundColor);

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionPadding}`}
                    >
                        <div className={`max-w-7xl mx-auto ${containerPadding}`}>
                            {section_data.carousel_title && (
                                <h2 className={`${titleSize} font-bold text-center ${titleMargin}`} style={{ color: titleColor }}>
                                    {section_data.carousel_title}
                                </h2>
                            )}

                            <Swiper
                                modules={[Navigation, Pagination, Autoplay]}
                                spaceBetween={slideSpace}
                                slidesPerView={isMobile ? 1 : 2}
                                breakpoints={isMobile ? {
                                    640: {
                                        slidesPerView: 2,
                                    },
                                    768: {
                                        slidesPerView: 3,
                                    },
                                    1024: {
                                        slidesPerView: 4,
                                    },
                                } : {
                                    640: {
                                        slidesPerView: 2,
                                    },
                                    768: {
                                        slidesPerView: 3,
                                    },
                                    1024: {
                                        slidesPerView: 4,
                                    },
                                }}
                                navigation={!isMobile}
                                pagination={{ clickable: true }}
                                autoplay={{ delay: 3000, disableOnInteraction: false }}
                                loop={section_data.carousel_image_urls?.length > 1}
                                className="carousel-images-swiper"
                            >
                                {section_data.carousel_image_urls?.map((url: string, idx: number) => (
                                    url && (
                                        <SwiperSlide key={idx}>
                                            <div className={`aspect-video ${slideRounded} overflow-hidden`}>
                                                <img
                                                    src={url}
                                                    alt={`Carousel ${idx + 1}`}
                                                    className="w-full h-full object-cover"
                                                />
                                            </div>
                                        </SwiperSlide>
                                    )
                                ))}
                            </Swiper>
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'faq_section': {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';
                const [openIndex, setOpenIndex] = useState<number | null>(0);

                const sectionPadding = isMobile ? 'py-12' : isTablet ? 'py-16' : 'py-20';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const titleSize = isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-4xl';
                const titleMargin = isMobile ? 'mb-8' : 'mb-12';
                const faqSpacing = isMobile ? 'space-y-3' : 'space-y-4';
                const buttonPadding = isMobile ? 'py-3 px-6' : isTablet ? 'py-3 px-8' : 'py-4 px-8';
                const buttonTextSize = isMobile ? 'text-sm' : 'text-base';
                const badgeSize = isMobile ? 'w-6 h-6' : 'w-8 h-8';

                const titleColor = section_data.faq_title_color || getContrastColor(effectiveBackgroundColor);
                const questionColor = section_data.faq_question_color || getContrastColor(effectiveBackgroundColor);
                const answerColor = section_data.faq_answer_color || '#374151';
                const buttonBgColor = section_data.faq_button_bg_color || primaryColor;
                const buttonTextColor = section_data.faq_button_text_color || getContrastColor(buttonBgColor);

                const handleToggle = (index: number) => {
                    setOpenIndex(openIndex === index ? null : index);
                };

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionPadding}`}
                    >
                        <div className={`max-w-7xl mx-auto ${containerPadding}`}>
                            <h2 className={`${titleSize} font-bold text-center ${titleMargin}`} style={{ color: titleColor }}>
                                {section_data.faq_title || 'Frequently Asked Questions'}
                            </h2>

                            <div className={faqSpacing}>
                                {section_data.faq_items?.map((item: any, idx: number) => (
                                    <div key={idx} className="rounded-lg overflow-hidden">
                                        <button
                                            type="button"
                                            className={`w-full text-left p-3 md:p-4 flex items-center gap-3 md:gap-4 transition-all duration-300 focus:outline-none`}
                                            style={{ backgroundColor: openIndex === idx ? effectiveBackgroundColor : themeDark }}
                                            onClick={() => handleToggle(idx)}
                                        >
                                            <span className={`${badgeSize} rounded-full flex items-center justify-center flex-shrink-0`} style={{ backgroundColor: buttonBgColor }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                                    <polyline points="20 6 9 17 4 12"></polyline>
                                                </svg>
                                            </span>
                                            <span className={`flex-1 font-semibold ${isMobile ? 'text-base' : 'text-lg'}`} style={{ color: questionColor }}>
                                                {item.question}
                                            </span>
                                            <span className="flex-shrink-0 transition-transform duration-300" style={{ color: questionColor, transform: openIndex === idx ? 'rotate(180deg)' : 'rotate(0deg)' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                    <polyline points="6 9 12 15 18 9"></polyline>
                                                </svg>
                                            </span>
                                        </button>
                                        <div className="overflow-hidden transition-all duration-300" style={{ maxHeight: openIndex === idx ? '500px' : '0' }}>
                                            <div className="p-4 md:p-6 bg-white">
                                                <p className="leading-relaxed text-sm md:text-base" style={{ color: answerColor }}>
                                                    {item.answer}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {section_data.button_text && (
                                <div className="flex justify-center mt-8 md:mt-12">
                                    <a
                                        href="#"
                                        className={`inline-flex items-center gap-2 ${buttonPadding} font-bold uppercase tracking-wide ${buttonTextSize} rounded-lg transition-opacity`}
                                        style={{ backgroundColor: buttonBgColor, color: buttonTextColor }}
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <circle cx="9" cy="21" r="1"></circle>
                                            <circle cx="20" cy="21" r="1"></circle>
                                            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                        </svg>
                                        {section_data.button_text}
                                    </a>
                                </div>
                            )}
                        </div>
                    </BackgroundRenderer>
                );
            }

            case 'featured_products': {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';
                const titleSize = isMobile ? 'text-2xl' : 'text-3xl';
                const titleMargin = isMobile ? 'mb-6' : isTablet ? 'mb-8' : 'mb-12';
                const gridCols = isMobile ? 'grid-cols-1' : 'md:grid-cols-2';
                const gridGap = isMobile ? 'gap-4' : isTablet ? 'gap-6' : 'gap-8';
                const imageHeight = isMobile ? 'h-48' : 'h-64';
                const cardRounded = isMobile ? 'rounded-md' : 'rounded-lg';
                const contentPadding = isMobile ? 'p-4' : 'p-6';
                const headingSize = isMobile ? 'text-lg' : 'text-xl';
                const headingMargin = isMobile ? 'mb-1' : 'mb-2';
                const descSize = isMobile ? 'text-sm' : 'text-base';
                const descMargin = isMobile ? 'mb-3' : 'mb-4';
                const buttonPadding = isMobile ? 'py-2.5 px-4' : 'py-3 px-6';
                const buttonTextSize = isMobile ? 'text-xs' : 'text-sm';

                const sectionTitleColor = section_data.featured_section_title_color || getContrastColor(effectiveBackgroundColor);
                const productTitleColor = section_data.featured_product_title_color || getContrastColor(effectiveBackgroundColor);
                const productDescriptionColor = section_data.featured_product_description_color || getSecondaryColor(effectiveBackgroundColor);
                const buttonBgColor = section_data.featured_button_bg_color || primaryColor;
                const buttonTextColor = section_data.featured_button_text_color || getContrastColor(buttonBgColor);

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionPadding}`}
                    >
                        <div className={`max-w-7xl mx-auto ${containerPadding}`}>
                            <h2 className={`${titleSize} font-bold text-center ${titleMargin}`} style={{ color: sectionTitleColor }}>
                                {section_data.section_title || 'Featured Products'}
                            </h2>

                            <div className={`grid ${gridCols} ${gridGap}`}>
                                {section_data.featured_products?.map((product: any, idx: number) => (
                                    <div key={idx} className={`bg-white/10 ${cardRounded} overflow-hidden backdrop-blur-sm`}>
                                        {product.image && (
                                            <img
                                                src={product.image}
                                                alt={product.title}
                                                className={`w-full ${imageHeight} object-cover`}
                                            />
                                        )}

                                        <div className={`${contentPadding} text-center`}>
                                            <h3 className={`${headingSize} font-bold ${headingMargin}`} style={{ color: productTitleColor }}>
                                                {product.title}
                                            </h3>

                                            <p className={`${descSize} ${descMargin}`} style={{ color: productDescriptionColor }}>
                                                {product.description}
                                            </p>

                                            {product.button_text && (
                                                <button
                                                    type="button"
                                                    className={`inline-flex items-center justify-center gap-2 ${buttonPadding} font-bold uppercase ${buttonTextSize} rounded-sm transition-colors duration-200`}
                                                    style={{
                                                        backgroundColor: buttonBgColor,
                                                        color: buttonTextColor
                                                    }}
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                        <circle cx="9" cy="21" r="1"></circle>
                                                        <circle cx="20" cy="21" r="1"></circle>
                                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                                    </svg>
                                                    {product.button_text}
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </BackgroundRenderer>
                );
            }

            default: {
                const isMobile = deviceType === 'mobile';
                const isTablet = deviceType === 'tablet';

                const sectionPadding = isMobile ? 'py-8' : isTablet ? 'py-10' : 'py-16';
                const containerPadding = isMobile ? 'px-4' : isTablet ? 'px-6' : 'px-8';

                return (
                    <BackgroundRenderer 
                        sectionData={section_data} 
                        pageConfigs={pageConfigs}
                        className={`w-full ${sectionPadding}`}
                    >
                        <div className={`max-w-4xl mx-auto ${containerPadding} text-center`}>
                            <p style={{ color: getSecondaryColor(effectiveBackgroundColor) }}>
                                Unknown section type: {type}
                            </p>
                        </div>
                    </BackgroundRenderer>
                );
            }
        }
    };

    return renderSection();
};

export default SectionRenderer;
