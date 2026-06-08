<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $table = 'acc_expenses';

    protected $fillable = [
        'reference_number',
        'category_id',
        'description',
        'amount',
        'currency',
        'exchange_rate',
        'expense_date',
        'payment_method',
        'payment_reference',
        'vendor_id',
        'user_id',
        'status',
        'approved_by',
        'approved_at',
        'notes',
        'receipts',
        'tags',
        'is_recurring',
        'recurrence_pattern',
        'recurring_expense_template_id',
        'is_tax_deductible',
        'tax_amount',
        'journal_entry_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'exchange_rate' => 'decimal:6',
        'expense_date' => 'date',
        'approved_at' => 'datetime',
        'receipts' => 'array',
        'tags' => 'array',
        'is_recurring' => 'boolean',
        'is_tax_deductible' => 'boolean',
        'tax_amount' => 'decimal:2',
    ];

    
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function recurringExpenseTemplate()
    {
        return $this->belongsTo(RecurringExpense::class, 'recurring_expense_template_id');
    }

    // Backward compatibility
    public function monthlyExpense()
    {
        return $this->recurringExpenseTemplate();
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function scopeRegular($query)
    {
        return $query->where('is_recurring', false);
    }

    public function scopeRecurringInstances($query)
    {
        return $query->whereNotNull('recurring_expense_template_id');
    }
}