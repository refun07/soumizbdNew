import React, { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { FaCaretDown } from 'react-icons/fa6';
import OptionDropdown from '../../../react/components/inputs/OptionDropdown';
import { dateRangeFilterOptions } from '../../../react/utils';
import { translate } from '../../../react/utils/translate';
import { ICommonQueryParams, ISaleAmountChart } from '../../types/response';
import { getSaleAmountChartData } from '../../utils';
import SalesAmountChart from '../charts/SalesAmountChart';

const SalesAmountCard = () => {
    const [timeline, setTimeline] =
        useState<ICommonQueryParams['timeline']>('thisMonth');
    const [data, setData] = useState<ISaleAmountChart>({
        days: [],
        totalAmounts: [],
    });

    useEffect(() => {
        getSaleAmountChartData({ timeline })
            .then(setData)
            .catch((error) => {
                toast.error(error.response.data.message);
            });
    }, [timeline]);

    return (
        <div className="sm:col-span-2 2xl:col-span-4 2xl:row-span-3 flex flex-col bg-background rounded-md px-4 lg:px-6 xl:px-9 py-4 lg:py-6 xl:py-8">
            <div className="flex justify-between mb-4">
                <h5 className="text-sm sm:text-base font-medium">
                    {translate('Sales Amount')}
                </h5>

                <div className="flex items-center gap-5 text-xs">
                    <p className="max-lg:hidden">{translate('Date Range')}</p>

                    <OptionDropdown
                        noArrow
                        noStyle
                        name="sort"
                        value={timeline}
                        optionsClassName="right-0"
                        options={dateRangeFilterOptions}
                        components={{
                            Toggler(option: any) {
                                return (
                                    <div className="flex items-center gap-3 border border-border rounded-md py-2 px-4 cursor-pointer">
                                        <div>
                                            <p className="text-foreground">
                                                {option?.label ||
                                                    'By Order Count'}
                                            </p>
                                            <span className="hidden md:inline-block text-muted">
                                                {option?.rangeStr}
                                            </span>
                                        </div>
                                        <FaCaretDown className="text-muted" />
                                    </div>
                                );
                            },
                        }}
                        onChange={(name, option) =>
                            setTimeline(option?.value as any)
                        }
                    />
                </div>
            </div>

            <div className="grow min-h-[170px] md:min-h-[300px]">
                <SalesAmountChart data={data} />
            </div>
        </div>
    );
};

export default SalesAmountCard;
