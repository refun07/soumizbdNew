import { useEffect, useState } from 'react';
import { useGetAllCategoriesQuery } from '../store/features/api/apiSlice';
import { ICategoryShort } from '../types';

export interface INavLink {
    title: string;
    link?: string;
    subMenu?: ICategoryShort[];
}

interface MenuItem {
    id: string;
    name: string;
    type: "Custom Link" | "Category" | "Existing Page";
    menuLevel?: "Primary" | "Sub - About Us";
    link: string;
    selectedPageId?: number | null;
    selectedCategoryId?: number | null;
    children?: MenuItem[];
}

export const useNavLinks = () => {
    const { data: categoriesRes } = useGetAllCategoriesQuery();
    const [navLinks, setNavLinks] = useState<INavLink[]>([]);

    useEffect(() => {
        try {
            // Load menu configuration from window.config
            const mainMenuConfig = (window as any).config?.mainMenuConfig || {};
            const menuItems: MenuItem[] = mainMenuConfig.menu_items || [];

            // Convert menu items to nav links format
            // For Category and Existing Page types, all items are considered primary (no menuLevel needed)
            // For Custom Link type, only items with menuLevel === 'Primary' or undefined are included
            const convertedLinks: INavLink[] = menuItems
                .filter(item => {
                    if (item.type === 'Custom Link') {
                        return item.menuLevel === 'Primary' || item.menuLevel === undefined;
                    }
                    // Category and Existing Page types don't need menuLevel
                    return true;
                })
                .map(item => {
                    // Convert children to subMenu format
                    const subMenu = Array.isArray(item.children) && item.children.length > 0
                        ? item.children.map(child => ({
                              id: child.id,
                              name: child.name,
                              slug: child.link,
                              // Additional properties if needed
                          }))
                        : undefined;

                    if (item.type === 'Category') {
                        return {
                            title: item.name,
                            link: '/products',
                            subMenu: categoriesRes?.categories
                        };
                    } else if (item.type === 'Existing Page') {
                        return {
                            title: item.name,
                            link: item.link || '#',
                            subMenu
                        };
                    } else {
                        // Custom Link
                        return {
                            title: item.name,
                            link: item.link || '#',
                            subMenu
                        };
                    }
                });

            // If no menu items configured, use default
            if (convertedLinks.length === 0) {
                setNavLinks([
                    { title: 'Home', link: '/' },
                    { title: 'Shops', link: '/shops' },
                    { title: 'Products', link: '/products' },
                    {
                        title: 'Categories',
                        subMenu: categoriesRes?.categories,
                    },
                    { title: 'About Us', link: '/pages/about-us' },
                    { title: 'All Brands', link: '/brands' },
                ]);
            } else {
                setNavLinks(convertedLinks);
            }
        } catch (error) {
            console.error('Failed to load nav links from config:', error);
            // Fallback to default on error
            setNavLinks([
                { title: 'Home', link: '/' },
                { title: 'Shops', link: '/shops' },
                { title: 'Products', link: '/products' },
                {
                    title: 'Categories',
                    subMenu: categoriesRes?.categories,
                },
                { title: 'About Us', link: '/pages/about-us' },
                { title: 'All Brands', link: '/brands' },
            ]);
        }
    }, [categoriesRes]);

    return navLinks;
};
