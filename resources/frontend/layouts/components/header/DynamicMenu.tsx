import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import { FaChevronDown } from 'react-icons/fa6';
import { useGetAllCategoriesQuery } from '../../../store/features/api/apiSlice';

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

interface MenuDesign {
  bg_color: string;
  text_color: string;
}

interface MenuConfig {
  menu_items: MenuItem[];
  show_sub_category: boolean;
  design: MenuDesign;
}

const DynamicMenu = ({ className = '' }: { className?: string }) => {
  const [menuConfig, setMenuConfig] = useState<MenuConfig>({
    menu_items: [],
    show_sub_category: true,
    design: {
      bg_color: '#ffffff',
      text_color: '#0f0f0f'
    }
  });

  const { data: categoriesRes } = useGetAllCategoriesQuery();

  // Default className if not provided
  const containerClassName = className || 'h-[50px]';

  useEffect(() => {
    // Load menu configuration from window.config
    const loadMenuConfig = () => {
      try {
        const mainMenuConfig = (window as any).config?.mainMenuConfig || {};

        // Ensure proper structure with fallbacks
        const config = {
          menu_items: mainMenuConfig.menu_items || [],
          show_sub_category: mainMenuConfig.show_sub_category !== undefined ? mainMenuConfig.show_sub_category : true,
          design: {
            bg_color: mainMenuConfig.design?.bg_color || '#ffffff',
            text_color: mainMenuConfig.design?.text_color || '#0f0f0f'
          }
        };

        setMenuConfig(config);
      } catch (error) {
        console.error('Failed to load menu config:', error);
        // Keep default state on error
      }
    };

    loadMenuConfig();
  }, []);

  const getLinkUrl = (item: MenuItem) => {
    if (item.type === 'Existing Page') {
      return item.link || '#';
    } else if (item.type === 'Category') {
      return item.link || '/products';
    } else {
      // Custom Link
      return item.link || '#';
    }
  };

  const renderMenuItem = (item: MenuItem, isChild = false) => {
    const url = getLinkUrl(item);
    const design = menuConfig.design || { bg_color: '#ffffff', text_color: '#0f0f0f' };

    // For Category type, show all categories from categoriesRes
    // For other types, use custom children if available
    let subCategories: any[] = [];
    if (item.type === 'Category' && categoriesRes?.categories) {
      // Show all root categories
      subCategories = categoriesRes.categories;
    }

    // Check if has submenu
    const hasSubMenu = (subCategories && subCategories.length > 0) ||
                       (item.children && Array.isArray(item.children) && item.children.length > 0);

    return (
      <div key={item.id} className="relative group">
        <Link
          to={url}
          className={`flex items-center gap-1 px-3 py-2 hover:bg-white/10 transition-colors ${
            isChild ? 'pl-8' : ''
          }`}
          style={{
            color: design.text_color
          }}
        >
          <span>{item.name}</span>
          {hasSubMenu && (
            <FaChevronDown className="w-4 h-4 ml-1" />
          )}
        </Link>

        {/* Render submenu if any */}
        {hasSubMenu && (
          <div
            className="absolute left-0 top-full min-w-[200px] bg-white shadow-lg border rounded-md py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10"
            style={{
              backgroundColor: design.bg_color
            }}
          >
            {subCategories.length > 0 ? (
              // Render categories for Category type
              subCategories.map((cat: any) => (
                <Link
                  key={cat.id}
                  to={`/products?category=${cat.id}`}
                  className="flex items-center gap-1 px-3 py-2 hover:bg-white/10 transition-colors text-white"
                  style={{
                    color: design.text_color
                  }}
                >
                  <span>{cat.name}</span>
                </Link>
              ))
            ) : (
              // Render custom children for Custom Link type
              item.children?.map((child) => renderMenuItem(child, true))
            )}
          </div>
        )}
      </div>
    );
  };

  // Ensure menu_items is always an array
  const menuItems = Array.isArray(menuConfig.menu_items) ? menuConfig.menu_items : [];

  if (menuItems.length === 0) {
    return null;
  }

  const design = menuConfig.design || { bg_color: '#ffffff', text_color: '#0f0f0f' };

  return (
    <div
      className={`hidden lg:block ${containerClassName}`}
      style={{
        backgroundColor: design.bg_color,
        color: design.text_color
      }}
    >
      <nav className="h-full flex items-center no-scrollbar">
        <div className="container flex justify-start items-center relative">
          <div className="flex items-center gap-1">
            {menuItems.map((item) => renderMenuItem(item))}
          </div>
        </div>
      </nav>
    </div>
  );
};

export default DynamicMenu;
