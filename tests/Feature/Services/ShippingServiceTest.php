<?php

namespace Tests\Feature\Services;

use App\Models\City;
use App\Models\Coupon;
use App\Models\Shop;
use App\Models\ShippingClass;
use App\Models\User;
use App\Models\Zone;
use App\Models\ZoneShippingCharge;
use App\Services\ShippingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->country = \App\Models\Country::create([
            'name' => 'Test Country',
            'code' => 'TC',
        ]);

        $this->state = \App\Models\State::create([
            'name' => 'Test State',
            'country_id' => $this->country->id,
        ]);

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->shop1 = Shop::create([
            'user_id' => $this->user->id,
            'name' => 'Test Shop 1',
            'slug' => 'test-shop-1',
            'default_shipping_charge' => 10.00,
        ]);

        $this->shop2 = Shop::create([
            'user_id' => $this->user->id,
            'name' => 'Test Shop 2',
            'slug' => 'test-shop-2',
            'default_shipping_charge' => 15.00,
        ]);

        $this->shippingClass1 = ShippingClass::create([
            'name' => 'Standard Shipping',
        ]);

        $this->shippingClass2 = ShippingClass::create([
            'name' => 'Express Shipping',
        ]);

        $this->zone = Zone::create([
            'name' => 'Test Zone',
        ]);

        $this->city = City::create([
            'name' => 'Test City',
            'zone_id' => $this->zone->id,
            'state_id' => $this->state->id,
        ]);

        ZoneShippingCharge::create([
            'shop_id' => $this->shop1->id,
            'zone_id' => $this->zone->id,
            'shipping_class_id' => null,
            'charge' => 5.00,
            'mrate' => null,
        ]);

        ZoneShippingCharge::create([
            'shop_id' => $this->shop1->id,
            'zone_id' => $this->zone->id,
            'shipping_class_id' => $this->shippingClass1->id,
            'charge' => 3.00,
            'mrate' => null,
        ]);

        ZoneShippingCharge::create([
            'shop_id' => $this->shop1->id,
            'zone_id' => $this->zone->id,
            'shipping_class_id' => $this->shippingClass2->id,
            'charge' => 7.00,
            'mrate' => 2,
        ]);

        ZoneShippingCharge::create([
            'shop_id' => $this->shop2->id,
            'zone_id' => $this->zone->id,
            'shipping_class_id' => null,
            'charge' => 8.00,
            'mrate' => null,
        ]);
    }

    public function test_empty_cart_returns_zero(): void
    {
        $result = ShippingService::calculate($this->city->id, []);

        $this->assertEquals(0.0, $result);
    }

    public function test_single_item_mrate_disabled_returns_charge_once(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => null, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(5.00, $result);
    }

    public function test_quantity_less_than_mrate_returns_single_batch(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(7.00, $result);
    }

    public function test_quantity_equals_mrate_returns_single_batch(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 2],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(7.00, $result);
    }

    public function test_quantity_greater_than_mrate_returns_multiple_batches(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 5],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(21.00, $result);
    }

    public function test_multiple_products_same_class_sums_quantities(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 1],
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(7.00, $result);
    }

    public function test_multiple_products_same_class_exceeds_mrate(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 2],
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 2],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(14.00, $result);
    }

    public function test_multiple_classes_with_different_mrates(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass1->id, 'quantity' => 1],
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 3],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(17.00, $result);
    }

    public function test_mixed_enabled_and_disabled_mrate_classes(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass1->id, 'quantity' => 1],
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(10.00, $result);
    }

    public function test_fallback_to_default_charge_when_no_zone_charge(): void
    {
        $newShop = Shop::create([
            'user_id' => $this->user->id,
            'name' => 'New Shop',
            'slug' => 'new-shop',
            'default_shipping_charge' => 12.00,
        ]);

        $newCity = City::create([
            'name' => 'New City',
            'zone_id' => $this->zone->id,
            'state_id' => $this->state->id,
        ]);

        $cartItems = [
            ['shop_id' => $newShop->id, 'shipping_class_id' => null, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($newCity->id, $cartItems);

        $this->assertEquals(12.00, $result);
    }

    public function test_zero_mrate_treated_as_disabled(): void
    {
        $shippingClass3 = ShippingClass::create([
            'name' => 'Free Shipping',
        ]);

        ZoneShippingCharge::create([
            'shop_id' => $this->shop1->id,
            'zone_id' => $this->zone->id,
            'shipping_class_id' => $shippingClass3->id,
            'charge' => 4.00,
            'mrate' => 0,
        ]);

        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $shippingClass3->id, 'quantity' => 10],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(4.00, $result);
    }

    public function test_free_shipping_coupon_skips_charge(): void
    {
        Coupon::create([
            'shop_id' => $this->shop1->id,
            'code' => 'FREESHIP',
            'discount_type' => 'percentage',
            'is_free_shipping' => true,
        ]);

        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => null, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems, ['FREESHIP']);

        $this->assertEquals(0.0, $result);
    }

    public function test_free_shipping_coupon_only_affects_matching_shop(): void
    {
        Coupon::create([
            'shop_id' => $this->shop1->id,
            'code' => 'FREESHIP1',
            'discount_type' => 'percentage',
            'is_free_shipping' => true,
        ]);

        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => null, 'quantity' => 1],
            ['shop_id' => $this->shop2->id, 'shipping_class_id' => null, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems, ['FREESHIP1']);

        $this->assertEquals(8.00, $result);
    }

    public function test_multiple_shops_with_different_charges(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => null, 'quantity' => 1],
            ['shop_id' => $this->shop2->id, 'shipping_class_id' => null, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(13.00, $result);
    }

    public function test_null_shipping_class_uses_null_class_charge(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => null, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(5.00, $result);
    }

    public function test_null_shipping_class_with_mrate(): void
    {
        ZoneShippingCharge::where('shop_id', $this->shop1->id)
            ->where('zone_id', $this->zone->id)
            ->whereNull('shipping_class_id')
            ->update(['mrate' => 3, 'charge' => 6.00]);

        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => null, 'quantity' => 7],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(18.00, $result);
    }

    public function test_missing_quantity_defaults_to_one(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => null],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(5.00, $result);
    }

    public function test_large_quantity_with_small_mrate(): void
    {
        $shippingClass4 = ShippingClass::create([
            'name' => 'Overnight Shipping',
        ]);

        ZoneShippingCharge::create([
            'shop_id' => $this->shop1->id,
            'zone_id' => $this->zone->id,
            'shipping_class_id' => $shippingClass4->id,
            'charge' => 2.50,
            'mrate' => 1,
        ]);

        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $shippingClass4->id, 'quantity' => 100],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(250.00, $result);
    }

    public function test_combined_mrate_across_multiple_items_same_class(): void
    {
        $cartItems = [
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 1],
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 1],
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 1],
            ['shop_id' => $this->shop1->id, 'shipping_class_id' => $this->shippingClass2->id, 'quantity' => 1],
        ];

        $result = ShippingService::calculate($this->city->id, $cartItems);

        $this->assertEquals(14.00, $result);
    }
}
