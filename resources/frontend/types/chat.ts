export interface IConversation {
    id: number;
    shopId: number;
    userId: number;
    shopSlug: string;
    shopName: string;
    shopLogo: string;
    userName: string;
    userAvatar: string;
    lastMessageAt: string;
    unseenMessageCounter: number;
}

export interface IMessage {
    id: number;
    conversationId: number;
    message: string;
    isSeenByReceiver: number;
    userId: number;
    userName: string;
    userAvatar: string;
    messageAt: string;
}
