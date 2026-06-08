<?php

namespace App\Jobs\Accounting;

use App\Models\Accounting\RecurringExpense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessRecurringExpensesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [60, 300, 900];

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Processing recurring expenses job started');

        $monthlyExpenses = RecurringExpense::active()
            ->due()
            ->notExpired()
            ->get();

        $processedCount = 0;
        $errorCount = 0;

        foreach ($monthlyExpenses as $monthlyExpense) {
            try {
                if ($monthlyExpense->canGenerate()) {
                    $expense = $monthlyExpense->generateExpense();
                    if ($expense) {
                        $processedCount++;
                        Log::info("Generated expense {$expense->reference_number} from monthly expense {$monthlyExpense->reference_number}");
                    }
                }
            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Failed to generate expense from monthly expense {$monthlyExpense->reference_number}: " . $e->getMessage(), [
                    'recurring_expense_template_id' => $monthlyExpense->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        Log::info("Processing recurring expenses job completed. Processed: {$processedCount}, Errors: {$errorCount}");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Processing recurring expenses job failed: ' . $exception->getMessage(), [
            'exception' => $exception,
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
