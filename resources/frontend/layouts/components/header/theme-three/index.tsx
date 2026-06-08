import Logobar from './Logobar';
import DynamicTopBar from '../DynamicTopBar';
import { useEffect, useState } from 'react';

interface HeaderConfig {
    topbar: {
        enabled: boolean;
        hotline_number: string;
        hotline?: string;
        promo_text_icon: string;
        promo_text: string;
        bg_color: string;
        text_color: string;
    };
    navbar: {
        custom_links_enabled: boolean;
        custom_links: Array<{
            name: string;
            url: string;
            icon_path: string;
            icon_svg?: string;
        }>;
    };
}

const HeaderThemeThree = () => {
    const [headerConfig, setHeaderConfig] = useState<HeaderConfig | null>(null);

    useEffect(() => {
        // Load header configuration from window.config
        const loadHeaderConfig = () => {
            try {
                const headerTopbarConfig = (window as any).config?.headerTopbarConfig || {};
                const headerNavbarConfig = (window as any).config?.headerNavbarConfig || {};

                const config: HeaderConfig = {
                    topbar: {
                        enabled: headerTopbarConfig.enabled ?? true,
                        hotline_number: headerTopbarConfig.hotline_number || window.config.topbarHotlineLabel || 'Our Hotline +88 0176 229 201',
                        hotline: headerTopbarConfig.hotline || headerTopbarConfig.hotline_number ? undefined : window.config.topbarHotline,
                        promo_text_icon: headerTopbarConfig.promo_text_icon || '',
                        promo_text: headerTopbarConfig.promo_text || window.config.topbarPromoText || 'FREE delivery & 40% Discount for next 3 orders!',
                        bg_color: headerTopbarConfig.bg_color || '#000000',
                        text_color: headerTopbarConfig.text_color || '#ffffff'
                    },
                    navbar: {
                        custom_links_enabled: headerNavbarConfig.custom_links_enabled ?? false,
                        custom_links: Array.isArray(headerNavbarConfig.custom_links) ? headerNavbarConfig.custom_links : []
                    }
                };

                setHeaderConfig(config);
            } catch (error) {
                // Fallback to default configuration
                setHeaderConfig({
                    topbar: {
                        enabled: true,
                        hotline_number: window.config.topbarHotlineLabel || 'Our Hotline +88 0176 229 201',
                        promo_text_icon: '',
                        promo_text: window.config.topbarPromoText || 'FREE delivery & 40% Discount for next 3 orders!',
                        bg_color: '#000000',
                        text_color: '#ffffff'
                    },
                    navbar: {
                        custom_links_enabled: false,
                        custom_links: []
                    }
                });
            }
        };

        loadHeaderConfig();
    }, []);

    return (
        <>
            {/* Use DynamicTopBar for the topbar section */}
            {headerConfig?.topbar?.enabled && (
                <DynamicTopBar config={headerConfig.topbar} />
            )}
            <Logobar showPromoLinks={headerConfig?.navbar?.custom_links_enabled} />
        </>
    );
};

export default HeaderThemeThree;
