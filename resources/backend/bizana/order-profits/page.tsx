import React, { useState, useEffect, useCallback } from 'react';
import { createRoot } from 'react-dom/client';
import '../../css/app.css';
import DateRangePicker from '../../js/bizana/components/DateRangePicker';

interface Shop {
    id: number;
    name: string;
}

interface ProfitLog {
    id: number;
    order_id: number;
    revenue: number;
    cogs: number;
    admin_commission: number;
    seller_earning: number;
    agent_commission: number;
    shipping_cost: number;
    tax_collected: number;
    discount_given: number;
    gross_profit: number;
    net_profit: number;
    profit_margin_pct: number;
    order: {
        id: number;
        order_code: number;
        delivery_status: string;
        payment_status: string;
        created_at: string;
        shop: { id: number; name: string } | null;
    };
}

interface ProfitSummary {
    total_orders: number;
    total_revenue: number;
    total_cogs: number;
    total_admin_commission: number;
    total_seller_earning: number;
    total_agent_commission: number;
    total_shipping_cost: number;
    total_tax_collected: number;
    total_discount_given: number;
    total_gross_profit: number;
    total_net_profit: number;
    avg_profit_margin_pct: number;
}

interface ItemBreakdown {
    id: number;
    product_name: string;
    variation_sku: string;
    qty: number;
    unit_price: number;
    unit_cost_price: number;
    total_price: number;
    total_cost: number;
    total_tax: number;
    total_discount: number;
    item_profit: number;
    profit_margin_pct: number;
}

interface PaginationInfo {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

const deliveryStatuses = [
    { id: 'all', label: 'All' },
    { id: 'order_placed', label: 'Order Placed' },
    { id: 'confirmed', label: 'Confirmed' },
    { id: 'processing', label: 'Processing' },
    { id: 'shipped', label: 'Shipped' },
    { id: 'delivered', label: 'Delivered' },
    { id: 'cancelled', label: 'Cancelled' },
];

const paymentStatuses = [
    { id: 'all', label: 'All' },
    { id: 'paid', label: 'Paid' },
    { id: 'unpaid', label: 'Unpaid' },
    { id: 'partial', label: 'Partial' },
];

function OrderProfitsPage() {
    const [profitLogs, setProfitLogs] = useState<ProfitLog[]>([]);
    const [shops, setShops] = useState<Shop[]>([]);
    const [summary, setSummary] = useState<ProfitSummary>({
        total_orders: 0, total_revenue: 0, total_cogs: 0,
        total_admin_commission: 0, total_seller_earning: 0,
        total_agent_commission: 0, total_shipping_cost: 0,
        total_tax_collected: 0, total_discount_given: 0,
        total_gross_profit: 0, total_net_profit: 0,
        avg_profit_margin_pct: 0,
    });
    const [pagination, setPagination] = useState<PaginationInfo>({
        current_page: 1, last_page: 1, per_page: 25, total: 0,
    });
    const [startDate, setStartDate] = useState('');
    const [endDate, setEndDate] = useState('');
    const [selectedShop, setSelectedShop] = useState('');
    const [deliveryStatus, setDeliveryStatus] = useState('all');
    const [paymentStatus, setPaymentStatus] = useState('all');
    const [loading, setLoading] = useState(true);
    const [exporting, setExporting] = useState(false);
    const [expandedOrder, setExpandedOrder] = useState<number | null>(null);
    const [itemBreakdown, setItemBreakdown] = useState<ItemBreakdown[]>([]);
    const [loadingDetail, setLoadingDetail] = useState(false);

    const getApiPrefix = () => (window as any).bizanaConfig?.apiPrefix || '/admin/api/bizana';
    const getCsrfToken = () => (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || '';

    const fetchShops = async () => {
        try {
            const res = await fetch(`${getApiPrefix()}/shops/list`, { headers: { 'X-CSRF-TOKEN': getCsrfToken() } });
            if (res.ok) { const data = await res.json(); setShops(data.shops || []); }
        } catch (e) { console.error('Error fetching shops:', e); }
    };

    const buildParams = useCallback((page = 1) => {
        const params = new URLSearchParams();
        if (startDate) params.append('date_from', startDate);
        if (endDate) params.append('date_to', endDate);
        if (selectedShop) params.append('shop_id', selectedShop);
        if (deliveryStatus !== 'all') params.append('delivery_status', deliveryStatus);
        if (paymentStatus !== 'all') params.append('payment_status', paymentStatus);
        params.append('page', page.toString());
        return params;
    }, [startDate, endDate, selectedShop, deliveryStatus, paymentStatus]);

    const fetchProfitLogs = useCallback(async (page = 1) => {
        try {
            setLoading(true);
            const res = await fetch(`${getApiPrefix()}/order-profits?${buildParams(page).toString()}`, {
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            });
            if (!res.ok) throw new Error('Failed');
            const data = await res.json();
            setProfitLogs(data.data || []);
            setPagination({
                current_page: data.current_page,
                last_page: data.last_page,
                per_page: data.per_page,
                total: data.total,
            });
        } catch (e) { console.error('Error fetching profit logs:', e); }
        finally { setLoading(false); }
    }, [buildParams]);

    const fetchSummary = useCallback(async () => {
        try {
            const res = await fetch(`${getApiPrefix()}/order-profits/summary?${buildParams().toString()}`, {
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            });
            if (res.ok) { const data = await res.json(); setSummary(data); }
        } catch (e) { console.error('Error fetching summary:', e); }
    }, [buildParams]);

    const handleExport = async () => {
        try {
            setExporting(true);
            const res = await fetch(`${getApiPrefix()}/order-profits/export?${buildParams().toString()}`, {
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            });
            if (!res.ok) throw new Error('Export failed');
            const blob = await res.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            const disposition = res.headers.get('Content-Disposition');
            const match = disposition?.match(/filename="(.+)"/);
            a.download = match ? match[1] : 'order-profit-report.csv';
            document.body.appendChild(a); a.click(); a.remove();
            window.URL.revokeObjectURL(url);
        } catch (e) { console.error('Export error:', e); alert('Export failed.'); }
        finally { setExporting(false); }
    };

    const toggleOrderDetail = async (orderId: number) => {
        if (expandedOrder === orderId) { setExpandedOrder(null); setItemBreakdown([]); return; }
        setExpandedOrder(orderId);
        setLoadingDetail(true);
        try {
            const res = await fetch(`${getApiPrefix()}/order-profits/${orderId}`, {
                headers: { 'X-CSRF-TOKEN': getCsrfToken() },
            });
            if (res.ok) {
                const data = await res.json();
                setItemBreakdown(data.item_breakdown || []);
            }
        } catch (e) { console.error('Error fetching detail:', e); }
        finally { setLoadingDetail(false); }
    };

    const refreshData = (page = 1) => {
        fetchProfitLogs(page);
        fetchSummary();
    };

    useEffect(() => { fetchShops(); refreshData(1); }, []);

    const fmt = (n: number) => parseFloat(String(n)).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const statusColor = (status: string) => {
        switch (status) {
            case 'delivered': return 'bg-green-100 text-green-800';
            case 'cancelled': return 'bg-red-100 text-red-800';
            case 'shipped': return 'bg-blue-100 text-blue-800';
            case 'processing': return 'bg-yellow-100 text-yellow-800';
            case 'confirmed': return 'bg-indigo-100 text-indigo-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    };

    const profitColor = (val: number) => val >= 0 ? 'text-green-600' : 'text-red-600';

    const SummaryCard = ({ label, value, color }: { label: string; value: string; color?: string }) => (
        <div className="bg-card rounded-lg shadow p-4 border border-border">
            <div className="text-xs text-muted font-medium uppercase tracking-wider">{label}</div>
            <div className={`text-lg font-bold mt-1 ${color || 'text-foreground'}`}>{value}</div>
        </div>
    );

    return (
        <div>
            <div className="card rounded-lg shadow p-6 mb-6">
                <div className="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 className="text-lg font-semibold text-foreground">Order Profit Report</h3>
                        <p className="text-sm text-muted mt-1">{summary.total_orders} orders analyzed</p>
                    </div>
                    <div className="flex items-center gap-4 flex-wrap">
                        <DateRangePicker startDate={startDate} endDate={endDate} onDateChange={(from, to) => { setStartDate(from); setEndDate(to); refreshData(1); }} />
                        <select value={selectedShop} onChange={(e) => { setSelectedShop(e.target.value); refreshData(1); }} className="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Shops</option>
                            {shops.map((s) => <option key={s.id} value={s.id}>{s.name}</option>)}
                        </select>
                        <select value={deliveryStatus} onChange={(e) => { setDeliveryStatus(e.target.value); refreshData(1); }} className="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {deliveryStatuses.map((s) => <option key={s.id} value={s.id}>{s.label}</option>)}
                        </select>
                        <select value={paymentStatus} onChange={(e) => { setPaymentStatus(e.target.value); refreshData(1); }} className="px-4 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            {paymentStatuses.map((s) => <option key={s.id} value={s.id}>{s.label}</option>)}
                        </select>
                        <button onClick={handleExport} disabled={exporting} className="text-white px-4 py-2 rounded-md flex items-center gap-2 transition-colors disabled:opacity-50" style={{ backgroundColor: '#4285F4' }}>
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            {exporting ? 'Exporting...' : 'Export CSV'}
                        </button>
                    </div>
                </div>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <SummaryCard label="Revenue" value={fmt(summary.total_revenue)} />
                <SummaryCard label="COGS" value={fmt(summary.total_cogs)} />
                <SummaryCard label="Gross Profit" value={fmt(summary.total_gross_profit)} color={profitColor(summary.total_gross_profit)} />
                <SummaryCard label="Net Profit" value={fmt(summary.total_net_profit)} color={profitColor(summary.total_net_profit)} />
                <SummaryCard label="Avg Margin" value={`${summary.avg_profit_margin_pct}%`} color={profitColor(summary.avg_profit_margin_pct)} />
                <SummaryCard label="Admin Commission" value={fmt(summary.total_admin_commission)} />
            </div>

            <div className="card rounded-lg shadow overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="bg-muted/30 border-b border-border">
                                <th className="px-4 py-3 text-left font-medium text-muted">Order</th>
                                <th className="px-4 py-3 text-left font-medium text-muted">Shop</th>
                                <th className="px-4 py-3 text-left font-medium text-muted">Status</th>
                                <th className="px-4 py-3 text-right font-medium text-muted">Revenue</th>
                                <th className="px-4 py-3 text-right font-medium text-muted">COGS</th>
                                <th className="px-4 py-3 text-right font-medium text-muted">Admin Comm.</th>
                                <th className="px-4 py-3 text-right font-medium text-muted">Agent Comm.</th>
                                <th className="px-4 py-3 text-right font-medium text-muted">Shipping</th>
                                <th className="px-4 py-3 text-right font-medium text-muted">Gross Profit</th>
                                <th className="px-4 py-3 text-right font-medium text-muted">Net Profit</th>
                                <th className="px-4 py-3 text-right font-medium text-muted">Margin %</th>
                            </tr>
                        </thead>
                        <tbody>
                            {loading ? (
                                <tr><td colSpan={11} className="px-4 py-8 text-center text-muted">Loading...</td></tr>
                            ) : profitLogs.length === 0 ? (
                                <tr><td colSpan={11} className="px-4 py-8 text-center text-muted">No profit data found.</td></tr>
                            ) : (
                                <>
                                    {profitLogs.map((log) => (
                                        <React.Fragment key={log.id}>
                                            <tr
                                                className="border-b border-border hover:bg-muted/5 cursor-pointer"
                                                onClick={() => toggleOrderDetail(log.order_id)}
                                            >
                                                <td className="px-4 py-3 font-medium">
                                                    <span className="text-blue-600">#{log.order?.order_code}</span>
                                                    <span className="ml-2 text-xs text-muted">{log.order?.created_at ? new Date(log.order.created_at).toLocaleDateString() : ''}</span>
                                                </td>
                                                <td className="px-4 py-3">{log.order?.shop?.name || 'N/A'}</td>
                                                <td className="px-4 py-3">
                                                    <span className={`px-2 py-0.5 text-xs font-medium rounded-full ${statusColor(log.order?.delivery_status || '')}`}>
                                                        {log.order?.delivery_status?.replace(/_/g, ' ')}
                                                    </span>
                                                </td>
                                                <td className="px-4 py-3 text-right">{fmt(log.revenue)}</td>
                                                <td className="px-4 py-3 text-right">{fmt(log.cogs)}</td>
                                                <td className="px-4 py-3 text-right">{fmt(log.admin_commission)}</td>
                                                <td className="px-4 py-3 text-right">{fmt(log.agent_commission || 0)}</td>
                                                <td className="px-4 py-3 text-right">{fmt(log.shipping_cost)}</td>
                                                <td className={`px-4 py-3 text-right font-medium ${profitColor(log.gross_profit)}`}>{fmt(log.gross_profit)}</td>
                                                <td className={`px-4 py-3 text-right font-medium ${profitColor(log.net_profit)}`}>{fmt(log.net_profit)}</td>
                                                <td className={`px-4 py-3 text-right font-medium ${profitColor(log.profit_margin_pct)}`}>{log.profit_margin_pct}%</td>
                                            </tr>
                                            {expandedOrder === log.order_id && (
                                                <tr>
                                                    <td colSpan={11} className="bg-muted/10 px-6 py-4">
                                                        {loadingDetail ? (
                                                            <div className="text-center text-muted py-2">Loading item breakdown...</div>
                                                        ) : itemBreakdown.length === 0 ? (
                                                            <div className="text-center text-muted py-2">No items found.</div>
                                                        ) : (
                                                            <table className="w-full text-xs">
                                                                <thead>
                                                                    <tr className="border-b border-border">
                                                                        <th className="px-2 py-2 text-left text-muted">Product</th>
                                                                        <th className="px-2 py-2 text-left text-muted">SKU</th>
                                                                        <th className="px-2 py-2 text-right text-muted">Qty</th>
                                                                        <th className="px-2 py-2 text-right text-muted">Unit Price</th>
                                                                        <th className="px-2 py-2 text-right text-muted">Unit Cost</th>
                                                                        <th className="px-2 py-2 text-right text-muted">Total Price</th>
                                                                        <th className="px-2 py-2 text-right text-muted">Total Cost</th>
                                                                        <th className="px-2 py-2 text-right text-muted">Item Profit</th>
                                                                        <th className="px-2 py-2 text-right text-muted">Margin %</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    {itemBreakdown.map((item) => (
                                                                        <tr key={item.id} className="border-b border-border/50">
                                                                            <td className="px-2 py-1.5">{item.product_name}</td>
                                                                            <td className="px-2 py-1.5 text-muted">{item.variation_sku}</td>
                                                                            <td className="px-2 py-1.5 text-right">{item.qty}</td>
                                                                            <td className="px-2 py-1.5 text-right">{fmt(item.unit_price)}</td>
                                                                            <td className="px-2 py-1.5 text-right">{fmt(item.unit_cost_price)}</td>
                                                                            <td className="px-2 py-1.5 text-right">{fmt(item.total_price)}</td>
                                                                            <td className="px-2 py-1.5 text-right">{fmt(item.total_cost)}</td>
                                                                            <td className={`px-2 py-1.5 text-right font-medium ${profitColor(item.item_profit)}`}>{fmt(item.item_profit)}</td>
                                                                            <td className={`px-2 py-1.5 text-right ${profitColor(item.profit_margin_pct)}`}>{item.profit_margin_pct}%</td>
                                                                        </tr>
                                                                    ))}
                                                                </tbody>
                                                            </table>
                                                        )}
                                                    </td>
                                                </tr>
                                            )}
                                        </React.Fragment>
                                    ))}
                                </>
                            )}
                        </tbody>
                    </table>
                </div>

                {pagination.last_page > 1 && (
                    <div className="flex items-center justify-between px-4 py-3 border-t border-border">
                        <div className="text-sm text-muted">
                            Showing {((pagination.current_page - 1) * pagination.per_page) + 1} to {Math.min(pagination.current_page * pagination.per_page, pagination.total)} of {pagination.total}
                        </div>
                        <div className="flex items-center gap-1">
                            <button onClick={() => fetchProfitLogs(pagination.current_page - 1)} disabled={pagination.current_page === 1} className="px-3 py-1 text-sm border border-border rounded-md disabled:opacity-50 hover:bg-muted/50">Prev</button>
                            {Array.from({ length: pagination.last_page }, (_, i) => i + 1)
                                .filter((p) => p === 1 || p === pagination.last_page || Math.abs(p - pagination.current_page) <= 1)
                                .map((page, idx, arr) => {
                                    const prev = arr[idx - 1];
                                    const showEllipsis = prev && page - prev > 1;
                                    return (
                                        <React.Fragment key={page}>
                                            {showEllipsis && <span className="px-2 text-muted">...</span>}
                                            <button onClick={() => fetchProfitLogs(page)} className={`px-3 py-1 text-sm border rounded-md ${page === pagination.current_page ? 'bg-blue-500 text-white border-blue-500' : 'border-border hover:bg-muted/50'}`}>{page}</button>
                                        </React.Fragment>
                                    );
                                })}
                            <button onClick={() => fetchProfitLogs(pagination.current_page + 1)} disabled={pagination.current_page === pagination.last_page} className="px-3 py-1 text-sm border border-border rounded-md disabled:opacity-50 hover:bg-muted/50">Next</button>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

const container = document.getElementById('bizana-app');
if (container) {
    const root = createRoot(container);
    root.render(<OrderProfitsPage />);
}
