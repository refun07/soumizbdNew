import React, { useState, useEffect, useMemo } from 'react';
import { SectionFormData, PageConfigs } from '../../hooks/useBuilderState';
import Button from '../../../../react/components/inputs/Button';
import LayoutSettings from './LayoutSettings';
import { MediaManagerModal, MediaFile } from '../../../../react/components/gallery';
import { TextInputWithColor, ButtonTextInput } from '../shared';

interface MultipleProductsFormProps {
    sectionId: number;
    sectionFormData: SectionFormData;
    pageConfigs: PageConfigs;
    onChange: (data: SectionFormData) => void;
    onSave: () => void;
    saving: boolean;
}

type ProductImageField = 'product1_image' | 'product2_image' | 'product3_image';

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

const MultipleProductsForm: React.FC<MultipleProductsFormProps> = ({
    sectionId,
    sectionFormData,
    pageConfigs,
    onChange,
    onSave,
    saving,
}) => {
    const [localData, setLocalData] = useState<SectionFormData>(sectionFormData);
    
    const [isMediaModalOpen, setIsMediaModalOpen] = useState(false);
    const [activeField, setActiveField] = useState<ProductImageField | null>(null);

    useEffect(() => {
        setLocalData(sectionFormData);
    }, [sectionFormData]);

    const updateField = (field: keyof SectionFormData, value: any) => {
        const newData = { ...localData, [field]: value };
        setLocalData(newData);
        onChange(newData);
    };

    const bgColor = localData.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedSubtitleColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButton1TextColor = useMemo(() => getContrastColor(localData.product1_button_bg_color || primaryColor), [localData.product1_button_bg_color, primaryColor]);
    const computedButton2TextColor = useMemo(() => getContrastColor(localData.product2_button_bg_color || primaryColor), [localData.product2_button_bg_color, primaryColor]);
    const computedButton3TextColor = useMemo(() => getContrastColor(localData.product3_button_bg_color || primaryColor), [localData.product3_button_bg_color, primaryColor]);

    const openMediaManager = (field: ProductImageField) => {
        setActiveField(field);
        setIsMediaModalOpen(true);
    };

    const handleMediaSelect = (files: MediaFile[]) => {
        if (activeField && files.length > 0) {
            const selectedFile = files[0];
            let path = selectedFile.media_file;
            if (path && !path.startsWith('/')) {
                path = '/' + path;
            }
            updateField(activeField, path);
        }
        setIsMediaModalOpen(false);
        setActiveField(null);
    };

    const handleRemoveImage = (field: ProductImageField) => {
        updateField(field, '');
    };

    const renderProductImageField = (field: ProductImageField, label: string) => {
        const imageUrl = localData[field] as string;
        
        return (
            <div>
                <label className="theme-input-label pt-0">{label} <span className="text-red-500">*</span></label>
                <div className="theme-input-wrapper">
                    <div className="flex items-center gap-3">
                        {imageUrl && (
                            <div className="relative">
                                <img
                                    src={imageUrl}
                                    alt="Preview"
                                    className="h-20 w-20 object-cover rounded"
                                />
                                <button
                                    type="button"
                                    onClick={() => handleRemoveImage(field)}
                                    className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600"
                                    title="Remove image"
                                >
                                    <i className="fa-solid fa-times"></i>
                                </button>
                            </div>
                        )}
                        <button
                            type="button"
                            onClick={(e) => {
                                e.preventDefault();
                                openMediaManager(field);
                            }}
                            className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                        >
                            <div className="px-4 py-2 bg-primary text-white rounded-md rounded-e-none text-sm">
                                Browse
                            </div>
                            <span className="media-count py-2 flex items-center text-muted text-sm">
                                {imageUrl ? '1 file' : 'No file'}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        );
    };

    return (
        <div className="space-y-6">
            <LayoutSettings
                data={localData}
                pageConfigs={pageConfigs}
                onChange={(data) => {
                    setLocalData(data);
                    onChange(data);
                }}
            />

            <div className="border-t pt-4">
                <h4 className="font-medium text-sm mb-4">Product 1</h4>
                {renderProductImageField('product1_image', 'Product 1 Image')}
                <p className="text-xs text-gray-500 mt-1">
                    Recommended: 300x300 pixels
                </p>
                <div className="mt-4">
                    <TextInputWithColor
                        label="Product 1 Title"
                        value={localData.product1_title || ''}
                        colorValue={localData.product1_title_color || null}
                        onChange={(value) => updateField('product1_title', value)}
                        onColorChange={(color) => updateField('product1_title_color', color)}
                        placeholder="Product 1 Title"
                        computedColor={computedTitleColor}
                    />
                </div>
                <div className="mt-4">
                    <TextInputWithColor
                        label="Product 1 Subtitle"
                        value={localData.product1_subtitle || ''}
                        colorValue={localData.product1_subtitle_color || null}
                        onChange={(value) => updateField('product1_subtitle', value)}
                        onColorChange={(color) => updateField('product1_subtitle_color', color)}
                        placeholder="Product 1 Subtitle"
                        computedColor={computedSubtitleColor}
                    />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <ButtonTextInput
                        label="Product 1 Button Text"
                        value={localData.product1_button_text || ''}
                        textColor={localData.product1_button_text_color || null}
                        bgColor={localData.product1_button_bg_color || null}
                        onChange={(value) => updateField('product1_button_text', value)}
                        onTextColorChange={(color) => updateField('product1_button_text_color', color)}
                        onBgColorChange={(color) => updateField('product1_button_bg_color', color)}
                        placeholder="BUY WITH CASH ON DELIVERY"
                        primaryColor={primaryColor}
                        computedTextColor={computedButton1TextColor}
                    />
                    <div>
                        <label className="theme-input-label pt-0">Product 1 Button URL</label>
                        <input
                            type="text"
                            className="theme-input"
                            value={localData.product1_button_url || ''}
                            onChange={(e) => updateField('product1_button_url', e.target.value)}
                            placeholder="#"
                        />
                    </div>
                </div>
            </div>

            <div className="border-t pt-4">
                <h4 className="font-medium text-sm mb-4">Product 2</h4>
                {renderProductImageField('product2_image', 'Product 2 Image')}
                <p className="text-xs text-gray-500 mt-1">
                    Recommended: 300x300 pixels
                </p>
                <div className="mt-4">
                    <TextInputWithColor
                        label="Product 2 Title"
                        value={localData.product2_title || ''}
                        colorValue={localData.product2_title_color || null}
                        onChange={(value) => updateField('product2_title', value)}
                        onColorChange={(color) => updateField('product2_title_color', color)}
                        placeholder="Product 2 Title"
                        computedColor={computedTitleColor}
                    />
                </div>
                <div className="mt-4">
                    <TextInputWithColor
                        label="Product 2 Subtitle"
                        value={localData.product2_subtitle || ''}
                        colorValue={localData.product2_subtitle_color || null}
                        onChange={(value) => updateField('product2_subtitle', value)}
                        onColorChange={(color) => updateField('product2_subtitle_color', color)}
                        placeholder="Product 2 Subtitle"
                        computedColor={computedSubtitleColor}
                    />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <ButtonTextInput
                        label="Product 2 Button Text"
                        value={localData.product2_button_text || ''}
                        textColor={localData.product2_button_text_color || null}
                        bgColor={localData.product2_button_bg_color || null}
                        onChange={(value) => updateField('product2_button_text', value)}
                        onTextColorChange={(color) => updateField('product2_button_text_color', color)}
                        onBgColorChange={(color) => updateField('product2_button_bg_color', color)}
                        placeholder="BUY WITH CASH ON DELIVERY"
                        primaryColor={primaryColor}
                        computedTextColor={computedButton2TextColor}
                    />
                    <div>
                        <label className="theme-input-label pt-0">Product 2 Button URL</label>
                        <input
                            type="text"
                            className="theme-input"
                            value={localData.product2_button_url || ''}
                            onChange={(e) => updateField('product2_button_url', e.target.value)}
                            placeholder="#"
                        />
                    </div>
                </div>
            </div>

            <div className="border-t pt-4">
                <h4 className="font-medium text-sm mb-4">Product 3</h4>
                {renderProductImageField('product3_image', 'Product 3 Image')}
                <p className="text-xs text-gray-500 mt-1">
                    Recommended: 300x300 pixels
                </p>
                <div className="mt-4">
                    <TextInputWithColor
                        label="Product 3 Title"
                        value={localData.product3_title || ''}
                        colorValue={localData.product3_title_color || null}
                        onChange={(value) => updateField('product3_title', value)}
                        onColorChange={(color) => updateField('product3_title_color', color)}
                        placeholder="Product 3 Title"
                        computedColor={computedTitleColor}
                    />
                </div>
                <div className="mt-4">
                    <TextInputWithColor
                        label="Product 3 Subtitle"
                        value={localData.product3_subtitle || ''}
                        colorValue={localData.product3_subtitle_color || null}
                        onChange={(value) => updateField('product3_subtitle', value)}
                        onColorChange={(color) => updateField('product3_subtitle_color', color)}
                        placeholder="Product 3 Subtitle"
                        computedColor={computedSubtitleColor}
                    />
                </div>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <ButtonTextInput
                        label="Product 3 Button Text"
                        value={localData.product3_button_text || ''}
                        textColor={localData.product3_button_text_color || null}
                        bgColor={localData.product3_button_bg_color || null}
                        onChange={(value) => updateField('product3_button_text', value)}
                        onTextColorChange={(color) => updateField('product3_button_text_color', color)}
                        onBgColorChange={(color) => updateField('product3_button_bg_color', color)}
                        placeholder="BUY WITH CASH ON DELIVERY"
                        primaryColor={primaryColor}
                        computedTextColor={computedButton3TextColor}
                    />
                    <div>
                        <label className="theme-input-label pt-0">Product 3 Button URL</label>
                        <input
                            type="text"
                            className="theme-input"
                            value={localData.product3_button_url || ''}
                            onChange={(e) => updateField('product3_button_url', e.target.value)}
                            placeholder="#"
                        />
                    </div>
                </div>
            </div>

            <div className="flex justify-end pt-4">
                <Button variant="primary" type="button" onClick={onSave} isLoading={saving}>
                    Save Section
                </Button>
            </div>

            <MediaManagerModal
                isOpen={isMediaModalOpen}
                onClose={() => {
                    setIsMediaModalOpen(false);
                    setActiveField(null);
                }}
                onSelect={handleMediaSelect}
                multiple={false}
                accept="image/*"
            />
        </div>
    );
};

export default MultipleProductsForm;
