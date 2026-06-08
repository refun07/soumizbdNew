import React, { useState } from 'react';
import axios from 'axios';

interface BulkStatusUpdateModalProps {
    isOpen: boolean;
    onClose: () => void;
    selectedOrderIds: number[];
    onSuccess: () => void;
}

const ORDER_STATUSES = [
    { value: 'order_placed', label: 'Order Placed' },
    { value: 'confirmed', label: 'Confirmed' },
    { value: 'processing', label: 'Processing' },
    { value: 'shipped', label: 'Shipped' },
    { value: 'delivered', label: 'Delivered' },
];

const BulkStatusUpdateModal: React.FC<BulkStatusUpdateModalProps> = ({
    isOpen,
    onClose,
    selectedOrderIds,
    onSuccess,
}) => {
    const [selectedStatus, setSelectedStatus] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [error, setError] = useState('');

    if (!isOpen) return null;

    const handleSubmit = async () => {
        if (!selectedStatus) {
            setError('Please select a status');
            return;
        }

        setIsLoading(true);
        setError('');

        try {
            const response = await axios.post('/admin/api/orders/bulk-update-status', {
                order_ids: selectedOrderIds,
                status: selectedStatus,
            });

            if (response.data.success) {
                onSuccess();
                handleClose();
            } else {
                setError(response.data.message || 'Failed to update order statuses');
            }
        } catch (err: any) {
            const message =
                err.response?.data?.message ||
                (err.response?.data?.errors
                    ? Object.values(err.response.data.errors).flat().join(' ')
                    : 'Something went wrong');
            setError(message);
        } finally {
            setIsLoading(false);
        }
    };

    const handleClose = () => {
        setSelectedStatus('');
        setError('');
        setIsLoading(false);
        onClose();
    };

    return (
        <div className="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 p-4">
            <div className="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md overflow-hidden">
                <div className="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                    <div className="flex items-center justify-between">
                        <h2 className="text-lg font-semibold text-foreground">
                            Change Order Status
                        </h2>
                        <button
                            onClick={handleClose}
                            className="text-muted hover:text-foreground text-xl transition-colors"
                        >
                            <i className="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <div className="p-4 space-y-4">
                    <p className="text-sm text-muted">
                        You are about to change the status of{' '}
                        <span className="font-semibold text-foreground">
                            {selectedOrderIds.length}
                        </span>{' '}
                        order{selectedOrderIds.length !== 1 ? 's' : ''}.
                    </p>

                    <div>
                        <label className="block text-sm font-medium text-foreground mb-1">
                            New Status
                        </label>
                        <select
                            value={selectedStatus}
                            onChange={(e) => {
                                setSelectedStatus(e.target.value);
                                setError('');
                            }}
                            className="w-full px-3 py-2 border border-border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-transparent text-foreground"
                        >
                            <option value="">Select Status</option>
                            {ORDER_STATUSES.map((status) => (
                                <option key={status.value} value={status.value}>
                                    {status.label}
                                </option>
                            ))}
                        </select>
                    </div>

                    {error && (
                        <div className="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md">
                            <p className="text-sm text-red-600 dark:text-red-400">
                                {error}
                            </p>
                        </div>
                    )}

                    <div className="flex gap-2 justify-end pt-2">
                        <button
                            type="button"
                            onClick={handleClose}
                            className="px-4 py-2 rounded-lg bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-500 transition text-sm"
                            disabled={isLoading}
                        >
                            Cancel
                        </button>
                        <button
                            type="button"
                            onClick={handleSubmit}
                            className="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 transition disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                            disabled={isLoading || !selectedStatus}
                        >
                            {isLoading ? 'Updating...' : 'Update Status'}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default BulkStatusUpdateModal;
