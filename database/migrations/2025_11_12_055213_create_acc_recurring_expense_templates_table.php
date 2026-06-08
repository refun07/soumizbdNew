<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * WHY WE NEED THIS TABLE:
     * The Recurring Expense Templates table stores expense templates that will
     * automatically generate expense records based on various recurrence patterns.
     * This enables businesses to automate recurring expenses with flexibility for
     * different scheduling needs (weekly, bi-weekly, monthly, quarterly, yearly).
     * The system will automatically create actual expense records based on these
     * templates when the scheduled job runs.
     *
     * WHEN THIS TABLE IS USED:
     * - When setting up recurring expenses with any pattern (rent, utilities, subscriptions)
     * - For automated expense generation by the ProcessRecurringExpenses job
     * - When managing and monitoring all active recurring expense templates
     * - For budgeting and forecasting recurring costs
     * - When disabling or modifying recurring expense templates
     */
    public function up(): void
    {
        Schema::create('acc_recurring_expense_templates', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 50)->unique(); // Unique reference for template
            $table->string('name', 200); // Name of the recurring expense
            $table->text('description')->nullable(); // Description of the expense
            $table->integer('category_id'); // Category ID (1-17 for different expense types)
            $table->decimal('amount', 15, 2); // Recurring amount
            $table->string('currency', 3)->default('USD'); // Currency
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'debit_card', 'check', 'other'])->default('bank_transfer');
            $table->string('payment_reference', 200)->nullable(); // Default payment reference pattern
            $table->foreignId('vendor_id')->nullable(); // Default vendor
            $table->foreignId('user_id')->constrained(); // Who created the template

            // Recurrence configuration
            $table->enum('recurrence_pattern', ['daily', 'weekly', 'bi_weekly', 'monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->integer('recurrence_interval')->default(1); // Every X units (every 2 weeks, every 3 months, etc.)
            $table->integer('day_of_month')->nullable(); // Day of month for monthly (1-31)
            $table->integer('day_of_week')->nullable(); // Day of week for weekly (0=Sunday, 6=Saturday)
            $table->integer('week_of_month')->nullable(); // Week of month for "first Friday" style (1-5)

            $table->date('start_date'); // When the recurring expense starts
            $table->date('end_date')->nullable(); // When it ends (null = indefinite)
            $table->date('next_date'); // Next date to generate expense

            // Configuration
            $table->boolean('is_active')->default(true); // Whether it's currently active
            $table->boolean('auto_approve')->default(false); // Auto-approve generated expenses
            $table->boolean('is_tax_deductible')->default(true);
            $table->decimal('tax_amount', 15, 2)->default(0); // Tax amount if applicable
            $table->json('tags')->nullable(); // Tags for generated expenses
            $table->text('notes')->nullable(); // Notes for generated expenses

            // Tracking
            $table->integer('total_generated')->default(0); // Count of expenses generated
            $table->timestamp('last_generated_at')->nullable(); // When last expense was generated
            $table->timestamps();

            $table->index(['is_active', 'next_date'], 'acc_rec_exp_temp_active_next');
            $table->index(['reference_number'], 'acc_rec_exp_temp_ref');
            $table->index(['category_id', 'is_active'], 'acc_rec_exp_temp_cat_active');
            $table->index(['recurrence_pattern', 'is_active'], 'acc_rec_exp_temp_pattern_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_recurring_expense_templates');
    }
};
