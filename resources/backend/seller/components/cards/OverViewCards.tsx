import React, { useEffect, useState } from 'react';
import { FaBasketShopping, FaCartShopping, FaStore } from 'react-icons/fa6';
import { GoArrowRight } from 'react-icons/go';
import { HiMiniUsers } from 'react-icons/hi2';
import { RxArrowBottomRight, RxArrowTopLeft } from 'react-icons/rx';
import { TbTilde } from 'react-icons/tb';
import {
    currencyFormatter,
    paddedNumber,
} from '../../../../frontend/utils/numberFormatter';
import OptionDropdown from '../../../react/components/inputs/OptionDropdown';
import { dashboardDateFilterOptions } from '../../../react/utils';
import { cn } from '../../../react/utils/cn';
import { translate } from '../../../react/utils/translate';
import { IFilterData } from '../../types/index';
import {
    ITotalEarnings,
    ITotalOrders,
    ITotalProducts,
    ITotalSales,
} from '../../types/response';
import {
    getTotalEarnings,
    getTotalOrders,
    getTotalProducts,
    getTotalSales,
} from '../../utils';

const initialFilterData: IFilterData = {
    data: null,
    filter: {
        timeline: 'thisWeek',
    },
};

const getComparisonPercentageDom = (percentage: number) => {
    const status =
        percentage > 0 ? 'positive' : percentage < 0 ? 'negative' : 'neutral';

    return (
        <span className="flex items-center gap-2 text-xs">
            <span
                className={cn(
                    `md:text-lg bg-white h-4 w-4 md:h-6 md:w-6 inline-flex items-center justify-center rounded-full `,
                    {
                        'text-green-500': status === 'positive',
                        'text-rose-500': status === 'negative',
                        'text-muted': status === 'neutral',
                    },
                )}
            >
                {percentage > 0 ? (
                    <RxArrowTopLeft />
                ) : percentage < 0 ? (
                    <RxArrowBottomRight />
                ) : (
                    <TbTilde />
                )}
            </span>{' '}
            {percentage || 0}%
        </span>
    );
};

const OverViewCards = () => {
    const [totalOrders, setTotalOrders] = useState<IFilterData<ITotalOrders>>({
        ...initialFilterData,
        data: {
            totalOrders: 0,
            totalOrdersComparisonPercentage: 0,
        },
    });
    const [totalSales, setTotalSales] = useState<IFilterData<ITotalSales>>({
        ...initialFilterData,
        data: {
            totalSalesAmount: 0,
            totalSalesComparisonPercentage: 0,
        },
    });

    const [totalEarnings, setTotalEarnings] = useState<
        IFilterData<ITotalEarnings>
    >({
        ...initialFilterData,
        data: {
            totalEarnings: 0,
            totalEarningsComparison: 0,
        },
    });
    const [totalProducts, setTotalProducts] = useState<
        IFilterData<ITotalProducts>
    >({
        ...initialFilterData,
        data: {
            totalProducts: 0,
            totalProductsComparisonPercentage: 0,
        },
    });

    useEffect(() => {
        getTotalOrders(totalOrders.filter).then((totalOrders) => {
            setTotalOrders((prev) => ({ ...prev, data: totalOrders }));
        });
    }, [totalOrders.filter]);

    useEffect(() => {
        getTotalSales(totalSales.filter).then((totalSales) => {
            setTotalSales((prev) => ({ ...prev, data: totalSales }));
        });
    }, [totalSales.filter]);

    useEffect(() => {
        getTotalEarnings(totalEarnings.filter).then((totalEarning) => {
            setTotalEarnings((prev) => ({ ...prev, data: totalEarning }));
        });
    }, [totalEarnings.filter]);

    useEffect(() => {
        getTotalProducts(totalProducts.filter).then((totalProducts) => {
            setTotalProducts((prev) => ({ ...prev, data: totalProducts }));
        });
    }, [totalProducts.filter]);

    const handleFilterChange = (
        name: string,
        value: any,
        stateFn: Function,
    ) => {
        stateFn((prev: any) => ({
            ...prev,
            filter: {
                ...prev.filter,
                [name]: value,
            },
        }));
    };

    return (
        <div className="grid sm:grid-cols-2 2xl:grid-cols-4 gap-4">
            <div className="relative flex flex-col px-4 py-3 lg:px-7 lg:py-5 xl:px-10 xl:pt-8 xl:pb-5 rounded-md bg-gradient-to-b from-amber-500 to-orange-200 text-white h-[160px] sm:h-[210px]">
                <img
                    src="/images/icons/vector-theme-orange.svg"
                    alt=""
                    className="absolute bottom-4 left-0 z-0 w-full pointer-events-none"
                />

                <div className="h-[50px] w-[50px] rounded-full text-[#FFA721] bg-amber-200 absolute top-6 right-8 text-2xl inline-flex justify-center items-center">
                    <FaCartShopping />
                </div>

                <div className="relative grow">
                    <h5 className="max-sm:text-sm uppercase mb-2">
                        {translate('Total Orders')}
                    </h5>

                    <div className="flex items-center gap-4 pt-2 pb-1">
                        <div className="text-xl sm:text-3xl xl:text-5xl font-bold">
                            {paddedNumber(totalOrders.data?.totalOrders)}
                        </div>
                        <div className="flex items-center gap-2 text-xs">
                            {getComparisonPercentageDom(
                                totalOrders.data
                                    .totalOrdersComparisonPercentage,
                            )}
                        </div>
                    </div>

                    <OptionDropdown
                        noArrow
                        noStyle
                        name="timeline"
                        placement="right-start"
                        value={totalOrders.filter.timeline || 'overall'}
                        optionsClassName="top-0 left-[calc(100%+1rem)]"
                        options={dashboardDateFilterOptions}
                        onChange={(name, option) => {
                            handleFilterChange(
                                name,
                                option.value,
                                setTotalOrders,
                            );
                        }}
                    />
                </div>

                <div className="text-right relative">
                    <a
                        href="/seller/orders"
                        className="inline-flex items-center gap-1"
                    >
                        {translate('View Orders')}
                        <GoArrowRight />
                    </a>
                </div>
            </div>
            <div className="relative flex flex-col px-4 py-3 lg:px-7 lg:py-5 xl:px-10 xl:pt-8 xl:pb-5 rounded-md bg-gradient-to-b from-sky-500 to-blue-300 text-white h-[160px] sm:h-[210px]">
                <img
                    src="/images/icons/vector-blue.svg"
                    alt=""
                    className="absolute bottom-4 left-0 z-0 w-full pointer-events-none"
                />

                <div className="h-[50px] w-[50px] rounded-full text-sky-500 bg-blue-300 absolute top-6 right-8 text-2xl inline-flex justify-center items-center">
                    <FaBasketShopping />
                </div>

                <div className="relative grow">
                    <h5 className="max-sm:text-sm uppercase mb-2">
                        {translate('Sales')}
                    </h5>

                    <div className="flex items-center gap-4 pt-2 pb-1">
                        <div className="text-xl sm:text-3xl xl:text-5xl font-bold">
                            {currencyFormatter(
                                totalSales.data?.totalSalesAmount,
                            )}
                        </div>
                        <div className="flex items-center gap-2 text-xs">
                            {getComparisonPercentageDom(
                                totalSales.data.totalSalesComparisonPercentage,
                            )}
                        </div>
                    </div>

                    <OptionDropdown
                        noArrow
                        noStyle
                        name="timeline"
                        placement="right-start"
                        value={totalSales.filter.timeline || 'overall'}
                        optionsClassName="top-0 left-[calc(100%+1rem)]"
                        options={dashboardDateFilterOptions}
                        onChange={(name, option) =>
                            handleFilterChange(
                                name,
                                option.value,
                                setTotalSales,
                            )
                        }
                    />
                </div>

                <div className="text-right relative">
                    <a
                        href="/seller/orders"
                        className="inline-flex items-center gap-1"
                    >
                        {translate('View Report')}
                        <GoArrowRight />
                    </a>
                </div>
            </div>
            <div className="relative flex flex-col px-4 py-3 lg:px-7 lg:py-5 xl:px-10 xl:pt-8 xl:pb-5 rounded-md bg-gradient-to-b from-indigo-500 to-indigo-400 text-white h-[160px] sm:h-[210px]">
                <img
                    src="/images/icons/vector-indigo.svg"
                    alt=""
                    className="absolute bottom-4 left-0 z-0 w-full pointer-events-none"
                />

                <div className="h-[50px] w-[50px] rounded-full text-indigo-500 bg-indigo-400 absolute top-6 right-8 text-2xl inline-flex justify-center items-center">
                    <FaStore />
                </div>

                <div className="relative grow">
                    <h5 className="max-sm:text-sm uppercase mb-2">
                        {translate('Total Earnings')}
                    </h5>

                    <div className="flex items-center gap-4 pt-2 pb-1">
                        <div className="text-xl sm:text-3xl xl:text-5xl font-bold">
                            {currencyFormatter(
                                totalEarnings.data?.totalEarnings,
                            )}
                        </div>
                        <div className="flex items-center gap-2 text-xs">
                            {getComparisonPercentageDom(
                                totalEarnings.data.totalEarningsComparison,
                            )}
                        </div>
                    </div>

                    <OptionDropdown
                        noArrow
                        noStyle
                        name="timeline"
                        placement="right-start"
                        value={totalProducts.filter.timeline || 'overall'}
                        optionsClassName="top-0 left-[calc(100%+1rem)]"
                        options={dashboardDateFilterOptions}
                        onChange={(name, option) =>
                            handleFilterChange(
                                name,
                                option.value,
                                setTotalEarnings,
                            )
                        }
                    />
                </div>

                <div className="text-right relative">
                    <a
                        href="/seller/orders"
                        className="inline-flex items-center gap-1"
                    >
                        {translate('View Earnings')}
                        <GoArrowRight />
                    </a>
                </div>
            </div>
            <div className="relative flex flex-col px-4 py-3 lg:px-7 lg:py-5 xl:px-10 xl:pt-8 xl:pb-5 rounded-md bg-gradient-to-b from-purple-500 to-purple-300 text-white h-[160px] sm:h-[210px]">
                <img
                    src="/images/icons/vector-purple.svg"
                    alt=""
                    className="absolute bottom-4 left-0 z-0 w-full pointer-events-none"
                />

                <div className="h-[50px] w-[50px] rounded-full text-purple-500 bg-fuchsia-400 absolute top-6 right-8 text-2xl inline-flex justify-center items-center">
                    <HiMiniUsers />
                </div>

                <div className="relative grow">
                    <h5 className="max-sm:text-sm uppercase mb-2">
                        {translate('Total Products')}
                    </h5>

                    <div className="flex items-center gap-4 pt-2 pb-1">
                        <div className="text-xl sm:text-3xl xl:text-5xl font-bold">
                            {paddedNumber(totalProducts.data?.totalProducts)}
                        </div>
                        <div className="flex items-center gap-2 text-xs">
                            {getComparisonPercentageDom(
                                totalProducts.data
                                    .totalProductsComparisonPercentage,
                            )}
                        </div>
                    </div>

                    <OptionDropdown
                        noArrow
                        noStyle
                        name="timeline"
                        placement="right-start"
                        value={totalProducts.filter.timeline || 'overall'}
                        optionsClassName="top-0 left-[calc(100%+1rem)]"
                        options={dashboardDateFilterOptions}
                        onChange={(name, option) =>
                            handleFilterChange(
                                name,
                                option.value,
                                setTotalProducts,
                            )
                        }
                    />
                </div>

                <div className="text-right relative">
                    <a
                        href="/seller/products"
                        className="inline-flex items-center gap-1"
                    >
                        {translate('View Products')}
                        <GoArrowRight />
                    </a>
                </div>
            </div>
        </div>
    );
};

export default OverViewCards;
