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
        Schema::create('website_landing_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('landing_id');   // FK to website_landings
            $table->integer('order')->default(0);       // Display order
            $table->string('type');                     // Component type

            // Component Data (JSON)
            $table->json('section_data');           // Main component configuration
            $table->json('responsive_data')->nullable(); // JSON: mobile/tablet/desktop configs

            // Visibility
            $table->boolean('is_visible')->default(1);
            $table->timestamp('visible_from')->nullable();
            $table->timestamp('visible_until')->nullable();

            // Audit Trail
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['landing_id', 'order']);
            $table->index('type');
            $table->index('is_visible');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_landing_sections');
    }
};
