<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        // Delete records from the table
        \DB::table('permissions')->delete();

        // Reset the auto-increment counter to 1
        \DB::statement('ALTER TABLE permissions AUTO_INCREMENT = 1');

        \DB::table('permissions')->insert(array(

            array('name' => 'show_dashboard', 'group_name' => 'dashboard', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'show_notifications', 'group_name' => 'dashboard', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'clear_cache', 'group_name' => 'dashboard', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'search', 'group_name' => 'dashboard', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'view_orders', 'group_name' => 'orders', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'manage_assigned_orders', 'group_name' => 'orders', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'manage_orders', 'group_name' => 'orders', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'pos_orders', 'group_name' => 'orders', 'guard_name' => 'web', 'format' => 'col'),

            // must be ordered in this format -> view, create, edit, delete for ( 'format' => 'row' )
            array('name' => 'view_products', 'group_name' => 'products', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_products', 'group_name' => 'products', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_products', 'group_name' => 'products', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_products', 'group_name' => 'products', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_categories', 'group_name' => 'categories', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_categories', 'group_name' => 'categories', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_categories', 'group_name' => 'categories', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_categories', 'group_name' => 'categories', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_variations', 'group_name' => 'variations', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_variations', 'group_name' => 'variations', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_variations', 'group_name' => 'variations', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_variations', 'group_name' => 'variations', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_variation_values', 'group_name' => 'variation_values', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_variation_values', 'group_name' => 'variation_values', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_variation_values', 'group_name' => 'variation_values', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_variation_values', 'group_name' => 'variation_values', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_brands', 'group_name' => 'brands', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_brands', 'group_name' => 'brands', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_brands', 'group_name' => 'brands', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_brands', 'group_name' => 'brands', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_units', 'group_name' => 'units', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_units', 'group_name' => 'units', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_units', 'group_name' => 'units', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_units', 'group_name' => 'units', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_taxes', 'group_name' => 'taxes', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_taxes', 'group_name' => 'taxes', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_taxes', 'group_name' => 'taxes', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_taxes', 'group_name' => 'taxes', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_badges', 'group_name' => 'badges', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_badges', 'group_name' => 'badges', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_badges', 'group_name' => 'badges', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_badges', 'group_name' => 'badges', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_purchase_orders', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_purchase_orders', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_purchase_orders', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_return_purchase_orders', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_return_purchase_orders', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'make_purchase_order_payments', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'show_purchase_order_payments', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_stock_adjustment', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_stock_adjustment', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'show_adjustment_details', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_stock_transfer', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_stock_transfer', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'show_stock_transfer_details', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_stock_report', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_damage_stocks', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_damage_stocks', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'delete_damage_stocks', 'group_name' => 'inventory', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'view_suppliers', 'group_name' => 'suppliers', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_suppliers', 'group_name' => 'suppliers', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_suppliers', 'group_name' => 'suppliers', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_suppliers', 'group_name' => 'suppliers', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_customers', 'group_name' => 'customers', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_customers', 'group_name' => 'customers', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'delete_customers', 'group_name' => 'customers', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'view_sellers', 'group_name' => 'sellers', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_sellers', 'group_name' => 'sellers', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'pay_sellers', 'group_name' => 'sellers', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'show_payouts', 'group_name' => 'sellers', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'show_payout_requests', 'group_name' => 'sellers', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'show_earning_histories', 'group_name' => 'sellers', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'view_tags', 'group_name' => 'tags', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_tags', 'group_name' => 'tags', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_tags', 'group_name' => 'tags', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_tags', 'group_name' => 'tags', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_coupons', 'group_name' => 'coupons', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_coupons', 'group_name' => 'coupons', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_coupons', 'group_name' => 'coupons', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_coupons', 'group_name' => 'coupons', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_campaigns', 'group_name' => 'campaigns', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_campaigns', 'group_name' => 'campaigns', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_campaigns', 'group_name' => 'campaigns', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_campaigns', 'group_name' => 'campaigns', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_countries', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_countries', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_states', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_states', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_states', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'delete_states', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_cities', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_cities', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_cities', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'delete_cities', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_areas', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_areas', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_areas', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'delete_areas', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_zones', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_zones', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_zones', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'delete_zones', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'view_warehouses', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'create_warehouses', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_warehouses', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'delete_warehouses', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'delivery_charges', 'group_name' => 'shipping', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'file_manager', 'group_name' => 'file_manager', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'conversation', 'group_name' => 'conversation', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'view_home_sections', 'group_name' => 'shop_settings', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'add_home_sections', 'group_name' => 'shop_settings', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'edit_home_sections', 'group_name' => 'shop_settings', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'delete_home_sections', 'group_name' => 'shop_settings', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'configurations', 'group_name' => 'shop_settings', 'guard_name' => 'web', 'format' => 'col'),


            array('name' => 'view_staffs', 'group_name' => 'staffs', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_staffs', 'group_name' => 'staffs', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_staffs', 'group_name' => 'staffs', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_staffs', 'group_name' => 'staffs', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_roles_and_permissions', 'group_name' => 'roles_and_permissions', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_roles_and_permissions', 'group_name' => 'roles_and_permissions', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_roles_and_permissions', 'group_name' => 'roles_and_permissions', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_roles_and_permissions', 'group_name' => 'roles_and_permissions', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_pages', 'group_name' => 'pages', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_pages', 'group_name' => 'pages', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_pages', 'group_name' => 'pages', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_pages', 'group_name' => 'pages', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'homepage', 'group_name' => 'website_setup', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'color_and_branding', 'group_name' => 'website_setup', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'general_settings', 'group_name' => 'configurations', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'order_settings', 'group_name' => 'configurations', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'smtp_settings', 'group_name' => 'configurations', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'payment_methods', 'group_name' => 'configurations', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'social_media_login', 'group_name' => 'configurations', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'view_languages', 'group_name' => 'languages', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_languages', 'group_name' => 'languages', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_languages', 'group_name' => 'languages', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_languages', 'group_name' => 'languages', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_subscribers', 'group_name' => 'promotions', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'send_newsletters', 'group_name' => 'promotions', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'bizana_view', 'group_name' => 'bizana', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'bizana_view_products', 'group_name' => 'bizana', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'bizana_view_accounting', 'group_name' => 'bizana', 'guard_name' => 'web', 'format' => 'col'),
            array('name' => 'bizana_view_profits', 'group_name' => 'bizana', 'guard_name' => 'web', 'format' => 'col'),

            array('name' => 'view_blocked_ips', 'group_name' => 'security', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'create_blocked_ips', 'group_name' => 'security', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_blocked_ips', 'group_name' => 'security', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'delete_blocked_ips', 'group_name' => 'security', 'guard_name' => 'web', 'format' => 'row'),

            array('name' => 'view_agent_commissions', 'group_name' => 'agent_commissions', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'pay_agent_commissions', 'group_name' => 'agent_commissions', 'guard_name' => 'web', 'format' => 'row'),
            array('name' => 'edit_agent_commission_rate', 'group_name' => 'agent_commissions', 'guard_name' => 'web', 'format' => 'row'),
        ));

        // Assign permissions to Staff role (id 2)
        $staffPermissions = [
            1, 2, 4,                    // dashboard: show_dashboard, show_notifications, search
            5, 6, 8,                    // orders: view_orders, manage_assigned_orders, pos_orders
            9,                          // products: view_products
            13,                         // categories: view_categories
            62,                         // customers: view_customers
            41, 48, 51, 54, 55,         // inventory: view_purchase_orders, view_stock_adjustment, view_stock_transfer, view_stock_report, view_damage_stocks
            58,                         // suppliers: view_suppliers
            107,                        // conversation
            145, 146,                   // agent_commissions: view_agent_commissions, pay_agent_commissions
        ];

        \DB::table('role_has_permissions')->delete();
        foreach ($staffPermissions as $permissionId) {
            \DB::table('role_has_permissions')->insert([
                'permission_id' => $permissionId,
                'role_id' => 2,
            ]);
        }

        // Assign permissions to Moderator role (id 3)
        $moderatorPermissions = [5]; // orders: view_orders
        foreach ($moderatorPermissions as $permissionId) {
            \DB::table('role_has_permissions')->insert([
                'permission_id' => $permissionId,
                'role_id' => 3,
            ]);
        }

        // Assign Staff role to staff user (id 3)
        \DB::table('model_has_roles')->insert([
            'role_id' => 2,
            'model_type' => 'App\\Models\\User',
            'model_id' => 3,
        ]);

        // Assign Moderator role to moderator user (id 4)
        \DB::table('model_has_roles')->insert([
            'role_id' => 3,
            'model_type' => 'App\\Models\\User',
            'model_id' => 4,
        ]);
    }
}
