import { useState, useEffect, useCallback } from 'react';
import dashboardService from '../services/dashboardService';

interface DashboardState {
  quickData: any | null;
  detailedData: any | null;
  loading: {
    quick: boolean;
    detailed: boolean;
  };
  error: {
    quick: string | null;
    detailed: string | null;
  };
  dateRange: {
    from: string;
    to: string;
  };
  displayDateRange: {
    from: string;
    to: string;
  };
  pagination: {
    productsPage: number;
    categoriesPage: number;
  };
}

export const useDashboardData = () => {
  const [state, setState] = useState<DashboardState>({
    quickData: null,
    detailedData: null,
    loading: {
      quick: false,
      detailed: false,
    },
    error: {
      quick: null,
      detailed: null,
    },
    dateRange: dashboardService.getDateRange(),
    displayDateRange: dashboardService.getDisplayDateRange(),
    pagination: {
      productsPage: 1,
      categoriesPage: 1,
    },
  });

  const updateState = useCallback((updates: Partial<DashboardState>) => {
    setState(prev => ({ ...prev, ...updates }));
  }, []);

  const fetchQuickData = useCallback(async (forceRefresh = false) => {
    updateState({
      loading: { ...state.loading, quick: true },
      error: { ...state.error, quick: null },
    });

    try {
      const { cached, fresh } = await dashboardService.fetchQuickData(forceRefresh);

      // Show cached data immediately if available
      if (cached) {
        updateState({ quickData: cached });
      }

      // Fetch fresh data
      const freshData = await fresh;
      updateState({ quickData: freshData });
    } catch (error) {
      updateState({
        error: {
          ...state.error,
          quick: error instanceof Error ? error.message : 'Failed to fetch quick data',
        },
      });
    } finally {
      updateState({
        loading: { ...state.loading, quick: false },
      });
    }
  }, [state.loading, state.error, updateState]);

  const fetchDetailedData = useCallback(async (forceRefresh = false) => {
    updateState({
      loading: { ...state.loading, detailed: true },
      error: { ...state.error, detailed: null },
    });

    try {
      const { cached, fresh } = await dashboardService.fetchDetailedData(
        forceRefresh,
        state.pagination.productsPage,
        state.pagination.categoriesPage
      );

      // Show cached data immediately if available
      if (cached) {
        updateState({ detailedData: cached });
      }

      // Fetch fresh data
      const freshData = await fresh;
      updateState({ detailedData: freshData });
    } catch (error) {
      updateState({
        error: {
          ...state.error,
          detailed: error instanceof Error ? error.message : 'Failed to fetch detailed data',
        },
      });
    } finally {
      updateState({
        loading: { ...state.loading, detailed: false },
      });
    }
  }, [state.loading, state.error, updateState, state.pagination.productsPage, state.pagination.categoriesPage]);

  const setDateRange = useCallback((from: string, to: string) => {
    dashboardService.setDateRange(from, to);
    updateState({
      dateRange: { from, to },
      displayDateRange: dashboardService.getDisplayDateRange(),
    });

    // Refetch data with new date range
    fetchQuickData(true);
    fetchDetailedData(true);
  }, [fetchQuickData, fetchDetailedData]);

  const refreshAll = useCallback(() => {
    fetchQuickData(true);
    fetchDetailedData(true);
  }, [fetchQuickData, fetchDetailedData]);

  const setProductsPage = useCallback((page: number) => {
    updateState({
      pagination: { ...state.pagination, productsPage: page }
    });
  }, [state.pagination]);

  const setCategoriesPage = useCallback((page: number) => {
    updateState({
      pagination: { ...state.pagination, categoriesPage: page }
    });
  }, [state.pagination]);

  // Initialize data on component mount
  useEffect(() => {
    fetchQuickData();
    fetchDetailedData();
  }, []); // eslint-disable-line react-hooks/exhaustive-deps

  // Fetch detailed data when pagination changes
  useEffect(() => {
    // Only fetch on pagination changes after initial load
    if (state.pagination.productsPage > 1 || state.pagination.categoriesPage > 1) {
      dashboardService.clearCache();
      fetchDetailedData(true);
    }
  }, [state.pagination.productsPage, state.pagination.categoriesPage]); // eslint-disable-line react-hooks/exhaustive-deps

  return {
    ...state,
    fetchQuickData,
    fetchDetailedData,
    setDateRange,
    refreshAll,
    setProductsPage,
    setCategoriesPage,
  };
};