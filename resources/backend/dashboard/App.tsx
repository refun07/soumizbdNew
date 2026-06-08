import React, { ReactNode } from 'react';
import { PiSmiley, PiSmileyBlank, PiSmileySad } from 'react-icons/pi';
import GoalCard from '../react/components/cards/GoalCard';
import { translate } from '../react/utils/translate';
import EarningFromSellerCard from './components/cards/EarningFromSellerCard';
import MostSellingProductsCard from './components/cards/MostSellingProductsCard';
import NewUpdatesCard from './components/cards/NewUpdatesCard';
import OrderUpdates from './components/cards/OrderUpdates';
import OverViewCards from './components/cards/OverViewCards';
import RecentOrders from './components/cards/RecentOrders';
import RecentProducts from './components/cards/RecentProducts';
import RevenueUpdateCard from './components/cards/RevenueUpdateCard';
import StockOutProductCard from './components/cards/StockOutProductCard';
import TopBrandsCard from './components/cards/TopBrandsCard';
import TopCategoriesCard from './components/cards/TopCategoriesCard';
import TopRatedSellersCard from './components/cards/TopRatedSellersCard';
import TopSellerByEarningCard from './components/cards/TopSellerByEarningCard';
import TotalProductsCard from './components/cards/TotalProductsCard';

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
                <GoalCard type="admin" />
            </div>

            <OverViewCards />

            <div className="grid sm:grid-cols-2 2xl:grid-cols-4 gap-4">
                <RevenueUpdateCard />
                <TopCategoriesCard />
                <TopBrandsCard />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                <RecentOrders />
                <OrderUpdates />
            </div>

            <div className="grid grid-cols-1 xl:grid-cols-10 gap-4">
                <div className="xl:col-span-2 flex max-sm:flex-col xl:flex-col gap-4">
                    <TotalProductsCard />
                    <StockOutProductCard />
                </div>

                <RecentProducts />
                <MostSellingProductsCard />
            </div>

            <div className="grid grid-cols-1 xl:grid-cols-10 gap-4">
                <EarningFromSellerCard />

                <TopRatedSellersCard />
                <TopSellerByEarningCard />
            </div>
        </div>
    );
};

export default App;
