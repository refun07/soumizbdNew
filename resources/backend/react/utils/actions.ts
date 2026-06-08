import { IConversation, IGoal, IMessage } from '../types';
import { objectToFormData } from './ObjectFormData';
import { axiosInstance } from './axios';

export const getConversations = async (params: any) => {
    const res = await axiosInstance.get('/chats', { params });
    return res.data.result as IConversation[];
};

export const getMessages = async (conversationId: string | number) => {
    const res = await axiosInstance.get(`/chats/messages/${conversationId}`);
    return res.data.result as IMessage[];
};

export const sendMessage = async (chatId: number, message: string) => {
    const res = await axiosInstance.post('/chats/messages', {
        chatId,
        message,
    });
    return res.data.result as IMessage[];
};

export const postGoal = async (data: { monthlyGoalAmount: number }) => {
    const res = await axiosInstance(`/goals`, {
        method: 'POST',
        data: objectToFormData(data),
    });

    return res.data.result as { monthlyGoalAmount: number };
};

export const getGoal = async () => {
    const res = await axiosInstance(`/goals`, {
        method: 'GET',
    });

    if (!res.data.result.goalAmount) return undefined;

    return res.data.result as IGoal;
};
export const sendOTP = async (data: { name: string, phone:string }) => {
    const res = await axiosInstance(`/api/v1/send-otp`, {
        method: 'POST',
        data: objectToFormData(data),
    });

    return res.data.result;
};
export const reSendOTP = async (data: { name: string, phone:string }) => {
    const res = await axiosInstance(`/api/v1/resend-otp`, {
        method: 'POST',
        data: objectToFormData(data),
    });

    return res.data;
};
