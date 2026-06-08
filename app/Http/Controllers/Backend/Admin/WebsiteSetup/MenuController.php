<?php

namespace App\Http\Controllers\Backend\Admin\WebsiteSetup;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MenuController extends Controller
{
    # constructor
    public function __construct()
    {
        $this->middleware(['permission:homepage'])->only(['index', 'store', 'update', 'destroy']);
    }

    # show menu builder page
    public function index()
    {
        return view('backend.admin.website-setup.menu-builder');
    }

    # get menu configuration
    public function getMenuConfig()
    {
        try {
            // Get menu configuration from settings
            $menuConfigJson = getSetting('main_menu_config', '{}');
            $menuConfig = json_decode($menuConfigJson, true);

            // If empty or invalid, return default structure
            if (!$menuConfig || !is_array($menuConfig)) {
                $menuConfig = [
                    'menu_items' => [],
                    'show_sub_category' => true,
                    'design' => [
                        'bg_color' => '#5A9D5E',
                        'text_color' => '#FFFFFF',
                        'hover_color' => '#FFFFFF'
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $menuConfig
            ]);

        } catch (\Exception $e) {
            \Log::error('Get menu config failed', [
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get menu configuration'
            ], 500);
        }
    }

    # save menu configuration
    public function saveMenuConfig(Request $request)
    {
        try {
            $request->validate([
                'menu_items' => 'nullable|array',
                'menu_items.*.id' => 'required|string',
                'menu_items.*.name' => 'required|string|max:50',
                'menu_items.*.type' => 'required|in:Custom Link,Category,Existing Page',
                'menu_items.*.menuLevel' => 'nullable|in:Primary,Sub',
                'menu_items.*.link' => 'required_if:type,Custom Link,Existing Page|string|max:255',
                'menu_items.*.selectedPageId' => 'nullable|integer',
                'menu_items.*.selectedCategoryId' => 'nullable|integer',
                'menu_items.*.children' => 'nullable|array',
                'show_sub_category' => 'boolean',
                'design.bg_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'design.text_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'design.hover_color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/'
            ]);

            // Prepare menu items for storage (remove generated IDs, use array indices)
            $menuItems = $request->input('menu_items', []);
            $processedItems = [];

            foreach ($menuItems as $item) {
                $processedItem = [
                    'id' => uniqid('menu_'),
                    'name' => $item['name'],
                    'type' => $item['type'],
                    'menuLevel' => $item['menuLevel'] ?? null,
                    'link' => $item['link'],
                    'selectedPageId' => $item['selectedPageId'] ?? null,
                    'selectedCategoryId' => $item['selectedCategoryId'] ?? null,
                    'children' => []
                ];

                // Process children
                if (isset($item['children']) && is_array($item['children'])) {
                    foreach ($item['children'] as $child) {
                        $processedItem['children'][] = [
                            'id' => uniqid('menu_'),
                            'name' => $child['name'],
                            'type' => $child['type'],
                            'menuLevel' => $child['menuLevel'] ?? null,
                            'link' => $child['link'],
                            'selectedPageId' => $child['selectedPageId'] ?? null,
                            'selectedCategoryId' => $child['selectedCategoryId'] ?? null
                        ];
                    }
                }

                $processedItems[] = $processedItem;
            }

            $configData = [
                'menu_items' => $processedItems,
                'show_sub_category' => $request->input('show_sub_category', true),
                'design' => [
                    'bg_color' => $request->input('design.bg_color', '#5A9D5E'),
                    'text_color' => $request->input('design.text_color', '#FFFFFF'),
                    'hover_color' => $request->input('design.hover_color', '#FFFFFF')
                ]
            ];

            // Save to database
            SystemSetting::updateOrCreate(
                ['type' => 'main_menu_config'],
                ['value' => json_encode($configData)]
            );

            // Log the saved data for debugging
            \Log::info('Menu configuration saved', [
                'config_data' => $configData,
                'menu_items_count' => count($processedItems)
            ]);

            // Clear all related caches
            Cache::forget('settings');
            Cache::flush();

            // Log the cache clear for debugging
            \Log::info('Menu configuration saved and cache cleared', [
                'config_data' => $configData,
                'cache_cleared' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu configuration saved successfully (cache cleared)',
                'data' => $configData
            ]);

        } catch (\Exception $e) {
            \Log::error('Save menu config failed', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save menu configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    # public API to get menu for frontend
    public function getPublicMenuConfig()
    {
        try {
            // Get menu configuration from settings
            $menuConfigJson = getSetting('main_menu_config', '{}');
            $menuConfig = json_decode($menuConfigJson, true);

            // If empty or invalid, return empty structure
            if (!$menuConfig || !is_array($menuConfig)) {
                $menuConfig = [
                    'menu_items' => [],
                    'show_sub_category' => true,
                    'design' => [
                        'bg_color' => '#5A9D5E',
                        'text_color' => '#FFFFFF',
                        'hover_color' => '#FFFFFF'
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $menuConfig
            ]);

        } catch (\Exception $e) {
            \Log::error('Get public menu config failed', [
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get menu configuration'
            ], 500);
        }
    }

    # get available pages for menu
    public function getPages()
    {
        try {
            $pages = \App\Models\Page::with('pageTranslations')
                ->get()
                ->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'slug' => $page->slug,
                        'title' => $page->collectTranslation('title')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $pages
            ]);

        } catch (\Exception $e) {
            \Log::error('Get pages failed', [
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get pages'
            ], 500);
        }
    }

    # get available categories for menu
    public function getCategories()
    {
        try {
            $categories = \App\Models\Category::isRoot()
                ->with('childrenCategories.categoryTranslations')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'slug' => $category->slug,
                        'title' => $category->collectTranslation('name'),
                        'children' => $category->childrenCategories->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'slug' => $child->slug,
                                'title' => $child->collectTranslation('name')
                            ];
                        })
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $categories
            ]);

        } catch (\Exception $e) {
            \Log::error('Get categories failed', [
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get categories'
            ], 500);
        }
    }
}
