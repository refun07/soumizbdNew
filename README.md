# multivendor-ecommerce
A complete solution for multivendor eCommerce business!

## Installation and Run with Docker

Clone the repo and run docker compose:

```bash
git clone https://github.com/arm-technologies/multivendor-ecommerce.git
cd multivendor-ecommerce
sudo docker compose up
```


## Installation and Run (xampp/lampp must be installed on your device)

Clone the repo into htdocs or var/www/html:

```bash
git clone https://github.com/arm-technologies/multivendor-ecommerce.git
cd multivendor-ecommerce
```

Copy .env.example file and change database configurations:

```bash
cp .env.example .env
```

Run migration and seeder:

```bash
php artisan migrate --seed
```


# Test
./vendor/bin/phpunit --testsuite Unit





-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS = 0;
-- Translation tables
TRUNCATE TABLE product_translations;
TRUNCATE TABLE category_translations;
TRUNCATE TABLE brand_translations;
TRUNCATE TABLE unit_translations;
TRUNCATE TABLE badge_translations;
TRUNCATE TABLE variation_translations;
TRUNCATE TABLE variation_value_translations;
-- Pivot tables
TRUNCATE TABLE product_categories;
TRUNCATE TABLE product_tags;
TRUNCATE TABLE product_taxes;
TRUNCATE TABLE product_badges;
TRUNCATE TABLE category_variations;
-- Customer shopping data
TRUNCATE TABLE carts;
TRUNCATE TABLE wishlists;
TRUNCATE TABLE pos_carts;
TRUNCATE TABLE pos_cart_groups;
TRUNCATE TABLE stock_requests;
-- Reviews & ratings
TRUNCATE TABLE product_reviews;
-- Stock & inventory
TRUNCATE TABLE product_variation_stocks;
TRUNCATE TABLE stock_adjustment_product_variations;
TRUNCATE TABLE stock_transfer_product_variations;
TRUNCATE TABLE damage_stocks;
TRUNCATE TABLE stock_adjustments;
TRUNCATE TABLE stock_transfers;
TRUNCATE TABLE stocks;
-- Purchase orders
TRUNCATE TABLE purchase_order_product_variations;
TRUNCATE TABLE purchase_return_order_product_variations;
TRUNCATE TABLE purchase_order_payments;
TRUNCATE TABLE purchase_return_orders;
TRUNCATE TABLE purchase_orders;
TRUNCATE TABLE suppliers;
-- Marketing & campaigns
TRUNCATE TABLE campaign_products;
TRUNCATE TABLE coupon_products;
TRUNCATE TABLE coupon_categories;
TRUNCATE TABLE coupon_conditions;
TRUNCATE TABLE campaigns;
TRUNCATE TABLE coupons;
-- Product variations
TRUNCATE TABLE product_variation_combinations;
TRUNCATE TABLE product_variations;
-- Order history
TRUNCATE TABLE order_items;
TRUNCATE TABLE return_request_items;
TRUNCATE TABLE return_requests;
-- Core products
TRUNCATE TABLE products;
-- Classification & attributes (parent tables)
TRUNCATE TABLE variation_values;
TRUNCATE TABLE variations;
TRUNCATE TABLE taxes;
TRUNCATE TABLE tags;
TRUNCATE TABLE badges;
TRUNCATE TABLE units;
TRUNCATE TABLE brands;
TRUNCATE TABLE shipping_classes;
TRUNCATE TABLE categories;
-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;