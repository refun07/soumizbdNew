import React, { useState } from 'react';
import { Head, Link, router, useForm } from '@inertiajs/react';
import {
  MdAssessment,
  MdCheckCircle,
  MdWarning,
  MdError,
  MdMonetizationOn,
  MdReceiptLong,
  MdTrendingUp,
  MdAccountBalance,
  MdRefresh,
  MdInfo,
  MdAddCircle
} from 'react-icons/md';
import BizanaLayout from '../../layouts/BizanaLayout';

interface SystemDiagnosticsProps {
  diagnostics: {
    accountTypes: {
      count: number;
      required: number;
      status: 'healthy' | 'warning' | 'error';
      details: Array<{
        name: string;
        accounts_count: number;
      }>;
    };
    chartOfAccounts: {
      count: number;
      required: number;
      status: 'healthy' | 'warning' | 'error';
      active_count: number;
    };
    financialPeriods: {
      count: number;
      current: number;
      status: 'healthy' | 'warning' | 'error';
      open_periods: Array<{
        name: string;
        start_date: string;
        end_date: string;
      }>;
    };
    expenseCategories: {
      count: number;
      required: number;
      status: 'healthy' | 'warning' | 'error';
      active_count: number;
    };
    taxSettings: {
      count: number;
      required: number;
      status: 'healthy' | 'warning' | 'error';
      active_count: number;
    };
    journalEntries: {
      count: number;
      balanced: boolean;
      status: 'healthy' | 'warning' | 'error';
    };
  };
}

function SystemDiagnostics({
  diagnostics
}: SystemDiagnosticsProps) {
    const [confirmationDialog, setConfirmationDialog] = useState<{
    type: string;
    title: string;
    description: string;
    onConfirm: () => void;
  } | null>(null);
  const [notification, setNotification] = useState<{
    type: 'success' | 'error';
    message: string;
    details?: string[];
  } | null>(null);

  const getStatusIcon = (status: string) => {
    switch (status) {
      case 'healthy':
        return <MdCheckCircle className="w-6 h-6 text-green-500" />;
      case 'warning':
        return <MdWarning className="w-6 h-6 text-yellow-500" />;
      case 'error':
        return <MdError className="w-6 h-6 text-red-500" />;
      default:
        return <MdInfo className="w-6 h-6 text-gray-500" />;
    }
  };

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'healthy':
        return 'bg-green-50 border-green-200 text-green-800';
      case 'warning':
        return 'bg-yellow-50 border-yellow-200 text-yellow-800';
      case 'error':
        return 'bg-red-50 border-red-200 text-red-800';
      default:
        return 'bg-gray-50 border-gray-200 text-gray-800';
    }
  };

  const getProgressColor = (current: number, required: number) => {
    const percentage = (current / required) * 100;
    if (percentage >= 100) return 'bg-green-500';
    if (percentage >= 50) return 'bg-yellow-500';
    return 'bg-red-500';
  };

  // Inertia forms for data creation
  const accountTypesForm = useForm({
    _token: ''
  });

  const chartOfAccountsForm = useForm({
    _token: ''
  });

  const financialPeriodsForm = useForm({
    _token: ''
  });

  const expenseCategoriesForm = useForm({
    _token: ''
  });

  const taxSettingsForm = useForm({
    _token: ''
  });

  const createData = (type: string, endpoint: string, form: any) => {
    form.post(endpoint, {
      onSuccess: (page: any) => {
        const flash = page.props.flash || {};
        if (flash.success) {
          setNotification({
            type: 'success',
            message: flash.success,
            details: flash.details || []
          });
        }
        // Refresh the page to show updated diagnostics
        setTimeout(() => {
          router.reload();
        }, 2000);
      },
      onError: (errors: any) => {
        setNotification({
          type: 'error',
          message: errors.message || 'Failed to create data. Please try again.'
        });
      },
      onFinish: () => {
        // Form processing automatically stops here
      }
    });
  };

  const handleCreateData = (type: string) => {
    let title = '';
    let description = '';
    let endpoint = '';
    let form: any = null;

    switch (type) {
      case 'accountTypes':
        title = 'Create Account Types';
        description = 'This will create the essential account types (Assets, Liabilities, Equity, Revenue, Expenses) needed for your accounting system.';
        endpoint = '/admin/bizana/accounting/create-account-types';
        form = accountTypesForm;
        break;
      case 'chartOfAccounts':
        title = 'Create Chart of Accounts';
        description = 'This will create a comprehensive set of default accounts (Cash, Accounts Receivable, Accounts Payable, Revenue, Expense accounts, etc.).';
        endpoint = '/admin/bizana/accounting/create-chart-accounts';
        form = chartOfAccountsForm;
        break;
      case 'financialPeriods':
        title = 'Create Financial Periods';
        description = 'This will create current and next month financial periods, allowing you to record transactions.';
        endpoint = '/admin/bizana/accounting/create-financial-periods';
        form = financialPeriodsForm;
        break;
      case 'expenseCategories':
        title = 'Create Expense Categories';
        description = 'This will create essential expense categories (Rent, Utilities, Marketing, etc.) for better expense tracking.';
        endpoint = '/admin/bizana/accounting/create-expense-categories';
        form = expenseCategoriesForm;
        break;
      case 'taxSettings':
        title = 'Create Tax Settings';
        description = 'This will create a default sales tax setting (8%) for proper tax calculation.';
        endpoint = '/admin/bizana/accounting/create-tax-settings';
        form = taxSettingsForm;
        break;
    }

    setConfirmationDialog({
      type,
      title,
      description,
      onConfirm: () => {
        setConfirmationDialog(null);
        createData(type, endpoint, form);
      }
    });
  };

  const getRecommendations = () => {
    const recommendations = [];

    if (diagnostics.accountTypes.status !== 'healthy') {
      recommendations.push({
        type: 'error',
        title: 'Account Types Setup Required',
        description: 'You need to set up all basic account types (Assets, Liabilities, Equity, Revenue, Expenses).',
        action: 'Manage Account Types',
        href: '/admin/bizana/accounting/account-types'
      });
    }

    if (diagnostics.chartOfAccounts.status !== 'healthy') {
      recommendations.push({
        type: 'warning',
        title: 'Expand Chart of Accounts',
        description: 'Consider adding more accounts to have a comprehensive chart of accounts.',
        action: 'Manage Chart of Accounts',
        href: '/admin/bizana/accounting/chart-of-accounts'
      });
    }

    if (diagnostics.financialPeriods.status !== 'healthy') {
      recommendations.push({
        type: 'error',
        title: 'Create Open Financial Period',
        description: 'You need at least one open financial period to record transactions.',
        action: 'Manage Financial Periods',
        href: '/admin/bizana/accounting/financial-periods'
      });
    }

    if (diagnostics.expenseCategories.status !== 'healthy') {
      recommendations.push({
        type: 'warning',
        title: 'Add Expense Categories',
        description: 'Set up expense categories for better expense tracking and reporting.',
        action: 'Manage Expense Categories',
        href: '/admin/bizana/accounting/expense-categories'
      });
    }

    if (diagnostics.taxSettings.status !== 'healthy') {
      recommendations.push({
        type: 'warning',
        title: 'Configure Tax Settings',
        description: 'Set up tax rates and settings for proper tax calculation.',
        action: 'Manage Tax Settings',
        href: '/admin/bizana/accounting/tax-settings'
      });
    }

    if (!diagnostics.journalEntries.balanced) {
      recommendations.push({
        type: 'error',
        title: 'Fix Journal Entry Balance',
        description: 'Some journal entries are not balanced. Please review and fix them.',
        action: 'Review Journal Entries',
        href: '/admin/bizana/accounting'
      });
    }

    return recommendations;
  };

  const overallHealth = Object.values(diagnostics).every(d => d.status === 'healthy') ? 'healthy' :
                        Object.values(diagnostics).some(d => d.status === 'error') ? 'error' : 'warning';

  const recommendations = getRecommendations();

  return (
    <div className="min-h-screen bg-gray-50 p-6">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 flex items-center">
            <MdAssessment className="mr-3" />
            System Diagnostics
          </h1>
          <p className="text-gray-600 mt-2">
            Check the health and status of your accounting system
          </p>
        </div>

        {/* Overall Health Status */}
        <div className={`border-l-4 p-6 mb-8 rounded-lg ${getStatusColor(overallHealth)}`}>
          <div className="flex items-center">
            {getStatusIcon(overallHealth)}
            <div className="ml-4">
              <h3 className="text-lg font-medium">
                Overall System Status: {overallHealth.charAt(0).toUpperCase() + overallHealth.slice(1)}
              </h3>
              <p className="text-sm mt-1">
                {overallHealth === 'healthy'
                  ? 'Your accounting system is properly configured and ready for use.'
                  : overallHealth === 'error'
                  ? 'Critical issues found. Please address them immediately.'
                  : 'Some improvements recommended for optimal performance.'
                }
              </p>
            </div>
            <button className="ml-auto">
              <MdRefresh className="w-6 h-6 text-gray-400 hover:text-gray-600 cursor-pointer" />
            </button>
          </div>
        </div>

        {/* System Status Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          {/* Account Types */}
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center mb-4">
              <MdAccountBalance className="w-6 h-6 text-indigo-500 mr-2" />
              <h3 className="text-lg font-medium text-gray-900">Account Types</h3>
            </div>
            <div className="flex items-center justify-between mb-2">
              <span className="text-sm text-gray-600">Count</span>
              <span className="font-medium">{diagnostics.accountTypes.count}/{diagnostics.accountTypes.required}</span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-2 mb-4">
              <div
                className={`h-2 rounded-full ${getProgressColor(diagnostics.accountTypes.count, diagnostics.accountTypes.required)}`}
                style={{ width: `${Math.min(100, (diagnostics.accountTypes.count / diagnostics.accountTypes.required) * 100)}%` }}
              />
            </div>
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                {getStatusIcon(diagnostics.accountTypes.status)}
                <span className="ml-2 text-sm font-medium capitalize">{diagnostics.accountTypes.status}</span>
              </div>
              {diagnostics.accountTypes.status !== 'healthy' && (
                <button
                  onClick={() => handleCreateData('accountTypes')}
                  disabled={accountTypesForm.processing}
                  className="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-indigo-700 bg-indigo-100 hover:bg-indigo-200 disabled:opacity-50"
                >
                  {accountTypesForm.processing ? (
                    <span className="w-4 h-4 mr-1 inline-block animate-spin">⟳</span>
                  ) : (
                    <MdAddCircle className="w-4 h-4 mr-1" />
                  )}
                  Create Missing
                </button>
              )}
            </div>
          </div>

          {/* Chart of Accounts */}
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center mb-4">
              <MdMonetizationOn className="w-6 h-6 text-blue-500 mr-2" />
              <h3 className="text-lg font-medium text-gray-900">Chart of Accounts</h3>
            </div>
            <div className="flex items-center justify-between mb-2">
              <span className="text-sm text-gray-600">Total Accounts</span>
              <span className="font-medium">{diagnostics.chartOfAccounts.count}</span>
            </div>
            <div className="flex items-center justify-between mb-4">
              <span className="text-sm text-gray-600">Active</span>
              <span className="font-medium">{diagnostics.chartOfAccounts.active_count}</span>
            </div>
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                {getStatusIcon(diagnostics.chartOfAccounts.status)}
                <span className="ml-2 text-sm font-medium capitalize">{diagnostics.chartOfAccounts.status}</span>
              </div>
              {diagnostics.chartOfAccounts.status !== 'healthy' && (
                <button
                  onClick={() => handleCreateData('chartOfAccounts')}
                  disabled={chartOfAccountsForm.processing}
                  className="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 disabled:opacity-50"
                >
                  {chartOfAccountsForm.processing ? (
                    <span className="w-4 h-4 mr-1 inline-block animate-spin">⟳</span>
                  ) : (
                    <MdAddCircle className="w-4 h-4 mr-1" />
                  )}
                  Create Missing
                </button>
              )}
            </div>
          </div>

          {/* Financial Periods */}
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center mb-4">
              <MdReceiptLong className="w-6 h-6 text-green-500 mr-2" />
              <h3 className="text-lg font-medium text-gray-900">Financial Periods</h3>
            </div>
            <div className="flex items-center justify-between mb-2">
              <span className="text-sm text-gray-600">Open Periods</span>
              <span className="font-medium">{diagnostics.financialPeriods.current}</span>
            </div>
            <div className="flex items-center justify-between mb-4">
              <span className="text-sm text-gray-600">Total Periods</span>
              <span className="font-medium">{diagnostics.financialPeriods.count}</span>
            </div>
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                {getStatusIcon(diagnostics.financialPeriods.status)}
                <span className="ml-2 text-sm font-medium capitalize">{diagnostics.financialPeriods.status}</span>
              </div>
              {diagnostics.financialPeriods.status !== 'healthy' && (
                <button
                  onClick={() => handleCreateData('financialPeriods')}
                  disabled={financialPeriodsForm.processing}
                  className="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 disabled:opacity-50"
                >
                  {financialPeriodsForm.processing ? (
                    <span className="w-4 h-4 mr-1 inline-block animate-spin">⟳</span>
                  ) : (
                    <MdAddCircle className="w-4 h-4 mr-1" />
                  )}
                  Create Missing
                </button>
              )}
            </div>
          </div>

          {/* Expense Categories */}
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center mb-4">
              <MdTrendingUp className="w-6 h-6 text-orange-500 mr-2" />
              <h3 className="text-lg font-medium text-gray-900">Expense Categories</h3>
            </div>
            <div className="flex items-center justify-between mb-2">
              <span className="text-sm text-gray-600">Count</span>
              <span className="font-medium">{diagnostics.expenseCategories.count}/{diagnostics.expenseCategories.required}</span>
            </div>
            <div className="flex items-center justify-between mb-4">
              <span className="text-sm text-gray-600">Active</span>
              <span className="font-medium">{diagnostics.expenseCategories.active_count}</span>
            </div>
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                {getStatusIcon(diagnostics.expenseCategories.status)}
                <span className="ml-2 text-sm font-medium capitalize">{diagnostics.expenseCategories.status}</span>
              </div>
              {diagnostics.expenseCategories.status !== 'healthy' && (
                <button
                  onClick={() => handleCreateData('expenseCategories')}
                  disabled={expenseCategoriesForm.processing}
                  className="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-orange-700 bg-orange-100 hover:bg-orange-200 disabled:opacity-50"
                >
                  {expenseCategoriesForm.processing ? (
                    <span className="w-4 h-4 mr-1 inline-block animate-spin">⟳</span>
                  ) : (
                    <MdAddCircle className="w-4 h-4 mr-1" />
                  )}
                  Create Missing
                </button>
              )}
            </div>
          </div>

          {/* Tax Settings */}
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center mb-4">
              <MdMonetizationOn className="w-6 h-6 text-purple-500 mr-2" />
              <h3 className="text-lg font-medium text-gray-900">Tax Settings</h3>
            </div>
            <div className="flex items-center justify-between mb-2">
              <span className="text-sm text-gray-600">Count</span>
              <span className="font-medium">{diagnostics.taxSettings.count}/{diagnostics.taxSettings.required}</span>
            </div>
            <div className="flex items-center justify-between mb-4">
              <span className="text-sm text-gray-600">Active</span>
              <span className="font-medium">{diagnostics.taxSettings.active_count}</span>
            </div>
            <div className="flex items-center justify-between">
              <div className="flex items-center">
                {getStatusIcon(diagnostics.taxSettings.status)}
                <span className="ml-2 text-sm font-medium capitalize">{diagnostics.taxSettings.status}</span>
              </div>
              {diagnostics.taxSettings.status !== 'healthy' && (
                <button
                  onClick={() => handleCreateData('taxSettings')}
                  disabled={taxSettingsForm.processing}
                  className="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded text-purple-700 bg-purple-100 hover:bg-purple-200 disabled:opacity-50"
                >
                  {taxSettingsForm.processing ? (
                    <span className="w-4 h-4 mr-1 inline-block animate-spin">⟳</span>
                  ) : (
                    <MdAddCircle className="w-4 h-4 mr-1" />
                  )}
                  Create Missing
                </button>
              )}
            </div>
          </div>

          {/* Journal Entries */}
          <div className="bg-white rounded-lg shadow p-6">
            <div className="flex items-center mb-4">
              <MdAssessment className="w-6 h-6 text-teal-500 mr-2" />
              <h3 className="text-lg font-medium text-gray-900">Journal Entries</h3>
            </div>
            <div className="flex items-center justify-between mb-2">
              <span className="text-sm text-gray-600">Total Entries</span>
              <span className="font-medium">{diagnostics.journalEntries.count}</span>
            </div>
            <div className="flex items-center justify-between mb-4">
              <span className="text-sm text-gray-600">Balance Status</span>
              <span className="font-medium">{diagnostics.journalEntries.balanced ? 'Balanced' : 'Unbalanced'}</span>
            </div>
            <div className="flex items-center">
              {getStatusIcon(diagnostics.journalEntries.status)}
              <span className="ml-2 text-sm font-medium capitalize">{diagnostics.journalEntries.status}</span>
            </div>
          </div>
        </div>

        {/* Recommendations */}
        {recommendations.length > 0 && (
          <div className="bg-white rounded-lg shadow">
            <div className="px-6 py-4 border-b border-gray-200">
              <h3 className="text-lg font-medium text-gray-900">Recommendations</h3>
            </div>
            <div className="divide-y divide-gray-200">
              {recommendations.map((rec, index) => (
                <div key={index} className="p-6">
                  <div className="flex items-start">
                    <div className="flex-shrink-0">
                      {rec.type === 'error' ? (
                        <MdError className="w-5 h-5 text-red-600" />
                      ) : (
                        <MdWarning className="w-5 h-5 text-yellow-600" />
                      )}
                    </div>
                    <div className="ml-3 flex-1">
                      <h4 className="text-sm font-medium text-gray-900">{rec.title}</h4>
                      <p className="text-sm text-gray-600 mt-1">{rec.description}</p>
                      <div className="mt-3">
                        <Link
                          href={rec.href}
                          className="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200"
                        >
                          {rec.action}
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Open Financial Periods */}
        {diagnostics.financialPeriods.open_periods.length > 0 && (
          <div className="bg-white rounded-lg shadow mt-6">
            <div className="px-6 py-4 border-b border-gray-200">
              <h3 className="text-lg font-medium text-gray-900">Active Financial Periods</h3>
            </div>
            <div className="p-6">
              <div className="space-y-3">
                {diagnostics.financialPeriods.open_periods.map((period, index) => (
                  <div key={index} className="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                    <div>
                      <h4 className="font-medium text-gray-900">{period.name}</h4>
                      <p className="text-sm text-gray-600">
                        {new Date(period.start_date).toLocaleDateString()} - {new Date(period.end_date).toLocaleDateString()}
                      </p>
                    </div>
                    <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                      Open
                    </span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        )}

        {/* Confirmation Dialog */}
        {confirmationDialog && (
          <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
              <div className="mt-3">
                <div className="flex items-center mb-4">
                  <MdWarning className="w-6 h-6 text-yellow-500 mr-2" />
                  <h3 className="text-lg font-medium text-gray-900">
                    {confirmationDialog.title}
                  </h3>
                </div>
                <div className="mt-2 px-7 py-3">
                  <p className="text-sm text-gray-500">
                    {confirmationDialog.description}
                  </p>
                </div>
                <div className="flex items-center justify-end px-4 py-3 bg-gray-50 rounded-b-lg">
                  <button
                    className="px-4 py-2 bg-gray-300 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors mr-2"
                    onClick={() => setConfirmationDialog(null)}
                  >
                    Cancel
                  </button>
                  <button
                    className="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors"
                    onClick={confirmationDialog.onConfirm}
                  >
                    Confirm
                  </button>
                </div>
              </div>
            </div>
          </div>
        )}

        {/* Notification */}
        {notification && (
          <div
            className={`fixed bottom-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
              notification.type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'
            } border`}
          >
            <div className="flex items-start">
              <div className="flex-shrink-0">
                {notification.type === 'success' ? (
                  <MdCheckCircle className="w-5 h-5 text-green-400" />
                ) : (
                  <MdError className="w-5 h-5 text-red-400" />
                )}
              </div>
              <div className="ml-3">
                <p className={`text-sm font-medium ${
                  notification.type === 'success' ? 'text-green-800' : 'text-red-800'
                }`}>
                  {notification.message}
                </p>
                {notification.details && (
                  <ul className="mt-1 text-sm text-gray-500 list-disc list-inside">
                    {notification.details.map((detail, index) => (
                      <li key={index}>{detail}</li>
                    ))}
                  </ul>
                )}
              </div>
              <div className="ml-auto pl-3">
                <button
                  onClick={() => setNotification(null)}
                  className="inline-flex text-gray-400 hover:text-gray-600"
                >
                  <span className="sr-only">Dismiss</span>
                  <MdError className="w-5 h-5" />
                </button>
              </div>
            </div>
          </div>
        )}
      </div>
  );
}

SystemDiagnostics.layout = (page: React.ReactElement) => (
  <BizanaLayout title="SystemDiagnostics">{page}</BizanaLayout>
);

export default SystemDiagnostics;