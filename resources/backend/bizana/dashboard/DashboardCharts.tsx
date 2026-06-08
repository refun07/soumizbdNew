import React from 'react';
import GrowthComparisonChart from '../../js/bizana/components/charts/GrowthComparisonChart';
import ExpenseBreakdownChart from '../../js/bizana/components/charts/ExpenseBreakdownChart';

interface DashboardChartsProps {
    detailedData: any;
}

const DashboardCharts: React.FC<DashboardChartsProps> = ({ detailedData }) => {
    return (
        <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {/* Growth Comparison Chart - 2/3 width */}
            <div className="lg:col-span-2 card p-6 rounded-lg shadow-sm">
                <h3 className="text-lg font-semibold text-foreground mb-4">
                    Growth Comparison
                </h3>
                <GrowthComparisonChart
                    height={320}
                    primaryLabel="Current Period"
                    secondaryLabel="Previous Period"
                    data={detailedData?.growthComparison}
                />
            </div>

            {/* Expense Breakdown Chart - 1/3 width */}
            <div className="card p-6 rounded-lg shadow-sm">
                <h3 className="text-lg font-semibold text-foreground mb-4">
                    Expense Breakdown
                </h3>
                <ExpenseBreakdownChart
                    height={400}
                    donutMode={false}
                    data={detailedData?.expenseBreakdown}
                />
            </div>
        </div>
    );
};

export default DashboardCharts;