import axios from 'axios';
import React, { useEffect, useState, useRef } from 'react';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';

const formatOrderDate = (dateString: string): string => {
    const orderDate = new Date(dateString);
    const now = new Date();
    const diffInMs = now.getTime() - orderDate.getTime();
    const diffInHours = diffInMs / (1000 * 60 * 60);

    if (diffInHours < 24) {
        const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
        const diffInHoursRounded = Math.floor(diffInMinutes / 60);

        if (diffInMinutes < 1) {
            return 'Just now';
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes} minute${diffInMinutes === 1 ? '' : 's'} ago`;
        } else if (diffInHoursRounded < 24) {
            return `${diffInHoursRounded} hour${diffInHoursRounded === 1 ? '' : 's'} ago`;
        }
    }

    return orderDate.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
};

interface PreOrderItem {
    id: number;
    qty: number;
    price: number;
    pre_order_info: {
        is_pre_order: boolean;
        expected_date: string;
        deposit_paid: number;
    } | null;
    product_name: string;
    variation_code: string;
    expected_date: string | null;
    deposit_paid: number;
    order: {
        id: number;
        code?: number;
        customerName?: string;
        customerPhone?: string;
        createdDate?: string;
        totalAmount: number;
        deliveryStatus?: string;
        paymentStatus: string;
        address?: string;
        orderPrefix?: string;
        isPosOrder: boolean;
        source?: string;
    };
    productVariation: {
        sku: string;
        code: string;
    };
}

interface Pagination {
    current_page: number;
    last_page: number;
    total: number;
}

const PreOrdersPage: React.FC = () => {
    const [orderItems, setOrderItems] = useState<PreOrderItem[]>([]);
    const [filters, setFilters] = useState({
        search: '',
    });
    const [pagination, setPagination] = useState<Pagination>({
        current_page: 1,
        last_page: 1,
        total: 0,
    });
    const [loading, setLoading] = useState(true);
    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const fetchOrderItems = async (page = 1, searchTerm?: string) => {
        setLoading(true);
        try {
            const searchQuery = searchTerm !== undefined ? searchTerm : filters.search;
            const response = await axios.get('/admin/api/pre-orders/customer-orders', {
                params: { search: searchQuery, page },
            });

            const data = response.data.result.data;
            setOrderItems(data);
            setPagination({
                current_page: response.data.result.current_page,
                last_page: response.data.result.last_page,
                total: response.data.result.total,
            });
        } catch (error) {
            console.error('Error fetching pre-orders:', error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchOrderItems();
    }, []);

    const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
        const value = e.target.value;
        setFilters((prev) => ({ ...prev, search: value }));
    };

    useEffect(() => {
        if (debounceRef.current) {
            clearTimeout(debounceRef.current);
        }

        if (filters.search !== '') {
            debounceRef.current = setTimeout(() => {
                fetchOrderItems(1);
            }, 500);
        } else {
            fetchOrderItems(1);
        }
    }, [filters]);

    const handleReset = () => {
        setFilters({ search: '' });
        fetchOrderItems(1);
    };

    const handlePageChange = (page: number) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchOrderItems(page);
        }
    };

    const handleViewOrder = (orderCode: number | undefined, orderPrefix?: string) => {
        if (!orderCode) return;
        const code = orderPrefix ? `${orderPrefix}${orderCode}` : orderCode.toString();
        window.location.href = `/admin/orders/${code}`;
    };

    const statusColorMap: Record<string, string> = {
        order_placed: 'bg-green-500',
        processing: 'bg-orange-500',
        confirmed: 'bg-yellow-400',
        cancelled: 'bg-red-500',
        shipped: 'bg-orange-500',
        delivered: 'bg-blue-500',
    };

    const statusLabelMap: Record<string, string> = {
        order_placed: 'Order Placed',
        processing: 'Processing',
        confirmed: 'Confirmed',
        cancelled: 'Cancelled',
        shipped: 'Shipped',
        delivered: 'Delivered',
    };

    return (
        <div className="p-4">
            <div className="flex items-center gap-4 mb-6">
                <h1 className="text-xl font-bold text-foreground">Pre-Orders</h1>
            </div>

            <div className="card rounded-lg shadow-sm">
                <div className="p-4 border-b border-border">
                    <div className="flex flex-wrap items-center justify-end gap-4">
                        <button
                            className="px-4 py-2 text-sm text-muted hover:text-foreground border border-border rounded-md ml-auto"
                            onClick={handleReset}
                        >
                            Reset
                        </button>

                        <div className="search-form relative w-72 flex-shrink-0">
                            <input
                                type="text"
                                value={filters.search}
                                className="theme-input w-full"
                                placeholder="Search order, customer, phone"
                                onChange={handleSearch}
                                autoComplete="off"
                            />
                            <span className="text-primary dark:text-muted absolute top-0 right-0 h-full flex items-center justify-center px-3 pointer-events-none">
                                <i className="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <div className="lg:hidden">
                    <div className="space-y-3 p-3">
                        {loading ? (
                            <div className="text-center py-8 text-muted">Loading...</div>
                        ) : orderItems.length === 0 ? (
                            <div className="text-center py-8 text-muted">No pre-orders found.</div>
                        ) : (
                            orderItems.map((item) => (
                                <div key={item.id} className="card rounded-lg border border-border p-4">
                                    <div className="space-y-3">
                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">Customer</div>
                                            <div className="font-medium">{item.order.customerName}</div>
                                            <div className="text-sm text-muted">{item.order.customerPhone}</div>
                                        </div>

                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">Invoice No</div>
                                            <div className="font-medium">
                                                {item.order.orderPrefix} {item.order.code}
                                            </div>
                                        </div>

                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">Date</div>
                                            <div className="text-sm">{formatOrderDate(item.order.createdDate || '')}</div>
                                        </div>

                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">Product</div>
                                            <div className="font-medium">{item.product_name}</div>
                                            <div className="text-sm text-muted">{item.variation_code}</div>
                                        </div>

                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">Qty</div>
                                            <div className="font-medium">{item.qty}</div>
                                        </div>

                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">Expected Date</div>
                                            <div className="font-medium">{item.expected_date || '-'}</div>
                                        </div>

                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">Order Value</div>
                                            <div className="font-bold text-sky-500">{currencyFormatter(item.order.totalAmount)}</div>
                                            {item.order.paymentStatus === 'paid' ? (
                                                <div className="inline-flex items-center gap-1.5 text-green-600 text-sm">
                                                    <i className="fa-solid fa-circle-check"></i>
                                                    <span>Paid Online</span>
                                                </div>
                                            ) : (
                                                <div className="text-sm text-muted">Cash On Delivery</div>
                                            )}
                                        </div>

                                        <div className="flex items-center justify-end gap-2 pt-2 border-t border-border">
                                            <button
                                                className="text-primary hover:text-primary/80 text-lg p-1"
                                                title="View Order Details"
                                                onClick={() => handleViewOrder(item.order.code, item.order.orderPrefix)}
                                            >
                                                <i className="fa-solid fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>
                </div>

                <div className="hidden lg:block overflow-x-auto">
                    <table className="divide-y divide-border product-list-table w-full">
                        <thead>
                            <tr className="theme-table__head">
                                <th className="px-4 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Invoice No
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Customer
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Product
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Qty
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Expected Date
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Date
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Value
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Status
                                </th>
                                <th className="px-6 py-4 text-right text-xs font-medium text-muted uppercase">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="theme-table__body divide-y divide-border">
                            {loading ? (
                                <tr>
                                    <td colSpan={9} className="text-center py-8 text-muted">
                                        Loading...
                                    </td>
                                </tr>
                            ) : orderItems.length === 0 ? (
                                <tr>
                                    <td colSpan={9} className="text-center py-8 text-muted">
                                        No pre-orders found.
                                    </td>
                                </tr>
                            ) : (
                                orderItems.map((item) => (
                                    <tr key={item.id}>
                                        <td className="px-4 py-4 whitespace-nowrap">
                                            <div className="font-medium">
                                                {item.order.orderPrefix} {item.order.code}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="font-medium">{item.order.customerName}</div>
                                            <div className="text-sm text-muted">{item.order.customerPhone}</div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="font-medium">{item.product_name}</div>
                                            <div className="text-sm text-muted">{item.variation_code}</div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="font-medium">{item.qty}</div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="font-medium">{item.expected_date || '-'}</div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm">
                                            {formatOrderDate(item.order.createdDate || '')}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <h6>{currencyFormatter(item.order.totalAmount)}</h6>
                                                {item.order.paymentStatus === 'paid' ? (
                                                    <div className="inline-flex items-center gap-1.5 text-green-600 text-xs">
                                                        <i className="fa-solid fa-circle-check"></i>
                                                        <span>Paid</span>
                                                    </div>
                                                ) : (
                                                    <div className="text-xs text-muted">COD</div>
                                                )}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white ${statusColorMap[item.order.deliveryStatus ?? 'order_placed']}`}>
                                                {statusLabelMap[item.order.deliveryStatus ?? 'order_placed']}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 text-right text-sm font-medium">
                                            <button
                                                className="text-primary hover:text-primary/80 text-lg p-1"
                                                title="View Order Details"
                                                onClick={() => handleViewOrder(item.order.code, item.order.orderPrefix)}
                                            >
                                                <i className="fa-solid fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                <div className="p-4 border-t border-border">
                    <div className="flex justify-between items-center">
                        <p className="text-sm text-muted">
                            Showing page {pagination.current_page} of {pagination.last_page} ({pagination.total} pre-orders)
                        </p>
                        <div className="flex space-x-2">
                            <button
                                onClick={() => handlePageChange(pagination.current_page - 1)}
                                disabled={pagination.current_page === 1}
                                className={`px-3 py-1 rounded ${
                                    pagination.current_page === 1
                                        ? 'bg-muted/20 text-muted cursor-not-allowed'
                                        : 'bg-indigo-600 text-white hover:bg-indigo-700'
                                }`}
                            >
                                Previous
                            </button>
                            <button
                                onClick={() => handlePageChange(pagination.current_page + 1)}
                                disabled={pagination.current_page === pagination.last_page}
                                className={`px-3 py-1 rounded ${
                                    pagination.current_page === pagination.last_page
                                        ? 'bg-muted/20 text-muted cursor-not-allowed'
                                        : 'bg-indigo-600 text-white hover:bg-indigo-700'
                                }`}
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default PreOrdersPage;
