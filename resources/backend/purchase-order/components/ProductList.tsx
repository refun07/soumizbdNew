import React, { useState, useEffect, useCallback } from 'react';
import { IProductWithVariations } from '../types';
import { getParentProducts } from '../utils/api';
import { debounce } from '../../../frontend/utils/debounce';
import { t } from 'i18next';
import Image from '../../react/components/Image';

interface ProductListProps {
    warehouseId: number | null;
    selectedVariationIds: number[];
    onProductSelect: (productId: number) => void;
}

const ProductList: React.FC<ProductListProps> = ({
    warehouseId,
    selectedVariationIds,
    onProductSelect,
}) => {
    const [products, setProducts] = useState<IProductWithVariations[]>([]);
    const [searchKey, setSearchKey] = useState('');
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const fetchProducts = useCallback(
        async (search: string) => {
            if (!warehouseId) {
                setProducts([]);
                return;
            }

            setLoading(true);
            setError(null);

            try {
                const data = await getParentProducts({
                    warehouseId,
                    searchKey: search,
                    limit: 20,
                    selectedVariationIds,
                });
                setProducts(data);
            } catch (err) {
                setError(t('failed_load_products', 'Failed to load products'));
                console.error('Error fetching products:', err);
            } finally {
                setLoading(false);
            }
        },
        [warehouseId, selectedVariationIds]
    );

    // Debounced search
    const debouncedFetch = useCallback(
        debounce((search: string) => {
            fetchProducts(search);
        }, 300),
        [fetchProducts]
    );

    useEffect(() => {
        debouncedFetch(searchKey);
    }, [searchKey, debouncedFetch]);

    // Initial load when warehouse changes
    useEffect(() => {
        if (warehouseId) {
            fetchProducts('');
        } else {
            setProducts([]);
        }
    }, [warehouseId, fetchProducts]);

    const handleSearchChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setSearchKey(e.target.value);
    };

    return (
        <div className="flex flex-col h-full min-h-0">
            <div className="p-4 border-b border-border">
                <div className="relative">
                    <input
                        type="text"
                        placeholder={t('search_products', 'Search products...')}
                        value={searchKey}
                        onChange={handleSearchChange}
                        disabled={!warehouseId}
                        className="w-full px-4 py-2 pl-10 border border-border rounded-md bg-background text-foreground placeholder:text-muted focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:opacity-50 disabled:cursor-not-allowed"
                    />
                    <i className="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-muted"></i>
                </div>
                {!warehouseId && (
                    <p className="text-sm text-warning mt-2">
                        {t('select_warehouse_first', 'Please select a warehouse first')}
                    </p>
                )}
            </div>

            <div className="flex-1 overflow-y-auto min-h-0">
                {loading && (
                    <div className="p-8 text-center text-muted">
                        <i className="fas fa-spinner fa-spin mr-2"></i>
                        {t('loading_products', 'Loading products...')}
                    </div>
                )}

                {error && (
                    <div className="p-8 text-center text-danger">
                        <i className="fas fa-exclamation-circle mr-2"></i>
                        {error}
                    </div>
                )}

                {!loading && !error && products.length === 0 && warehouseId && (
                    <div className="p-8 text-center text-muted">
                        <i className="fas fa-box-open text-4xl mb-3 block"></i>
                        {searchKey
                            ? t('no_products_search', 'No products found matching your search')
                            : t('no_products', 'No products available')}
                    </div>
                )}

                <div className="divide-y divide-border">
                    {products.map((product) => (
                        <div
                            key={product.id}
                            className="p-4 hover:bg-primary/5 transition-colors cursor-pointer group"
                            onClick={() => onProductSelect(product.id)}
                        >
                            <div className="flex items-center gap-4">
                                <div className="w-16 h-16 rounded-md border border-border overflow-hidden bg-background flex-shrink-0">
                                    <Image
                                        src={product.thumbnailImage}
                                        alt={product.name}
                                        className="w-full h-full object-cover"
                                    />
                                </div>
                                <div className="flex-1 min-w-0">
                                    <h4 className="font-medium text-foreground truncate">
                                        {product.name}
                                    </h4>
                                    <p className="text-sm text-muted mt-1">
                                        {product.hasVariation
                                            ? `${product.variations.length} ${t('variations', 'variations')}`
                                            : t('simple_product', 'Simple product')}
                                        {' · '}
                                        {t('unit', 'Unit')}: {product.unitName}
                                    </p>
                                </div>
                                <div className="flex-shrink-0">
                                    <button
                                        type="button"
                                        className="px-3 py-1.5 text-sm bg-primary text-white rounded-md opacity-0 group-hover:opacity-100 transition-opacity"
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            onProductSelect(product.id);
                                        }}
                                    >
                                        <i className="fas fa-plus mr-1"></i>
                                        {t('add', 'Add')}
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default ProductList;
