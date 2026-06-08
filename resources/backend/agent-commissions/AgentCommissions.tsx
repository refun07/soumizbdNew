import React, { useState } from 'react';
import { translate } from '../react/utils/translate';
import BalanceOverview from './BalanceOverview';
import CommissionList from './CommissionList';
import { AgentBalance } from './CommissionApi';

const currency = (window as any).config?.currency;

const formatPrice = (value: number): string => {
    if (!currency) return value.toFixed(2);
    const formatted = new Intl.NumberFormat('en-US', {
        maximumFractionDigits: currency.numOfDecimals || 0,
    }).format(value).replaceAll(',', currency.thousandSeparator || ',');

    if (currency.symbol?.position === 'amount_first') return formatted + (currency.symbol.show || '$');
    return (currency.symbol.show || '$') + formatted;
};

const formatCommissionRate = (balance: AgentBalance): string => {
    const { agent_commission_percentage, agent_commission_fixed_amount, agent_commission_mode } = balance.agent;
    switch (agent_commission_mode) {
        case 'fixed':
            return formatPrice(agent_commission_fixed_amount);
        case 'both_highest':
            return `max(${agent_commission_percentage}%, ${formatPrice(agent_commission_fixed_amount)})`;
        case 'both_additive':
            return `${agent_commission_percentage}% + ${formatPrice(agent_commission_fixed_amount)}`;
        case 'percentage':
        default:
            return `${agent_commission_percentage}%`;
    }
};

interface Props {
    initialBalance: AgentBalance;
}

const AgentCommissions: React.FC<Props> = ({ initialBalance }) => {
    const [balance, setBalance] = useState<AgentBalance>(initialBalance);

    return (
        <div className="space-y-6">
            <div className="dashboard-nav pt-6 flex items-center justify-between mb-2">
                <div className="flex items-center">
                    <span className="text-xl mr-3 text-secondary">
                        <i className="fa-regular fa-money-check-dollar"></i>
                    </span>
                    <span className="text-sm sm:text-base font-bold">
                        {translate('My Commissions')}
                    </span>
                </div>
                <div className="flex items-center gap-2">
                    <span className="text-sm text-muted">
                        {translate('Commission Rate')}:
                    </span>
                    <span className="text-sm font-bold text-secondary">
                        {formatCommissionRate(balance)}
                    </span>
                </div>
            </div>

            <BalanceOverview balance={balance} />

            <CommissionList />
        </div>
    );
};

export default AgentCommissions;
