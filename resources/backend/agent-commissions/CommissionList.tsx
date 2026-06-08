import React, { useState, useEffect, useCallback } from 'react';
import { translate } from '../react/utils/translate';
import Pagination from '../react/components/pagination/Pagination';
import { CommissionHistory, PaginatedCommissions, getMyCommissions, payCommission, payBulkCommissions } from './CommissionApi';

const currency = (window as any).config?.currency;

const formatPrice = (value: number): string => {
    if (!currency) return value.toFixed(2);
    const formatted = new Intl.NumberFormat('en-US', {
        maximumFractionDigits: currency.numOfDecimals || 0,
    }).format(value).replaceAll(',', currency.thousandSeparator || ',');

    if (currency.symbol?.position === 'amount_first') return formatted + (currency.symbol.show || '$');
    return (currency.symbol.show || '$') + formatted;
};

const formatDate = (dateString: string): string => {
    return new Date(dateString).toLocaleDateString('en-US', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const statusStyles: Record<string, string> = {
    pending: 'bg-yellow-100 text-yellow-800',
    earned: 'bg-green-100 text-green-800',
    paid: 'bg-blue-100 text-blue-800',
    cancelled: 'bg-red-100 text-red-800',
};

const formatCommissionLabel = (commission: CommissionHistory): string => {
    const mode = commission.commission_mode_used || 'percentage';
    switch (mode) {
        case 'fixed':
            return formatPrice(commission.commission_fixed_amount);
        case 'both_highest':
            return `max(${commission.commission_percentage}%, ${formatPrice(commission.commission_fixed_amount)})`;
        case 'both_additive':
            return `${commission.commission_percentage}% + ${formatPrice(commission.commission_fixed_amount)}`;
        case 'percentage':
        default:
            return `${commission.commission_percentage}%`;
    }
};

const CommissionList: React.FC = () => {
    const [commissions, setCommissions] = useState<PaginatedCommissions | null>(null);
    const [loading, setLoading] = useState(true);
    const [statusFilter, setStatusFilter] = useState<string>('');
    const [selectedIds, setSelectedIds] = useState<Set<number>>(new Set());
    const [paying, setPaying] = useState(false);

    const fetchCommissions = useCallback(async (page = 1) => {
        setLoading(true);
        try {
            const params: any = { page, per_page: 15 };
            if (statusFilter) params.status = statusFilter;
            const data = await getMyCommissions(params);
            setCommissions(data);
            setSelectedIds(new Set());
        } catch (err) {
            console.error('Failed to fetch commissions', err);
        } finally {
            setLoading(false);
        }
    }, [statusFilter]);

    useEffect(() => {
        fetchCommissions(1);
    }, [fetchCommissions]);

    const handlePaySingle = async (id: number) => {
        if (!confirm(translate('Mark this commission as paid?'))) return;
        setPaying(true);
        try {
            await payCommission(id);
            fetchCommissions(commissions?.meta.current_page || 1);
        } catch (err) {
            console.error('Failed to pay commission', err);
        } finally {
            setPaying(false);
        }
    };

    const handlePayBulk = async () => {
        if (selectedIds.size === 0) return;
        if (!confirm(translate(`Mark ${selectedIds.size} commission(s) as paid?`))) return;
        setPaying(true);
        try {
            await payBulkCommissions(Array.from(selectedIds));
            fetchCommissions(1);
        } catch (err) {
            console.error('Failed to pay bulk commissions', err);
        } finally {
            setPaying(false);
        }
    };

    const toggleSelect = (id: number) => {
        setSelectedIds((prev) => {
            const next = new Set(prev);
            if (next.has(id)) next.delete(id);
            else next.add(id);
            return next;
        });
    };

    const toggleSelectAll = (items: CommissionHistory[]) => {
        const earnedIds = items.filter((c) => c.status === 'earned').map((c) => c.id);
        if (earnedIds.every((id) => selectedIds.has(id))) {
            setSelectedIds(new Set());
        } else {
            setSelectedIds(new Set(earnedIds));
        }
    };

    const items = commissions?.data || [];

    return (
        <div className="card rounded-lg">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-b border-border">
                <h3 className="font-bold text-foreground">{translate('Commission History')}</h3>
                <div className="flex items-center gap-3">
                    <select
                        value={statusFilter}
                        onChange={(e) => setStatusFilter(e.target.value)}
                        className="rounded border border-border bg-background text-foreground text-sm px-3 py-1.5"
                    >
                        <option value="">{translate('All Statuses')}</option>
                        <option value="pending">{translate('Pending')}</option>
                        <option value="earned">{translate('Earned')}</option>
                        <option value="paid">{translate('Paid')}</option>
                        <option value="cancelled">{translate('Cancelled')}</option>
                    </select>
                    {selectedIds.size > 0 && (
                        <button
                            onClick={handlePayBulk}
                            disabled={paying}
                            className="px-3 py-1.5 text-sm rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
                        >
                            {translate('Pay Selected')} ({selectedIds.size})
                        </button>
                    )}
                </div>
            </div>

            <div className="overflow-x-auto">
                <table className="w-full text-sm">
                    <thead className="bg-primary/10 uppercase text-left">
                        <tr>
                            <th className="px-4 py-3">
                                <input
                                    type="checkbox"
                                    onChange={() => toggleSelectAll(items)}
                                    checked={items.length > 0 && items.filter((c) => c.status === 'earned').every((c) => selectedIds.has(c.id))}
                                    className="rounded"
                                />
                            </th>
                            <th className="px-4 py-3">{translate('Order')}</th>
                            <th className="px-4 py-3">{translate('Order Amount')}</th>
                            <th className="px-4 py-3">{translate('Commission Rate')}</th>
                            <th className="px-4 py-3">{translate('Commission Amount')}</th>
                            <th className="px-4 py-3">{translate('Status')}</th>
                            <th className="px-4 py-3">{translate('Date')}</th>
                            <th className="px-4 py-3">{translate('Action')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr>
                                <td colSpan={8} className="px-4 py-8 text-center text-muted">
                                    {translate('Loading...')}
                                </td>
                            </tr>
                        ) : items.length === 0 ? (
                            <tr>
                                <td colSpan={8} className="px-4 py-8 text-center text-muted">
                                    {translate('No commissions found')}
                                </td>
                            </tr>
                        ) : (
                            items.map((commission) => (
                                <tr key={commission.id} className="border-b border-border hover:bg-background-hover">
                                    <td className="px-4 py-3">
                                        {commission.status === 'earned' && (
                                            <input
                                                type="checkbox"
                                                checked={selectedIds.has(commission.id)}
                                                onChange={() => toggleSelect(commission.id)}
                                                className="rounded"
                                            />
                                        )}
                                    </td>
                                    <td className="px-4 py-3">
                                        #{commission.order?.order_group?.order_code || commission.order_id}
                                    </td>
                                    <td className="px-4 py-3">{formatPrice(commission.order_amount)}</td>
                                    <td className="px-4 py-3">{formatCommissionLabel(commission)}</td>
                                    <td className="px-4 py-3 font-medium">{formatPrice(commission.commission_amount)}</td>
                                    <td className="px-4 py-3">
                                        <span className={`px-2 py-0.5 rounded text-xs font-medium ${statusStyles[commission.status] || ''}`}>
                                            {translate(commission.status.charAt(0).toUpperCase() + commission.status.slice(1))}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3">{formatDate(commission.created_at)}</td>
                                    <td className="px-4 py-3">
                                        {commission.status === 'earned' && (
                                            <button
                                                onClick={() => handlePaySingle(commission.id)}
                                                disabled={paying}
                                                className="px-2 py-1 text-xs rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50"
                                            >
                                                {translate('Mark Paid')}
                                            </button>
                                        )}
                                        {commission.status === 'paid' && commission.paid_at && (
                                            <span className="text-xs text-muted">{formatDate(commission.paid_at)}</span>
                                        )}
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {commissions?.meta && (
                <div className="px-4">
                    <Pagination
                        pagination={{
                            current_page: commissions.meta.current_page,
                            from: commissions.meta.from,
                            last_page: commissions.meta.last_page,
                            per_page: commissions.meta.per_page,
                            to: commissions.meta.to,
                            total: commissions.meta.total,
                        }}
                        resourceName={translate('commissions')}
                        onPageChange={({ selected }) => fetchCommissions(selected)}
                    />
                </div>
            )}
        </div>
    );
};

export default CommissionList;
