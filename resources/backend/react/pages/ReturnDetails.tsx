import axios from 'axios';
import React, { useEffect, useState, useRef } from 'react';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';

// Import DateRangePicker from bizana
import DateRangePicker from '../../js/bizana/components/DateRangePicker';
import 'react-date-range/dist/styles.css'; // main css file
import 'react-date-range/dist/theme/default.css'; // theme css file

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
            return `${diffInMinutes} minute${diffInMinutes === 1 ? '' : 's'} ago`;
        } else if (diffInHoursRounded < 24) {
            return `${diffInHoursRounded} hour${diffInHoursRounded === 1 ? '' : 's'} ago`;
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

interface ReturnDetail {
    id: number;
    orderId: number;
    code: number;
    customerName?: string;
    customerPhone?: string;
    createdDate?: string;
    totalAmount: number;
    paymentStatus: string;
    address?: string;
    orderPrefix?: string;
    isPosOrder: boolean;
    returnReason?: string;
    returnStatus?: string;
}

interface Pagination {
    current_page: number;
    last_page: number;
    total: number;
}

const ReturnDetails: React.FC = () => {
    const [returnDetails, setReturnDetails] = useState<ReturnDetail[]>([]);
    const [filters, setFilters] = useState({
        from: '',
        to: '',
    });

    const [activeTab, setActiveTab] = useState('all');

    const [pagination, setPagination] = useState<Pagination>({
        current_page: 1,
        last_page: 1,
        total: 0,
    });

    // Tab configuration
    const tabs = [
        { id: 'all', label: 'All', filter: {} },
        { id: 'damaged', label: 'Damaged', filter: { type: 'damaged' } },
    ];

    const fetchReturnDetails = async (page = 1) => {
        try {
            // Get current tab filter
            const currentTab = tabs.find(t => t.id === activeTab);
            const tabFilter = currentTab?.filter || {};

            const response = await axios.get('/admin/api/return-details', {
                params: { ...filters, ...tabFilter, page },
            });

            const data = response.data.result.data;
            console.log(data);
            const paginationData = response.data.result.meta;
            setReturnDetails(data);
            setPagination({
                current_page: paginationData.current_page,
                last_page: paginationData.last_page,
                total: paginationData.total,
            });
        } catch (error) {
            console.error('Error fetching return details:', error);
        }
    };

    useEffect(() => {
        fetchReturnDetails();
    }, [filters, activeTab]);

    const handleDateRangeChange = (startDate: string, endDate: string) => {
        setFilters((prev) => ({ ...prev, from: startDate, to: endDate }));
    };

    const handleTabChange = (tabId: string) => {
        setActiveTab(tabId);
        // Fetch return details will be called by useEffect when activeTab changes
    };

    const handlePageChange = (page: number) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchReturnDetails(page);
        }
    };

    return (
        <div className="p-4">
            <h1 className="text-xl font-bold mb-4 text-foreground">Return Details</h1>

            <div className="card rounded-lg shadow-sm">
                {/* Top Actions */}
                <div className="p-4">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        {/* Date Range Picker */}
                        <div className="flex-shrink-0">
                            <DateRangePicker
                                startDate={filters.from}
                                endDate={filters.to}
                                onDateChange={handleDateRangeChange}
                            />
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
                    </div>
                </div>

                {/* Mobile Card View */}
                <div className="lg:hidden">
                    <div className="space-y-3 p-3">
                        {returnDetails.map((returnDetail, index) => (
                            <div key={returnDetail.id} className="card rounded-lg border border-border p-4">
                                <div className="space-y-3">
                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">Customer</div>
                                        <div className="font-medium">{returnDetail.customerName}</div>
                                        <div className="text-sm text-muted">{returnDetail.customerPhone}</div>
                                    </div>

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">Invoice No</div>
                                        <div className="font-medium">
                                            {returnDetail.orderPrefix} {returnDetail.code}
                                        </div>
                                    </div>

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">Date</div>
                                        <div className="text-sm">{formatOrderDate(returnDetail.createdDate || '')}</div>
                                    </div>

                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">Order Value</div>
                                        <div className="font-bold text-sky-500">{currencyFormatter(returnDetail.totalAmount)}</div>
                                        {returnDetail.paymentStatus === 'paid' ? (
                                            <div className="inline-flex items-center gap-1.5 text-green-600 text-sm">
                                                <i className="fa-solid fa-circle-check"></i>
                                                <span>Paid</span>
                                            </div>
                                        ) : (
                                            <div className="text-sm text-muted">Unpaid</div>
                                        )}
                                    </div>

                                    {returnDetail.returnReason && (
                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">Return Reason</div>
                                            <div className="text-sm">{returnDetail.returnReason}</div>
                                        </div>
                                    )}

                                    {returnDetail.returnStatus && (
                                        <div>
                                            <div className="text-xs text-muted uppercase mb-1">Return Status</div>
                                            <div className="text-sm capitalize">{returnDetail.returnStatus}</div>
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))}
                        {returnDetails.length === 0 && (
                            <div className="text-center py-8 text-muted">
                                No return details found.
                            </div>
                        )}
                    </div>
                </div>

                {/* Desktop Table View */}
                <div className="hidden lg:block overflow-x-auto">
                    <table className="divide-y divide-border product-list-table w-full">
                        <thead>
                            <tr className="theme-table__head">
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Customer
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Invoice No
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Date
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Value
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Payment Status
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Return Reason
                                </th>
                                <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                    Return Status
                                </th>
                            </tr>
                        </thead>
                        <tbody className="theme-table__body divide-y divide-border">
                            {returnDetails.map((returnDetail, index) => (
                                <tr key={returnDetail.id}>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="line-clamp-2">
                                            {returnDetail.customerName}
                                            <div className="line-clamp-2">
                                                {returnDetail.customerPhone}
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {returnDetail.orderPrefix} {returnDetail.code}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {formatOrderDate(returnDetail.createdDate || '')}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="line-clamp-2">
                                            <h6>
                                                {currencyFormatter(returnDetail.totalAmount)}
                                            </h6>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {returnDetail.paymentStatus === 'paid' ? (
                                            <div className="inline-flex items-center gap-1.5">
                                                <i className="fa-solid fa-circle-check text-green-500"></i>
                                                <span>Paid</span>
                                            </div>
                                        ) : (
                                            <div>Unpaid</div>
                                        )}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="line-clamp-2">
                                            {returnDetail.returnReason || '-'}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="line-clamp-2 capitalize">
                                            {returnDetail.returnStatus || '-'}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {returnDetails.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={7}
                                        className="text-center py-4 text-muted"
                                    >
                                        No return details found.
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
                            {pagination.last_page} ({pagination.total} returns)
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

export default ReturnDetails;
