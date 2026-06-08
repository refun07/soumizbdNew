import { axiosInstance } from '../react/utils/axios';
import { objectToFormData } from '../react/utils/ObjectFormData';

export interface AgentBalance {
    agent: {
        id: number;
        name: string;
        email: string;
        agent_commission_percentage: number;
        agent_commission_fixed_amount: number;
        agent_commission_mode: 'percentage' | 'fixed' | 'both_highest' | 'both_additive';
        agent_current_balance: number;
    };
    pending_total: number;
    earned_total: number;
    paid_total: number;
    cancelled_total: number;
}

export interface CommissionHistory {
    id: number;
    order_id: number;
    agent_id: number;
    order_amount: number;
    commission_percentage: number;
    commission_fixed_amount: number;
    commission_mode_used: string | null;
    commission_amount: number;
    status: 'pending' | 'earned' | 'paid' | 'cancelled';
    paid_at: string | null;
    created_at: string;
    updated_at: string;
    order: {
        id: number;
        order_group: { order_code: string } | null;
    } | null;
    agent: {
        id: number;
        name: string;
        email: string;
    } | null;
}

export interface PaginatedCommissions {
    data: CommissionHistory[];
    meta: {
        current_page: number;
        from: number;
        last_page: number;
        per_page: number;
        to: number;
        total: number;
    };
}

export const getMyBalance = async (): Promise<AgentBalance> => {
    const res = await axiosInstance.get('/agent-commissions/my-balance');
    return res.data.data;
};

export const getMyCommissions = async (
    params?: { status?: string; page?: number; per_page?: number },
): Promise<PaginatedCommissions> => {
    const res = await axiosInstance.get('/agent-commissions/my-commissions', { params });
    return res.data.data;
};

export const payCommission = async (id: number): Promise<CommissionHistory> => {
    const res = await axiosInstance.post(`/agent-commissions/${id}/pay`, objectToFormData({}));
    return res.data.data;
};

export const payBulkCommissions = async (ids: number[]): Promise<void> => {
    await axiosInstance.post('/agent-commissions/pay-bulk', objectToFormData({ ids }));
};
