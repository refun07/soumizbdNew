import React, { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import {
  MdMonetizationOn,
  MdAdd,
  MdEdit,
  MdToggleOn,
  MdToggleOff,
  MdPercent
} from 'react-icons/md';
import BizanaLayout from '../../layouts/BizanaLayout';

interface TaxSettingsProps {
  taxSettings: Array<{
    id: number;
    name: string;
    rate: number;
    description: string;
    is_active: boolean;
    created_at: string;
  }>;
}

function TaxSettings({
  taxSettings
}: TaxSettingsProps) {
  const [showCreateForm, setShowCreateForm] = useState(false);

  const handleCreateTaxSetting = (e: React.FormEvent) => {
    e.preventDefault();
    const formData = new FormData(e.target as HTMLFormElement);

    router.post('/admin/bizana/accounting/tax-settings', formData, {
      onSuccess: () => {
        setShowCreateForm(false);
        (e.target as HTMLFormElement).reset();
      },
    });
  };

  const toggleTaxSettingStatus = (taxSetting: any) => {
    const formData = new FormData();
    formData.append('name', taxSetting.name);
    formData.append('rate', taxSetting.rate.toString());
    formData.append('description', taxSetting.description || '');
    formData.append('is_active', (!taxSetting.is_active).toString());

    router.put(`/admin/bizana/accounting/tax-settings/${taxSetting.id}`, formData);
  };

  const formatPercentage = (rate: number) => {
    return `${rate}%`;
  };

  return (
    <div className="min-h-screen bg-gray-50 p-6">
        {/* Header */}
        <div className="mb-8">
          <h1 className="text-3xl font-bold text-gray-900 flex items-center">
            <MdMonetizationOn className="mr-3" />
            Tax Settings
          </h1>
          <p className="text-gray-600 mt-2">
            Configure tax rates and settings for your business
          </p>
        </div>

        {/* Create Tax Setting Button */}
        <div className="mb-6">
          <button
            onClick={() => setShowCreateForm(!showCreateForm)}
            className="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
          >
            <MdAdd className="mr-2" />
            Create New Tax Setting
          </button>
        </div>

        {/* Create Tax Setting Form */}
        {showCreateForm && (
          <div className="bg-white rounded-lg shadow mb-6">
            <div className="px-6 py-4 border-b border-gray-200">
              <h3 className="text-lg font-medium text-gray-900">Create New Tax Setting</h3>
            </div>
            <form onSubmit={handleCreateTaxSetting} className="p-6">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Tax Name *
                  </label>
                  <input
                    type="text"
                    name="name"
                    required
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., Sales Tax"
                  />
                </div>
                <div>
                  <label className="block text-sm font-medium text-gray-700 mb-2">
                    Tax Rate (%)
                  </label>
                  <input
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    name="rate"
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="e.g., 8.5"
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
                  placeholder="Optional description for this tax setting"
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
                  Tax setting is active
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
                  Create Tax Setting
                </button>
              </div>
            </form>
          </div>
        )}

        {/* Tax Settings Grid */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          {taxSettings.map((tax) => (
            <div key={tax.id} className="bg-white rounded-lg shadow p-6">
              <div className="flex items-start justify-between">
                <div className="flex-1">
                  <div className="flex items-center">
                    <MdPercent className="w-6 h-6 text-green-600 mr-2" />
                    <h3 className="text-lg font-medium text-gray-900">{tax.name}</h3>
                  </div>
                  <div className="mt-2">
                    <span className="text-2xl font-bold text-green-600">
                      {formatPercentage(tax.rate)}
                    </span>
                  </div>
                  <p className="mt-2 text-sm text-gray-600">
                    {tax.description || 'No description provided'}
                  </p>
                  <div className="mt-4">
                    <span className={`inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                      tax.is_active
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    }`}>
                      {tax.is_active ? 'Active' : 'Inactive'}
                    </span>
                  </div>
                  <p className="mt-2 text-xs text-gray-500">
                    Created {new Date(tax.created_at).toLocaleDateString()}
                  </p>
                </div>
              </div>
              <div className="mt-4 flex justify-end space-x-2">
                <button
                  onClick={() => toggleTaxSettingStatus(tax)}
                  className="text-gray-400 hover:text-gray-600"
                  title={tax.is_active ? 'Deactivate' : 'Activate'}
                >
                  {tax.is_active ? (
                    <MdToggleOn className="w-5 h-5 text-green-600" />
                  ) : (
                    <MdToggleOff className="w-5 h-5 text-red-600" />
                  )}
                </button>
                <button
                  className="text-blue-600 hover:text-blue-900"
                  title="Edit"
                >
                  <MdEdit className="w-4 h-4" />
                </button>
              </div>
            </div>
          ))}
        </div>

        {/* Empty State */}
        {taxSettings.length === 0 && (
          <div className="bg-white rounded-lg shadow p-12 text-center">
            <MdPercent className="mx-auto h-12 w-12 text-gray-400" />
            <h3 className="mt-2 text-sm font-medium text-gray-900">No tax settings found</h3>
            <p className="mt-1 text-sm text-gray-500">
              Get started by creating your first tax setting.
            </p>
            <div className="mt-6">
              <button
                onClick={() => setShowCreateForm(true)}
                className="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
              >
                <MdAdd className="mr-2" />
                New Tax Setting
              </button>
            </div>
          </div>
        )}
      </div>
  );
}

TaxSettings.layout = (page: React.ReactElement) => (
  <BizanaLayout title="TaxSettings">{page}</BizanaLayout>
);

export default TaxSettings;