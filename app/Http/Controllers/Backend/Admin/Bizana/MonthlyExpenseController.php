<?php

namespace App\Http\Controllers\Backend\Admin\Bizana;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\PurchaseOrder;
use App\Models\Accounting\RecurringExpense;
use App\Models\Accounting\Expense;
use App\Helpers\ExpenseCategoryHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Carbon\Carbon;

class MonthlyExpenseController extends Controller
{
    public function __construct()
    {
        // Permission middleware for monthly expense operations
        $this->middleware(['permission:bizana_view_products'])->only(['priceCalculator']);
        $this->middleware(['permission:bizana_view_accounting'])->only(['monthlyExpensePage', 'getMonthlyExpensesData']);
        $this->middleware(['permission:bizana_manage_accounting'])->only(['storeExpense', 'updateExpense', 'destroyExpense']);
    }

    /**
     * Monthly Expense Page with Price Calculator
     */
    public function monthlyExpensePage(Request $request)
    {
        // Get price calculator data
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $categoryId = $request->get('category_id', '');
        $brandId = $request->get('brand_id', '');
        $purchaseOrderId = $request->get('purchase_order_id', '');

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
            ->paginate($perPage);

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

        // Get filter options for price calculator
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

        // Get monthly expenses data
        $monthlyExpenses = RecurringExpense::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'name' => $expense->name,
                    'amount' => (float) $expense->amount,
                    'currency' => $expense->currency ?? 'BDT',
                    'category' => [
                        'id' => $expense->category_id,
                        'name' => $expense->category_id ? ExpenseCategoryHelper::getCategoryName($expense->category_id) : 'N/A',
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

        return Inertia::render('expenses/RecurringExpensePage', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'purchase_order_id' => $purchaseOrderId,
            ],
            'filterOptions' => [
                'categories' => $categories,
                'brands' => $brands,
                'purchase_orders' => $purchaseOrders,
                'expenseCategories' => $expenseCategories,
            ],
            'monthlyExpenses' => [
                'data' => $monthlyExpenses,
                'total' => $monthlyExpenses->sum('amount'),
            ],
            'currentMonth' => $currentMonth,
            'currentYear' => $currentYear,
        ]);
    }

    /**
     * Get monthly expenses data for API endpoint
     */
    public function getMonthlyExpensesData(Request $request)
    {
        // Get monthly expenses data
        $monthlyExpenses = RecurringExpense::where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(20)
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
     * Price Calculator method
     */
    public function priceCalculator(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');
        $categoryId = $request->get('category_id', '');
        $brandId = $request->get('brand_id', '');
        $purchaseOrderId = $request->get('purchase_order_id', '');

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
            ->paginate($perPage);

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

        return Inertia::render('products/PriceCalculator', [
            'products' => $products,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage,
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'purchase_order_id' => $purchaseOrderId,
            ],
            'filterOptions' => [
                'categories' => $categories,
                'brands' => $brands,
                'purchase_orders' => $purchaseOrders,
            ],
        ]);
    }

    /**
     * Simple expense store for Monthly Expense Page
     */
    public function storeExpense(Request $request)
    {
        Log::info('=== MONTHLY EXPENSE CREATION STARTED ===');
        Log::info('storeExpense called', ['request_data' => $request->all()]);
        Log::info('Request headers', ['headers' => $request->headers->all()]);
        Log::info('Current user', ['user_id' => auth()->id(), 'user_email' => auth()->user() ? auth()->user()->email : 'null']);

        try {
            $validator = \Validator::make($request->all(), [
                'name' => 'required|string|max:200',
                'description' => 'nullable|string',
                'category_id' => 'required|integer|min:1',
                'amount' => 'required|numeric|min:0',
                'currency' => 'required|string|size:3',
                'start_date' => 'required|date',
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

            // Validate that the category ID exists in our fixed categories
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

            // Calculate next date - for monthly expenses, use the start date if it's today or future
            $startDate = \Carbon\Carbon::parse($request->start_date);
            $nextDate = $startDate->gte(now()->startOfDay()) ? $startDate->toDateString() : $startDate->addMonth()->toDateString();

            Log::info('Date calculation', [
                'start_date' => $request->start_date,
                'parsed_start_date' => $startDate->toDateString(),
                'today' => now()->startOfDay()->toDateString(),
                'next_date' => $nextDate
            ]);

            $referenceNumber = $this->generateReferenceNumber('MEP');
            Log::info('Generated reference number', ['reference_number' => $referenceNumber]);

            $expenseData = [
                'reference_number' => $referenceNumber,
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $categoryId,
                'amount' => (float) $request->amount,
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
            ];

            Log::info('About to create RecurringExpense with data', $expenseData);

            $monthlyExpense = RecurringExpense::create($expenseData);

            if (!$monthlyExpense) {
                Log::error('FAILED TO CREATE RecurringExpense - create() returned false');
                return response()->json(['success' => false, 'error' => 'Failed to create expense record'], 500);
            }

            Log::info('RecurringExpense created successfully', [
                'id' => $monthlyExpense->id,
                'reference_number' => $monthlyExpense->reference_number,
                'name' => $monthlyExpense->name,
                'amount' => $monthlyExpense->amount
            ]);

            // Check if the template can generate an immediate expense and process it
            try {
                if (method_exists($monthlyExpense, 'canGenerate') && $monthlyExpense->canGenerate()) {
                    Log::info('Generating immediate expense');
                    if (method_exists($monthlyExpense, 'generateExpense')) {
                        $monthlyExpense->generateExpense();
                        Log::info('Immediate expense generated successfully');
                    } else {
                        Log::warning('generateExpense method not found');
                    }
                }
            } catch (\Exception $genEx) {
                Log::warning('Failed to generate immediate expense', [
                    'error' => $genEx->getMessage(),
                    'trace' => $genEx->getTraceAsString()
                ]);
            }

            Log::info('=== MONTHLY EXPENSE CREATION COMPLETED SUCCESSFULLY ===');
            return response()->json(['success' => true, 'message' => 'Monthly expense added successfully']);

        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::error('VALIDATION EXCEPTION', [
                'error' => $ve->getMessage(),
                'errors' => $ve->errors()->toArray(),
                'trace' => $ve->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Validation error: ' . $ve->getMessage(),
                'errors' => $ve->errors()
            ], 422);

        } catch (\Illuminate\Database\QueryException $qe) {
            Log::error('DATABASE QUERY EXCEPTION', [
                'error' => $qe->getMessage(),
                'sql' => $qe->getSql(),
                'bindings' => $qe->getBindings(),
                'trace' => $qe->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => 'Database error: ' . $qe->getMessage()], 500);

        } catch (\Exception $e) {
            Log::error('GENERAL EXCEPTION CREATING EXPENSE', [
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => 'Error adding monthly expense: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update expense template and sync to actual expenses
     */
    public function updateExpense(Request $request, $id)
    {
        Log::info('=== MONTHLY EXPENSE UPDATE STARTED ===');
        Log::info('updateExpense called', ['template_id' => $id, 'request_data' => $request->all()]);
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
                        $updateData = [
                            'name' => $request->name,
                            'description' => $request->description,
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

        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::error('VALIDATION EXCEPTION', [
                'error' => $ve->getMessage(),
                'errors' => $ve->errors()->toArray(),
                'trace' => $ve->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Validation error: ' . $ve->getMessage(),
                'errors' => $ve->errors()
            ], 422);

        } catch (\Illuminate\Database\QueryException $qe) {
            Log::error('DATABASE QUERY EXCEPTION', [
                'error' => $qe->getMessage(),
                'sql' => $qe->getSql(),
                'bindings' => $qe->getBindings(),
                'trace' => $qe->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'error' => 'Database error: ' . $qe->getMessage()], 500);

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
     * Simple expense destroy for Monthly Expense Page
     */
    public function destroyExpense($id)
    {
        $monthlyExpense = RecurringExpense::findOrFail($id);

        try {
            $monthlyExpense->delete();
            return redirect()->back()->with('success', 'Monthly expense deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error deleting monthly expense: ' . $e->getMessage());
        }
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