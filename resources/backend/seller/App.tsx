import React, { ReactNode } from 'react';
import { PiSmiley, PiSmileyBlank, PiSmileySad } from 'react-icons/pi';
import GoalCard from '../react/components/cards/GoalCard';
import { translate } from '../react/utils/translate';
import ActiveCampaignsCard from './components/cards/ActiveCampaignsCard';
import NewUpdatesCard from './components/cards/NewUpdatesCard';
import OrderCountCards from './components/cards/OrderCountCards';
import OrderUpdates from './components/cards/OrderUpdates';
import OverViewCards from './components/cards/OverViewCards';
import RecentOrders from './components/cards/RecentOrders';
import SalesAmountCard from './components/cards/SalesAmountCard';
import StockOutProductCard from './components/cards/StockOutProductCard';
import TopSellingProducts from './components/cards/TopSellingProducts';
import TotalCommissionCard from './components/cards/TotalCommissionCard';

const orderStatusColors = {
    lime: {
        bg: '#EDF8DA',
        iconBg: '#CEE3AB',
        icon: '#F3FFDF',
    },
    sky: {
        bg: '#DAF7F7',
        iconBg: '#B5E8E8',
        icon: '#E0FFFF',
    },
    red: {
        bg: '#F7DADA',
        iconBg: '#F4C4C4',
        icon: '#FBDFDF',
    },
    violet: {
        bg: '#DADBF7',
        iconBg: '#C8C9F5',
        icon: '#DADBFF',
    },
};

export const impressions: {
    name: string;
    icon: ReactNode;
    value: 'positive' | 'negative' | 'neutral';
}[] = [
    { name: translate('Positive'), icon: <PiSmiley />, value: 'positive' },
    { name: translate('Negative'), icon: <PiSmileySad />, value: 'negative' },
    { name: translate('Neutral'), icon: <PiSmileyBlank />, value: 'neutral' },
];

const App = () => {
    return (
        <div className="space-y-[30px]">
            <div className="grid xl:grid-cols-2 gap-4">
                <NewUpdatesCard />
                <GoalCard type="seller" />
            </div>

            <OverViewCards />

            <div className="grid sm:grid-cols-2 2xl:grid-cols-6 gap-4">
                <SalesAmountCard />
                <OrderCountCards />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                <RecentOrders />
                <OrderUpdates />
            </div>

            <div className="grid grid-cols-1 xl:grid-cols-10 gap-4">
                <div className="xl:col-span-2 flex max-sm:flex-col xl:flex-col gap-4">
                    <StockOutProductCard />
                    <TotalCommissionCard />
                </div>

                <TopSellingProducts />
                <ActiveCampaignsCard />
            </div>
        </div>
    );
};

export default App;
