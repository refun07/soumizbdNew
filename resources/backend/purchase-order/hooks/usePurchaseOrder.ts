import { useState, useMemo, useCallback } from 'react';
import {
    IPurchaseOrderFormData,
    ISelectedVariation,
    IGroupedSelection,
    ISummary,
    IExistingOrderItem,
    IWarehouse,
    ISupplier,
} from '../types';

interface UsePurchaseOrderProps {
    initialWarehouses: IWarehouse[];
    initialSuppliers: ISupplier[];
    existingOrderItems: IExistingOrderItem[];
    initialFormData: Partial<IPurchaseOrderFormData>;
    useInventory: boolean;
    isEdit: boolean;
}

export const usePurchaseOrder = ({
    initialWarehouses,
    initialSuppliers,
    existingOrderItems,
    initialFormData,
    useInventory,
    isEdit,
}: UsePurchaseOrderProps) => {
    // Form data state
    const [formData, setFormData] = useState<IPurchaseOrderFormData>({
        warehouseId: initialFormData.warehouseId || null,
        supplierId: initialFormData.supplierId || null,
        date: initialFormData.date || new Date().toISOString().split('T')[0],
        status: initialFormData.status || 'pending',
        tax: initialFormData.tax || 0,
        discount: initialFormData.discount || 0,
        shipping: initialFormData.shipping || 0,
        note: initialFormData.note || '',
    });

    // Selected variations state
    const [selectedVariations, setSelectedVariations] = useState<ISelectedVariation[]>(
        existingOrderItems.map((item) => ({
            variationId: item.variationId,
            productId: item.productId,
            productName: item.productName,
            variationName: item.variationName,
            sku: item.sku,
            unitPrice: item.unitPrice,
            quantity: item.quantity,
            currentStock: item.currentStock,
            unitName: item.unitName,
            discount: item.discount,
            tax: item.tax,
            purchasedQty: item.purchasedQty,
            expiryDate: item.expiryDate,
        }))
    );

    // UI state
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [selectedProductId, setSelectedProductId] = useState<number | null>(null);

    // Group variations by parent product for display
    const groupedSelections = useMemo<IGroupedSelection[]>(() => {
        const grouped = selectedVariations.reduce((acc, variation) => {
            if (!acc[variation.productId]) {
                acc[variation.productId] = {
                    productId: variation.productId,
                    productName: variation.productName,
                    variations: [],
                };
            }
            acc[variation.productId].variations.push(variation);
            return acc;
        }, {} as Record<number, IGroupedSelection>);

        return Object.values(grouped);
    }, [selectedVariations]);

    // Calculate summary totals
    const summary = useMemo<ISummary>(() => {
        const subtotal = selectedVariations.reduce((sum, v) => {
            const itemTotal = v.unitPrice * v.quantity;
            const itemTax = v.tax * v.quantity;
            const itemDiscount = v.discount * v.quantity;
            return sum + itemTotal + itemTax - itemDiscount;
        }, 0);

        const taxValue = (subtotal * formData.tax) / 100;
        const grandTotal = subtotal + taxValue - formData.discount + formData.shipping;

        return {
            subtotal,
            taxValue,
            discount: formData.discount,
            shipping: formData.shipping,
            grandTotal,
        };
    }, [selectedVariations, formData.tax, formData.discount, formData.shipping]);

    // Form data handlers
    const updateFormData = useCallback((updates: Partial<IPurchaseOrderFormData>) => {
        setFormData((prev) => ({ ...prev, ...updates }));
    }, []);

    // Variation handlers
    const addVariations = useCallback((variations: ISelectedVariation[]) => {
        setSelectedVariations((prev) => {
            // Filter out any existing variations with same IDs
            const existingIds = new Set(variations.map((v) => v.variationId));
            const filtered = prev.filter((v) => !existingIds.has(v.variationId));
            return [...filtered, ...variations];
        });
    }, []);

    const removeVariation = useCallback((variationId: number) => {
        setSelectedVariations((prev) => prev.filter((v) => v.variationId !== variationId));
    }, []);

    const updateVariation = useCallback((variationId: number, updates: Partial<ISelectedVariation>) => {
        setSelectedVariations((prev) => {
            return prev.map((v) => (v.variationId === variationId ? { ...v, ...updates } : v));
        });
    }, []);

    const removeProductGroup = useCallback((productId: number) => {
        setSelectedVariations((prev) => prev.filter((v) => v.productId !== productId));
    }, []);

    // Modal handlers
    const openVariationModal = useCallback((productId: number) => {
        setSelectedProductId(productId);
        setIsModalOpen(true);
    }, []);

    const closeVariationModal = useCallback(() => {
        setIsModalOpen(false);
        setSelectedProductId(null);
    }, []);

    // Get selected variation IDs for API filtering
    const selectedVariationIds = useMemo(
        () => selectedVariations.map((v) => v.variationId),
        [selectedVariations]
    );

    return {
        // State
        formData,
        selectedVariations,
        groupedSelections,
        summary,
        isModalOpen,
        selectedProductId,
        selectedVariationIds,
        warehouses: initialWarehouses,
        suppliers: initialSuppliers,
        useInventory,
        isEdit,

        // Actions
        updateFormData,
        addVariations,
        removeVariation,
        updateVariation,
        removeProductGroup,
        openVariationModal,
        closeVariationModal,
    };
};
