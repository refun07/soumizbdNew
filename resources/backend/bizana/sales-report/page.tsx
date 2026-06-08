import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import '../../css/app.css';
import DateRangePicker from '../../js/bizana/components/DateRangePicker';

interface Shop {
    id: number;
    name: string;
}

interface ProductSale {
    product_variation_id: number;
    sku: string;
    product_name: string;
    total_qty: number;
    unit_price: string;
    total_price: string;
    total_discount: string;
}

interface Summary {
    total_products: number;
    total_qty: number;
    total_revenue: number;
    total_discount: number;
    total_delivery_charge: number;
    total_tax: number;
    net_amount: number;
    total_orders: number;
}

interface PaginationInfo {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
}

function SalesReportPage() {
    const [productSales, setProductSales] = useState<ProductSale[]>([]);
    const [shops, setShops] = useState<Shop[]>([]);
    const [summary, setSummary] = useState<Summary>({
        total_products: 0,
        total_qty: 0,
        total_revenue: 0,
        total_discount: 0,
        total_delivery_charge: 0,
        total_tax: 0,
        net_amount: 0,
        total_orders: 0,
    });
    const [pagination, setPagination] = useState<PaginationInfo>({
        current_page: 1,
        last_page: 1,
        per_page: 25,
        total: 0,
        from: null,
        to: null,
    });
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [selectedShop, setSelectedShop] = useState('');
    const [activeTab, setActiveTab] = useState('all');
    const [loading, setLoading] = useState(true);
    const [exporting, setExporting] = useState(false);

    const tabs = [
        { id: 'all', label: 'All' },
        { id: 'order_placed', label: 'Order Placed' },
        { id: 'confirmed', label: 'Confirmed' },
        { id: 'processing', label: 'Processing' },
        { id: 'shipped', label: 'Shipped' },
        { id: 'delivered', label: 'Delivered' },
        { id: 'cancelled', label: 'Cancelled' },
    ];

    const getApiPrefix = () => {
        return (window as any).bizanaConfig?.apiPrefix || '/admin/api/bizana';
    };

    const getCsrfToken = () => {
        return (
            (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
        );
    };

    const fetchShops = async () => {
        try {
            const response = await fetch(`${getApiPrefix()}/shops/list`, {
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            });
            if (!response.ok) return;
            const data = await response.json();
            setShops(data.shops || []);
        } catch (error) {
            console.error('Error fetching shops:', error);
        }
    };

    const fetchSalesData = async (page = 1, overrides?: { tab?: string; shop?: string; dateFrom?: string; dateTo?: string }) => {
        try {
            setLoading(true);
            const tab = overrides?.tab ?? activeTab;
            const shop = overrides?.shop ?? selectedShop;
            const from = overrides?.dateFrom ?? startDate;
            const to = overrides?.dateTo ?? endDate;
            const params = new URLSearchParams();
            if (from) params.append('date_from', from);
            if (to) params.append('date_to', to);
            if (shop) params.append('shop_id', shop);
            if (tab !== 'all') params.append('delivery_status', tab);
            params.append('page', page.toString());

            const response = await fetch(
                `${getApiPrefix()}/sales-report/data?${params.toString()}`,
                {
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() },
                },
            );
            if (!response.ok) throw new Error('Failed to fetch sales data');

            const data = await response.json();
            setProductSales(data.productSales);
            setSummary(data.summary);
            setPagination(data.pagination);
        } catch (error) {
            console.error('Error fetching sales data:', error);
        } finally {
            setLoading(false);
        }
    };

    const handleExport = async () => {
        try {
            setExporting(true);
            const params = new URLSearchParams();
            if (startDate) params.append('date_from', startDate);
            if (endDate) params.append('date_to', endDate);
            if (selectedShop) params.append('shop_id', selectedShop);
            if (activeTab !== 'all') params.append('delivery_status', activeTab);

            const response = await fetch(
                `${getApiPrefix()}/sales-report/export?${params.toString()}`,
                {
                    headers: { 'X-CSRF-TOKEN': getCsrfToken() },
                },
            );
            if (!response.ok) throw new Error('Export failed');

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;

            const disposition = response.headers.get('Content-Disposition');
            const filenameMatch = disposition?.match(/filename="(.+)"/);
            a.download = filenameMatch ? filenameMatch[1] : 'sales_report.csv';

            document.body.appendChild(a);
            a.click();
            a.remove();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Error exporting:', error);
            alert('Failed to export. Please try again.');
        } finally {
            setExporting(false);
        }
    };

    const handleDateRangeChange = (from: string, to: string) => {
        setStartDate(from);
        setEndDate(to);
        fetchSalesData(1, { dateFrom: from, dateTo: to });
    };

    const handleTabChange = (tabId: string) => {
        setActiveTab(tabId);
        fetchSalesData(1, { tab: tabId });
    };

    const handleShopChange = (shopId: string) => {
        setSelectedShop(shopId);
        fetchSalesData(1, { shop: shopId });
    };

    useEffect(() => {
        fetchShops();
        fetchSalesData(1);
    }, []);

    const formatNumber = (num: number | string) => {
        return parseFloat(String(num)).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        });
    };

    const formatDateRange = () => {
        if (!startDate || !endDate) return '';
        const start = new Date(startDate + 'T00:00:00');
        const end = new Date(endDate + 'T00:00:00');
        const opts: Intl.DateTimeFormatOptions = { day: 'numeric', month: 'short' };
        return `${start.toLocaleDateString('en-US', opts)} – ${end.toLocaleDateString('en-US', opts)}`;
    };

    return (
        <div>
            <div className="card rounded-lg shadow p-6 mb-6">
                <div className="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 className="text-lg font-semibold text-foreground">
                            Sales Report — {formatDateRange()}
                        </h3>
                        <p className="text-sm text-muted mt-1">
                            {summary.total_orders} orders &middot; {summary.total_products} unique products
                        </p>
                    </div>
                    <div className="flex items-center gap-4 flex-wrap">
                        <DateRangePicker
                            startDate={startDate}
                            endDate={endDate}
                            onDateChange={handleDateRangeChange}
                        />
                        <select
                            value={selectedShop}
                            onChange={(e) => handleShopChange(e.target.value)}
                            className="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Shops</option>
                            {shops.map((shop) => (
                                <option key={shop.id} value={shop.id}>
                                    {shop.name}
                                </option>
                            ))}
                        </select>
                        <button
                            onClick={handleExport}
                            disabled={exporting}
                            className="text-white px-4 py-2 rounded-md flex items-center gap-2 transition-colors disabled:opacity-50"
                            style={{ backgroundColor: '#4285F4' }}
                            onMouseEnter={(e) =>
                                (e.currentTarget.style.backgroundColor = '#3367d6')
                            }
                            onMouseLeave={(e) =>
                                (e.currentTarget.style.backgroundColor = '#4285F4')
                            }
                        >
                            <svg
                                className="w-4 h-4"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                />
                            </svg>
                            {exporting ? 'Exporting...' : 'Export CSV'}
                        </button>
                    </div>
                </div>
            </div>

            <div className="card rounded-lg shadow mb-6">
                <div className="flex items-center gap-1 overflow-x-auto px-2 py-2">
                    {tabs.map((tab) => (
                        <button
                            key={tab.id}
                            onClick={() => handleTabChange(tab.id)}
                            className={`px-4 py-2 text-sm font-medium rounded-md whitespace-nowrap transition-colors ${
                                activeTab === tab.id
                                    ? 'bg-blue-500 text-white'
                                    : 'text-muted hover:bg-muted/10'
                            }`}
                        >
                            {tab.label}
                        </button>
                    ))}
                </div>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
                {[
                    {
                        label: 'Revenue',
                        value: formatNumber(summary.total_revenue),
                        color: '#4285F4',
                    },
                    {
                        label: 'Qty Sold',
                        value: summary.total_qty.toLocaleString(),
                        color: '#34A853',
                    },
                    {
                        label: 'Discount',
                        value: formatNumber(summary.total_discount),
                        color: '#EA4335',
                    },
                    {
                        label: 'Delivery Charges',
                        value: formatNumber(summary.total_delivery_charge),
                        color: '#FBBC04',
                    },
                    {
                        label: 'Tax',
                        value: formatNumber(summary.total_tax),
                        color: '#9C27B0',
                    },
                    {
                        label: 'Net Amount',
                        value: formatNumber(summary.net_amount),
                        color: '#0D9488',
                    },
                    {
                        label: 'Orders',
                        value: summary.total_orders.toLocaleString(),
                        color: '#607D8B',
                    },
                ].map((card) => (
                    <div key={card.label} className="card rounded-lg shadow p-4">
                        <p className="text-xs text-muted uppercase tracking-wide">
                            {card.label}
                        </p>
                        <p
                            className="text-xl font-bold mt-1"
                            style={{ color: card.color }}
                        >
                            {card.value}
                        </p>
                    </div>
                ))}
            </div>

            <div className="card rounded-lg shadow overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-border">
                        <thead className="theme-table__head">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                    SKU
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                    Product Name
                                </th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">
                                    Qty
                                </th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">
                                    Unit Price
                                </th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">
                                    Total Price
                                </th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-muted uppercase tracking-wider">
                                    Discount
                                </th>
                            </tr>
                        </thead>
                        <tbody className="theme-table__body divide-y divide-border">
                            {loading ? (
                                <tr>
                                    <td
                                        colSpan={6}
                                        className="px-6 py-12 text-center"
                                    >
                                        <div className="text-muted">
                                            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-muted mx-auto"></div>
                                            <p className="mt-2 text-sm">
                                                Loading sales data...
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            ) : productSales.length === 0 ? (
                                <tr>
                                    <td
                                        colSpan={6}
                                        className="px-6 py-12 text-center"
                                    >
                                        <div className="text-muted">
                                            <svg
                                                className="mx-auto h-12 w-12"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth={2}
                                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"
                                                />
                                            </svg>
                                            <h3 className="mt-2 text-sm font-medium text-foreground">
                                                No sales data found
                                            </h3>
                                            <p className="mt-1 text-sm text-muted">
                                                Try adjusting the date range or
                                                shop filter.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            ) : (
                                productSales.map((sale) => (
                                    <tr
                                        key={`${sale.product_variation_id}-${sale.unit_price}`}
                                        className="theme-table__row"
                                    >
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-mono text-muted">
                                            {sale.sku || 'N/A'}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">
                                            {sale.product_name}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-right text-foreground">
                                            {sale.total_qty}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-right text-foreground">
                                            {formatNumber(sale.unit_price)}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-right font-medium text-foreground">
                                            {formatNumber(sale.total_price)}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600">
                                            {parseFloat(sale.total_discount) > 0
                                                ? formatNumber(sale.total_discount)
                                                : '-'}
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            {pagination.last_page > 1 && (
                <div className="card rounded-lg shadow p-4 mt-6">
                    <div className="flex items-center justify-between">
                        <div className="text-sm text-foreground">
                            Showing {pagination.from || 0} to{' '}
                            {pagination.to || 0} of {pagination.total} products
                        </div>
                        <div className="flex items-center space-x-2">
                            <button
                                onClick={() =>
                                    fetchSalesData(pagination.current_page - 1)
                                }
                                disabled={pagination.current_page === 1}
                                className="px-3 py-1 text-sm border border-border rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-muted/50"
                            >
                                Previous
                            </button>
                            {Array.from(
                                { length: pagination.last_page },
                                (_, i) => i + 1,
                            )
                                .filter((page) => {
                                    return (
                                        page === 1 ||
                                        page === pagination.last_page ||
                                        Math.abs(page - pagination.current_page) <= 1
                                    );
                                })
                                .map((page, idx, arr) => {
                                    const prev = arr[idx - 1];
                                    const showEllipsis = prev && page - prev > 1;
                                    return (
                                        <React.Fragment key={page}>
                                            {showEllipsis && (
                                                <span className="px-2 text-muted">
                                                    ...
                                                </span>
                                            )}
                                            <button
                                                onClick={() =>
                                                    fetchSalesData(page)
                                                }
                                                className={`px-3 py-1 text-sm border rounded-md ${
                                                    page === pagination.current_page
                                                        ? 'bg-blue-500 text-white border-blue-500'
                                                        : 'border-border hover:bg-muted/50'
                                                }`}
                                            >
                                                {page}
                                            </button>
                                        </React.Fragment>
                                    );
                                })}
                            <button
                                onClick={() =>
                                    fetchSalesData(pagination.current_page + 1)
                                }
                                disabled={
                                    pagination.current_page === pagination.last_page
                                }
                                className="px-3 py-1 text-sm border border-border rounded-md disabled:opacity-50 disabled:cursor-not-allowed hover:bg-muted/50"
                            >
                                Next
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

const container = document.getElementById('bizana-app');
if (container) {
    const root = createRoot(container);
    root.render(<SalesReportPage />);
}
