import React, { useState, useEffect, useMemo } from 'react';
import { SectionFormData, PageConfigs } from '../../hooks/useBuilderState';
import Button from '../../../../react/components/inputs/Button';
import LayoutSettings from './LayoutSettings';
import { TextInputWithColor, TextareaWithColor, ButtonTextInput } from '../shared';

interface FeaturedProduct {
    id: string;
    title: string;
    description: string;
    image: string;
    button_text: string;
}

interface FeaturedProductsFormProps {
    sectionId: number;
    landingPageId: number;
    sectionFormData: SectionFormData;
    pageConfigs: PageConfigs;
    onChange?: (data: SectionFormData) => void;
    onSave?: () => void;
    saving?: boolean;
}

const getContrastColor = (hexColor: string | null | undefined): string => {
    if (!hexColor) return '#ffffff';
    const hex = hexColor.replace('#', '');
    if (hex.length !== 6) return '#ffffff';
    const r = parseInt(hex.slice(0, 2), 16);
    const g = parseInt(hex.slice(2, 4), 16);
    const b = parseInt(hex.slice(4, 6), 16);
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.5 ? '#1a1a1a' : '#ffffff';
};

const getSecondaryColor = (hexColor: string | null | undefined): string => {
    if (!hexColor) return 'rgba(255,255,255,0.7)';
    const hex = hexColor.replace('#', '');
    if (hex.length !== 6) return 'rgba(255,255,255,0.7)';
    const r = parseInt(hex.slice(0, 2), 16);
    const g = parseInt(hex.slice(2, 4), 16);
    const b = parseInt(hex.slice(4, 6), 16);
    const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
    return luminance > 0.5 ? '#4a4a4a' : 'rgba(255,255,255,0.7)';
};

const FeaturedProductsForm: React.FC<FeaturedProductsFormProps> = ({
    sectionId,
    landingPageId,
    sectionFormData,
    pageConfigs,
    onChange,
    onSave,
    saving = false,
}) => {
    const [localData, setLocalData] = useState<SectionFormData>(sectionFormData);

    useEffect(() => {
        setLocalData(sectionFormData);
    }, [sectionFormData]);

    const updateField = (field: string, value: any) => {
        const newData = { ...localData, [field]: value };
        setLocalData(newData);
        onChange?.(newData);
    };

    const bgColor = localData.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedProductTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedProductDescColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.featured_button_bg_color || primaryColor), [localData.featured_button_bg_color, primaryColor]);

    const getProducts = (): FeaturedProduct[] => {
        if (localData.featured_products && Array.isArray(localData.featured_products)) {
            return localData.featured_products as unknown as FeaturedProduct[];
        }
        return [];
    };

    const addProduct = () => {
        const currentProducts = getProducts();
        const newProduct: FeaturedProduct = {
            id: `product-${Date.now()}`,
            title: '',
            description: '',
            image: '',
            button_text: 'BUY WITH CASH ON DELIVERY',
        };
        updateField('featured_products', [...currentProducts, newProduct]);
    };

    const updateProduct = (index: number, field: keyof FeaturedProduct, value: string) => {
        const currentProducts = getProducts();
        const updatedProducts = [...currentProducts];
        updatedProducts[index] = { ...updatedProducts[index], [field]: value };
        updateField('featured_products', updatedProducts);
    };

    const removeProduct = (index: number) => {
        const currentProducts = getProducts();
        const updatedProducts = currentProducts.filter((_, i) => i !== index);
        updateField('featured_products', updatedProducts);
    };

    const products = getProducts();

    return (
        <div className="space-y-6">
            <LayoutSettings
                data={localData}
                pageConfigs={pageConfigs}
                onChange={(data) => setLocalData(data)}
            />

            <TextInputWithColor
                label="Section Title"
                value={localData.section_title || ''}
                colorValue={localData.featured_section_title_color || null}
                onChange={(value) => updateField('section_title', value)}
                onColorChange={(color) => updateField('featured_section_title_color', color)}
                placeholder="e.g., Featured Products"
                computedColor={computedTitleColor}
            />

            <div className="border-t pt-4">
                <div className="mb-4 p-3 bg-gray-50 rounded-lg border">
                    <p className="text-xs text-gray-600 mb-2">Colors apply to all products:</p>
                    <div className="grid grid-cols-3 gap-3">
                        <TextInputWithColor
                            label="Title Color"
                            value=""
                            colorValue={localData.featured_product_title_color || null}
                            onChange={() => {}}
                            onColorChange={(color) => updateField('featured_product_title_color', color)}
                            computedColor={computedProductTitleColor}
                        />
                        <TextInputWithColor
                            label="Description Color"
                            value=""
                            colorValue={localData.featured_product_description_color || null}
                            onChange={() => {}}
                            onColorChange={(color) => updateField('featured_product_description_color', color)}
                            computedColor={computedProductDescColor}
                        />
                        <ButtonTextInput
                            label="Button Colors"
                            value=""
                            textColor={localData.featured_button_text_color || null}
                            bgColor={localData.featured_button_bg_color || null}
                            onChange={() => {}}
                            onTextColorChange={(color) => updateField('featured_button_text_color', color)}
                            onBgColorChange={(color) => updateField('featured_button_bg_color', color)}
                            primaryColor={primaryColor}
                            computedTextColor={computedButtonTextColor}
                        />
                    </div>
                </div>

                <div className="flex items-center justify-between mb-4">
                    <h3 className="text-lg font-semibold">Products (2 Column Grid)</h3>
                    <button
                        type="button"
                        onClick={addProduct}
                        className="btn btn-secondary btn-sm"
                    >
                        <i className="fa-solid fa-plus mr-1"></i>
                        Add Product
                    </button>
                </div>

                {products.length === 0 ? (
                    <p className="text-gray-500 text-sm italic">No products added yet. Click "Add Product" to create one.</p>
                ) : (
                    <div className="grid grid-cols-1 gap-4">
                        {products.map((product, index) => (
                            <div key={product.id} className="bg-gray-50 p-4 rounded-lg border">
                                <div className="flex items-start justify-between mb-3">
                                    <span className="text-sm font-medium text-gray-700">Product #{index + 1}</span>
                                    <button
                                        type="button"
                                        onClick={() => removeProduct(index)}
                                        className="text-red-500 hover:text-red-700"
                                    >
                                        <i className="fa-solid fa-times"></i>
                                    </button>
                                </div>

                                <div className="mb-3">
                                    <label className="theme-input-label pt-0 text-xs">Product Image URL</label>
                                    <div className="flex items-center gap-3">
                                        {product.image && (
                                            <img
                                                src={product.image}
                                                alt="Preview"
                                                className="h-16 w-16 object-cover rounded"
                                            />
                                        )}
                                        <input
                                            type="text"
                                            className="theme-input flex-1"
                                            value={product.image}
                                            onChange={(e) => updateProduct(index, 'image', e.target.value)}
                                            placeholder="https://example.com/image.jpg"
                                        />
                                    </div>
                                </div>

                                <div className="mb-3">
                                    <label className="theme-input-label pt-0 text-xs">Product Title</label>
                                    <input
                                        type="text"
                                        className="theme-input"
                                        value={product.title}
                                        onChange={(e) => updateProduct(index, 'title', e.target.value)}
                                        placeholder="e.g., Durable Denim Jeans"
                                    />
                                </div>

                                <div className="mb-3">
                                    <label className="theme-input-label pt-0 text-xs">Description</label>
                                    <textarea
                                        className="theme-input min-h-[60px]"
                                        value={product.description}
                                        onChange={(e) => updateProduct(index, 'description', e.target.value)}
                                        placeholder="e.g., We have prepared special discounts for you on"
                                    />
                                </div>

                                <div>
                                    <label className="theme-input-label pt-0 text-xs">Button Text</label>
                                    <input
                                        type="text"
                                        className="theme-input"
                                        value={product.button_text}
                                        onChange={(e) => updateProduct(index, 'button_text', e.target.value)}
                                        placeholder="e.g., BUY WITH CASH ON DELIVERY"
                                    />
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <div className="flex justify-end pt-4 border-t">
                <Button variant="primary" type="button" onClick={onSave} isLoading={saving}>
                    Save Section
                </Button>
            </div>
        </div>
    );
};

export default FeaturedProductsForm;
