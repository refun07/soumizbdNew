<?php

namespace App\Models\Accounting;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RecurringExpense extends Model
{
    use HasFactory;

    protected $table = 'acc_recurring_expense_templates';

    protected $fillable = [
        'reference_number',
        'name',
        'description',
        'category_id',
        'amount',
        'currency',
        'payment_method',
        'payment_reference',
        'vendor_id',
        'user_id',
        'recurrence_pattern',
        'recurrence_interval',
        'day_of_month',
        'day_of_week',
        'week_of_month',
        'start_date',
        'end_date',
        'next_date',
        'is_active',
        'auto_approve',
        'is_tax_deductible',
        'tax_amount',
        'tags',
        'notes',
        'total_generated',
        'last_generated_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'next_date' => 'date',
        'is_active' => 'boolean',
        'auto_approve' => 'boolean',
        'is_tax_deductible' => 'boolean',
        'tax_amount' => 'decimal:2',
        'tags' => 'array',
        'last_generated_at' => 'datetime',
        'total_generated' => 'integer',
    ];

    
  
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'recurring_expense_template_id');
    }

    // Scopes for querying
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDue($query)
    {
        return $query->where('next_date', '<=', now()->toDateString());
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('end_date')
              ->orWhere('end_date', '>=', now()->toDateString());
        });
    }

    public function scopeByPattern($query, $pattern)
    {
        return $query->where('recurrence_pattern', $pattern);
    }

    // Main method to generate the next expense instance
    public function generateExpense()
    {
        return DB::transaction(function () {
            // Create the actual expense
            $expense = Expense::create([
                'reference_number' => $this->generateExpenseReference(),
                'category_id' => $this->category_id,
                'description' => $this->description ?: $this->name,
                'amount' => $this->amount,
                'currency' => $this->currency,
                'expense_date' => $this->next_date,
                'payment_method' => $this->payment_method,
                'payment_reference' => $this->payment_reference,
                'vendor_id' => $this->vendor_id,
                'user_id' => $this->user_id,
                'status' => $this->auto_approve ? 'approved' : 'submitted',
                'approved_by' => $this->auto_approve ? $this->user_id : null,
                'approved_at' => $this->auto_approve ? now() : null,
                'notes' => $this->notes,
                'tags' => $this->tags,
                'is_recurring' => true,
                'recurrence_pattern' => $this->recurrence_pattern,
                'recurring_expense_template_id' => $this->id,
                'is_tax_deductible' => $this->is_tax_deductible,
                'tax_amount' => $this->tax_amount ?? 0,
            ]);

            // Calculate the next occurrence date
            $nextDate = $this->calculateNextDate();

            // Update the monthly expense template
            $this->update([
                'total_generated' => $this->total_generated + 1,
                'last_generated_at' => now(),
                'next_date' => $nextDate,
                'is_active' => $nextDate !== null, // Deactivate if no more next dates
            ]);

  
            return $expense;
        });
    }

    // Check if this template can generate a new expense
    public function canGenerate()
    {
        // Convert everything to Carbon objects for proper comparison
        $nextDate = $this->next_date ? \Carbon\Carbon::parse($this->next_date) : null;
        $today = now()->startOfDay();

        Log::debug('Next date: ' . ($nextDate ? $nextDate->toDateTimeString() : 'null'));
        Log::debug('Today: ' . $today->toDateTimeString());
        Log::debug('Is active: ' . ($this->is_active ? 'true' : 'false'));

        return $this->is_active &&
               $nextDate &&
               $nextDate->lte($today) &&
               (!$this->end_date || $nextDate->lte(\Carbon\Carbon::parse($this->end_date)));
    }

    // Calculate the next recurrence date based on the pattern
    public function calculateNextDate()
    {
        if (!$this->next_date) {
            return null;
        }

        $currentDate = Carbon::parse($this->next_date);

        switch ($this->recurrence_pattern) {
            case 'daily':
                return $this->calculateNextDaily($currentDate);
            case 'weekly':
                return $this->calculateNextWeekly($currentDate);
            case 'bi_weekly':
                return $this->calculateNextBiWeekly($currentDate);
            case 'monthly':
                return $this->calculateNextMonthly($currentDate);
            case 'quarterly':
                return $this->calculateNextQuarterly($currentDate);
            case 'yearly':
                return $this->calculateNextYearly($currentDate);
            default:
                return null;
        }
    }

    private function calculateNextDaily($currentDate)
    {
        $nextDate = $currentDate->addDays($this->recurrence_interval);
        return $this->checkEndDate($nextDate);
    }

    private function calculateNextWeekly($currentDate)
    {
        if ($this->day_of_week !== null) {
            // Go to the specified day of the week
            $nextDate = $currentDate->copy()->next($this->day_of_week);
            if ($nextDate->lte($currentDate)) {
                $nextDate->addWeek();
            }
        } else {
            $nextDate = $currentDate->addWeeks($this->recurrence_interval);
        }

        return $this->checkEndDate($nextDate);
    }

    private function calculateNextBiWeekly($currentDate)
    {
        $interval = max(2, $this->recurrence_interval);
        $nextDate = $currentDate->addWeeks($interval);
        return $this->checkEndDate($nextDate);
    }

    private function calculateNextMonthly($currentDate)
    {
        if ($this->day_of_month) {
            // Specific day of month
            $nextDate = $currentDate->copy()->addMonths($this->recurrence_interval);
            $maxDay = $nextDate->daysInMonth;
            $actualDay = min($this->day_of_month, $maxDay);
            $nextDate->day($actualDay);
        } else if ($this->day_of_week !== null && $this->week_of_month) {
            // "Second Friday" style
            $nextDate = $currentDate->copy()->addMonths($this->recurrence_interval);
            $nextDate->firstOfMonth($this->day_of_week);
            if ($this->week_of_month > 1) {
                $nextDate->addWeeks($this->week_of_month - 1);
            }
        } else {
            // Same day as current date
            $nextDate = $currentDate->addMonths($this->recurrence_interval);
        }

        return $this->checkEndDate($nextDate);
    }

    private function calculateNextQuarterly($currentDate)
    {
        $interval = max(1, $this->recurrence_interval);
        $nextDate = $currentDate->copy()->addQuarters($interval);

        if ($this->day_of_month) {
            $maxDay = $nextDate->daysInMonth;
            $actualDay = min($this->day_of_month, $maxDay);
            $nextDate->day($actualDay);
        }

        return $this->checkEndDate($nextDate);
    }

    private function calculateNextYearly($currentDate)
    {
        $nextDate = $currentDate->copy()->addYears($this->recurrence_interval);

        if ($this->day_of_month && $this->day_of_week !== null) {
            // "Third Friday of June" style
            $nextDate->month($this->day_of_month)->firstOfMonth($this->day_of_week);
            if ($this->week_of_month > 1) {
                $nextDate->addWeeks($this->week_of_month - 1);
            }
        } else if ($this->day_of_month) {
            // Specific day of specific month
            $nextDate->month($this->day_of_month);
            $maxDay = $nextDate->daysInMonth;
            $actualDay = min($this->day_of_month, $maxDay);
            $nextDate->day($actualDay);
        }

        return $this->checkEndDate($nextDate);
    }

    private function checkEndDate($nextDate)
    {
        if ($this->end_date && $nextDate->gt($this->end_date)) {
            // Disable this monthly expense since it's completed
            $this->update(['is_active' => false]);
            return null;
        }

        return $nextDate->toDateString();
    }

    // Get human-readable pattern description
    public function getPatternDescription()
    {
        $descriptions = [
            'daily' => 'Every {interval} day(s)',
            'weekly' => 'Every {interval} week(s)',
            'bi_weekly' => 'Every {interval} bi-week(s)',
            'monthly' => 'Every {interval} month(s)',
            'quarterly' => 'Every {interval} quarter(s)',
            'yearly' => 'Every {interval} year(s)',
        ];

        $description = $descriptions[$this->recurrence_pattern] ?? 'Unknown';
        return str_replace('{interval}', $this->recurrence_interval, $description);
    }

    // Initialize the next_date based on start_date and pattern
    public function initializeNextDate()
    {
        if (!$this->start_date) {
            return;
        }

        $startDate = Carbon::parse($this->start_date);

        // Set next_date to start_date initially, then calculate the first actual recurrence
        $this->next_date = $this->calculateFirstRecurrence($startDate);
        $this->save();
    }

    private function calculateFirstRecurrence($startDate)
    {
        // For the first recurrence, we use the start date itself if it's today or future,
        // otherwise calculate the next occurrence
        if ($startDate->gte(now()->startOfDay())) {
            return $startDate->toDateString();
        }

        // If start date is in the past, calculate the next occurrence
        switch ($this->recurrence_pattern) {
            case 'daily':
                return now()->addDays($this->recurrence_interval)->toDateString();
            case 'weekly':
                return $this->calculateNextWeekly(now());
            case 'bi_weekly':
                return $this->calculateNextBiWeekly(now());
            case 'monthly':
                return $this->calculateNextMonthly(now());
            case 'quarterly':
                return $this->calculateNextQuarterly(now());
            case 'yearly':
                return $this->calculateNextYearly(now());
            default:
                return $startDate->toDateString();
        }
    }

    private function generateExpenseReference()
    {
        $prefix = 'EXP';
        $date = now()->format('ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));

        return "{$prefix}-{$date}-{$random}";
    }
}
