<?php

namespace App\Http\Controllers\Backend\Admin\Bizana;

use App\Http\Controllers\Controller;
use App\Models\Accounting\RecurringExpense;
use App\Models\Accounting\Expense;
use App\Models\Accounting\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class RecurringExpenseController extends Controller
{
    public function __construct()
    {
        // Permissions for recurring expense management
        $this->middleware(['permission:bizana_view_accounting'])->only(['index', 'dailyExpensesPage']);
        $this->middleware(['permission:bizana_manage_accounting'])->only(['store', 'update', 'destroy', 'storeDailyExpense', 'updateDailyExpense', 'destroyDailyExpense', 'toggleStatus', 'generateNow']);
    }

  
    /**
     * Original comprehensive monthly expenses index
     */
    public function index(Request $request)
    {
        $query = RecurringExpense::with(['category', 'chartAccount', 'user', 'expenses' => function ($q) {
            $q->latest()->take(5);
        }]);

        // Search and filter
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('reference_number', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->status) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->recurrence_pattern) {
            $query->byPattern($request->recurrence_pattern);
        }

        $monthlyExpenses = $query->orderBy('next_date')->paginate(20);

        // Transform data for frontend
        $monthlyExpenses->getCollection()->transform(function ($expense) {
            return [
                'id' => $expense->id,
                'reference_number' => $expense->reference_number,
                'name' => $expense->name,
                'description' => $expense->description,
                'amount' => $expense->amount,
                'currency' => $expense->currency,
                'category' => $expense->category ? [
                    'id' => $expense->category->id,
                    'name' => $expense->category->name,
                ] : null,
                'recurrence_pattern' => $expense->recurrence_pattern,
                'recurrence_interval' => $expense->recurrence_interval,
                'pattern_description' => $expense->getPatternDescription(),
                'start_date' => $expense->start_date->format('Y-m-d'),
                'end_date' => $expense->end_date?->format('Y-m-d'),
                'next_date' => $expense->next_date?->format('Y-m-d'),
                'day_of_month' => $expense->day_of_month,
                'day_of_week' => $expense->day_of_week,
                'week_of_month' => $expense->week_of_month,
                'is_active' => $expense->is_active,
                'auto_approve' => $expense->auto_approve,
                'total_generated' => $expense->total_generated,
                'last_generated_at' => $expense->last_generated_at?->format('Y-m-d H:i:s'),
                'created_at' => $expense->created_at->format('Y-m-d H:i:s'),
                'status' => $expense->is_active ? 'Active' : 'Inactive',
                'recent_instances' => $expense->expenses->map(function ($instance) {
                    return [
                        'id' => $instance->id,
                        'reference_number' => $instance->reference_number,
                        'expense_date' => $instance->expense_date->format('Y-m-d'),
                        'amount' => $instance->amount,
                        'status' => $instance->status,
                    ];
                }),
            ];
        });

        // Get filter options
        $categories = ExpenseCategory::where('is_active', true)->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
            ];
        });

        // Get pattern options
        $patternOptions = [
            ['value' => 'daily', 'label' => 'Daily'],
            ['value' => 'weekly', 'label' => 'Weekly'],
            ['value' => 'bi_weekly', 'label' => 'Bi-Weekly'],
            ['value' => 'monthly', 'label' => 'Monthly'],
            ['value' => 'quarterly', 'label' => 'Quarterly'],
            ['value' => 'yearly', 'label' => 'Yearly'],
        ];

        // Get summary statistics
        $summary = [
            'total' => RecurringExpense::count(),
            'active' => RecurringExpense::active()->count(),
            'inactive' => RecurringExpense::where('is_active', false)->count(),
            'due_today' => RecurringExpense::active()->due()->count(),
            'monthly_total' => RecurringExpense::active()->sum('amount'),
        ];

        return Inertia::render('accounting/MonthlyExpenses', [
            'monthlyExpenses' => $monthlyExpenses,
            'filters' => [
                'search' => $request->search ?? '',
                'status' => $request->status ?? '',
                'category_id' => $request->category_id ?? '',
                'recurrence_pattern' => $request->recurrence_pattern ?? '',
            ],
            'filterOptions' => [
                'categories' => $categories,
                'patterns' => $patternOptions,
            ],
            'summary' => $summary,
        ]);
    }

    
    /**
     * Comprehensive recurring expense store
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,check,other',
            'payment_reference' => 'nullable|string|max:200',
            'vendor_id' => 'nullable|exists:users,id',
            'recurrence_pattern' => 'required|in:daily,weekly,bi_weekly,monthly,quarterly,yearly',
            'recurrence_interval' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'day_of_month' => 'nullable|integer|min:1|max:31',
            'day_of_week' => 'nullable|integer|min:0|max:6',
            'week_of_month' => 'nullable|integer|min:1|max:5',
            'is_active' => 'boolean',
            'auto_approve' => 'boolean',
            'is_tax_deductible' => 'boolean',
            'tax_amount' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Validate that the category ID exists in our fixed categories
            $allCategories = array_merge(
                ExpenseCategoryHelper::getMonthlyCategories(),
                ExpenseCategoryHelper::getDailyCategories()
            );
            $validCategoryIds = [];
            foreach (ExpenseCategoryHelper::$categoryMappings as $id => $key) {
                if (isset($allCategories[$key])) {
                    $validCategoryIds[] = $id;
                }
            }

            if (!in_array($request->category_id, $validCategoryIds)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid expense category selected'
                ], 422);
            }

            // Convert integer ID to string key for database storage
            $categoryKey = ExpenseCategoryHelper::getCategoryKeyById($request->category_id);
            if (!$categoryKey) {
                return response()->json(['success' => false, 'error' => 'Failed to resolve category'], 422);
            }

            // Calculate initial next date based on start date
          $startDate = \Carbon\Carbon::parse($request->start_date);
          $nextDate = $startDate->gte(now()->startOfDay()) ? $startDate->toDateString() : $startDate->copy();

          $monthlyExpense = RecurringExpense::create([
                'reference_number' => $this->generateReferenceNumber('REC'),
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $categoryKey,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'vendor_id' => $request->vendor_id,
                'user_id' => auth()->id(),
                'recurrence_pattern' => $request->recurrence_pattern,
                'recurrence_interval' => $request->recurrence_interval,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'day_of_month' => $request->day_of_month,
                'day_of_week' => $request->day_of_week,
                'week_of_month' => $request->week_of_month,
                'is_active' => $request->is_active ?? true,
                'auto_approve' => $request->auto_approve ?? false,
                'is_tax_deductible' => $request->is_tax_deductible ?? true,
                'tax_amount' => $request->tax_amount ?? 0,
                'tags' => $request->tags,
                'notes' => $request->notes,
                'next_date' => $nextDate,
            ]);

            DB::commit();

            // Check if the template can generate an immediate expense and process it
            if ($monthlyExpense->canGenerate()) {
                $monthlyExpense->generateExpense();
            }

            return redirect()->back()->with('success', 'Recurring expense template created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error creating recurring expense: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $monthlyExpense = RecurringExpense::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'payment_method' => 'required|in:cash,bank_transfer,credit_card,debit_card,check,other',
            'payment_reference' => 'nullable|string|max:200',
            'vendor_id' => 'nullable|exists:users,id',
            'recurrence_pattern' => 'required|in:daily,weekly,bi_weekly,monthly,quarterly,yearly',
            'recurrence_interval' => 'required|integer|min:1',
            'end_date' => 'nullable|date|after:start_date',
            'day_of_month' => 'nullable|integer|min:1|max:31',
            'day_of_week' => 'nullable|integer|min:0|max:6',
            'week_of_month' => 'nullable|integer|min:1|max:5',
            'is_active' => 'boolean',
            'auto_approve' => 'boolean',
            'is_tax_deductible' => 'boolean',
            'tax_amount' => 'nullable|numeric|min:0',
            'tags' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $monthlyExpense->update([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'payment_method' => $request->payment_method,
                'payment_reference' => $request->payment_reference,
                'vendor_id' => $request->vendor_id,
                'recurrence_pattern' => $request->recurrence_pattern,
                'recurrence_interval' => $request->recurrence_interval,
                'end_date' => $request->end_date,
                'day_of_month' => $request->day_of_month,
                'day_of_week' => $request->day_of_week,
                'week_of_month' => $request->week_of_month,
                'is_active' => $request->is_active,
                'auto_approve' => $request->auto_approve,
                'is_tax_deductible' => $request->is_tax_deductible,
                'tax_amount' => $request->tax_amount,
                'tags' => $request->tags,
                'notes' => $request->notes,
            ]);

            // Recalculate next date if pattern changed
          if ($monthlyExpense->wasChanged(['recurrence_pattern', 'recurrence_interval', 'day_of_month', 'day_of_week', 'week_of_month', 'start_date'])) {
                // Calculate new next date based on updated pattern
                $startDate = \Carbon\Carbon::parse($monthlyExpense->start_date);
                $nextDate = $monthlyExpense->calculateFirstRecurrence($startDate);
                $monthlyExpense->update(['next_date' => $nextDate]);
            }

            DB::commit();

            return redirect()->back()->with('success', 'Recurring expense template updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating recurring expense: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $monthlyExpense = RecurringExpense::findOrFail($id);

        try {
            // Check if there are any generated expenses
            $generatedExpenses = $monthlyExpense->expenses()->count();

            if ($generatedExpenses > 0) {
                return redirect()->back()->with('error', 'Cannot delete recurring expense template that has generated expenses. Please deactivate it instead.');
            }

            $monthlyExpense->delete();

            return redirect()->back()->with('success', 'Recurring expense template deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting recurring expense: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $monthlyExpense = RecurringExpense::findOrFail($id);

        try {
            $monthlyExpense->update([
                'is_active' => !$monthlyExpense->is_active
            ]);

            $status = $monthlyExpense->is_active ? 'activated' : 'deactivated';
            return redirect()->back()->with('success', "Recurring expense template {$status} successfully");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating recurring expense status: ' . $e->getMessage());
        }
    }

    public function generateNow($id)
    {
        $monthlyExpense = RecurringExpense::findOrFail($id);

        try {
            if ($monthlyExpense->canGenerate()) {
                $expense = $monthlyExpense->generateExpense();
                return redirect()->back()->with('success', "Expense {$expense->reference_number} generated successfully");
            } else {
                return redirect()->back()->with('error', 'Cannot generate expense from this recurring expense template at this time');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error generating expense: ' . $e->getMessage());
        }
    }

    /**
     * Daily Expenses Page
     */
    public function dailyExpensesPage(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $categoryId = $request->get('category_id', '');

        // Get daily expenses within date range
        $dailyExpensesQuery = Expense::with(['category'])
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc');

        // Filter by category if specified
        if ($categoryId) {
            $dailyExpensesQuery->where('category_id', $categoryId);
        }

        $dailyExpenses = $dailyExpensesQuery->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'description' => $expense->description,
                    'amount' => (float) $expense->amount,
                    'currency' => $expense->currency ?? 'BDT',
                    'date' => $expense->expense_date->format('Y-m-d'),
                    'category' => [
                        'id' => $expense->category->id,
                        'name' => $expense->category->name,
                    ],
                    'created_at' => $expense->created_at->toISOString(),
                ];
            });

        // Get expense categories
        $expenseCategories = ExpenseCategoryHelper::getCategoriesForDropdown();

        return Inertia::render('expenses/DailyExpensesPage', [
            'dailyExpenses' => [
                'data' => $dailyExpenses,
                'total' => $dailyExpenses->sum('amount'),
            ],
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'category_id' => $categoryId,
            ],
            'filterOptions' => [
                'expenseCategories' => $expenseCategories,
            ],
        ]);
    }

    /**
     * Store a new daily expense
     */
    public function storeDailyExpense(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'category_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'date' => 'required|date|before_or_equal:today',
        ]);

        try {
            DB::beginTransaction();

            $category = ExpenseCategory::findOrFail($request->category_id);

            $expense = Expense::create([
                'reference_number' => $this->generateReferenceNumber('EXP'),
                'description' => $request->description,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'expense_date' => $request->date,
                'user_id' => auth()->id(),
                'status' => 'approved', // Auto-approve daily expenses
                'is_tax_deductible' => true,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Daily expense added successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error adding daily expense: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error adding daily expense: ' . $e->getMessage());
        }
    }

    /**
     * Update a daily expense
     */
    public function updateDailyExpense(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'category_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'date' => 'required|date|before_or_equal:today',
        ]);

        try {
            DB::beginTransaction();

            $expense = Expense::findOrFail($id);
            $category = ExpenseCategory::findOrFail($request->category_id);

            $expense->update([
                'description' => $request->description,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'expense_date' => $request->date,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Daily expense updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating daily expense: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error updating daily expense: ' . $e->getMessage());
        }
    }

    /**
     * Delete a daily expense
     */
    public function destroyDailyExpense($id)
    {
        try {
            $expense = Expense::findOrFail($id);

            // Only allow deletion of expenses that haven't been reconciled
            // Add additional business logic here if needed

            $expense->delete();

            return redirect()->back()->with('success', 'Daily expense deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting daily expense: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error deleting daily expense: ' . $e->getMessage());
        }
    }

    private function generateReferenceNumber($prefix = 'REC')
    {
        $date = now()->format('ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));

        return "{$prefix}-{$date}-{$random}";
    }
}
