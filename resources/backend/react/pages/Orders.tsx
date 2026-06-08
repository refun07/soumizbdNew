import axios from 'axios';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import { useDispatch } from 'react-redux';
import { useNavigate } from 'react-router-dom';
import { togglePopup } from '../../../frontend/store/features/popup/popupSlice';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import SelectInput from '../components/inputs/SelectInput';
import CourierInitLoader from '../components/order/delivery/CourierIntentLoader';
import SelectPartner from '../components/order/delivery/SelectPartner';
import BulkCourierOrderModal from '../components/order/delivery/BulkCourierOrderModal';
import BulkStatusUpdateModal from '../components/order/BulkStatusUpdateModal';
import { useAppContext } from '../Context';
import { route } from '../utils/routeHelpers';

// Import DateRangePicker from bizana
import 'react-date-range/dist/styles.css'; // main css file
import 'react-date-range/dist/theme/default.css'; // theme css file
import DateRangePicker from '../../js/bizana/components/DateRangePicker';

// Helper function to format date based on age
const formatOrderDate = (dateString: string): string => {
    const orderDate = new Date(dateString);
    const now = new Date();
    const diffInMs = now.getTime() - orderDate.getTime();
    const diffInHours = diffInMs / (1000 * 60 * 60);

    // If less than 24 hours, show relative time
    if (diffInHours < 24) {
        const diffInMinutes = Math.floor(diffInMs / (1000 * 60));
        const diffInHoursRounded = Math.floor(diffInMinutes / 60);

        if (diffInMinutes < 1) {
            return 'Just now';
        } else if (diffInMinutes < 60) {
            return `${diffInMinutes} minute${
                diffInMinutes === 1 ? '' : 's'
            } ago`;
        } else if (diffInHoursRounded < 24) {
            return `${diffInHoursRounded} hour${
                diffInHoursRounded === 1 ? '' : 's'
            } ago`;
        }
    }

    // If 24 hours or more, show exact date/time in user's local timezone
    return orderDate.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
};

interface Order {
    id: number;
    code: number;
    customerName?: string;
    customerPhone?: string;
    agentId?: number | null;
    agentName?: string | null;
    createdDate?: string;
    orderCount?: string;
    totalAmount: number;
    deliveryStatus?: string;
    deliveryStatusToShow?: string;
    paymentStatus: string;
    address?: string;
    shipping_address?: string;
    orderPrefix?: string;
    isPosOrder: boolean;
    isExchangeOrder?: boolean;
    hasExchange?: boolean;
    exchangeStatus?: string;
    exchangeOrderCode?: string;
    exchangeOrderRawCode?: string;
    exchangeOrderId?: number;
    originalOrderCode?: string;
    originalOrderRawCode?: string;
    originalOrderId?: number;
    source?: string;
    latestDeliveryUpdate?: {
        status: string;
        note: string;
        actorName: string;
        actorRole?: string | null;
        createdDate: string;
        createdTime: string;
    } | null;
}

const formatOrderSource = (isPosOrder: boolean, source?: string): string => {
    if (!source) return '';
    if (isPosOrder) {
        return `POS/${source.toLowerCase()}`;
    }
    return source;
};

interface Pagination {
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
}

const OrderPage: React.FC = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const { setPopup } = useAppContext();
    const [orders, setOrders] = useState<Order[]>([]);
    const [selectedOrderIds, setSelectedOrderIds] = useState<number[]>([]);
    const [filters, setFilters] = useState({
        from: '',
        to: '',
        search: '',
        payment_status: '',
        pos_order: '',
    });

    const [activeTab, setActiveTab] = useState('all');
    const [isBulkCourierModalOpen, setIsBulkCourierModalOpen] = useState(false);
    const [isBulkStatusModalOpen, setIsBulkStatusModalOpen] = useState(false);
    const [isExporting, setIsExporting] = useState(false);

    // Debounce ref for search to avoid too many API calls
    const debounceRef = useRef<NodeJS.Timeout>();

    // Tab configuration
    const tabs = [
        { id: 'all', label: 'All', filter: {} },
        {
            id: 'order_placed',
            label: 'Order Placed',
            filter: { delivery_status: 'order_placed' },
        },
        {
            id: 'confirmed',
            label: 'Confirmed',
            filter: { delivery_status: 'confirmed' },
        },
        {
            id: 'processing',
            label: 'Processing',
            filter: { delivery_status: 'processing' },
        },
        {
            id: 'shipped',
            label: 'Shipped',
            filter: { delivery_status: 'shipped' },
        },
        {
            id: 'delivered',
            label: 'Delivered',
            filter: { delivery_status: 'delivered' },
        },
        {
            id: 'cancelled',
            label: 'Cancelled',
            filter: { delivery_status: 'cancelled' },
        },
        {
            id: 'exchange',
            label: 'Exchange',
            filter: { has_exchange: '1' },
        },
    ];

    const [perPage, setPerPage] = useState(15);
    const [pagination, setPagination] = useState<Pagination>({
        current_page: 1,
        last_page: 1,
        total: 0,
        per_page: 15,
    });

    const fetchOrders = async (page = 1, searchTerm?: string, limitOverride?: number) => {
        try {
            const currentTab = tabs.find((t) => t.id === activeTab);
            const tabFilter = currentTab?.filter || {};

            const searchQuery =
                searchTerm !== undefined ? searchTerm : filters.search;

            const response = await axios.get('/admin/api/orders', {
                params: { ...filters, search: searchQuery, ...tabFilter, page, limit: limitOverride ?? perPage },
            });

            const data = response.data.result.data;
            console.log(data);
            const paginationData = response.data.result.meta;
            setOrders(data);
            setPagination({
                current_page: paginationData.current_page,
                last_page: paginationData.last_page,
                total: paginationData.total,
                per_page: paginationData.per_page,
            });
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    };

    const handleStatusUpdate = (
        status: string,
        id: string,
        order_id: number,
    ) => {
        dispatch(
            togglePopup({
                popup: 'confirmation-status-update',
                popupProps: {
                    message: `You want to Update status to ${status}`,
                    title: 'Are Your Sure?',
                    order: { status, id, order_id, setOrders },
                },
            }),
        );
    };
    useEffect(() => {
        // Clear previous timeout
        if (debounceRef.current) {
            clearTimeout(debounceRef.current);
        }

        // Debounce fetchOrders when search changes (500ms delay)
        if (filters.search !== '') {
            debounceRef.current = setTimeout(() => {
                fetchOrders();
            }, 500);
        } else {
            // Fetch immediately if search is empty
            fetchOrders();
        }
    }, [filters, activeTab]);

    const handleChange = (key: string, value: string) => {
        setFilters((prev) => ({ ...prev, [key]: value }));
    };

    const handleDateChange = (startDate: string, endDate: string) => {
        setFilters((prev) => ({ ...prev, from: startDate, to: endDate }));
    };

    const handlePaymentStatusChange = (value: string) => {
        setFilters((prev) => ({ ...prev, payment_status: value }));
    };

    const togglePosFilter = () => {
        setFilters((prev) => ({
            ...prev,
            pos_order: prev.pos_order === '1' ? '' : '1',
        }));
    };

    const handleSearch = useCallback((searchTerm: string) => {
        // Search is now handled by useEffect with debounce
        // This callback is kept for interface compatibility
    }, []);

    const handleReset = () => {
        setFilters({
            from: '',
            to: '',
            search: '',
            payment_status: '',
            pos_order: '',
        });
        setActiveTab('order_placed');
        fetchOrders(1);
    };

    const handleTabChange = (tabId: string) => {
        setActiveTab(tabId);
        // Fetch orders will be called by useEffect when activeTab changes
    };

    const handlePageChange = (page: number) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchOrders(page);
        }
    };

    const handlePerPageChange = (newPerPage: number) => {
        setPerPage(newPerPage);
        fetchOrders(1, undefined, newPerPage);
    };

    const toggleSelectAll = () => {
        if (selectedOrderIds.length === orders.length) {
            setSelectedOrderIds([]);
        } else {
            setSelectedOrderIds(orders.map((o) => o.id));
        }
    };

    const toggleSelectOne = (orderId: number) => {
        setSelectedOrderIds((prev) =>
            prev.includes(orderId)
                ? prev.filter((id) => id !== orderId)
                : [...prev, orderId],
        );
    };

    const handleProductListClick = (
        orderId: number,
        event: React.MouseEvent<HTMLButtonElement>,
    ) => {
        event.stopPropagation();
        const rect = event.currentTarget.getBoundingClientRect();
        const x = Math.min(rect.left, window.innerWidth - 350);
        const yCenter = rect.top + rect.height / 2; // Center Y position of the arrow
        setPopup({
            name: 'product-list',
            props: { orderId, position: { x, y: yCenter } },
        });
    };

    const handleFraudCheckClick = (
        event: React.MouseEvent<HTMLButtonElement>,
        phoneNumber?: string,
    ) => {
        event.stopPropagation();
        if (!phoneNumber) return;
        setPopup({ name: 'fraud-check', props: { phoneNumber } });
    };

    const handleEditOrder = (orderId: number) => {
        setPopup({ name: 'edit-order', props: { orderId } });
    };

    const handleViewOrder = (orderId: number) => {
        setPopup({ name: 'quick-view-order', props: { orderId } });
    };

    const handleDownload = async () => {
        try {
            const response = await axios.get(
                `/admin/multiple-invoice-download/${selectedOrderIds.join(
                    ',',
                )}`,
                {
                    responseType: 'blob',
                },
            );

            const blob = new Blob([response.data], { type: 'application/zip' });
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'invoices.zip';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);
        } catch (error) {
            console.error('Error downloading invoices:', error);
        }
    };

    const handleDownloadIndividual = async () => {
        try {
            // Download each invoice sequentially with a small delay to avoid browser blocking
            for (let i = 0; i < selectedOrderIds.length; i++) {
                const orderId = selectedOrderIds[i];
                
                // Fetch PDF as blob
                const response = await axios.get(
                    `/admin/invoice-download/${orderId}`,
                    {
                        responseType: 'blob',
                    },
                );

                // Create blob and download link
                const blob = new Blob([response.data], { type: 'application/pdf' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `invoice_${orderId}.pdf`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                window.URL.revokeObjectURL(url);

                // Wait a bit before the next download to avoid overwhelming the browser
                await new Promise((resolve) => setTimeout(resolve, 800));
            }
        } catch (error) {
            console.error('Error downloading invoices:', error);
        }
    };

    const handleExportCSV = async () => {
        try {
            setIsExporting(true);
            const currentTab = tabs.find((t) => t.id === activeTab);
            const tabFilter = currentTab?.filter || {};

            const params: Record<string, unknown> = { ...filters, ...tabFilter };
            if (selectedOrderIds.length > 0) {
                params.order_ids = selectedOrderIds;
            }

            const response = await axios.get('/admin/api/orders/export', {
                params,
                responseType: 'blob',
            });

            const blob = new Blob([response.data], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;

            const contentDisposition = response.headers['content-disposition'];
            const filenameMatch = contentDisposition?.match(/filename="(.+)"/);
            link.download = filenameMatch ? filenameMatch[1] : 'orders.csv';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Error exporting orders:', error);
        } finally {
            setIsExporting(false);
        }
    };

    const statusColorMap: Record<string, string> = {
        order_placed: '!bg-emerald-700',
        processing: '!bg-orange-700',
        confirmed: '!bg-amber-300',
        cancelled: '!bg-red-700',
        shipped: '!bg-orange-700',
        delivered: '!bg-blue-700',
    };
    const statusTextColorMap: Record<string, string> = {
        order_placed: '!text-white',
        processing: '!text-white',
        confirmed: '!text-slate-950',
        cancelled: '!text-white',
        shipped: '!text-white',
        delivered: '!text-white',
    };
    const nativeSelectOptionClass =
        'bg-white text-neutral-900 dark:bg-neutral-900 dark:text-neutral-100';

    const renderLatestDeliveryUpdate = (order: Order) => {
        if (!order.latestDeliveryUpdate) {
            return null;
        }

        const { actorName, actorRole, status, createdDate, createdTime } =
            order.latestDeliveryUpdate;

        return (
            <div className="mt-2 text-[11px] leading-4 text-muted">
                <div className="font-medium text-foreground/80">
                    Last by {actorName}
                    {actorRole ? ` (${actorRole})` : ''}
                </div>
                <div>
                    {status} · {createdDate} {createdTime}
                </div>
            </div>
        );
    };

    return (
        <div className="p-4">
            <CourierInitLoader />
            <h1 className="text-xl font-bold mb-4 text-foreground">
                Orders Panel
            </h1>

            <div className="card rounded-lg shadow-sm">
                {/* Top Actions */}
                <div className="border-b border-border p-3 sm:p-4">
                    <div className="flex flex-wrap items-center justify-between gap-3 sm:gap-4">
                        {/* Bulk Selection Controls */}
                        <div className="flex w-full flex-wrap items-center gap-2 sm:gap-3 xl:w-auto">
                            <div className="flex items-center">
                                <input
                                    type="checkbox"
                                    checked={
                                        selectedOrderIds.length ===
                                            orders.length && orders.length > 0
                                    }
                                    onChange={toggleSelectAll}
                                    className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                />
                            </div>
                            <span className="text-sm text-muted">
                                Selected{' '}
                                {selectedOrderIds.length
                                    .toString()
                                    .padStart(2, '0')}
                            </span>
                            <select
                                className="min-h-[37px] min-w-[160px] flex-1 rounded-md border border-border bg-transparent px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500 sm:flex-none"
                                onChange={(e) => {
                                    const value = e.target.value;
                                    if (value === 'download_invoice_zip') {
                                        handleDownload();
                                        e.target.value = 'bulk_actions';
                                    } else if (value === 'download_invoice_individual') {
                                        handleDownloadIndividual();
                                        e.target.value = 'bulk_actions';
                                    } else if (value === 'send_bulk_courier') {
                                        if (selectedOrderIds.length === 0) {
                                            alert('Please select at least one order');
                                            e.target.value = 'bulk_actions';
                                        } else {
                                            setIsBulkCourierModalOpen(true);
                                            e.target.value = 'bulk_actions';
                                        }
                                    } else if (value === 'change_status') {
                                        if (selectedOrderIds.length === 0) {
                                            alert('Please select at least one order');
                                            e.target.value = 'bulk_actions';
                                        } else {
                                            setIsBulkStatusModalOpen(true);
                                            e.target.value = 'bulk_actions';
                                        }
                                    }
                                }}
                            >
                                <option
                                    value="bulk_actions"
                                    className={nativeSelectOptionClass}
                                >
                                    Bulk Actions
                                </option>
                                <option
                                    value="download_invoice_zip"
                                    className={nativeSelectOptionClass}
                                >
                                    Download Invoices (ZIP)
                                </option>
                                <option
                                    value="download_invoice_individual"
                                    className={nativeSelectOptionClass}
                                >
                                    Download Invoices (Individual)
                                </option>
                                <option
                                    value="send_bulk_courier"
                                    className={nativeSelectOptionClass}
                                >
                                    Send to Courier (Bulk)
                                </option>
                                <option
                                    value="change_status"
                                    className={nativeSelectOptionClass}
                                >
                                    Change Status
                                </option>
                            </select>
                            <button
                                onClick={handleExportCSV}
                                disabled={isExporting}
                                className={`flex min-h-[37px] w-full items-center justify-center gap-1.5 rounded-md border border-border px-3 py-2 text-sm font-medium sm:w-auto ${
                                    isExporting
                                        ? 'bg-muted/20 text-muted cursor-not-allowed'
                                        : 'bg-transparent text-foreground hover:bg-muted/20'
                                }`}
                            >
                                <i className={`fa-solid ${isExporting ? 'fa-spinner fa-spin' : 'fa-file-csv'}`}></i>
                                {isExporting ? 'Exporting...' : 'Export CSV'}
                            </button>
                        </div>

                        {/* Date Range Picker */}
                        <div className="w-full flex-shrink-0 sm:w-auto">
                            <DateRangePicker
                                startDate={filters.from}
                                endDate={filters.to}
                                onDateChange={handleDateChange}
                            />
                        </div>

                        {/* Payment Status Filter */}
                        <div className="flex w-full flex-shrink-0 flex-col gap-1.5 sm:w-auto sm:flex-row sm:items-center sm:gap-2">
                            <label className="text-sm font-medium text-muted whitespace-nowrap">
                                Payment Status
                            </label>
                            <select
                                className="min-h-[37px] w-full rounded-md border border-border bg-transparent px-3 py-2 text-sm text-foreground focus:outline-none focus:ring-2 focus:ring-blue-500 sm:w-40"
                                value={filters.payment_status}
                                onChange={(e) =>
                                    handlePaymentStatusChange(e.target.value)
                                }
                            >
                                <option
                                    value=""
                                    className={nativeSelectOptionClass}
                                >
                                    Select Status
                                </option>
                                <option
                                    value="paid"
                                    className={nativeSelectOptionClass}
                                >
                                    paid
                                </option>
                                <option
                                    value="unpaid"
                                    className={nativeSelectOptionClass}
                                >
                                    unpaid
                                </option>
                            </select>
                        </div>

                        {/* Search Input */}
                        <div className="search-form relative w-full flex-shrink-0 sm:w-72">
                            <input
                                type="text"
                                id="search"
                                name="search"
                                value={filters.search}
                                className="theme-input w-full"
                                placeholder="Search Order with ID/Phone/Name"
                                onChange={(e) =>
                                    handleChange('search', e.target.value)
                                }
                                autoComplete="off"
                            />
                            <span className="text-primary dark:text-muted absolute top-0 right-0 h-full flex items-center justify-center px-3 pointer-events-none">
                                <i className="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </div>
                    </div>
                </div>

                {/* Tabs */}
                <div>
                    <div className="flex overflow-x-auto gap-2 p-2">
                        {tabs.map((tab) => (
                            <button
                                key={tab.id}
                                onClick={() => handleTabChange(tab.id)}
                                className={`px-6 py-3 text-sm font-medium whitespace-nowrap transition-colors rounded-sm ${
                                    activeTab === tab.id
                                        ? 'bg-green-700 text-white'
                                        : 'bg-muted/20 text-muted hover:bg-muted/40'
                                }`}
                            >
                                {tab.label}
                            </button>
                        ))}
                        <button
                            onClick={togglePosFilter}
                            className={`px-6 py-3 text-sm font-medium whitespace-nowrap transition-colors rounded-sm ${
                                filters.pos_order === '1'
                                    ? 'bg-green-700 text-white'
                                    : 'bg-muted/20 text-muted hover:bg-muted/40'
                            }`}
                        >
                            POS
                        </button>
                    </div>
                </div>

                {/* Mobile Card View */}
                <div className="lg:hidden">
                    <div className="space-y-3 p-3">
                        {orders.map((order, index) => (
                            <div
                                key={order.code}
                                className="card rounded-lg border border-border p-4"
                            >
                                <div className="flex items-start justify-between mb-3">
                                    <div className="flex items-center gap-2">
                                        <input
                                            type="checkbox"
                                            checked={selectedOrderIds.includes(
                                                order.id,
                                            )}
                                            onChange={() =>
                                                toggleSelectOne(order.id)
                                            }
                                            className="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        />
                                        <span className="text-sm font-medium text-muted">
                                            Select
                                        </span>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <a
                                            title="Download Invoice"
                                            href={route(
                                                'admin.orders.downloadInvoice',
                                                { id: order.id },
                                            )}
                                            target="_blank"
                                            className="text-theme-orange hover:text-orange-600"
                                        >
                                            <i className="fa-solid fa-file-arrow-down"></i>
                                        </a>
                                        {!!order.isPosOrder && (
                                            <a
                                                title="Download Challan"
                                                href={route(
                                                    'admin.orders.downloadChallan',
                                                    { id: order.id },
                                                )}
                                                target="_blank"
                                                className="text-theme-orange hover:text-orange-600"
                                            >
                                                <i className="fa-light fa-file-contract"></i>
                                            </a>
                                        )}
                                    </div>
                                </div>

                                <div className="space-y-3">
                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">
                                            Customer
                                        </div>
                                        <div className="font-medium">
                                            {order.customerName}
                                        </div>
                                        <div className="text-sm text-muted">
                                            {order.customerPhone}
                                        </div>
                                    </div>

                                    {order.agentName && (
                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">
                                                Agent
                                            </div>
                                            <div className="font-medium text-sm">
                                                {order.agentName}
                                            </div>
                                        </div>
                                    )}

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">
                                            Invoice No
                                        </div>
                                        <div className="font-medium flex items-center gap-1.5">
                                            <span>
                                                {order.orderPrefix} {order.code}
                                            </span>
                                            {order.isExchangeOrder && (
                                                <a
                                                    href={`/admin/orders/${order.originalOrderRawCode}`}
                                                    title="Exchange order - view original"
                                                >
                                                    <span className="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                        EXC
                                                    </span>
                                                </a>
                                            )}
                                            {order.hasExchange && !order.isExchangeOrder && (
                                                <a
                                                    href={`/admin/orders/${order.exchangeOrderRawCode}`}
                                                    title="Has exchange - view exchange order"
                                                >
                                                    <span className="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                                        EXC
                                                    </span>
                                                </a>
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">
                                            Date
                                        </div>
                                        <div className="text-sm">
                                            {formatOrderDate(
                                                order.createdDate || '',
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">
                                            Source
                                        </div>
                                        <div className="font-medium">
                                            {formatOrderSource(
                                                order.isPosOrder,
                                                order.source,
                                            )}
                                        </div>
                                    </div>

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">
                                            Order Value
                                        </div>
                                        <div className="font-bold text-sky-500">
                                            {currencyFormatter(
                                                order.totalAmount,
                                            )}
                                        </div>
                                        {order.paymentStatus === 'paid' ? (
                                            <div className="inline-flex items-center gap-1.5 text-green-600 text-sm">
                                                <i className="fa-solid fa-circle-check"></i>
                                                <span>Paid Online</span>
                                            </div>
                                        ) : (
                                            <div className="text-sm text-muted">
                                                Cash On Delivery
                                            </div>
                                        )}
                                    </div>

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">
                                            Items
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <span className="text-sm">
                                                {order.orderCount}
                                            </span>
                                            <button
                                                onClick={(e) =>
                                                    handleProductListClick(
                                                        order.id,
                                                        e,
                                                    )
                                                }
                                                className="text-muted hover:text-foreground focus:outline-none text-xs"
                                                title="View Items"
                                            >
                                                <i className="fa-solid fa-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">
                                            Delivery Status
                                        </div>
                                        <SelectInput
                                            name="delivery_status"
                                            placeholder=""
                                            value={order.deliveryStatus}
                                            options={[
                                                {
                                                    id: 'order_placed',
                                                    name: 'Order Placed',
                                                },
                                                {
                                                    id: 'confirmed',
                                                    name: 'Confirmed',
                                                },
                                                {
                                                    id: 'processing',
                                                    name: 'Processing',
                                                },
                                                {
                                                    id: 'cancelled',
                                                    name: 'Cancelled',
                                                },
                                                {
                                                    id: 'shipped',
                                                    name: 'Shipped',
                                                },
                                                {
                                                    id: 'delivered',
                                                    name: 'Delivered',
                                                },
                                            ]}
                                            onChange={(e) => {
                                                handleStatusUpdate(
                                                    e.name,
                                                    e.id,
                                                    order.id,
                                                );
                                            }}
                                            getOptionLabel={(item) => item.name}
                                            getOptionValue={(item) => item.id}
                                            groupClassName="w-full"
                                            statusColorClass={`${
                                                statusColorMap[
                                                    order.deliveryStatus ??
                                                        'order_placed'
                                                ]
                                            }`}
                                            statusTextClass={
                                                statusTextColorMap[
                                                    order.deliveryStatus ??
                                                        'order_placed'
                                                ] ?? '!text-white'
                                            }
                                        />
                                        {renderLatestDeliveryUpdate(order)}
                                    </div>

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">
                                            Delivery Partner
                                        </div>
                                        <SelectPartner order={order} />
                                    </div>

                                    <div className="flex items-center justify-end gap-2 pt-2 border-t border-border">
                                        <button
                                            className="text-primary hover:text-primary/80 text-lg p-1"
                                            title="View Order Details"
                                            onClick={() =>
                                                handleViewOrder(order.id)
                                            }
                                        >
                                            <i className="fa-solid fa-eye"></i>
                                        </button>
                                        <button
                                            className="text-blue-500 hover:text-blue-700 text-lg p-1"
                                            title="Edit Order"
                                            onClick={() =>
                                                handleEditOrder(order.id)
                                            }
                                        >
                                            <i className="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button
                                            className="text-orange-500 hover:text-orange-700 text-lg p-1"
                                            title="Delete Order"
                                            onClick={() => {
                                                console.log(
                                                    'Delete order:',
                                                    order.id,
                                                );
                                            }}
                                        >
                                            <i className="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        ))}
                        {orders.length === 0 && (
                            <div className="text-center py-8 text-muted">
                                No orders found.
                            </div>
                        )}
                    </div>
                </div>

                {/* Desktop Table View */}
                <div className="hidden lg:block overflow-x-auto">
                    <table className="divide-y divide-border product-list-table w-full">
                        <thead>
                            <tr className="theme-table__head">
                                <th className="px-4 py-4"></th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Customer
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Agent
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Shipping Address
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    ITEM
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase max-w-[150px]">
                                    Date
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Invoice No
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Source
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Value
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase min-w-[170px]">
                                    Order Status
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Delivery
                                </th>
                                <th className="px-6 py-4 text-right text-xs font-medium text-muted uppercase"></th>
                            </tr>
                        </thead>
                        <tbody className="theme-table__body divide-y divide-border">
                            {orders.map((order, index) => (
                                <tr key={order.code}>
                                    <td className="px-4 py-4">
                                        <input
                                            type="checkbox"
                                            checked={selectedOrderIds.includes(
                                                order.id,
                                            )}
                                            onChange={() =>
                                                toggleSelectOne(order.id)
                                            }
                                        />
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="flex items-center gap-3">
                                            <div className="line-clamp-2">
                                                {order.customerName}
                                                <div className="line-clamp-2">
                                                    {order.customerPhone}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-muted">
                                        {order.agentName || '—'}
                                    </td>
                                    <td className="px-6 py-4 whitespace-pre-wrap">
                                        <div className="whitespace-pre-wrap break-words">
                                            {order.shipping_address}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="flex items-center gap-2">
                                            <span>{order.orderCount}</span>
                                            <button
                                                onClick={(e) =>
                                                    handleProductListClick(
                                                        order.id,
                                                        e,
                                                    )
                                                }
                                                className="text-muted hover:text-foreground focus:outline-none"
                                                title="View Items"
                                            >
                                                <i className="fa-solid fa-chevron-down text-xs"></i>
                                            </button>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {formatOrderDate(
                                            order.createdDate || '',
                                        )}
                                    </td>
                                    <td className="text-center">
                                        <a
                                            title="Download Invoice"
                                            href={route(
                                                'admin.orders.downloadInvoice',
                                                { id: order.id },
                                            )}
                                            target="_blank"
                                        >
                                            {order.orderPrefix} {order.code}
                                            <span className="text-lg text-theme-orange pl-1">
                                                <i className="fa-solid fa-file-arrow-down"></i>
                                            </span>
                                        </a>
                                        {!!order.isPosOrder && (
                                            <a
                                                title="Download Challan"
                                                href={route(
                                                    'admin.orders.downloadChallan',
                                                    { id: order.id },
                                                )}
                                                target="_blank"
                                            >
                                                <span className="text-lg text-theme-orange pl-1">
                                                    <i className="fa-light fa-file-contract"></i>
                                                </span>
                                            </a>
                                        )}
                                        {order.isExchangeOrder && (
                                            <a
                                                href={`/admin/orders/${order.originalOrderRawCode}`}
                                                title="Exchange order - click to view original"
                                                className="ml-1"
                                            >
                                                <span className="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                                    EXC
                                                </span>
                                            </a>
                                        )}
                                        {order.hasExchange && !order.isExchangeOrder && (
                                            <a
                                                href={`/admin/orders/${order.exchangeOrderRawCode}`}
                                                title="Has exchange - click to view exchange order"
                                                className="ml-1"
                                            >
                                                <span className="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                                    EXC
                                                </span>
                                            </a>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {formatOrderSource(
                                            order.isPosOrder,
                                            order.source,
                                        )}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="line-clamp-2">
                                            <h6>
                                                {currencyFormatter(
                                                    order.totalAmount,
                                                )}
                                            </h6>
                                            {order.paymentStatus === 'paid' ? (
                                                <div className="inline-flex items-center gap-1.5">
                                                    <i className="fa-solid fa-circle-check text-green-500"></i>
                                                    <span>Paid Online</span>
                                                </div>
                                            ) : (
                                                <div>Cash On Delivery</div>
                                            )}
                                        </div>
                                    </td>
                                    <td className="px-0 py-0">
                                        <div className="px-3 py-3">
                                            <SelectInput
                                                name="delivery_status"
                                                placeholder=""
                                                value={order.deliveryStatus}
                                                options={[
                                                    {
                                                        id: 'order_placed',
                                                        name: 'Order Placed',
                                                    },
                                                    {
                                                        id: 'confirmed',
                                                        name: 'Confirmed',
                                                    },
                                                    {
                                                        id: 'processing',
                                                        name: 'Processing',
                                                    },
                                                    {
                                                        id: 'cancelled',
                                                        name: 'Cancelled',
                                                    },
                                                    {
                                                        id: 'shipped',
                                                        name: 'Shipped',
                                                    },
                                                    {
                                                        id: 'delivered',
                                                        name: 'Delivered',
                                                    },
                                                ]}
                                                onChange={(e) => {
                                                    handleStatusUpdate(
                                                        e.name,
                                                        e.id,
                                                        order.id,
                                                    );
                                                }}
                                                getOptionLabel={(item) => item.name}
                                                getOptionValue={(item) => item.id}
                                                groupClassName="grow"
                                                statusColorClass={`${
                                                    statusColorMap[
                                                        order.deliveryStatus ??
                                                            'order_placed'
                                                    ]
                                                }`}
                                                statusTextClass={
                                                    statusTextColorMap[
                                                        order.deliveryStatus ??
                                                            'order_placed'
                                                    ] ?? '!text-white'
                                                }
                                            />
                                            {renderLatestDeliveryUpdate(order)}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 w-16">
                                        <SelectPartner order={order} />
                                    </td>
                                    <td className="px-6 py-4 text-right text-sm font-medium">
                                        <div className="flex flex-col items-end gap-1">
                                            <div className="flex items-center gap-1">
                                                <button
                                                    className="text-primary hover:text-primary/80 text-lg p-1"
                                                    title="View Order Details"
                                                    onClick={() =>
                                                        handleViewOrder(
                                                            order.id,
                                                        )
                                                    }
                                                >
                                                    <i className="fa-solid fa-eye"></i>
                                                </button>
                                                <button
                                                    className="text-blue-500 hover:text-blue-700 text-lg p-1"
                                                    title="Edit Order"
                                                    onClick={() =>
                                                        handleEditOrder(
                                                            order.id,
                                                        )
                                                    }
                                                >
                                                    <i className="fa-solid fa-pen-to-square"></i>
                                                </button>
                                            </div>
                                            <div className="flex items-center gap-1">
                                                <button
                                                    className="text-red-500 hover:text-red-700 text-lg p-1"
                                                    title="Check Courier Information"
                                                    onClick={(e) =>
                                                        handleFraudCheckClick(
                                                            e,
                                                            order.customerPhone,
                                                        )
                                                    }
                                                >
                                                    <i className="fa-solid fa-user-shield"></i>
                                                </button>
                                                <button
                                                    className="text-orange-500 hover:text-orange-700 text-lg p-1"
                                                    title="Delete Order"
                                                    onClick={() => {
                                                        // TODO: Implement delete functionality
                                                        console.log(
                                                            'Delete order:',
                                                            order.id,
                                                        );
                                                    }}
                                                >
                                                    <i className="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {orders.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={11}
                                        className="text-center py-4 text-muted"
                                    >
                                        No orders found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Pagination */}
                <div className="p-4 border-t border-border">
                    <div className="flex justify-between items-center">
                        <p className="text-sm text-muted">
                            Showing page {pagination.current_page} of{' '}
                            {pagination.last_page} ({pagination.total} orders)
                        </p>
                        <div className="flex items-center space-x-4">
                            <div className="flex items-center gap-2 text-sm text-muted">
                                <select
                                    value={perPage}
                                    onChange={(e) =>
                                        handlePerPageChange(
                                            Number(e.target.value),
                                        )
                                    }
                                    className="px-2 py-1 text-sm border border-border rounded bg-card focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value={10}>10 per page</option>
                                    <option value={15}>15 per page</option>
                                    <option value={25}>25 per page</option>
                                    <option value={50}>50 per page</option>
                                    <option value={100}>100 per page</option>
                                </select>
                            </div>
                            <div className="flex space-x-2">
                                <button
                                    onClick={() =>
                                        handlePageChange(
                                            pagination.current_page - 1,
                                        )
                                    }
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
                                    onClick={() =>
                                        handlePageChange(
                                            pagination.current_page + 1,
                                        )
                                    }
                                    disabled={
                                        pagination.current_page ===
                                        pagination.last_page
                                    }
                                    className={`px-3 py-1 rounded ${
                                        pagination.current_page ===
                                        pagination.last_page
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

            {/* Bulk Courier Order Modal */}
            <BulkCourierOrderModal
                isOpen={isBulkCourierModalOpen}
                onClose={() => setIsBulkCourierModalOpen(false)}
                selectedOrders={orders.filter((order) =>
                    selectedOrderIds.includes(order.id),
                )}
                onSuccess={() => {
                    // Refresh orders to show updated courier status
                    fetchOrders();
                    // Clear selection
                    setSelectedOrderIds([]);
                }}
            />

            {/* Bulk Status Update Modal */}
            <BulkStatusUpdateModal
                isOpen={isBulkStatusModalOpen}
                onClose={() => setIsBulkStatusModalOpen(false)}
                selectedOrderIds={selectedOrderIds}
                onSuccess={() => {
                    fetchOrders();
                    setSelectedOrderIds([]);
                }}
            />
        </div>
    );
};

export default OrderPage;
