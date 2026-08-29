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
    customerOrderCode?: number;
    customerOrderCodeToShow?: string;
    customerName?: string;
    customerPhone?: string;
    agentId?: number | null;
    agentName?: string | null;
    createdDate?: string;
    orderCount?: number;
    totalItemQuantity?: number;
    firstItem?: {
        name: string;
        variationCode?: string | null;
        quantity: number;
        image?: string | null;
    } | null;
    subtotalAmount: number;
    shippingChargeAmount: number;
    discountAmount: number;
    couponDiscountAmount: number;
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
    courier_partner?: string;
    courier_devlivery_status?: string;
    tracking_number?: string;
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
    const [deleteOrderId, setDeleteOrderId] = useState<number | null>(null);
    const [isDeletingOrder, setIsDeletingOrder] = useState(false);
    const [deleteError, setDeleteError] = useState<string | null>(null);

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

    const handleDeleteOrder = (orderId: number) => {
        setDeleteError(null);
        setDeleteOrderId(orderId);
    };

    const closeDeleteConfirm = () => {
        if (isDeletingOrder) return;
        setDeleteOrderId(null);
        setDeleteError(null);
    };

    const confirmDeleteOrder = async () => {
        if (!deleteOrderId) return;
        setIsDeletingOrder(true);
        setDeleteError(null);
        try {
            await axios.delete(`/admin/api/orders/${deleteOrderId}`);
            setSelectedOrderIds((prev) =>
                prev.filter((id) => id !== deleteOrderId),
            );
            await fetchOrders(pagination.current_page);
            setDeleteOrderId(null);
        } catch (error) {
            console.error('Error deleting order:', error);
            setDeleteError('Could not delete this order. Please try again.');
        } finally {
            setIsDeletingOrder(false);
        }
    };

    const handleDownload = async () => {
        if (selectedOrderIds.length === 0) {
            alert('Please select at least one order');
            return;
        }

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

    const handleDownloadSinglePage = async () => {
        if (selectedOrderIds.length === 0) {
            alert('Please select at least one order');
            return;
        }

        try {
            const response = await axios.get(
                `/admin/single-page-invoice-download/${selectedOrderIds.join(
                    ',',
                )}`,
                {
                    responseType: 'blob',
                },
            );

            const blob = new Blob([response.data], { type: 'application/pdf' });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'single_page_invoices.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Error downloading single page invoices:', error);
        }
    };

    const handleDownloadIndividual = async () => {
        if (selectedOrderIds.length === 0) {
            alert('Please select at least one order');
            return;
        }

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

    const orderPendingDeletion = orders.find((order) => order.id === deleteOrderId);

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
                                    } else if (value === 'download_invoice_single_page') {
                                        handleDownloadSinglePage();
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
                                    value="download_invoice_single_page"
                                    className={nativeSelectOptionClass}
                                >
                                    Download Invoices (Single Page)
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
                                        <div className="mt-1 text-xs text-muted">
                                            {order.shipping_address || 'No shipping address'}
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
                                            {order.customerOrderCodeToShow || `${order.orderPrefix || ''}${order.customerOrderCode || order.code}`}
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
                                        <div className="text-xs text-muted">
                                            Subtotal {currencyFormatter(order.subtotalAmount)}
                                            {' · '}Delivery {currencyFormatter(order.shippingChargeAmount)}
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
                                            {order.firstItem?.image && (
                                                <img
                                                    src={order.firstItem.image}
                                                    alt={order.firstItem.name}
                                                    className="h-10 w-10 rounded border border-border object-cover"
                                                />
                                            )}
                                            <div className="min-w-0">
                                                <div className="line-clamp-1 text-sm font-medium">
                                                    {order.firstItem?.name || 'No product'}
                                                </div>
                                                <div className="text-xs text-muted">
                                                    Qty {order.totalItemQuantity || 0} · {order.orderCount || 0} item{order.orderCount === 1 ? '' : 's'}
                                                </div>
                                            </div>
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
                                            onClick={() =>
                                                handleDeleteOrder(order.id)
                                            }
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
                <div className="hidden lg:block overflow-visible">
                    <table className="divide-y divide-border product-list-table w-full table-fixed">
                        <thead>
                            <tr className="theme-table__head">
                                <th className="w-[38px] px-2 py-3"></th>
                                <th className="w-[108px] px-2 py-3 text-left text-xs font-medium text-muted uppercase">
                                    Order
                                </th>
                                <th className="w-[15%] px-2 py-3 text-left text-xs font-medium text-muted uppercase">
                                    Customer & Address
                                </th>
                                <th className="w-[18%] px-2 py-3 text-left text-xs font-medium text-muted uppercase">
                                    Products
                                </th>
                                <th className="w-[126px] px-2 py-3 text-left text-xs font-medium text-muted uppercase">
                                    Amounts
                                </th>
                                <th className="w-[138px] px-2 py-3 text-left text-xs font-medium text-muted uppercase">
                                    Order Status
                                </th>
                                <th className="w-[144px] px-2 py-3 text-left text-xs font-medium text-muted uppercase">
                                    Courier
                                </th>
                                <th className="w-[92px] px-2 py-3 text-left text-xs font-medium text-muted uppercase">
                                    Assigned To
                                </th>
                                <th className="w-[68px] px-2 py-3 text-right text-xs font-medium text-muted uppercase"></th>
                            </tr>
                        </thead>
                        <tbody className="theme-table__body divide-y divide-border">
                            {orders.map((order) => (
                                <tr key={order.code} className="align-top hover:bg-muted/10">
                                    <td className="px-2 !py-4">
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
                                    <td className="px-2 !py-4">
                                        <a
                                            href={`/admin/orders/${order.code}`}
                                            className="font-semibold text-primary hover:underline"
                                        >
                                            {order.customerOrderCodeToShow || `${order.orderPrefix || ''}${order.customerOrderCode || order.code}`}
                                        </a>
                                        <div className="mt-1 flex flex-wrap gap-1">
                                            <span className="rounded bg-sky-100 px-1.5 py-0.5 text-[10px] font-semibold text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                                                {formatOrderSource(order.isPosOrder, order.source) || 'Website'}
                                            </span>
                                            {order.isExchangeOrder && (
                                                <a href={`/admin/orders/${order.originalOrderRawCode}`} className="rounded bg-purple-100 px-1.5 py-0.5 text-[10px] font-semibold text-purple-700 dark:bg-purple-900/40 dark:text-purple-300">
                                                    EXCHANGE
                                                </a>
                                            )}
                                            {order.hasExchange && !order.isExchangeOrder && (
                                                <a href={`/admin/orders/${order.exchangeOrderRawCode}`} className="rounded bg-indigo-100 px-1.5 py-0.5 text-[10px] font-semibold text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                                                    HAS EXCHANGE
                                                </a>
                                            )}
                                        </div>
                                        <div className="mt-1 text-xs text-muted">
                                            {formatOrderDate(order.createdDate || '')}
                                        </div>
                                    </td>
                                    <td className="px-2 !py-4">
                                        <div className="font-semibold text-foreground">
                                            {order.customerName || 'Guest customer'}
                                        </div>
                                        <button
                                            type="button"
                                            onClick={(e) => handleFraudCheckClick(e, order.customerPhone)}
                                            className="mt-0.5 text-xs text-primary hover:underline"
                                            title="Check courier information"
                                        >
                                            {order.customerPhone || 'No phone'}
                                        </button>
                                        <div className="mt-1 line-clamp-2 text-xs leading-4 text-muted" title={order.shipping_address}>
                                            {order.shipping_address || 'No shipping address'}
                                        </div>
                                    </td>
                                    <td className="px-2 !py-4">
                                        <div className="flex items-start gap-2.5">
                                            <div className="h-11 w-11 shrink-0 overflow-hidden rounded border border-border bg-muted/20">
                                                {order.firstItem?.image ? (
                                                    <img
                                                        src={order.firstItem.image}
                                                        alt={order.firstItem.name}
                                                        className="h-full w-full object-cover"
                                                    />
                                                ) : (
                                                    <div className="flex h-full w-full items-center justify-center text-muted">
                                                        <i className="fa-regular fa-image"></i>
                                                    </div>
                                                )}
                                            </div>
                                            <div className="min-w-0">
                                                <div className="line-clamp-2 text-sm font-medium leading-5">
                                                    {order.firstItem?.name || 'No product'}
                                                </div>
                                                <div className="mt-0.5 text-xs text-muted">
                                                    {order.firstItem?.variationCode && `SKU: ${order.firstItem.variationCode} · `}
                                                    Qty: {order.totalItemQuantity || order.firstItem?.quantity || 0}
                                                    {' · '}{order.orderCount || 0} item{order.orderCount === 1 ? '' : 's'}
                                                </div>
                                            </div>
                                            <button
                                                onClick={(e) =>
                                                    handleProductListClick(
                                                        order.id,
                                                        e,
                                                    )
                                                }
                                                className="ml-auto shrink-0 rounded border border-border px-2 py-1 text-xs text-primary hover:bg-muted/20"
                                                title="View all products"
                                            >
                                                View
                                            </button>
                                        </div>
                                    </td>
                                    <td className="px-2 !py-4 text-xs">
                                        <div className="flex justify-between gap-3 text-muted">
                                            <span>Subtotal</span>
                                            <span>{currencyFormatter(order.subtotalAmount)}</span>
                                        </div>
                                        <div className="mt-0.5 flex justify-between gap-3 text-muted">
                                            <span>Delivery</span>
                                            <span>{currencyFormatter(order.shippingChargeAmount)}</span>
                                        </div>
                                        {(Number(order.discountAmount) + Number(order.couponDiscountAmount)) > 0 && (
                                            <div className="mt-0.5 flex justify-between gap-3 text-red-500">
                                                <span>Discount</span>
                                                <span>-{currencyFormatter(Number(order.discountAmount) + Number(order.couponDiscountAmount))}</span>
                                            </div>
                                        )}
                                        <div className="mt-1 flex justify-between gap-3 border-t border-border pt-1 text-sm font-bold text-foreground">
                                            <span>Total</span>
                                            <span>{currencyFormatter(order.totalAmount)}</span>
                                        </div>
                                        <div className={`mt-1 text-[11px] font-semibold ${order.paymentStatus === 'paid' ? 'text-green-600' : 'text-amber-600'}`}>
                                            {order.paymentStatus === 'paid' ? 'Paid' : 'Cash on delivery / Unpaid'}
                                        </div>
                                    </td>
                                    <td className="px-2 !py-4">
                                        <div>
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
                                    <td className="px-2 !py-4">
                                        <SelectPartner order={order} />
                                        {(order.tracking_number || order.courier_devlivery_status) && (
                                            <div className="mt-1 text-[11px] leading-4 text-muted">
                                                {order.tracking_number && <div>Tracking: {order.tracking_number}</div>}
                                                {order.courier_devlivery_status && <div>Status: {order.courier_devlivery_status.replace(/[_-]/g, ' ')}</div>}
                                            </div>
                                        )}
                                    </td>
                                    <td className="px-2 !py-4">
                                        <div className="break-words text-sm font-medium">
                                            {order.agentName || 'Not assigned'}
                                        </div>
                                        <div className="mt-1 text-xs text-muted">
                                            {order.agentName ? 'Order agent' : 'Needs assignment'}
                                        </div>
                                    </td>
                                    <td className="px-2 !py-4 text-right text-sm font-medium">
                                        <div className="grid grid-cols-2 gap-1 justify-items-end">
                                            <a
                                                title="Download Invoice"
                                                href={route('admin.orders.downloadInvoice', { id: order.id })}
                                                target="_blank"
                                                className="flex h-7 w-7 items-center justify-center rounded text-theme-orange hover:bg-orange-50 hover:text-orange-600"
                                            >
                                                <i className="fa-solid fa-file-arrow-down"></i>
                                            </a>
                                            {!!order.isPosOrder && (
                                                <a
                                                    title="Download Challan"
                                                    href={route('admin.orders.downloadChallan', { id: order.id })}
                                                    target="_blank"
                                                    className="flex h-7 w-7 items-center justify-center rounded text-theme-orange hover:bg-orange-50 hover:text-orange-600"
                                                >
                                                    <i className="fa-light fa-file-contract"></i>
                                                </a>
                                            )}
                                                <button
                                                    className="flex h-7 w-7 items-center justify-center rounded text-primary hover:bg-primary/10 hover:text-primary/80"
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
                                                    className="flex h-7 w-7 items-center justify-center rounded text-blue-500 hover:bg-blue-50 hover:text-blue-700"
                                                    title="Edit Order"
                                                    onClick={() =>
                                                        handleEditOrder(
                                                            order.id,
                                                        )
                                                    }
                                                >
                                                    <i className="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <button
                                                    className="flex h-7 w-7 items-center justify-center rounded text-red-500 hover:bg-red-50 hover:text-red-700"
                                                    title="Delete Order"
                                                    onClick={() =>
                                                        handleDeleteOrder(
                                                            order.id,
                                                        )
                                                    }
                                                >
                                                    <i className="fa-solid fa-trash"></i>
                                                </button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {orders.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={9}
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

            {deleteOrderId !== null && (
                <div className="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 px-4">
                    <div className="w-full max-w-md rounded-lg border border-border bg-card p-5 shadow-xl">
                        <div className="flex items-start gap-3">
                            <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                                <i className="fa-solid fa-trash"></i>
                            </div>
                            <div className="min-w-0">
                                <h2 className="text-lg font-semibold text-foreground">
                                    Delete order?
                                </h2>
                                <p className="mt-1 text-sm leading-5 text-muted">
                                    This will remove{' '}
                                    <span className="font-semibold text-foreground">
                                        {orderPendingDeletion
                                            ? `${orderPendingDeletion.orderPrefix ?? ''}${orderPendingDeletion.code}`
                                            : 'this order'}
                                    </span>
                                    {orderPendingDeletion?.customerName
                                        ? ` for ${orderPendingDeletion.customerName}`
                                        : ''}
                                    .
                                </p>
                            </div>
                        </div>

                        {deleteError && (
                            <div className="mt-4 rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                                {deleteError}
                            </div>
                        )}

                        <div className="mt-5 flex justify-end gap-2">
                            <button
                                type="button"
                                onClick={closeDeleteConfirm}
                                disabled={isDeletingOrder}
                                className="rounded-md border border-border px-4 py-2 text-sm font-medium text-foreground hover:bg-muted/20 disabled:cursor-not-allowed disabled:opacity-60"
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                onClick={confirmDeleteOrder}
                                disabled={isDeletingOrder}
                                className="inline-flex items-center gap-2 rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-70"
                            >
                                {isDeletingOrder && (
                                    <i className="fa-solid fa-spinner fa-spin"></i>
                                )}
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            )}

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
