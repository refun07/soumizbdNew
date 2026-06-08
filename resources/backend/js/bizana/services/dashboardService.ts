import axios from 'axios';

const API_BASE_URL = '/admin/api/bizana/dashboard';

interface DashboardData {
  quick?: {
    summary: {
      totalOrders: number;
      totalRevenue: number;
      grossProfit: number;
      expenses: number;
      deliveryCharges: number;
      other: number;
    };
    orderStats: {
      placed: number;
      successful: number;
      returned: number;
      damaged: number;
      cancelled: number;
    };
    netProfit: {
      amount: number;
      growth: number;
    };
  };
  detailed?: {
    growthComparison: {
      current: Array<{ date: string; orders: number; revenue: number }>;
      previous: Array<{ date: string; orders: number; revenue: number }>;
    };
    expenseBreakdown: {
      categories: string[];
      values: number[];
    };
    topSellingProducts: Array<{
      id: number;
      name: string;
      quantity: number;
      revenue: number;
    }>;
    topSellingCategories: Array<{
      category: string;
      productCount: number;
      quantity: number;
      revenue: number;
    }>;
    revenueOverTime: Array<{ date: string; revenue: number }>;
  };
}

class DashboardService {
  private readonly STORAGE_KEYS = {
    QUICK_DATA: 'bizana_dashboard_quick_data',
    DETAILED_DATA: 'bizana_dashboard_detailed_data',
    LAST_FETCH: 'bizana_dashboard_last_fetch',
    DATE_RANGE: 'bizana_dashboard_date_range'
  };

  private readonly CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

  /**
   * Get date range from local storage or use default
   */
  getDateRange(): { from: string; to: string } {
    const stored = localStorage.getItem(this.STORAGE_KEYS.DATE_RANGE);
    if (stored) {
      try {
        return JSON.parse(stored);
      } catch {
        // If parsing fails, use default
      }
    }

    const now = new Date();
    const thirtyDaysAgo = new Date(now.getTime() - 30 * 24 * 60 * 60 * 1000);

    const defaultRange = {
      from: thirtyDaysAgo.toISOString().split('T')[0],
      to: now.toISOString().split('T')[0]
    };

    localStorage.setItem(this.STORAGE_KEYS.DATE_RANGE, JSON.stringify(defaultRange));
    return defaultRange;
  }

  /**
   * Set date range and clear cache
   */
  setDateRange(from: string, to: string): void {
    const range = { from, to };
    localStorage.setItem(this.STORAGE_KEYS.DATE_RANGE, JSON.stringify(range));
    this.clearCache();
  }

  /**
   * Update date range with validation
   */
  updateDateRange(from: string, to: string): { success: boolean; error?: string } {
    try {
      // Validate date format
      const fromDate = new Date(from);
      const toDate = new Date(to);

      if (isNaN(fromDate.getTime()) || isNaN(toDate.getTime())) {
        return { success: false, error: 'Invalid date format' };
      }

      if (fromDate > toDate) {
        return { success: false, error: 'Start date cannot be after end date' };
      }

      this.setDateRange(from, to);
      return { success: true };
    } catch (error) {
      return { success: false, error: 'Failed to update date range' };
    }
  }

  /**
   * Check if cache is valid
   */
  private isCacheValid(lastFetch: number): boolean {
    return Date.now() - lastFetch < this.CACHE_DURATION;
  }

  /**
   * Get cached quick data
   */
  private getCachedQuickData(): DashboardData['quick'] | null {
    try {
      const cached = localStorage.getItem(this.STORAGE_KEYS.QUICK_DATA);
      const lastFetch = parseInt(localStorage.getItem(this.STORAGE_KEYS.LAST_FETCH) || '0');

      if (cached && this.isCacheValid(lastFetch)) {
        return JSON.parse(cached);
      }
    } catch {
      // If there's any error with cache, return null
    }
    return null;
  }

  /**
   * Get cached detailed data
   */
  private getCachedDetailedData(): DashboardData['detailed'] | null {
    try {
      const cached = localStorage.getItem(this.STORAGE_KEYS.DETAILED_DATA);
      const lastFetch = parseInt(localStorage.getItem(this.STORAGE_KEYS.LAST_FETCH) || '0');

      if (cached && this.isCacheValid(lastFetch)) {
        return JSON.parse(cached);
      }
    } catch {
      // If there's any error with cache, return null
    }
    return null;
  }

  /**
   * Cache data with timestamp
   */
  private cacheData(type: 'quick' | 'detailed', data: any): void {
    try {
      const storageKey = type === 'quick' ? this.STORAGE_KEYS.QUICK_DATA : this.STORAGE_KEYS.DETAILED_DATA;
      localStorage.setItem(storageKey, JSON.stringify(data));
      localStorage.setItem(this.STORAGE_KEYS.LAST_FETCH, Date.now().toString());
    } catch {
      // If localStorage is full, clear it and try again
      this.clearCache();
      try {
        const storageKey = type === 'quick' ? this.STORAGE_KEYS.QUICK_DATA : this.STORAGE_KEYS.DETAILED_DATA;
        localStorage.setItem(storageKey, JSON.stringify(data));
        localStorage.setItem(this.STORAGE_KEYS.LAST_FETCH, Date.now().toString());
      } catch {
        // If it still fails, just skip caching
      }
    }
  }

  /**
   * Clear all cached data
   */
  clearCache(): void {
    Object.values(this.STORAGE_KEYS).forEach(key => {
      localStorage.removeItem(key);
    });
  }

  /**
   * Fetch quick dashboard data
   * Returns cached data immediately if available, then fetches fresh data
   */
  async fetchQuickData(forceRefresh = false): Promise<{
    cached: DashboardData['quick'] | null;
    fresh: Promise<DashboardData['quick']>;
  }> {
    const cached = forceRefresh ? null : this.getCachedQuickData();

    const fresh = new Promise<DashboardData['quick']>(async (resolve, reject) => {
      try {
        const dateRange = this.getDateRange();
        const response = await axios.get(`${API_BASE_URL}/quick`, {
          params: {
            date_from: dateRange.from,
            date_to: dateRange.to
          }
        });

        this.cacheData('quick', response.data);
        resolve(response.data);
      } catch (error) {
        reject(error);
      }
    });

    return { cached, fresh };
  }

  /**
   * Fetch detailed dashboard data
   * Returns cached data immediately if available, then fetches fresh data
   */
  async fetchDetailedData(
    forceRefresh = false,
    productsPage = 1,
    categoriesPage = 1
  ): Promise<{
    cached: DashboardData['detailed'] | null;
    fresh: Promise<DashboardData['detailed']>;
  }> {
    const cached = forceRefresh ? null : this.getCachedDetailedData();

    const fresh = new Promise<DashboardData['detailed']>(async (resolve, reject) => {
      try {
        const dateRange = this.getDateRange();

        const apiParams = {
          date_from: dateRange.from,
          date_to: dateRange.to,
          products_limit: 6,
          categories_limit: 6,
          products_page: productsPage,
          categories_page: categoriesPage
        };

        const response = await axios.get(`${API_BASE_URL}/detailed`, {
          params: apiParams
        });
        this.cacheData('detailed', response.data);
        resolve(response.data);
      } catch (error) {
        console.error('API error:', error);
        reject(error);
      }
    });

    return { cached, fresh };
  }

  /**
   * Get date range for display
   */
  getDisplayDateRange(): { from: string; to: string } {
    const range = this.getDateRange();

    return {
      from: new Date(range.from).toLocaleDateString('en-US', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      }),
      to: new Date(range.to).toLocaleDateString('en-US', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      })
    };
  }
}

export default new DashboardService();