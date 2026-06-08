<?php

namespace App\Helpers;

class ExpenseCategoryHelper
{
    /**
     * Category mappings with integer IDs
     */
    private static array $categoryMappings = [
        // Monthly Expense Categories (1-9)
        1 => 'salaries_hr',
        2 => 'office_rent',
        3 => 'utilities_bills',
        4 => 'software_subscriptions',
        5 => 'warehouse_storage',
        6 => 'marketing_retainer',
        7 => 'operations_admin',
        8 => 'maintenance',
        9 => 'other_fixed_costs',

        // Daily Expense Categories (10-17)
        10 => 'transportation',
        11 => 'marketing_ad_spend',
        12 => 'office_refreshments',
        13 => 'office_supplies',
        14 => 'vendor_payments',
        15 => 'emergency_expenses',
        16 => 'repair_small_maintenance',
        17 => 'other_expenses',
    ];

    /**
     * Category display names
     */
    private static array $categoryNames = [
        'salaries_hr' => 'Salaries & HR',
        'office_rent' => 'Office Rent',
        'utilities_bills' => 'Utilities & Bills',
        'software_subscriptions' => 'Software & Subscriptions',
        'warehouse_storage' => 'Warehouse / Storage',
        'marketing_retainer' => 'Marketing (Retainer)',
        'operations_admin' => 'Operations & Admin',
        'maintenance' => 'Maintenance',
        'other_fixed_costs' => 'Other Fixed Costs',
        'transportation' => 'Transportation',
        'marketing_ad_spend' => 'Marketing Ad Spend',
        'office_refreshments' => 'Office Refreshments',
        'office_supplies' => 'Office Supplies',
        'vendor_payments' => 'Vendor Payments',
        'emergency_expenses' => 'Emergency Expenses',
        'repair_small_maintenance' => 'Repair & Small Maintenance',
        'other_expenses' => 'Other Expenses',
    ];

    /**
     * Get all monthly expense categories
     */
    public static function getMonthlyCategories(): array
    {
        return array_intersect_key(
            self::$categoryNames,
            array_flip([
                'salaries_hr', 'office_rent', 'utilities_bills', 'software_subscriptions',
                'warehouse_storage', 'marketing_retainer', 'operations_admin',
                'maintenance', 'other_fixed_costs'
            ])
        );
    }

    /**
     * Get all daily expense categories
     */
    public static function getDailyCategories(): array
    {
        return array_intersect_key(
            self::$categoryNames,
            array_flip([
                'transportation', 'marketing_ad_spend', 'office_refreshments', 'office_supplies',
                'vendor_payments', 'emergency_expenses', 'repair_small_maintenance', 'other_expenses'
            ])
        );
    }

    /**
     * Get all categories (monthly + daily)
     */
    public static function getAllCategories(): array
    {
        return array_merge(
            self::getMonthlyCategories(),
            self::getDailyCategories()
        );
    }

    /**
     * Get categories formatted for dropdown/select
     */
    public static function getCategoriesForDropdown(): array
    {
        $allCategories = self::getAllCategories();
        $formatted = [];

        foreach ($allCategories as $key => $value) {
            $formatted[] = [
                'id' => $key,
                'name' => $value,
            ];
        }

        return $formatted;
    }

    /**
     * Get category name by key
     */
    public static function getCategoryName(string $key): ?string
    {
        $allCategories = self::getAllCategories();
        return $allCategories[$key] ?? null;
    }

    /**
     * Get category key by integer ID
     */
    public static function getCategoryKeyById(int $id): ?string
    {
        return self::$categoryMappings[$id] ?? null;
    }

    /**
     * Get category integer ID by key
     */
    public static function getCategoryIdByKey(string $key): ?int
    {
        $flipped = array_flip(self::$categoryMappings);
        return $flipped[$key] ?? null;
    }

    /**
     * Check if a category ID is valid
     */
    public static function isValidCategoryId(int $id): bool
    {
        return isset(self::$categoryMappings[$id]);
    }

    /**
     * Check if a category key exists (for backward compatibility)
     */
    public static function isValidCategory(string $key): bool
    {
        return isset(self::$categoryNames[$key]);
    }

    /**
     * Get monthly categories for dropdown (with integer IDs)
     */
    public static function getMonthlyCategoriesForDropdown(): array
    {
        $formatted = [];

        foreach (self::$categoryMappings as $id => $key) {
            if ($id >= 1 && $id <= 9 && isset(self::$categoryNames[$key])) {
                $formatted[] = [
                    'id' => $id,
                    'name' => self::$categoryNames[$key],
                ];
            }
        }

        return $formatted;
    }

    /**
     * Get daily categories for dropdown (with integer IDs)
     */
    public static function getDailyCategoriesForDropdown(): array
    {
        $formatted = [];

        foreach (self::$categoryMappings as $id => $key) {
            if ($id >= 10 && $id <= 17 && isset(self::$categoryNames[$key])) {
                $formatted[] = [
                    'id' => $id,
                    'name' => self::$categoryNames[$key],
                ];
            }
        }

        return $formatted;
    }
}