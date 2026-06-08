<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderGroup;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use App\Models\ProductVariation;
use Carbon\Carbon;

class OrdersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear existing orders
        OrderGroup::query()->delete();
        Order::query()->delete();
        OrderItem::query()->delete();

        // Get sample users and shops
        $users = User::where('user_type', 'customer')->take(10)->get();
        $shops = Shop::take(3)->get();
        $products = Product::all();
        $productVariations = ProductVariation::all();

        // If not enough users, create more using only the specified phone numbers
        if ($users->count() < 2) {
            $customerData = [
                ['name' => 'Customer 1', 'email' => 'customer1@epikcart.com', 'phone' => '01511240995'],
                ['name' => 'Customer 2', 'email' => 'customer2@epikcart.com', 'phone' => '01572797380'],
            ];
            $existingCount = $users->count();
            for ($i = $existingCount; $i < 2; $i++) {
                $customer = User::create([
                    'name' => $customerData[$i]['name'],
                    'email' => $customerData[$i]['email'],
                    'phone' => $customerData[$i]['phone'],
                    'password' => bcrypt('password'),
                    'user_type' => 'customer',
                    'email_or_otp_verified' => 1,
                ]);
                $users->push($customer);
            }
        }

        if ($users->isEmpty()) {
            // Create sample customers if none exists
            $customer1 = User::create([
                'name' => 'Customer 1',
                'email' => 'customer1@epikcart.com',
                'phone' => '01511240995',
                'password' => bcrypt('password'),
                'user_type' => 'customer',
                'email_or_otp_verified' => 1,
            ]);
            $customer2 = User::create([
                'name' => 'Customer 2',
                'email' => 'customer2@epikcart.com',
                'phone' => '01572797380',
                'password' => bcrypt('password'),
                'user_type' => 'customer',
                'email_or_otp_verified' => 1,
            ]);
            $users = collect([$customer1, $customer2]);
        }

        if ($shops->isEmpty()) {
            // Use the admin shop if no shops exist
            $shops = collect([Shop::find(1)]);
        }

        $deliveryStatuses = ['order_placed', 'confirmed', 'processing', 'shipped', 'delivered'];
        $paymentStatuses = ['paid', 'unpaid', 'pending'];
        $customerNames = ['Customer 1', 'Customer 2'];
        $customerPhones = ['01511240995', '01572797380'];

        // Create 100 order groups with sample orders and items
        for ($i = 1; $i <= 100; $i++) {
            $user = $users->random();
            $shop = $shops->random();
            $customerName = $customerNames[array_rand($customerNames)];
            $customerPhone = $customerPhones[array_rand($customerPhones)];

            // Create order group
            $orderGroup = OrderGroup::create([
                'user_id' => $user->id,
                'name' => $customerName,
                'email' => strtolower(str_replace(' ', '.', $customerName)) . '@example.com',
                'phone' => $customerPhone,
                'shipping_address_id' => 1, // Assuming address ID 1 exists
                'billing_address_id' => 1,
                'note' => $i % 3 == 0 ? 'Please deliver after 5 PM' : null,
                'is_pos_order' => 0,
                'created_at' => Carbon::now()->subDays(rand(1, 60))->subHours(rand(1, 23)),
                'updated_at' => Carbon::now()->subDays(rand(0, 30))->subHours(rand(1, 23)),
            ]);

            // Create 1-3 orders per order group
            $orderCount = rand(1, 3);
            for ($j = 1; $j <= $orderCount; $j++) {
                $deliveryStatus = $deliveryStatuses[array_rand($deliveryStatuses)];
                $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

                $amount = rand(25.00, 250.00);
                $taxAmount = $amount * 0.1; // 10% tax
                $shippingAmount = rand(0.00, 15.00);
                $discountAmount = rand(0.00, 20.00);
                $totalAmount = $amount + $taxAmount + $shippingAmount - $discountAmount;

                $order = Order::create([
                    'order_group_id' => $orderGroup->id,
                    'shop_id' => $shop->id,
                    'user_id' => $user->id,
                    'delivery_status' => $deliveryStatus,
                    'payment_status' => $paymentStatus,
                    'amount' => $amount,
                    'tax_amount' => $taxAmount,
                    'shipping_charge_amount' => $shippingAmount,
                    'discount_amount' => $discountAmount,
                    'total_amount' => $totalAmount,
                    'pickup_or_delivery' => 'delivery',
                    'created_at' => $orderGroup->created_at->addMinutes($j * 5),
                    'updated_at' => $orderGroup->updated_at->addMinutes($j * 2),
                ]);

                // Create 2-5 order items per order
                $itemCount = rand(2, 5);
                for ($k = 1; $k <= $itemCount; $k++) {
                    $quantity = rand(1, 3);
                    $unitPrice = rand(10.00, 100.00);
                    $totalTax = $unitPrice * $quantity * 0.1; // 10% tax
                    $totalDiscount = rand(0.00, 5.00);
                    $totalPrice = ($unitPrice * $quantity) + $totalTax - $totalDiscount;

                    // Get a random product variation from all available variations
                    $variation = $productVariations->random();
                    $variationId = $variation->id;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_variation_id' => $variationId,
                        'qty' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_tax' => $totalTax,
                        'total_discount' => $totalDiscount,
                        'total_price' => $totalPrice,
                        'created_at' => $order->created_at->addSeconds($k * 10),
                        'updated_at' => $order->updated_at->addSeconds($k * 5),
                    ]);
                }
            }
        }

        $this->command->info('Created 100 order groups with sample orders and items.');
    }
}