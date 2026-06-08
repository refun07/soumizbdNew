import React, { useEffect, useState } from 'react';
import { FaCartShopping } from 'react-icons/fa6';
import { GoArrowRight } from 'react-icons/go';
import { translate } from '../../../react/utils/translate';
import { getRecentProducts } from '../../utils';
import { paddedNumber } from '../../../../frontend/utils/numberFormatter';

const TotalProductsCard = () => {
    const [totalProductCount, setTotalProductCount] = useState(0);

    useEffect(() => {
        getRecentProducts().then((data) =>
            setTotalProductCount(data.totalProducts),
        );
    }, []);

    return (
        <div className="grow flex flex-col gap-2 relative bg-gradient-to-b from-teal-700 to-teal-400 text-white px-4 lg:px-6 2xl:px-9 py-3 lg:py-6 2xl:py-11 rounded-md">
            <span className="bg-teal-500/70 text-teal-700 h-[50px] w-[50px] rounded-full flex items-center justify-center text-xl">
                <FaCartShopping />
            </span>
            <div className="grow">
                <h5 className="text-sm sm:text-base font-medium mb-2">
                    {translate('Total Products')}
                </h5>
                <p className="text-4xl font-semibold">
                    {paddedNumber(totalProductCount)}
                </p>
            </div>

            <div>
                <a
                    href="/admin/products"
                    className="inline-flex items-center gap-1"
                >
                    {translate('View All')}
                    <GoArrowRight />
                </a>
            </div>
        </div>
    );
};

export default TotalProductsCard;
