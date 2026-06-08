import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import '../../css/app.css';

// Import custom hook
import { useAdminDashboardData } from './useAdminDashboardData';

// Import existing components
import DateRangePicker from '../../js/bizana/components/DateRangePicker';

// Import extracted dashboard components
import MetricCards from './MetricCards';
import StatusCards from './StatusCards';
import DashboardCharts from './DashboardCharts';
import DashboardTables from './DashboardTables';

interface BizanaAdminDashboardProps {
    apiPrefix: string;
}

interface BizanaAdminAppProps {
    children?: React.ReactNode;
}

const BizanaAdminDashboard: React.FC<BizanaAdminDashboardProps> = ({
    apiPrefix,
}) => {
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
    } = useAdminDashboardData(apiPrefix);

    return (
        <div className="pt-4 min-h-screen">
            <div className="">
                {/* Main Dashboard Content */}
                <div className="card p-4 rounded-md">
                    {/* Title and Date Range */}
                    <div className="flex justify-between items-center">
                        <DateRangePicker
                            startDate={dateRange.from}
                            endDate={dateRange.to}
                            onDateChange={(startDate, endDate) => setDateRange({ from: startDate, to: endDate })}
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
                </div>

                {/* Extracted Components */}
                <MetricCards quickData={quickData} detailedData={detailedData} loading={loading} />
                <DashboardCharts detailedData={detailedData} />
                <StatusCards quickData={quickData} loading={loading} />
                <DashboardTables
                    detailedData={detailedData}
                    loading={loading}
                    pagination={pagination}
                    setProductsPage={setProductsPage}
                    setCategoriesPage={setCategoriesPage}
                />
            </div>
        </div>
    );
};

const BizanaAdminApp: React.FC<BizanaAdminAppProps> = () => {
    // Get config from window object provided by Blade template
    const bizanaConfig = (window as any).bizanaConfig || {
        adminMode: true,
        apiPrefix: '/admin/api/bizana',
    };

    return (
        <div className="bizana-admin-dashboard">
            <BizanaAdminDashboard apiPrefix={bizanaConfig.apiPrefix} />
        </div>
    );
};

// Mount the app
const container = document.getElementById('bizana-app');
if (container) {
    const root = createRoot(container);
    root.render(<BizanaAdminApp />);
} else {
    console.error('Bizana admin app container not found');
}

export default BizanaAdminApp;
