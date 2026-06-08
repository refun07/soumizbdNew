import React, { useState, useEffect, useMemo } from 'react';
import { SectionFormData, PageConfigs } from '../../hooks/useBuilderState';
import Button from '../../../../react/components/inputs/Button';
import LayoutSettings from './LayoutSettings';
import { MediaManagerModal, MediaFile } from '../../../../react/components/gallery';
import { TextInputWithColor, ButtonTextInput } from '../shared';

interface CenteredProductOfferHeroFormProps {
    sectionId: number;
    sectionFormData: SectionFormData;
    pageConfigs: PageConfigs;
    onChange: (data: SectionFormData) => void;
    onSave: () => void;
    saving: boolean;
}

type ImageField = 'brand_logo_url' | 'product_image_url';

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

const CenteredProductOfferHeroForm: React.FC<CenteredProductOfferHeroFormProps> = ({
    sectionId,
    sectionFormData,
    pageConfigs,
    onChange,
    onSave,
    saving,
}) => {
    const [localData, setLocalData] = useState<SectionFormData>(sectionFormData);
    
    const [isMediaModalOpen, setIsMediaModalOpen] = useState(false);
    const [activeField, setActiveField] = useState<ImageField | null>(null);

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
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.product_button_bg_color || primaryColor), [localData.product_button_bg_color, primaryColor]);

    const openMediaManager = (field: ImageField) => {
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

    const handleRemoveImage = (field: ImageField) => {
        updateField(field, '');
    };

    return (
        <div className="space-y-4">
            <LayoutSettings
                data={localData}
                pageConfigs={pageConfigs}
                onChange={(data) => {
                    setLocalData(data);
                    onChange(data);
                }}
            />

            <div>
                <label className="theme-input-label pt-0">Brand Logo</label>
                <div className="theme-input-wrapper">
                    <div className="flex items-center gap-3">
                        {localData.brand_logo_url && (
                            <div className="relative">
                                <img
                                    src={localData.brand_logo_url}
                                    alt="Brand Logo Preview"
                                    className="h-10 w-auto object-contain"
                                />
                                <button
                                    type="button"
                                    onClick={() => handleRemoveImage('brand_logo_url')}
                                    className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600"
                                    title="Remove logo"
                                >
                                    <i className="fa-solid fa-times"></i>
                                </button>
                            </div>
                        )}
                        <button
                            type="button"
                            onClick={() => openMediaManager('brand_logo_url')}
                            className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                        >
                            <div className="px-6 py-3 bg-primary text-white rounded-md rounded-e-none">
                                Browse
                            </div>
                            <span className="media-count py-2 flex items-center text-muted">
                                {localData.brand_logo_url ? '1 file selected' : 'No Selected Item'}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <p className="text-xs text-gray-500 mt-1">
                Recommended: 150x40 pixels
            </p>

            <TextInputWithColor
                label="Offer Badge Text"
                value={localData.product_offer_badge_text || ''}
                colorValue={localData.product_offer_badge_text_color || null}
                onChange={(value) => updateField('product_offer_badge_text', value)}
                onColorChange={(color) => updateField('product_offer_badge_text_color', color)}
                placeholder="e.g., Limited Time Offer"
                computedColor={primaryColor}
            />

            <TextInputWithColor
                label="Product Title"
                value={localData.product_title || ''}
                colorValue={localData.product_title_color || null}
                onChange={(value) => updateField('product_title', value)}
                onColorChange={(color) => updateField('product_title_color', color)}
                placeholder="e.g., Best Quality Products"
                computedColor={computedTitleColor}
            />

            <TextInputWithColor
                label="Product Subtitle"
                value={localData.product_subtitle || ''}
                colorValue={localData.product_subtitle_color || null}
                onChange={(value) => updateField('product_subtitle', value)}
                onColorChange={(color) => updateField('product_subtitle_color', color)}
                placeholder="e.g., We have prepared special discounts for you"
                computedColor={computedSubtitleColor}
            />

            <div className="space-y-4">
                <div>
                    <label className="theme-input-label pt-0">Product Image</label>
                    <div className="theme-input-wrapper">
                        <div className="flex items-center gap-3">
                            {localData.product_image_url && (
                                <div className="relative">
                                    <img
                                        src={localData.product_image_url}
                                        alt="Preview"
                                        className="h-16 w-16 object-cover rounded"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => handleRemoveImage('product_image_url')}
                                        className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600"
                                        title="Remove image"
                                    >
                                        <i className="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            )}
                            <button
                                type="button"
                                onClick={() => openMediaManager('product_image_url')}
                                className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                            >
                                <div className="px-6 py-3 bg-primary text-white rounded-md rounded-e-none">
                                    Browse
                                </div>
                                <span className="media-count py-2 flex items-center text-muted">
                                    {localData.product_image_url ? '1 file selected' : 'No Selected Item'}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <p className="text-xs text-gray-500 mt-1">
                    Recommended: 300x300 pixels
                </p>

                <div className="text-center text-sm text-muted">- OR -</div>

                <div>
                    <label className="theme-input-label pt-0">YouTube Video URL</label>
                    <input
                        type="url"
                        className="theme-input"
                        value={localData.product_video_url || ''}
                        onChange={(e) => updateField('product_video_url', e.target.value)}
                        placeholder="https://www.youtube.com/watch?v=..."
                    />
                </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <TextInputWithColor
                    label="Old Price"
                    value={localData.product_old_price || ''}
                    colorValue={localData.product_old_price_color || null}
                    onChange={(value) => updateField('product_old_price', value)}
                    onColorChange={(color) => updateField('product_old_price_color', color)}
                    placeholder="e.g., 3000Tk"
                    computedColor={computedSubtitleColor}
                />
                <TextInputWithColor
                    label="New Price"
                    value={localData.product_new_price || ''}
                    colorValue={localData.product_new_price_color || null}
                    onChange={(value) => updateField('product_new_price', value)}
                    onColorChange={(color) => updateField('product_new_price_color', color)}
                    placeholder="e.g., 2500Tk"
                    required
                    computedColor={computedTitleColor}
                />
            </div>

            <ButtonTextInput
                label="Button Text"
                value={localData.product_button_text || ''}
                textColor={localData.product_button_text_color || null}
                bgColor={localData.product_button_bg_color || null}
                onChange={(value) => updateField('product_button_text', value)}
                onTextColorChange={(color) => updateField('product_button_text_color', color)}
                onBgColorChange={(color) => updateField('product_button_bg_color', color)}
                placeholder="e.g., BUY WITH CASH ON DELIVERY"
                primaryColor={primaryColor}
                computedTextColor={computedButtonTextColor}
            />

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

export default CenteredProductOfferHeroForm;
