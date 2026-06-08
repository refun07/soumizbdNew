<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->integer('role_id')->nullable();
            $table->string('user_type')->default('customer');
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('email_or_otp_verified')->default(0);
            $table->string('verification_code')->nullable();
            $table->string('new_email_verification_code')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('provider_id')->nullable();
            $table->string('avatar')->nullable();
            $table->string('postal_code')->nullable();
            $table->double('user_balance')->default('0');
            $table->boolean('is_banned')->default(0);
            $table->integer('shop_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();
            $table->integer('updated_by')->nullable();
            $table->softDeletes();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
