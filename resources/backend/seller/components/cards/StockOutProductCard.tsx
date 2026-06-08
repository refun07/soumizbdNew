import React, { useEffect, useState } from 'react';
import { GoArrowRight } from 'react-icons/go';
import { GuageMeter } from '../../../react/components/icons';
import { translate } from '../../../react/utils/translate';
import { getTopSellingProducts } from '../../utils';
import { paddedNumber } from '../../../../frontend/utils/numberFormatter';

const StockOutProductCard = () => {
    const [stockOutProductCount, setStockOutProductCount] = useState(0);

    useEffect(() => {
        getTopSellingProducts().then((data) =>
            setStockOutProductCount(data.stockOut),
        );
    }, []);

    return (
        <div className="grow bg-gradient-to-b from-indigo-500 to-indigo-400 text-white rounded-md px-4 lg:px-6 2xl:px-9 py-4 lg:py-6 2xl:py-8 flex flex-col gap-8">
            <div className="relative grow w-full max-w-[180px] mx-auto aspect-square flex flex-col gap-1 items-center justify-center rounded-full text-center">
                <GuageMeter className="absolute inset-0" />
                <span className="text-xs md:text-base font-medium">
                    {translate('Stock Out')}
                </span>
                <span className="text-4xl font-semibold">
                    {paddedNumber(stockOutProductCount)}
                </span>
            </div>

            <div>
                <a
                    href="/seller/products"
                    className="flex items-center justify-center gap-1"
                >
                    {translate('View Products')}
                    <GoArrowRight />
                </a>
            </div>
        </div>
    );
};

export default StockOutProductCard;
