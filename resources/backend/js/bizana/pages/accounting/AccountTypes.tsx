import React from 'react';
import { Head } from '@inertiajs/react';
import {
  MdAccountBalance,
  MdMonetizationOn,
  MdTrendingUp,
  MdTrendingDown,
  MdAssessment
} from 'react-icons/md';
import BizanaLayout from '../../layouts/BizanaLayout';

interface AccountTypesProps {
  accountTypes: Array<{
    id: number;
    name: string;
    normal_balance: string;
    description: string;
    accounts_count: number;
    created_at: string;
  }>;
}

function AccountTypes({
  accountTypes
}: AccountTypesProps) {
  const getAccountTypeIcon = (name: string) => {
    switch (name.toLowerCase()) {
      case 'assets':
        return <MdMonetizationOn className="w-8 h-8 text-blue-500" />;
      case 'liabilities':
        return <MdTrendingDown className="w-8 h-8 text-red-500" />;
      case 'equity':
        return <MdAccountBalance className="w-8 h-8 text-green-500" />;
      case 'revenue':
        return <MdTrendingUp className="w-8 h-8 text-purple-500" />;
      case 'expenses':
        return <MdTrendingDown className="w-8 h-8 text-orange-500" />;
      default:
        return <MdAssessment className="w-8 h-8 text-gray-500" />;
    }
  };

  const getBalanceTypeColor = (balanceType: string) => {
    switch (balanceType.toLowerCase()) {
      case 'debit':
        return 'bg-blue-100 text-blue-800';
      case 'credit':
        return 'bg-green-100 text-green-800';
      default:
        return 'bg-gray-100 text-gray-800';
    }
  };

  return (
    <div className="min-h-screen bg-gray-50 p-6">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 flex items-center">
            <MdAccountBalance className="mr-3" />
            Account Types
          </h1>
          <p className="text-gray-600 mt-2">
            View and manage fundamental account types for your accounting system
          </p>
        </div>

        {/* Account Types Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {accountTypes.map((type) => (
            <div key={type.id} className="bg-white rounded-lg shadow p-6">
              <div className="flex items-start">
                {getAccountTypeIcon(type.name)}
                <div className="ml-4 flex-1">
                  <h3 className="text-lg font-medium text-gray-900">{type.name}</h3>
                  <div className="mt-1">
                    <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${getBalanceTypeColor(type.normal_balance)}`}>
                      Normal Balance: {type.normal_balance}
                    </span>
                  </div>
                  <p className="mt-3 text-sm text-gray-600">
                    {type.description || 'No description provided'}
                  </p>
                  <div className="mt-4">
                    <div className="flex items-center justify-between">
                      <span className="text-sm text-gray-500">Accounts Count</span>
                      <span className="text-sm font-medium text-gray-900">{type.accounts_count}</span>
                    </div>
                    {type.accounts_count > 0 && (
                      <div className="mt-2">
                        <div className="w-full bg-gray-200 rounded-full h-2">
                          <div
                            className="bg-blue-500 h-2 rounded-full"
                            style={{ width: `${Math.min(100, (type.accounts_count / 10) * 100)}%` }}
                          />
                        </div>
                      </div>
                    )}
                  </div>
                  <p className="mt-3 text-xs text-gray-500">
                    Created {new Date(type.created_at).toLocaleDateString()}
                  </p>
                </div>
              </div>
            </div>
          ))}
        </div>

        {/* Information Section */}
        <div className="mt-8 bg-white rounded-lg shadow">
          <div className="px-6 py-4 border-b border-gray-200">
            <h3 className="text-lg font-medium text-gray-900">Understanding Account Types</h3>
          </div>
          <div className="p-6">
            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <h4 className="font-medium text-gray-900 mb-3">Basic Accounting Equation</h4>
                <div className="bg-gray-50 p-4 rounded-lg">
                  <p className="text-center text-lg font-mono font-medium text-gray-700">
                    Assets = Liabilities + Equity
                  </p>
                </div>
                <p className="mt-3 text-sm text-gray-600">
                  This fundamental equation must always balance in double-entry bookkeeping.
                </p>
              </div>
              <div>
                <h4 className="font-medium text-gray-900 mb-3">Normal Balances</h4>
                <div className="space-y-2">
                  <div className="flex items-center">
                    <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">
                      Debit
                    </span>
                    <span className="text-sm text-gray-700">Assets, Expenses, Cost of Goods Sold</span>
                  </div>
                  <div className="flex items-center">
                    <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 mr-2">
                      Credit
                    </span>
                    <span className="text-sm text-gray-700">Liabilities, Equity, Revenue, Gains</span>
                  </div>
                </div>
              </div>
            </div>

            <div className="mt-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
              <div className="bg-blue-50 p-4 rounded-lg">
                <h5 className="font-medium text-blue-900 mb-1">Assets</h5>
                <p className="text-sm text-blue-700">Resources owned by the business</p>
              </div>
              <div className="bg-red-50 p-4 rounded-lg">
                <h5 className="font-medium text-red-900 mb-1">Liabilities</h5>
                <p className="text-sm text-red-700">Obligations and debts of the business</p>
              </div>
              <div className="bg-green-50 p-4 rounded-lg">
                <h5 className="font-medium text-green-900 mb-1">Equity</h5>
                <p className="text-sm text-green-700">Owner's interest in the business</p>
              </div>
              <div className="bg-purple-50 p-4 rounded-lg">
                <h5 className="font-medium text-purple-900 mb-1">Revenue</h5>
                <p className="text-sm text-purple-700">Income from business operations</p>
              </div>
              <div className="bg-orange-50 p-4 rounded-lg">
                <h5 className="font-medium text-orange-900 mb-1">Expenses</h5>
                <p className="text-sm text-orange-700">Costs of operating the business</p>
              </div>
            </div>
          </div>
        </div>

        {/* Empty State */}
        {accountTypes.length === 0 && (
          <div className="bg-white rounded-lg shadow p-12 text-center">
            <MdAccountBalance className="mx-auto h-12 w-12 text-gray-400" />
            <h3 className="mt-2 text-sm font-medium text-gray-900">No account types found</h3>
            <p className="mt-1 text-sm text-gray-500">
              Account types should be automatically created when setting up the accounting system.
            </p>
            <p className="mt-1 text-sm text-gray-500">
              If you're seeing this, please run the accounting seeder or contact support.
            </p>
          </div>
        )}
      </div>
  );
}

AccountTypes.layout = (page: React.ReactElement) => (
  <BizanaLayout title="AccountTypes">{page}</BizanaLayout>
);

export default AccountTypes;