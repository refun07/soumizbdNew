import { apiSlice } from './apiSlice';
import { objectToFormData } from '../../../utils/ObjectFormData';

export interface Ticket {
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
    updated_at: string;
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

export interface TicketAttachment {
    id: number;
    ticket_id: number;
    file_path: string;
    file_type: 'image' | 'video';
    original_name: string;
    file_size: number;
    full_url: string;
    formatted_size: string;
    is_image: boolean;
    is_video: boolean;
}

export interface TicketReply {
    id: number;
    ticket_id: number;
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
        avatar?: string;
    };
}

export interface CreateTicketRequest {
    order_id: string;
    phone: string;
    subject: string;
    description: string;
    priority?: 'low' | 'medium' | 'high';
    files?: File[];
    audio?: File | null;
}

export interface CreateTicketResponse {
    success: boolean;
    status: number;
    message: string;
    data: {
        ticket: Ticket;
        public_url: string;
    };
}

export interface ReplyRequest {
    message: string;
    attachment?: File;
}

const ticketApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Ticket'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            createTicket: builder.mutation<CreateTicketResponse, CreateTicketRequest>({
                query: (body) => {
                    const formData = objectToFormData({
                        order_id: body.order_id,
                        phone: body.phone,
                        subject: body.subject,
                        description: body.description,
                        priority: body.priority || 'medium',
                    });

                    // Add files if present
                    if (body.files && body.files.length > 0) {
                        body.files.forEach((file) => {
                            formData.append('files[]', file);
                        });
                    }

                    // Add audio if present
                    if (body.audio) {
                        formData.append('audio', body.audio);
                    }

                    return {
                        url: '/tickets',
                        method: 'POST',
                        body: formData,
                    };
                },
                invalidatesTags: ['Ticket'],
            }),

            getTicket: builder.query<{ success: boolean; status: number; data: { ticket: Ticket } }, string>({
                query: (publicId) => `/tickets/${publicId}`,
                providesTags: ['Ticket'],
            }),

            getTicketReplies: builder.query<{ success: boolean; status: number; data: { replies: TicketReply[] } }, string>({
                query: (publicId) => `/tickets/${publicId}/replies`,
                providesTags: ['Ticket'],
            }),

            addReply: builder.mutation<any, { publicId: string; data: ReplyRequest }>({
                query: ({ publicId, data }) => {
                    const formData = objectToFormData({
                        message: data.message,
                    });

                    if (data.attachment) {
                        formData.append('attachment', data.attachment);
                    }

                    return {
                        url: `/tickets/${publicId}/reply`,
                        method: 'POST',
                        body: formData,
                    };
                },
                invalidatesTags: ['Ticket'],
            }),
        }),
    });

export const {
    useCreateTicketMutation,
    useGetTicketQuery,
    useGetTicketRepliesQuery,
    useAddReplyMutation,
} = ticketApi;

export default ticketApi;