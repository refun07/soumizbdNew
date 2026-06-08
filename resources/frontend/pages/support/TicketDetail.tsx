import { useState } from 'react';
import { useParams } from 'react-router-dom';
import { format } from 'date-fns';
import { FaPaperclip, FaPlay, FaPause, FaPaperPlane } from 'react-icons/fa';
import { useFormik } from 'formik';
import { object, string } from 'yup';
import toast from 'react-hot-toast';
import Button from '../../components/buttons/Button';
import { useGetTicketQuery, useGetTicketRepliesQuery, useAddReplyMutation, TicketReply } from '../../store/features/api/ticketApi';
import { translate } from '../../utils/translate';

const replyValidationSchema = object().shape({
    message: string().required(translate('Message is required')).min(5, translate('Message must be at least 5 characters')),
});

const TicketDetail = () => {
    const { publicId } = useParams<{ publicId: string }>();
    const [playingAudio, setPlayingAudio] = useState<HTMLAudioElement | null>(null);
    const [isPlaying, setIsPlaying] = useState(false);

    const { data: ticketData, isLoading: isLoadingTicket, refetch: refetchTicket } = useGetTicketQuery(publicId || '', {
        skip: !publicId,
    });

    const { data: repliesData, isLoading: isLoadingReplies, refetch: refetchReplies } = useGetTicketRepliesQuery(publicId || '', {
        skip: !publicId,
    });

    const [addReply, { isLoading: isSubmittingReply }] = useAddReplyMutation();

    const ticket = ticketData?.data?.ticket;
    const replies = repliesData?.data?.replies || [];

    const formik = useFormik({
        initialValues: {
            message: '',
            attachment: null as File | null,
        },
        validationSchema: replyValidationSchema,
        onSubmit: async (values, { resetForm }) => {
            if (!publicId) return;

            try {
                await addReply({
                    publicId,
                    data: {
                        message: values.message,
                        attachment: values.attachment || undefined,
                    },
                }).unwrap();

                resetForm();
                refetchReplies();
                refetchTicket();
                toast.success(translate('Reply sent successfully'));
            } catch (error: any) {
                const errorMessage = error?.data?.message || translate('Failed to send reply');
                toast.error(errorMessage);
            }
        },
    });

    const handleAudioPlay = (url: string) => {
        if (playingAudio) {
            playingAudio.pause();
            playingAudio.currentTime = 0;
            if (playingAudio.src === url) {
                setPlayingAudio(null);
                setIsPlaying(false);
                return;
            }
        }

        const audio = new Audio(url);
        audio.onended = () => {
            setIsPlaying(false);
            setPlayingAudio(null);
        };
        audio.play();
        setPlayingAudio(audio);
        setIsPlaying(true);
    };

    const getStatusBadgeClass = (status: string) => {
        const classes: Record<string, string> = {
            open: 'bg-blue-100 text-blue-800',
            in_progress: 'bg-yellow-100 text-yellow-800',
            resolved: 'bg-green-100 text-green-800',
            closed: 'bg-gray-100 text-gray-800',
        };
        return classes[status] || 'bg-gray-100 text-gray-800';
    };

    const getPriorityBadgeClass = (priority: string) => {
        const classes: Record<string, string> = {
            low: 'bg-green-100 text-green-800',
            medium: 'bg-yellow-100 text-yellow-800',
            high: 'bg-red-100 text-red-800',
        };
        return classes[priority] || 'bg-gray-100 text-gray-800';
    };

    const getStatusLabel = (status: string) => {
        const labels: Record<string, string> = {
            open: translate('Open'),
            in_progress: translate('In Progress'),
            resolved: translate('Resolved'),
            closed: translate('Closed'),
        };
        return labels[status] || status;
    };

    const getPriorityLabel = (priority: string) => {
        const labels: Record<string, string> = {
            low: translate('Low'),
            medium: translate('Medium'),
            high: translate('High'),
        };
        return labels[priority] || priority;
    };

    if (isLoadingTicket) {
        return (
            <div className="container max-w-4xl mx-auto p-4 sm:p-6">
                <div className="animate-pulse space-y-4">
                    <div className="h-32 bg-gray-200 rounded"></div>
                    <div className="h-64 bg-gray-200 rounded"></div>
                </div>
            </div>
        );
    }

    if (!ticket) {
        return (
            <div className="container max-w-4xl mx-auto p-4 sm:p-6">
                <div className="text-center py-12">
                    <h2 className="text-xl font-semibold mb-2">{translate('Ticket not found')}</h2>
                    <p className="text-gray-600">{translate('The ticket you are looking for does not exist or you do not have access to it.')}</p>
                </div>
            </div>
        );
    }

    return (
        <div className="container max-w-4xl mx-auto p-3 sm:p-6">
            {/* Ticket Header */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
                <div className="flex flex-wrap items-start justify-between gap-4 mb-4">
                    <div>
                        <h1 className="text-xl sm:text-2xl font-bold mb-2">{ticket.subject}</h1>
                        <p className="text-sm text-gray-500">
                            {translate('Ticket ID:')} {ticket.public_id.substring(0, 8)}...
                        </p>
                    </div>
                    
                    <div className="flex flex-wrap gap-2">
                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${getStatusBadgeClass(ticket.status)}`}>
                            {getStatusLabel(ticket.status)}
                        </span>
                        <span className={`px-3 py-1 rounded-full text-sm font-medium ${getPriorityBadgeClass(ticket.priority)}`}>
                            {getPriorityLabel(ticket.priority)}
                        </span>
                    </div>
                </div>

                <div className="grid sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span className="text-gray-500">{translate('Order ID:')}</span>
                        <p className="font-medium">{ticket.order?.order_code}</p>
                    </div>
                    <div>
                        <span className="text-gray-500">{translate('Created:')}</span>
                        <p className="font-medium">{format(new Date(ticket.created_at), 'PPP p')}</p>
                    </div>
                    <div>
                        <span className="text-gray-500">{translate('Phone:')}</span>
                        <p className="font-medium">{ticket.phone}</p>
                    </div>
                    {ticket.assigned_admin && (
                        <div>
                            <span className="text-gray-500">{translate('Assigned to:')}</span>
                            <p className="font-medium">{ticket.assigned_admin.name}</p>
                        </div>
                    )}
                </div>
            </div>

            {/* Ticket Description */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
                <h2 className="text-lg font-semibold mb-4">{translate('Description')}</h2>
                <p className="text-gray-700 whitespace-pre-wrap">{ticket.description}</p>

                {/* Audio Recording */}
                {ticket.audio_url && (
                    <div className="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div className="flex items-center gap-3">
                            <button
                                onClick={() => handleAudioPlay(ticket.audio_url!)}
                                className="w-10 h-10 flex items-center justify-center rounded-full bg-primary text-white hover:bg-primary/90"
                            >
                                {playingAudio?.src === ticket.audio_url && isPlaying ? (
                                    <FaPause className="w-4 h-4" />
                                ) : (
                                    <FaPlay className="w-4 h-4 ml-0.5" />
                                )}
                            </button>
                            <span className="text-sm text-gray-600">{translate('Voice message')}</span>
                        </div>
                    </div>
                )}

                {/* Attachments */}
                {ticket.attachments && ticket.attachments.length > 0 && (
                    <div className="mt-4">
                        <h3 className="text-sm font-semibold mb-2">{translate('Attachments:')}</h3>
                        <div className="flex flex-wrap gap-3">
                            {ticket.attachments.map((attachment) => (
                                <a
                                    key={attachment.id}
                                    href={attachment.full_url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="flex items-center gap-2 p-2 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                                >
                                    {attachment.is_image ? (
                                        <img
                                            src={attachment.full_url}
                                            alt={attachment.original_name}
                                            className="w-16 h-16 object-cover rounded"
                                        />
                                    ) : (
                                        <div className="w-16 h-16 flex items-center justify-center bg-gray-200 rounded">
                                            <FaPaperclip className="w-6 h-6 text-gray-500" />
                                        </div>
                                    )}
                                    <div className="text-xs">
                                        <p className="font-medium truncate max-w-[120px]">{attachment.original_name}</p>
                                        <p className="text-gray-500">{attachment.formatted_size}</p>
                                    </div>
                                </a>
                            ))}
                        </div>
                    </div>
                )}
            </div>

            {/* Replies Section */}
            <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6 mb-6">
                <h2 className="text-lg font-semibold mb-4">{translate('Conversation')}</h2>

                <div className="space-y-4 max-h-[500px] overflow-y-auto">
                    {isLoadingReplies ? (
                        <div className="text-center py-4">{translate('Loading...')}</div>
                    ) : replies.length === 0 ? (
                        <p className="text-gray-500 text-center py-4">{translate('No replies yet.')}</p>
                    ) : (
                        replies.map((reply: TicketReply) => (
                            <div
                                key={reply.id}
                                className={`flex ${reply.is_admin_reply ? 'justify-start' : 'justify-end'}`}
                            >
                                <div
                                    className={`max-w-[80%] p-4 rounded-lg ${
                                        reply.is_admin_reply
                                            ? 'bg-gray-100'
                                            : 'bg-primary/10'
                                    }`}
                                >
                                    <div className="flex items-center gap-2 mb-2">
                                        <span className="font-semibold text-sm">
                                            {reply.is_admin_reply ? translate('Support Team') : reply.user?.name}
                                        </span>
                                        <span className="text-xs text-gray-500">
                                            {format(new Date(reply.created_at), 'PP p')}
                                        </span>
                                    </div>
                                    
                                    <p className="text-gray-700 whitespace-pre-wrap">{reply.message}</p>

                                    {reply.attachment_path && (
                                        <a
                                            href={reply.attachment_url || '#'}
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            className="inline-flex items-center gap-2 mt-2 text-sm text-primary hover:underline"
                                        >
                                            <FaPaperclip className="w-4 h-4" />
                                            {translate('View attachment')}
                                        </a>
                                    )}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>

            {/* Reply Form */}
            {ticket.status !== 'closed' && (
                <div className="bg-white rounded-lg shadow-sm border border-gray-200 p-4 sm:p-6">
                    <h2 className="text-lg font-semibold mb-4">{translate('Add Reply')}</h2>
                    
                    <form onSubmit={formik.handleSubmit} className="space-y-4">
                        <div>
                            <textarea
                                rows={4}
                                placeholder={translate('Type your message...')}
                                className={`w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-primary focus:border-primary resize-none ${
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
                            <label className="flex items-center gap-2 cursor-pointer text-sm text-gray-600 hover:text-gray-800">
                                <FaPaperclip className="w-5 h-5" />
                                <span>
                                    {formik.values.attachment
                                        ? formik.values.attachment.name
                                        : translate('Attach file (optional)')}
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
                                        className="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                    >
                                        {translate('Remove')}
                                    </button>
                                )}

                                <Button
                                    type="submit"
                                    variant="primary"
                                    isLoading={isSubmittingReply}
                                    disabled={isSubmittingReply || !formik.values.message.trim()}
                                    className="flex items-center gap-2"
                                >
                                    <FaPaperPlane className="w-4 h-4" />
                                    {translate('Send')}
                                </Button>
                            </div>
                        </div>
                    </form>
                </div>
            )}

            {ticket.status === 'closed' && (
                <div className="bg-gray-50 rounded-lg p-4 text-center">
                    <p className="text-gray-600">{translate('This ticket has been closed and cannot receive new replies.')}</p>
                </div>
            )}
        </div>
    );
};

export default TicketDetail;