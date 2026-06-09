<?php

use App\Models\Zone;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['Inside Dhaka', 'Outside Dhaka'] as $name) {
            Zone::withTrashed()->updateOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'is_active' => 1,
                    'deleted_at' => null,
                ]
            );
        }

        Schema::table('user_addresses', function (Blueprint $table) {
            $table->integer('state_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('user_addresses', function (Blueprint $table) {
            $table->integer('state_id')->nullable(false)->change();
        });
    }
};
