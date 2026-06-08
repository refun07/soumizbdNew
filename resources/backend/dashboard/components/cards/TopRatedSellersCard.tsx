import React, { useEffect, useState } from 'react';
import { PiSmiley } from 'react-icons/pi';
import {
    numberFormatter,
    paddedNumber,
} from '../../../../frontend/utils/numberFormatter';
import Image from '../../../react/components/Image';
import ThemeRating from '../../../react/components/ThemeRating';
import { translate } from '../../../react/utils/translate';
import { ISellers } from '../../types/response';
import { getTopRatedSellers } from '../../utils';

const TopRatedSellersCard = () => {
    const [sellers, setSellers] = useState<ISellers[]>([]);

    useEffect(() => {
        getTopRatedSellers().then(setSellers);
    }, []);

    return (
        <div className="xl:col-span-4 card">
            <h5 className="card__title border-none pb-0">
                {translate('Top Rated Sellers')}
                <span className="text-muted ml-2">
                    ({paddedNumber(sellers.length)})
                </span>
            </h5>

            <div className="card__content space-y-4 max-h-[400px] overflow-y-auto">
                <table className="w-full [&_td]:py-2">
                    <tbody>
                        {sellers.map((seller) => (
                            <tr key={seller.id}>
                                <td>
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
                                </td>
                                <td>
                                    <div className="hidden md:flex items-center">
                                        <span className="text-3xl leading-none text-theme-orange">
                                            <PiSmiley />
                                        </span>
                                        <span className="ml-2">
                                            {translate('Positive')}
                                        </span>
                                        <span className="text-muted ml-1">
                                            (
                                            {paddedNumber(
                                                seller.positiveImpressions,
                                            )}
                                            )
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div className="text-right flex flex-col items-end">
                                        <p>
                                            {numberFormatter(
                                                seller.rating.average,
                                            )}{' '}
                                            {translate('out of 5')}
                                        </p>
                                        <ThemeRating
                                            readOnly
                                            rating={seller.rating.average}
                                            totalReviews={seller.rating.total}
                                        />
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default TopRatedSellersCard;
