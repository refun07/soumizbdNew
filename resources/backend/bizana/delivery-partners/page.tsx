import React, { useEffect, useMemo, useState } from 'react';
import { createRoot } from 'react-dom/client';
import '../../css/app.css';
import DateRangePicker from '../../js/bizana/components/DateRangePicker';

interface PartnerRow {
    partner: 'pathao' | 'steadfast' | 'redx' | 'others';
    totalOrders: number;
    deliveredOrders: number;
    cancelledOrders: number;
    deliveryCharge: number;
}

interface DeliveryPartnersResponse {
    dateRange: {
        from: string;
        to: string;
    };
    credentials: {
        pathao: { configured: boolean };
        steadfast: { configured: boolean };
        redx: { configured: boolean };
    };
    partners: PartnerRow[];
    totals: {
        totalOrders: number;
        deliveredOrders: number;
        cancelledOrders: number;
        deliveryCharge: number;
    };
}

function DeliveryPartnersPage() {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [partnerFilter, setPartnerFilter] = useState<
        'all' | 'pathao' | 'steadfast' | 'redx' | 'others'
    >('all');
    const [dateRange, setDateRange] = useState({
        from: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)
            .toISOString()
            .split('T')[0],
        to: new Date().toISOString().split('T')[0],
    });
    const [data, setData] = useState<DeliveryPartnersResponse>({
        dateRange: dateRange,
        credentials: {
            pathao: { configured: false },
            steadfast: { configured: false },
            redx: { configured: false },
        },
        partners: [],
        totals: {
            totalOrders: 0,
            deliveredOrders: 0,
            cancelledOrders: 0,
            deliveryCharge: 0,
        },
    });

    const getApiPrefix = () => {
        return (window as any).bizanaConfig?.apiPrefix || '/admin/api/bizana';
    };

    useEffect(() => {
        const fetchData = async () => {
            setLoading(true);
            setError('');

            try {
                const response = await fetch(
                    `${getApiPrefix()}/dashboard/delivery-partners?from=${dateRange.from}&to=${dateRange.to}`,
                );

                if (!response.ok) {
                    throw new Error('Failed to fetch delivery partner data');
                }

                const json = (await response.json()) as DeliveryPartnersResponse;
                setData(json);
            } catch (err) {
                setError(
                    err instanceof Error
                        ? err.message
                        : 'Unknown error while fetching data',
                );
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, [dateRange.from, dateRange.to]);

    const filteredRows = useMemo(() => {
        if (partnerFilter === 'all') return data.partners;
        return data.partners.filter((row) => row.partner === partnerFilter);
    }, [data.partners, partnerFilter]);

    const formatPartnerLabel = (partner: PartnerRow['partner']) => {
        if (partner === 'redx') return 'RedX';
        if (partner === 'pathao') return 'Pathao';
        if (partner === 'steadfast') return 'Steadfast';
        return 'Others';
    };

    return (
        <div className="pt-4 min-h-screen">
            <div className="card p-6 rounded-md">
                <div className="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 mb-6">
                    <div>
                        <h2 className="text-xl font-bold text-foreground mb-2">
                            Delivery Partners Overview
                        </h2>
                        <p className="text-sm text-muted">
                            Partner-wise delivery charge and order performance.
                        </p>
                    </div>
                    <div className="flex flex-col md:flex-row gap-3 md:items-center">
                        <DateRangePicker
                            startDate={dateRange.from}
                            endDate={dateRange.to}
                            onDateChange={(startDate, endDate) =>
                                setDateRange({ from: startDate, to: endDate })
                            }
                        />
                        <select
                            value={partnerFilter}
                            onChange={(e) =>
                                setPartnerFilter(
                                    e.target.value as
                                        | 'all'
                                        | 'pathao'
                                        | 'steadfast'
                                        | 'redx'
                                        | 'others',
                                )
                            }
                            className="h-10 card border border-border rounded px-3 text-sm text-foreground"
                        >
                            <option value="all">All Partners</option>
                            <option value="pathao">Pathao</option>
                            <option value="steadfast">Steadfast</option>
                            <option value="redx">RedX</option>
                            <option value="others">Others</option>
                        </select>
                    </div>
                </div>

                {error ? (
                    <div className="p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 mb-4">
                        {error}
                    </div>
                ) : null}

                <div className="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div className="card border border-border rounded-lg p-4">
                        <p className="text-xs text-muted font-semibold mb-1">
                            TOTAL DELIVERY CHARGE
                        </p>
                        <p className="text-xl font-bold text-foreground">
                            {data.totals.deliveryCharge.toLocaleString()} Tk
                        </p>
                    </div>
                    <div className="card border border-border rounded-lg p-4">
                        <p className="text-xs text-muted font-semibold mb-1">
                            TOTAL ORDERS
                        </p>
                        <p className="text-xl font-bold text-foreground">
                            {data.totals.totalOrders.toLocaleString()}
                        </p>
                    </div>
                    <div className="card border border-border rounded-lg p-4">
                        <p className="text-xs text-muted font-semibold mb-1">
                            DELIVERED
                        </p>
                        <p className="text-xl font-bold text-green-600">
                            {data.totals.deliveredOrders.toLocaleString()}
                        </p>
                    </div>
                    <div className="card border border-border rounded-lg p-4">
                        <p className="text-xs text-muted font-semibold mb-1">
                            CANCELLED
                        </p>
                        <p className="text-xl font-bold text-red-600">
                            {data.totals.cancelledOrders.toLocaleString()}
                        </p>
                    </div>
                </div>

                <div className="flex items-center justify-between mb-4">
                    <div>
                        <h3 className="text-base font-semibold text-foreground">
                            Partner Breakdown
                        </h3>
                        <p className="text-xs text-muted">
                            Credentials configured: Pathao (
                            {data.credentials.pathao.configured ? 'Yes' : 'No'}
                            ), Steadfast (
                            {data.credentials.steadfast.configured
                                ? 'Yes'
                                : 'No'}
                            ), RedX (
                            {data.credentials.redx.configured ? 'Yes' : 'No'})
                        </p>
                    </div>
                </div>

                <div className="overflow-x-auto border border-border rounded-lg">
                    <table className="w-full text-sm text-foreground">
                        <thead className="bg-muted/30">
                            <tr>
                                <th className="text-left px-4 py-3 font-semibold text-foreground">
                                    Partner
                                </th>
                                <th className="text-right px-4 py-3 font-semibold text-foreground">
                                    Orders
                                </th>
                                <th className="text-right px-4 py-3 font-semibold text-foreground">
                                    Delivered
                                </th>
                                <th className="text-right px-4 py-3 font-semibold text-foreground">
                                    Cancelled
                                </th>
                                <th className="text-right px-4 py-3 font-semibold text-foreground">
                                    Delivery Charge
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {loading ? (
                                <tr>
                                    <td
                                        colSpan={5}
                                        className="px-4 py-6 text-center text-muted"
                                    >
                                        Loading data...
                                    </td>
                                </tr>
                            ) : filteredRows.length === 0 ? (
                                <tr>
                                    <td
                                        colSpan={5}
                                        className="px-4 py-6 text-center text-muted"
                                    >
                                        No data found for selected filters.
                                    </td>
                                </tr>
                            ) : (
                                filteredRows.map((row) => (
                                    <tr
                                        key={row.partner}
                                        className="border-t border-border"
                                    >
                                        <td className="px-4 py-3 text-foreground font-medium">
                                            {formatPartnerLabel(row.partner)}
                                        </td>
                                        <td className="px-4 py-3 text-right text-foreground">
                                            {row.totalOrders.toLocaleString()}
                                        </td>
                                        <td className="px-4 py-3 text-right text-green-600">
                                            {row.deliveredOrders.toLocaleString()}
                                        </td>
                                        <td className="px-4 py-3 text-right text-red-600">
                                            {row.cancelledOrders.toLocaleString()}
                                        </td>
                                        <td className="px-4 py-3 text-right text-foreground font-semibold">
                                            {row.deliveryCharge.toLocaleString()}{' '}
                                            Tk
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                <p className="text-xs text-muted mt-4">
                    Date range: {data.dateRange.from} to {data.dateRange.to}
                </p>
            </div>
        </div>
    );
}

const container = document.getElementById('bizana-app');
if (container) {
    const root = createRoot(container);
    root.render(<DeliveryPartnersPage />);
} else {
    console.error('Bizana admin app container not found');
}
