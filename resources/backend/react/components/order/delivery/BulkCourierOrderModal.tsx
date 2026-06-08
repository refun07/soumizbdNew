import React, { useState, useEffect } from 'react';
import { useAppSelector } from '../../../../order/hooks';
import SelectInput from '../../inputs/SelectInput';
import { useAreas, useZones } from '../../../../api/courierApi';
import type { DeliveryPartner } from '../../../types';

interface Order {
  id: number;
  code: number;
  customerName?: string;
  customerPhone?: string;
  address?: string;
  shipping_address?: string;
}

interface OrderConfig {
  weight: string;
  itemDescription: string;
  instructions: string;
  cityId: number | null;
  zoneId: number | null;
  areaId: number | null;
  area: string | null;
}

interface BulkCourierOrderModalProps {
  isOpen: boolean;
  onClose: () => void;
  selectedOrders: Order[];
  onSuccess: () => void;
}

interface OrderClassification {
  order: Order;
  isValid: boolean;
  errors: string[];
  config: OrderConfig;
}

interface OrderFieldsProps {
  orderId: number;
  config: OrderConfig;
  cities: any[];
  redxAreas: any[];
  updateOrderConfig: (orderId: number, field: keyof OrderConfig, value: any) => void;
}

interface BulkResult {
  success: boolean;
  status: string;
  summary?: {
    total_selected: number;
    submitted: number;
    skipped: number;
    already_assigned: number;
    success: number;
    failed: number;
  };
  data?: {
    success: Array<{ code: string; tracking_number: string; status: string }>;
    skipped: Array<{ code: string; reason: string }>;
    failed: Array<{ code: string; reason: string }>;
  };
  message?: string;
}

const PathaoOrderFields: React.FC<OrderFieldsProps> = ({ orderId, config, cities, updateOrderConfig }) => {
  const { data: zones = [] } = useZones(config.cityId ?? undefined);
  const { data: areas = [] } = useAreas(config.zoneId ?? undefined);

  return (
    <div className="space-y-2">
      <div className="grid grid-cols-12 gap-2">
        <div className="col-span-2">
          <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
            Wt(kg)*
          </label>
          <input
            type="number"
            value={config.weight}
            onChange={(e) => updateOrderConfig(orderId, 'weight', e.target.value)}
            className="theme-input w-full text-xs py-1 px-2"
            step="0.1"
            min="0.1"
            required
          />
        </div>
        <div className="col-span-3">
          <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
            City*
          </label>
          <SelectInput
            name={`recipient_city_${orderId}`}
            placeholder="City"
            value={config.cityId}
            options={cities}
            getOptionLabel={(item: { city_name: any }) => item.city_name}
            getOptionValue={(item: { city_id: any }) => item.city_id}
            onChange={(item) => {
              updateOrderConfig(orderId, 'cityId', item.city_id);
              updateOrderConfig(orderId, 'zoneId', null);
              updateOrderConfig(orderId, 'areaId', null);
            }}
            groupClassName="w-full"
            required
          />
        </div>
        <div className="col-span-3">
          <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
            Zone*
          </label>
          <SelectInput
            name={`recipient_zone_${orderId}`}
            placeholder="Zone"
            value={config.zoneId}
            options={zones}
            getOptionLabel={(item: { zone_name: any }) => item.zone_name}
            getOptionValue={(item: { zone_id: any }) => item.zone_id}
            onChange={(item) => {
              updateOrderConfig(orderId, 'zoneId', item.zone_id);
              updateOrderConfig(orderId, 'areaId', null);
            }}
            groupClassName="w-full"
            required
          />
        </div>
        <div className="col-span-4">
          <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
            Area*
          </label>
          <SelectInput
            name={`recipient_area_${orderId}`}
            placeholder="Area"
            value={config.areaId}
            options={areas}
            getOptionLabel={(item: { area_name: any }) => item.area_name}
            getOptionValue={(item: { area_id: any }) => item.area_id}
            onChange={(item) => updateOrderConfig(orderId, 'areaId', item.area_id)}
            groupClassName="w-full"
            required
          />
        </div>
      </div>
      <div className="grid grid-cols-2 gap-2">
        <div>
          <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
            Item Desc
          </label>
          <input
            type="text"
            value={config.itemDescription}
            onChange={(e) => updateOrderConfig(orderId, 'itemDescription', e.target.value)}
            className="theme-input w-full text-xs py-1 px-2"
            placeholder="e.g., Electronics"
          />
        </div>
        <div>
          <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
            Instructions
          </label>
          <input
            type="text"
            value={config.instructions}
            onChange={(e) => updateOrderConfig(orderId, 'instructions', e.target.value)}
            className="theme-input w-full text-xs py-1 px-2"
            placeholder="Delivery notes..."
          />
        </div>
      </div>
    </div>
  );
};

const RedxOrderFields: React.FC<OrderFieldsProps> = ({ orderId, config, redxAreas, updateOrderConfig }) => (
  <div className="space-y-2">
    <div className="grid grid-cols-12 gap-2">
      <div className="col-span-2">
        <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
          Wt(kg)*
        </label>
        <input
          type="number"
          value={config.weight}
          onChange={(e) => updateOrderConfig(orderId, 'weight', e.target.value)}
          className="theme-input w-full text-xs py-1 px-2"
          step="0.1"
          min="0.1"
          required
        />
      </div>
      <div className="col-span-5">
        <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
          Area*
        </label>
        <SelectInput
          name={`recipient_area_${orderId}`}
          placeholder="Select Area"
          value={config.areaId}
          options={redxAreas}
          getOptionLabel={(item: { name: any }) => item.name}
          getOptionValue={(item: { id: any }) => item.id}
          onChange={(item) => {
            updateOrderConfig(orderId, 'areaId', item.id);
            updateOrderConfig(orderId, 'area', item.name);
          }}
          groupClassName="w-full"
          required
        />
      </div>
      <div className="col-span-5">
        <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
          Instructions
        </label>
        <input
          type="text"
          value={config.instructions}
          onChange={(e) => updateOrderConfig(orderId, 'instructions', e.target.value)}
          className="theme-input w-full text-xs py-1 px-2"
          placeholder="Delivery notes..."
        />
      </div>
    </div>
  </div>
);

const SteadfastOrderFields: React.FC<OrderFieldsProps> = ({ orderId, config, updateOrderConfig }) => (
  <div className="space-y-2">
    <div className="grid grid-cols-12 gap-2">
      <div className="col-span-2">
        <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
          Wt(kg)*
        </label>
        <input
          type="number"
          value={config.weight}
          onChange={(e) => updateOrderConfig(orderId, 'weight', e.target.value)}
          className="theme-input w-full text-xs py-1 px-2"
          step="0.1"
          min="0.1"
          required
        />
      </div>
      <div className="col-span-10">
        <label className="block text-[10px] text-gray-500 dark:text-gray-400 mb-0.5 font-medium">
          Instructions
        </label>
        <input
          type="text"
          value={config.instructions}
          onChange={(e) => updateOrderConfig(orderId, 'instructions', e.target.value)}
          className="theme-input w-full text-xs py-1 px-2"
          placeholder="Delivery notes..."
        />
      </div>
    </div>
  </div>
);

const BulkCourierOrderModal: React.FC<BulkCourierOrderModalProps> = ({
  isOpen,
  onClose,
  selectedOrders,
  onSuccess,
}) => {
  const [step, setStep] = useState<'partner' | 'configure'>('partner');
  const [partner, setPartner] = useState<DeliveryPartner>('pathao');
  const [isLoading, setIsLoading] = useState(false);
  const [result, setResult] = useState<BulkResult | null>(null);
  const [errors, setErrors] = useState<Record<number, string[]>>({});
  const [showConfirmation, setShowConfirmation] = useState(false);
  const [orderClassification, setOrderClassification] = useState<OrderClassification[]>([]);

  const [orderConfigs, setOrderConfigs] = useState<Record<number, OrderConfig>>({});

  const cities = useAppSelector((state) => state.courier.cities);
  const redxAreas = useAppSelector((state) => state.courier.redxAreas);

  useEffect(() => {
    const initialConfigs: Record<number, OrderConfig> = {};
    selectedOrders.forEach((order) => {
      initialConfigs[order.id] = {
        weight: '0.5',
        itemDescription: '',
        instructions: '',
        cityId: null,
        zoneId: null,
        areaId: null,
        area: null,
      };
    });
    setOrderConfigs(initialConfigs);
    setErrors({});
    setShowConfirmation(false);
    setOrderClassification([]);
  }, [selectedOrders]);

  if (!isOpen) return null;

  const resetForm = () => {
    setStep('partner');
    setPartner('pathao');
    setResult(null);
    setErrors({});
    setShowConfirmation(false);
    setOrderClassification([]);
    const initialConfigs: Record<number, OrderConfig> = {};
    selectedOrders.forEach((order) => {
      initialConfigs[order.id] = {
        weight: '0.5',
        itemDescription: '',
        instructions: '',
        cityId: null,
        zoneId: null,
        areaId: null,
        area: null,
      };
    });
    setOrderConfigs(initialConfigs);
  };

  const handleClose = () => {
    resetForm();
    onClose();
  };

  const updateOrderConfig = (orderId: number, field: keyof OrderConfig, value: any) => {
    setOrderConfigs((prev) => ({
      ...prev,
      [orderId]: {
        ...prev[orderId],
        [field]: value,
      },
    }));

    if (errors[orderId]) {
      setErrors((prev) => ({
        ...prev,
        [orderId]: prev[orderId]?.filter((e) => !e.toLowerCase().includes(field.toLowerCase())) || [],
      }));
    }
  };

  const classifyOrder = (orderId: number): { isValid: boolean; errors: string[] } => {
    const config = orderConfigs[orderId];
    const orderErrors: string[] = [];

    if (!config) {
      return { isValid: false, errors: ['Configuration missing'] };
    }

    const weight = parseFloat(config.weight);
    if (isNaN(weight) || weight <= 0) {
      orderErrors.push('Weight required');
    }

    if (partner === 'pathao') {
      if (!config.cityId) orderErrors.push('City required');
      if (!config.zoneId) orderErrors.push('Zone required');
      if (!config.areaId) orderErrors.push('Area required');
    } else if (partner === 'redx') {
      if (!config.areaId) orderErrors.push('Area required');
    }
    // Steadfast only needs weight which defaults to 0.5

    return {
      isValid: orderErrors.length === 0,
      errors: orderErrors,
    };
  };

  const classifyAllOrders = (): OrderClassification[] => {
    return selectedOrders.map((order) => {
      const classification = classifyOrder(order.id);
      return {
        order,
        isValid: classification.isValid,
        errors: classification.errors,
        config: orderConfigs[order.id],
      };
    });
  };

  const handleContinue = () => {
    setStep('configure');
  };

  const handleBack = () => {
    setStep('partner');
    setShowConfirmation(false);
  };

  const handleSubmitClick = (e: React.FormEvent) => {
    e.preventDefault();

    // Classify all orders
    const classifications = classifyAllOrders();
    setOrderClassification(classifications);

    const validCount = classifications.filter((c) => c.isValid).length;

    if (validCount === 0) {
      // No valid orders, show errors
      const allErrors: Record<number, string[]> = {};
      classifications.forEach((c) => {
        if (c.errors.length > 0) {
          allErrors[c.order.id] = c.errors;
        }
      });
      setErrors(allErrors);
      const firstErrorElement = document.querySelector('[data-order-error]');
      firstErrorElement?.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    // Show confirmation dialog
    setShowConfirmation(true);
  };

  const handleConfirmSubmit = async () => {
    setIsLoading(true);
    setShowConfirmation(false);

    // Get only valid orders
    const validOrders = orderClassification.filter((c) => c.isValid);

    const ordersPayload = validOrders.map((classification) => {
      const order = classification.order;
      const config = classification.config;
      const basePayload = {
        order_group_id: order.orderGroupId || order.order_group_id,
        weight: parseFloat(config.weight),
        instructions: config.instructions || '',
      };

      if (partner === 'pathao') {
        return {
          ...basePayload,
          recipient_city: config.cityId,
          recipient_zone: config.zoneId,
          recipient_area: config.areaId,
          item_description: config.itemDescription || '',
        };
      } else if (partner === 'redx') {
        return {
          ...basePayload,
          delivery_area: config.area,
          delivery_area_id: config.areaId,
        };
      }

      return basePayload;
    });

    const payload = {
      partner,
      orders: ordersPayload,
    };

    try {
      const res = await fetch('/api/v1/courier/bulk-order', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify(payload),
      });

      const data: BulkResult = await res.json();
      setResult(data);

      if (data.success) {
        onSuccess();
      }
    } catch (err) {
      setResult({
        success: false,
        status: 'error',
        message: 'Network error occurred',
      });
    } finally {
      setIsLoading(false);
    }
  };

  const renderConfirmationDialog = () => {
    const validCount = orderClassification.filter((c) => c.isValid).length;
    const invalidCount = orderClassification.filter((c) => !c.isValid).length;

    return (
      <div className="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-[60] p-4">
        <div className="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md p-6">
          <h3 className="text-lg font-semibold text-foreground mb-4">Confirm Submission</h3>

          <div className="space-y-3 mb-6">
            <div className="flex justify-between items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
              <span className="text-blue-700 dark:text-blue-300">Orders to Submit:</span>
              <span className="text-xl font-bold text-blue-700 dark:text-blue-300">{validCount}</span>
            </div>

            {invalidCount > 0 && (
              <div className="flex justify-between items-center p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <span className="text-yellow-700 dark:text-yellow-300">Orders to Skip:</span>
                <span className="text-xl font-bold text-yellow-700 dark:text-yellow-300">{invalidCount}</span>
              </div>
            )}

            <p className="text-sm text-muted">
              {invalidCount > 0
                ? `${validCount} out of ${selectedOrders.length} orders have all required fields and will be submitted. Orders with missing required data will be skipped and shown in results.`
                : `All ${validCount} orders have all required fields and will be submitted.`}
            </p>
          </div>

          <div className="flex justify-end gap-3">
            <button
              type="button"
              onClick={() => setShowConfirmation(false)}
              className="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500 transition"
              disabled={isLoading}
            >
              Cancel
            </button>
            <button
              type="button"
              onClick={handleConfirmSubmit}
              className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition disabled:opacity-50"
              disabled={isLoading}
            >
              {isLoading ? 'Submitting...' : `Submit ${validCount} Orders`}
            </button>
          </div>
        </div>
      </div>
    );
  };

  const renderResults = () => {
    if (!result) return null;

    if (!result.success) {
      return (
        <div className="mt-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-600 rounded-lg">
          <h3 className="text-red-600 dark:text-red-400 font-semibold mb-2">Error</h3>
          <p className="text-red-700 dark:text-red-300">{result.message || 'An error occurred'}</p>
        </div>
      );
    }

    const { summary, data } = result;

    return (
      <div className="mt-4 space-y-4">
        <div className="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
          <h3 className="text-foreground font-semibold mb-3">Results Summary</h3>
          <div className="grid grid-cols-2 gap-3 text-sm">
            <div className="text-muted">Total Selected:</div>
            <div className="text-foreground font-medium">{summary?.total_selected}</div>
            <div className="text-muted">Submitted:</div>
            <div className="text-blue-600 dark:text-blue-400 font-medium">{summary?.submitted}</div>
            <div className="text-muted">Skipped (Missing Data):</div>
            <div className="text-yellow-600 dark:text-yellow-400 font-medium">{summary?.skipped}</div>
            <div className="text-muted">Already Assigned:</div>
            <div className="text-orange-600 dark:text-orange-400 font-medium">{summary?.already_assigned}</div>
            <div className="text-muted">Success:</div>
            <div className="text-green-600 dark:text-green-400 font-medium">{summary?.success}</div>
            <div className="text-muted">Failed:</div>
            <div className="text-red-600 dark:text-red-400 font-medium">{summary?.failed}</div>
          </div>
        </div>

        {data?.success && data.success.length > 0 && (
          <div className="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-600 rounded-lg">
            <h4 className="text-green-700 dark:text-green-400 font-semibold mb-2">
              Submitted Successfully ({data.success.length})
            </h4>
            <div className="max-h-40 overflow-y-auto space-y-1">
              {data.success.map((item) => (
                <div key={item.code} className="text-sm text-green-700 dark:text-green-300">
                  {item.code} - Tracking: {item.tracking_number}
                </div>
              ))}
            </div>
          </div>
        )}

        {data?.skipped && data.skipped.length > 0 && (
          <div className="p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-600 rounded-lg">
            <h4 className="text-yellow-700 dark:text-yellow-400 font-semibold mb-2">
              Skipped - Missing Required Data ({data.skipped.length})
            </h4>
            <div className="max-h-40 overflow-y-auto space-y-1">
              {data.skipped.map((item) => (
                <div key={item.code} className="text-sm text-yellow-700 dark:text-yellow-300">
                  {item.code} - {item.reason}
                </div>
              ))}
            </div>
          </div>
        )}

        {data?.failed && data.failed.length > 0 && (
          <div className="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-600 rounded-lg">
            <h4 className="text-red-700 dark:text-red-400 font-semibold mb-2">
              Failed ({data.failed.length})
            </h4>
            <div className="max-h-40 overflow-y-auto space-y-1">
              {data.failed.map((item) => (
                <div key={item.code} className="text-sm text-red-700 dark:text-red-300">
                  {item.code} - {item.reason}
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    );
  };

  const renderPartnerSelection = () => (
    <div className="space-y-4 min-h-[350px]">
      <div className="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-600 rounded-lg">
        <span className="text-blue-700 dark:text-blue-300 text-sm">
          Selected Orders: <span className="font-bold text-foreground">{selectedOrders.length}</span>
        </span>
      </div>

      <div className="pb-32">
        <label className="block text-sm text-gray-700 dark:text-gray-300 mb-2 font-medium">
          Select Courier Partner
        </label>
        <SelectInput
          name="partner"
          placeholder="Select Partner"
          value={partner}
          options={[
            { id: 'pathao', name: 'Pathao' },
            { id: 'steadfast', name: 'Steadfast' },
            { id: 'redx', name: 'Redx' },
          ]}
          getOptionLabel={(item: { name: any }) => item.name}
          getOptionValue={(item: { id: any }) => item.id}
          onChange={(item) => setPartner(item.id as DeliveryPartner)}
          groupClassName="w-full"
          required
        />
      </div>

      <div className="flex justify-end gap-2 mt-6">
        <button
          type="button"
          onClick={handleClose}
          className="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500 transition"
        >
          Cancel
        </button>
        <button
          type="button"
          onClick={handleContinue}
          className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition"
        >
          Continue
        </button>
      </div>
    </div>
  );

  const renderOrderConfigForm = () => {
    // Calculate valid orders count in real-time
    const classifications = classifyAllOrders();
    const validCount = classifications.filter((c) => c.isValid).length;
    const invalidCount = classifications.filter((c) => !c.isValid).length;

    return (
      <form onSubmit={handleSubmitClick}>
        <div className="mb-3 flex items-center justify-between">
          <div>
            <h3 className="text-lg font-semibold text-foreground">
              Configure {selectedOrders.length} Orders
            </h3>
            <p className="text-xs text-muted">
              Courier: <span className="font-medium capitalize text-foreground">{partner}</span>
              {validCount > 0 && (
                <span className="ml-2 text-green-600 dark:text-green-400">
                  ({validCount} ready to submit)
                </span>
              )}
            </p>
          </div>
          <button
            type="button"
            onClick={handleBack}
            className="text-xs text-blue-600 hover:text-blue-500 transition"
          >
            ← Change Partner
          </button>
        </div>

        {/* Info banner about partial submission */}
        <div className="mb-3 p-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-600 rounded-lg">
          <p className="text-xs text-blue-700 dark:text-blue-300">
            <i className="fa-solid fa-circle-info mr-1"></i>
            Fill the required fields for the orders you want to submit. Orders with missing data will be skipped automatically.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-3 max-h-[70vh] overflow-y-auto pr-2">
          {selectedOrders.map((order, index) => {
            const config = orderConfigs[order.id];
            const classification = classifications.find((c) => c.order.id === order.id);
            const isValid = classification?.isValid ?? false;
            const displayAddress = order.shipping_address || order.address || 'No address available';

            if (!config) return null;

            return (
              <div
                key={order.id}
                className={`border rounded-lg p-3 ${
                  isValid
                    ? 'border-green-300 dark:border-green-600 bg-green-50/20 dark:bg-green-900/10'
                    : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800'
                }`}
              >
                <div className="flex items-start justify-between mb-2">
                  <div className="flex-1 min-w-0">
                    <div className="flex items-center gap-2 mb-1">
                      <span className="text-xs font-bold text-foreground bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">
                        #{index + 1}
                      </span>
                      <span className="text-sm font-semibold text-foreground">
                        Order {order.code}
                      </span>
                      {isValid ? (
                        <span className="text-green-500 text-xs" title="Ready to submit">
                          <i className="fa-solid fa-check-circle"></i>
                        </span>
                      ) : (
                        <span className="text-yellow-500 text-xs" title="Will be skipped - fill required fields">
                          <i className="fa-solid fa-circle-exclamation"></i>
                        </span>
                      )}
                    </div>
                    <div className="space-y-0.5 text-xs">
                      <div className="text-foreground">
                        <span className="text-muted">Customer:</span>{' '}
                        <span className="font-medium">{order.customerName || 'N/A'}</span>
                      </div>
                      <div className="text-foreground">
                        <span className="text-muted">Phone:</span>{' '}
                        <span className="font-medium">{order.customerPhone || 'N/A'}</span>
                      </div>
                      <div className="text-foreground">
                        <span className="text-muted">📍</span>{' '}
                        <span className="text-muted break-all" title={displayAddress}>
                          {displayAddress.length > 60 ? displayAddress.substring(0, 60) + '...' : displayAddress}
                        </span>
                      </div>
                    </div>
                  </div>
                </div>

                {partner === 'pathao' && <PathaoOrderFields orderId={order.id} config={config} cities={cities || []} redxAreas={redxAreas || []} updateOrderConfig={updateOrderConfig} />}
                {partner === 'redx' && <RedxOrderFields orderId={order.id} config={config} cities={cities || []} redxAreas={redxAreas || []} updateOrderConfig={updateOrderConfig} />}
                {partner === 'steadfast' && <SteadfastOrderFields orderId={order.id} config={config} cities={cities || []} redxAreas={redxAreas || []} updateOrderConfig={updateOrderConfig} />}
              </div>
            );
          })}
        </div>

        <div className="flex justify-between items-center mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
          <div className="text-xs text-muted">
            {validCount > 0 ? (
              <span className="text-green-600 dark:text-green-400">
                <i className="fa-solid fa-check mr-1"></i>
                {validCount} order{validCount !== 1 ? 's' : ''} ready to submit
              </span>
            ) : (
              <span className="text-yellow-600 dark:text-yellow-400">
                <i className="fa-solid fa-triangle-exclamation mr-1"></i>
                Fill at least one order to submit
              </span>
            )}
          </div>
          <div className="flex gap-2">
            <button
              type="button"
              onClick={handleClose}
              className="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500 transition text-sm"
              disabled={isLoading}
            >
              Cancel
            </button>
            <button
              type="submit"
              className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
              disabled={isLoading || validCount === 0}
            >
              {isLoading ? 'Sending...' : `Submit Orders`}
            </button>
          </div>
        </div>
      </form>
    );
  };

  return (
    <div className="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-2 sm:p-4">
      <div className={`bg-white dark:bg-gray-800 rounded-xl shadow-xl w-[95vw] ${step === 'configure' ? 'max-h-[95vh]' : ''} overflow-hidden flex flex-col`}>
        <div className="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
          <div className="flex items-center justify-between">
            <h2 className="text-lg font-semibold text-foreground">
              Bulk Courier Order
            </h2>
            <button
              onClick={handleClose}
              className="text-muted hover:text-foreground text-xl transition-colors"
            >
              <i className="fa-solid fa-xmark"></i>
            </button>
          </div>
        </div>

        <div className={`p-3 sm:p-4 flex-1 ${step === 'configure' ? 'overflow-y-auto' : ''}`}>
          {!result ? (
            step === 'partner' ? renderPartnerSelection() : renderOrderConfigForm()
          ) : (
            <>
              {renderResults()}
              <div className="flex justify-end gap-2 mt-6">
                <button
                  onClick={handleClose}
                  className="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500 transition"
                >
                  Close
                </button>
                {result.success && result.data?.failed && result.data.failed.length > 0 && (
                  <button
                    onClick={() => {
                      setResult(null);
                      setStep('configure');
                    }}
                    className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition"
                  >
                    Retry Failed
                  </button>
                )}
              </div>
            </>
          )}
        </div>
      </div>

      {showConfirmation && renderConfirmationDialog()}
    </div>
  );
};

export default BulkCourierOrderModal;
