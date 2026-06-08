import React, { useEffect, useState } from 'react';
import { GoArrowRight } from 'react-icons/go';
import { currencyFormatter } from '../../../../frontend/utils/numberFormatter';
import { GuageMeter } from '../../../react/components/icons';
import { translate } from '../../../react/utils/translate';
import { getTopSellingProducts } from '../../utils';

const TotalCommissionCard = () => {
    const [totalCommission, setTotalCommission] = useState(0);

    useEffect(() => {
        getTopSellingProducts().then((data) =>
            setTotalCommission(data.totalCommission),
        );
    }, []);

    return (
        <div className="grow bg-gradient-to-b from-[#B635F3] to-[#DA99F8] text-white rounded-md px-4 lg:px-6 2xl:px-9 py-4 lg:py-6 2xl:py-8 flex flex-col gap-8">
            <div className="grow">
                <div className="relative w-full max-w-[180px] mx-auto aspect-square flex flex-col gap-1 items-center justify-center rounded-full text-center">
                    <GuageMeter className="absolute inset-0" />
                    <span className="text-xs md:text-base font-medium">
                        {translate('Earnings')}
                    </span>
                    <span className="text-4xl font-semibold">
                        {currencyFormatter(totalCommission)}
                    </span>
                </div>
            </div>

            <div>
                <a
                    href="/seller/orders"
                    className="flex items-center justify-center gap-1"
                >
                    {translate('View Orders')}
                    <GoArrowRight />
                </a>
            </div>
        </div>
    );
};

export default TotalCommissionCard;
