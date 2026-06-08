import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import BizanaLayout from '../../layouts/BizanaLayout';
import {
  MdMonetizationOn,
  MdAdd,
  MdEdit,
  MdSearch,
  MdFilterList,
  MdToggleOn,
  MdToggleOff,
  MdDelete,
  MdChevronLeft,
  MdChevronRight
} from 'react-icons/md';

interface ChartOfAccountsProps {
  accounts: {
    data: Array<{
      id: number;
      name: string;
      code: string;
      description: string;
      account_type: {
        id: number;
        name: string;
        normal_balance: string;
      };
      parent: {
        id: number;
        name: string;
        code: string;
      } | null;
      opening_balance: number;
      current_balance: number;
      is_active: boolean;
      created_at: string;
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
    account_type_id: string;
    is_active: string;
  };
  filterOptions: {
    accountTypes: Array<{
      id: number;
      name: string;
      normal_balance: string;
    }>;
  };
}

function ChartOfAccounts({
  accounts,
  filters,
  filterOptions
}: ChartOfAccountsProps) {
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [editingAccount, setEditingAccount] = useState<any>(null);
  const [searchTerm, setSearchTerm] = useState(filters.search);
  const [selectedAccountType, setSelectedAccountType] = useState(filters.account_type_id);
  const [selectedStatus, setSelectedStatus] = useState(filters.is_active);

  const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount);
  };

  const handleSearch = (value: string) => {
    setSearchTerm(value);
    const params = new URLSearchParams(window.location.search);
    if (value) {
      params.set('search', value);
    } else {
      params.delete('search');
    }
    router.get(`${window.location.pathname}?${params.toString()}`);
  };

  const handleAccountTypeFilter = (value: string) => {
    setSelectedAccountType(value);
    const params = new URLSearchParams(window.location.search);
    if (value) {
      params.set('account_type_id', value);
    } else {
      params.delete('account_type_id');
    }
    router.get(`${window.location.pathname}?${params.toString()}`);
  };

  const handleStatusFilter = (value: string) => {
    setSelectedStatus(value);
    const params = new URLSearchParams(window.location.search);
    if (value) {
      params.set('is_active', value);
    } else {
      params.delete('is_active');
    }
    router.get(`${window.location.pathname}?${params.toString()}`);
  };

  const handleCreateAccount = (e: React.FormEvent) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);

    router.post('/admin/bizana/accounting/chart-of-accounts', formData, {
      onSuccess: () => {
        setShowCreateForm(false);
        (e.target as HTMLFormElement).reset();
      },
    });
  };

  const handleUpdateAccount = (e: React.FormEvent) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);

    router.put(`/admin/bizana/accounting/chart-of-accounts/${editingAccount.id}`, formData, {
      onSuccess: () => {
        setEditingAccount(null);
      },
    });
  };

  const toggleAccountStatus = (account: any) => {
    const formData = new FormData();
    formData.append('name', account.name);
    formData.append('code', account.code);
    formData.append('account_type_id', account.account_type.id);
    formData.append('parent_id', account.parent?.id || '');
    formData.append('description', account.description || '');
    formData.append('is_active', (!account.is_active).toString());

    router.put(`/admin/bizana/accounting/chart-of-accounts/${account.id}`, formData);
  };

  return (
    <div className="min-h-screen bg-gray-50 p-6">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 flex items-center">
            <MdMonetizationOn className="mr-3" />
            Chart of Accounts
          </h1>
          <p className="text-gray-600 mt-2">
            Manage your chart of accounts and account structure
          </p>
        </div>

        {/* Create Account Button */}
        <div className="mb-6">
          <button
            onClick={() => setShowCreateForm(!showCreateForm)}
            className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            <MdAdd className="mr-2" />
            Create New Account
          </button>
        </div>

        {/* Create Account Form */}
        {showCreateForm && (
          <div className="bg-white rounded-lg shadow mb-6">
            <div className="px-6 py-4 border-b border-gray-200">
              <h3 className="text-lg font-medium text-gray-900">Create New Account</h3>
            </div>
            <form onSubmit={handleCreateAccount} className="p-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Account Name *
                  </label>
                  <input
                    type="text"
                    name="name"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Cash Account"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Account Code *
                  </label>
                  <input
                    type="text"
                    name="code"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., 1000"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Account Type *
                  </label>
                  <select
                    name="account_type_id"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Select Account Type</option>
                    {filterOptions.accountTypes.map((type) => (
                      <option key={type.id} value={type.id}>
                        {type.name} ({type.normal_balance})
                      </option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Parent Account
                  </label>
                  <select
                    name="parent_id"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">No Parent (Root Account)</option>
                    {accounts.data.map((account) => (
                      <option key={account.id} value={account.id}>
                        {account.code} - {account.name}
                      </option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Opening Balance *
                  </label>
                  <input
                    type="number"
                    step="0.01"
                    name="opening_balance"
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
                  className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                  placeholder="Optional description for this account"
                />
              </div>
              <div className="mt-6 flex items-center">
                <input
                  type="checkbox"
                  name="is_active"
                  defaultChecked={true}
                  className="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                />
                <label className="ml-2 block text-sm text-gray-700">
                  Account is active
                </label>
              </div>
              <div className="mt-6 flex justify-end space-x-3">
                <button
                  type="button"
                  onClick={() => setShowCreateForm(false)}
                  className="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                >
                  Create Account
                </button>
              </div>
            </form>
          </div>
        )}

        {/* Filters */}
        <div className="bg-white rounded-lg shadow mb-6 p-6">
          <div className="flex items-center mb-4">
            <MdFilterList className="text-gray-500 mr-2" />
            <h3 className="text-lg font-medium text-gray-900">Filters</h3>
          </div>
          <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Search</label>
              <div className="relative">
                <MdSearch className="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                <input
                  type="text"
                  value={searchTerm}
                  onChange={(e) => handleSearch(e.target.value)}
                  placeholder="Search accounts..."
                  className="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Account Type</label>
              <select
                value={selectedAccountType}
                onChange={(e) => handleAccountTypeFilter(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Types</option>
                {filterOptions.accountTypes.map((type) => (
                  <option key={type.id} value={type.id}>
                    {type.name}
                  </option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select
                value={selectedStatus}
                onChange={(e) => handleStatusFilter(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Status</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
              </select>
            </div>
          </div>
        </div>

        {/* Accounts Table */}
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Code
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Account Name
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Type
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Balance
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
                {accounts.data.map((account) => (
                  <tr key={account.id} className="hover:bg-gray-50">
                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                      {account.code}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div className="text-sm font-medium text-gray-900">{account.name}</div>
                        {account.parent && (
                          <div className="text-xs text-gray-500">
                            Parent: {account.parent.code} - {account.parent.name}
                          </div>
                        )}
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {account.account_type.name}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {formatCurrency(account.current_balance)}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                        account.is_active
                          ? 'bg-green-100 text-green-800'
                          : 'bg-red-100 text-red-800'
                      }`}>
                        {account.is_active ? 'Active' : 'Inactive'}
                      </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                      <div className="flex justify-end space-x-2">
                        <button
                          onClick={() => toggleAccountStatus(account)}
                          className="text-gray-400 hover:text-gray-600"
                          title={account.is_active ? 'Deactivate' : 'Activate'}
                        >
                          {account.is_active ? (
                            <MdToggleOn className="w-5 h-5 text-green-600" />
                          ) : (
                            <MdToggleOff className="w-5 h-5 text-red-600" />
                          )}
                        </button>
                        <button
                          onClick={() => setEditingAccount(account)}
                          className="text-blue-600 hover:text-blue-900"
                          title="Edit"
                        >
                          <MdEdit className="w-4 h-4" />
                        </button>
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div className="flex-1 flex justify-between sm:hidden">
              {accounts.links.map((link, index) => (
                link.url && (
                  <Link
                    key={index}
                    href={link.url}
                    className={`relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md ${
                      link.active ? 'bg-blue-50 text-blue-600' : 'bg-white text-gray-700 hover:bg-gray-50'
                    }`}
                  >
                    {link.label}
                  </Link>
                )
              ))}
            </div>
            <div className="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p className="text-sm text-gray-700">
                  Showing <span className="font-medium">{(accounts.current_page - 1) * accounts.per_page + 1}</span> to{' '}
                  <span className="font-medium">
                    {Math.min(accounts.current_page * accounts.per_page, accounts.total)}
                  </span>{' '}
                  of <span className="font-medium">{accounts.total}</span> results
                </p>
              </div>
              <div>
                <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  {accounts.links.map((link, index) => (
                    link.url && (
                      <Link
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
        </div>
      </div>
  );
}

ChartOfAccounts.layout = (page: React.ReactElement) => (
  <BizanaLayout title="Chart of Accounts">{page}</BizanaLayout>
);

export default ChartOfAccounts;