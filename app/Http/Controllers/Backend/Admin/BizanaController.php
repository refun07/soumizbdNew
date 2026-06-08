<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PurchaseOrder;
use App\Models\Accounting\RecurringExpense;
use App\Models\Accounting\Expense;
use App\Helpers\ExpenseCategoryHelper;
use App\Models\Accounting\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class BizanaController extends Controller
{
    /**
     * Display the main Bizana dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('backend.admin.bizana.index');
    }

    /**
     * Display the products analytics dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function products()
    {
        return view('backend.admin.bizana.products');
    }

    /**
     * Display the orders analytics dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function orders()
    {
        return view('backend.admin.bizana.orders');
    }

    /**
     * Display the daily expenses dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function expenses()
    {
        return view('backend.admin.bizana.expenses');
    }

    /**
     * Display the sales summary dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function sales()
    {
        return view('backend.admin.bizana.sales');
    }

    /**
     * Display the price calculator page.
     *
     * @return \Illuminate\View\View
     */
    public function priceCalculator()
    {
        return view('backend.admin.bizana.price-calculator');
    }

    /**
     * Display the delivery partners overview page.
     *
     * @return \Illuminate\View\View
     */
    public function deliveryPartners()
    {
        return view('backend.admin.bizana.delivery-partners');
    }

    /**
     * Display the daily expenses page.
     *
     * @return \Illuminate\View\View
     */
    public function dailyExpenses()
    {
        return view('backend.admin.bizana.daily-expenses');
    }

    /**
     * Display the monthly expenses page.
     *
     * @return \Illuminate\View\View
     */
    public function monthlyExpenses()
    {
        return view('backend.admin.bizana.monthly-expenses');
    }

    public function orderProfits()
    {
        return view('backend.admin.bizana.order-profits');
    }

    /**
     * Get products for price calculator (API endpoint for admin panel)
     */
    public function getProductsForPriceCalculator(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $categoryId = $request->get('category_id', '');
        $brandId = $request->get('brand_id', '');
        $purchaseOrderId = $request->get('purchase_order_id', '');
        $page = $request->get('page', 1);

        $products = Product::query()
            ->with(['productTranslations', 'categories', 'brand', 'purchaseOrderProductVariations' => function ($query) use ($purchaseOrderId) {
                if ($purchaseOrderId) {
                    $query->where('purchase_order_id', $purchaseOrderId);
                }
            }])
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhereHas('productTranslations', function ($q) use ($search) {
                          $q->where('name', 'like', '%' . $search . '%');
                      });
            })
            ->when($categoryId, function ($query) use ($categoryId) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            })
            ->when($brandId, function ($query) use ($brandId) {
                $query->where('brand_id', $brandId);
            })
            ->paginate($perPage, ['*'], 'page', $page);

        $products->getCollection()->transform(function ($product) use ($purchaseOrderId) {
            $purchaseCost = 0;

            if ($purchaseOrderId) {
                $poVariation = $product->purchaseOrderProductVariations->first();
                if ($poVariation) {
                    $purchaseCost = $poVariation->unit_price;
                }
            } else {
                $purchaseCost = $product->getAveragePurchaseCost();
            }

            return [
                'id' => $product->id,
                'name' => $product->collectTranslation('name'),
                'purchase_cost' => $purchaseCost,
                'packaging_cost' => (float)($product->packaging_cost ?? 0),
                'additional_costs' => (float)($product->additional_costs ?? 0),
            ];
        });

        return response()->json($products);
    }

    /**
     * Get filter options for price calculator (API endpoint for admin panel)
     */
    public function getFilterOptions(Request $request)
    {
        // Get categories
        $categories = Category::with(['categoryTranslations'])
            ->where('level', 0)
            ->orderBy('name')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->collectTranslation('name'),
                ];
            });

        // Get brands
        $brands = Brand::with(['brandTranslations'])
            ->isActive()
            ->orderBy('name')
            ->get()
            ->map(function ($brand) {
                return [
                    'id' => $brand->id,
                    'name' => $brand->collectTranslation('name'),
                ];
            });

        // Get purchase orders
        $purchaseOrders = PurchaseOrder::with(['supplier'])
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get()
            ->map(function ($po) {
                return [
                    'id' => $po->id,
                    'reference_code' => $po->reference_code,
                    'supplier_name' => $po->supplier ? $po->supplier->name : 'Unknown',
                ];
            });

        return response()->json([
            'categories' => $categories,
            'brands' => $brands,
            'purchase_orders' => $purchaseOrders,
        ]);
    }

    /**
     * Update packaging cost for a specific product
     */
    public function updatePackagingCost(Request $request, $id)
    {
        $request->validate([
            'packaging_cost' => 'required|numeric|min:0|max:999999.99',
        ]);

        $product = Product::findOrFail($id);
        $product->packaging_cost = $request->packaging_cost;
        $product->save();

        return response()->json(['success' => true]);
    }

    /**
     * Bulk update packaging costs for multiple products
     */
    public function bulkUpdatePackagingCost(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
            'packaging_cost' => 'required|numeric|min:0|max:999999.99',
        ]);

        $productIds = $request->product_ids;
        $packagingCost = $request->packaging_cost;

        // Update all products at once for better performance
        $updatedCount = Product::whereIn('id', $productIds)
            ->update(['packaging_cost' => $packagingCost]);

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Update additional costs for a specific product
     */
    public function updateAdditionalCost(Request $request, $id)
    {
        $request->validate([
            'additional_costs' => 'required|numeric|min:0|max:999999.99',
        ]);

        $product = Product::findOrFail($id);
        $product->additional_costs = $request->additional_costs;
        $product->save();

        return response()->json(['success' => true]);
    }

    /**
     * Bulk update additional costs for multiple products
     */
    public function bulkUpdateAdditionalCost(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'integer|exists:products,id',
            'additional_costs' => 'required|numeric|min:0|max:999999.99',
        ]);

        $productIds = $request->product_ids;
        $additionalCosts = $request->additional_costs;

        // Update all products at once for better performance
        $updatedCount = Product::whereIn('id', $productIds)
            ->update(['additional_costs' => $additionalCosts]);

        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount,
        ]);
    }

    /**
     * Get monthly expenses data for the admin panel
     */
    public function getMonthlyExpensesData(Request $request)
    {
        // Get monthly expenses data
        $monthlyExpenses = RecurringExpense::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'name' => $expense->name,
                    'amount' => (float) $expense->amount,
                    'currency' => $expense->currency ?? 'BDT',
                    'category' => [
                        'id' => $expense->category_id,
                        'name' => $expense->category_id ? ExpenseCategoryHelper::getCategoryName(ExpenseCategoryHelper::getCategoryKeyById($expense->category_id) ?: '') : 'N/A',
                    ],
                    'start_date' => $expense->start_date->format('Y-m-d'),
                    'description' => $expense->description,
                ];
            });

        // Get expense categories
        $expenseCategories = ExpenseCategoryHelper::getMonthlyCategoriesForDropdown();

        // Get current month and year
        $currentMonth = now()->format('F');
        $currentYear = now()->format('Y');

        return response()->json([
            'data' => $monthlyExpenses,
            'expenseCategories' => $expenseCategories,
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Store a new monthly expense
     */
    public function storeMonthlyExpense(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'description' => 'nullable|string',
            'category_id' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'start_date' => 'required|date',
        ]);

        try {
            // Validate that the category ID exists in our fixed categories
            if (!ExpenseCategoryHelper::isValidCategoryId($request->category_id)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid expense category selected. Valid categories are: ' . implode(', ', array_column(ExpenseCategoryHelper::getMonthlyCategoriesForDropdown(), 'name'))
                ], 422);
            }

            // Calculate next date - for monthly expenses, use the start date if it's today or future
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $nextDate = $startDate->gte(now()->startOfDay()) ? $startDate->toDateString() : $startDate->addMonth()->toDateString();

            $monthlyExpense = RecurringExpense::create([
                'reference_number' => $this->generateReferenceNumber('MEP'),
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'amount' => $request->amount,
                'currency' => $request->currency,
                'payment_method' => 'other',
                'start_date' => $request->start_date,
                'next_date' => $nextDate,
                'user_id' => auth()->id(),
                'recurrence_pattern' => 'monthly',
                'recurrence_interval' => 1,
                'is_active' => true,
                'auto_approve' => false,
                'is_tax_deductible' => true,
            ]);

            // Check if the template can generate an immediate expense and process it
            if ($monthlyExpense->canGenerate()) {
                $monthlyExpense->generateExpense();
            }

            return response()->json(['success' => true, 'message' => 'Monthly expense added successfully']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Error creating monthly expense", ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Error adding monthly expense: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update an existing monthly expense and sync to actual expenses
     */
    public function updateMonthlyExpense(Request $request, $id)
    {
        Log::info('=== MONTHLY EXPENSE UPDATE STARTED ===');
        Log::info('updateMonthlyExpense called', ['template_id' => $id, 'request_data' => $request->all()]);
        Log::info('Current user', ['user_id' => auth()->id(), 'user_email' => auth()->user() ? auth()->user()->email : 'null']);

        try {
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:200',
                'description' => 'nullable|string',
                'category_id' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|size:3',
                'start_date' => 'required|date',
                'update_current_month' => 'boolean',
                'update_previous_months' => 'boolean',
            ]);

            if ($validator->fails()) {
                Log::error('VALIDATION FAILED', ['errors' => $validator->errors()->toArray()]);
                return response()->json([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            Log::info('Validation passed successfully');

            // Validate that the category ID exists in our fixed monthly categories
            $categoryId = $request->category_id;
            Log::info('Validating category ID', ['category_id' => $categoryId]);

            if (!ExpenseCategoryHelper::isValidCategoryId($categoryId)) {
                Log::error('INVALID CATEGORY ID', [
                    'category_id' => $categoryId,
                    'valid_categories' => ExpenseCategoryHelper::getMonthlyCategoriesForDropdown()
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid expense category selected. Valid categories are: ' . implode(', ', array_column(ExpenseCategoryHelper::getMonthlyCategoriesForDropdown(), 'name'))
                ], 422);
            }

            // Get the template
            $monthlyExpense = RecurringExpense::findOrFail($id);
            Log::info('Found template', [
                'template_id' => $monthlyExpense->id,
                'template_name' => $monthlyExpense->name,
                'current_amount' => $monthlyExpense->amount
            ]);

            // Update the template
            $monthlyExpense->update([
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'amount' => (float) $request->amount,
                'currency' => $request->currency,
                'start_date' => $request->start_date,
            ]);

            Log::info('Template updated successfully', [
                'template_id' => $monthlyExpense->id,
                'new_amount' => $monthlyExpense->amount
            ]);

            // Sync to actual expenses
            $updateCurrentMonth = $request->boolean('update_current_month', true);
            $updatePreviousMonths = $request->boolean('update_previous_months', false);

            Log::info('Sync options', [
                'update_current_month' => $updateCurrentMonth,
                'update_previous_months' => $updatePreviousMonths
            ]);

            $actualExpensesUpdated = 0;

            if ($updateCurrentMonth || $updatePreviousMonths) {
                try {
                    // Query actual expenses for this template
                    $query = Expense::where('recurring_expense_template_id', '=', (int)$id);

                    // Filter by month if needed
                    if (!$updatePreviousMonths) {
                        $currentMonth = Carbon::now()->startOfMonth();
                        $endOfCurrentMonth = Carbon::now()->endOfMonth();
                        $query->whereBetween('expense_date', [$currentMonth, $endOfCurrentMonth]);
                        Log::info('Filtering to current month only', [
                            'from' => $currentMonth->toDateString(),
                            'to' => $endOfCurrentMonth->toDateString()
                        ]);
                    }

                    $matchingExpenses = $query->get();
                    $actualExpensesUpdated = $matchingExpenses->count();

                    Log::info('Found matching actual expenses', [
                        'count' => $actualExpensesUpdated,
                        'expense_ids' => $matchingExpenses->pluck('id')->toArray(),
                        'query_sql' => $query->toSql(),
                        'query_bindings' => $query->getBindings(),
                        'sample_expense' => $actualExpensesUpdated > 0 ? $matchingExpenses->first()->toArray() : null
                    ]);

                    if ($actualExpensesUpdated > 0) {
                        // Update the matching expenses
                        // Note: acc_expenses table uses 'description' field, not 'name'
                        $updateData = [
                            'description' => $request->description ?: $request->name,
                            'category_id' => $request->category_id,
                            'amount' => (float) $request->amount,
                            'currency' => $request->currency,
                        ];

                        $query->update($updateData);

                        Log::info('Actual expenses updated successfully', [
                            'count' => $actualExpensesUpdated,
                            'update_data' => $updateData
                        ]);
                    } else {
                        Log::info('No actual expenses found to update');
                    }

                } catch (\Exception $syncException) {
                    Log::error('Failed to sync actual expenses', [
                        'error' => $syncException->getMessage(),
                        'trace' => $syncException->getTraceAsString()
                    ]);

                    return response()->json([
                        'success' => false,
                        'error' => 'Template updated successfully, but failed to sync actual expenses: ' . $syncException->getMessage()
                    ], 500);
                }
            } else {
                Log::info('Sync disabled - not updating any actual expenses');
            }

            // Prepare success message
            $message = 'Monthly expense template updated successfully';
            if ($actualExpensesUpdated > 0) {
                $monthsText = $updatePreviousMonths ? 'all months' : 'current month';
                $message .= ". Updated {$actualExpensesUpdated} actual expense record(s) for {$monthsText}.";
            } else {
                $message .= '. No actual expenses were updated.';
            }

            Log::info('=== MONTHLY EXPENSE UPDATE COMPLETED SUCCESSFULLY ===');
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'template_updated' => true,
                    'actual_expenses_updated' => $actualExpensesUpdated
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $me) {
            Log::error('TEMPLATE NOT FOUND', ['template_id' => $id]);
            return response()->json([
                'success' => false,
                'error' => 'Expense template not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('GENERAL EXCEPTION UPDATING EXPENSE', [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => 'Error updating monthly expense: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Delete a monthly expense
     */
    public function deleteMonthlyExpense($id)
    {
        $monthlyExpense = RecurringExpense::findOrFail($id);

        try {
            $monthlyExpense->delete();
            return response()->json(['success' => true, 'message' => 'Monthly expense deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error deleting monthly expense: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get daily expenses data (placeholder for future implementation)
     */
    public function getDailyExpensesData(Request $request)
    {
        return response()->json([
            'data' => [],
            'expenseCategories' => [],
            'currentDate' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Store a daily expense (placeholder for future implementation)
     */
    public function storeDailyExpense(Request $request)
    {
        return response()->json(['success' => false, 'message' => 'Daily expenses feature coming soon'], 501);
    }

    /**
     * Update a daily expense (placeholder for future implementation)
     */
    public function updateDailyExpense(Request $request, $id)
    {
        return response()->json(['success' => false, 'message' => 'Daily expenses feature coming soon'], 501);
    }

    /**
     * Delete a daily expense (placeholder for future implementation)
     */
    public function deleteDailyExpense($id)
    {
        return response()->json(['success' => false, 'message' => 'Daily expenses feature coming soon'], 501);
    }

    /**
     * Generate reference number for expenses
     */
    private function generateReferenceNumber($prefix = 'MEP')
    {
        $date = now()->format('ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));

        return "{$prefix}-{$date}-{$random}";
    }
}
