import React, { useState, useEffect, useRef } from 'react';
import { createRoot } from 'react-dom/client';
import '../../css/app.css';
import { debounce } from '../../../frontend/utils/debounce';

interface Product {
    id: number;
    name: string;
    purchase_cost: number;
    packaging_cost: number;
    additional_costs: number;
    total_cost?: number;
}

interface PaginatedProducts {
    data: Product[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number;
    to: number;
}

interface FilterOption {
    id: number;
    name: string;
}

interface PurchaseOrderOption {
    id: number;
    reference_code: string;
    supplier_name: string;
}

interface PriceCalculatorState {
    products: Product[];
    paginatedProducts: PaginatedProducts | null;
    loading: boolean;
    error: string;
    filters: {
        search: string;
        per_page: number;
        category_id: string;
        brand_id: string;
        purchase_order_id: string;
    };
    filterOptions: {
        categories: FilterOption[];
        brands: FilterOption[];
        purchase_orders: PurchaseOrderOption[];
    };
    changedPackagingCosts: Set<number>;
    originalPackagingCosts: Map<number, number>;
    bulkPackagingCost: string;
    bulkPackagingCostChanged: boolean;
    changedAdditionalCosts: Set<number>;
    originalAdditionalCosts: Map<number, number>;
    bulkAdditionalCost: string;
    bulkAdditionalCostChanged: boolean;
}

interface PriceCalculatorProps {
    apiPrefix: string;
}

const PriceCalculator: React.FC<PriceCalculatorProps> = ({ apiPrefix }) => {
    const abortControllerRef = useRef<AbortController | null>(null);

    const [state, setState] = useState<PriceCalculatorState>({
        products: [],
        paginatedProducts: null,
        loading: false,
        error: '',
        filters: {
            search: '',
            per_page: 10,
            category_id: '',
            brand_id: '',
            purchase_order_id: '',
        },
        filterOptions: {
            categories: [],
            brands: [],
            purchase_orders: [],
        },
        changedPackagingCosts: new Set(),
        originalPackagingCosts: new Map(),
        bulkPackagingCost: '',
        bulkPackagingCostChanged: false,
        changedAdditionalCosts: new Set(),
        originalAdditionalCosts: new Map(),
        bulkAdditionalCost: '',
        bulkAdditionalCostChanged: false,
    });

    const fetchProducts = async (page: number = 1, searchQuery?: string) => {
        // Cancel any ongoing request
        if (abortControllerRef.current) {
            abortControllerRef.current.abort();
        }

        // Create new abort controller for this request
        const abortController = new AbortController();
        abortControllerRef.current = abortController;

        setState(prev => ({ ...prev, loading: true, error: '' }));

        try {
            const params = new URLSearchParams({
                search: searchQuery !== undefined ? searchQuery : state.filters.search,
                per_page: state.filters.per_page.toString(),
                category_id: state.filters.category_id,
                brand_id: state.filters.brand_id,
                purchase_order_id: state.filters.purchase_order_id,
                page: page.toString(),
            });

            const response = await fetch(`${apiPrefix}/price-calculator/products?${params}`, {
                signal: abortController.signal,
            });

            if (!response.ok) throw new Error('Failed to fetch products');

            const data = await response.json();

            // Transform product data to include total cost
            const products = data.data.map((product: Product) => ({
                ...product,
                total_cost: Number(
                    (product.purchase_cost || 0) +
                    (product.packaging_cost || 0) +
                    (product.additional_costs || 0)
                ),
            }));

            setState(prev => ({
                ...prev,
                products,
                paginatedProducts: data,
                loading: false,
            }));
        } catch (error: any) {
            // Don't show error for aborted requests
            if (error.name !== 'AbortError') {
                setState(prev => ({
                    ...prev,
                    error: error instanceof Error ? error.message : 'Unknown error',
                    loading: false,
                }));
            }
        }
    };

    const fetchFilterOptions = async () => {
        try {
            const response = await fetch(`${apiPrefix}/price-calculator/filter-options`);
            if (!response.ok) throw new Error('Failed to fetch filter options');

            const data = await response.json();

            setState(prev => ({
                ...prev,
                filterOptions: data,
            }));
        } catch (error) {
            console.error('Failed to fetch filter options:', error);
        }
    };

    // Create debounced search function with 800ms delay
    const debouncedFetchProducts = React.useMemo(
        () => debounce((searchQuery: string, page: number = 1) => {
            fetchProducts(page, searchQuery);
        }, 800),
        [apiPrefix, state.filters.per_page, state.filters.category_id, state.filters.brand_id, state.filters.purchase_order_id]
    );

    useEffect(() => {
        fetchProducts();
        fetchFilterOptions();
    }, []);

    useEffect(() => {
        // Only fetch immediately for non-search filter changes
        // Search changes are handled by the debounced handler
        if (state.filters.search !== '') {
            // If there's a search term, use the debounced function
            debouncedFetchProducts(state.filters.search, 1);
        } else {
            // If search is empty, fetch immediately to reset results
            fetchProducts(1);
        }
    }, [state.filters.category_id, state.filters.brand_id, state.filters.purchase_order_id, state.filters.per_page]);

    useEffect(() => {
        // Store original costs when products change
        const newOriginalPackagingCosts = new Map<number, number>();
        const newOriginalAdditionalCosts = new Map<number, number>();

        state.products.forEach((product) => {
            newOriginalPackagingCosts.set(product.id, product.packaging_cost);
            newOriginalAdditionalCosts.set(product.id, product.additional_costs);
        });

        setState(prev => ({
            ...prev,
            originalPackagingCosts: newOriginalPackagingCosts,
            originalAdditionalCosts: newOriginalAdditionalCosts,
        }));
    }, [state.products]);

    // Cleanup effect to cancel ongoing requests
    useEffect(() => {
        return () => {
            if (abortControllerRef.current) {
                abortControllerRef.current.abort();
            }
        };
    }, []);

    const updateProduct = (id: number, field: keyof Product, value: number) => {
        setState(prev => ({
            ...prev,
            products: prev.products.map((product) => {
                if (product.id === id) {
                    const updated = { ...product, [field]: value };
                    if (['packaging_cost', 'additional_costs'].includes(field)) {
                        updated.total_cost = Number(
                            (updated.purchase_cost || 0) +
                            (updated.packaging_cost || 0) +
                            (updated.additional_costs || 0)
                        );
                    }
                    return updated;
                }
                return product;
            }),
        }));
    };

    const handleFilterChange = (filterType: string, value: string) => {
        setState(prev => ({
            ...prev,
            filters: {
                ...prev.filters,
                [filterType === 'category' ? 'category_id' :
                 filterType === 'brand' ? 'brand_id' : 'purchase_order_id']: value,
            },
        }));
    };

    const handleSearchChange = (search: string) => {
        setState(prev => ({ ...prev, filters: { ...prev.filters, search } }));

        // Trigger debounced search
        debouncedFetchProducts(search, 1);
    };

    const handlePerPageChange = (perPage: number) => {
        setState(prev => ({ ...prev, filters: { ...prev.filters, per_page: perPage } }));
    };

    const handlePageChange = (page: number) => {
        fetchProducts(page);
    };

    const handlePackagingCostChange = (id: number, value: string) => {
        const packagingCost = parseFloat(value) || 0;
        const originalCost = state.originalPackagingCosts.get(id) || 0;

        updateProduct(id, 'packaging_cost', packagingCost);

        // Mark as changed if value is different from original
        if (packagingCost !== originalCost) {
            setState(prev => ({
                ...prev,
                changedPackagingCosts: new Set(prev.changedPackagingCosts).add(id),
            }));
        } else {
            setState(prev => {
                const newSet = new Set(prev.changedPackagingCosts);
                newSet.delete(id);
                return { ...prev, changedPackagingCosts: newSet };
            });
        }
    };

    const handleAdditionalCostChange = (id: number, value: string) => {
        const additionalCost = parseFloat(value) || 0;
        const originalCost = state.originalAdditionalCosts.get(id) || 0;

        updateProduct(id, 'additional_costs', additionalCost);

        // Mark as changed if value is different from original
        if (additionalCost !== originalCost) {
            setState(prev => ({
                ...prev,
                changedAdditionalCosts: new Set(prev.changedAdditionalCosts).add(id),
            }));
        } else {
            setState(prev => {
                const newSet = new Set(prev.changedAdditionalCosts);
                newSet.delete(id);
                return { ...prev, changedAdditionalCosts: newSet };
            });
        }
    };

    const savePackagingCost = async (id: number, packagingCost: number) => {
        try {
            const response = await fetch(`${apiPrefix}/products/${id}/packaging-cost`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    packaging_cost: packagingCost,
                }),
            });

            if (!response.ok) throw new Error('Failed to save packaging cost');

            await response.json();
            return true;
        } catch (error) {
            console.error('Error saving packaging cost:', error);
            return false;
        }
    };

    const handleSavePackagingCost = async (id: number) => {
        const product = state.products.find((p) => p.id === id);
        if (!product) return;

        const success = await savePackagingCost(id, product.packaging_cost);
        if (success) {
            // Update original cost and remove from changed set
            setState(prev => ({
                ...prev,
                originalPackagingCosts: new Map(prev.originalPackagingCosts).set(id, product.packaging_cost),
                changedPackagingCosts: (() => {
                    const newSet = new Set(prev.changedPackagingCosts);
                    newSet.delete(id);
                    return newSet;
                })(),
            }));
        }
    };

    const handleResetPackagingCost = (id: number) => {
        const originalCost = state.originalPackagingCosts.get(id) || 0;
        updateProduct(id, 'packaging_cost', originalCost);

        // Remove from changed set
        setState(prev => {
            const newSet = new Set(prev.changedPackagingCosts);
            newSet.delete(id);
            return { ...prev, changedPackagingCosts: newSet };
        });
    };

    const saveAdditionalCost = async (id: number, additionalCost: number) => {
        try {
            const response = await fetch(`${apiPrefix}/products/${id}/additional-cost`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    additional_costs: additionalCost,
                }),
            });

            if (!response.ok) throw new Error('Failed to save additional cost');

            await response.json();
            return true;
        } catch (error) {
            console.error('Error saving additional cost:', error);
            return false;
        }
    };

    const handleSaveAdditionalCost = async (id: number) => {
        const product = state.products.find((p) => p.id === id);
        if (!product) return;

        const success = await saveAdditionalCost(id, product.additional_costs);
        if (success) {
            // Update original cost and remove from changed set
            setState(prev => ({
                ...prev,
                originalAdditionalCosts: new Map(prev.originalAdditionalCosts).set(id, product.additional_costs),
                changedAdditionalCosts: (() => {
                    const newSet = new Set(prev.changedAdditionalCosts);
                    newSet.delete(id);
                    return newSet;
                })(),
            }));
        }
    };

    const handleResetAdditionalCost = (id: number) => {
        const originalCost = state.originalAdditionalCosts.get(id) || 0;
        updateProduct(id, 'additional_costs', originalCost);

        // Remove from changed set
        setState(prev => {
            const newSet = new Set(prev.changedAdditionalCosts);
            newSet.delete(id);
            return { ...prev, changedAdditionalCosts: newSet };
        });
    };

    const bulkUpdatePackagingCost = async (packagingCost: number) => {
        try {
            const productIds = state.products.map((product) => product.id);
            const response = await fetch(`${apiPrefix}/products/bulk-packaging-cost`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    product_ids: productIds,
                    packaging_cost: packagingCost,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to bulk update packaging costs');
            }

            const result = await response.json();
            return result.success;
        } catch (error) {
            console.error('Error bulk updating packaging costs:', error);
            return false;
        }
    };

    const handleBulkSavePackagingCost = async () => {
        if (!state.bulkPackagingCost) return;

        const packagingCost = parseFloat(state.bulkPackagingCost) || 0;
        const success = await bulkUpdatePackagingCost(packagingCost);

        if (success) {
            // Update all products locally
            setState(prev => ({
                ...prev,
                products: prev.products.map((product) => {
                    const updated = {
                        ...product,
                        packaging_cost: packagingCost,
                    };
                    updated.total_cost = Number(
                        (updated.purchase_cost || 0) +
                        (updated.packaging_cost || 0) +
                        (updated.additional_costs || 0)
                    );
                    return updated;
                }),
            }));

            // Update original costs
            const newOriginalCosts = new Map<number, number>();
            state.products.forEach((product) => {
                newOriginalCosts.set(product.id, packagingCost);
            });

            setState(prev => ({
                ...prev,
                originalPackagingCosts: newOriginalCosts,
                changedPackagingCosts: new Set(),
                bulkPackagingCost: '',
                bulkPackagingCostChanged: false,
            }));
        }
    };

    const bulkUpdateAdditionalCost = async (additionalCost: number) => {
        try {
            const productIds = state.products.map((product) => product.id);
            const response = await fetch(`${apiPrefix}/products/bulk-additional-cost`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    product_ids: productIds,
                    additional_costs: additionalCost,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to bulk update additional costs');
            }

            const result = await response.json();
            return result.success;
        } catch (error) {
            console.error('Error bulk updating additional costs:', error);
            return false;
        }
    };

    const handleBulkSaveAdditionalCost = async () => {
        if (!state.bulkAdditionalCost) return;

        const additionalCost = parseFloat(state.bulkAdditionalCost) || 0;
        const success = await bulkUpdateAdditionalCost(additionalCost);

        if (success) {
            // Update all products locally
            setState(prev => ({
                ...prev,
                products: prev.products.map((product) => {
                    const updated = {
                        ...product,
                        additional_costs: additionalCost,
                    };
                    updated.total_cost = Number(
                        (updated.purchase_cost || 0) +
                        (updated.packaging_cost || 0) +
                        (updated.additional_costs || 0)
                    );
                    return updated;
                }),
            }));

            // Update original costs
            const newOriginalCosts = new Map<number, number>();
            state.products.forEach((product) => {
                newOriginalCosts.set(product.id, additionalCost);
            });

            setState(prev => ({
                ...prev,
                originalAdditionalCosts: newOriginalCosts,
                changedAdditionalCosts: new Set(),
                bulkAdditionalCost: '',
                bulkAdditionalCostChanged: false,
            }));
        }
    };

    const getCookie = (name: string): string | null => {
        let cookieValue: string | null = null;
        if (document.cookie && document.cookie !== '') {
            const cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                const cookie = cookies[i].trim();
                if (cookie.substring(0, name.length + 1) === name + '=') {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    };

    if (state.loading && !state.paginatedProducts) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    return (
        <div className="">
            <div className="">
                {state.error && (
                    <div className="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p className="text-red-600 text-sm">{state.error}</p>
                    </div>
                )}

                <div className="card rounded-lg shadow">
                    <div className="p-6 border-b border-border">
                        <div className="flex justify-between items-center">
                            <div className="flex flex-wrap gap-4">
                                <div>
                                    <select
                                        value={state.filters.category_id}
                                        onChange={(e) => handleFilterChange('category', e.target.value)}
                                        className="px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-48"
                                    >
                                        <option value="">All Categories</option>
                                        {state.filterOptions.categories.map((category) => (
                                            <option key={category.id} value={category.id}>
                                                {category.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <select
                                        value={state.filters.brand_id}
                                        onChange={(e) => handleFilterChange('brand', e.target.value)}
                                        className="px-3 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-48"
                                    >
                                        <option value="">All Brands</option>
                                        {state.filterOptions.brands.map((brand) => (
                                            <option key={brand.id} value={brand.id}>
                                                {brand.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <select
                                        value={state.filters.purchase_order_id}
                                        onChange={(e) => handleFilterChange('purchase_order', e.target.value)}
                                        className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-56"
                                    >
                                        <option value="">All Purchase Orders</option>
                                        {state.filterOptions.purchase_orders.map((po) => (
                                            <option key={po.id} value={po.id}>
                                                {po.reference_code} - {po.supplier_name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                            <div className="relative">
                                <input
                                    type="text"
                                    placeholder="Search products..."
                                    value={state.filters.search}
                                    onChange={(e) => handleSearchChange(e.target.value)}
                                    className="pl-3 pr-10 py-2 border border-border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
                                />
                                <div className="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg
                                        className="w-4 h-4 text-muted"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                        />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-border">
                            <thead className="theme-table__head">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        Product Name
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        Purchase Cost (Tk)
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        <div className="flex flex-col items-center gap-2">
                                            <span>Packaging Cost (Tk)</span>
                                            <div className="flex items-center gap-1">
                                                <input
                                                    type="number"
                                                    value={state.bulkPackagingCost}
                                                    onChange={(e) => {
                                                        setState(prev => ({
                                                            ...prev,
                                                            bulkPackagingCost: e.target.value,
                                                            bulkPackagingCostChanged: e.target.value !== ''
                                                        }));
                                                    }}
                                                    onKeyPress={(e) => {
                                                        if (e.key === 'Enter') {
                                                            handleBulkSavePackagingCost();
                                                        }
                                                    }}
                                                    placeholder="Bulk cost"
                                                    className={`w-24 text-xs text-foreground border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                                        state.bulkPackagingCostChanged
                                                            ? 'border-blue-400 bg-muted/50'
                                                            : 'border-border'
                                                    }`}
                                                    step="0.01"
                                                    min="0"
                                                />
                                                {state.bulkPackagingCostChanged && (
                                                    <div className="flex gap-1">
                                                        <button
                                                            onClick={handleBulkSavePackagingCost}
                                                            className="p-1 text-green-600 hover:bg-green-100 rounded-full transition-colors"
                                                            title="Apply to all products"
                                                        >
                                                            <svg
                                                                className="w-3 h-3"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    strokeWidth={2}
                                                                    d="M5 13l4 4L19 7"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            onClick={() => {
                                                                setState(prev => ({
                                                                    ...prev,
                                                                    bulkPackagingCost: '',
                                                                    bulkPackagingCostChanged: false
                                                                }));
                                                            }}
                                                            className="p-1 text-red-600 hover:bg-red-100 rounded-full transition-colors"
                                                            title="Clear bulk value"
                                                        >
                                                            <svg
                                                                className="w-3 h-3"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    strokeWidth={2}
                                                                    d="M6 18L18 6M6 6l12 12"
                                                                />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        <div className="flex flex-col items-center gap-2">
                                            <span>Additional Costs (Tk)</span>
                                            <div className="flex items-center gap-1">
                                                <input
                                                    type="number"
                                                    value={state.bulkAdditionalCost}
                                                    onChange={(e) => {
                                                        setState(prev => ({
                                                            ...prev,
                                                            bulkAdditionalCost: e.target.value,
                                                            bulkAdditionalCostChanged: e.target.value !== ''
                                                        }));
                                                    }}
                                                    onKeyPress={(e) => {
                                                        if (e.key === 'Enter') {
                                                            handleBulkSaveAdditionalCost();
                                                        }
                                                    }}
                                                    placeholder="Bulk cost"
                                                    className={`w-24 text-xs text-foreground border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                                        state.bulkAdditionalCostChanged
                                                            ? 'border-blue-400 bg-muted/50'
                                                            : 'border-border'
                                                    }`}
                                                    step="0.01"
                                                    min="0"
                                                />
                                                {state.bulkAdditionalCostChanged && (
                                                    <div className="flex gap-1">
                                                        <button
                                                            onClick={handleBulkSaveAdditionalCost}
                                                            className="p-1 text-green-600 hover:bg-green-100 rounded-full transition-colors"
                                                            title="Apply to all products"
                                                        >
                                                            <svg
                                                                className="w-3 h-3"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    strokeWidth={2}
                                                                    d="M5 13l4 4L19 7"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            onClick={() => {
                                                                setState(prev => ({
                                                                    ...prev,
                                                                    bulkAdditionalCost: '',
                                                                    bulkAdditionalCostChanged: false
                                                                }));
                                                            }}
                                                            className="p-1 text-red-600 hover:bg-red-100 rounded-full transition-colors"
                                                            title="Clear bulk value"
                                                        >
                                                            <svg
                                                                className="w-3 h-3"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    strokeWidth={2}
                                                                    d="M6 18L18 6M6 6l12 12"
                                                                />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-muted uppercase tracking-wider">
                                        Total Cost (Tk)
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="theme-table__body divide-y divide-border">
                                {state.products.map((product) => (
                                    <tr key={product.id} className="theme-table__row">
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="text-sm font-medium text-foreground">
                                                {product.name}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="text-sm font-medium text-muted">
                                                ৳{(product.purchase_cost || 0).toFixed(2)}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex items-center gap-1">
                                                <input
                                                    type="number"
                                                    value={product.packaging_cost}
                                                    onChange={(e) => handlePackagingCostChange(product.id, e.target.value)}
                                                    onKeyPress={(e) => {
                                                        if (e.key === 'Enter') {
                                                            handleSavePackagingCost(product.id);
                                                        }
                                                    }}
                                                    className={`w-20 text-sm text-foreground border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                                        state.changedPackagingCosts.has(product.id)
                                                            ? 'border-blue-400 bg-muted/50'
                                                            : 'border-border'
                                                    }`}
                                                    step="0.01"
                                                    min="0"
                                                />
                                                {state.changedPackagingCosts.has(product.id) && (
                                                    <div className="flex gap-1">
                                                        <button
                                                            onClick={() => handleSavePackagingCost(product.id)}
                                                            className="p-1 text-green-600 hover:bg-green-100 rounded-full transition-colors"
                                                            title="Save packaging cost"
                                                        >
                                                            <svg
                                                                className="w-4 h-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    strokeWidth={2}
                                                                    d="M5 13l4 4L19 7"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            onClick={() => handleResetPackagingCost(product.id)}
                                                            className="p-1 text-red-600 hover:bg-red-100 rounded-full transition-colors"
                                                            title="Reset to original value"
                                                        >
                                                            <svg
                                                                className="w-4 h-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    strokeWidth={2}
                                                                    d="M6 18L18 6M6 6l12 12"
                                                                />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                )}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex items-center gap-1">
                                                <input
                                                    type="number"
                                                    value={product.additional_costs}
                                                    onChange={(e) => handleAdditionalCostChange(product.id, e.target.value)}
                                                    onKeyPress={(e) => {
                                                        if (e.key === 'Enter') {
                                                            handleSaveAdditionalCost(product.id);
                                                        }
                                                    }}
                                                    className={`w-20 text-sm text-foreground border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                                        state.changedAdditionalCosts.has(product.id)
                                                            ? 'border-blue-400 bg-muted/50'
                                                            : 'border-border'
                                                    }`}
                                                    step="0.01"
                                                    min="0"
                                                />
                                                {state.changedAdditionalCosts.has(product.id) && (
                                                    <div className="flex gap-1">
                                                        <button
                                                            onClick={() => handleSaveAdditionalCost(product.id)}
                                                            className="p-1 text-green-600 hover:bg-green-100 rounded-full transition-colors"
                                                            title="Save additional cost"
                                                        >
                                                            <svg
                                                                className="w-4 h-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    strokeWidth={2}
                                                                    d="M5 13l4 4L19 7"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            onClick={() => handleResetAdditionalCost(product.id)}
                                                            className="p-1 text-red-600 hover:bg-red-100 rounded-full transition-colors"
                                                            title="Reset to original value"
                                                        >
                                                            <svg
                                                                className="w-4 h-4"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >
                                                                <path
                                                                    strokeLinecap="round"
                                                                    strokeLinejoin="round"
                                                                    strokeWidth={2}
                                                                    d="M6 18L18 6M6 6l12 12"
                                                                />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                )}
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="text-sm font-semibold text-purple-600">
                                                ৳{Number(product.total_cost || 0).toFixed(2)}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {state.paginatedProducts && (
                        <div className="p-6 border-t border-border bg-muted/10">
                            <div className="flex justify-end items-center gap-6">
                                <div className="flex items-center gap-4 text-sm text-muted">
                                    <span>
                                        Showing {state.paginatedProducts.from || 0} to{' '}
                                        {state.paginatedProducts.to || 0} of{' '}
                                        {state.paginatedProducts.total} products
                                    </span>
                                    <div>
                                        <select
                                            value={state.filters.per_page}
                                            onChange={(e) => handlePerPageChange(Number(e.target.value))}
                                            className="px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        >
                                            <option value={10}>10 per page</option>
                                            <option value={25}>25 per page</option>
                                            <option value={50}>50 per page</option>
                                            <option value={100}>100 per page</option>
                                        </select>
                                    </div>
                                </div>

                                {state.paginatedProducts.last_page > 1 && (
                                    <div className="flex items-center gap-2">
                                        <button
                                            onClick={() => handlePageChange(state.paginatedProducts!.current_page - 1)}
                                            disabled={state.paginatedProducts.current_page <= 1}
                                            className="px-3 py-1 text-sm card border border-border rounded hover:bg-muted/50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            Previous
                                        </button>

                                        <div className="flex items-center gap-1">
                                            {Array.from({ length: Math.min(5, state.paginatedProducts!.last_page) }, (_, i) => {
                                                let page;
                                                if (state.paginatedProducts!.last_page <= 5) {
                                                    page = i + 1;
                                                } else if (state.paginatedProducts!.current_page <= 3) {
                                                    page = i + 1;
                                                } else if (state.paginatedProducts!.current_page >= state.paginatedProducts!.last_page - 2) {
                                                    page = state.paginatedProducts!.last_page - 4 + i;
                                                } else {
                                                    page = state.paginatedProducts!.current_page - 2 + i;
                                                }

                                                return (
                                                    <button
                                                        key={page}
                                                        onClick={() => handlePageChange(page)}
                                                        className={`px-3 py-1 text-sm border rounded ${
                                                            page === state.paginatedProducts!.current_page
                                                                ? 'bg-blue-600 text-white border-blue-600'
                                                                : 'card border-border hover:bg-muted/50'
                                                        }`}
                                                    >
                                                        {page}
                                                    </button>
                                                );
                                            })}
                                        </div>

                                        <button
                                            onClick={() => handlePageChange(state.paginatedProducts!.current_page + 1)}
                                            disabled={state.paginatedProducts.current_page >= state.paginatedProducts.last_page}
                                            className="px-3 py-1 text-sm card border border-border rounded hover:bg-muted/50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            Next
                                        </button>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

const PriceCalculatorApp: React.FC = () => {
    // Get config from window object provided by Blade template
    const bizanaConfig = (window as any).bizanaConfig || {
        apiPrefix: '/admin/api/bizana',
    };

    return (
        <div className="bizana-price-calculator">
            <PriceCalculator apiPrefix={bizanaConfig.apiPrefix} />
        </div>
    );
};

// Mount the app
const container = document.getElementById('bizana-app');
if (container) {
    const root = createRoot(container);
    root.render(<PriceCalculatorApp />);
} else {
    console.error('Bizana price calculator app container not found');
}

export default PriceCalculatorApp;