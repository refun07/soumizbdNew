import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import {
  MdReceiptLong,
  MdAdd,
  MdEdit,
  MdCheckCircle,
  MdLock,
  MdCalendarToday
} from 'react-icons/md';
import BizanaLayout from '../../layouts/BizanaLayout';

interface FinancialPeriodsProps {
  periods: {
    data: Array<{
      id: number;
      name: string;
      fiscal_year: number;
      type: string;
      start_date: string;
      end_date: string;
      status: 'open' | 'closed' | 'locked';
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
    year: string;
    status: string;
  };
}

function FinancialPeriods({
  periods,
  filters
}: FinancialPeriodsProps) {
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [editingPeriod, setEditingPeriod] = useState<any>(null);
  const [selectedYear, setSelectedYear] = useState(filters.year);
  const [selectedStatus, setSelectedStatus] = useState(filters.status);

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'open':
        return {
          color: 'bg-green-100 text-green-800',
          icon: <MdCheckCircle className="w-4 h-4" />,
          text: 'Open'
        };
      case 'closed':
        return {
          color: 'bg-yellow-100 text-yellow-800',
          icon: <MdCalendarToday className="w-4 h-4" />,
          text: 'Closed'
        };
      case 'locked':
        return {
          color: 'bg-red-100 text-red-800',
          icon: <MdLock className="w-4 h-4" />,
          text: 'Locked'
        };
      default:
        return {
          color: 'bg-gray-100 text-gray-800',
          icon: null,
          text: status
        };
    }
  };

  const handleYearFilter = (value: string) => {
    setSelectedYear(value);
    const params = new URLSearchParams(window.location.search);
    if (value) {
      params.set('year', value);
    } else {
      params.delete('year');
    }
    router.get(`${window.location.pathname}?${params.toString()}`);
  };

  const handleStatusFilter = (value: string) => {
    setSelectedStatus(value);
    const params = new URLSearchParams(window.location.search);
    if (value) {
      params.set('status', value);
    } else {
      params.delete('status');
    }
    router.get(`${window.location.pathname}?${params.toString()}`);
  };

  const handleCreatePeriod = (e: React.FormEvent) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);

    router.post('/admin/bizana/accounting/financial-periods', formData, {
      onSuccess: () => {
        setShowCreateForm(false);
        (e.target as HTMLFormElement).reset();
      },
    });
  };

  const generateYearOptions = () => {
    const currentYear = new Date().getFullYear();
    const years = [];
    for (let year = currentYear - 2; year <= currentYear + 2; year++) {
      years.push(year);
    }
    return years;
  };

  return (
    <div className="min-h-screen bg-gray-50 p-6">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 flex items-center">
            <MdReceiptLong className="mr-3" />
            Financial Periods
          </h1>
          <p className="text-gray-600 mt-2">
            Manage your financial reporting periods
          </p>
        </div>

        {/* Create Period Button */}
        <div className="mb-6">
          <button
            onClick={() => setShowCreateForm(!showCreateForm)}
            className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            <MdAdd className="mr-2" />
            Create New Period
          </button>
        </div>

        {/* Create Period Form */}
        {showCreateForm && (
          <div className="bg-white rounded-lg shadow mb-6">
            <div className="px-6 py-4 border-b border-gray-200">
              <h3 className="text-lg font-medium text-gray-900">Create New Financial Period</h3>
            </div>
            <form onSubmit={handleCreatePeriod} className="p-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Period Name *
                  </label>
                  <input
                    type="text"
                    name="name"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., January 2024"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Fiscal Year *
                  </label>
                  <select
                    name="fiscal_year"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Select Year</option>
                    {generateYearOptions().map((year) => (
                      <option key={year} value={year}>
                        {year}
                      </option>
                    ))}
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Period Type *
                  </label>
                  <select
                    name="type"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                    <option value="yearly">Yearly</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Status *
                  </label>
                  <select
                    name="status"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="open">Open</option>
                    <option value="closed">Closed</option>
                    <option value="locked">Locked</option>
                  </select>
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Start Date *
                  </label>
                  <input
                    type="date"
                    name="start_date"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    End Date *
                  </label>
                  <input
                    type="date"
                    name="end_date"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                  />
                </div>
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
                  Create Period
                </button>
              </div>
            </form>
          </div>
        )}

        {/* Filters */}
        <div className="bg-white rounded-lg shadow mb-6 p-6">
          <h3 className="text-lg font-medium text-gray-900 mb-4">Filters</h3>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-2">Year</label>
              <select
                value={selectedYear}
                onChange={(e) => handleYearFilter(e.target.value)}
                className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
              >
                <option value="">All Years</option>
                {generateYearOptions().map((year) => (
                  <option key={year} value={year}>
                    {year}
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
                <option value="open">Open</option>
                <option value="closed">Closed</option>
                <option value="locked">Locked</option>
              </select>
            </div>
          </div>
        </div>

        {/* Periods Table */}
        <div className="bg-white rounded-lg shadow overflow-hidden">
          <div className="overflow-x-auto">
            <table className="min-w-full divide-y divide-gray-200">
              <thead className="bg-gray-50">
                <tr>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Period Name
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Year/Period
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Duration
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                  </th>
                  <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Created
                  </th>
                  <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>
              <tbody className="bg-white divide-y divide-gray-200">
                {periods.data.map((period) => {
                  const statusInfo = getStatusBadge(period.status);
                  return (
                    <tr key={period.id} className="hover:bg-gray-50">
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm font-medium text-gray-900">{period.name}</div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-gray-900">
                          Year {period.fiscal_year} - {period.type}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <div className="text-sm text-gray-900">
                          {new Date(period.start_date).toLocaleDateString()} - {new Date(period.end_date).toLocaleDateString()}
                        </div>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap">
                        <span className={`inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full ${statusInfo.color}`}>
                          {statusInfo.icon}
                          <span className="ml-1">{statusInfo.text}</span>
                        </span>
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {new Date(period.created_at).toLocaleDateString()}
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button
                          onClick={() => setEditingPeriod(period)}
                          className="text-blue-600 hover:text-blue-900"
                          title="Edit"
                        >
                          <MdEdit className="w-4 h-4" />
                        </button>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>

          {/* Pagination */}
          <div className="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            <div className="flex-1 flex justify-between sm:hidden">
              {periods.links.map((link, index) => (
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
                  Showing <span className="font-medium">{(periods.current_page - 1) * periods.per_page + 1}</span> to{' '}
                  <span className="font-medium">
                    {Math.min(periods.current_page * periods.per_page, periods.total)}
                  </span>{' '}
                  of <span className="font-medium">{periods.total}</span> results
                </p>
              </div>
              <div>
                <nav className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  {periods.links.map((link, index) => (
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
        </div>
      </div>
  );
}

FinancialPeriods.layout = (page: React.ReactElement) => (
  <BizanaLayout title="FinancialPeriods">{page}</BizanaLayout>
);

export default FinancialPeriods;