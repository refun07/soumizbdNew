<?php

namespace App\Http\Controllers\Backend\Admin\Bizana;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Expense;
use App\Helpers\ExpenseCategoryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DailyExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:bizana_manage_accounting'])->only(['store', 'update', 'destroy']);
    }

    /**
     * Get expenses data for API requests
     */
    public function getExpensesData(Request $request)
    {
        try {
            // Get filters from request
            $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));
            $categoryId = $request->get('category_id', '');
            $page = $request->get('page', 1);

            // Get daily expenses with filters - only non-recurring expenses
            $dailyExpensesQuery = Expense::regular()
                ->whereBetween('expense_date', [$startDate, $endDate]);

            // Apply category filter if provided
            if ($categoryId) {
                $dailyExpensesQuery->where('category_id', $categoryId);
            }

            // Paginate the results - 15 items per page
            $dailyExpenses = $dailyExpensesQuery->orderBy('expense_date', 'desc')
                ->paginate(15, ['*'], 'page', $page)
                ->through(function ($expense) {
                    return [
                        'id' => $expense->id,
                        'date' => $expense->expense_date->format('Y-m-d'),
                        'description' => $expense->description,
                        'category' => $expense->category_id ? ExpenseCategoryHelper::getCategoryName(ExpenseCategoryHelper::getCategoryKeyById($expense->category_id) ?: '') : 'N/A',
                        'category_id' => $expense->category_id,
                        'amount' => number_format($expense->amount, 2),
                        'currency' => $expense->currency,
                        'status' => $expense->status,
                        'reference_number' => $expense->reference_number,
                    ];
                });

            // Calculate total amount for all filtered expenses (not just current page)
            $totalAmountQuery = Expense::regular()
                ->whereBetween('expense_date', [$startDate, $endDate]);

            if ($categoryId) {
                $totalAmountQuery->where('category_id', $categoryId);
            }

            $totalAmount = $totalAmountQuery->sum('amount');

            // Get categories for dropdown
            $categories = ExpenseCategoryHelper::getDailyCategoriesForDropdown();

            return response()->json([
                'data' => $dailyExpenses->items(),
                'dailyExpenses' => $dailyExpenses->items(),
                'categories' => $categories,
                'expenseCategories' => $categories,
                'filters' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'category_id' => $categoryId,
                ],
                'totalAmount' => number_format($totalAmount, 2),
                'pagination' => [
                    'current_page' => $dailyExpenses->currentPage(),
                    'last_page' => $dailyExpenses->lastPage(),
                    'per_page' => $dailyExpenses->perPage(),
                    'total' => $dailyExpenses->total(),
                    'from' => $dailyExpenses->firstItem(),
                    'to' => $dailyExpenses->lastItem(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching daily expenses data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch daily expenses data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get expense categories for dropdown
     */
    public function getCategories(Request $request)
    {
        try {
            $categories = ExpenseCategoryHelper::getDailyCategoriesForDropdown();

            return response()->json([
                'data' => $categories,
                'success' => true
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching expense categories: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch expense categories',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the daily expenses page
     */
    public function index(Request $request)
    {
        // Get filters from request
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $categoryId = $request->get('category_id', '');

        // Get expense categories for filter dropdown
        $categories = ExpenseCategoryHelper::getDailyCategoriesForDropdown();

        // Get daily expenses with filters - only non-recurring expenses
        $dailyExpensesQuery = Expense::regular()
            ->whereBetween('expense_date', [$startDate, $endDate])
            ->orderBy('expense_date', 'desc');

        // Apply category filter if provided
        if ($categoryId) {
            $dailyExpensesQuery->where('category_id', $categoryId);
        }

        // Paginate the results - 15 items per page
        $dailyExpenses = $dailyExpensesQuery->paginate(15)->through(function ($expense) {
            return [
                'id' => $expense->id,
                'date' => $expense->expense_date->format('Y-m-d'),
                'description' => $expense->description,
                'category' => $expense->category_id ? ExpenseCategoryHelper::getCategoryName(ExpenseCategoryHelper::getCategoryKeyById($expense->category_id) ?: '') : 'N/A',
                'category_id' => $expense->category_id,
                'amount' => number_format($expense->amount, 2),
                'currency' => $expense->currency,
                'status' => $expense->status,
                'reference_number' => $expense->reference_number,
            ];
        });

        // Calculate total amount for all filtered expenses (not just current page)
        $totalAmountQuery = Expense::regular()
            ->whereBetween('expense_date', [$startDate, $endDate]);

        if ($categoryId) {
            $totalAmountQuery->where('category_id', $categoryId);
        }

        $totalAmount = $totalAmountQuery->sum('amount');

        return inertia()->render('expenses/DailyExpensesPage', [
            'dailyExpenses' => $dailyExpenses->items(),
            'categories' => $categories,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'category_id' => $categoryId,
            ],
            'totalAmount' => number_format($totalAmount, 2),
            'expenseCategories' => $categories,
            'pagination' => [
                'current_page' => $dailyExpenses->currentPage(),
                'last_page' => $dailyExpenses->lastPage(),
                'per_page' => $dailyExpenses->perPage(),
                'total' => $dailyExpenses->total(),
                'from' => $dailyExpenses->firstItem(),
                'to' => $dailyExpenses->lastItem(),
            ],
        ]);
    }

    /**
     * Store a new daily expense
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'description' => 'required|string|max:255',
                'category_id' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|size:3',
                'date' => 'required|date|before_or_equal:today',
            ]);

            // Validate that the category ID exists in our fixed categories
            $categoryId = $request->category_id;
            if (!ExpenseCategoryHelper::isValidCategoryId($categoryId)) {
                return response()->json(['success' => false, 'error' => 'Invalid expense category selected'], 422);
            }

            $expense = Expense::create([
                'reference_number' => $this->generateUniqueReferenceNumber(),
                'description' => $request->description,
                'category_id' => $categoryId,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'expense_date' => $request->date,
                'user_id' => auth()->id(),
                'status' => 'approved', // Auto-approve daily expenses
                'is_tax_deductible' => true,
            ]);

            return response()->json(['success' => true, 'message' => 'Daily expense added successfully']);
        } catch (\Exception $e) {
            Log::error('Error adding daily expense: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error adding daily expense: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update a daily expense
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'category_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'date' => 'required|date|before_or_equal:today',
        ]);

        // Validate that the category ID exists in our fixed categories
        $categoryId = $request->category_id;
        if (!ExpenseCategoryHelper::isValidCategoryId($categoryId)) {
            return response()->json(['success' => false, 'error' => 'Invalid expense category selected'], 422);
        }

        try {
            DB::beginTransaction();

            $expense = Expense::findOrFail($id);

            $expense->update([
                'description' => $request->description,
                'category_id' => $categoryId,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'expense_date' => $request->date,
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Daily expense updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating daily expense: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error updating daily expense: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a daily expense
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $expense = Expense::findOrFail($id);
            $expense->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Daily expense deleted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting daily expense: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'Error deleting daily expense: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Generate a unique reference number
     */
    private function generateUniqueReferenceNumber()
    {
        // Generate a random string - no race conditions, no database queries needed
        return 'EXP-' . strtoupper(uniqid() . '-' . substr(md5(mt_rand()), 0, 4));
    }
}
