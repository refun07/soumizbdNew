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
        Schema::create('media_files', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->nullable();
            $table->longText('media_file')->nullable();
            $table->integer('media_size')->nullable();
            $table->string('media_type')->nullable()->comment('video / image / pdf / ...');
            $table->text('media_name')->nullable();
            $table->string('media_extension')->nullable();
            $table->longText('alt_text')->nullable();
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
        Schema::dropIfExists('media_files');
    }
};
