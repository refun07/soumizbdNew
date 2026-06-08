import React, { useState, useEffect } from 'react';
import { IProductWithVariations, IProductVariation, ISelectedVariation } from '../types';
import { getProductVariations } from '../utils/api';
import { t } from 'i18next';
import Image from '../../react/components/Image';
import Input from '../../react/components/inputs/Input';

interface VariationModalProps {
    isOpen: boolean;
    productId: number | null;
    warehouseId: number | null;
    selectedVariationIds: number[];
    onClose: () => void;
    onAdd: (variations: ISelectedVariation[]) => void;
}

const VariationModal: React.FC<VariationModalProps> = ({
    isOpen,
    productId,
    warehouseId,
    selectedVariationIds,
    onClose,
    onAdd,
}) => {
    const [product, setProduct] = useState<IProductWithVariations | null>(null);
    const [loading, setLoading] = useState(false);
    const [sameQuantityForAll, setSameQuantityForAll] = useState(true);
    const [globalQuantity, setGlobalQuantity] = useState(1);
    const [selectedIds, setSelectedIds] = useState<number[]>([]);
    const [quantities, setQuantities] = useState<Record<number, number>>({});

    // Fetch product details when modal opens
    useEffect(() => {
        if (isOpen && productId && warehouseId) {
            setLoading(true);
            getProductVariations(productId, warehouseId, selectedVariationIds)
                .then((data) => {
                    setProduct(data);
                    // Select all variations by default
                    const allIds = data.variations.map((v) => v.id);
                    setSelectedIds(allIds);
                    // Initialize quantities
                    const initialQuantities: Record<number, number> = {};
                    data.variations.forEach((v) => {
                        initialQuantities[v.id] = 1;
                    });
                    setQuantities(initialQuantities);
                    setGlobalQuantity(1);
                    setSameQuantityForAll(true);
                })
                .catch((err) => {
                    console.error('Error fetching product variations:', err);
                })
                .finally(() => {
                    setLoading(false);
                });
        }
    }, [isOpen, productId, warehouseId, selectedVariationIds]);

    // Reset state when modal closes
    useEffect(() => {
        if (!isOpen) {
            setProduct(null);
            setSelectedIds([]);
            setQuantities({});
            setSameQuantityForAll(true);
            setGlobalQuantity(1);
        }
    }, [isOpen]);

    const handleToggleVariation = (variationId: number) => {
        setSelectedIds((prev) => {
            if (prev.includes(variationId)) {
                return prev.filter((id) => id !== variationId);
            }
            return [...prev, variationId];
        });
    };

    const handleSelectAll = () => {
        if (product) {
            const allIds = product.variations.map((v) => v.id);
            setSelectedIds(allIds);
        }
    };

    const handleDeselectAll = () => {
        setSelectedIds([]);
    };

    const handleGlobalQuantityChange = (value: number) => {
        setGlobalQuantity(value);
        if (sameQuantityForAll && product) {
            const newQuantities: Record<number, number> = {};
            product.variations.forEach((v) => {
                newQuantities[v.id] = value;
            });
            setQuantities(newQuantities);
        }
    };

    const handleIndividualQuantityChange = (variationId: number, value: number) => {
        setQuantities((prev) => ({
            ...prev,
            [variationId]: value,
        }));
    };

    const calculateVariationValues = (
        variation: IProductVariation,
        quantity: number
    ): { unitPrice: number; discount: number; tax: number } => {
        // These calculations should match the backend logic
        // For now, using variation price as unit price
        const unitPrice = variation.price;
        const discount = 0; // Will be calculated based on product settings
        const tax = 0; // Will be calculated based on product settings
        return { unitPrice, discount, tax };
    };

    const handleAdd = () => {
        if (!product) return;

        const selectedVariations: ISelectedVariation[] = selectedIds
            .map((variationId) => {
                const variation = product.variations.find((v) => v.id === variationId);
                if (!variation) return null;

                const quantity = quantities[variationId] || 1;
                const { unitPrice, discount, tax } = calculateVariationValues(variation, quantity);

                return {
                    variationId: variation.id,
                    productId: product.id,
                    productName: product.name,
                    variationName: variation.variationName,
                    sku: variation.sku,
                    unitPrice,
                    quantity,
                    currentStock: variation.stockQty,
                    unitName: product.unitName,
                    discount,
                    tax,
                };
            })
            .filter(Boolean) as ISelectedVariation[];

        onAdd(selectedVariations);
        onClose();
    };

    if (!isOpen) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
            <div className="bg-background rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
                {/* Header */}
                <div className="flex items-center justify-between p-4 border-b border-border">
                    <h3 className="text-lg font-semibold text-foreground">
                        {t('select_variations', 'Select Variations')}
                    </h3>
                    <button
                        type="button"
                        onClick={onClose}
                        className="text-muted hover:text-foreground transition-colors"
                    >
                        <i className="fas fa-times text-xl"></i>
                    </button>
                </div>

                {/* Content */}
                <div className="flex-1 overflow-y-auto p-4">
                    {loading ? (
                        <div className="text-center py-8 text-muted">
                            <i className="fas fa-spinner fa-spin mr-2"></i>
                            {t('loading', 'Loading...')}
                        </div>
                    ) : !product ? (
                        <div className="text-center py-8 text-muted">
                            {t('no_product_selected', 'No product selected')}
                        </div>
                    ) : (
                        <>
                            {/* Product Info */}
                            <div className="flex items-center gap-4 mb-6 p-4 bg-primary/5 rounded-lg">
                                <div className="w-16 h-16 rounded-md border border-border overflow-hidden bg-background flex-shrink-0">
                                    <Image
                                        src={product.thumbnailImage}
                                        alt={product.name}
                                        className="w-full h-full object-cover"
                                    />
                                </div>
                                <div>
                                    <h4 className="font-medium text-foreground">{product.name}</h4>
                                    <p className="text-sm text-muted">
                                        {product.variations.length} {t('variations_available', 'variations available')}
                                    </p>
                                </div>
                            </div>

                            {/* Same Quantity Toggle */}
                            <div className="flex items-center justify-between mb-4 p-3 bg-background-hover rounded-lg">
                                <label className="flex items-center gap-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={sameQuantityForAll}
                                        onChange={(e) => {
                                            setSameQuantityForAll(e.target.checked);
                                            if (e.target.checked) {
                                                handleGlobalQuantityChange(globalQuantity);
                                            }
                                        }}
                                        className="w-4 h-4 rounded border-border text-primary focus:ring-primary"
                                    />
                                    <span className="text-sm font-medium text-foreground">
                                        {t('same_quantity_for_all', 'Same quantity for all variations')}
                                    </span>
                                </label>

                                {sameQuantityForAll && (
                                    <div className="w-32">
                                        <Input
                                            type="number"
                                            min={1}
                                            value={globalQuantity}
                                            onChange={(e) =>
                                                handleGlobalQuantityChange(parseInt(e.target.value) || 1)
                                            }
                                            className="text-center"
                                        />
                                    </div>
                                )}
                            </div>

                            {/* Select/Deselect All */}
                            <div className="flex items-center justify-between mb-3">
                                <span className="text-sm text-muted">
                                    {selectedIds.length} {t('of', 'of')} {product.variations.length}{' '}
                                    {t('selected', 'selected')}
                                </span>
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        onClick={handleSelectAll}
                                        className="text-sm text-primary hover:underline"
                                    >
                                        {t('select_all', 'Select all')}
                                    </button>
                                    <span className="text-muted">|</span>
                                    <button
                                        type="button"
                                        onClick={handleDeselectAll}
                                        className="text-sm text-primary hover:underline"
                                    >
                                        {t('deselect_all', 'Deselect all')}
                                    </button>
                                </div>
                            </div>

                            {/* Variations List */}
                            <div className="space-y-2">
                                {product.variations.map((variation) => (
                                    <div
                                        key={variation.id}
                                        className={`p-3 border rounded-lg transition-colors ${
                                            selectedIds.includes(variation.id)
                                                ? 'border-primary bg-primary/5'
                                                : 'border-border bg-background'
                                        }`}
                                    >
                                        <div className="flex items-center gap-3">
                                            <input
                                                type="checkbox"
                                                checked={selectedIds.includes(variation.id)}
                                                onChange={() => handleToggleVariation(variation.id)}
                                                className="w-4 h-4 rounded border-border text-primary focus:ring-primary"
                                            />
                                            <div className="flex-1">
                                                <p className="font-medium text-foreground">
                                                    {variation.variationName}
                                                </p>
                                                <p className="text-sm text-muted">
                                                    SKU: {variation.sku} · {t('stock', 'Stock')}:{' '}
                                                    {variation.stockQty}
                                                </p>
                                            </div>
                                            <div className="text-right">
                                                <p className="font-medium text-foreground">
                                                    {variation.price}
                                                </p>
                                            </div>
                                            {!sameQuantityForAll && selectedIds.includes(variation.id) && (
                                                <div className="w-24">
                                                    <Input
                                                        type="number"
                                                        min={1}
                                                        value={quantities[variation.id] || 1}
                                                        onChange={(e) =>
                                                            handleIndividualQuantityChange(
                                                                variation.id,
                                                                parseInt(e.target.value) || 1
                                                            )
                                                        }
                                                        className="text-center"
                                                    />
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </>
                    )}
                </div>

                {/* Footer */}
                <div className="flex items-center justify-end gap-3 p-4 border-t border-border">
                    <button
                        type="button"
                        onClick={onClose}
                        className="px-4 py-2 text-sm font-medium text-foreground bg-background border border-border rounded-md hover:bg-background-hover"
                    >
                        {t('cancel', 'Cancel')}
                    </button>
                    <button
                        type="button"
                        onClick={handleAdd}
                        disabled={selectedIds.length === 0 || loading}
                        className="px-4 py-2 text-sm font-medium text-white bg-primary rounded-md hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {t('add_to_order', 'Add to Order')}
                    </button>
                </div>
            </div>
        </div>
    );
};

export default VariationModal;
