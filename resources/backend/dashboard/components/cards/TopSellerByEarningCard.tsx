import React, { useEffect, useState } from 'react';
import {
    currencyFormatter,
    paddedNumber,
} from '../../../../frontend/utils/numberFormatter';
import Image from '../../../react/components/Image';
import { translate } from '../../../react/utils/translate';
import { ISellers } from '../../types/response';
import { getTopSellerByEarnings } from '../../utils';

const TopSellerByEarningCard = () => {
    const [sellers, setSellers] = useState<ISellers[]>([]);

    useEffect(() => {
        getTopSellerByEarnings().then(setSellers);
    }, []);

    return (
        <div className=" xl:col-span-3 card">
            <h5 className="card__title border-none pb-0">
                {translate('Top Sellers')}
                <span className="text-muted ml-2">
                    ({paddedNumber(sellers.length)})
                </span>
            </h5>

            <div className="card__content space-y-4 max-h-[400px] overflow-y-auto">
                {sellers.map((seller) => (
                    <div
                        className="flex justify-between items-center gap-4"
                        key={seller.id}
                    >
                        <div className="flex items-center gap-3">
                            <div>
                                <Image
                                    src={seller.logo}
                                    alt=""
                                    className="w-[60px] aspect-square rounded-full border border-muted"
                                />
                            </div>
                            <h5>{seller.name}</h5>
                        </div>

                        <div className="text-right text-muted">
                            {currencyFormatter(seller.ordersAmount)}
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default TopSellerByEarningCard;
