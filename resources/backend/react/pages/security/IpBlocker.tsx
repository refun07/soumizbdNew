import axios from 'axios';
import React, { useEffect, useState, useCallback, useRef } from 'react';

interface BlockedIp {
    id: number;
    ip_address: string;
    reason: string | null;
    blocked_until: string | null;
    is_active: boolean;
    created_by: number | null;
    created_at: string;
    updated_at: string;
    deleted_at: string | null;
    created_by_user?: {
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

interface IpBlockerProps {
    initialData?: {
        data: BlockedIp[];
        meta: Pagination;
    } | null;
}

type BlockDuration = 'permanent' | '1hour' | '24hours' | '7days' | '30days';

const getDurationLabel = (duration: BlockDuration): string => {
    const labels: Record<BlockDuration, string> = {
        permanent: 'Permanent',
        '1hour': '1 Hour',
        '24hours': '24 Hours',
        '7days': '7 Days',
        '30days': '30 Days',
    };
    return labels[duration];
};

const getDurationDate = (duration: BlockDuration): string | null => {
    if (duration === 'permanent') return null;
    
    const now = new Date();
    switch (duration) {
        case '1hour':
            now.setHours(now.getHours() + 1);
            break;
        case '24hours':
            now.setDate(now.getDate() + 1);
            break;
        case '7days':
            now.setDate(now.getDate() + 7);
            break;
        case '30days':
            now.setDate(now.getDate() + 30);
            break;
    }
    return now.toISOString().slice(0, 16);
};

const formatDate = (dateString: string | null): string => {
    if (!dateString) return 'Permanent';
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

const formatBlockDate = (dateString: string): string => {
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

const isExpired = (blockedUntil: string | null): boolean => {
    if (!blockedUntil) return false;
    return new Date(blockedUntil) < new Date();
};

const IpBlocker: React.FC<IpBlockerProps> = ({ initialData }) => {
    const [blockedIps, setBlockedIps] = useState<BlockedIp[]>(initialData?.data || []);
    const [pagination, setPagination] = useState<Pagination>(
        initialData?.meta || { current_page: 1, last_page: 1, total: 0, per_page: 15 }
    );
    const [searchTerm, setSearchTerm] = useState('');
    const [isLoading, setIsLoading] = useState(false);
    const [showAddModal, setShowAddModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [selectedIp, setSelectedIp] = useState<BlockedIp | null>(null);
    const [isSubmitting, setIsSubmitting] = useState(false);

    // Form states
    const [formData, setFormData] = useState({
        ip_address: '',
        reason: '',
        duration: 'permanent' as BlockDuration,
        blocked_until: null as string | null,
    });

    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const fetchBlockedIps = async (page = 1, search?: string) => {
        setIsLoading(true);
        try {
            const response = await axios.get('/admin/api/blocked-ips', {
                params: {
                    search: search !== undefined ? search : searchTerm,
                    page,
                    limit: pagination.per_page,
                },
            });

            if (response.data.success) {
                setBlockedIps(response.data.blocked_ips.data);
                setPagination({
                    current_page: response.data.blocked_ips.current_page,
                    last_page: response.data.blocked_ips.last_page,
                    total: response.data.blocked_ips.total,
                    per_page: response.data.blocked_ips.per_page,
                });
            }
        } catch (error) {
            console.error('Error fetching blocked IPs:', error);
        } finally {
            setIsLoading(false);
        }
    };

    useEffect(() => {
        if (debounceRef.current) {
            clearTimeout(debounceRef.current);
        }

        if (searchTerm !== '') {
            debounceRef.current = setTimeout(() => {
                fetchBlockedIps();
            }, 500);
        } else {
            fetchBlockedIps();
        }
    }, [searchTerm]);

    const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setSearchTerm(e.target.value);
    };

    const handlePageChange = (page: number) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchBlockedIps(page);
        }
    };

    const handleOpenAddModal = () => {
        setFormData({
            ip_address: '',
            reason: '',
            duration: 'permanent',
            blocked_until: null,
        });
        setShowAddModal(true);
    };

    const handleCloseAddModal = () => {
        setShowAddModal(false);
    };

    const handleOpenEditModal = (ip: BlockedIp) => {
        setSelectedIp(ip);
        setFormData({
            ip_address: ip.ip_address,
            reason: ip.reason || '',
            duration: ip.blocked_until ? 'custom' as BlockDuration : 'permanent',
            blocked_until: ip.blocked_until,
        });
        setShowEditModal(true);
    };

    const handleCloseEditModal = () => {
        setShowEditModal(false);
        setSelectedIp(null);
    };

    const handleDurationChange = (duration: BlockDuration) => {
        setFormData(prev => ({
            ...prev,
            duration,
            blocked_until: getDurationDate(duration),
        }));
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setIsSubmitting(true);

        try {
            const response = await axios.post('/admin/api/blocked-ips', {
                ip_address: formData.ip_address,
                reason: formData.reason,
                blocked_until: formData.blocked_until,
            });

            if (response.data.success) {
                setShowAddModal(false);
                fetchBlockedIps(pagination.current_page);
                alert('IP address has been blocked successfully');
            }
        } catch (error: any) {
            const message = error.response?.data?.message || 'Failed to block IP address';
            alert(message);
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleUpdate = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!selectedIp) return;

        setIsSubmitting(true);

        try {
            const response = await axios.put(`/admin/api/blocked-ips/${selectedIp.id}`, {
                reason: formData.reason,
                blocked_until: formData.blocked_until,
            });

            if (response.data.success) {
                setShowEditModal(false);
                setSelectedIp(null);
                fetchBlockedIps(pagination.current_page);
                alert('IP block has been updated successfully');
            }
        } catch (error: any) {
            const message = error.response?.data?.message || 'Failed to update IP block';
            alert(message);
        } finally {
            setIsSubmitting(false);
        }
    };

    const handleToggleStatus = async (ip: BlockedIp) => {
        try {
            const response = await axios.post(`/admin/api/blocked-ips/${ip.id}/toggle-status`);

            if (response.data.success) {
                fetchBlockedIps(pagination.current_page);
                const newStatus = response.data.blocked_ip.is_active ? 'activated' : 'deactivated';
                alert(`IP block has been ${newStatus} successfully`);
            }
        } catch (error: any) {
            const message = error.response?.data?.message || 'Failed to toggle status';
            alert(message);
        }
    };

    const handleDelete = async (ip: BlockedIp) => {
        if (!confirm('Are you sure you want to unblock this IP address?')) {
            return;
        }

        try {
            const response = await axios.delete(`/admin/api/blocked-ips/${ip.id}`);

            if (response.data.success) {
                fetchBlockedIps(pagination.current_page);
                alert('IP address has been unblocked successfully');
            }
        } catch (error: any) {
            const message = error.response?.data?.message || 'Failed to unblock IP address';
            alert(message);
        }
    };

    return (
        <div className="p-4">
            <div className="flex items-center justify-between mb-4">
                <h1 className="text-xl font-bold text-foreground">IP Blocker</h1>
                <button
                    onClick={handleOpenAddModal}
                    className="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded flex items-center gap-2"
                >
                    <i className="fa-solid fa-plus"></i>
                    Block New IP
                </button>
            </div>

            <div className="card rounded-lg shadow-sm">
                {/* Filters */}
                <div className="p-4 border-b border-border">
                    <div className="flex flex-wrap items-center justify-between gap-4">
                        <div className="search-form relative w-72 flex-shrink-0">
                            <input
                                type="text"
                                value={searchTerm}
                                className="theme-input w-full"
                                placeholder="Search by IP address"
                                onChange={handleSearchChange}
                                autoComplete="off"
                            />
                            <span className="text-primary dark:text-muted absolute top-0 right-0 h-full flex items-center justify-center px-3 pointer-events-none">
                                <i className="fa-solid fa-magnifying-glass"></i>
                            </span>
                        </div>
                    </div>
                </div>

                {/* Loading State */}
                {isLoading && (
                    <div className="p-8 text-center text-muted">
                        <i className="fa-solid fa-circle-notch fa-spin text-2xl mb-2"></i>
                        <p>Loading blocked IPs...</p>
                    </div>
                )}

                {/* Mobile Card View */}
                {!isLoading && (
                    <div className="lg:hidden">
                        <div className="space-y-3 p-3">
                            {blockedIps.map((ip) => (
                                <div key={ip.id} className="card rounded-lg border border-border p-4">
                                    <div className="space-y-3">
                                        <div className="flex items-start justify-between">
                                            <div>
                                                <div className="text-xs text-muted uppercase mb-1">IP Address</div>
                                                <div className="font-medium font-mono">{ip.ip_address}</div>
                                            </div>
                                            {ip.is_active && !ip.deleted_at && !isExpired(ip.blocked_until) ? (
                                                <span className="px-2 py-1 text-xs font-medium text-white bg-red-500 rounded-full">
                                                    Blocked
                                                </span>
                                            ) : ip.deleted_at ? (
                                                <span className="px-2 py-1 text-xs font-medium text-white bg-gray-500 rounded-full">
                                                    Unblocked
                                                </span>
                                            ) : (
                                                <span className="px-2 py-1 text-xs font-medium text-white bg-yellow-500 rounded-full">
                                                    Inactive
                                                </span>
                                            )}
                                        </div>

                                        {ip.reason && (
                                            <div>
                                                <div className="text-xs text-muted uppercase mb-1">Reason</div>
                                                <div className="text-sm">{ip.reason}</div>
                                            </div>
                                        )}

                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <div className="text-xs text-muted uppercase mb-1">Blocked Until</div>
                                                <div className="text-sm">{formatDate(ip.blocked_until)}</div>
                                            </div>
                                            <div>
                                                <div className="text-xs text-muted uppercase mb-1">Blocked By</div>
                                                <div className="text-sm">{ip.created_by_user?.name || 'System'}</div>
                                            </div>
                                        </div>

                                        <div className="flex items-center justify-end pt-2 border-t border-border gap-2">
                                            <button
                                                onClick={() => handleOpenEditModal(ip)}
                                                className="text-primary hover:text-primary/80 p-2"
                                                title="Edit"
                                            >
                                                <i className="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button
                                                onClick={() => handleToggleStatus(ip)}
                                                className={`p-2 ${ip.is_active ? 'text-yellow-600 hover:text-yellow-700' : 'text-green-600 hover:text-green-700'}`}
                                                title={ip.is_active ? 'Deactivate' : 'Activate'}
                                            >
                                                <i className={`fa-solid ${ip.is_active ? 'fa-ban' : 'fa-check'}`}></i>
                                            </button>
                                            {!ip.deleted_at && (
                                                <button
                                                    onClick={() => handleDelete(ip)}
                                                    className="text-red-600 hover:text-red-700 p-2"
                                                    title="Unblock"
                                                >
                                                    <i className="fa-solid fa-trash"></i>
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            ))}
                            {blockedIps.length === 0 && (
                                <div className="text-center py-8 text-muted">
                                    No blocked IPs found.
                                </div>
                            )}
                        </div>
                    </div>
                )}

                {/* Desktop Table View */}
                {!isLoading && (
                    <div className="hidden lg:block overflow-x-auto">
                        <table className="divide-y divide-border product-list-table w-full">
                            <thead>
                                <tr className="theme-table__head">
                                    <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                        IP Address
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                        Reason
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                        Blocked Until
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                        Blocked By
                                    </th>
                                    <th className="px-6 py-4 text-left text-xs font-medium text-muted uppercase">
                                        Blocked At
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
                                {blockedIps.map((ip) => (
                                    <tr key={ip.id}>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="font-mono font-medium">{ip.ip_address}</span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <span className="text-sm">{ip.reason || '-'}</span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="text-sm">{formatDate(ip.blocked_until)}</span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="text-sm">{ip.created_by_user?.name || 'System'}</span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="text-sm text-muted">{formatBlockDate(ip.created_at)}</span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            {ip.deleted_at ? (
                                                <span className="px-2 py-1 text-xs font-medium text-white bg-gray-500 rounded-full">
                                                    Unblocked
                                                </span>
                                            ) : ip.is_active && !isExpired(ip.blocked_until) ? (
                                                <span className="px-2 py-1 text-xs font-medium text-white bg-red-500 rounded-full">
                                                    Blocked
                                                </span>
                                            ) : (
                                                <span className="px-2 py-1 text-xs font-medium text-white bg-yellow-500 rounded-full">
                                                    Inactive
                                                </span>
                                            )}
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <button
                                                onClick={() => handleOpenEditModal(ip)}
                                                className="text-primary hover:text-primary/80 p-2"
                                                title="Edit"
                                            >
                                                <i className="fa-solid fa-pen-to-square"></i>
                                            </button>
                                            <button
                                                onClick={() => handleToggleStatus(ip)}
                                                className={`p-2 ${ip.is_active ? 'text-yellow-600 hover:text-yellow-700' : 'text-green-600 hover:text-green-700'}`}
                                                title={ip.is_active ? 'Deactivate' : 'Activate'}
                                            >
                                                <i className={`fa-solid ${ip.is_active ? 'fa-ban' : 'fa-check'}`}></i>
                                            </button>
                                            {!ip.deleted_at && (
                                                <button
                                                    onClick={() => handleDelete(ip)}
                                                    className="text-red-600 hover:text-red-700 p-2"
                                                    title="Unblock"
                                                >
                                                    <i className="fa-solid fa-trash"></i>
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                                {blockedIps.length === 0 && (
                                    <tr>
                                        <td colSpan={7} className="text-center py-8 text-muted">
                                            No blocked IPs found.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                )}

                {/* Pagination */}
                {!isLoading && blockedIps.length > 0 && (
                    <div className="p-4 border-t border-border">
                        <div className="flex justify-between items-center">
                            <p className="text-sm text-muted">
                                Showing page {pagination.current_page} of {pagination.last_page} ({pagination.total} IPs)
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
                )}
            </div>

            {/* Add Block Modal */}
            {showAddModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <div className="flex items-center justify-between mb-4">
                                <h2 className="text-xl font-bold text-foreground">Block IP Address</h2>
                                <button
                                    onClick={handleCloseAddModal}
                                    className="text-muted hover:text-foreground text-xl"
                                >
                                    <i className="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <form onSubmit={handleSubmit}>
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            IP Address *
                                        </label>
                                        <input
                                            type="text"
                                            value={formData.ip_address}
                                            onChange={(e) => setFormData(prev => ({ ...prev, ip_address: e.target.value }))}
                                            className="theme-input w-full"
                                            placeholder="192.168.1.1"
                                            required
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Reason
                                        </label>
                                        <textarea
                                            value={formData.reason}
                                            onChange={(e) => setFormData(prev => ({ ...prev, reason: e.target.value }))}
                                            className="theme-input w-full"
                                            rows={3}
                                            placeholder="Reason for blocking this IP"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Block Duration
                                        </label>
                                        <div className="grid grid-cols-2 gap-2">
                                            {(['permanent', '1hour', '24hours', '7days', '30days'] as BlockDuration[]).map((duration) => (
                                                <button
                                                    key={duration}
                                                    type="button"
                                                    onClick={() => handleDurationChange(duration)}
                                                    className={`px-3 py-2 rounded text-sm ${
                                                        formData.duration === duration
                                                            ? 'bg-indigo-600 text-white'
                                                            : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                                                    }`}
                                                >
                                                    {getDurationLabel(duration)}
                                                </button>
                                            ))}
                                        </div>
                                    </div>
                                </div>

                                <div className="mt-6 flex justify-end gap-3">
                                    <button
                                        type="button"
                                        onClick={handleCloseAddModal}
                                        className="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting}
                                        className={`px-4 py-2 rounded ${
                                            isSubmitting
                                                ? 'bg-indigo-400 cursor-not-allowed'
                                                : 'bg-indigo-600 hover:bg-indigo-700'
                                        } text-white`}
                                    >
                                        {isSubmitting ? (
                                            <>
                                                <i className="fa-solid fa-circle-notch fa-spin mr-2"></i>
                                                Blocking...
                                            </>
                                        ) : (
                                            'Block IP'
                                        )}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}

            {/* Edit Block Modal */}
            {showEditModal && selectedIp && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                        <div className="p-6">
                            <div className="flex items-center justify-between mb-4">
                                <h2 className="text-xl font-bold text-foreground">Edit IP Block</h2>
                                <button
                                    onClick={handleCloseEditModal}
                                    className="text-muted hover:text-foreground text-xl"
                                >
                                    <i className="fa-solid fa-xmark"></i>
                                </button>
                            </div>

                            <form onSubmit={handleUpdate}>
                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            IP Address
                                        </label>
                                        <input
                                            type="text"
                                            value={formData.ip_address}
                                            disabled
                                            className="theme-input w-full bg-gray-100"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Reason
                                        </label>
                                        <textarea
                                            value={formData.reason}
                                            onChange={(e) => setFormData(prev => ({ ...prev, reason: e.target.value }))}
                                            className="theme-input w-full"
                                            rows={3}
                                            placeholder="Reason for blocking this IP"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Blocked Until
                                        </label>
                                        <input
                                            type="datetime-local"
                                            value={formData.blocked_until || ''}
                                            onChange={(e) => setFormData(prev => ({ ...prev, blocked_until: e.target.value || null }))}
                                            className="theme-input w-full"
                                        />
                                        <p className="text-xs text-muted mt-1">
                                            Leave empty for permanent block
                                        </p>
                                    </div>
                                </div>

                                <div className="mt-6 flex justify-end gap-3">
                                    <button
                                        type="button"
                                        onClick={handleCloseEditModal}
                                        className="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="submit"
                                        disabled={isSubmitting}
                                        className={`px-4 py-2 rounded ${
                                            isSubmitting
                                                ? 'bg-indigo-400 cursor-not-allowed'
                                                : 'bg-indigo-600 hover:bg-indigo-700'
                                        } text-white`}
                                    >
                                        {isSubmitting ? (
                                            <>
                                                <i className="fa-solid fa-circle-notch fa-spin mr-2"></i>
                                                Updating...
                                            </>
                                        ) : (
                                            'Update'
                                        )}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default IpBlocker;