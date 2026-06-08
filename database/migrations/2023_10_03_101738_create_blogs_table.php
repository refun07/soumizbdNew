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
        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('slug');
            $table->integer('blog_category_id')->nullable();
            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->text('thumbnail_image')->nullable();
            $table->longText('banner')->nullable();
            $table->string('video_provider')->default('youtube')->comment('youtube / vimeo / ...');
            $table->text('video_link')->nullable();
            $table->boolean('is_active')->default(1);
            $table->mediumText('meta_title')->nullable();
            $table->mediumText('meta_keywords')->nullable();
            $table->string('meta_image')->nullable();
            $table->longText('meta_description')->nullable();
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
        Schema::dropIfExists('blogs');
    }
};
