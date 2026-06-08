import { useState, useEffect } from 'react';
import { usePage, router, useForm } from '@inertiajs/react';
import BizanaLayout from '../../layouts/BizanaLayout';
import DateRangePicker from '../../components/DateRangePicker';

interface FilterOption {
    id: number;
    name: string;
}

interface DailyExpense {
    id: number;
    date: string;
    description: string;
    category: string;
    category_id: number;
    amount: string;
    currency: string;
    status: string;
    reference_number: string;
}

interface PaginationInfo {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

interface DailyExpensesPageProps {
    dailyExpenses: DailyExpense[];
    categories: FilterOption[];
    expenseCategories: FilterOption[];
    filters: {
        start_date: string;
        end_date: string;
        category_id: string;
    };
    totalAmount: string;
    pagination: PaginationInfo;
}

function DailyExpensesPage({
    dailyExpenses,
    categories,
    expenseCategories,
    filters,
    totalAmount,
    pagination,
}: DailyExpensesPageProps) {
    // State Management
    const [showAddExpenseModal, setShowAddExpenseModal] = useState(false);
    const [showEditExpenseModal, setShowEditExpenseModal] = useState(false);
    const [editingExpense, setEditingExpense] = useState<DailyExpense | null>(null);
    const [startDate, setStartDate] = useState(filters.start_date);
    const [endDate, setEndDate] = useState(filters.end_date);
    const [selectedCategory, setSelectedCategory] = useState(filters.category_id || 'All category');

    // Setup Inertia forms for expense operations
    const addExpenseForm = useForm({
        description: '',
        category_id: '',
        amount: '',
        currency: 'BDT',
        date: new Date().toISOString().split('T')[0],
    });

    const editExpenseForm = useForm({
        description: '',
        category_id: '',
        amount: '',
        currency: 'BDT',
        date: '',
    });

    // Calculate total expense
    const totalExpense = dailyExpenses.reduce(
        (sum, expense) => sum + parseFloat(expense.amount),
        0,
    );

    // Format date range for display
    const formatDateRange = () => {
        const start = new Date(startDate);
        const end = new Date(endDate);

        const startFormatted = start.toLocaleDateString('en-US', {
            day: 'numeric',
            month: 'short',
        }).replace(',', '');

        const endFormatted = end.toLocaleDateString('en-US', {
            day: 'numeric',
            month: 'short',
        }).replace(',', '');

        return `${startFormatted} – ${endFormatted}`;
    };

    // Handle filter changes with pagination
    const handleFilterChange = (page = 1) => {
        const params = new URLSearchParams();
        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (selectedCategory && selectedCategory !== 'All category') {
            params.append('category_id', selectedCategory);
        }
        if (page > 1) params.append('page', page.toString());

        router.get(`/admin/bizana/daily-expenses?${params.toString()}`, {}, {
            preserveState: true,
        });
    };

    // Daily Expense Functions
    const handleAddExpense = (e: React.FormEvent) => {
        e.preventDefault();

        addExpenseForm.post('/admin/api/bizana/expenses/expenses', {
            onSuccess: () => {
                setShowAddExpenseModal(false);
                addExpenseForm.reset();
                // Reload the page to show updated data
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
            `/admin/api/bizana/expenses/expenses/${editingExpense.id}`,
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

    const handleDeleteExpense = (expense: DailyExpense) => {
        if (confirm(`Are you sure you want to delete this expense?`)) {
            router.delete(`/admin/api/bizana/expenses/expenses/${expense.id}`, {
                onSuccess: () => {
                    router.reload();
                },
                onError: (errors) => {
                    console.error('Error deleting expense:', errors);
                },
            });
        }
    };

    const openEditModal = (expense: DailyExpense) => {
        setEditingExpense(expense);
        editExpenseForm.setData({
            description: expense.description,
            category_id: expense.category_id.toString(),
            amount: expense.amount,
            currency: expense.currency,
            date: expense.date,
        });
        setShowEditExpenseModal(true);
    };

    // Update filter when date or category changes
    useEffect(() => {
        handleFilterChange(1); // Reset to first page when filters change
    }, [startDate, endDate, selectedCategory]);

    // Handle date range change from DateRangePicker
    const handleDateRangeChange = (from: string, to: string) => {
        setStartDate(from);
        setEndDate(to);
    };

    return (
        <div className="">
            {/* Summary Card */}
            <div className="bg-white rounded-lg shadow p-6 mb-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h3 className="text-lg font-semibold text-gray-900">
                            Total Expense of {formatDateRange()}
                        </h3>
                        <p
                            className="text-2xl font-bold mt-2"
                            style={{ color: '#4285F4' }}
                        >
                            {parseFloat(totalAmount).toFixed(0)}Tk
                        </p>
                    </div>
                    <div className="flex items-center gap-4">
                        <DateRangePicker
                            startDate={startDate}
                            endDate={endDate}
                            onDateChange={handleDateRangeChange}
                        />
                        <select
                            value={selectedCategory}
                            onChange={(e) => setSelectedCategory(e.target.value)}
                            className="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="All category">
                                All category
                            </option>
                            {expenseCategories.map(
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
                                (e.currentTarget.style.backgroundColor = '#3367d6')
                            }
                            onMouseLeave={(e) =>
                                (e.currentTarget.style.backgroundColor = '#4285F4')
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

            {/* Daily Expenses Table */}
            <div className="bg-white rounded-lg shadow overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    DATE
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    DESCRIPTION
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    CATEGORY
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    AMOUNT
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    ACTIONS
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {dailyExpenses.map((expense, index) => (
                                <tr
                                    key={expense.id}
                                    className={
                                        index % 2 === 1 ? 'bg-gray-50' : 'bg-white'
                                    }
                                >
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        {new Date(expense.date)
                                            .toLocaleDateString('en-US', {
                                                day: 'numeric',
                                                month: 'long',
                                                year: 'numeric',
                                            })
                                            .replace(',', 'th')}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {expense.description}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <span
                                            className="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                                            style={{
                                                backgroundColor: '#4285F4',
                                            }}
                                        >
                                            {expense.category}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {parseFloat(expense.amount).toLocaleString()}tk
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div className="flex items-center justify-end space-x-2">
                                            <button
                                                onClick={() => openEditModal(expense)}
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
                                                onClick={() => handleDeleteExpense(expense)}
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
                            {dailyExpenses.length === 0 && (
                                <tr>
                                    <td colSpan={5} className="px-6 py-12 text-center">
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
                                                Get started by adding your first daily expense.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Pagination Controls */}
            {pagination.last_page > 1 && (
                <div className="bg-white rounded-lg shadow p-4 mt-6">
                    <div className="flex items-center justify-between">
                        <div className="text-sm text-gray-700">
                            Showing {pagination.from || 0} to {pagination.to || 0} of {pagination.total} results
                        </div>
                        <div className="flex items-center space-x-2">
                            <button
                                onClick={() => handleFilterChange(pagination.current_page - 1)}
                                disabled={pagination.current_page === 1}
                                className="px-3 py-1 text-sm border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                            >
                                Previous
                            </button>

                            {/* Page numbers */}
                            {Array.from({ length: pagination.last_page }, (_, i) => i + 1).map((page) => {
                                // Show first page, last page, current page, and pages around current page
                                if (
                                    page === 1 ||
                                    page === pagination.last_page ||
                                    (page >= pagination.current_page - 1 && page <= pagination.current_page + 1)
                                ) {
                                    return (
                                        <button
                                            key={page}
                                            onClick={() => handleFilterChange(page)}
                                            className={`px-3 py-1 text-sm border rounded-md ${
                                                page === pagination.current_page
                                                    ? 'bg-blue-500 text-white border-blue-500'
                                                    : 'border-gray-300 hover:bg-gray-50'
                                            }`}
                                        >
                                            {page}
                                        </button>
                                    );
                                } else if (
                                    page === pagination.current_page - 2 ||
                                    page === pagination.current_page + 2
                                ) {
                                    // Show ellipsis for skipped pages
                                    return <span key={page} className="px-2 text-gray-500">...</span>;
                                }
                                return null;
                            })}

                            <button
                                onClick={() => handleFilterChange(pagination.current_page + 1)}
                                disabled={pagination.current_page === pagination.last_page}
                                className="px-3 py-1 text-sm border border-gray-300 rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            )}

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
                                            Description
                                        </label>
                                        <input
                                            type="text"
                                            value={addExpenseForm.data.description}
                                            onChange={(e) =>
                                                addExpenseForm.setData(
                                                    'description',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Enter expense description"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-1">
                                            Category
                                        </label>
                                        <select
                                            value={addExpenseForm.data.category_id}
                                            onChange={(e) =>
                                                addExpenseForm.setData(
                                                    'category_id',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="">Select category</option>
                                            {expenseCategories.map(
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
                                            Date
                                        </label>
                                        <input
                                            type="date"
                                            value={addExpenseForm.data.date}
                                            onChange={(e) =>
                                                addExpenseForm.setData(
                                                    'date',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        />
                                    </div>
                                </div>
                                {/* Error Display */}
                                {addExpenseForm.errors &&
                                    Object.keys(addExpenseForm.errors).length >
                                        0 && (
                                        <div className="mt-4 p-3 bg-red-50 border border-red-200 rounded-md">
                                            <div className="text-sm text-red-600">
                                                {Object.values(addExpenseForm.errors).map(
                                                    (error, index) => (
                                                        <div key={index}>{error}</div>
                                                    ),
                                                )}
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
                                            Description
                                        </label>
                                        <input
                                            type="text"
                                            value={editExpenseForm.data.description}
                                            onChange={(e) =>
                                                editExpenseForm.setData(
                                                    'description',
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
                                            value={editExpenseForm.data.category_id}
                                            onChange={(e) =>
                                                editExpenseForm.setData(
                                                    'category_id',
                                                    e.target.value,
                                                )
                                            }
                                            required
                                            className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value="">Select category</option>
                                            {expenseCategories.map(
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
                                            Date
                                        </label>
                                        <input
                                            type="date"
                                            value={editExpenseForm.data.date}
                                            onChange={(e) =>
                                                editExpenseForm.setData(
                                                    'date',
                                                    e.target.value,
                                                )
                                            }
                                            required
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
                                                {Object.values(editExpenseForm.errors).map(
                                                    (error, index) => (
                                                        <div key={index}>{error}</div>
                                                    ),
                                                )}
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

DailyExpensesPage.layout = (page: any) => <BizanaLayout title="Daily Expenses">{page}</BizanaLayout>;

export default DailyExpensesPage;