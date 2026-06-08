import React, { useState } from 'react';
import { IGroupedSelection, ISelectedVariation, ISummary } from '../types';
import { t } from 'i18next';
import Input from '../../react/components/inputs/Input';

interface SelectedProductsProps {
    groupedSelections: IGroupedSelection[];
    summary: ISummary;
    useInventory: boolean;
    isEdit: boolean;
    isReturn: boolean;
    onRemoveVariation: (variationId: number) => void;
    onRemoveProductGroup: (productId: number) => void;
    onUpdateVariation: (variationId: number, updates: Partial<ISelectedVariation>) => void;
}

const SelectedProducts: React.FC<SelectedProductsProps> = ({
    groupedSelections,
    summary,
    useInventory,
    isEdit,
    isReturn,
    onRemoveVariation,
    onRemoveProductGroup,
    onUpdateVariation,
}) => {
    const [expandedGroups, setExpandedGroups] = useState<Record<number, boolean>>({});

    const toggleGroup = (productId: number) => {
        setExpandedGroups((prev) => ({
            ...prev,
            [productId]: !prev[productId],
        }));
    };

    const formatPrice = (price: number) => {
        return price.toFixed(3);
    };

    if (groupedSelections.length === 0) {
        return (
            <div className="flex flex-col h-full">
                <div className="flex-1 flex items-center justify-center p-8">
                    <div className="text-center text-muted">
                        <i className="fas fa-shopping-cart text-4xl mb-3 block opacity-30"></i>
                        <p>{t('no_products_yet', 'No products added yet')}</p>
                        <p className="text-sm mt-2">
                            {t('select_products_hint', 'Select products from the left panel to add them to your order')}
                        </p>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="flex flex-col h-full">
            <div className="flex-1 overflow-y-auto">
                <div className="divide-y divide-border">
                    {groupedSelections.map((group) => (
                        <div key={group.productId} className="border-b border-border last:border-b-0">
                            {/* Product Group Header */}
                            <div
                                className="flex items-center justify-between p-4 bg-primary/5 cursor-pointer hover:bg-primary/10 transition-colors"
                                onClick={() => toggleGroup(group.productId)}
                            >
                                <div className="flex items-center gap-3">
                                    <i
                                        className={`fas fa-chevron-${
                                            expandedGroups[group.productId] ? 'down' : 'right'
                                        } text-muted transition-transform`}
                                    ></i>
                                    <span className="font-medium text-foreground">{group.productName}</span>
                                    <span className="text-sm text-muted">
                                        ({group.variations.length}{' '}
                                        {group.variations.length === 1
                                            ? t('variation', 'variation')
                                            : t('variations', 'variations')}
                                        )
                                    </span>
                                </div>
                                <button
                                    type="button"
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        onRemoveProductGroup(group.productId);
                                    }}
                                    className="text-danger hover:text-danger/80 transition-colors"
                                    title={t('remove_all_variations', 'Remove all variations')}
                                    disabled={isReturn}
                                >
                                    <i className="fas fa-trash-alt"></i>
                                </button>
                            </div>

                            {/* Variations Table */}
                            {expandedGroups[group.productId] === true && (
                                <div className="overflow-x-auto">
                                    <table className="w-full min-w-[600px]">
                                        <thead className="bg-background-hover">
                                            <tr className="text-left text-xs uppercase text-muted">
                                                <th className="p-3 font-medium">{t('variation', 'Variation')}</th>
                                                <th className="p-3 font-medium">{t('stock', 'Stock')}</th>
                                                <th className="p-3 font-medium">{t('unit_price', 'Unit Price')}</th>
                                                <th className="p-3 font-medium">{t('qty', 'Qty')}</th>
                                                <th className="p-3 font-medium">{t('discount', 'Discount')}</th>
                                                <th className="p-3 font-medium">{t('tax', 'Tax')}</th>
                                                <th className="p-3 font-medium">{t('subtotal', 'Subtotal')}</th>
                                                {useInventory && isEdit && (
                                                    <th className="p-3 font-medium">{t('expiry', 'Expiry')}</th>
                                                )}
                                                <th className="p-3 font-medium w-10"></th>
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-border">
                                            {group.variations.map((variation) => {
                                                const subtotal =
                                                    variation.unitPrice * variation.quantity +
                                                    variation.tax * variation.quantity -
                                                    variation.discount * variation.quantity;

                                                return (
                                                    <tr key={variation.variationId} className="hover:bg-background-hover">
                                                        <td className="p-3">
                                                            <div>
                                                                <p className="font-medium text-foreground">
                                                                    {variation.variationName}
                                                                </p>
                                                                <p className="text-xs text-muted">
                                                                    SKU: {variation.sku}
                                                                </p>
                                                            </div>
                                                        </td>
                                                        <td className="p-3 text-foreground">
                                                            {variation.currentStock}{' '}
                                                            <span className="text-muted">{variation.unitName}</span>
                                                        </td>
                                                        <td className="p-3">
                                                            <Input
                                                                type="number"
                                                                min={0}
                                                                step={0.001}
                                                                value={variation.unitPrice}
                                                                disabled={isReturn}
                                                                onChange={(e) =>
                                                                    onUpdateVariation(
                                                                        variation.variationId,
                                                                        {
                                                                            unitPrice:
                                                                                parseFloat(
                                                                                    e.target.value
                                                                                ) || 0,
                                                                        }
                                                                    )
                                                                }
                                                                className="w-24 text-right"
                                                            />
                                                        </td>
                                                        <td className="p-3">
                                                            <Input
                                                                type="number"
                                                                min={isReturn ? 0 : 1}
                                                                max={isReturn ? variation.purchasedQty : undefined}
                                                                value={variation.quantity}
                                                                onChange={(e) =>
                                                                    onUpdateVariation(
                                                                        variation.variationId,
                                                                        {
                                                                            quantity:
                                                                                parseInt(
                                                                                    e.target.value
                                                                                ) || 1,
                                                                        }
                                                                    )
                                                                }
                                                                className="w-20 text-center"
                                                            />
                                                        </td>
                                                        <td className="p-3 text-foreground">
                                                            <input
                                                                type="hidden"
                                                                name="discount[]"
                                                                value={variation.discount}
                                                            />
                                                            <span>{formatPrice(variation.discount)}</span>
                                                        </td>
                                                        <td className="p-3 text-foreground">
                                                            <input
                                                                type="hidden"
                                                                name="tax[]"
                                                                value={variation.tax}
                                                            />
                                                            <span>{formatPrice(variation.tax)}</span>
                                                        </td>
                                                        <td className="p-3 font-medium text-foreground">
                                                            <input
                                                                type="hidden"
                                                                name="subtotal[]"
                                                                value={subtotal}
                                                            />
                                                            <span>{formatPrice(subtotal)}</span>
                                                        </td>
                                                        {useInventory && isEdit && (
                                                            <td className="p-3">
                                                                <input
                                                                    type="date"
                                                                    name="expiryDates[]"
                                                                    value={variation.expiryDate || ''}
                                                                    onChange={(e) =>
                                                                        onUpdateVariation(
                                                                            variation.variationId,
                                                                            {
                                                                                expiryDate:
                                                                                    e.target.value,
                                                                            }
                                                                        )
                                                                    }
                                                                    className="w-32 px-2 py-1 border border-border rounded text-sm"
                                                                />
                                                            </td>
                                                        )}
                                                        <td className="p-3">
                                                            <button
                                                                type="button"
                                                                onClick={() =>
                                                                    onRemoveVariation(
                                                                        variation.variationId
                                                                    )
                                                                }
                                                                className="text-danger hover:text-danger/80 transition-colors"
                                                                disabled={isReturn}
                                                            >
                                                                <i className="fas fa-times"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                );
                                            })}
                                        </tbody>
                                    </table>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </div>

            {/* Summary */}
            <div className="border-t border-border p-4 bg-background">
                <div className="flex justify-end">
                    <div className="w-full max-w-sm space-y-2">
                        <div className="flex justify-between text-sm">
                            <span className="text-muted">{t('subtotal', 'Subtotal')}</span>
                            <span className="font-medium">{formatPrice(summary.subtotal)}</span>
                        </div>
                        <div className="flex justify-between text-sm">
                            <span className="text-muted">{t('tax', 'Tax')}</span>
                            <span className="font-medium">{formatPrice(summary.taxValue)}</span>
                        </div>
                        <div className="flex justify-between text-sm">
                            <span className="text-muted">{t('discount', 'Discount')}</span>
                            <span className="font-medium">{formatPrice(summary.discount)}</span>
                        </div>
                        <div className="flex justify-between text-sm">
                            <span className="text-muted">{t('shipping', 'Shipping')}</span>
                            <span className="font-medium">{formatPrice(summary.shipping)}</span>
                        </div>
                        <div className="border-t border-border pt-2 flex justify-between">
                            <span className="font-semibold text-foreground">{t('grand_total', 'Grand Total')}</span>
                            <span className="font-semibold text-primary text-lg">
                                {formatPrice(summary.grandTotal)}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default SelectedProducts;
