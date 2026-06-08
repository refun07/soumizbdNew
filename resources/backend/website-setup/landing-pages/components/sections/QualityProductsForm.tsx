import React, { useState, useEffect, useMemo } from 'react';
import { SectionFormData, PageConfigs } from '../../hooks/useBuilderState';
import Button from '../../../../react/components/inputs/Button';
import LayoutSettings from './LayoutSettings';
import { MediaManagerModal, MediaFile } from '../../../../react/components/gallery';
import { TextInputWithColor, ButtonTextInput } from '../shared';

interface QualityProductsFormProps {
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

const QualityProductsForm: React.FC<QualityProductsFormProps> = ({
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
    const computedFeatureTextColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.quality_button_bg_color || primaryColor), [localData.quality_button_bg_color, primaryColor]);

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
            updateField('quality_image_url', path);
        }
        setIsMediaModalOpen(false);
    };

    const handleRemoveImage = () => {
        updateField('quality_image_url', '');
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
                value={localData.quality_title || ''}
                colorValue={localData.quality_title_color || null}
                onChange={(value) => updateField('quality_title', value)}
                onColorChange={(color) => updateField('quality_title_color', color)}
                placeholder="Best Quality Products"
                computedColor={computedTitleColor}
            />

            <div>
                <label className="theme-input-label pt-0">
                    Product Image <span className="text-red-500">*</span>
                </label>
                <div className="theme-input-wrapper">
                    <div className="flex items-center gap-3">
                        {localData.quality_image_url && (
                            <div className="relative">
                                <img
                                    src={localData.quality_image_url as string}
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
                                {localData.quality_image_url ? '1 file' : 'No file'}
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <p className="text-xs text-gray-500 mt-1">
                Recommended: 300x300 pixels
            </p>

            <div className="mb-4 p-3 bg-gray-50 rounded-lg border">
                <p className="text-xs text-gray-600 mb-2">Color applies to all features:</p>
                <TextInputWithColor
                    label="Feature Text Color"
                    value=""
                    colorValue={localData.feature_text_color || null}
                    onChange={() => {}}
                    onColorChange={(color) => updateField('feature_text_color', color)}
                    computedColor={computedFeatureTextColor}
                />
            </div>

            <div className="space-y-4">
                <h4 className="font-medium text-sm">Feature Items</h4>
                {[1, 2, 3, 4].map((itemNum) => {
                    const iconKey = `item${itemNum}_icon` as keyof SectionFormData;
                    const textKey = `item${itemNum}_text` as keyof SectionFormData;

                    return (
                        <div key={itemNum} className="border rounded-lg p-4 bg-gray-50">
                            <h5 className="font-medium text-sm mb-3">Feature {itemNum}</h5>

                            <div className="mb-3">
                                <label className="theme-input-label pt-0">SVG Icon</label>
                                <textarea
                                    className="theme-input font-mono text-sm"
                                    rows={4}
                                    value={(localData[iconKey] as string) || ''}
                                    onChange={(e) => updateField(iconKey, e.target.value)}
                                    placeholder={`<svg>...</svg>`}
                                />
                                {localData[iconKey] && (
                                    <div className="mt-2 p-3 bg-white rounded border">
                                        <div
                                            className="flex items-center justify-center h-10"
                                            dangerouslySetInnerHTML={{ __html: localData[iconKey] as string }}
                                        />
                                    </div>
                                )}
                            </div>

                            <div>
                                <label className="theme-input-label pt-0">Feature Text</label>
                                <input
                                    type="text"
                                    className="theme-input"
                                    value={(localData[textKey] as string) || ''}
                                    onChange={(e) => updateField(textKey, e.target.value)}
                                    placeholder={`Feature ${itemNum} text`}
                                />
                            </div>
                        </div>
                    );
                })}
            </div>

            <div className="border-t pt-4">
                <h4 className="font-medium text-sm mb-4">Button Settings</h4>
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <ButtonTextInput
                        label="Button Text"
                        value={localData.button_text || ''}
                        textColor={localData.quality_button_text_color || null}
                        bgColor={localData.quality_button_bg_color || null}
                        onChange={(value) => updateField('button_text', value)}
                        onTextColorChange={(color) => updateField('quality_button_text_color', color)}
                        onBgColorChange={(color) => updateField('quality_button_bg_color', color)}
                        placeholder="BUY WITH CASH ON DELIVERY"
                        primaryColor={primaryColor}
                        computedTextColor={computedButtonTextColor}
                    />
                    <div>
                        <label className="theme-input-label pt-0">Button URL</label>
                        <input
                            type="text"
                            className="theme-input"
                            value={localData.button_url || ''}
                            onChange={(e) => updateField('button_url', e.target.value)}
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

export default QualityProductsForm;
