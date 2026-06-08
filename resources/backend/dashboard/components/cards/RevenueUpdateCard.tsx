import React, { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { FaCaretDown } from 'react-icons/fa6';
import OptionDropdown from '../../../react/components/inputs/OptionDropdown';
import { dateRangeFilterOptions } from '../../../react/utils';
import { translate } from '../../../react/utils/translate';
import { ICommonQueryParams, IRevenueUpdate } from '../../types/response';
import { getRevenueUpdate } from '../../utils';
import RevenueUpdateChart from '../charts/RevenueUpdateChart';

const RevenueUpdateCard = () => {
    const [timeline, setTimeline] =
        useState<ICommonQueryParams['timeline']>('thisMonth');
    const [data, setData] = useState<IRevenueUpdate>({
        days: [],
        totalAmounts: [],
    });

    useEffect(() => {
        getRevenueUpdate({ timeline })
            .then(setData)
            .catch((error: any) => {
                toast.error(error.response.data.message);
            });
    }, [timeline]);

    return (
        <div className="sm:col-span-2 card flex flex-col">
            <div className="card__title border-none pb-0 flex justify-between">
                <h5 className="text-sm sm:text-base font-medium">
                    {translate('Revenue Update')}
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
                                            <p className="">
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

            <div className="card__content grow min-h-[170px] md:min-h-[300px]">
                <RevenueUpdateChart data={data} />
            </div>
        </div>
    );
};

export default RevenueUpdateCard;
