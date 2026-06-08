import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import '../../css/app.css';
import DateRangePicker from '../../js/bizana/components/DateRangePicker';

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

interface DailyExpensesResponse {
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

function DailyExpensesPage() {
    // State Management
    const [dailyExpenses, setDailyExpenses] = useState<DailyExpense[]>([]);
    const [categories, setCategories] = useState<FilterOption[]>([]);
    const [expenseCategories, setExpenseCategories] = useState<FilterOption[]>(
        [],
    );
    const [totalAmount, setTotalAmount] = useState<string>('0');
    const [pagination, setPagination] = useState<PaginationInfo>({
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
        from: null,
        to: null,
    });

    const [showAddExpenseModal, setShowAddExpenseModal] = useState(false);
    const [showEditExpenseModal, setShowEditExpenseModal] = useState(false);
    const [editingExpense, setEditingExpense] = useState<DailyExpense | null>(
        null,
    );
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [selectedCategory, setSelectedCategory] = useState('');

    // Form states
    const [addExpenseForm, setAddExpenseForm] = useState({
        name: '',
        category_id: '',
        amount: '',
        currency: 'BDT',
        date: new Date().toISOString().split('T')[0],
        processing: false,
        errors: {} as Record<string, string>,
    });

    const [editExpenseForm, setEditExpenseForm] = useState({
        name: '',
        category_id: '',
        amount: '',
        currency: 'BDT',
        date: '',
        processing: false,
        errors: {} as Record<string, string>,
    });

    const [loading, setLoading] = useState(true);

    // Get API prefix from window config
    const getApiPrefix = () => {
        return (window as any).bizanaConfig?.apiPrefix || '/admin/api/bizana';
    };

    // Fetch daily expenses data
    const fetchDailyExpenses = async (page = 1) => {
        try {
            setLoading(true);
            const params = new URLSearchParams();
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);
            if (selectedCategory && selectedCategory !== 'All category') {
                params.append('category_id', selectedCategory);
            }
            params.append('page', page.toString());

            const response = await fetch(
                `${getApiPrefix()}/expenses/data?${params.toString()}`,
            );
            if (!response.ok) {
                throw new Error('Failed to fetch daily expenses');
            }

            const data: DailyExpensesResponse = await response.json();
            setDailyExpenses(data.dailyExpenses);
            setCategories(data.categories);
            setExpenseCategories(data.expenseCategories);
            setTotalAmount(data.totalAmount);
            setPagination(data.pagination);

            // Update form states with new filters
            setStartDate(data.filters.start_date);
            setEndDate(data.filters.end_date);
            setSelectedCategory(data.filters.category_id || 'All category');
        } catch (error) {
            console.error('Error fetching daily expenses:', error);
        } finally {
            setLoading(false);
        }
    };

    // Format date range for display
    const formatDateRange = () => {
        if (!startDate || !endDate) return '';

        const start = new Date(startDate);
        const end = new Date(endDate);

        const startFormatted = start
            .toLocaleDateString('en-US', {
                day: 'numeric',
                month: 'short',
            })
            .replace(',', '');

        const endFormatted = end
            .toLocaleDateString('en-US', {
                day: 'numeric',
                month: 'short',
            })
            .replace(',', '');

        return `${startFormatted} – ${endFormatted}`;
    };

    // Handle filter changes with pagination
    const handleFilterChange = (page = 1) => {
        fetchDailyExpenses(page);
    };

    // Handle date range change from DateRangePicker
    const handleDateRangeChange = (from: string, to: string) => {
        setStartDate(from);
        setEndDate(to);
    };

    // Daily Expense Functions
    const handleAddExpense = async (e: React.FormEvent) => {
        e.preventDefault();

        try {
            setAddExpenseForm((prev) => ({
                ...prev,
                processing: true,
                errors: {},
            }));

            const response = await fetch(
                `${getApiPrefix()}/expenses/expenses`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN':
                            (
                                document.querySelector(
                                    'meta[name="csrf-token"]',
                                ) as HTMLMetaElement
                            )?.content || '',
                    },
                    body: JSON.stringify({
                        description: addExpenseForm.name,
                        category_id: addExpenseForm.category_id,
                        amount: addExpenseForm.amount,
                        currency: addExpenseForm.currency,
                        date: addExpenseForm.date,
                    }),
                },
            );

            if (!response.ok) {
                const errorData = await response.json();
                if (errorData.errors) {
                    setAddExpenseForm((prev) => ({
                        ...prev,
                        errors: errorData.errors,
                    }));
                    return;
                }
                throw new Error('Failed to add expense');
            }

            setShowAddExpenseModal(false);
            setAddExpenseForm({
                name: '',
                category_id: '',
                amount: '',
                currency: 'BDT',
                date: new Date().toISOString().split('T')[0],
                processing: false,
                errors: {},
            });

            // Reload the data
            fetchDailyExpenses(pagination.current_page);
        } catch (error) {
            console.error('Error adding expense:', error);
            setAddExpenseForm((prev) => ({
                ...prev,
                processing: false,
                errors: { general: 'Error adding expense. Please try again.' },
            }));
        }
    };

    const handleEditExpense = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!editingExpense) return;

        try {
            setEditExpenseForm((prev) => ({
                ...prev,
                processing: true,
                errors: {},
            }));

            const response = await fetch(
                `${getApiPrefix()}/expenses/expenses/${editingExpense.id}`,
                {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN':
                            (
                                document.querySelector(
                                    'meta[name="csrf-token"]',
                                ) as HTMLMetaElement
                            )?.content || '',
                    },
                    body: JSON.stringify({
                        description: editExpenseForm.name,
                        category_id: editExpenseForm.category_id,
                        amount: editExpenseForm.amount,
                        currency: editExpenseForm.currency,
                        date: editExpenseForm.date,
                    }),
                },
            );

            if (!response.ok) {
                const errorData = await response.json();
                if (errorData.errors) {
                    setEditExpenseForm((prev) => ({
                        ...prev,
                        errors: errorData.errors,
                    }));
                    return;
                }
                throw new Error('Failed to update expense');
            }

            setShowEditExpenseModal(false);
            setEditingExpense(null);
            setEditExpenseForm({
                name: '',
                category_id: '',
                amount: '',
                currency: 'BDT',
                date: '',
                processing: false,
                errors: {},
            });

            // Reload the data
            fetchDailyExpenses(pagination.current_page);
        } catch (error) {
            console.error('Error updating expense:', error);
            setEditExpenseForm((prev) => ({
                ...prev,
                processing: false,
                errors: {
                    general: 'Error updating expense. Please try again.',
                },
            }));
        }
    };

    const handleDeleteExpense = async (expense: DailyExpense) => {
        if (!confirm(`Are you sure you want to delete this expense?`)) {
            return;
        }

        try {
            const response = await fetch(
                `${getApiPrefix()}/expenses/expenses/${expense.id}`,
                {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN':
                            (
                                document.querySelector(
                                    'meta[name="csrf-token"]',
                                ) as HTMLMetaElement
                            )?.content || '',
                    },
                },
            );

            if (!response.ok) {
                throw new Error('Failed to delete expense');
            }

            // Reload the data
            fetchDailyExpenses(pagination.current_page);
        } catch (error) {
            console.error('Error deleting expense:', error);
            alert('Error deleting expense. Please try again.');
        }
    };

    const openEditModal = (expense: DailyExpense) => {
        setEditingExpense(expense);
        setEditExpenseForm({
            name: expense.description,
            category_id: expense.category_id.toString(),
            amount: expense.amount,
            currency: expense.currency,
            date: expense.date,
            processing: false,
            errors: {},
        });
        setShowEditExpenseModal(true);
    };

    // Update filter when date or category changes
    useEffect(() => {
        handleFilterChange(1); // Reset to first page when filters change
    }, [startDate, endDate, selectedCategory]);

    // Initial data fetch
    useEffect(() => {
        fetchDailyExpenses();
    }, []);

    return (
        <div className="">
            {/* Summary Card */}
            <div className="card rounded-lg shadow p-6 mb-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h3 className="text-lg font-semibold text-foreground">
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
                            onChange={(e) =>
                                setSelectedCategory(e.target.value)
                            }
                            className="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="All category">All category</option>
                            {expenseCategories.map((category) => (
                                <option key={category.id} value={category.id}>
                                    {category.name}
                                </option>
                            ))}
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

            {/* Daily Expenses Table */}
            <div className="card rounded-lg shadow overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-border">
                        <thead className="theme-table__head">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                    DATE
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                    DESCRIPTION
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                    CATEGORY
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                    AMOUNT
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                    ACTIONS
                                </th>
                            </tr>
                        </thead>
                        <tbody className="theme-table__body divide-y divide-border">
                            {loading ? (
                                <tr>
                                    <td
                                        colSpan={5}
                                        className="px-6 py-12 text-center"
                                    >
                                        <div className="text-muted">
                                            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-muted mx-auto"></div>
                                            <p className="mt-2 text-sm">
                                                Loading expenses...
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            ) : dailyExpenses.length === 0 ? (
                                <tr>
                                    <td
                                        colSpan={5}
                                        className="px-6 py-12 text-center"
                                    >
                                        <div className="text-muted">
                                            <svg
                                                className="mx-auto h-12 w-12 text-muted"
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
                                            <h3 className="mt-2 text-sm font-medium text-foreground">
                                                No expenses found
                                            </h3>
                                            <p className="mt-1 text-sm text-muted">
                                                Get started by adding your first
                                                daily expense.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            ) : (
                                dailyExpenses.map((expense, index) => (
                                    <tr
                                        key={expense.id}
                                        className="theme-table__row"
                                    >
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-muted">
                                            {new Date(expense.date)
                                                .toLocaleDateString('en-US', {
                                                    day: 'numeric',
                                                    month: 'long',
                                                    year: 'numeric',
                                                })
                                                .replace(',', 'th')}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
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
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                                            {parseFloat(
                                                expense.amount,
                                            ).toLocaleString()}
                                            tk
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div className="flex items-center justify-end space-x-2">
                                                <button
                                                    onClick={() =>
                                                        openEditModal(expense)
                                                    }
                                                    className="text-muted hover:text-blue-600 transition-colors"
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
                                                    className="text-muted hover:text-red-600 transition-colors"
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
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {/* Pagination Controls */}
            {pagination.last_page > 1 && (
                <div className="card rounded-lg shadow p-4 mt-6">
                    <div className="flex items-center justify-between">
                        <div className="text-sm text-foreground">
                            Showing {pagination.from || 0} to{' '}
                            {pagination.to || 0} of {pagination.total} results
                        </div>
                        <div className="flex items-center space-x-2">
                            <button
                                onClick={() =>
                                    handleFilterChange(
                                        pagination.current_page - 1,
                                    )
                                }
                                disabled={pagination.current_page === 1}
                                className="px-3 py-1 text-sm border border-border rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-muted/50"
                            >
                                Previous
                            </button>

                            {/* Page numbers */}
                            {Array.from(
                                { length: pagination.last_page },
                                (_, i) => i + 1,
                            ).map((page) => {
                                // Show first page, last page, current page, and pages around current page
                                if (
                                    page === 1 ||
                                    page === pagination.last_page ||
                                    (page >= pagination.current_page - 1 &&
                                        page <= pagination.current_page + 1)
                                ) {
                                    return (
                                        <button
                                            key={page}
                                            onClick={() =>
                                                handleFilterChange(page)
                                            }
                                            className={`px-3 py-1 text-sm border rounded-md ${
                                                page === pagination.current_page
                                                    ? 'bg-blue-500 text-white border-blue-500'
                                                    : 'border-border hover:bg-muted/50'
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
                                    return (
                                        <span
                                            key={page}
                                            className="px-2 text-muted"
                                        >
                                            ...
                                        </span>
                                    );
                                }
                                return null;
                            })}

                            <button
                                onClick={() =>
                                    handleFilterChange(
                                        pagination.current_page + 1,
                                    )
                                }
                                disabled={
                                    pagination.current_page ===
                                    pagination.last_page
                                }
                                className="px-3 py-1 text-sm border border-border rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-muted/50"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Add Expense Modal */}
            {showAddExpenseModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
                    <div className="relative card rounded-md shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                        {/* Modal Header */}
                        <div className="flex items-center justify-between p-6 border-b border-border">
                            <div className="flex items-center space-x-3">
                                <div>
                                    <h2 className="text-xl font-bold text-foreground">
                                        Add New Expense
                                    </h2>
                                </div>
                            </div>
                            <button
                                type="button"
                                onClick={() => {
                                    setShowAddExpenseModal(false);
                                    setAddExpenseForm({
                                        name: '',
                                        category_id: '',
                                        amount: '',
                                        currency: 'BDT',
                                        date: new Date()
                                            .toISOString()
                                            .split('T')[0],
                                        processing: false,
                                        errors: {},
                                    });
                                }}
                                className="w-10 h-10 rounded-full hover:bg-muted/50 flex items-center justify-center transition-colors"
                            >
                                <svg
                                    className="w-5 h-5 text-muted"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        {/* Modal Body */}
                        <form onSubmit={handleAddExpense}>
                            <div className="p-6 space-y-6">
                                {/* Description */}
                                <div className="flex gap-16 items-center">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Name
                                        </label>
                                    </div>
                                    <input
                                        type="text"
                                        value={addExpenseForm.name}
                                        onChange={(e) =>
                                            setAddExpenseForm((prev) => ({
                                                ...prev,
                                                name: e.target.value,
                                            }))
                                        }
                                        required
                                        className="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-muted/50"
                                        placeholder="Enter expense name"
                                    />
                                </div>

                                {/* Category */}
                                <div className="flex gap-16 items-center">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Category
                                        </label>
                                    </div>
                                    <select
                                        value={addExpenseForm.category_id}
                                        onChange={(e) =>
                                            setAddExpenseForm((prev) => ({
                                                ...prev,
                                                category_id: e.target.value,
                                            }))
                                        }
                                        required
                                        className="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">
                                            Select category
                                        </option>
                                        {expenseCategories.map((category) => (
                                            <option
                                                key={category.id}
                                                value={category.id}
                                            >
                                                {category.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {/* Amount */}
                                <div className="flex gap-16 items-center">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Cost
                                        </label>
                                    </div>
                                    <input
                                        type="number"
                                        value={addExpenseForm.amount}
                                        onChange={(e) =>
                                            setAddExpenseForm((prev) => ({
                                                ...prev,
                                                amount: e.target.value,
                                            }))
                                        }
                                        step="0.01"
                                        min="0"
                                        required
                                        className="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="0.00"
                                    />
                                </div>

                                {/* Date */}
                                <div className="flex gap-16 items-center">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Date
                                        </label>
                                    </div>
                                    <input
                                        type="date"
                                        value={addExpenseForm.date}
                                        onChange={(e) =>
                                            setAddExpenseForm((prev) => ({
                                                ...prev,
                                                date: e.target.value,
                                            }))
                                        }
                                        required
                                        className="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                {/* Error Display */}
                                {Object.keys(addExpenseForm.errors).length >
                                    0 && (
                                    <div className="p-4 bg-red-50 border border-red-200 rounded-xl">
                                        <div className="flex items-center space-x-2">
                                            <svg
                                                className="w-5 h-5 text-red-600"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth={2}
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            <span className="text-sm font-medium text-red-800">
                                                Error
                                            </span>
                                        </div>
                                        <div className="mt-2 text-sm text-red-700">
                                            {Object.values(
                                                addExpenseForm.errors,
                                            ).map((error, index) => (
                                                <div key={index}>{error}</div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Modal Footer */}
                            <div className="p-6 border-t border-border bg-muted/10 rounded-b-2xl">
                                <button
                                    type="submit"
                                    disabled={addExpenseForm.processing}
                                    className="w-full px-6 py-3 text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                                >
                                    {addExpenseForm.processing ? (
                                        <>
                                            <svg
                                                className="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    className="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    strokeWidth="4"
                                                ></circle>
                                                <path
                                                    className="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                            <span>Adding...</span>
                                        </>
                                    ) : (
                                        <>
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
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>
                                            <span>Save Expense</span>
                                        </>
                                    )}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Edit Expense Modal */}
            {showEditExpenseModal && editingExpense && (
                <div className="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
                    <div className="relative card rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                        {/* Modal Header */}
                        <div className="flex items-center justify-between p-6 border-b border-border">
                            <div className="flex items-center space-x-3">
                                <div className="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                                    <svg
                                        className="w-6 h-6 text-blue-600"
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
                                </div>
                                <div>
                                    <h2 className="text-xl font-bold text-foreground">
                                        Edit Expense
                                    </h2>
                                    <p className="text-sm text-muted">
                                        Update expense record
                                    </p>
                                </div>
                            </div>
                            <button
                                type="button"
                                onClick={() => {
                                    setShowEditExpenseModal(false);
                                    setEditingExpense(null);
                                    setEditExpenseForm({
                                        name: '',
                                        category_id: '',
                                        amount: '',
                                        currency: 'BDT',
                                        date: '',
                                        processing: false,
                                        errors: {},
                                    });
                                }}
                                className="w-10 h-10 rounded-full hover:bg-muted/50 flex items-center justify-center transition-colors"
                            >
                                <svg
                                    className="w-5 h-5 text-muted"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>

                        {/* Modal Body */}
                        <form onSubmit={handleEditExpense}>
                            <div className="p-6 space-y-6">
                                {/* Description */}
                                <div className="flex gap-16 items-center">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Description
                                        </label>
                                    </div>
                                    <input
                                        type="text"
                                        value={editExpenseForm.name}
                                        onChange={(e) =>
                                            setEditExpenseForm((prev) => ({
                                                ...prev,
                                                name: e.target.value,
                                            }))
                                        }
                                        required
                                        className="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent placeholder-muted/50"
                                        placeholder="Enter expense description"
                                    />
                                </div>

                                {/* Category */}
                                <div className="flex gap-16 items-center">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Category
                                        </label>
                                    </div>
                                    <select
                                        value={editExpenseForm.category_id}
                                        onChange={(e) =>
                                            setEditExpenseForm((prev) => ({
                                                ...prev,
                                                category_id: e.target.value,
                                            }))
                                        }
                                        required
                                        className="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                        <option value="">
                                            Select category
                                        </option>
                                        {expenseCategories.map((category) => (
                                            <option
                                                key={category.id}
                                                value={category.id}
                                            >
                                                {category.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                {/* Amount */}
                                <div className="flex gap-16 items-center">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Cost
                                        </label>
                                    </div>
                                    <input
                                        type="number"
                                        value={editExpenseForm.amount}
                                        onChange={(e) =>
                                            setEditExpenseForm((prev) => ({
                                                ...prev,
                                                amount: e.target.value,
                                            }))
                                        }
                                        step="0.01"
                                        min="0"
                                        required
                                        className="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="0.00"
                                    />
                                </div>

                                {/* Date */}
                                <div className="flex gap-16 items-center">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Date
                                        </label>
                                    </div>
                                    <input
                                        type="date"
                                        value={editExpenseForm.date}
                                        onChange={(e) =>
                                            setEditExpenseForm((prev) => ({
                                                ...prev,
                                                date: e.target.value,
                                            }))
                                        }
                                        required
                                        className="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                {/* Error Display */}
                                {Object.keys(editExpenseForm.errors).length >
                                    0 && (
                                    <div className="p-4 bg-red-50 border border-red-200 rounded-xl">
                                        <div className="flex items-center space-x-2">
                                            <svg
                                                className="w-5 h-5 text-red-600"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth={2}
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                            <span className="text-sm font-medium text-red-800">
                                                Error
                                            </span>
                                        </div>
                                        <div className="mt-2 text-sm text-red-700">
                                            {Object.values(
                                                editExpenseForm.errors,
                                            ).map((error, index) => (
                                                <div key={index}>{error}</div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Modal Footer */}
                            <div className="p-6 border-t border-border bg-muted/10 rounded-b-2xl">
                                <button
                                    type="submit"
                                    disabled={editExpenseForm.processing}
                                    className="w-full px-6 py-3 text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                                >
                                    {editExpenseForm.processing ? (
                                        <>
                                            <svg
                                                className="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                            >
                                                <circle
                                                    className="opacity-25"
                                                    cx="12"
                                                    cy="12"
                                                    r="10"
                                                    stroke="currentColor"
                                                    strokeWidth="4"
                                                ></circle>
                                                <path
                                                    className="opacity-75"
                                                    fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                                ></path>
                                            </svg>
                                            <span>Updating...</span>
                                        </>
                                    ) : (
                                        <>
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
                                                    d="M5 13l4 4L19 7"
                                                />
                                            </svg>
                                            <span>Save Expense</span>
                                        </>
                                    )}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

// Mount the React app
const container = document.getElementById('bizana-app');
if (container) {
    const root = createRoot(container);
    root.render(<DailyExpensesPage />);
} else {
    console.error('Bizana daily expenses app container not found');
}

export default DailyExpensesPage;
