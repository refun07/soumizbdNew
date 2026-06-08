import { useState, useCallback, useEffect } from 'react';
import { usePage, router } from '@inertiajs/react';
import BizanaLayout from '../../layouts/BizanaLayout';

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

interface PageProps {
    products: PaginatedProducts;
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
}

function PriceCalculator() {
    const {
        products: paginatedProducts,
        filters,
        filterOptions,
    } = usePage<PageProps>().props;
    const [products, setProducts] = useState<Product[]>(paginatedProducts.data);
    const [search, setSearch] = useState(filters.search);
    const [perPage, setPerPage] = useState(filters.per_page);
    const [categoryId, setCategoryId] = useState(filters.category_id);
    const [brandId, setBrandId] = useState(filters.brand_id);
    const [purchaseOrderId, setPurchaseOrderId] = useState(
        filters.purchase_order_id,
    );
    const [changedPackagingCosts, setChangedPackagingCosts] = useState<
        Set<number>
    >(new Set());
    const [originalPackagingCosts, setOriginalPackagingCosts] = useState<
        Map<number, number>
    >(new Map());
    const [bulkPackagingCost, setBulkPackagingCost] = useState<string>('');
    const [bulkPackagingCostChanged, setBulkPackagingCostChanged] =
        useState<boolean>(false);
    const [changedAdditionalCosts, setChangedAdditionalCosts] = useState<
        Set<number>
    >(new Set());
    const [originalAdditionalCosts, setOriginalAdditionalCosts] = useState<
        Map<number, number>
    >(new Map());
    const [bulkAdditionalCost, setBulkAdditionalCost] = useState<string>('');
    const [bulkAdditionalCostChanged, setBulkAdditionalCostChanged] =
        useState<boolean>(false);

    const updateProduct = (id: number, field: keyof Product, value: number) => {
        setProducts((prev) =>
            prev.map((product) => {
                if (product.id === id) {
                    const updated = { ...product, [field]: value };
                    if (
                        ['packaging_cost', 'additional_costs'].includes(field)
                    ) {
                        updated.total_cost = Number(
                            (updated.purchase_cost || 0) +
                                (updated.packaging_cost || 0) +
                                (updated.additional_costs || 0),
                        );
                    }
                    return updated;
                }
                return product;
            }),
        );
    };

    useEffect(() => {
        const timeoutId = setTimeout(() => {
            if (
                search !== filters.search ||
                categoryId !== filters.category_id ||
                brandId !== filters.brand_id ||
                purchaseOrderId !== filters.purchase_order_id
            ) {
                router.get(
                    '/admin/bizana/products/price-calculator',
                    {
                        search,
                        per_page: perPage,
                        category_id: categoryId,
                        brand_id: brandId,
                        purchase_order_id: purchaseOrderId,
                    },
                    {
                        preserveState: true,
                        replace: true,
                    },
                );
            }
        }, 500);

        return () => clearTimeout(timeoutId);
    }, [
        search,
        perPage,
        categoryId,
        brandId,
        purchaseOrderId,
        filters.search,
        filters.category_id,
        filters.brand_id,
        filters.purchase_order_id,
    ]);

    useEffect(() => {
        setProducts(
            paginatedProducts.data.map((product) => ({
                ...product,
                total_cost: Number(
                    (product.purchase_cost || 0) +
                        (product.packaging_cost || 0) +
                        (product.additional_costs || 0),
                ),
            })),
        );

        // Store original packaging costs
        const newOriginalCosts = new Map<number, number>();
        paginatedProducts.data.forEach((product) => {
            newOriginalCosts.set(product.id, product.packaging_cost);
        });
        setOriginalPackagingCosts(newOriginalCosts);

        // Store original additional costs
        const newOriginalAdditionalCosts = new Map<number, number>();
        paginatedProducts.data.forEach((product) => {
            newOriginalAdditionalCosts.set(
                product.id,
                product.additional_costs,
            );
        });
        setOriginalAdditionalCosts(newOriginalAdditionalCosts);
    }, [paginatedProducts.data]);

    const handlePerPageChange = (value: number) => {
        setPerPage(value);
        router.get(
            '/admin/bizana/products/price-calculator',
            {
                search,
                per_page: value,
                category_id: categoryId,
                brand_id: brandId,
                purchase_order_id: purchaseOrderId,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const handlePageChange = (page: number) => {
        router.get(
            '/admin/bizana/products/price-calculator',
            {
                search,
                per_page: perPage,
                category_id: categoryId,
                brand_id: brandId,
                purchase_order_id: purchaseOrderId,
                page,
            },
            {
                preserveState: true,
                replace: true,
            },
        );
    };

    const handleFilterChange = (filterType: string, value: string) => {
        switch (filterType) {
            case 'category':
                setCategoryId(value);
                break;
            case 'brand':
                setBrandId(value);
                break;
            case 'purchase_order':
                setPurchaseOrderId(value);
                break;
        }
    };

    const savePackagingCost = async (id: number, packagingCost: number) => {
        try {
            const response = await fetch(
                `/admin/api/bizana/products/${id}/packaging-cost`,
                {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                    },
                    body: JSON.stringify({
                        packaging_cost: packagingCost,
                    }),
                },
            );

            if (!response.ok) {
                throw new Error('Failed to save packaging cost');
            }

            await response.json();
            return true;
        } catch (error) {
            console.error('Error saving packaging cost:', error);
            return false;
        }
    };

    const bulkUpdatePackagingCost = async (packagingCost: number) => {
        try {
            const productIds = products.map((product) => product.id);
            const response = await fetch(
                '/admin/api/bizana/products/bulk-packaging-cost',
                {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                    },
                    body: JSON.stringify({
                        product_ids: productIds,
                        packaging_cost: packagingCost,
                    }),
                },
            );

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

    const saveAdditionalCost = async (id: number, additionalCost: number) => {
        try {
            const response = await fetch(
                `/admin/api/bizana/products/${id}/additional-cost`,
                {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                    },
                    body: JSON.stringify({
                        additional_costs: additionalCost,
                    }),
                },
            );

            if (!response.ok) {
                throw new Error('Failed to save additional cost');
            }

            await response.json();
            return true;
        } catch (error) {
            console.error('Error saving additional cost:', error);
            return false;
        }
    };

    const bulkUpdateAdditionalCost = async (additionalCost: number) => {
        try {
            const productIds = products.map((product) => product.id);
            const response = await fetch(
                '/admin/api/bizana/products/bulk-additional-cost',
                {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-XSRF-TOKEN': getCookie('XSRF-TOKEN'),
                    },
                    body: JSON.stringify({
                        product_ids: productIds,
                        additional_costs: additionalCost,
                    }),
                },
            );

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

    const getCookie = (name: string) => {
        let cookieValue = null;
        if (document.cookie && document.cookie !== '') {
            const cookies = document.cookie.split(';');
            for (let i = 0; i < cookies.length; i++) {
                const cookie = cookies[i].trim();
                if (cookie.substring(0, name.length + 1) === name + '=') {
                    cookieValue = decodeURIComponent(
                        cookie.substring(name.length + 1),
                    );
                    break;
                }
            }
        }
        return cookieValue;
    };

    const handlePackagingCostChange = (id: number, value: string) => {
        const packagingCost = parseFloat(value) || 0;
        const originalCost = originalPackagingCosts.get(id) || 0;

        updateProduct(id, 'packaging_cost', packagingCost);

        // Mark as changed if value is different from original
        if (packagingCost !== originalCost) {
            setChangedPackagingCosts((prev) => new Set(prev).add(id));
        } else {
            setChangedPackagingCosts((prev) => {
                const newSet = new Set(prev);
                newSet.delete(id);
                return newSet;
            });
        }
    };

    const handleSavePackagingCost = async (id: number) => {
        const product = products.find((p) => p.id === id);
        if (!product) return;

        const success = await savePackagingCost(id, product.packaging_cost);
        if (success) {
            // Update original cost and remove from changed set
            setOriginalPackagingCosts((prev) =>
                new Map(prev).set(id, product.packaging_cost),
            );
            setChangedPackagingCosts((prev) => {
                const newSet = new Set(prev);
                newSet.delete(id);
                return newSet;
            });
        }
    };

    const handleResetPackagingCost = (id: number) => {
        const originalCost = originalPackagingCosts.get(id) || 0;
        updateProduct(id, 'packaging_cost', originalCost);

        // Remove from changed set
        setChangedPackagingCosts((prev) => {
            const newSet = new Set(prev);
            newSet.delete(id);
            return newSet;
        });
    };

    const handlePackagingCostKeyPress = (
        e: React.KeyboardEvent,
        id: number,
    ) => {
        if (e.key === 'Enter') {
            handleSavePackagingCost(id);
        }
    };

    const handleBulkPackagingCostChange = (value: string) => {
        setBulkPackagingCost(value);
        setBulkPackagingCostChanged(value !== '');
    };

    const handleBulkSavePackagingCost = async () => {
        if (!bulkPackagingCost) return;

        const packagingCost = parseFloat(bulkPackagingCost) || 0;
        const success = await bulkUpdatePackagingCost(packagingCost);

        if (success) {
            // Update all products locally
            setProducts((prev) =>
                prev.map((product) => {
                    const updated = {
                        ...product,
                        packaging_cost: packagingCost,
                    };
                    updated.total_cost = Number(
                        (updated.purchase_cost || 0) +
                            (updated.packaging_cost || 0) +
                            (updated.additional_costs || 0),
                    );
                    return updated;
                }),
            );

            // Update original costs
            const newOriginalCosts = new Map<number, number>();
            products.forEach((product) => {
                newOriginalCosts.set(product.id, packagingCost);
            });
            setOriginalPackagingCosts(newOriginalCosts);

            // Clear changed individual costs and bulk input
            setChangedPackagingCosts(new Set());
            setBulkPackagingCost('');
            setBulkPackagingCostChanged(false);
        }
    };

    const handleBulkResetPackagingCost = () => {
        setBulkPackagingCost('');
        setBulkPackagingCostChanged(false);
    };

    const handleBulkPackagingCostKeyPress = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            handleBulkSavePackagingCost();
        }
    };

    const handleAdditionalCostChange = (id: number, value: string) => {
        const additionalCost = parseFloat(value) || 0;
        const originalCost = originalAdditionalCosts.get(id) || 0;

        updateProduct(id, 'additional_costs', additionalCost);

        // Mark as changed if value is different from original
        if (additionalCost !== originalCost) {
            setChangedAdditionalCosts((prev) => new Set(prev).add(id));
        } else {
            setChangedAdditionalCosts((prev) => {
                const newSet = new Set(prev);
                newSet.delete(id);
                return newSet;
            });
        }
    };

    const handleSaveAdditionalCost = async (id: number) => {
        const product = products.find((p) => p.id === id);
        if (!product) return;

        const success = await saveAdditionalCost(id, product.additional_costs);
        if (success) {
            // Update original cost and remove from changed set
            setOriginalAdditionalCosts((prev) =>
                new Map(prev).set(id, product.additional_costs),
            );
            setChangedAdditionalCosts((prev) => {
                const newSet = new Set(prev);
                newSet.delete(id);
                return newSet;
            });
        }
    };

    const handleResetAdditionalCost = (id: number) => {
        const originalCost = originalAdditionalCosts.get(id) || 0;
        updateProduct(id, 'additional_costs', originalCost);

        // Remove from changed set
        setChangedAdditionalCosts((prev) => {
            const newSet = new Set(prev);
            newSet.delete(id);
            return newSet;
        });
    };

    const handleAdditionalCostKeyPress = (
        e: React.KeyboardEvent,
        id: number,
    ) => {
        if (e.key === 'Enter') {
            handleSaveAdditionalCost(id);
        }
    };

    const handleBulkAdditionalCostChange = (value: string) => {
        setBulkAdditionalCost(value);
        setBulkAdditionalCostChanged(value !== '');
    };

    const handleBulkSaveAdditionalCost = async () => {
        if (!bulkAdditionalCost) return;

        const additionalCost = parseFloat(bulkAdditionalCost) || 0;
        const success = await bulkUpdateAdditionalCost(additionalCost);

        if (success) {
            // Update all products locally
            setProducts((prev) =>
                prev.map((product) => {
                    const updated = {
                        ...product,
                        additional_costs: additionalCost,
                    };
                    updated.total_cost = Number(
                        (updated.purchase_cost || 0) +
                            (updated.packaging_cost || 0) +
                            (updated.additional_costs || 0),
                    );
                    return updated;
                }),
            );

            // Update original costs
            const newOriginalCosts = new Map<number, number>();
            products.forEach((product) => {
                newOriginalCosts.set(product.id, additionalCost);
            });
            setOriginalAdditionalCosts(newOriginalCosts);

            // Clear changed individual costs and bulk input
            setChangedAdditionalCosts(new Set());
            setBulkAdditionalCost('');
            setBulkAdditionalCostChanged(false);
        }
    };

    const handleBulkResetAdditionalCost = () => {
        setBulkAdditionalCost('');
        setBulkAdditionalCostChanged(false);
    };

    const handleBulkAdditionalCostKeyPress = (e: React.KeyboardEvent) => {
        if (e.key === 'Enter') {
            handleBulkSaveAdditionalCost();
        }
    };

    return (
        <div className="">
            <div className="">
                <div className="bg-white rounded-lg shadow">
                    <div className="p-6 border-b border-gray-200">
                        <div className="flex justify-between items-center">
                            <div className="flex flex-wrap gap-4">
                                <div>
                                    <select
                                        value={categoryId}
                                        onChange={(e) =>
                                            handleFilterChange(
                                                'category',
                                                e.target.value,
                                            )
                                        }
                                        className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-48"
                                    >
                                        <option value="">All Categories</option>
                                        {filterOptions.categories.map(
                                            (category) => (
                                                <option
                                                    key={category.id}
                                                    value={category.id}
                                                >
                                                    {category.name}
                                                </option>
                                            ),
                                        )}
                                    </select>
                                </div>
                                <div>
                                    <select
                                        value={brandId}
                                        onChange={(e) =>
                                            handleFilterChange(
                                                'brand',
                                                e.target.value,
                                            )
                                        }
                                        className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-48"
                                    >
                                        <option value="">All Brands</option>
                                        {filterOptions.brands.map((brand) => (
                                            <option
                                                key={brand.id}
                                                value={brand.id}
                                            >
                                                {brand.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <select
                                        value={purchaseOrderId}
                                        onChange={(e) =>
                                            handleFilterChange(
                                                'purchase_order',
                                                e.target.value,
                                            )
                                        }
                                        className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-56"
                                    >
                                        <option value="">
                                            All Purchase Orders
                                        </option>
                                        {filterOptions.purchase_orders.map(
                                            (po) => (
                                                <option
                                                    key={po.id}
                                                    value={po.id}
                                                >
                                                    {po.reference_code} -{' '}
                                                    {po.supplier_name}
                                                </option>
                                            ),
                                        )}
                                    </select>
                                </div>
                            </div>
                            <div>
                                <input
                                    type="text"
                                    placeholder="Search products..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    className="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-64"
                                />
                            </div>
                        </div>
                    </div>

                    <div className="overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Product Name
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Purchase Cost ($)
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div className="flex flex-col items-center gap-2">
                                            <span>Packaging Cost ($)</span>
                                            <div className="flex items-center gap-1">
                                                <input
                                                    type="number"
                                                    value={bulkPackagingCost}
                                                    onChange={(e) =>
                                                        handleBulkPackagingCostChange(
                                                            e.target.value,
                                                        )
                                                    }
                                                    onKeyPress={
                                                        handleBulkPackagingCostKeyPress
                                                    }
                                                    placeholder="Bulk cost"
                                                    className={`w-24 text-xs text-gray-900 border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                                        bulkPackagingCostChanged
                                                            ? 'border-blue-400 bg-blue-50'
                                                            : 'border-gray-300'
                                                    }`}
                                                    step="0.01"
                                                    min="0"
                                                />
                                                {bulkPackagingCostChanged && (
                                                    <div className="flex gap-1">
                                                        <button
                                                            onClick={
                                                                handleBulkSavePackagingCost
                                                            }
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
                                                                    strokeWidth={
                                                                        2
                                                                    }
                                                                    d="M5 13l4 4L19 7"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            onClick={
                                                                handleBulkResetPackagingCost
                                                            }
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
                                                                    strokeWidth={
                                                                        2
                                                                    }
                                                                    d="M6 18L18 6M6 6l12 12"
                                                                />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <div className="flex flex-col items-center gap-2">
                                            <span>Additional Costs ($)</span>
                                            <div className="flex items-center gap-1">
                                                <input
                                                    type="number"
                                                    value={bulkAdditionalCost}
                                                    onChange={(e) =>
                                                        handleBulkAdditionalCostChange(
                                                            e.target.value,
                                                        )
                                                    }
                                                    onKeyPress={
                                                        handleBulkAdditionalCostKeyPress
                                                    }
                                                    placeholder="Bulk cost"
                                                    className={`w-24 text-xs text-gray-900 border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                                        bulkAdditionalCostChanged
                                                            ? 'border-blue-400 bg-blue-50'
                                                            : 'border-gray-300'
                                                    }`}
                                                    step="0.01"
                                                    min="0"
                                                />
                                                {bulkAdditionalCostChanged && (
                                                    <div className="flex gap-1">
                                                        <button
                                                            onClick={
                                                                handleBulkSaveAdditionalCost
                                                            }
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
                                                                    strokeWidth={
                                                                        2
                                                                    }
                                                                    d="M5 13l4 4L19 7"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            onClick={
                                                                handleBulkResetAdditionalCost
                                                            }
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
                                                                    strokeWidth={
                                                                        2
                                                                    }
                                                                    d="M6 18L18 6M6 6l12 12"
                                                                />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Cost ($)
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {products.map((product) => (
                                    <tr
                                        key={product.id}
                                        className="hover:bg-gray-50"
                                    >
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="text-sm font-medium text-gray-900">
                                                {product.name}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <span className="text-sm font-medium text-gray-600">
                                                $
                                                {(
                                                    product.purchase_cost || 0
                                                ).toFixed(2)}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <div className="flex items-center gap-1">
                                                <input
                                                    type="number"
                                                    value={
                                                        product.packaging_cost
                                                    }
                                                    onChange={(e) =>
                                                        handlePackagingCostChange(
                                                            product.id,
                                                            e.target.value,
                                                        )
                                                    }
                                                    onKeyPress={(e) =>
                                                        handlePackagingCostKeyPress(
                                                            e,
                                                            product.id,
                                                        )
                                                    }
                                                    className={`w-20 text-sm text-gray-900 border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                                        changedPackagingCosts.has(
                                                            product.id,
                                                        )
                                                            ? 'border-blue-400 bg-blue-50'
                                                            : 'border-gray-300'
                                                    }`}
                                                    step="0.01"
                                                    min="0"
                                                />
                                                {changedPackagingCosts.has(
                                                    product.id,
                                                ) && (
                                                    <div className="flex gap-1">
                                                        <button
                                                            onClick={() =>
                                                                handleSavePackagingCost(
                                                                    product.id,
                                                                )
                                                            }
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
                                                                    strokeWidth={
                                                                        2
                                                                    }
                                                                    d="M5 13l4 4L19 7"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            onClick={() =>
                                                                handleResetPackagingCost(
                                                                    product.id,
                                                                )
                                                            }
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
                                                                    strokeWidth={
                                                                        2
                                                                    }
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
                                                    value={
                                                        product.additional_costs
                                                    }
                                                    onChange={(e) =>
                                                        handleAdditionalCostChange(
                                                            product.id,
                                                            e.target.value,
                                                        )
                                                    }
                                                    onKeyPress={(e) =>
                                                        handleAdditionalCostKeyPress(
                                                            e,
                                                            product.id,
                                                        )
                                                    }
                                                    className={`w-20 text-sm text-gray-900 border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500 ${
                                                        changedAdditionalCosts.has(
                                                            product.id,
                                                        )
                                                            ? 'border-blue-400 bg-blue-50'
                                                            : 'border-gray-300'
                                                    }`}
                                                    step="0.01"
                                                    min="0"
                                                />
                                                {changedAdditionalCosts.has(
                                                    product.id,
                                                ) && (
                                                    <div className="flex gap-1">
                                                        <button
                                                            onClick={() =>
                                                                handleSaveAdditionalCost(
                                                                    product.id,
                                                                )
                                                            }
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
                                                                    strokeWidth={
                                                                        2
                                                                    }
                                                                    d="M5 13l4 4L19 7"
                                                                />
                                                            </svg>
                                                        </button>
                                                        <button
                                                            onClick={() =>
                                                                handleResetAdditionalCost(
                                                                    product.id,
                                                                )
                                                            }
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
                                                                    strokeWidth={
                                                                        2
                                                                    }
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
                                                $
                                                {Number(
                                                    product.total_cost || 0,
                                                ).toFixed(2)}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    <div className="p-6 border-t border-gray-200 bg-gray-50">
                        <div className="flex justify-end items-center gap-6">
                            <div className="flex items-center gap-4 text-sm text-gray-600">
                                <span>
                                    Showing {paginatedProducts.from || 0} to{' '}
                                    {paginatedProducts.to || 0} of{' '}
                                    {paginatedProducts.total} products
                                </span>
                                <div>
                                    <select
                                        value={perPage}
                                        onChange={(e) =>
                                            handlePerPageChange(
                                                Number(e.target.value),
                                            )
                                        }
                                        className="px-3 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    >
                                        <option value={10}>10 per page</option>
                                        <option value={25}>25 per page</option>
                                        <option value={50}>50 per page</option>
                                        <option value={100}>
                                            100 per page
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {paginatedProducts.last_page > 1 && (
                                <div className="flex items-center gap-2">
                                    <button
                                        onClick={() =>
                                            handlePageChange(
                                                paginatedProducts.current_page -
                                                    1,
                                            )
                                        }
                                        disabled={
                                            paginatedProducts.current_page <= 1
                                        }
                                        className="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Previous
                                    </button>

                                    <div className="flex items-center gap-1">
                                        {Array.from(
                                            {
                                                length: Math.min(
                                                    5,
                                                    paginatedProducts.last_page,
                                                ),
                                            },
                                            (_, i) => {
                                                let page;
                                                if (
                                                    paginatedProducts.last_page <=
                                                    5
                                                ) {
                                                    page = i + 1;
                                                } else if (
                                                    paginatedProducts.current_page <=
                                                    3
                                                ) {
                                                    page = i + 1;
                                                } else if (
                                                    paginatedProducts.current_page >=
                                                    paginatedProducts.last_page -
                                                        2
                                                ) {
                                                    page =
                                                        paginatedProducts.last_page -
                                                        4 +
                                                        i;
                                                } else {
                                                    page =
                                                        paginatedProducts.current_page -
                                                        2 +
                                                        i;
                                                }

                                                return (
                                                    <button
                                                        key={page}
                                                        onClick={() =>
                                                            handlePageChange(
                                                                page,
                                                            )
                                                        }
                                                        className={`px-3 py-1 text-sm border rounded ${
                                                            page ===
                                                            paginatedProducts.current_page
                                                                ? 'bg-blue-600 text-white border-blue-600'
                                                                : 'bg-white border-gray-300 hover:bg-gray-50'
                                                        }`}
                                                    >
                                                        {page}
                                                    </button>
                                                );
                                            },
                                        )}
                                    </div>

                                    <button
                                        onClick={() =>
                                            handlePageChange(
                                                paginatedProducts.current_page +
                                                    1,
                                            )
                                        }
                                        disabled={
                                            paginatedProducts.current_page >=
                                            paginatedProducts.last_page
                                        }
                                        className="px-3 py-1 text-sm bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                    >
                                        Next
                                    </button>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

PriceCalculator.layout = (page: React.ReactElement) => (
    <BizanaLayout title="Product Price Calculator">{page}</BizanaLayout>
);

export default PriceCalculator;
