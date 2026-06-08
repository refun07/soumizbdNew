import React, { useState, useEffect, useMemo } from 'react';
import { SectionFormData, PageConfigs } from '../../hooks/useBuilderState';
import Button from '../../../../react/components/inputs/Button';
import LayoutSettings from './LayoutSettings';
import { MediaManagerModal, MediaFile } from '../../../../react/components/gallery';
import { TextInputWithColor, TextareaWithColor, ButtonTextInput } from '../shared';

interface QualityAboutFormProps {
    sectionId: number;
    sectionFormData: SectionFormData;
    pageConfigs: PageConfigs;
    onChange: (data: SectionFormData) => void;
    onSave: () => void;
    saving: boolean;
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

const QualityAboutForm: React.FC<QualityAboutFormProps> = ({
    sectionId,
    sectionFormData,
    pageConfigs,
    onChange,
    onSave,
    saving,
}) => {
    const [localData, setLocalData] = useState<SectionFormData>(sectionFormData);
    const [isMediaModalOpen, setIsMediaModalOpen] = useState(false);

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
    const computedDescColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.quality_about_button_bg_color || primaryColor), [localData.quality_about_button_bg_color, primaryColor]);

    const openMediaManager = () => {
        setIsMediaModalOpen(true);
    };

    const handleMediaSelect = (files: MediaFile[]) => {
        if (files.length > 0) {
            const selectedFile = files[0];
            let path = selectedFile.media_file;
            if (path && !path.startsWith('/')) {
                path = '/' + path;
            }
            updateField('quality_about_image_url', path);
        }
        setIsMediaModalOpen(false);
    };

    const handleRemoveImage = () => {
        updateField('quality_about_image_url', '');
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

            <TextInputWithColor
                label="Section Title"
                value={localData.quality_about_title || ''}
                colorValue={localData.quality_about_title_color || null}
                onChange={(value) => updateField('quality_about_title', value)}
                onColorChange={(color) => updateField('quality_about_title_color', color)}
                placeholder="Best Quality Products"
                computedColor={computedTitleColor}
            />

            <TextareaWithColor
                label="Description"
                value={localData.quality_about_description || ''}
                colorValue={localData.quality_about_description_color || null}
                onChange={(value) => updateField('quality_about_description', value)}
                onColorChange={(color) => updateField('quality_about_description_color', color)}
                placeholder="Enter description about your products..."
                rows={4}
                computedColor={computedDescColor}
            />

            <div>
                <label className="theme-input-label pt-0">
                    Product Image <span className="text-red-500">*</span>
                </label>
                <div className="theme-input-wrapper">
                    <div className="flex items-center gap-3">
                        {localData.quality_about_image_url && (
                            <div className="relative">
                                <img
                                    src={localData.quality_about_image_url as string}
                                    alt="Preview"
                                    className="h-20 w-20 object-cover rounded"
                                />
                                <button
                                    type="button"
                                    onClick={handleRemoveImage}
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
                                openMediaManager();
                            }}
                            className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                        >
                            <div className="px-4 py-2 bg-primary text-white rounded-md rounded-e-none text-sm">
                                Browse
                            </div>
                            <span className="media-count py-2 flex items-center text-muted text-sm">
                                {localData.quality_about_image_url ? '1 file' : 'No file'}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <p className="text-xs text-gray-500 mt-1">
                Recommended: 500x500 pixels
            </p>

            <div className="border-t pt-4">
                <h4 className="font-medium text-sm mb-4">Button Settings</h4>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <ButtonTextInput
                        label="Button Text"
                        value={localData.quality_about_button_text || ''}
                        textColor={localData.quality_about_button_text_color || null}
                        bgColor={localData.quality_about_button_bg_color || null}
                        onChange={(value) => updateField('quality_about_button_text', value)}
                        onTextColorChange={(color) => updateField('quality_about_button_text_color', color)}
                        onBgColorChange={(color) => updateField('quality_about_button_bg_color', color)}
                        placeholder="BUY WITH CASH ON DELIVERY"
                        primaryColor={primaryColor}
                        computedTextColor={computedButtonTextColor}
                    />
                    <div>
                        <label className="theme-input-label pt-0">Button URL</label>
                        <input
                            type="text"
                            className="theme-input"
                            value={localData.quality_about_button_url || ''}
                            onChange={(e) => updateField('quality_about_button_url', e.target.value)}
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
                onClose={() => setIsMediaModalOpen(false)}
                onSelect={handleMediaSelect}
                multiple={false}
                accept="image/*"
            />
        </div>
    );
};

export default QualityAboutForm;
