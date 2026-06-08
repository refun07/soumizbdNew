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
        Schema::create('website_landings', function (Blueprint $table) {
            $table->id();
            $table->string('title');                   // Page title
            $table->string('slug')->unique();          // URL slug (e.g., /landing/summer-sale)
            $table->text('description')->nullable();   // Internal description

            // Workflow
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('selected_products')->nullable();
            $table->timestamp('published_at')->nullable();

            // SEO Data (JSON)
            $table->json('seo_data')->nullable(); // JSON: meta_title, meta_description, meta_keywords, meta_image, og_title, og_description, og_image, twitter_title, twitter_description, twitter_image, canonical_url

            // Page Configuration
            $table->json('page_configs')->nullable(); // JSON: global styles (background, fonts, etc.)

            // Audit Trail
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('slug');
            $table->index('status');
            $table->index('published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('website_landings');
    }
};
