import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import {
  MdRepeat,
  MdAdd,
  MdEdit,
  MdSearch,
  MdToggleOn,
  MdToggleOff,
  MdDelete,
  MdChevronLeft,
  MdChevronRight,
  MdPlayArrow,
  MdCalendarToday,
  MdAttachMoney,
  MdCategory,
  MdAccountBalance
} from 'react-icons/md';
import BizanaLayout from '../../layouts/BizanaLayout';

interface MonthlyExpensesProps {
  monthlyExpenses: {
    data: Array<{
      id: number;
      reference_number: string;
      name: string;
      description: string;
      amount: number;
      currency: string;
      category?: {
        id: number;
        name: string;
      };
      chart_account?: {
        id: number;
        name: string;
        code: string;
      };
      recurrence_pattern: string;
      recurrence_interval: number;
      pattern_description: string;
      start_date: string;
      end_date?: string;
      next_date?: string;
      day_of_month?: number;
      day_of_week?: number;
      week_of_month?: number;
      is_active: boolean;
      auto_approve: boolean;
      total_generated: number;
      last_generated_at?: string;
      created_at: string;
      status: string;
      recent_instances: Array<{
        id: number;
        reference_number: string;
        expense_date: string;
        amount: number;
        status: string;
      }>;
    }>;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
  };
  filters: {
    search: string;
    status: string;
    category_id: string;
    recurrence_pattern: string;
  };
  filterOptions: {
    categories: Array<{
      id: number;
      name: string;
    }>;
    chartAccounts: Array<{
      id: number;
      name: string;
      code: string;
    }>;
    patterns: Array<{
      value: string;
      label: string;
    }>;
  };
  summary: {
    total: number;
    active: number;
    inactive: number;
    due_today: number;
    monthly_total: number;
  };
}

function MonthlyExpenses({
  monthlyExpenses,
  filters,
  filterOptions,
  summary
}: MonthlyExpensesProps) {
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [showEditForm, setShowEditForm] = useState(false);
  const [editingExpense, setEditingExpense] = useState<any>(null);
  const [searchTerm, setSearchTerm] = useState(filters.search);
  const [statusFilter, setStatusFilter] = useState(filters.status);
  const [categoryFilter, setCategoryFilter] = useState(filters.category_id);
  const [patternFilter, setPatternFilter] = useState(filters.recurrence_pattern);

  const handleSearch = (value: string) => {
    setSearchTerm(value);
    updateFilters({ search: value });
  };

  const updateFilters = (newFilters: any) => {
    const params = new URLSearchParams(window.location.search);
    Object.entries(newFilters).forEach(([key, value]) => {
      if (value) {
        params.set(key, value as string);
      } else {
        params.delete(key);
      }
    });
    router.get(`${window.location.pathname}?${params.toString()}`);
  };

  const handleCreateExpense = (e: React.FormEvent) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);

    router.post('/admin/api/bizana/recurring-expenses', formData, {
      onSuccess: () => {
        setShowCreateForm(false);
        (e.target as HTMLFormElement).reset();
        router.reload();
      },
    });
  };

  const handleUpdateExpense = (e: React.FormEvent) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);

    router.put(`/admin/api/bizana/recurring-expenses/${editingExpense.id}`, formData, {
      onSuccess: () => {
        setShowEditForm(false);
        setEditingExpense(null);
        router.reload();
      },
    });
  };

  const toggleExpenseStatus = (expense: any) => {
    router.post(`/admin/api/bizana/recurring-expenses/${expense.id}/toggle-status`, {}, {
      onSuccess: () => {
        router.reload();
      },
    });
  };

  const generateExpenseNow = (expense: any) => {
    if (confirm('Are you sure you want to generate an expense instance now?')) {
      router.post(`/admin/api/bizana/recurring-expenses/${expense.id}/generate-now`, {}, {
        onSuccess: () => {
          router.reload();
        },
      });
    }
  };

  const deleteExpense = (expense: any) => {
    if (confirm('Are you sure you want to delete this recurring expense template?')) {
      router.delete(`/admin/api/bizana/recurring-expenses/${expense.id}`, {}, {
        onSuccess: () => {
          router.reload();
        },
      });
    }
  };

  const openEditForm = (expense: any) => {
    setEditingExpense(expense);
    setShowEditForm(true);
  };

  const RecurrenceForm = ({ expense, isEdit = false }: { expense?: any; isEdit?: boolean }) => {
    const [pattern, setPattern] = useState(expense?.recurrence_pattern || 'monthly');

    return (
      <form onSubmit={isEdit ? handleUpdateExpense : handleCreateExpense} className="p-6">
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Name *
            </label>
            <input
              type="text"
              name="name"
              defaultValue={expense?.name || ''}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              placeholder="e.g., Office Rent"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Amount *
            </label>
            <input
              type="number"
              name="amount"
              defaultValue={expense?.amount || ''}
              step="0.01"
              min="0"
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              placeholder="0.00"
            />
          </div>
        </div>

        <div className="mt-6">
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Description
          </label>
          <textarea
            name="description"
            rows={3}
            defaultValue={expense?.description || ''}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            placeholder="Optional description for this recurring expense"
          />
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Expense Category *
            </label>
            <select
              name="category_id"
              defaultValue={expense?.category?.id || ''}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Select a category</option>
              {filterOptions.categories.map((category) => (
                <option key={category.id} value={category.id}>
                  {category.name}
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Chart Account *
            </label>
            <select
              name="chart_account_id"
              defaultValue={expense?.chart_account?.id || ''}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Select an account</option>
              {filterOptions.chartAccounts.map((account) => (
                <option key={account.id} value={account.id}>
                  {account.code} - {account.name}
                </option>
              ))}
            </select>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Recurrence Pattern *
            </label>
            <select
              name="recurrence_pattern"
              value={pattern}
              onChange={(e) => setPattern(e.target.value)}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              {filterOptions.patterns.map((p) => (
                <option key={p.value} value={p.value}>
                  {p.label}
                </option>
              ))}
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Interval *
            </label>
            <input
              type="number"
              name="recurrence_interval"
              defaultValue={expense?.recurrence_interval || 1}
              min="1"
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Payment Method *
            </label>
            <select
              name="payment_method"
              defaultValue={expense?.payment_method || 'bank_transfer'}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="cash">Cash</option>
              <option value="bank_transfer">Bank Transfer</option>
              <option value="credit_card">Credit Card</option>
              <option value="debit_card">Debit Card</option>
              <option value="check">Check</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>

        {/* Pattern-specific options */}
        {(pattern === 'monthly' || pattern === 'quarterly' || pattern === 'yearly') && (
          <div className="mt-6">
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Day of Month
            </label>
            <input
              type="number"
              name="day_of_month"
              defaultValue={expense?.day_of_month || ''}
              min="1"
              max="31"
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              placeholder="e.g., 15 for 15th of each month"
            />
          </div>
        )}

        {(pattern === 'weekly' || pattern === 'bi_weekly') && (
          <div className="mt-6">
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Day of Week
            </label>
            <select
              name="day_of_week"
              defaultValue={expense?.day_of_week || ''}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="">Select day</option>
              <option value="0">Sunday</option>
              <option value="1">Monday</option>
              <option value="2">Tuesday</option>
              <option value="3">Wednesday</option>
              <option value="4">Thursday</option>
              <option value="5">Friday</option>
              <option value="6">Saturday</option>
            </select>
          </div>
        )}

        <div className="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Start Date *
            </label>
            <input
              type="date"
              name="start_date"
              defaultValue={expense?.start_date || ''}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              End Date
            </label>
            <input
              type="date"
              name="end_date"
              defaultValue={expense?.end_date || ''}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Currency *
            </label>
            <select
              name="currency"
              defaultValue={expense?.currency || 'USD'}
              required
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            >
              <option value="USD">USD</option>
              <option value="EUR">EUR</option>
              <option value="GBP">GBP</option>
              <option value="BDT">BDT</option>
            </select>
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-2">
              Payment Reference
            </label>
            <input
              type="text"
              name="payment_reference"
              defaultValue={expense?.payment_reference || ''}
              className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              placeholder="Optional reference number"
            />
          </div>
        </div>

        <div className="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
          <div className="flex items-center">
            <input
              type="checkbox"
              name="is_active"
              defaultChecked={expense?.is_active ?? true}
              className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label className="ml-2 block text-sm text-gray-700">
              Active
            </label>
          </div>
          <div className="flex items-center">
            <input
              type="checkbox"
              name="auto_approve"
              defaultChecked={expense?.auto_approve ?? false}
              className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label className="ml-2 block text-sm text-gray-700">
              Auto Approve
            </label>
          </div>
          <div className="flex items-center">
            <input
              type="checkbox"
              name="is_tax_deductible"
              defaultChecked={expense?.is_tax_deductible ?? true}
              className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
            />
            <label className="ml-2 block text-sm text-gray-700">
              Tax Deductible
            </label>
          </div>
        </div>

        <div className="mt-6 flex justify-end space-x-3">
          <button
            type="button"
            onClick={() => {
              isEdit ? setShowEditForm(false) : setShowCreateForm(false);
              setEditingExpense(null);
            }}
            className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
          >
            Cancel
          </button>
          <button
            type="submit"
            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
          >
            {isEdit ? 'Update Expense' : 'Create Expense'}
          </button>
        </div>
      </form>
    );
  };

  return (
    <div className="min-h-screen bg-gray-50 p-6">
      {/* Header */}
      <div className="mb-8">
        <h1 className="text-3xl font-bold text-gray-900 flex items-center">
          <MdRepeat className="mr-3" />
          Recurring Expenses
        </h1>
        <p className="text-gray-600 mt-2">
          Manage recurring expense templates for automated expense generation
        </p>
      </div>

      {/* Summary Cards */}
      <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="flex-shrink-0 bg-blue-100 rounded-md p-3">
              <MdRepeat className="h-6 w-6 text-blue-600" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-gray-500">Total Templates</p>
              <p className="text-2xl font-bold text-gray-900">{summary.total}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="flex-shrink-0 bg-green-100 rounded-md p-3">
              <MdToggleOn className="h-6 w-6 text-green-600" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-gray-500">Active</p>
              <p className="text-2xl font-bold text-gray-900">{summary.active}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="flex-shrink-0 bg-red-100 rounded-md p-3">
              <MdToggleOff className="h-6 w-6 text-red-600" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-gray-500">Inactive</p>
              <p className="text-2xl font-bold text-gray-900">{summary.inactive}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="flex-shrink-0 bg-yellow-100 rounded-md p-3">
              <MdCalendarToday className="h-6 w-6 text-yellow-600" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-gray-500">Due Today</p>
              <p className="text-2xl font-bold text-gray-900">{summary.due_today}</p>
            </div>
          </div>
        </div>
        <div className="bg-white rounded-lg shadow p-6">
          <div className="flex items-center">
            <div className="flex-shrink-0 bg-purple-100 rounded-md p-3">
              <MdAttachMoney className="h-6 w-6 text-purple-600" />
            </div>
            <div className="ml-4">
              <p className="text-sm font-medium text-gray-500">Monthly Total</p>
              <p className="text-2xl font-bold text-gray-900">
                ${typeof summary.monthly_total === 'number' ? summary.monthly_total.toFixed(2) : parseFloat(summary.monthly_total || 0).toFixed(2)}
              </p>
            </div>
          </div>
        </div>
      </div>

      {/* Create Expense Button */}
      <div className="mb-6 flex justify-between items-center">
        <button
          onClick={() => setShowCreateForm(!showCreateForm)}
          className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
        >
          <MdAdd className="mr-2" />
          Create New Recurring Expense
        </button>
      </div>

      {/* Create/Edit Form */}
      {(showCreateForm || showEditForm) && (
        <div className="bg-white rounded-lg shadow mb-6">
          <div className="px-6 py-4 border-b border-gray-200">
            <h3 className="text-lg font-medium text-gray-900">
              {showEditForm ? 'Edit Recurring Expense' : 'Create New Recurring Expense'}
            </h3>
          </div>
          <RecurrenceForm expense={editingExpense} isEdit={showEditForm} />
        </div>
      )}

      {/* Filters */}
      <div className="bg-white rounded-lg shadow mb-6 p-6">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div className="flex items-center">
            <MdSearch className="text-gray-500 mr-3" />
            <input
              type="text"
              value={searchTerm}
              onChange={(e) => handleSearch(e.target.value)}
              placeholder="Search expenses..."
              className="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
            />
          </div>
          <select
            value={statusFilter}
            onChange={(e) => {
              setStatusFilter(e.target.value);
              updateFilters({ status: e.target.value });
            }}
            className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
          <select
            value={categoryFilter}
            onChange={(e) => {
              setCategoryFilter(e.target.value);
              updateFilters({ category_id: e.target.value });
            }}
            className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">All Categories</option>
            {filterOptions.categories.map((category) => (
              <option key={category.id} value={category.id}>
                {category.name}
              </option>
            ))}
          </select>
          <select
            value={patternFilter}
            onChange={(e) => {
              setPatternFilter(e.target.value);
              updateFilters({ recurrence_pattern: e.target.value });
            }}
            className="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
          >
            <option value="">All Patterns</option>
            {filterOptions.patterns.map((pattern) => (
              <option key={pattern.value} value={pattern.value}>
                {pattern.label}
              </option>
            ))}
          </select>
        </div>
      </div>

      {/* Expenses Table */}
      <div className="bg-white rounded-lg shadow overflow-hidden">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Name
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Amount
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Category
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Pattern
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Next Date
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Generated
                </th>
                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {monthlyExpenses.data.map((expense) => (
                <tr key={expense.id} className="hover:bg-gray-50">
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div className="text-sm font-medium text-gray-900">{expense.name}</div>
                      <div className="text-sm text-gray-500">{expense.reference_number}</div>
                      {expense.description && (
                        <div className="text-xs text-gray-400 mt-1">{expense.description}</div>
                      )}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm font-medium text-gray-900">
                      {expense.currency} {typeof expense.amount === 'number' ? expense.amount.toFixed(2) : parseFloat(expense.amount || 0).toFixed(2)}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm text-gray-600">
                      {expense.category?.name || '-'}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm text-gray-600">
                      {expense.pattern_description}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm text-gray-600">
                      {expense.next_date ? new Date(expense.next_date).toLocaleDateString() : '-'}
                    </div>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <div className="text-sm text-gray-600">
                      {expense.total_generated} instances
                    </div>
                    {expense.last_generated_at && (
                      <div className="text-xs text-gray-400">
                        Last: {new Date(expense.last_generated_at).toLocaleDateString()}
                      </div>
                    )}
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap">
                    <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                      expense.is_active
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    }`}>
                      {expense.status}
                    </span>
                  </td>
                  <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <div className="flex justify-end space-x-2">
                      {expense.is_active && (
                        <button
                          onClick={() => generateExpenseNow(expense)}
                          className="text-green-600 hover:text-green-900"
                          title="Generate Now"
                        >
                          <MdPlayArrow className="w-5 h-5" />
                        </button>
                      )}
                      <button
                        onClick={() => toggleExpenseStatus(expense)}
                        className="text-gray-400 hover:text-gray-600"
                        title={expense.is_active ? 'Deactivate' : 'Activate'}
                      >
                        {expense.is_active ? (
                          <MdToggleOn className="w-5 h-5 text-green-600" />
                        ) : (
                          <MdToggleOff className="w-5 h-5 text-red-600" />
                        )}
                      </button>
                      <button
                        onClick={() => openEditForm(expense)}
                        className="text-blue-600 hover:text-blue-900"
                        title="Edit"
                      >
                        <MdEdit className="w-4 h-4" />
                      </button>
                      <button
                        onClick={() => deleteExpense(expense)}
                        className="text-red-600 hover:text-red-900"
                        title="Delete"
                      >
                        <MdDelete className="w-4 h-4" />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Empty State */}
        {monthlyExpenses.data.length === 0 && (
          <div className="px-6 py-12 text-center">
            <MdRepeat className="mx-auto h-12 w-12 text-gray-400" />
            <h3 className="mt-2 text-sm font-medium text-gray-900">No recurring expenses found</h3>
            <p className="mt-1 text-sm text-gray-500">
              Get started by creating your first recurring expense template.
            </p>
            <div className="mt-6">
              <button
                onClick={() => setShowCreateForm(true)}
                className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
              >
                <MdAdd className="mr-2" />
                New Recurring Expense
              </button>
            </div>
          </div>
        )}

        {/* Pagination */}
        {monthlyExpenses.data.length > 0 && (
          <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div className="flex-1 flex justify-between sm:hidden">
              {monthlyExpenses.links.map((link, index) => (
                link.url && (
                  <a
                    key={index}
                    href={link.url}
                    className={`relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md ${
                      link.active ? 'bg-blue-50 text-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50'
                    }`}
                  >
                    {link.label}
                  </a>
                )
              ))}
            </div>
            <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p className="text-sm text-gray-700">
                  Showing <span className="font-medium">{(monthlyExpenses.current_page - 1) * monthlyExpenses.per_page + 1}</span> to{' '}
                  <span className="font-medium">
                    {Math.min(monthlyExpenses.current_page * monthlyExpenses.per_page, monthlyExpenses.total)}
                  </span>{' '}
                  of <span className="font-medium">{monthlyExpenses.total}</span> results
                </p>
              </div>
              <div>
                <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  {monthlyExpenses.links.map((link, index) => (
                    link.url && (
                      <a
                        key={index}
                        href={link.url}
                        dangerouslySetInnerHTML={{ __html: link.label }}
                        className={`relative inline-flex items-center px-3 py-2 border text-sm font-medium ${
                          link.active
                            ? 'z-10 bg-blue-50 border-blue-500 text-blue-600'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                        } ${
                          link.label.includes('Previous') || link.label.includes('Next')
                            ? 'rounded-l-md rounded-r-md'
                            : ''
                        }`}
                      />
                    )
                  ))}
                </nav>
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

MonthlyExpenses.layout = (page: React.ReactElement) => (
  <BizanaLayout title="MonthlyExpenses">{page}</BizanaLayout>
);

export default MonthlyExpenses;
