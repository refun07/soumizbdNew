import React from 'react';
import { Head, Link } from '@inertiajs/react';
import BizanaLayout from '../../layouts/BizanaLayout';
import {
  MdAccountBalance,
  MdWarning,
  MdCheckCircle,
  MdError,
  MdTrendingUp,
  MdMonetizationOn,
  MdReceiptLong,
  MdAssessment
} from 'react-icons/md';

interface DashboardProps {
  systemHealth: {
    status: 'healthy' | 'warning' | 'error';
    accountTypes: number;
    chartOfAccounts: number;
    openPeriods: number;
  };
  missingData: Array<{
    type: 'error' | 'warning';
    message: string;
    action: string;
  }>;
  summary: {
    totalAccounts: number;
    activeAccounts: number;
    totalJournalEntries: number;
    openPeriods: number;
    expenseCategories: number;
    taxSettings: number;
  };
  recentEntries: Array<{
    id: number;
    reference_number: string;
    date: string;
    description: string;
    status: string;
    total_amount: number;
  }>;
}

function AccountingDashboard({
  systemHealth,
  missingData,
  summary,
  recentEntries
}: DashboardProps) {
  const getHealthIcon = () => {
    switch (systemHealth.status) {
      case 'healthy':
        return <MdCheckCircle className="w-6 h-6 text-green-500" />;
      case 'warning':
        return <MdWarning className="w-6 h-6 text-yellow-500" />;
      case 'error':
        return <MdError className="w-6 h-6 text-red-500" />;
      default:
        return <MdAssessment className="w-6 h-6 text-gray-500" />;
    }
  };

  const getHealthColor = () => {
    switch (systemHealth.status) {
      case 'healthy':
        return 'bg-green-100 border-green-400 text-green-700';
      case 'warning':
        return 'bg-yellow-100 border-yellow-400 text-yellow-700';
      case 'error':
        return 'bg-red-100 border-red-400 text-red-700';
      default:
        return 'bg-gray-100 border-gray-400 text-gray-700';
    }
  };

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount);
  };

  return (
    <div className="min-h-screen bg-gray-50 p-6">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 flex items-center">
            <MdAccountBalance className="mr-3" />
            Accounting Dashboard
          </h1>
          <p className="text-gray-600 mt-2">
            Monitor and manage your accounting system setup and health
          </p>
        </div>

        {/* System Health Alert */}
        <div className={`border-l-4 p-4 mb-6 rounded ${getHealthColor()}`}>
          <div className="flex items-center">
            {getHealthIcon()}
            <div className="ml-3">
              <h3 className="text-lg font-medium">
                System Status: {systemHealth.status.charAt(0).toUpperCase() + systemHealth.status.slice(1)}
              </h3>
              <p className="text-sm mt-1">
                {systemHealth.status === 'healthy'
                  ? 'Your accounting system is properly configured and running smoothly.'
                  : 'Some issues detected. Please review the alerts below and take necessary actions.'
                }
              </p>
            </div>
          </div>
        </div>

        {/* Missing Data Alerts */}
        {missingData.length > 0 && (
          <div className="mb-6">
            <h3 className="text-lg font-medium text-gray-900 mb-3">System Alerts</h3>
            <div className="space-y-2">
              {missingData.map((alert, index) => (
                <div
                  key={index}
                  className={`p-4 rounded-lg border ${
                    alert.type === 'error'
                      ? 'bg-red-50 border-red-200'
                      : 'bg-yellow-50 border-yellow-200'
                  }`}
                >
                  <div className="flex items-start">
                    {alert.type === 'error' ? (
                      <MdError className="w-5 h-5 text-red-600 mt-0.5" />
                    ) : (
                      <MdWarning className="w-5 h-5 text-yellow-600 mt-0.5" />
                    )}
                    <p className="ml-3 text-gray-900">{alert.message}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Summary Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6 mb-8">
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <MdMonetizationOn className="w-8 h-8 text-blue-500" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Total Accounts</p>
                <p className="text-2xl font-bold text-gray-900">{summary.totalAccounts}</p>
                <p className="text-xs text-gray-500">{summary.activeAccounts} active</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <MdReceiptLong className="w-8 h-8 text-green-500" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Journal Entries</p>
                <p className="text-2xl font-bold text-gray-900">{summary.totalJournalEntries}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <MdTrendingUp className="w-8 h-8 text-purple-500" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Open Periods</p>
                <p className="text-2xl font-bold text-gray-900">{summary.openPeriods}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <MdAccountBalance className="w-8 h-8 text-indigo-500" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Account Types</p>
                <p className="text-2xl font-bold text-gray-900">{systemHealth.accountTypes}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <MdMonetizationOn className="w-8 h-8 text-orange-500" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Chart of Accounts</p>
                <p className="text-2xl font-bold text-gray-900">{systemHealth.chartOfAccounts}</p>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center">
              <MdAssessment className="w-8 h-8 text-teal-500" />
              <div className="ml-4">
                <p className="text-sm font-medium text-gray-600">Expense Categories</p>
                <p className="text-2xl font-bold text-gray-900">{summary.expenseCategories}</p>
              </div>
            </div>
          </div>
        </div>

        {/* Quick Actions & Recent Entries */}
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
          {/* Quick Actions */}
          <div className="lg:col-span-1">
            <div className="bg-white rounded-lg shadow p-6">
              <h3 className="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
              <div className="space-y-3">
                <Link
                  href="/admin/bizana/accounting/chart-of-accounts"
                  className="block w-full text-left px-4 py-3 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors"
                >
                  <div className="flex items-center">
                    <MdMonetizationOn className="mr-3" />
                    Manage Chart of Accounts
                  </div>
                </Link>

                <Link
                  href="/admin/bizana/accounting/financial-periods"
                  className="block w-full text-left px-4 py-3 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors"
                >
                  <div className="flex items-center">
                    <MdReceiptLong className="mr-3" />
                    Setup Financial Periods
                  </div>
                </Link>

                <Link
                  href="/admin/bizana/accounting/system-diagnostics"
                  className="block w-full text-left px-4 py-3 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors"
                >
                  <div className="flex items-center">
                    <MdAssessment className="mr-3" />
                    Run System Diagnostics
                  </div>
                </Link>

                <Link
                  href="/admin/bizana/accounting/expense-categories"
                  className="block w-full text-left px-4 py-3 bg-orange-50 text-orange-700 rounded-lg hover:bg-orange-100 transition-colors"
                >
                  <div className="flex items-center">
                    <MdMonetizationOn className="mr-3" />
                    Configure Expense Categories
                  </div>
                </Link>
              </div>
            </div>
          </div>

          {/* Recent Journal Entries */}
          <div className="lg:col-span-2">
            <div className="bg-white rounded-lg shadow">
              <div className="px-6 py-4 border-b border-gray-200">
                <h3 className="text-lg font-medium text-gray-900">Recent Journal Entries</h3>
              </div>
              <div className="overflow-hidden">
                {recentEntries.length > 0 ? (
                  <div className="divide-y divide-gray-200">
                    {recentEntries.map((entry) => (
                      <div key={entry.id} className="px-6 py-4 hover:bg-gray-50">
                        <div className="flex items-center justify-between">
                          <div className="flex-1">
                            <div className="flex items-center">
                              <span className="text-sm font-medium text-gray-900">
                                #{entry.reference_number}
                              </span>
                              <span className={`ml-2 px-2 py-1 text-xs rounded-full ${
                                entry.status === 'posted'
                                  ? 'bg-green-100 text-green-800'
                                  : 'bg-yellow-100 text-yellow-800'
                              }`}>
                                {entry.status}
                              </span>
                            </div>
                            <p className="text-sm text-gray-600 mt-1">{entry.description}</p>
                            <div className="flex items-center mt-2 text-sm text-gray-500">
                              <span>{entry.date}</span>
                              <span className="mx-2">•</span>
                              <span className="font-medium">{formatCurrency(entry.total_amount)}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    ))}
                  </div>
                ) : (
                  <div className="px-6 py-8 text-center">
                    <MdReceiptLong className="mx-auto h-12 w-12 text-gray-400" />
                    <p className="mt-2 text-sm text-gray-500">No journal entries found</p>
                    <p className="text-xs text-gray-400 mt-1">Start by creating your first journal entry</p>
                  </div>
                )}
              </div>
            </div>
          </div>
        </div>
      </div>
  );
}

AccountingDashboard.layout = (page: React.ReactElement) => (
  <BizanaLayout title="Accounting Dashboard">{page}</BizanaLayout>
);

export default AccountingDashboard;