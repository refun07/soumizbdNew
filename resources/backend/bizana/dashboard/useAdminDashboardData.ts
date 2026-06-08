import { useState, useEffect } from 'react';

interface DashboardData {
    quickData: any;
    detailedData: any;
    loading: {
        quick: boolean;
        detailed: boolean;
    };
    error: {
        quick: string;
        detailed: string;
    };
    dateRange: {
        from: string;
        to: string;
    };
    setDateRange: React.Dispatch<React.SetStateAction<{
        from: string;
        to: string;
    }>>;
    pagination: {
        productsPage: number;
        categoriesPage: number;
    };
    setProductsPage: (page: { selected: number }) => void;
    setCategoriesPage: (page: { selected: number }) => void;
}

// Custom hook for admin dashboard data
export const useAdminDashboardData = (
    apiPrefix: string,
): DashboardData => {
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
        from: new Date(Date.now() - 30 * 24 * 60 * 60 * 1000)
            .toISOString()
            .split('T')[0], // 30 days ago
        to: new Date().toISOString().split('T')[0], // today
    });
    const [pagination, setPagination] = useState({
        productsPage: 1,
        categoriesPage: 1,
    });

    useEffect(() => {
        const fetchQuickData = async () => {
            setLoading((prev) => ({ ...prev, quick: true }));
            setError((prev) => ({ ...prev, quick: '' }));

            try {
                const response = await fetch(
                    `${apiPrefix}/dashboard/quick?from=${dateRange.from}&to=${dateRange.to}`,
                );
                if (!response.ok) throw new Error('Failed to fetch quick data');
                const data = await response.json();
                setQuickData(data);
            } catch (err) {
                setError((prev) => ({
                    ...prev,
                    quick: err instanceof Error ? err.message : 'Unknown error',
                }));
            } finally {
                setLoading((prev) => ({ ...prev, quick: false }));
            }
        };

        fetchQuickData();
    }, [apiPrefix, dateRange]);

    useEffect(() => {
        const fetchDetailedData = async () => {
            setLoading((prev) => ({ ...prev, detailed: true }));
            setError((prev) => ({ ...prev, detailed: '' }));

            try {
                const response = await fetch(
                    `${apiPrefix}/dashboard/detailed?from=${dateRange.from}&to=${dateRange.to}&products_page=${pagination.productsPage}&categories_page=${pagination.categoriesPage}`,
                );
                if (!response.ok)
                    throw new Error('Failed to fetch detailed data');
                const data = await response.json();
                setDetailedData(data);
            } catch (err) {
                setError((prev) => ({
                    ...prev,
                    detailed:
                        err instanceof Error ? err.message : 'Unknown error',
                }));
            } finally {
                setLoading((prev) => ({ ...prev, detailed: false }));
            }
        };

        fetchDetailedData();
    }, [
        apiPrefix,
        dateRange,
        pagination.productsPage,
        pagination.categoriesPage,
    ]);

    return {
        quickData,
        detailedData,
        loading,
        error,
        dateRange,
        setDateRange,
        pagination,
        setProductsPage: (page: { selected: number }) =>
            setPagination((prev) => ({ ...prev, productsPage: page.selected })),
        setCategoriesPage: (page: { selected: number }) =>
            setPagination((prev) => ({ ...prev, categoriesPage: page.selected })),
    };
};