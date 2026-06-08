import React from 'react';
import { translate } from '../react/utils/translate';
import { AgentBalance } from './CommissionApi';

interface Props {
    balance: AgentBalance | null;
}

const currency = (window as any).config?.currency;

const formatPrice = (value: number): string => {
    if (!currency) return value.toFixed(2);
    const formatted = new Intl.NumberFormat('en-US', {
        maximumFractionDigits: currency.numOfDecimals || 0,
    }).format(value).replaceAll(',', currency.thousandSeparator || ',');

    if (currency.symbol?.position === 'amount_first') return formatted + (currency.symbol.show || '$');
    return (currency.symbol.show || '$') + formatted;
};

const BalanceOverview: React.FC<Props> = ({ balance }) => {
    if (!balance) {
        return (
            <div className="grid grid-cols-2 lg:grid-cols-5 gap-4">
                {[...Array(5)].map((_, i) => (
                    <div key={i} className="card p-4 animate-pulse">
                        <div className="h-4 bg-secondary/20 rounded w-20 mb-2"></div>
                        <div className="h-6 bg-secondary/20 rounded w-28"></div>
                    </div>
                ))}
            </div>
        );
    }

    const cards = [
        { label: translate('Current Balance'), value: balance.agent.agent_current_balance, color: 'text-secondary', bg: 'bg-secondary/10' },
        { label: translate('Pending'), value: balance.pending_total, color: 'text-yellow-600', bg: 'bg-yellow-50' },
        { label: translate('Earned'), value: balance.earned_total, color: 'text-green-600', bg: 'bg-green-50' },
        { label: translate('Paid'), value: balance.paid_total, color: 'text-blue-600', bg: 'bg-blue-50' },
        { label: translate('Cancelled'), value: balance.cancelled_total, color: 'text-red-600', bg: 'bg-red-50' },
    ];

    return (
        <div className="grid grid-cols-2 lg:grid-cols-5 gap-4">
            {cards.map((card) => (
                <div key={card.label} className={`card p-4 rounded-lg ${card.bg}`}>
                    <p className="text-xs text-muted mb-1">{card.label}</p>
                    <p className={`text-lg font-bold ${card.color}`}>{formatPrice(card.value)}</p>
                </div>
            ))}
        </div>
    );
};

export default BalanceOverview;
