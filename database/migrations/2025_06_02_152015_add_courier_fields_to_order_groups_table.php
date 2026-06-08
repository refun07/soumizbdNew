<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCourierFieldsToOrderGroupsTable extends Migration
{
    public function up(): void
    {
        Schema::table('order_groups', function (Blueprint $table) {
            $table->string('courier_partner')->nullable()->after('shipping_address_id'); // 'redx', 'pathao', 'steadfast', etc.
            $table->string('tracking_number')->nullable()->after('courier_partner');
            $table->string('delivery_status')->nullable()->after('tracking_number'); // e.g., 'order_placed', 'shipped', 'delivered'
            $table->json('courier_response')->nullable()->after('delivery_status'); // store full courier API response
            $table->text('delivery_logs')->nullable()->after('courier_response'); // optional log notes or tracking updates
        });
    }

    public function down(): void
    {
        Schema::table('order_groups', function (Blueprint $table) {
            $table->dropColumn([
                'courier_partner',
                'tracking_number',
                'delivery_status',
                'courier_response',
                'delivery_logs',
            ]);
        });
    }
}
