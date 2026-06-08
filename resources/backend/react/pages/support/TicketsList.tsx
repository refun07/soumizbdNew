import axios from 'axios';
import React, { useEffect, useState, useCallback, useRef } from 'react';
import { useNavigate } from 'react-router-dom';

interface Ticket {
    id: number;
    public_id: string;
    user_id: number;
    order_id: number;
    phone: string;
    subject: string;
    status: 'open' | 'in_progress' | 'resolved' | 'closed';
    priority: 'low' | 'medium' | 'high';
    assigned_to: number | null;
    created_at: string;
    user: {
        id: number;
        name: string;
        email: string;
    };
    order: {
        id: number;
        order_code: string;
    };
    assigned_admin?: {
        id: number;
        name: string;
    } | null;
}

interface Pagination {
    current_page: number;
    last_page: number;
    total: number;
    per_page: number;
}

interface Admin {
    id: number;
    name: string;
    email: string;
}

const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
    });
};

const getStatusBadgeClass = (status: string): string => {
    const classes: Record<string, string> = {
        open: 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-700',
        in_progress: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700',
        resolved: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border-green-200 dark:border-green-700',
        closed: 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-600',
    };
    return classes[status] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-600';
};

const getPriorityBadgeClass = (priority: string): string => {
    const classes: Record<string, string> = {
        low: 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border-green-200 dark:border-green-700',
        medium: 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700',
        high: 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border-red-200 dark:border-red-700',
    };
    return classes[priority] || 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300 border-gray-200 dark:border-gray-600';
};

const getStatusLabel = (status: string): string => {
    const labels: Record<string, string> = {
        open: 'Open',
        in_progress: 'In Progress',
        resolved: 'Resolved',
        closed: 'Closed',
    };
    return labels[status] || status;
};

const getPriorityLabel = (priority: string): string => {
    const labels: Record<string, string> = {
        low: 'Low',
        medium: 'Medium',
        high: 'High',
    };
    return labels[priority] || priority;
};

const TicketsList: React.FC = () => {
    const navigate = useNavigate();
    const [tickets, setTickets] = useState<Ticket[]>([]);
    const [pagination, setPagination] = useState<Pagination>({
        current_page: 1,
        last_page: 1,
        total: 0,
        per_page: 15,
    });
    const [isLoading, setIsLoading] = useState(false);
    const [searchTerm, setSearchTerm] = useState('');
    const [statusFilter, setStatusFilter] = useState('');
    const [priorityFilter, setPriorityFilter] = useState('');
    const [assignedFilter, setAssignedFilter] = useState('');
    const [admins, setAdmins] = useState<Admin[]>([]);
    const [dateFrom, setDateFrom] = useState('');
    const [dateTo, setDateTo] = useState('');
    const [copySuccess, setCopySuccess] = useState(false);

    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const customerTicketUrl = (window as any).config?.customerTicketUrl || '';

    const handleCopyUrl = async () => {
        if (!customerTicketUrl) return;
        try {
            await navigator.clipboard.writeText(customerTicketUrl);
            setCopySuccess(true);
            setTimeout(() => setCopySuccess(false), 2000);
        } catch (err) {
            console.error('Failed to copy URL:', err);
        }
    };

    const fetchTickets = useCallback(async (page = 1) => {
        setIsLoading(true);
        try {
            const params: Record<string, any> = {
                page,
                limit: pagination.per_page,
            };

            if (searchTerm) params.search = searchTerm;
            if (statusFilter) params.status = statusFilter;
            if (priorityFilter) params.priority = priorityFilter;
            if (assignedFilter) params.assigned_to = assignedFilter;
            if (dateFrom) params.date_from = dateFrom;
            if (dateTo) params.date_to = dateTo;

            const response = await axios.get('/admin/api/support/tickets', { params });
            
            if (response.data.success) {
                setTickets(response.data.data.data);
                setPagination({
                    current_page: response.data.data.current_page,
                    last_page: response.data.data.last_page,
                    total: response.data.data.total,
                    per_page: response.data.data.per_page,
                });
            }
        } catch (error) {
            console.error('Error fetching tickets:', error);
        } finally {
            setIsLoading(false);
        }
    }, [searchTerm, statusFilter, priorityFilter, assignedFilter, dateFrom, dateTo, pagination.per_page]);

    const fetchAdmins = useCallback(async () => {
        try {
            const response = await axios.get('/admin/api/staffs', { params: { role: 'admin' } });
            if (response.data.success) {
                setAdmins(response.data.data.data || []);
            }
        } catch (error) {
            console.error('Error fetching admins:', error);
        }
    }, []);

    useEffect(() => {
        fetchAdmins();
    }, [fetchAdmins]);

    useEffect(() => {
        if (debounceRef.current) {
            clearTimeout(debounceRef.current);
        }

        debounceRef.current = setTimeout(() => {
            fetchTickets(1);
        }, 500);

        return () => {
            if (debounceRef.current) {
                clearTimeout(debounceRef.current);
            }
        };
    }, [fetchTickets]);

    const handlePageChange = (page: number) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchTickets(page);
        }
    };

    const handleClearFilters = () => {
        setSearchTerm('');
        setStatusFilter('');
        setPriorityFilter('');
        setAssignedFilter('');
        setDateFrom('');
        setDateTo('');
    };

    const hasActiveFilters = searchTerm || statusFilter || priorityFilter || assignedFilter || dateFrom || dateTo;

    return (
        <div className="p-4">
            <div className="mb-6">
                <h1 className="text-2xl font-bold mb-2 dark:text-gray-100">Support Tickets</h1>
                <p className="text-gray-600 dark:text-gray-300">Manage and respond to customer support tickets</p>
            </div>

            {/* Copy Customer Ticket URL */}
            <div className="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4 mb-6">
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 className="font-medium text-blue-900 dark:text-blue-300">Customer Ticket Form URL</h3>
                        <p className="text-sm text-blue-700 dark:text-blue-400 mt-1">Share this link with customers to let them submit support tickets</p>
                    </div>
                    <div className="flex items-center gap-2">
                        <input
                            type="text"
                            readOnly
                            value={customerTicketUrl}
                            className="hidden sm:block flex-1 px-3 py-2 bg-white dark:bg-gray-800 border border-blue-300 dark:border-blue-600 rounded-lg text-sm text-blue-800 dark:text-blue-300 min-w-[300px]"
                        />
                        <button
                            onClick={handleCopyUrl}
                            className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium flex items-center gap-2 whitespace-nowrap"
                        >
                            {copySuccess ? (
                                <>
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                    </svg>
                                    Copied!
                                </>
                            ) : (
                                <>
                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Copy URL
                                </>
                            )}
                        </button>
                    </div>
                </div>
            </div>

            {/* Filters */}
            <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                        <input
                            type="text"
                            value={searchTerm}
                            onChange={(e) => setSearchTerm(e.target.value)}
                            placeholder="Order, phone, subject..."
                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                        <select
                            value={statusFilter}
                            onChange={(e) => setStatusFilter(e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        >
                            <option value="">All Statuses</option>
                            <option value="open">Open</option>
                            <option value="in_progress">In Progress</option>
                            <option value="resolved">Resolved</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                        <select
                            value={priorityFilter}
                            onChange={(e) => setPriorityFilter(e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        >
                            <option value="">All Priorities</option>
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assigned To</label>
                        <select
                            value={assignedFilter}
                            onChange={(e) => setAssignedFilter(e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        >
                            <option value="">All Admins</option>
                            <option value="unassigned">Unassigned</option>
                            {admins.map((admin) => (
                                <option key={admin.id} value={admin.id}>
                                    {admin.name}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date From</label>
                        <input
                            type="date"
                            value={dateFrom}
                            onChange={(e) => setDateFrom(e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date To</label>
                        <input
                            type="date"
                            value={dateTo}
                            onChange={(e) => setDateTo(e.target.value)}
                            className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        />
                    </div>
                </div>

                {hasActiveFilters && (
                    <div className="mt-4 flex justify-end">
                        <button
                            onClick={handleClearFilters}
                            className="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                )}
            </div>

            {/* Tickets Table */}
            <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                {isLoading ? (
                    <div className="p-8 text-center">
                        <div className="animate-spin w-8 h-8 border-4 border-primary border-t-transparent rounded-full mx-auto mb-4"></div>
                        <p className="text-gray-600 dark:text-gray-300">Loading tickets...</p>
                    </div>
                ) : tickets.length === 0 ? (
                    <div className="p-8 text-center">
                        <p className="text-gray-600 dark:text-gray-300">No tickets found</p>
                        {hasActiveFilters && (
                            <button
                                onClick={handleClearFilters}
                                className="mt-2 text-primary hover:underline"
                            >
                                Clear filters to see all tickets
                            </button>
                        )}
                    </div>
                ) : (
                    <>
                        {/* Desktop Table */}
                        <div className="hidden lg:block overflow-x-auto">
                            <table className="w-full">
                                <thead className="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                                    <tr>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Ticket
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Customer
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Order
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Priority
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Assigned To
                                        </th>
                                        <th className="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Created
                                        </th>
                                        <th className="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-200 dark:divide-gray-700">
                                    {tickets.map((ticket) => (
                                        <tr key={ticket.id} className="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td className="px-4 py-3">
                                                <div>
                                                    <p className="font-medium text-gray-900 dark:text-gray-100">{ticket.subject}</p>
                                                    <p className="text-xs text-gray-500 dark:text-gray-400">{ticket.public_id.substring(0, 8)}...</p>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <div>
                                                    <p className="font-medium text-gray-900 dark:text-gray-100">{ticket.user?.name}</p>
                                                    <p className="text-xs text-gray-500 dark:text-gray-400">{ticket.phone}</p>
                                                </div>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="text-sm text-gray-700 dark:text-gray-300">{ticket.order?.order_code}</span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className={`px-2 py-1 text-xs font-medium rounded-full border ${getStatusBadgeClass(ticket.status)}`}>
                                                    {getStatusLabel(ticket.status)}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className={`px-2 py-1 text-xs font-medium rounded-full border ${getPriorityBadgeClass(ticket.priority)}`}>
                                                    {getPriorityLabel(ticket.priority)}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="text-sm text-gray-700 dark:text-gray-300">
                                                    {ticket.assigned_admin?.name || 'Unassigned'}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3">
                                                <span className="text-sm text-gray-500 dark:text-gray-400">
                                                    {formatDate(ticket.created_at)}
                                                </span>
                                            </td>
                                            <td className="px-4 py-3 text-right">
                                                <button
                                                    onClick={() => navigate(`/admin/support/tickets/${ticket.id}`)}
                                                    className="text-primary hover:text-primary/80 font-medium text-sm"
                                                >
                                                    View
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Mobile Cards */}
                        <div className="lg:hidden">
                            {tickets.map((ticket) => (
                                <div key={ticket.id} className="p-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <div className="flex justify-between items-start mb-2">
                                        <div>
                                            <p className="font-medium text-gray-900 dark:text-gray-100">{ticket.subject}</p>
                                            <p className="text-xs text-gray-500 dark:text-gray-400">{ticket.user?.name} • {ticket.phone}</p>
                                        </div>
                                        <span className={`px-2 py-1 text-xs font-medium rounded-full border ${getStatusBadgeClass(ticket.status)}`}>
                                            {getStatusLabel(ticket.status)}
                                        </span>
                                    </div>
                                    <div className="grid grid-cols-2 gap-2 text-sm mb-3">
                                        <div>
                                            <span className="text-gray-500 dark:text-gray-400">Order:</span>{' '}
                                            <span className="dark:text-gray-300">{ticket.order?.order_code}</span>
                                        </div>
                                        <div>
                                            <span className="text-gray-500 dark:text-gray-400">Priority:</span>{' '}
                                            <span className={`${ticket.priority === 'high' ? 'text-red-600 dark:text-red-400' : ticket.priority === 'medium' ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400'}`}>
                                                {getPriorityLabel(ticket.priority)}
                                            </span>
                                        </div>
                                        <div>
                                            <span className="text-gray-500 dark:text-gray-400">Assigned:</span>{' '}
                                            <span className="dark:text-gray-300">{ticket.assigned_admin?.name || 'Unassigned'}</span>
                                        </div>
                                        <div>
                                            <span className="text-gray-500 dark:text-gray-400">Created:</span>{' '}
                                            <span className="dark:text-gray-300">{formatDate(ticket.created_at)}</span>
                                        </div>
                                    </div>
                                    <button
                                        onClick={() => navigate(`/admin/support/tickets/${ticket.id}`)}
                                        className="w-full py-2 text-center text-primary hover:bg-primary/5 rounded-lg transition-colors"
                                    >
                                        View Ticket
                                    </button>
                                </div>
                            ))}
                        </div>

                        {/* Pagination */}
                        {pagination.last_page > 1 && (
                            <div className="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                                <div className="text-sm text-gray-700 dark:text-gray-300">
                                    Showing {(pagination.current_page - 1) * pagination.per_page + 1} to{' '}
                                    {Math.min(pagination.current_page * pagination.per_page, pagination.total)} of{' '}
                                    {pagination.total} tickets
                                </div>
                                <div className="flex gap-2">
                                    <button
                                        onClick={() => handlePageChange(pagination.current_page - 1)}
                                        disabled={pagination.current_page === 1}
                                        className="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                    >
                                        Previous
                                    </button>
                                    <button
                                        onClick={() => handlePageChange(pagination.current_page + 1)}
                                        disabled={pagination.current_page === pagination.last_page}
                                        className="px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-lg disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-50 dark:hover:bg-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                    >
                                        Next
                                    </button>
                                </div>
                            </div>
                        )}
                    </>
                )}
            </div>
        </div>
    );
};

export default TicketsList;