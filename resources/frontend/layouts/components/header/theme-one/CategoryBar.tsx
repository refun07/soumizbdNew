import NavLinks from '../NavLinks';
import { useEffect, useState } from 'react';

interface CustomLink {
    name: string;
    url: string;
    icon_path: string;
    icon_svg?: string;
}

interface MenuStyle {
    font_size?: number;
    font_weight?: string;
    text_color?: string;
    active_border_color?: string;
    active_border_width?: number;
}

interface CategoryBarProps {
    navbarConfig?: {
        custom_links_enabled: boolean;
        custom_links: CustomLink[];
        menu_style?: MenuStyle;
    };
}

interface MenuDesign {
    bg_color: string;
    text_color: string;
    hover_color: string;
}

const CategoryBar: React.FC<CategoryBarProps> = ({ navbarConfig }) => {
    const [menuDesign, setMenuDesign] = useState<MenuDesign>({
        bg_color: '#5A9D5E',
        text_color: '#FFFFFF',
        hover_color: '#FFFFFF'
    });

    useEffect(() => {
        try {
            const mainMenuConfig = (window as any).config?.mainMenuConfig || {};
            const design = mainMenuConfig.design || {};
            setMenuDesign({
                bg_color: design.bg_color || '#5A9D5E',
                text_color: design.text_color || '#FFFFFF',
                hover_color: design.hover_color || '#FFFFFF'
            });
        } catch (error) {
            console.error('Failed to load menu design config:', error);
        }
    }, []);

    const menuTextColor = navbarConfig?.menu_style?.text_color || menuDesign.text_color;
    const menuFontSize = navbarConfig?.menu_style?.font_size || 14;
    const menuFontWeight = navbarConfig?.menu_style?.font_weight || '500';

    return (
        <div className="hidden lg:block">
            <div
                className="h-[57px] flex items-center no-scrollbar border-b border-gray-100"
                style={{ backgroundColor: menuDesign.bg_color }}
            >
                <style>{`
                    .category-bar-container {
                        --menu-text-color: ${menuDesign.text_color};
                        --menu-hover-color: ${menuDesign.hover_color};
                    }
                    .category-bar-container .custom-link:hover {
                        color: var(--menu-hover-color) !important;
                    }
                `}</style>
                <div className="container flex items-center justify-between relative category-bar-container">
                    <NavLinks menuStyle={navbarConfig?.menu_style} />

                    {navbarConfig?.custom_links_enabled && navbarConfig?.custom_links?.length > 0 && (
                        <div className="flex items-center gap-5">
                            {navbarConfig.custom_links.map((link, index) => (
                                <a
                                    key={index}
                                    href={link.url || '#'}
                                    className="flex items-center gap-2 transition-colors custom-link"
                                    style={{
                                        color: menuTextColor,
                                        fontSize: `${menuFontSize}px`,
                                        fontWeight: menuFontWeight,
                                    }}
                                >
                                    {link.icon_svg ? (
                                        <span
                                            className="w-4 h-4 inline-block"
                                            dangerouslySetInnerHTML={{ __html: link.icon_svg }}
                                        />
                                    ) : link.icon_path ? (
                                        <img
                                            src={`/${link.icon_path}`}
                                            alt={link.name}
                                            className="w-4 h-4"
                                        />
                                    ) : null}
                                    <span>{link.name}</span>
                                </a>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default CategoryBar;
