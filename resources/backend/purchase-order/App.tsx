import React from 'react';
import { usePurchaseOrder } from './hooks/usePurchaseOrder';
import ProductList from './components/ProductList';
import VariationModal from './components/VariationModal';
import SelectedProducts from './components/SelectedProducts';
import { t } from 'i18next';
import Input from '../react/components/inputs/Input';
import SelectInput from '../react/components/inputs/SelectInput';
import Textarea from '../react/components/inputs/Textarea';

interface AppProps {
    warehouses: Array<{ id: number; name: string }>;
    suppliers: Array<{ id: number; name: string }>;
    existingOrderItems: any[];
    initialFormData: any;
    useInventory: boolean;
    isEdit: boolean;
    isReturn: boolean;
    submitUrl: string;
    method: string;
}

const App: React.FC<AppProps> = ({
    warehouses,
    suppliers,
    existingOrderItems,
    initialFormData,
    useInventory,
    isEdit,
    isReturn,
    submitUrl,
    method,
}) => {
    const {
        formData,
        selectedVariations,
        groupedSelections,
        summary,
        isModalOpen,
        selectedProductId,
        selectedVariationIds,
        updateFormData,
        addVariations,
        removeVariation,
        updateVariation,
        removeProductGroup,
        openVariationModal,
        closeVariationModal,
    } = usePurchaseOrder({
        initialWarehouses: warehouses,
        initialSuppliers: suppliers,
        existingOrderItems,
        initialFormData,
        useInventory,
        isEdit,
    });

    const handleSubmit = (e: React.FormEvent) => {
        if (!formData.supplierId) {
            e.preventDefault();
            alert(t('error_no_supplier', 'Please select a supplier before creating the order'));
            return;
        }

        if (selectedVariations.length === 0) {
            e.preventDefault();
            alert(t('error_no_products', 'Please add at least one product to the order'));
            return;
        }
    };

    return (
        <form
            action={submitUrl}
            method="POST"
            onSubmit={handleSubmit}
            className="space-y-6"
        >
            <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''} />
            {method === 'PUT' && <input type="hidden" name="_method" value="PUT" />}

            {/* Header Section */}
            <div className="card">
                <div className="card__content">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-foreground mb-1">
                                {t('warehouse', 'Warehouse')} <span className="text-red-500">*</span>
                            </label>
                            <SelectInput
                                name="warehouse_id"
                                value={formData.warehouseId}
                                options={warehouses}
                                getOptionLabel={(w) => w.name}
                                getOptionValue={(w) => w.id}
                                placeholder={t('select_warehouse', 'Select warehouse')}
                                onChange={(value) => {
                                    updateFormData({ warehouseId: value?.id || null });
                                }}
                                isDisabled={isEdit}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-foreground mb-1">
                                {t('supplier', 'Supplier')} <span className="text-red-500">*</span>
                            </label>
                            <SelectInput
                                name="supplier_id"
                                value={formData.supplierId}
                                options={suppliers}
                                getOptionLabel={(s) => s.name}
                                getOptionValue={(s) => s.id}
                                placeholder={t('select_supplier', 'Select supplier')}
                                onChange={(value) => {
                                    updateFormData({ supplierId: value?.id || null });
                                }}
                                isDisabled={isReturn}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-foreground mb-1">
                                {t('date', 'Date')} <span className="text-red-500">*</span>
                            </label>
                            <Input
                                type="date"
                                name="date"
                                value={formData.date}
                                onChange={(e) => updateFormData({ date: e.target.value })}
                            />
                        </div>
                    </div>
                </div>
            </div>

            {/* Two Column Layout */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {/* Left Column - Product Selection */}
                <div className="lg:col-span-5">
                    <div className="card h-[600px] flex flex-col">
                        <div className="card__title">
                            <span>{t('select_products', 'Select Products')}</span>
                        </div>
                        <div className="card__content flex-1 p-0 overflow-hidden">
                            <ProductList
                                warehouseId={formData.warehouseId}
                                selectedVariationIds={selectedVariationIds}
                                onProductSelect={isReturn ? () => null : openVariationModal}
                            />
                        </div>
                    </div>
                </div>

                {/* Right Column - Selected Products */}
                <div className="lg:col-span-7">
                    <div className="card h-[600px] flex flex-col">
                        <div className="card__title flex justify-between items-center">
                            <span>{t('order_items', 'Order Items')}</span>
                            <span className="text-sm text-muted">
                                {selectedVariations.length} {t('items', 'items')}
                            </span>
                        </div>
                        <div className="card__content flex-1 p-0 overflow-hidden">
                            <SelectedProducts
                                groupedSelections={groupedSelections}
                                summary={summary}
                                useInventory={useInventory}
                                isEdit={isEdit}
                                isReturn={isReturn}
                                onRemoveVariation={isReturn ? () => null : removeVariation}
                                onRemoveProductGroup={isReturn ? () => null : removeProductGroup}
                                onUpdateVariation={updateVariation}
                            />
                        </div>
                    </div>
                </div>
            </div>

            {/* Hidden Form Fields for Backend Compatibility */}
            <div className="hidden">
                {selectedVariations.map((variation) => (
                    <React.Fragment key={variation.variationId}>
                        <input
                            type="hidden"
                            name="selectedVariationIds[]"
                            value={variation.variationId}
                        />
                        <input
                            type="hidden"
                            name="unitPrice[]"
                            value={variation.unitPrice}
                        />
                        <input
                            type="hidden"
                            name="stockQty[]"
                            value={variation.quantity}
                        />
                        <input
                            type="hidden"
                            name="discount[]"
                            value={variation.discount}
                        />
                        <input
                            type="hidden"
                            name="discountPrice[]"
                            value={variation.discount * variation.quantity}
                        />
                        <input
                            type="hidden"
                            name="tax[]"
                            value={variation.tax}
                        />
                        <input
                            type="hidden"
                            name="taxPrice[]"
                            value={variation.tax * variation.quantity}
                        />
                        <input
                            type="hidden"
                            name="subtotal[]"
                            value={
                                variation.unitPrice * variation.quantity +
                                variation.tax * variation.quantity -
                                variation.discount * variation.quantity
                            }
                        />
                        {useInventory && variation.expiryDate && (
                            <input
                                type="hidden"
                                name="expiryDates[]"
                                value={variation.expiryDate}
                            />
                        )}
                    </React.Fragment>
                ))}
            </div>

            {/* Summary and Additional Fields */}
            <div className="card">
                <div className="card__content">
                    <div className="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label className="block text-sm font-medium text-foreground mb-1">
                                {t('status', 'Status')}
                            </label>
                            <select
                                name="status"
                                value={formData.status}
                                onChange={(e) => updateFormData({ status: e.target.value })}
                                className="w-full px-3 py-2 border border-border rounded-md bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                {isReturn ? (
                                    <>
                                        <option value="pending">{t('status_pending', 'Pending')}</option>
                                        <option value="completed">{t('status_completed', 'Completed')}</option>
                                    </>
                                ) : (
                                    <>
                                        <option value="pending">{t('status_pending', 'Pending')}</option>
                                        <option value="ordered">{t('status_ordered', 'Ordered')}</option>
                                        <option value="received">{t('status_received', 'Received')}</option>
                                    </>
                                )}
                            </select>
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-foreground mb-1">
                                {t('tax', 'Tax')} (%)
                            </label>
                            <Input
                                type="number"
                                name="tax"
                                min={0}
                                max={100}
                                step={0.001}
                                value={formData.tax}
                                onChange={(e) =>
                                    updateFormData({ tax: parseFloat(e.target.value) || 0 })
                                }
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-foreground mb-1">
                                {t('discount', 'Discount')}
                            </label>
                            <Input
                                type="number"
                                name="discount"
                                min={0}
                                step={0.001}
                                value={formData.discount}
                                onChange={(e) =>
                                    updateFormData({ discount: parseFloat(e.target.value) || 0 })
                                }
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-medium text-foreground mb-1">
                                {t('shipping', 'Shipping')}
                            </label>
                            <Input
                                type="number"
                                name="shipping"
                                min={0}
                                step={0.001}
                                value={formData.shipping}
                                onChange={(e) =>
                                    updateFormData({ shipping: parseFloat(e.target.value) || 0 })
                                }
                            />
                        </div>
                    </div>

                    <div className="mt-4">
                        <label className="block text-sm font-medium text-foreground mb-1">
                            {t('note', 'Note')}
                        </label>
                        <Textarea
                            name="note"
                            value={formData.note}
                            onChange={(e) => updateFormData({ note: e.target.value })}
                            placeholder={t('add_notes_placeholder', 'Add any additional notes...')}
                            rows={3}
                        />
                    </div>

                    <div className="mt-6 flex justify-end">
                        <button
                            type="submit"
                            className="px-6 py-2 bg-primary text-white font-medium rounded-md hover:bg-primary/90 transition-colors"
                        >
                            <i className="fas fa-save mr-2"></i>
                            {isReturn
                                ? t('return_purchase_order', 'Return Purchase Order')
                                : isEdit
                                  ? t('update_order', 'Update Order')
                                  : t('create_order', 'Create Order')}
                        </button>
                    </div>
                </div>
            </div>

            {/* Variation Selection Modal */}
            <VariationModal
                isOpen={isModalOpen}
                productId={selectedProductId}
                warehouseId={formData.warehouseId}
                selectedVariationIds={selectedVariationIds}
                onClose={closeVariationModal}
                onAdd={isReturn ? () => null : addVariations}
            />
        </form>
    );
};

export default App;
