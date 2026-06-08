import axios from 'axios';
import React, { useEffect, useState, useCallback, useRef } from 'react';
import { useParams } from 'react-router-dom';
import { format } from 'date-fns';
import { FaPaperclip, FaPlay, FaPause, FaPaperPlane, FaTrash } from 'react-icons/fa';
import { useFormik } from 'formik';
import { object, string } from 'yup';

interface Ticket {
    id: number;
    public_id: string;
    user_id: number;
    order_id: number;
    phone: string;
    subject: string;
    description: string;
    audio_path: string | null;
    audio_url: string | null;
    status: 'open' | 'in_progress' | 'resolved' | 'closed';
    priority: 'low' | 'medium' | 'high';
    assigned_to: number | null;
    resolved_at: string | null;
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
    attachments: TicketAttachment[];
    replies: TicketReply[];
}

interface TicketAttachment {
    id: number;
    file_path: string;
    file_type: 'image' | 'video';
    original_name: string;
    file_size: number;
    full_url: string;
    formatted_size: string;
    is_image: boolean;
    is_video: boolean;
}

interface TicketReply {
    id: number;
    user_id: number;
    message: string;
    attachment_path: string | null;
    attachment_url: string | null;
    is_admin_reply: boolean;
    read_at: string | null;
    created_at: string;
    user: {
        id: number;
        name: string;
    };
}

interface Admin {
    id: number;
    name: string;
    email: string;
}

const formatDate = (dateString: string): string => {
    return format(new Date(dateString), 'PPP p');
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

const replyValidationSchema = object().shape({
    message: string().required('Message is required').min(5, 'Message must be at least 5 characters'),
});

const TicketDetail: React.FC = () => {
    const { id } = useParams<{ id: string }>();
    const [ticket, setTicket] = useState<Ticket | null>(null);
    const [admins, setAdmins] = useState<Admin[]>([]);
    const [isLoading, setIsLoading] = useState(true);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const repliesEndRef = useRef<HTMLDivElement>(null);

    const fetchTicket = useCallback(async () => {
        if (!id) return;
        
        try {
            const response = await axios.get(`/admin/api/support/tickets/${id}`);
            if (response.data.success) {
                setTicket(response.data.data.ticket);
                setAdmins(response.data.data.admins);
            }
        } catch (error) {
            console.error('Error fetching ticket:', error);
        } finally {
            setIsLoading(false);
        }
    }, [id]);

    useEffect(() => {
        fetchTicket();
    }, [fetchTicket]);

    // Scroll to bottom only after submitting a new reply (not on initial load)
    const scrollToBottom = useCallback(() => {
        if (repliesEndRef.current) {
            repliesEndRef.current.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    }, []);

    // Track initial load to prevent auto-scroll on page load
    const isInitialLoad = useRef(true);
    useEffect(() => {
        if (isInitialLoad.current) {
            isInitialLoad.current = false;
            return;
        }
        // Only scroll if replies were added after initial load
        if (ticket?.replies && ticket.replies.length > 0) {
            scrollToBottom();
        }
    }, [ticket?.replies?.length, scrollToBottom]);

    const formik = useFormik({
        initialValues: {
            message: '',
            attachment: null as File | null,
        },
        validationSchema: replyValidationSchema,
        onSubmit: async (values, { resetForm }) => {
            if (!id || !ticket || ticket.status === 'closed') return;

            setIsSubmitting(true);
            try {
                const formData = new FormData();
                formData.append('message', values.message);
                if (values.attachment) {
                    formData.append('attachment', values.attachment);
                }

                await axios.post(`/admin/api/support/tickets/${id}/reply`, formData);
                resetForm();
                fetchTicket();
            } catch (error) {
                console.error('Error sending reply:', error);
            } finally {
                setIsSubmitting(false);
            }
        },
    });

    const updateStatus = async (status: string) => {
        if (!id) return;
        
        try {
            await axios.put(`/admin/api/support/tickets/${id}/status`, { status });
            fetchTicket();
        } catch (error) {
            console.error('Error updating status:', error);
        }
    };

    const updatePriority = async (priority: string) => {
        if (!id) return;
        
        try {
            await axios.put(`/admin/api/support/tickets/${id}/priority`, { priority });
            fetchTicket();
        } catch (error) {
            console.error('Error updating priority:', error);
        }
    };

    const assignTicket = async (adminId: string) => {
        if (!id) return;
        
        try {
            await axios.put(`/admin/api/support/tickets/${id}/assign`, {
                assigned_to: adminId === 'unassigned' ? null : parseInt(adminId),
            });
            fetchTicket();
        } catch (error) {
            console.error('Error assigning ticket:', error);
        }
    };

    const deleteAttachment = async (attachmentId: number) => {
        if (!id || !confirm('Are you sure you want to delete this attachment?')) return;
        
        try {
            await axios.delete(`/admin/api/support/tickets/${id}/attachments/${attachmentId}`);
            fetchTicket();
        } catch (error) {
            console.error('Error deleting attachment:', error);
        }
    };

    if (isLoading) {
        return (
            <div className="p-4">
                <div className="animate-pulse space-y-4">
                    <div className="h-8 bg-gray-200 rounded w-1/3"></div>
                    <div className="h-64 bg-gray-200 rounded"></div>
                </div>
            </div>
        );
    }

    if (!ticket) {
        return (
            <div className="p-4">
                <div className="text-center py-12">
                    <h2 className="text-xl font-semibold mb-2 dark:text-gray-100">Ticket not found</h2>
                    <p className="text-gray-600 dark:text-gray-300">The ticket you are looking for does not exist.</p>
                </div>
            </div>
        );
    }

    return (
        <div className="p-4">
            {/* Header */}
            <div className="mb-6">
                <div className="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold mb-2 dark:text-gray-100">{ticket.subject}</h1>
                        <p className="text-gray-600 dark:text-gray-300">
                            Ticket ID: {ticket.public_id} • Order: {ticket.order?.order_code}
                        </p>
                    </div>
                    
                    <div className="flex gap-2">
                        <span className={`px-3 py-1 rounded-full text-sm font-medium border ${getStatusBadgeClass(ticket.status)}`}>
                            {getStatusLabel(ticket.status)}
                        </span>
                        <span className={`px-3 py-1 rounded-full text-sm font-medium border ${getPriorityBadgeClass(ticket.priority)}`}>
                            {getPriorityLabel(ticket.priority)}
                        </span>
                    </div>
                </div>
            </div>

            <div className="grid lg:grid-cols-3 gap-6">
                {/* Main Content */}
                <div className="lg:col-span-2 space-y-6">
                    {/* Description */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                        <h2 className="text-lg font-semibold mb-4 dark:text-gray-100">Description</h2>
                        <p className="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mb-4">{ticket.description}</p>

                        {/* Audio Recording */}
                        {ticket.audio_url && (
                            <div className="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <div className="flex items-center gap-3 mb-3">
                                    <span className="text-sm text-gray-600 dark:text-gray-300 font-medium">Voice message from customer</span>
                                </div>
                                <audio
                                    src={ticket.audio_url}
                                    controls
                                    className="w-full h-10 [&::-webkit-media-controls-panel]:bg-white dark:[&::-webkit-media-controls-panel]:bg-gray-800 [&::-webkit-media-controls-current-time-display]:text-gray-700 dark:[&::-webkit-media-controls-current-time-display]:text-gray-300 [&::-webkit-media-controls-time-remaining-display]:text-gray-700 dark:[&::-webkit-media-controls-time-remaining-display]:text-gray-300"
                                    preload="metadata"
                                >
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        )}

                        {/* Attachments */}
                        {ticket.attachments?.length > 0 && (
                            <div className="mt-4">
                                <h3 className="text-sm font-semibold mb-2 dark:text-gray-300">Attachments:</h3>
                                <div className="flex flex-wrap gap-3">
                                    {ticket.attachments.map((attachment) => (
                                        <div key={attachment.id} className="relative group">
                                            <a
                                                href={attachment.full_url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="flex items-center gap-2 p-2 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                            >
                                                {attachment.is_image ? (
                                                    <img
                                                        src={attachment.full_url}
                                                        alt={attachment.original_name}
                                                        className="w-16 h-16 object-cover rounded"
                                                    />
                                                ) : (
                                                    <div className="w-16 h-16 flex items-center justify-center bg-gray-200 dark:bg-gray-600 rounded">
                                                        <FaPaperclip className="w-6 h-6 text-gray-500 dark:text-gray-400" />
                                                    </div>
                                                )}
                                                <div className="text-xs">
                                                    <p className="font-medium truncate max-w-[120px] dark:text-gray-200">{attachment.original_name}</p>
                                                    <p className="text-gray-500 dark:text-gray-400">{attachment.formatted_size}</p>
                                                </div>
                                            </a>
                                            <button
                                                onClick={() => deleteAttachment(attachment.id)}
                                                className="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                                title="Delete attachment"
                                            >
                                                <FaTrash className="w-3 h-3" />
                                            </button>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Conversation */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                        <h2 className="text-lg font-semibold mb-4 dark:text-gray-100">Conversation</h2>

                        <div className="space-y-4 max-h-[500px] overflow-y-auto mb-6">
                            {ticket.replies?.length === 0 ? (
                                <p className="text-gray-500 dark:text-gray-400 text-center py-4">No replies yet.</p>
                            ) : (
                                ticket.replies?.map((reply) => (
                                    <div
                                        key={reply.id}
                                        className={`flex ${reply.is_admin_reply ? 'justify-start' : 'justify-end'}`}
                                    >
                                        <div
                                            className={`max-w-[80%] p-4 rounded-lg ${
                                                reply.is_admin_reply
                                                    ? 'bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-700'
                                                    : 'bg-gray-100 dark:bg-gray-700'
                                            }`}
                                        >
                                            <div className="flex items-center gap-2 mb-2">
                                                <span className="font-semibold text-sm dark:text-gray-100">
                                                    {reply.is_admin_reply ? 'Support Team' : ticket.user?.name}
                                                </span>
                                                <span className="text-xs text-gray-500 dark:text-gray-400">
                                                    {formatDate(reply.created_at)}
                                                </span>
                                                {reply.is_admin_reply && reply.read_at && (
                                                    <span className="text-xs text-green-600 dark:text-green-400">✓ Read</span>
                                                )}
                                            </div>
                                            
                                            <p className="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{reply.message}</p>

                                            {reply.attachment_path && (
                                                <a
                                                    href={reply.attachment_url || '#'}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="inline-flex items-center gap-2 mt-2 text-sm text-primary hover:underline"
                                                >
                                                    <FaPaperclip className="w-4 h-4" />
                                                    View attachment
                                                </a>
                                            )}
                                        </div>
                                    </div>
                                ))
                            )}
                            <div ref={repliesEndRef} />
                        </div>

                        {/* Reply Form */}
                        {ticket.status !== 'closed' && (
                            <form onSubmit={formik.handleSubmit} className="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <div className="mb-4">
                                    <textarea
                                        rows={4}
                                        placeholder="Type your reply..."
                                        className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100 dark:placeholder-gray-400 ${
                                            formik.errors.message && formik.touched.message
                                                ? 'border-red-500'
                                                : ''
                                        }`}
                                        {...formik.getFieldProps('message')}
                                    />
                                    {formik.errors.message && formik.touched.message && (
                                        <p className="text-red-500 text-sm mt-1">{formik.errors.message}</p>
                                    )}
                                </div>

                                <div className="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <label className="flex items-center gap-2 cursor-pointer text-sm text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-gray-100">
                                        <FaPaperclip className="w-5 h-5" />
                                        <span>
                                            {formik.values.attachment
                                                ? formik.values.attachment.name
                                                : 'Attach file (optional)'}
                                        </span>
                                        <input
                                            type="file"
                                            className="hidden"
                                            accept="image/*,video/*,.pdf,.doc,.docx"
                                            onChange={(e) => {
                                                const file = e.target.files?.[0];
                                                if (file) {
                                                    formik.setFieldValue('attachment', file);
                                                }
                                            }}
                                        />
                                    </label>

                                    <div className="flex gap-2">
                                        {formik.values.attachment && (
                                            <button
                                                type="button"
                                                onClick={() => formik.setFieldValue('attachment', null)}
                                                className="px-4 py-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                            >
                                                Remove
                                            </button>
                                        )}

                                        <button
                                            type="submit"
                                            disabled={isSubmitting || !formik.values.message.trim()}
                                            className="flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            <FaPaperPlane className="w-4 h-4" />
                                            {isSubmitting ? 'Sending...' : 'Send Reply'}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        )}

                        {ticket.status === 'closed' && (
                            <div className="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 text-center">
                                <p className="text-gray-600 dark:text-gray-300">This ticket has been closed.</p>
                            </div>
                        )}
                    </div>
                </div>

                {/* Sidebar */}
                <div className="space-y-6">
                    {/* Customer Info */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <h3 className="font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 dark:text-gray-100">Customer Information</h3>
                        
                        <div className="space-y-3 text-sm">
                            <div>
                                <span className="text-gray-500 dark:text-gray-400">Name:</span>
                                <p className="font-medium dark:text-gray-200">{ticket.user?.name}</p>
                            </div>
                            <div>
                                <span className="text-gray-500 dark:text-gray-400">Email:</span>
                                <p className="font-medium dark:text-gray-200">{ticket.user?.email}</p>
                            </div>
                            <div>
                                <span className="text-gray-500 dark:text-gray-400">Phone:</span>
                                <p className="font-medium dark:text-gray-200">{ticket.phone}</p>
                            </div>
                        </div>
                    </div>

                    {/* Order Info */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <h3 className="font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 dark:text-gray-100">Order Information</h3>
                        
                        <div className="space-y-3 text-sm">
                            <div>
                                <span className="text-gray-500 dark:text-gray-400">Order ID:</span>
                                <p className="font-medium dark:text-gray-200">{ticket.order?.order_code}</p>
                            </div>
                            <a
                                href={`/admin/orders/${ticket.order?.order_code}`}
                                target="_blank"
                                className="inline-block text-primary hover:underline text-sm"
                            >
                                View Order Details →
                            </a>
                        </div>
                    </div>

                    {/* Ticket Actions */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <h3 className="font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 dark:text-gray-100">Ticket Actions</h3>
                        
                        <div className="space-y-4">
                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select
                                    value={ticket.status}
                                    onChange={(e) => updateStatus(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                >
                                    <option value="open">Open</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Priority</label>
                                <select
                                    value={ticket.priority}
                                    onChange={(e) => updatePriority(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                >
                                    <option value="low">Low</option>
                                    <option value="medium">Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>

                            <div>
                                <label className="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assigned To</label>
                                <select
                                    value={ticket.assigned_to || 'unassigned'}
                                    onChange={(e) => assignTicket(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                                >
                                    <option value="unassigned">Unassigned</option>
                                    {admins.map((admin) => (
                                        <option key={admin.id} value={admin.id}>
                                            {admin.name}
                                        </option>
                                    ))}
                                </select>
                            </div>
                        </div>
                    </div>

                    {/* Ticket Timeline */}
                    <div className="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <h3 className="font-semibold mb-4 pb-2 border-b border-gray-200 dark:border-gray-700 dark:text-gray-100">Timeline</h3>
                        
                        <div className="space-y-3 text-sm">
                            <div>
                                <span className="text-gray-500 dark:text-gray-400">Created:</span>
                                <p className="font-medium dark:text-gray-200">{formatDate(ticket.created_at)}</p>
                            </div>
                            {ticket.resolved_at && (
                                <div>
                                    <span className="text-gray-500 dark:text-gray-400">Resolved:</span>
                                    <p className="font-medium dark:text-gray-200">{formatDate(ticket.resolved_at)}</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default TicketDetail;