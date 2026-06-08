import React from 'react';
import {
    IoArrowForward,
    IoBagCheck,
    IoCardOutline,
    IoCarOutline,
    IoCashOutline,
    IoTrendingUp,
    IoWalletOutline,
    IoWarningOutline,
} from 'react-icons/io5';
import RevenueOverTimeChart from './RevenueOverTimeChart';

interface MetricCardsProps {
    quickData: any;
    detailedData: any;
    loading: {
        quick: boolean;
        detailed: boolean;
    };
}

const MetricCards: React.FC<MetricCardsProps> = ({
    quickData,
    detailedData,
    loading,
}) => {
    return (
        <div className="card mb-8 relative p-4" style={{ minHeight: '280px' }}>
            <div className="flex flex-col lg:flex-row h-full gap-0">
                {/* Stats Section - 8/12 columns */}
                <div className="flex-1 lg:flex-[8] pr-8">
                    <div className="flex flex-col h-full justify-center py-4">
                        {loading.quick && !quickData ? (
                            <div className="animate-pulse space-y-8">
                                <div className="flex gap-6">
                                    {[1, 2, 3].map((i) => (
                                        <div
                                            key={i}
                                            className="flex items-center space-x-3 flex-1"
                                        >
                                            <div className="w-10 h-10 bg-muted rounded-full"></div>
                                            <div className="flex-1">
                                                <div className="h-3 bg-muted rounded w-20 mb-2"></div>
                                                <div className="h-5 bg-muted rounded w-16"></div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                                <div className="border-b border-border"></div>
                                <div className="flex gap-6">
                                    {[1, 2, 3].map((i) => (
                                        <div
                                            key={i}
                                            className="flex items-center space-x-3 flex-1"
                                        >
                                            <div className="w-10 h-10 bg-muted rounded-full"></div>
                                            <div className="flex-1">
                                                <div className="h-3 bg-muted rounded w-16 mb-2"></div>
                                                <div className="h-5 bg-muted rounded w-12"></div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ) : (
                            <>
                                {/* First Row */}
                                <div className="flex gap-6 pb-8">
                                    {/* Total Order */}
                                    <div className="flex items-center space-x-3 flex-1">
                                        <div className="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <IoBagCheck className="w-5 h-5 text-green-600" />
                                        </div>
                                        <div>
                                            <p className="text-muted text-xs font-medium">
                                                TOTAL ORDER
                                            </p>
                                            <p className="text-xl font-bold text-foreground mt-1">
                                                {quickData?.summary
                                                    ?.totalOrders || 0}
                                            </p>
                                        </div>
                                    </div>

                                    {/* Vertical divider */}
                                    <div className="w-px bg-border"></div>

                                    {/* Total Revenue */}
                                    <div className="flex items-center space-x-3 flex-1">
                                        <div className="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <IoCashOutline className="w-5 h-5 text-pink-600" />
                                        </div>
                                        <div>
                                            <p className="text-muted text-xs font-medium">
                                                TOTAL REVENUE
                                            </p>
                                            <p className="text-xl font-bold text-foreground mt-1">
                                                {quickData?.summary
                                                    ?.totalRevenue
                                                    ? `${quickData.summary.totalRevenue.toLocaleString()} Tk`
                                                    : '0 Tk'}
                                            </p>
                                        </div>
                                    </div>

                                    {/* Vertical divider */}
                                    <div className="w-px bg-border"></div>

                                    {/* Gross Profit */}
                                    <div className="flex items-center space-x-3 flex-1">
                                        <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <IoTrendingUp className="w-5 h-5 text-blue-600" />
                                        </div>
                                        <div>
                                            <p className="text-muted text-xs font-medium">
                                                GROSS PROFIT
                                            </p>
                                            <p className="text-xl font-bold text-foreground mt-1">
                                                {quickData?.summary?.grossProfit
                                                    ? `${quickData.summary.grossProfit.toLocaleString()} Tk`
                                                    : '0 Tk'}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {/* Horizontal divider line */}
                                <div className="border-b border-gray-200 mb-8"></div>

                                {/* Second Row */}
                                <div className="flex gap-6">
                                    {/* Expenses */}
                                    <div className="flex items-center space-x-3 flex-1">
                                        <div className="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <IoCardOutline className="w-5 h-5 text-yellow-600" />
                                        </div>
                                        <div>
                                            <p className="text-muted text-xs font-medium">
                                                EXPENSES
                                            </p>
                                            <p className="text-xl font-bold text-foreground mt-1">
                                                {quickData?.summary?.expenses
                                                    ? `${quickData.summary.expenses.toLocaleString()} Tk`
                                                    : '0 Tk'}
                                            </p>
                                        </div>
                                    </div>

                                    {/* Vertical divider */}
                                    <div className="w-px bg-border"></div>

                                    {/* Delivery Charge */}
                                    {/* <div className="flex items-center space-x-3 flex-1">
                                        <div className="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <IoCarOutline className="w-5 h-5 text-orange-600" />
                                        </div>
                                        <div>
                                            <p className="text-muted text-xs font-medium">
                                                DELIVERY CHARGE
                                            </p>
                                            <p className="text-xl font-bold text-foreground mt-1">
                                                {quickData?.summary
                                                    ?.deliveryCharges
                                                    ? `${quickData.summary.deliveryCharges.toLocaleString()} Tk`
                                                    : '0 Tk'}
                                            </p>
                                            <button
                                                type="button"
                                                onClick={() => {
                                                    window.location.href =
                                                        '/admin/bizana/delivery-partners';
                                                }}
                                                className="mt-2 text-xs font-semibold text-orange-600 hover:text-orange-700 underline underline-offset-2"
                                            >
                                                View Details
                                            </button>
                                        </div>
                                    </div> */}

                                    {/* Delivery Charge */}
                                    <div className="flex items-center space-x-3 flex-1">
                                        {/* Icon */}
                                        <div className="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <IoCarOutline className="w-5 h-5 text-orange-600" />
                                        </div>

                                        {/* Text Content */}
                                        <div>
                                            <p className="text-muted text-xs font-medium">
                                                DELIVERY CHARGE
                                            </p>
                                            <p className="text-xl font-bold text-foreground mt-1">
                                                {quickData?.summary
                                                    ?.deliveryCharges
                                                    ? `${quickData.summary.deliveryCharges.toLocaleString()} Tk`
                                                    : '0 Tk'}
                                            </p>
                                        </div>

                                        {/* Right Side View Button */}
                                        <button
                                            type="button"
                                            onClick={() => {
                                                window.location.href =
                                                    '/admin/bizana/delivery-partners';
                                            }}
                                            className="ml-auto flex items-center gap-1 text-sm font-semibold text-orange-600 hover:text-orange-700 transition"
                                        >
                                            View
                                            <IoArrowForward className="w-4 h-4" />
                                        </button>
                                    </div>

                                    {/* Vertical divider */}
                                    <div className="w-px bg-border"></div>

                                    {/* Additional stat for balance */}
                                    <div className="flex items-center space-x-3 flex-1">
                                        <div className="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                            <IoWarningOutline className="w-5 h-5 text-gray-600" />
                                        </div>
                                        <div>
                                            <p className="text-muted text-xs font-medium">
                                                DAMAGED
                                            </p>
                                            <p className="text-xl font-bold text-foreground mt-1">
                                                {quickData?.summary?.other
                                                    ? `${quickData.summary.other.toLocaleString()} Tk`
                                                    : '0 Tk'}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </>
                        )}
                    </div>
                </div>

                {/* Net Profit Section - 4/12 columns */}
                <div className="flex-1 lg:flex-[4] pl-8">
                    <div
                        className="p-6 rounded-lg text-white h-full flex flex-col"
                        style={{
                            background:
                                'linear-gradient(to bottom, rgb(55, 76, 185), rgba(55, 76, 185, 0.7))',
                        }}
                    >
                        {loading.quick && !quickData ? (
                            <div className="animate-pulse">
                                <div className="h-3 bg-white/30 rounded w-20 mb-2"></div>
                                <div className="h-8 bg-white/30 rounded w-32 mb-4"></div>
                                <div className="h-3 bg-white/30 rounded w-40"></div>
                            </div>
                        ) : (
                            <>
                                <div className="flex justify-between items-start">
                                    <div className="flex-1">
                                        <p className="text-primary-foreground/80 text-sm font-medium">
                                            NET PROFIT
                                        </p>
                                        <p className="text-3xl font-bold text-white mt-2">
                                            {quickData?.netProfit?.amount
                                                ? `${quickData.netProfit.amount.toLocaleString()} Tk`
                                                : '0 Tk'}
                                        </p>
                                        <div className="flex items-center mt-4">
                                            {quickData?.netProfit?.growth >
                                            0 ? (
                                                <>
                                                    <IoTrendingUp className="w-4 h-4 text-green-300 mr-1" />
                                                    <span className="text-green-300 text-xs">
                                                        {quickData.netProfit.growth.toFixed(
                                                            1,
                                                        )}
                                                        % Comparing to Last
                                                        Period
                                                    </span>
                                                </>
                                            ) : quickData?.netProfit?.growth <
                                              0 ? (
                                                <>
                                                    <IoTrendingUp className="w-4 h-4 text-red-300 mr-1 transform rotate-180" />
                                                    <span className="text-red-300 text-xs">
                                                        {Math.abs(
                                                            quickData.netProfit
                                                                .growth,
                                                        ).toFixed(1)}
                                                        % Comparing to Last
                                                        Period
                                                    </span>
                                                </>
                                            ) : (
                                                <span className="text-white/60 text-xs">
                                                    No change from last period
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                    <div className="w-12 h-12 bg-primary/80 rounded-full flex items-center justify-center flex-shrink-0">
                                        <IoWalletOutline className="w-6 h-6 text-white" />
                                    </div>
                                </div>

                                {/* Line Chart */}
                                <div className="mt-6">
                                    <RevenueOverTimeChart
                                        data={detailedData?.revenueOverTime}
                                        height={96}
                                    />
                                </div>
                            </>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
};

export default MetricCards;
