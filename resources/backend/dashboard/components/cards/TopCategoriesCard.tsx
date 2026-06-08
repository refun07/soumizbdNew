import React, { useEffect, useState } from 'react';
import { GoArrowRight } from 'react-icons/go';
import { paddedNumber } from '../../../../frontend/utils/numberFormatter';
import OptionDropdown from '../../../react/components/inputs/OptionDropdown';
import { translate } from '../../../react/utils/translate';
import { ICategories, TGetBy } from '../../types/response';
import { getByFilterOptions, getTopCategories } from '../../utils';
import TopCategoryChart from '../charts/TopCategoryChart';

const TopCategoriesCard = () => {
    const [getBy, setGetBy] = useState<TGetBy>('orderCount');
    const [topCategoryData, setTopCategoryData] = useState<ICategories>();

    useEffect(() => {
        getTopCategories({ getBy }).then((topCategories) => {
            setTopCategoryData(topCategories);
        });
    }, [getBy]);

    const graphData = topCategoryData?.topCategories.map((item) => {
        const value =
            getBy === 'orderAmount'
                ? item.totalSaleAmount
                : item.totalSaleCount;

        return {
            name: item.name,
            value,
        };
    });

    return (
        <div className="card">
            <div className="card__title border-none pb-0 flex justify-between">
                <h5 className="text-sm sm:text-base font-medium">
                    {translate('Top Categories')}
                </h5>

                <OptionDropdown
                    noArrow
                    noStyle
                    name="getBy"
                    value={getBy}
                    togglerClassName="text-muted"
                    optionsClassName="right-0"
                    options={getByFilterOptions}
                    onChange={(_name, option) =>
                        setGetBy(option.value as TGetBy)
                    }
                />
            </div>

            <div className="card__content">
                <TopCategoryChart data={graphData} />
            </div>

            <div className="card__footer pt-0">
                <p className="uppercase text-muted mb-2">
                    {translate('Total Category')}
                </p>
                <div className="flex items-center justify-between">
                    <div className="text-[26px] font-semibold text-foreground">
                        {paddedNumber(topCategoryData?.totalCategories)}
                    </div>

                    <a
                        href="/admin/categories"
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

export default TopCategoriesCard;
