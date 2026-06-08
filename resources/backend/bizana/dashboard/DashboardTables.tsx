import React from 'react';
import TopSellingProducts from '../../js/bizana/components/tables/TopSellingProducts';
import TopSellingCategories from '../../js/bizana/components/tables/TopSellingCategories';

interface DashboardTablesProps {
    detailedData: any;
    loading: {
        detailed: boolean;
    };
    pagination: {
        productsPage: number;
        categoriesPage: number;
    };
    setProductsPage: (page: { selected: number }) => void;
    setCategoriesPage: (page: { selected: number }) => void;
}

const DashboardTables: React.FC<DashboardTablesProps> = ({
    detailedData,
    loading,
    pagination,
    setProductsPage,
    setCategoriesPage,
}) => {
    return (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {/* Top Selling Products */}
            <TopSellingProducts
                data={detailedData?.topSellingProducts?.data}
                loading={loading.detailed && !detailedData}
                currentPage={pagination.productsPage}
                onPageChange={setProductsPage}
                pagination={detailedData?.topSellingProducts?.pagination}
            />

            {/* Top Selling Category */}
            <TopSellingCategories
                data={detailedData?.topSellingCategories?.data}
                loading={loading.detailed && !detailedData}
                currentPage={pagination.categoriesPage}
                onPageChange={setCategoriesPage}
                pagination={detailedData?.topSellingCategories?.pagination}
            />
        </div>
    );
};

export default DashboardTables;