<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * WHY WE NEED THIS TABLE:
     * The Expenses table stores all expense records (both regular and recurring instances)
     * with complete details including receipts, approval workflows, and payment tracking.
     * This enables comprehensive expense management for daily/monthly cost tracking,
     * tax deduction optimization, and budget control. Essential for businesses to
     * monitor and control spending.
     *
     * WHEN THIS TABLE IS USED:
     * - When employees submit expense reports and receipts
     * - For regular (non-recurring) expense tracking
     * - For recurring expense instances generated from templates
     * - During expense approval processes (submitted → approved → paid)
     * - When generating expense reports by category, date, or template
     * - For tax preparation and identifying deductible business expenses
     * - When ExpenseCreated event fires and creates automatic journal entries
     * - Budget vs actual expense variance analysis
     */
    public function up(): void
    {
        Schema::create('acc_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 50)->unique(); // Expense reference number
            $table->integer('category_id'); // Category ID (1-17 for different expense types)
            $table->string('description', 500); // Expense description
            $table->decimal('amount', 15, 2); // Expense amount
            $table->string('currency', 3)->default('USD'); // Currency
            $table->decimal('exchange_rate', 10, 6)->default(1); // Exchange rate if not base currency
            $table->date('expense_date'); // Date of expense
            $table->enum('payment_method', ['cash', 'bank_transfer', 'credit_card', 'debit_card', 'check', 'other'])->default('bank_transfer');
            $table->string('payment_reference', 200)->nullable(); // Payment reference/check number
            $table->foreignId('vendor_id')->nullable(); // Reference to vendor/supplier
            $table->foreignId('user_id')->constrained(); // Who created expense
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'paid'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users', 'id'); // Who approved
            $table->timestamp('approved_at')->nullable(); // Approval timestamp
            $table->text('notes')->nullable(); // Additional notes
            $table->json('receipts')->nullable(); // Array of receipt file paths
            $table->json('tags')->nullable(); // Tags for categorization
            $table->boolean('is_recurring')->default(false); // Flag for recurring instances
            $table->string('recurrence_pattern', 50)->nullable(); // Pattern from template for reference
            $table->foreignId('recurring_expense_template_id')->nullable()->constrained('acc_recurring_expense_templates'); // Link to recurring template
            $table->boolean('is_tax_deductible')->default(true);
            $table->decimal('tax_amount', 15, 2)->default(0); // Tax portion if applicable
            $table->timestamps();

            $table->index(['reference_number', 'expense_date']);
            $table->index(['category_id', 'status']);
            $table->index(['expense_date', 'amount']);
            $table->index('recurring_expense_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('acc_expenses');
    }
};
