import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import { Toaster, toast } from 'react-hot-toast';
import '../../css/app.css';

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

interface FilterOption {
    id: number;
    name: string;
}

function MonthlyExpensesPage() {
    const [loading, setLoading] = useState(true);
    const [showAddExpenseModal, setShowAddExpenseModal] = useState(false);
    const [showEditExpenseModal, setShowEditExpenseModal] = useState(false);
    const [editingExpense, setEditingExpense] =
        useState<RecurringExpense | null>(null);
    const [selectedMonth, setSelectedMonth] = useState('');
    const [currentDisplayMonth, setCurrentDisplayMonth] = useState(new Date());
    const [selectedCategory, setSelectedCategory] = useState('All category');

    // Data states
    const [monthlyExpenses, setMonthlyExpenses] = useState<RecurringExpense[]>(
        [],
    );
    const [allExpenses, setAllExpenses] = useState<RecurringExpense[]>([]);
    const [expenseCategories, setExpenseCategories] = useState<FilterOption[]>(
        [],
    );
    const [currentMonth, setCurrentMonth] = useState('');
    const [currentYear, setCurrentYear] = useState('');

    // Form states
    const [addExpenseForm, setAddExpenseForm] = useState({
        name: '',
        description: '',
        category_id: '',
        amount: '',
        currency: 'BDT',
        start_date: new Date().toISOString().split('T')[0],
        processing: false,
        errors: {} as Record<string, string>,
    });

    const [editExpenseForm, setEditExpenseForm] = useState({
        name: '',
        description: '',
        category_id: '',
        amount: '',
        currency: 'BDT',
        start_date: '',
        processing: false,
        errors: {} as Record<string, string>,
    });

    // Get API prefix
    const getApiPrefix = () => {
        return (window as any).bizanaConfig?.apiPrefix || '/admin/api/bizana';
    };

    // Fetch monthly expenses data
    const fetchData = async () => {
        try {
            const response = await fetch(
                `${getApiPrefix()}/monthly-expenses/data`,
            );
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            const data = await response.json();

            setAllExpenses(data.data || []);
            setMonthlyExpenses(data.data || []);
            setExpenseCategories(data.expenseCategories || []);
            setCurrentMonth(data.currentMonth || '');
            setCurrentYear(data.currentYear || '');

            // Set current display month
            const today = new Date();
            const monthName = today.toLocaleDateString('en-US', {
                month: 'long',
            });
            const year = today.getFullYear();
            setSelectedMonth(`${monthName}, ${year}`);
            setCurrentDisplayMonth(today);
        } catch (error) {
            console.error('Error fetching data:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchData();
    }, []);

    // Handle category filter change
    const handleCategoryFilterChange = (categoryValue: string) => {
        setSelectedCategory(categoryValue);
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

        const monthName = newDate.toLocaleDateString('en-US', {
            month: 'long',
        });
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
            year: 'numeric',
        });
    };

    // Apply both category and month filters
    const applyFilters = (category: string, month: string) => {
        let filteredExpenses = allExpenses;

        // Apply category filter
        if (category !== 'All category') {
            filteredExpenses = filteredExpenses.filter(
                (expense) => expense.category.name === category,
            );
        }

        // Apply month filter based on start_date of recurring expense
        if (month && month !== `${currentMonth}, ${currentYear}`) {
            const [selectedMonthName, selectedYear] = month.split(', ');
            const monthIndex = new Date(
                Date.parse(selectedMonthName + ' 1, 2023'),
            ).getMonth();

            filteredExpenses = filteredExpenses.filter((expense) => {
                const expenseStartDate = new Date(expense.start_date);
                return (
                    expenseStartDate.getMonth() === monthIndex &&
                    expenseStartDate.getFullYear() === parseInt(selectedYear)
                );
            });
        }

        setMonthlyExpenses(filteredExpenses);
    };

    // Calculate total expense
    const totalExpense = monthlyExpenses.reduce(
        (sum, expense) => sum + expense.amount,
        0,
    );

    // Handle add expense
    const handleAddExpense = async (e: React.FormEvent) => {
        e.preventDefault();

        try {
            setAddExpenseForm((prev) => ({
                ...prev,
                processing: true,
                errors: {},
            }));

            const response = await fetch(
                `${getApiPrefix()}/monthly-expenses/expenses`,
                {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN':
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({
                        name: addExpenseForm.name,
                        description: addExpenseForm.description,
                        category_id: addExpenseForm.category_id,
                        amount: addExpenseForm.amount,
                        currency: addExpenseForm.currency,
                        start_date: addExpenseForm.start_date,
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
                description: '',
                category_id: '',
                amount: '',
                currency: 'BDT',
                start_date: new Date().toISOString().split('T')[0],
                processing: false,
                errors: {},
            });

            // Reload the data
            await fetchData();
        } catch (error) {
            console.error('Error adding expense:', error);
            setAddExpenseForm((prev) => ({
                ...prev,
                processing: false,
                errors: { general: 'Error adding expense. Please try again.' },
            }));
        }
    };

    // Handle edit expense
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
                `${getApiPrefix()}/monthly-expenses/expenses/${
                    editingExpense.id
                }`,
                {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN':
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content') || '',
                    },
                    body: JSON.stringify({
                        name: editExpenseForm.name,
                        description: editExpenseForm.description,
                        category_id: editExpenseForm.category_id,
                        amount: editExpenseForm.amount,
                        currency: editExpenseForm.currency,
                        start_date: editExpenseForm.start_date,
                        update_current_month: (document.querySelector('input[name="update_current_month"]') as HTMLInputElement)?.checked ?? true,
                        update_previous_months: (document.querySelector('input[name="update_previous_months"]') as HTMLInputElement)?.checked ?? false,
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
                throw new Error(errorData.error || 'Failed to update expense');
            }

            const successData = await response.json();

            setShowEditExpenseModal(false);
            setEditingExpense(null);
            setEditExpenseForm({
                name: '',
                description: '',
                category_id: '',
                amount: '',
                currency: 'BDT',
                start_date: '',
                processing: false,
                errors: {},
            });

            // Show success message with update details
            toast.success(successData.message || 'Expense updated successfully');

            // Reload the data
            await fetchData();
        } catch (error) {
            console.error('Error updating expense:', error);
            toast.error('Error updating expense. Please try again.');
            setEditExpenseForm((prev) => ({
                ...prev,
                processing: false,
                errors: {
                    general: 'Error updating expense. Please try again.',
                },
            }));
        }
    };

    // Handle delete expense
    const handleDeleteExpense = async (expense: RecurringExpense) => {
        if (!confirm(`Are you sure you want to delete "${expense.name}"?`)) {
            return;
        }

        try {
            const response = await fetch(
                `${getApiPrefix()}/monthly-expenses/expenses/${expense.id}`,
                {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN':
                            document
                                .querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content') || '',
                    },
                },
            );

            if (!response.ok) {
                throw new Error('Failed to delete expense');
            }

            // Reload the data
            await fetchData();
        } catch (error) {
            console.error('Error deleting expense:', error);
            toast.error('Error deleting expense. Please try again.');
        }
    };

    // Open edit modal
    const openEditModal = (expense: RecurringExpense) => {
        setEditingExpense(expense);
        setEditExpenseForm({
            name: expense.name,
            description: expense.description || '',
            category_id: expense.category.id.toString(),
            amount: expense.amount.toString(),
            currency: expense.currency,
            start_date: expense.start_date,
            processing: false,
            errors: {},
        });
        setShowEditExpenseModal(true);
    };

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
    }, [allExpenses]);

    return (
        <div className="">
            <Toaster position="top-right" />
            {/* Monthly Expenses Section */}
            <div>
                {/* Summary Card */}
                <div className="card rounded-lg shadow p-6 mb-6">
                    <div className="flex items-center justify-between">
                        <div>
                            <h3 className="text-lg font-semibold text-foreground">
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
                                    className="p-2 border border-border rounded-md hover:bg-muted/50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    title="Previous month"
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
                                            d="M15 19l-7-7 7-7"
                                        />
                                    </svg>
                                </button>

                                <div className="px-4 py-2 border border-border rounded-md card min-w-[160px] text-center font-medium text-foreground">
                                    {getDisplayMonthString()}
                                </div>

                                <button
                                    onClick={() => navigateMonth('next')}
                                    className="p-2 border border-border rounded-md hover:bg-muted/50 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    title="Next month"
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
                                className="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="All category">
                                    All category
                                </option>
                                {expenseCategories.map((category) => (
                                    <option
                                        key={category.id}
                                        value={category.name}
                                    >
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

                {/* Expenses Table */}
                <div className="card rounded-lg shadow overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-border">
                            <thead className="theme-table__head">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        EXPENSE
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        EXPENSE CATEGORY
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        TOTAL EXPENSE
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        STARTING DATE
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        OPTIONS
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
                                            <div className="flex flex-col items-center">
                                                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                                                <p className="mt-2 text-muted">
                                                    Loading expenses...
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                ) : (
                                    monthlyExpenses.map((expense, index) => (
                                        <tr
                                            key={expense.id}
                                            className="theme-table__row"
                                        >
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                                                {expense.name}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    className="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                                                    style={{
                                                        backgroundColor:
                                                            '#4285F4',
                                                    }}
                                                >
                                                    {expense.category.name}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                                                {expense.amount.toLocaleString()}
                                                tk
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-sm text-muted">
                                                {new Date(expense.start_date)
                                                    .toLocaleDateString(
                                                        'en-US',
                                                        {
                                                            day: 'numeric',
                                                            month: 'long',
                                                            year: 'numeric',
                                                        },
                                                    )
                                                    .replace(',', 'th')}
                                            </td>
                                            <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div className="flex items-center justify-end space-x-2">
                                                    <button
                                                        onClick={() =>
                                                            openEditModal(
                                                                expense,
                                                            )
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
                                {!loading && monthlyExpenses.length === 0 && (
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
                                        description: '',
                                        category_id: '',
                                        amount: '',
                                        currency: 'BDT',
                                        start_date: new Date()
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
                                {/* Expense Name */}
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
                                        value={addExpenseForm.start_date}
                                        onChange={(e) =>
                                            setAddExpenseForm((prev) => ({
                                                ...prev,
                                                start_date: e.target.value,
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
                    <div className="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                        {/* Modal Header */}
                        <div className="flex items-center justify-between p-6 border-b border-gray-100">
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
                                    <h2 className="text-xl font-bold text-gray-900">
                                        Edit Expense
                                    </h2>
                                    <p className="text-sm text-gray-500">
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
                                        description: '',
                                        category_id: '',
                                        amount: '',
                                        currency: 'BDT',
                                        start_date: '',
                                        processing: false,
                                        errors: {},
                                    });
                                }}
                                className="w-10 h-10 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors"
                            >
                                <svg
                                    className="w-5 h-5 text-gray-400"
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
                                {/* Expense Name */}
                                <div className="flex gap-16 items-center">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Name
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
                                        value={editExpenseForm.start_date}
                                        onChange={(e) =>
                                            setEditExpenseForm((prev) => ({
                                                ...prev,
                                                start_date: e.target.value,
                                            }))
                                        }
                                        required
                                        className="w-full px-4 py-3 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    />
                                </div>

                                {/* Sync Options */}
                                <div className="flex gap-16 items-start">
                                    <div className="w-24">
                                        <label className="block text-sm font-semibold text-foreground mb-2">
                                            Sync Options
                                        </label>
                                    </div>
                                    <div className="flex-1 space-y-3">
                                        <label className="flex items-center space-x-3 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                defaultChecked={true}
                                                className="w-4 h-4 text-blue-600 border-border rounded focus:ring-blue-500"
                                                name="update_current_month"
                                            />
                                            <span className="text-sm text-foreground">
                                                Update current month actual expense (recommended)
                                            </span>
                                        </label>
                                        <label className="flex items-center space-x-3 cursor-pointer">
                                            <input
                                                type="checkbox"
                                                defaultChecked={false}
                                                className="w-4 h-4 text-blue-600 border-border rounded focus:ring-blue-500"
                                                name="update_previous_months"
                                            />
                                            <span className="text-sm text-foreground">
                                                Also update previous months' expenses
                                            </span>
                                        </label>
                                    </div>
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
    root.render(<MonthlyExpensesPage />);
} else {
    console.error('Bizana monthly expenses app container not found');
}

export default MonthlyExpensesPage;
