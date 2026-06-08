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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('assigned_to')->nullable()->after('role_id');
            $table->dateTime('reorder_reminder_date')->nullable()->after('assigned_to');
            $table->dateTime('next_follow_up_date')->nullable()->after('reorder_reminder_date');
            $table->longText('status_note')->nullable()->after('next_follow_up_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['assigned_to', 'reorder_reminder_date', 'next_follow_up_date', 'status_note']);
        });
    }
};
