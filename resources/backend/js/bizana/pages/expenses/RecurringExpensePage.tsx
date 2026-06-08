import { useState, useCallback, useEffect } from 'react';
import { usePage, router, useForm } from '@inertiajs/react';
import BizanaLayout from '../../layouts/BizanaLayout';

// Price Calculator Interfaces
interface Product {
    id: number;
    name: string;
    purchase_cost: number;
    packaging_cost: number;
    additional_costs: number;
    total_cost?: number;
}

interface PaginatedProducts {
    data: Product[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface FilterOption {
    id: number;
    name: string;
}

interface PurchaseOrderOption {
    id: number;
    reference_code: string;
    supplier_name: string;
}

// Monthly Expense Interfaces
interface RecurringExpense {
    id: number;
    name: string;
    amount: number;
    currency: string;
    category: {
        id: number;
        name: string;
    };
    start_date: string;
    description?: string;
}

interface MonthlyExpensePageProps {
    products: PaginatedProducts;
    filters: {
        search: string;
        per_page: number;
        category_id: string;
        brand_id: string;
        purchase_order_id: string;
    };
    filterOptions: {
        categories: FilterOption[];
        brands: FilterOption[];
        purchase_orders: PurchaseOrderOption[];
        expenseCategories: FilterOption[];
    };
    monthlyExpenses: {
        data: RecurringExpense[];
        total: number;
    };
    currentMonth: string;
    currentYear: string;
}

function MonthlyExpensePage({
    products: paginatedProducts,
    filters,
    filterOptions,
    monthlyExpenses,
    currentMonth,
    currentYear,
}: MonthlyExpensePageProps) {
    // Price Calculator State
    const [products, setProducts] = useState<Product[]>(paginatedProducts.data);
    const [search, setSearch] = useState(filters.search);
    const [perPage, setPerPage] = useState(filters.per_page);
    const [categoryId, setCategoryId] = useState(filters.category_id);
    const [brandId, setBrandId] = useState(filters.brand_id);
    const [purchaseOrderId, setPurchaseOrderId] = useState(
        filters.purchase_order_id,
    );
    const [changedPackagingCosts, setChangedPackagingCosts] = useState<
        Set<number>
    >(new Set());
    const [originalPackagingCosts, setOriginalPackagingCosts] = useState<
        Map<number, number>
    >(new Map());
    const [bulkPackagingCost, setBulkPackagingCost] = useState<string>('');
    const [bulkPackagingCostChanged, setBulkPackagingCostChanged] =
        useState<boolean>(false);
    const [changedAdditionalCosts, setChangedAdditionalCosts] = useState<
        Set<number>
    >(new Set());
    const [originalAdditionalCosts, setOriginalAdditionalCosts] = useState<
        Map<number, number>
    >(new Map());
    const [bulkAdditionalCost, setBulkAdditionalCost] = useState<string>('');
    const [bulkAdditionalCostChanged, setBulkAdditionalCostChanged] =
        useState<boolean>(false);

    // Monthly Expense State
    const [showAddExpenseModal, setShowAddExpenseModal] = useState(false);
    const [showEditExpenseModal, setShowEditExpenseModal] = useState(false);
    const [editingExpense, setEditingExpense] =
        useState<RecurringExpense | null>(null);
    const [selectedMonth, setSelectedMonth] = useState(
        `${currentMonth}, ${currentYear}`,
    );
    const [currentDisplayMonth, setCurrentDisplayMonth] = useState(new Date());
    const [selectedCategory, setSelectedCategory] = useState('All category');
    const [expenses, setExpenses] = useState<RecurringExpense[]>(
        monthlyExpenses.data,
    );

    // Handle category filter change
    const handleCategoryFilterChange = (categoryValue: string) => {
        setSelectedCategory(categoryValue);

        // Filter expenses based on selected category
        applyFilters(categoryValue, selectedMonth);
    };

    // Handle month navigation
    const navigateMonth = (direction: 'prev' | 'next') => {
        const newDate = new Date(currentDisplayMonth);
        if (direction === 'prev') {
            newDate.setMonth(newDate.getMonth() - 1);
        } else {
            newDate.setMonth(newDate.getMonth() + 1);
        }
        setCurrentDisplayMonth(newDate);

        const monthName = newDate.toLocaleDateString('en-US', { month: 'long' });
        const year = newDate.getFullYear();
        const monthString = `${monthName}, ${year}`;
        setSelectedMonth(monthString);

        // Apply filters with new month
        applyFilters(selectedCategory, monthString);
    };

    // Format current display month
    const getDisplayMonthString = () => {
        return currentDisplayMonth.toLocaleDateString('en-US', {
            month: 'long',
            year: 'numeric'
        });
    };

  // Apply both category and month filters
    const applyFilters = (category: string, month: string) => {
        let filteredExpenses = monthlyExpenses.data;

        // Apply category filter
        if (category !== 'All category') {
            filteredExpenses = filteredExpenses.filter(expense =>
                expense.category.name === category
            );
        }

        // Apply month filter based on start_date of recurring expense
        if (month && month !== `${currentMonth}, ${currentYear}`) {
            const [selectedMonthName, selectedYear] = month.split(', ');
            const monthIndex = new Date(Date.parse(selectedMonthName + ' 1, 2023')).getMonth();

            filteredExpenses = filteredExpenses.filter(expense => {
                const expenseStartDate = new Date(expense.start_date);
                return expenseStartDate.getMonth() === monthIndex &&
                       expenseStartDate.getFullYear() === parseInt(selectedYear);
            });
        }

        setExpenses(filteredExpenses);
    };

    // Setup Inertia forms for expense operations
    const addExpenseForm = useForm({
        name: '',
        description: '',
        category_id: '',
        amount: '',
        currency: 'BDT',
        start_date: new Date().toISOString().split('T')[0],
    });

    const editExpenseForm = useForm({
        name: '',
        description: '',
        category_id: '',
        amount: '',
        currency: 'BDT',
        start_date: '',
    });

    // Calculate total expense
    const totalExpense = expenses.reduce(
        (sum, expense) => sum + expense.amount,
        0,
    );

    // Price Calculator Functions
    const updateProduct = (id: number, field: keyof Product, value: number) => {
        setProducts((prev) =>
            prev.map((product) => {
                if (product.id === id) {
                    const updated = { ...product, [field]: value };
                    if (
                        ['packaging_cost', 'additional_costs'].includes(field)
                    ) {
                        updated.total_cost = Number(
                            (updated.purchase_cost || 0) +
                                (updated.packaging_cost || 0) +
                                (updated.additional_costs || 0),
                        );
                    }
                    return updated;
                }
                return product;
            }),
        );
    };

    const handlePackagingCostChange = (id: number, value: string) => {
        const packagingCost = parseFloat(value) || 0;
        const originalCost = originalPackagingCosts.get(id) || 0;

        updateProduct(id, 'packaging_cost', packagingCost);

        if (packagingCost !== originalCost) {
            setChangedPackagingCosts((prev) => new Set(prev).add(id));
        } else {
            setChangedPackagingCosts((prev) => {
                const newSet = new Set(prev);
                newSet.delete(id);
                return newSet;
            });
        }
    };

    const handleAdditionalCostChange = (id: number, value: string) => {
        const additionalCost = parseFloat(value) || 0;
        const originalCost = originalAdditionalCosts.get(id) || 0;

        updateProduct(id, 'additional_costs', additionalCost);

        if (additionalCost !== originalCost) {
            setChangedAdditionalCosts((prev) => new Set(prev).add(id));
        } else {
            setChangedAdditionalCosts((prev) => {
                const newSet = new Set(prev);
                newSet.delete(id);
                return newSet;
            });
        }
    };

    const getCookie = (name: string) => {
        let cookieValue = null;
        if (document.cookie && document.cookie !== '') {
            const cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                const cookie = cookies[i].trim();
                if (cookie.substring(0, name.length + 1) === name + '=') {
                    cookieValue = decodeURIComponent(
                        cookie.substring(name.length + 1),
                    );
                    break;
                }
            }
        }
        return cookieValue;
    };

    // Monthly Expense Functions
    const handleAddExpense = (e: React.FormEvent) => {
        e.preventDefault();

        addExpenseForm.post('/admin/api/bizana/recurring-expenses', {
            onSuccess: () => {
                setShowAddExpenseModal(false);
                addExpenseForm.reset();
                router.reload();
            },
            onError: (errors) => {
                console.error('Error adding expense:', errors);
            },
        });
    };

    const handleEditExpense = (e: React.FormEvent) => {
        e.preventDefault();
        if (!editingExpense) return;

        editExpenseForm.put(
            `/admin/api/bizana/recurring-expenses/${editingExpense.id}`,
            {
                onSuccess: () => {
                    setShowEditExpenseModal(false);
                    setEditingExpense(null);
                    editExpenseForm.reset();
                    router.reload();
                },
                onError: (errors) => {
                    console.error('Error updating expense:', errors);
                },
            },
        );
    };

    const handleDeleteExpense = (expense: RecurringExpense) => {
        if (confirm(`Are you sure you want to delete "${expense.name}"?`)) {
            router.delete(`/admin/api/bizana/recurring-expenses/${expense.id}`, {
                onSuccess: () => {
                    router.reload();
                },
                onError: (errors) => {
                    console.error('Error deleting expense:', errors);
                },
            });
        }
    };

    const openEditModal = (expense: RecurringExpense) => {
        setEditingExpense(expense);
        // Populate the edit form with current expense data
        editExpenseForm.setData({
            name: expense.name,
            description: expense.description || '',
            category_id: expense.category.id.toString(),
            amount: expense.amount.toString(),
            currency: expense.currency,
            start_date: expense.start_date,
        });
        setShowEditExpenseModal(true);
    };

    // Initialize products and update expenses from props
    useEffect(() => {
        setProducts(
            paginatedProducts.data.map((product) => ({
                ...product,
                total_cost: Number(
                    (product.purchase_cost || 0) +
                        (product.packaging_cost || 0) +
                        (product.additional_costs || 0),
                ),
            })),
        );

        const newOriginalCosts = new Map<number, number>();
        paginatedProducts.data.forEach((product) => {
            newOriginalCosts.set(product.id, product.packaging_cost);
        });
        setOriginalPackagingCosts(newOriginalCosts);

        const newOriginalAdditionalCosts = new Map<number, number>();
        paginatedProducts.data.forEach((product) => {
            newOriginalAdditionalCosts.set(
                product.id,
                product.additional_costs,
            );
        });
        setOriginalAdditionalCosts(newOriginalAdditionalCosts);
    }, [paginatedProducts.data]);

    // Initialize display month on component mount
    useEffect(() => {
        const today = new Date();
        setCurrentDisplayMonth(today);

        const monthName = today.toLocaleDateString('en-US', { month: 'long' });
        const year = today.getFullYear();
        const monthString = `${monthName}, ${year}`;
        setSelectedMonth(monthString);
    }, []);

    // Update expenses state when props change (after page reload)
    useEffect(() => {
        applyFilters(selectedCategory, selectedMonth);
    }, [monthlyExpenses.data]);

    return (
        <div className="">
            {/* Monthly Expenses Section */}
            <div>
                {/* Summary Card */}
                <div className="bg-white rounded-lg shadow p-6 mb-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h3 className="text-lg font-semibold text-gray-900">
                                Total Expense of {getDisplayMonthString()}
                            </h3>
                            <p
                                className="text-3xl font-bold mt-2"
                                style={{ color: '#4285F4' }}
                            >
                                {totalExpense.toFixed(0)}Tk
                            </p>
                        </div>
                        <div className="flex items-center gap-4">
                            <div className="flex items-center gap-2">
                        <button
                            onClick={() => navigateMonth('prev')}
                            className="p-2 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            title="Previous month"
                        >
                            <svg
                                className="w-5 h-5 text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </button>

                        <div className="px-4 py-2 border border-gray-300 rounded-md bg-white min-w-[160px] text-center font-medium text-gray-900">
                            {getDisplayMonthString()}
                        </div>

                        <button
                            onClick={() => navigateMonth('next')}
                            className="p-2 border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            title="Next month"
                        >
                            <svg
                                className="w-5 h-5 text-gray-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>
                        </button>
                    </div>
                            <select
                                value={selectedCategory}
                                onChange={(e) =>
                                    handleCategoryFilterChange(e.target.value)
                                }
                                className="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="All category">
                                    All category
                                </option>
                                {filterOptions.expenseCategories.map(
                                    (category) => (
                                        <option
                                            key={category.id}
                                            value={category.name}
                                        >
                                            {category.name}
                                        </option>
                                    ),
                                )}
                            </select>
                            <button
                                onClick={() => setShowAddExpenseModal(true)}
                                className="text-white px-4 py-2 rounded-md flex items-center gap-2 transition-colors"
                                style={{ backgroundColor: '#4285F4' }}
                                onMouseEnter={(e) =>
                                    (e.currentTarget.style.backgroundColor =
                                        '#3367d6')
                                }
                                onMouseLeave={(e) =>
                                    (e.currentTarget.style.backgroundColor =
                                        '#4285F4')
                                }
                            >
                                <svg
                                    className="w-5 h-5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M12 4v16m8-8H4"
                                    />
                                </svg>
                                ADD NEW EXPENSE
                            </button>
                        </div>
                    </div>
                </div>

                {/* Expenses Table */}
                <div className="bg-white rounded-lg shadow overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        EXPENSE
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        EXPENSE CATEGORY
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        TOTAL EXPENSE
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        STARTING DATE
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        OPTIONS
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {expenses.map((expense, index) => (
                                    <tr
                                        key={expense.id}
                                        className={
                                            index % 2 === 1
                                                ? 'bg-gray-50'
                                                : 'bg-white'
                                        }
                                    >
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {expense.name}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span
                                                className="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                                                style={{
                                                    backgroundColor: '#4285F4',
                                                }}
                                            >
                                                {expense.category.name}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {expense.amount.toLocaleString()}tk
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                            {new Date(expense.start_date)
                                                .toLocaleDateString('en-US', {
                                                    day: 'numeric',
                                                    month: 'long',
                                                    year: 'numeric',
                                                })
                                                .replace(',', 'th')}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div className="flex items-center justify-end space-x-2">
                                                <button
                                                    onClick={() =>
                                                        openEditModal(expense)
                                                    }
                                                    className="text-gray-600 hover:text-blue-600 transition-colors"
                                                    title="Edit expense"
                                                >
                                                    <svg
                                                        className="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth={2}
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                                        />
                                                    </svg>
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleDeleteExpense(
                                                            expense,
                                                        )
                                                    }
                                                    className="text-gray-600 hover:text-red-600 transition-colors"
                                                    title="Delete expense"
                                                >
                                                    <svg
                                                        className="w-4 h-4"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth={2}
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                        />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                                {expenses.length === 0 && (
                                    <tr>
                                        <td
                                            colSpan={5}
                                            className="px-6 py-12 text-center"
                                        >
                                            <div className="text-gray-400">
                                                <svg
                                                    className="mx-auto h-12 w-12 text-gray-400"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        strokeWidth={2}
                                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                                    />
                                                </svg>
                                                <h3 className="mt-2 text-sm font-medium text-gray-900">
                                                    No expenses found
                                                </h3>
                                                <p className="mt-1 text-sm text-gray-500">
                                                    Get started by adding your
                                                    first monthly expense.
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* Add Expense Modal */}
            {showAddExpenseModal && (
                <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div className="mt-3">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Add New Expense
                            </h3>
                            <form onSubmit={handleAddExpense}>
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Expense Name
                                        </label>
                                        <input
                                            type="text"
                                            value={addExpenseForm.data.name}
                                            onChange={(e) =>
                                                addExpenseForm.setData(
                                                    'name',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Enter expense name"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Category
                                        </label>
                                        <select
                                            value={
                                                addExpenseForm.data.category_id
                                            }
                                            onChange={(e) =>
                                                addExpenseForm.setData(
                                                    'category_id',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="">
                                                Select category
                                            </option>
                                            {filterOptions.expenseCategories.map(
                                                (category) => (
                                                    <option
                                                        key={category.id}
                                                        value={category.id}
                                                    >
                                                        {category.name}
                                                    </option>
                                                ),
                                            )}
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Amount
                                        </label>
                                        <input
                                            type="number"
                                            value={addExpenseForm.data.amount}
                                            onChange={(e) =>
                                                addExpenseForm.setData(
                                                    'amount',
                                                    e.target.value,
                                                )
                                            }
                                            step="0.01"
                                            min="0"
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="0.00"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Starting Date
                                        </label>
                                        <input
                                            type="date"
                                            value={
                                                addExpenseForm.data.start_date
                                            }
                                            onChange={(e) =>
                                                addExpenseForm.setData(
                                                    'start_date',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Description
                                        </label>
                                        <textarea
                                            value={
                                                addExpenseForm.data.description
                                            }
                                            onChange={(e) =>
                                                addExpenseForm.setData(
                                                    'description',
                                                    e.target.value,
                                                )
                                            }
                                            rows={3}
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Optional description"
                                        />
                                    </div>
                                </div>
                                {/* Error Display */}
                                {addExpenseForm.errors &&
                                    Object.keys(addExpenseForm.errors).length >
                                        0 && (
                                        <div className="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                                            <div className="text-sm text-red-600">
                                                {Object.values(
                                                    addExpenseForm.errors,
                                                ).map((error, index) => (
                                                    <div key={index}>
                                                        {error}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                <div className="flex justify-end space-x-3 mt-6">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setShowAddExpenseModal(false);
                                            addExpenseForm.reset();
                                        }}
                                        className="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={addExpenseForm.processing}
                                        className="px-4 py-2 text-white rounded-md hover:opacity-90 transition-opacity disabled:opacity-50"
                                        style={{ backgroundColor: '#4285F4' }}
                                    >
                                        {addExpenseForm.processing
                                            ? 'Adding...'
                                            : 'Add Expense'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* Edit Expense Modal */}
            {showEditExpenseModal && editingExpense && (
                <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <div className="mt-3">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Edit Expense
                            </h3>
                            <form onSubmit={handleEditExpense}>
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Expense Name
                                        </label>
                                        <input
                                            type="text"
                                            value={editExpenseForm.data.name}
                                            onChange={(e) =>
                                                editExpenseForm.setData(
                                                    'name',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Category
                                        </label>
                                        <select
                                            value={
                                                editExpenseForm.data.category_id
                                            }
                                            onChange={(e) =>
                                                editExpenseForm.setData(
                                                    'category_id',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="">
                                                Select category
                                            </option>
                                            {filterOptions.expenseCategories.map(
                                                (category) => (
                                                    <option
                                                        key={category.id}
                                                        value={category.id}
                                                    >
                                                        {category.name}
                                                    </option>
                                                ),
                                            )}
                                        </select>
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Amount
                                        </label>
                                        <input
                                            type="number"
                                            value={editExpenseForm.data.amount}
                                            onChange={(e) =>
                                                editExpenseForm.setData(
                                                    'amount',
                                                    e.target.value,
                                                )
                                            }
                                            step="0.01"
                                            min="0"
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Starting Date
                                        </label>
                                        <input
                                            type="date"
                                            value={
                                                editExpenseForm.data.start_date
                                            }
                                            onChange={(e) =>
                                                editExpenseForm.setData(
                                                    'start_date',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Description
                                        </label>
                                        <textarea
                                            value={
                                                editExpenseForm.data.description
                                            }
                                            onChange={(e) =>
                                                editExpenseForm.setData(
                                                    'description',
                                                    e.target.value,
                                                )
                                            }
                                            rows={3}
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>
                                {/* Error Display */}
                                {editExpenseForm.errors &&
                                    Object.keys(editExpenseForm.errors).length >
                                        0 && (
                                        <div className="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                                            <div className="text-sm text-red-600">
                                                {Object.values(
                                                    editExpenseForm.errors,
                                                ).map((error, index) => (
                                                    <div key={index}>
                                                        {error}
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                <div className="flex justify-end space-x-3 mt-6">
                                    <button
                                        type="button"
                                        onClick={() => {
                                            setShowEditExpenseModal(false);
                                            setEditingExpense(null);
                                            editExpenseForm.reset();
                                        }}
                                        className="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={editExpenseForm.processing}
                                        className="px-4 py-2 text-white rounded-md hover:opacity-90 transition-opacity disabled:opacity-50"
                                        style={{ backgroundColor: '#4285F4' }}
                                    >
                                        {editExpenseForm.processing
                                            ? 'Updating...'
                                            : 'Update Expense'}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

MonthlyExpensePage.layout = (page: React.ReactElement) => (
    <BizanaLayout title="Fixed Monthly Expense">{page}</BizanaLayout>
);

export default MonthlyExpensePage;
