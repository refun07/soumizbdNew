import React, { useState, useEffect } from 'react';
import {
    IoBagCheck,
    IoCashOutline,
    IoTrendingUp,
    IoWalletOutline,
    IoCardOutline,
    IoWarningOutline,
    IoCheckmarkCircle,
    IoTime,
    IoCarOutline,
    IoReturnDownBack,
    IoCloseCircle,
    IoCalendar,
} from 'react-icons/io5';
import GrowthComparisonChart from '../components/charts/GrowthComparisonChart';
import ExpenseBreakdownChart from '../components/charts/ExpenseBreakdownChart';
import TopSellingProducts from '../components/tables/TopSellingProducts';
import TopSellingCategories from '../components/tables/TopSellingCategories';
import DateRangePicker from '../components/DateRangePicker';

interface BizanaAdminDashboardProps {
    section: string;
    apiPrefix: string;
}

interface DashboardData {
    quickData?: any;
    detailedData?: any;
    loading: {
        quick: boolean;
        detailed: boolean;
    };
    error: {
        quick?: string;
        detailed?: string;
    };
    dateRange: {
        from: string;
        to: string;
    };
    setDateRange: (range: { from: string; to: string }) => void;
    pagination: {
        productsPage: number;
        categoriesPage: number;
    };
    setProductsPage: (page: number) => void;
    setCategoriesPage: (page: number) => void;
}

// Custom hook for admin dashboard data
const useAdminDashboardData = (apiPrefix: string, section: string): DashboardData => {
    const [quickData, setQuickData] = useState<any>(null);
    const [detailedData, setDetailedData] = useState<any>(null);
    const [loading, setLoading] = useState({
        quick: false,
        detailed: false,
    });
    const [error, setError] = useState({
        quick: '',
        detailed: '',
    });
    const [dateRange, setDateRange] = useState({
        from: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0], // 30 days ago
        to: new Date().toISOString().split('T')[0], // today
    });
    const [pagination, setPagination] = useState({
        productsPage: 1,
        categoriesPage: 1,
    });

    useEffect(() => {
        const fetchQuickData = async () => {
            setLoading(prev => ({ ...prev, quick: true }));
            setError(prev => ({ ...prev, quick: '' }));

            try {
                const response = await fetch(`${apiPrefix}/dashboard/quick?from=${dateRange.from}&to=${dateRange.to}`);
                if (!response.ok) throw new Error('Failed to fetch quick data');
                const data = await response.json();
                setQuickData(data);
            } catch (err) {
                setError(prev => ({ ...prev, quick: err instanceof Error ? err.message : 'Unknown error' }));
            } finally {
                setLoading(prev => ({ ...prev, quick: false }));
            }
        };

        fetchQuickData();
    }, [apiPrefix, dateRange]);

    useEffect(() => {
        const fetchDetailedData = async () => {
            setLoading(prev => ({ ...prev, detailed: true }));
            setError(prev => ({ ...prev, detailed: '' }));

            try {
                const response = await fetch(`${apiPrefix}/dashboard/detailed?from=${dateRange.from}&to=${dateRange.to}&products_page=${pagination.productsPage}&categories_page=${pagination.categoriesPage}`);
                if (!response.ok) throw new Error('Failed to fetch detailed data');
                const data = await response.json();
                setDetailedData(data);
            } catch (err) {
                setError(prev => ({ ...prev, detailed: err instanceof Error ? err.message : 'Unknown error' }));
            } finally {
                setLoading(prev => ({ ...prev, detailed: false }));
            }
        };

        fetchDetailedData();
    }, [apiPrefix, dateRange, pagination.productsPage, pagination.categoriesPage]);

    return {
        quickData,
        detailedData,
        loading,
        error,
        dateRange,
        setDateRange,
        pagination,
        setProductsPage: (page: number) => setPagination(prev => ({ ...prev, productsPage: page })),
        setCategoriesPage: (page: number) => setPagination(prev => ({ ...prev, categoriesPage: page })),
    };
};

// Net Profit Line Chart
const NetProfitChart = () => {
    return (
        <div className="h-24 w-full flex items-center">
            <svg viewBox="0 0 200 60" className="w-full h-full">
                <path
                    d="M 10 50 Q 50 20 100 30 T 190 10"
                    fill="none"
                    stroke="#ffffff"
                    strokeWidth="2"
                    strokeLinecap="round"
                    opacity="0.8"
                />
            </svg>
        </div>
    );
};

const BizanaAdminDashboard: React.FC<BizanaAdminDashboardProps> = ({ section, apiPrefix }) => {
    const {
        quickData,
        detailedData,
        loading,
        error,
        dateRange,
        setDateRange,
        pagination,
        setProductsPage,
        setCategoriesPage,
    } = useAdminDashboardData(apiPrefix, section);

    const renderDashboardContent = () => {
        switch (section) {
            case 'products':
                return (
                    <div className="bg-white p-6 rounded-lg shadow-sm">
                        <h3 className="text-lg font-semibold text-gray-800 mb-4">Products Analytics</h3>
                        <p className="text-gray-600">Products analytics dashboard coming soon...</p>
                    </div>
                );

            case 'orders':
                return (
                    <div className="bg-white p-6 rounded-lg shadow-sm">
                        <h3 className="text-lg font-semibold text-gray-800 mb-4">Orders Analytics</h3>
                        <p className="text-gray-600">Orders analytics dashboard coming soon...</p>
                    </div>
                );

            case 'expenses':
                return (
                    <div className="bg-white p-6 rounded-lg shadow-sm">
                        <h3 className="text-lg font-semibold text-gray-800 mb-4">Daily Expenses</h3>
                        <p className="text-gray-600">Daily expenses dashboard coming soon...</p>
                    </div>
                );

            case 'sales':
                return (
                    <div className="bg-white p-6 rounded-lg shadow-sm">
                        <h3 className="text-lg font-semibold text-gray-800 mb-4">Sales Summary</h3>
                        <p className="text-gray-600">Sales summary dashboard coming soon...</p>
                    </div>
                );

            default:
            case 'dashboard':
                return (
                    <>
                        {/* Main Dashboard Content */}
                        <div className="bg-white p-4 mb-4 rounded-md">
                            {/* Title and Date Range */}
                            <div className="flex justify-between items-center">
                                <DateRangePicker
                                    startDate={dateRange.from}
                                    endDate={dateRange.to}
                                    onDateChange={setDateRange}
                                />
                            </div>

                            {/* Error Display */}
                            {(error.quick || error.detailed) && (
                                <div className="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <p className="text-red-600 text-sm">
                                        {error.quick || error.detailed}
                                    </p>
                                </div>
                            )}

                            {/* Unified Metric Card */}
                            <div className="bg-white rounded-lg shadow-sm mb-8 relative" style={{ minHeight: '280px' }}>
                                <div className="flex flex-col lg:flex-row h-full gap-0">
                                    {/* Stats Section - 8/12 columns */}
                                    <div className="flex-1 lg:flex-[8] pr-8">
                                        <div className="flex flex-col h-full justify-center py-8">
                                            {loading.quick && !quickData ? (
                                                <div className="animate-pulse space-y-8">
                                                    <div className="flex gap-6">
                                                        {[1, 2, 3].map((i) => (
                                                            <div key={i} className="flex items-center space-x-3 flex-1">
                                                                <div className="w-10 h-10 bg-gray-200 rounded-full"></div>
                                                                <div className="flex-1">
                                                                    <div className="h-3 bg-gray-200 rounded w-20 mb-2"></div>
                                                                    <div className="h-5 bg-gray-200 rounded w-16"></div>
                                                                </div>
                                                            </div>
                                                        ))}
                                                    </div>
                                                    <div className="border-b border-gray-200"></div>
                                                    <div className="flex gap-6">
                                                        {[1, 2, 3].map((i) => (
                                                            <div key={i} className="flex items-center space-x-3 flex-1">
                                                                <div className="w-10 h-10 bg-gray-200 rounded-full"></div>
                                                                <div className="flex-1">
                                                                    <div className="h-3 bg-gray-200 rounded w-16 mb-2"></div>
                                                                    <div className="h-5 bg-gray-200 rounded w-12"></div>
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
                                                                <p className="text-gray-500 text-xs font-medium">TOTAL ORDER</p>
                                                                <p className="text-xl font-bold text-gray-800 mt-1">
                                                                    {quickData?.summary?.totalOrders || 0}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        {/* Vertical divider */}
                                                        <div className="w-px bg-gray-200"></div>

                                                        {/* Total Revenue */}
                                                        <div className="flex items-center space-x-3 flex-1">
                                                            <div className="w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                                <IoCashOutline className="w-5 h-5 text-pink-600" />
                                                            </div>
                                                            <div>
                                                                <p className="text-gray-500 text-xs font-medium">TOTAL REVENUE</p>
                                                                <p className="text-xl font-bold text-gray-800 mt-1">
                                                                    {quickData?.summary?.totalRevenue ? `${quickData.summary.totalRevenue.toLocaleString()} Tk` : '0 Tk'}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        {/* Vertical divider */}
                                                        <div className="w-px bg-gray-200"></div>

                                                        {/* Gross Profit */}
                                                        <div className="flex items-center space-x-3 flex-1">
                                                            <div className="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                                <IoTrendingUp className="w-5 h-5 text-blue-600" />
                                                            </div>
                                                            <div>
                                                                <p className="text-gray-500 text-xs font-medium">GROSS PROFIT</p>
                                                                <p className="text-xl font-bold text-gray-800 mt-1">
                                                                    {quickData?.summary?.grossProfit ? `${quickData.summary.grossProfit.toLocaleString()} Tk` : '0 Tk'}
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
                                                                <p className="text-gray-500 text-xs font-medium">EXPENSES</p>
                                                                <p className="text-xl font-bold text-gray-800 mt-1">
                                                                    {quickData?.summary?.expenses ? `${quickData.summary.expenses.toLocaleString()} Tk` : '0 Tk'}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        {/* Vertical divider */}
                                                        <div className="w-px bg-gray-200"></div>

                                                        {/* Delivery Charge */}
                                                        <div className="flex items-center space-x-3 flex-1">
                                                            <div className="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                                <IoCarOutline className="w-5 h-5 text-orange-600" />
                                                            </div>
                                                            <div>
                                                                <p className="text-gray-500 text-xs font-medium">DELIVERY CHARGE</p>
                                                                <p className="text-xl font-bold text-gray-800 mt-1">
                                                                    {quickData?.summary?.deliveryCharges ? `${quickData.summary.deliveryCharges.toLocaleString()} Tk` : '0 Tk'}
                                                                </p>
                                                            </div>
                                                        </div>

                                                        {/* Vertical divider */}
                                                        <div className="w-px bg-gray-200"></div>

                                                        {/* Additional stat for balance */}
                                                        <div className="flex items-center space-x-3 flex-1">
                                                            <div className="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                                <IoWarningOutline className="w-5 h-5 text-gray-600" />
                                                            </div>
                                                            <div>
                                                                <p className="text-gray-500 text-xs font-medium">OTHER</p>
                                                                <p className="text-xl font-bold text-gray-800 mt-1">
                                                                    {quickData?.summary?.other ? `${quickData.summary.other.toLocaleString()} Tk` : '0 Tk'}
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
                                                background: 'linear-gradient(to bottom, rgb(55, 76, 185), rgba(55, 76, 185, 0.7))',
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
                                                            <p className="text-primary-foreground/80 text-sm font-medium">NET PROFIT</p>
                                                            <p className="text-3xl font-bold text-white mt-2">
                                                                {quickData?.netProfit?.amount ? `${quickData.netProfit.amount.toLocaleString()} Tk` : '0 Tk'}
                                                            </p>
                                                            <div className="flex items-center mt-4">
                                                                {quickData?.netProfit?.growth > 0 ? (
                                                                    <>
                                                                        <IoTrendingUp className="w-4 h-4 text-green-300 mr-1" />
                                                                        <span className="text-green-300 text-xs">
                                                                            {quickData.netProfit.growth.toFixed(1)}% Comparing to Last Period
                                                                        </span>
                                                                    </>
                                                                ) : quickData?.netProfit?.growth < 0 ? (
                                                                    <>
                                                                        <IoTrendingUp className="w-4 h-4 text-red-300 mr-1 transform rotate-180" />
                                                                        <span className="text-red-300 text-xs">
                                                                            {Math.abs(quickData.netProfit.growth).toFixed(1)}% Comparing to Last Period
                                                                        </span>
                                                                    </>
                                                                ) : (
                                                                    <span className="text-white/60 text-xs">No change from last period</span>
                                                                )}
                                                            </div>
                                                        </div>
                                                        <div className="w-12 h-12 bg-primary/80 rounded-full flex items-center justify-center flex-shrink-0">
                                                            <IoWalletOutline className="w-6 h-6 text-white" />
                                                        </div>
                                                    </div>

                                                    {/* Line Chart */}
                                                    <div className="mt-6">
                                                        <NetProfitChart />
                                                    </div>
                                                </>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Charts Section */}
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                            {/* Growth Comparison Chart - 2/3 width */}
                            <div className="lg:col-span-2 bg-white p-6 rounded-lg shadow-sm">
                                <h3 className="text-lg font-semibold text-gray-800 mb-4">Growth Comparison</h3>
                                <GrowthComparisonChart
                                    height={320}
                                    primaryLabel="Current Period"
                                    secondaryLabel="Previous Period"
                                    data={detailedData?.growthComparison}
                                />
                            </div>

                            {/* Expense Breakdown Chart - 1/3 width */}
                            <div className="bg-white p-6 rounded-lg shadow-sm">
                                <h3 className="text-lg font-semibold text-gray-800 mb-4">Expense Breakdown</h3>
                                <ExpenseBreakdownChart
                                    height={400}
                                    donutMode={false}
                                    data={detailedData?.expenseBreakdown}
                                />
                            </div>
                        </div>

                        {/* Status Cards */}
                        <div className="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
                            {loading.quick && !quickData ? (
                                Array.from({ length: 5 }).map((_, i) => (
                                    <div key={i} className="bg-white p-4 rounded-lg shadow-sm animate-pulse">
                                        <div className="flex justify-between items-center py-8">
                                            <div className="flex items-center gap-2">
                                                <div className="w-8 h-8 bg-gray-200 rounded"></div>
                                                <div className="h-3 bg-gray-200 rounded w-16"></div>
                                            </div>
                                            <div className="h-5 bg-gray-200 rounded w-8"></div>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <>
                                    <div className="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                                        <div className="flex items-center gap-2">
                                            <IoTime className="w-8 h-8 text-blue-500" />
                                            <p className="text-xs text-gray-600 text-center">Order Placed</p>
                                        </div>
                                        <p className="text-lg font-bold text-gray-800">{quickData?.orderStats?.placed || 0}</p>
                                    </div>
                                    <div className="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                                        <div className="flex items-center gap-2">
                                            <IoCheckmarkCircle className="w-8 h-8 text-green-500" />
                                            <p className="text-xs text-gray-600 text-center">Successful Order</p>
                                        </div>
                                        <p className="text-lg font-bold text-gray-800">{quickData?.orderStats?.successful || 0}</p>
                                    </div>
                                    <div className="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                                        <div className="flex items-center gap-2">
                                            <IoReturnDownBack className="w-8 h-8 text-orange-500" />
                                            <p className="text-xs text-gray-600 text-center">Returned Order</p>
                                        </div>
                                        <p className="text-lg font-bold text-gray-800">{quickData?.orderStats?.returned || 0}</p>
                                    </div>
                                    <div className="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                                        <div className="flex items-center gap-2">
                                            <IoWarningOutline className="w-8 h-8 text-yellow-500" />
                                            <p className="text-xs text-gray-600 text-center">Damaged Order</p>
                                        </div>
                                        <p className="text-lg font-bold text-gray-800">{quickData?.orderStats?.damaged || 0}</p>
                                    </div>
                                    <div className="bg-white p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                                        <div className="flex items-center gap-2">
                                            <IoCloseCircle className="w-8 h-8 text-red-500" />
                                            <p className="text-xs text-gray-600 text-center">Order Cancelled</p>
                                        </div>
                                        <p className="text-lg font-bold text-gray-800">{quickData?.orderStats?.cancelled || 0}</p>
                                    </div>
                                </>
                            )}
                        </div>

                        {/* Data Tables */}
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {/* Top Selling Products */}
                            <TopSellingProducts
                                data={detailedData?.topSellingProducts?.data}
                                loading={loading.detailed && !detailedData}
                                currentPage={pagination.productsPage}
                                onPageChange={(page) => setProductsPage(page.selected)}
                                pagination={detailedData?.topSellingProducts?.pagination}
                            />

                            {/* Top Selling Category */}
                            <TopSellingCategories
                                data={detailedData?.topSellingCategories?.data}
                                loading={loading.detailed && !detailedData}
                                currentPage={pagination.categoriesPage}
                                onPageChange={(page) => setCategoriesPage(page.selected)}
                                pagination={detailedData?.topSellingCategories?.pagination}
                            />
                        </div>
                    </>
                );
        }
    };

    return (
        <div className="pt-4 min-h-screen">
            <div className="max-w-7xl mx-auto">
                {renderDashboardContent()}
            </div>
        </div>
    );
};

export default BizanaAdminDashboard;