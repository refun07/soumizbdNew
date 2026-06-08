import React, { useState, useEffect } from 'react';
import ToastContainer from '../../theme/components/Toast';
import useToast from '../../theme/hooks/useToast';

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

interface Page {
  id: number;
  slug: string;
  title: string;
}

interface Category {
  id: number;
  slug: string;
  title: string;
  children?: Category[];
}

interface MenuDesign {
  bg_color: string;
  text_color: string;
  hover_color: string;
}

interface MenuConfig {
  menu_items: MenuItem[];
  show_sub_category: boolean;
  design: MenuDesign;
}

interface MenuBuilderProps {
  apiPrefix: string;
}

const MenuBuilder: React.FC<MenuBuilderProps> = ({ apiPrefix }) => {
  const [menuItems, setMenuItems] = useState<MenuItem[]>([]);
  const [showSubCategory, setShowSubCategory] = useState(true);
  const [bgColor, setBgColor] = useState("#5A9D5E");
  const [textColor, setTextColor] = useState("#FFFFFF");
  const [hoverColor, setHoverColor] = useState("#FFFFFF");
  const [loading, setLoading] = useState(true);
  const [saving, setSaving] = useState(false);
  const [pages, setPages] = useState<Page[]>([]);
  const [categories, setCategories] = useState<Category[]>([]);
  const { toasts, success, error, removeToast } = useToast();

  // Load menu configuration and options from API
  useEffect(() => {
    const loadData = async () => {
      try {
        // Load menu config
        const configResponse = await fetch(`${apiPrefix}/config`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
          }
        });

        const configData = await configResponse.json();

        if (configData.success && configData.data) {
          const config: MenuConfig = configData.data;
          setMenuItems(config.menu_items || []);
          setShowSubCategory(config.show_sub_category);
          setBgColor(config.design?.bg_color || "#5A9D5E");
          setTextColor(config.design?.text_color || "#FFFFFF");
          setHoverColor(config.design?.hover_color || "#FFFFFF");
        }

        // Load pages
        const pagesResponse = await fetch(`${apiPrefix}/pages`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
          }
        });

        const pagesData = await pagesResponse.json();

        if (pagesData.success) {
          setPages(pagesData.data || []);
        }

        // Load categories
        const categoriesResponse = await fetch(`${apiPrefix}/categories`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
          }
        });

        const categoriesData = await categoriesResponse.json();

        if (categoriesData.success) {
          setCategories(categoriesData.data || []);
        }
      } catch (error) {
        console.error('Failed to load data:', error);
      } finally {
        setLoading(false);
      }
    };

    loadData();
  }, [apiPrefix]);

  const addNewMenuItem = () => {
    const newItem: MenuItem = {
      id: `menu_${Date.now()}`,
      name: '',
      type: 'Existing Page',
      link: '',
      selectedPageId: null,
      selectedCategoryId: null,
      menuLevel: undefined // Category and Existing Page don't need menuLevel
    };
    setMenuItems([...menuItems, newItem]);
  };

  const updateMenuItem = (id: string, field: keyof MenuItem, value: any) => {
    setMenuItems(menuItems.map(item => {
      if (item.id === id) {
        const updatedItem = { ...item, [field]: value };

        // Auto-populate link and name when page is selected (not for category)
        if (field === 'selectedPageId' && value) {
          const selectedPage = pages.find(p => p.id === value);
          if (selectedPage) {
            updatedItem.link = `/pages/${selectedPage.slug}`;
            // Auto-populate name if not manually modified
            if (!item.name || item.name === '') {
              updatedItem.name = selectedPage.title;
            }
          }
        }

        // Auto-set menuLevel to Primary when type is set to Custom Link
        if (field === 'type' && value === 'Custom Link') {
          updatedItem.menuLevel = 'Primary';
        }

        // Category type doesn't need auto-population - user sets the name manually
        // and it shows all categories in the frontend

        return updatedItem;
      }
      return item;
    }));
  };

  const removeMenuItem = (id: string) => {
    setMenuItems(menuItems.filter(item => item.id !== id));
  };

  const addChildItem = (parentId: string) => {
    const newChild: MenuItem = {
      id: `menu_${Date.now()}`,
      name: '',
      type: 'Custom Link', // Sub-menus default to Custom Link
      link: '',
      selectedPageId: null,
      selectedCategoryId: null,
      menuLevel: 'Sub' // Sub-menus are "Sub" level by default
    };

    setMenuItems(menuItems.map(item => {
      if (item.id === parentId) {
        return {
          ...item,
          children: [...(item.children || []), newChild]
        };
      }
      return item;
    }));
  };

  const updateChildItem = (parentId: string, childId: string, field: keyof MenuItem, value: any) => {
    setMenuItems(menuItems.map(item => {
      if (item.children) {
        const updatedChildren = item.children.map(child => {
          if (child.id === childId) {
            const updatedChild = { ...child, [field]: value };

            // Auto-populate link and name when page is selected (not for category)
            if (field === 'selectedPageId' && value) {
              const selectedPage = pages.find(p => p.id === value);
              if (selectedPage) {
                updatedChild.link = `/pages/${selectedPage.slug}`;
                // Auto-populate name if not manually modified
                if (!child.name || child.name === '') {
                  updatedChild.name = selectedPage.title;
                }
              }
            }

            // Auto-set menuLevel to Sub when type is set to Custom Link for child items
            if (field === 'type' && value === 'Custom Link') {
              updatedChild.menuLevel = 'Sub';
            }

            // Category type doesn't need auto-population

            return updatedChild;
          }
          return child;
        });
        if (updatedChildren.some((child, idx) => child.id === childId)) {
          return { ...item, children: updatedChildren };
        }
      }
      return item;
    }));
  };

  const removeChildItem = (parentId: string, childId: string) => {
    setMenuItems(menuItems.map(item => {
      if (item.children) {
        const hasChild = item.children.some(child => child.id === childId);
        if (hasChild) {
          return {
            ...item,
            children: item.children.filter(child => child.id !== childId)
          };
        }
      }
      return item;
    }));
  };

  const handleSave = async () => {
    setSaving(true);
    try {
      const response = await fetch(`${apiPrefix}/config`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
        },
        body: JSON.stringify({
          menu_items: menuItems,
          show_sub_category: showSubCategory,
          design: {
            bg_color: bgColor,
            text_color: textColor,
            hover_color: hoverColor
          }
        })
      });

      const data = await response.json();

      if (data.success) {
        success('Menu configuration saved successfully!');
      } else {
        error(data.message || 'Failed to save menu configuration');
      }
    } catch (error) {
      console.error('Save failed:', error);
      error('Error saving menu configuration');
    } finally {
      setSaving(false);
    }
  };

  // Reorder menu items at parent level
  const reorderMenuItems = (startIndex: number, endIndex: number) => {
    const result = Array.from(menuItems);
    const [removed] = result.splice(startIndex, 1);
    result.splice(endIndex, 0, removed);
    setMenuItems(result);
  };

  // Reorder child items
  const reorderChildItems = (parentId: string, startIndex: number, endIndex: number) => {
    setMenuItems(menuItems.map(item => {
      if (item.id === parentId && item.children) {
        const result = Array.from(item.children);
        const [removed] = result.splice(startIndex, 1);
        result.splice(endIndex, 0, removed);
        return { ...item, children: result };
      }
      return item;
    }));
  };

  const renderMenuRows = (items: MenuItem[], isChild = false, parentId?: string) => {
    return items.map((item, index) => (
      <div key={item.id}>
        <div className={`flex items-center gap-4 py-4 px-6 border-b border-gray-200 ${isChild ? "ml-20" : ""}`}>
          {/* Reorder Controls */}
          <div className="flex flex-col gap-1">
            <button
              onClick={() => {
                if (isChild && parentId) {
                  reorderChildItems(parentId, index, index - 1);
                } else {
                  reorderMenuItems(index, index - 1);
                }
              }}
              disabled={index === 0}
              className="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed"
              title="Move up"
            >
              <i className="fa-solid fa-chevron-up w-4 h-4"></i>
            </button>
            <button
              onClick={() => {
                if (isChild && parentId) {
                  reorderChildItems(parentId, index, index + 1);
                } else {
                  reorderMenuItems(index, index + 1);
                }
              }}
              disabled={isChild ? index >= (items.length - 1) : index >= (menuItems.length - 1)}
              className="p-1 text-gray-400 hover:text-gray-600 disabled:opacity-30 disabled:cursor-not-allowed"
              title="Move down"
            >
              <i className="fa-solid fa-chevron-down w-4 h-4"></i>
            </button>
          </div>

          <div className="flex-1">
            <label className="text-xs font-semibold text-gray-700 block mb-1">NAME</label>
            <input
              type="text"
              value={item.name}
              onChange={(e) => {
                if (isChild && parentId) {
                  updateChildItem(parentId, item.id, 'name', e.target.value);
                } else {
                  updateMenuItem(item.id, 'name', e.target.value);
                }
              }}
              placeholder="Enter menu name"
              className="w-full px-3 py-2 border border-gray-300 rounded bg-white text-sm focus:outline-none focus:border-blue-500"
            />
          </div>

          <div className="flex-1">
            <label className="text-xs font-semibold text-gray-700 block mb-1">Type</label>
            <div className="relative">
              <select
                value={item.type}
                onChange={(e) => {
                  if (isChild && parentId) {
                    updateChildItem(parentId, item.id, 'type', e.target.value);
                  } else {
                    updateMenuItem(item.id, 'type', e.target.value);
                  }
                }}
                className="w-full px-3 py-2 border border-gray-300 rounded bg-white text-sm appearance-none focus:outline-none focus:border-blue-500 pr-8"
              >
                <option value="Existing Page">Existing Page</option>
                <option value="Custom Link">Custom Link</option>
                <option value="Category">Category</option>
              </select>
              <i className="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
            </div>
          </div>

          {item.type === 'Custom Link' && (
            <div className="flex-1">
              <label className="text-xs font-semibold text-gray-700 block mb-1">Menu Level</label>
              <div className="relative">
                <select
                  value={item.menuLevel || ''}
                  onChange={(e) => {
                    if (isChild && parentId) {
                      updateChildItem(parentId, item.id, 'menuLevel', e.target.value || undefined);
                    } else {
                      updateMenuItem(item.id, 'menuLevel', e.target.value || undefined);
                    }
                  }}
                  className="w-full px-3 py-2 border border-gray-300 rounded bg-white text-sm appearance-none focus:outline-none focus:border-blue-500 pr-8"
                >
                  <option value="">Select level</option>
                  <option value="Primary">Primary</option>
                  <option value="Sub">Sub</option>
                </select>
                <i className="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
              </div>
            </div>
          )}

          {/* Dynamic field based on type */}
          {item.type === 'Existing Page' && (
            <div className="flex-1">
              <label className="text-xs font-semibold text-gray-700 block mb-1">Select Page</label>
              <div className="relative">
                <select
                  value={item.selectedPageId || ''}
                  onChange={(e) => {
                    if (isChild && parentId) {
                      updateChildItem(parentId, item.id, 'selectedPageId', e.target.value ? parseInt(e.target.value) : null);
                    } else {
                      updateMenuItem(item.id, 'selectedPageId', e.target.value ? parseInt(e.target.value) : null);
                    }
                  }}
                  className="w-full px-3 py-2 border border-gray-300 rounded bg-white text-sm appearance-none focus:outline-none focus:border-blue-500 pr-8"
                  style={{ color: item.selectedPageId ? '#000000' : '#6B7280' }}
                >
                  <option value="" disabled>Select a page</option>
                  {pages.map(page => (
                    <option key={page.id} value={page.id}>
                      {page.title}
                    </option>
                  ))}
                </select>
                <i className="fa-solid fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
              </div>
            </div>
          )}

          {/* For Category type, no additional fields needed - user just sets the name */}
          {/* and it shows all categories in the frontend */}

          {item.type === 'Custom Link' && (
            <div className="flex-1">
              <label className="text-xs font-semibold text-gray-700 block mb-1">Link</label>
              <input
                type="text"
                value={item.link}
                onChange={(e) => {
                  if (isChild && parentId) {
                    updateChildItem(parentId, item.id, 'link', e.target.value);
                  } else {
                    updateMenuItem(item.id, 'link', e.target.value);
                  }
                }}
                placeholder="Enter link URL"
                className="w-full px-3 py-2 border border-gray-300 rounded bg-white text-sm focus:outline-none focus:border-blue-500"
              />
            </div>
          )}

          {/* Read-only link field for Existing Page type only (Category doesn't need link) */}
          {item.type === 'Existing Page' && (
            <div className="flex-1">
              <label className="text-xs font-semibold text-gray-700 block mb-1">Link (Auto)</label>
              <input
                type="text"
                value={item.link}
                readOnly
                className="w-full px-3 py-2 border border-gray-300 rounded bg-gray-50 text-sm focus:outline-none"
                placeholder="Link will be auto-generated"
              />
            </div>
          )}

          <button
            onClick={() => {
              if (isChild && parentId) {
                removeChildItem(parentId, item.id);
              } else {
                removeMenuItem(item.id);
              }
            }}
            className="text-red-400 hover:text-red-600 mt-5"
          >
            <i className="fa-regular fa-trash-can w-5 h-5"></i>
          </button>
        </div>

        {/* Child items */}
        {item.children && renderMenuRows(item.children, true, item.id)}

        {/* Add child button for Custom Link items only */}
        {!isChild && item.type === 'Custom Link' && (
          <div className="ml-20 py-2">
            <button
              onClick={() => addChildItem(item.id)}
              className="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center gap-1"
            >
              <i className="fa-solid fa-plus w-4 h-4"></i>
              Add Sub Menu
            </button>
          </div>
        )}
      </div>
    ));
  };

  if (loading) {
    return (
      <div className="min-h-screen bg-gray-50 p-6 flex items-center justify-center">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
          <p className="text-gray-600">Loading menu configuration...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-gray-50 p-6">
      {/* Header */}
      <div className="flex items-center justify-between mb-8">
        <h1 className="text-2xl font-bold text-gray-900">Menu Builder</h1>
      </div>

      {/* Main Menu Section */}
      <div className="bg-white rounded mb-8">
        <div className="bg-gray-100 px-6 py-4 rounded-t border-b border-gray-200">
          <h2 className="text-base font-semibold text-gray-900">Main Menu</h2>
        </div>
        <div>{renderMenuRows(menuItems)}</div>
        <div className="px-6 py-6 border-t border-gray-200">
          <button
            onClick={addNewMenuItem}
            className="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded flex items-center gap-2 font-semibold text-sm"
          >
            <i className="fa-solid fa-plus w-5 h-5"></i>
            ADD NEW MENU
          </button>
        </div>
      </div>

      {/* Category Section */}
      <div className="bg-white rounded mb-8">
        <div className="bg-gray-100 px-6 py-4 rounded-t border-b border-gray-200">
          <h2 className="text-base font-semibold text-gray-900">Category</h2>
        </div>
        <div className="px-6 py-6 flex items-center gap-4">
          <label className="text-sm font-semibold text-gray-900">SHOW/HIDE SUB CATEGORY</label>
          <button
            onClick={() => setShowSubCategory(!showSubCategory)}
            className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
              showSubCategory ? "bg-green-500" : "bg-gray-300"
            }`}
          >
            <span
              className={`inline-block h-5 w-5 transform rounded-full bg-white transition-transform ${
                showSubCategory ? "translate-x-5" : "translate-x-0.5"
              }`}
            />
          </button>
        </div>
      </div>

      {/* Menu Design Section */}
      <div className="bg-white rounded mb-8">
        <div className="bg-gray-100 px-6 py-4 rounded-t border-b border-gray-200">
          <h2 className="text-base font-semibold text-gray-900">Menu Design</h2>
        </div>
        <div className="px-6 py-8">
          <div className="flex items-start gap-8">
            <div className="flex flex-col gap-4">
              <label className="text-sm font-semibold text-gray-900">BACKGROUND COLOR</label>
              <input
                type="color"
                value={bgColor}
                onChange={(e) => setBgColor(e.target.value)}
                className="w-10 h-10 border border-gray-300 rounded cursor-pointer"
              />
            </div>
            <div className="flex flex-col gap-4">
              <label className="text-sm font-semibold text-gray-900">TEXT COLOR</label>
              <input
                type="color"
                value={textColor}
                onChange={(e) => setTextColor(e.target.value)}
                className="w-10 h-10 border border-gray-300 rounded cursor-pointer"
              />
            </div>
            <div className="flex flex-col gap-4">
              <label className="text-sm font-semibold text-gray-900">HOVER COLOR</label>
              <input
                type="color"
                value={hoverColor}
                onChange={(e) => setHoverColor(e.target.value)}
                className="w-10 h-10 border border-gray-300 rounded cursor-pointer"
              />
            </div>
          </div>
        </div>
      </div>

      {/* Save Settings Button */}
      <div className="flex justify-end">
        <button
          onClick={handleSave}
          disabled={saving}
          className="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded font-semibold text-sm disabled:opacity-50 disabled:cursor-not-allowed"
        >
          {saving ? 'SAVING...' : 'SAVE SETTINGS'}
        </button>
      </div>

      {/* Toast Container */}
      <ToastContainer toasts={toasts} onRemove={removeToast} />
    </div>
  );
};

export default MenuBuilder;
