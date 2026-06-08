import React, { useEffect, useState } from 'react';
import { GoArrowRight } from 'react-icons/go';
import { paddedNumber } from '../../../../frontend/utils/numberFormatter';
import { GuageMeter } from '../../../react/components/icons';
import { translate } from '../../../react/utils/translate';
import { getRecentProducts } from '../../utils';

const StockOutProductCard = () => {
    const [stockOutProductCount, setStockOutProductCount] = useState(0);

    useEffect(() => {
        getRecentProducts().then((data) =>
            setStockOutProductCount(data.stockOutProducts),
        );
    }, []);

    return (
        <div className="grow bg-gradient-to-b from-indigo-500 to-indigo-400 text-white rounded-md px-4 lg:px-6 2xl:px-9 py-4 lg:py-6 2xl:py-8 flex flex-col gap-8">
            <div className="grow">
                <div className="relative w-full max-w-[180px] mx-auto aspect-square flex flex-col gap-1 items-center justify-center rounded-full text-center">
                    <GuageMeter className="absolute inset-0" />
                    <span className="text-xs md:text-base">
                        {translate('Stock Out')}
                    </span>
                    <span className="text-4xl font-semibold">
                        {paddedNumber(stockOutProductCount)}
                    </span>
                </div>
            </div>

            <div>
                <a
                    href="/admin/products"
                    className="inline-flex items-center gap-1"
                >
                    {translate('View Products')}
                    <GoArrowRight />
                </a>
            </div>
        </div>
    );
};

export default StockOutProductCard;
