import { IConversation, IMessage } from '../../../types/chat';
import { objectToFormData } from '../../../utils/ObjectFormData';
import { apiSlice } from './apiSlice';

const chatApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['Chats', 'Messages'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            getConversations: builder.query<
                IConversation[],
                { searchKey: string }
            >({
                query: (params) => ({
                    url: `chats`,
                    params,
                }),
                providesTags: ['Chats'],
                transformResponse: (res: any) => res.result,
            }),

            createConversation: builder.mutation<any, { shopId: number }>({
                query: (body) => ({
                    url: `/chats`,
                    method: 'POST',
                    body: objectToFormData(body),
                }),
                transformResponse: (res: any) => res.result,
                invalidatesTags: ['Chats'],
            }),

            getMessages: builder.query<any, { conversationId: number }>({
                query: ({ conversationId }) =>
                    `/chats/messages/${conversationId}`,
                providesTags: ['Messages'],
                transformResponse: (res: any) => res.result,
            }),

            sendMessage: builder.mutation<
                IMessage[],
                { chatId: number; message: string }
            >({
                query: (body) => ({
                    url: `/chats/messages`,
                    method: 'POST',
                    body: objectToFormData(body),
                }),
                invalidatesTags: ['Chats'],
                transformResponse: (res: any) => res.result,
            }),
        }),
    });

export const {
    useLazyGetConversationsQuery,
    useCreateConversationMutation,
    useLazyGetMessagesQuery,
    useSendMessageMutation,
} = chatApi;
