import React, { useEffect, useState } from 'react';
import { GoArrowRight } from 'react-icons/go';
import {
    currencyFormatter,
    paddedNumber,
} from '../../../../frontend/utils/numberFormatter';
import Image from '../../../react/components/Image';
import OptionDropdown from '../../../react/components/inputs/OptionDropdown';
import { translate } from '../../../react/utils/translate';
import { IBrand, ICommonQueryParams } from '../../types/response';
import { getByFilterOptions, getTopBrands } from '../../utils';

const TopBrandsCard = () => {
    const [getBy, setGetBy] =
        useState<ICommonQueryParams['getBy']>('orderCount');
    const [brands, setBrands] = useState<IBrand[]>([]);
    const [totalBrands, setTotalBrands] = useState(0);

    useEffect(() => {
        getTopBrands({ getBy }).then((response) => {
            setBrands(response.topBrands);
            setTotalBrands(response.totalBrands);
        });
    }, [getBy]);

    return (
        <div className="card flex flex-col">
            <div className="card__title border-none pb-0 flex justify-between">
                <h5 className="text-sm sm:text-base font-medium">
                    {translate('Top Brands')}
                </h5>

                <OptionDropdown
                    noArrow
                    noStyle
                    name="getBy"
                    value={getBy}
                    togglerClassName="text-muted"
                    optionsClassName="right-0"
                    options={getByFilterOptions}
                    onChange={(name, option) => {
                        setGetBy(option.value as ICommonQueryParams['getBy']);
                    }}
                />
            </div>

            <div className="card__content grow space-y-2.5 overflow-y-auto">
                {brands.map((brand, i) => (
                    <div className="flex items-center gap-5" key={i}>
                        <div className="border border-border rounded-sm">
                            <Image
                                src={brand.thumbnailImage}
                                alt=""
                                className="max-w-[44px] aspect-square"
                            />
                        </div>
                        <h5 className="grow text-xs sm:text-sm font-public-sans">
                            {brand.name}
                        </h5>
                        <p className="text-muted">
                            {getBy === 'orderCount'
                                ? paddedNumber(brand.totalSaleCount)
                                : currencyFormatter(brand.totalSaleAmount)}
                        </p>
                    </div>
                ))}
            </div>

            <div className="card__footer">
                <p className="uppercase text-muted mb-2">
                    {translate('Total Brands')}
                </p>
                <div className="flex items-center justify-between">
                    <div className="text-[26px] font-semibold text-foreground">
                        {paddedNumber(totalBrands)}
                    </div>

                    <a
                        href="/admin/brands"
                        className="inline-flex items-center gap-1 text-secondary"
                    >
                        {translate('View All')}
                        <GoArrowRight />
                    </a>
                </div>
            </div>
        </div>
    );
};

export default TopBrandsCard;
