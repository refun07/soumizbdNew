import { FaAngleDown } from 'react-icons/fa';
import { NavLink } from 'react-router-dom';
import { INavLink, useNavLinks } from '../../../hooks/useNavLinks';
import { cn } from '../../../utils/cn';
import { translate } from '../../../utils/translate';
import MegaDropdown from './MegaDropdown';
import SimpleDropdown from './SimpleDropdown';
import { useEffect, useState, useRef } from 'react';

interface MenuDesign {
    bg_color: string;
    text_color: string;
    hover_color: string;
}

interface MenuStyle {
    font_size?: number;
    font_weight?: string;
    text_color?: string;
    active_border_color?: string;
    active_border_width?: number;
}

interface Props {
    dropdownPosition?:
        | 'left'
        | 'right'
        | 'center'
        | 'relative-left'
        | 'relative-right'
        | 'relative-center';
    classNames?: {
        container?: string;
        menuLinks?: string;

        subMenuGroup?: string;
        subMenuContainer?: string;
        subMenuLinks?: string;
    };
    navLinks?: INavLink[];
    menuStyle?: MenuStyle;
}

const NavLinks = ({
    navLinks,
    classNames,
    dropdownPosition,
    menuStyle,
}: Props) => {
    const defaultNavLinks = navLinks || useNavLinks();
    const [menuDesign, setMenuDesign] = useState<MenuDesign>({
        bg_color: '#ffffff',
        text_color: '#0f0f0f',
        hover_color: '#b3052a'
    });
    const menuItemRefs = useRef<Map<number, HTMLDivElement>>(new Map());
    const [rightAlignedItems, setRightAlignedItems] = useState<Set<number>>(new Set());

    useEffect(() => {
        try {
            const mainMenuConfig = (window as any).config?.mainMenuConfig || {};
            const design = mainMenuConfig.design || {};
            setMenuDesign({
                bg_color: design.bg_color || '#ffffff',
                text_color: design.text_color || '#0f0f0f',
                hover_color: design.hover_color || '#b3052a'
            });
        } catch (error) {
            console.error('Failed to load menu design config:', error);
        }
    }, []);

    // Cleanup refs when component unmounts or navLinks changes
    useEffect(() => {
        return () => {
            menuItemRefs.current.clear();
        };
    }, [navLinks]);

    const checkAndSetAlignment = (idx: number) => {
        // Skip if dropdownPosition prop is provided (manual override)
        if (dropdownPosition) return;
        
        const element = menuItemRefs.current.get(idx);
        if (element) {
            const rect = element.getBoundingClientRect();
            const viewportWidth = window.innerWidth;
            const dropdownWidth = 250; // estimated dropdown width
            // Check if dropdown would overflow the right edge
            const shouldRightAlign = rect.right + dropdownWidth > viewportWidth;
            setRightAlignedItems(prev => {
                const newSet = new Set(prev);
                if (shouldRightAlign) {
                    newSet.add(idx);
                } else {
                    newSet.delete(idx);
                }
                return newSet;
            });
        }
    };

    const getDropdownPosition = (idx: number) => {
        const element = menuItemRefs.current.get(idx);
        if (!element) return dropdownPosition || 'left';
        
        const rect = element.getBoundingClientRect();
        const viewportWidth = window.innerWidth;
        const dropdownWidth = 250;
        
        // Check for left edge overflow (for centered dropdowns)
        const wouldOverflowLeft = rect.left - (dropdownWidth / 2) < 0;
        // Check for right edge overflow
        const wouldOverflowRight = rect.right + (dropdownWidth / 2) > viewportWidth;
        
        // If explicit dropdownPosition prop is provided, validate it doesn't overflow
        if (dropdownPosition) {
            // If using center but would overflow left, use left instead
            if ((dropdownPosition === 'relative-center' || dropdownPosition === 'center') && wouldOverflowLeft) {
                return 'left';
            }
            // If using center but would overflow right, use right instead
            if ((dropdownPosition === 'relative-center' || dropdownPosition === 'center') && wouldOverflowRight) {
                return 'right';
            }
            return dropdownPosition;
        }
        
        // Otherwise use dynamic edge detection
        // If edge detection says right-align, use 'right' (which applies right-0)
        // Otherwise use 'left' (which applies left-0)
        return rightAlignedItems.has(idx) ? 'right' : 'left';
    };

    const textColor = menuStyle?.text_color || menuDesign.text_color;
    const activeBorderColor =
        menuStyle?.active_border_color || menuDesign.hover_color;
    const fontSize = menuStyle?.font_size || 14;
    const fontWeight = menuStyle?.font_weight || '500';
    const activeBorderWidth = menuStyle?.active_border_width || 4;

    return (
        <>
            <style>{`
                .nav-menu-container {
                    --menu-text-color: ${textColor};
                    --menu-hover-color: ${menuDesign.hover_color};
                    --menu-font-size: ${fontSize}px;
                    --menu-font-weight: ${fontWeight};
                    --menu-active-border-color: ${activeBorderColor};
                    --menu-active-border-width: ${activeBorderWidth}px;
                }
                .nav-menu-container .nav-link,
                .nav-menu-container .nav-link-hover {
                    font-size: var(--menu-font-size);
                    font-weight: var(--menu-font-weight);
                    border-bottom-width: var(--menu-active-border-width);
                }
                .nav-menu-container .nav-link:hover,
                .nav-menu-container .nav-link-hover:hover {
                    color: var(--menu-hover-color) !important;
                }
                .nav-menu-container .nav-link-active {
                    color: var(--menu-text-color) !important;
                    border-color: var(--menu-active-border-color) !important;
                }
                .nav-menu-container .nav-border-hover:hover {
                    border-color: var(--menu-active-border-color) !important;
                }
            `}</style>
            <div
                className={cn(
                    'flex items-center gap-6 md:gap-6 3xl:gap-14 nav-menu-container',
                    classNames?.container,
                )}
                style={{ color: textColor }}
            >
                {defaultNavLinks.map((item, idx) =>
                    item.subMenu ? (
                        <div
                            className={cn('group relative', classNames?.subMenuGroup)}
                            key={idx}
                            ref={(el) => {
                                if (el) {
                                    menuItemRefs.current.set(idx, el);
                                } else {
                                    menuItemRefs.current.delete(idx);
                                }
                            }}
                            onMouseEnter={() => checkAndSetAlignment(idx)}
                        >
                            <span
                                className={cn(
                                    'inline-flex items-center gap-2 py-[15px] border-b-4 border-transparent cursor-pointer nav-link-hover nav-border-hover transition-all duration-300',
                                    classNames?.menuLinks,
                                )}
                            >
                                {item.title}
                                <span className="transition-all duration-300">
                                    <FaAngleDown />
                                </span>
                            </span>

                            {item.subMenu[0]?.hasOwnProperty('children_categories') ? (
                                <MegaDropdown
                                    subMenu={item.subMenu}
                                    classNames={classNames}
                                    dropdownPosition={getDropdownPosition(idx)}
                                />
                            ) : (
                                <SimpleDropdown
                                    items={item.subMenu}
                                    classNames={classNames}
                                    dropdownPosition={getDropdownPosition(idx)}
                                />
                            )}
                        </div>
                    ) : (
                        <NavLink
                            to={item.link!}
                            className={(props) =>
                                cn(
                                    'group inline-flex items-center gap-2 py-[15px] border-b-4 transition-all duration-300 nav-link',
                                    classNames?.menuLinks,
                                    {
                                        'nav-link-active': props.isActive,
                                        'border-transparent': !props.isActive,
                                    },
                                )
                            }
                            key={idx}
                        >
                            {translate(item.title)}
                        </NavLink>
                    ),
                )}
            </div>
        </>
    );
};

export default NavLinks;
