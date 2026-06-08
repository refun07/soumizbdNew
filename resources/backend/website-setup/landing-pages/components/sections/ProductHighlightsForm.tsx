import React, { useState, useEffect, useMemo } from 'react';
import { SectionFormData, PageConfigs } from '../../hooks/useBuilderState';
import Button from '../../../../react/components/inputs/Button';
import LayoutSettings from './LayoutSettings';
import { TextInputWithColor, TextareaWithColor, ButtonTextInput } from '../shared';

interface ProductHighlightsFormProps {
    sectionId: number;
    landingPageId: number;
    sectionFormData: SectionFormData;
    pageConfigs: PageConfigs;
    onUpdate?: () => void;
}

interface BenefitItem {
    id: string;
    icon: string;
    title: string;
    description: string;
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

const ProductHighlightsForm: React.FC<ProductHighlightsFormProps> = ({
    sectionId,
    landingPageId,
    sectionFormData,
    pageConfigs,
    onUpdate,
}) => {
    const [localData, setLocalData] = useState<SectionFormData>(sectionFormData);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        setLocalData(sectionFormData);
    }, [sectionFormData]);

    const updateField = (field: string, value: any) => {
        const newData = { ...localData, [field]: value };
        setLocalData(newData);
    };

    const bgColor = localData.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedBenefitTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedBenefitDescColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.highlights_button_bg_color || primaryColor), [localData.highlights_button_bg_color, primaryColor]);

    const getBenefits = (): BenefitItem[] => {
        if (localData.benefits_items && Array.isArray(localData.benefits_items)) {
            return localData.benefits_items as unknown as BenefitItem[];
        }
        return [];
    };

    const addBenefit = () => {
        const currentBenefits = getBenefits();
        const newBenefit: BenefitItem = {
            id: `benefit-${Date.now()}`,
            icon: '',
            title: '',
            description: '',
        };
        updateField('benefits_items', [...currentBenefits, newBenefit]);
    };

    const updateBenefit = (index: number, field: keyof BenefitItem, value: string) => {
        const currentBenefits = getBenefits();
        const updatedBenefits = [...currentBenefits];
        updatedBenefits[index] = { ...updatedBenefits[index], [field]: value };
        updateField('benefits_items', updatedBenefits);
    };

    const removeBenefit = (index: number) => {
        const currentBenefits = getBenefits();
        const updatedBenefits = currentBenefits.filter((_, i) => i !== index);
        updateField('benefits_items', updatedBenefits);
    };

    const handleSave = async () => {
        setSaving(true);
        const payload = {
            type: 'product_highlights',
            section_data: {
                benefits_title: localData.benefits_title || localData.section_title || '',
                button_text: localData.button_text || '',
                benefits_items: localData.benefits_items || [],
                width: localData.width || 'minimum',
                background_color: localData.background_color || null,
                highlights_title_color: localData.highlights_title_color || null,
                benefit_title_color: localData.benefit_title_color || null,
                benefit_description_color: localData.benefit_description_color || null,
                highlights_button_text_color: localData.highlights_button_text_color || null,
                highlights_button_bg_color: localData.highlights_button_bg_color || null,
            },
            order: (localData as any).order || 1,
            is_visible: (localData as any).is_visible ?? true,
        };
        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPageId}/sections/${sectionId}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error('Failed to save section');
            }

            onUpdate?.();

            if (window.flash) {
                window.flash('Section saved successfully', 'success');
            }
        } catch (error) {
            console.error('Error saving section:', error);
            alert('Failed to save section');
        } finally {
            setSaving(false);
        }
    };

    const benefits = getBenefits();

    return (
        <div className="space-y-6">
            <LayoutSettings
                data={localData}
                pageConfigs={pageConfigs}
                onChange={(data) => setLocalData(data)}
            />

            <TextInputWithColor
                label="Section Title"
                value={localData.benefits_title || localData.section_title || ''}
                colorValue={localData.highlights_title_color || null}
                onChange={(value) => updateField('benefits_title', value)}
                onColorChange={(color) => updateField('highlights_title_color', color)}
                placeholder="e.g., Product Highlights"
                computedColor={computedTitleColor}
            />

            <div className="mb-4 p-3 bg-gray-50 rounded-lg border">
                <p className="text-xs text-gray-600 mb-2">Colors apply to all benefits:</p>
                <div className="grid grid-cols-2 gap-3">
                    <TextInputWithColor
                        label="Title Color"
                        value=""
                        colorValue={localData.benefit_title_color || null}
                        onChange={() => {}}
                        onColorChange={(color) => updateField('benefit_title_color', color)}
                        computedColor={computedBenefitTitleColor}
                    />
                    <TextInputWithColor
                        label="Description Color"
                        value=""
                        colorValue={localData.benefit_description_color || null}
                        onChange={() => {}}
                        onColorChange={(color) => updateField('benefit_description_color', color)}
                        computedColor={computedBenefitDescColor}
                    />
                </div>
            </div>

            <div className="border-t pt-4">
                <div className="flex items-center justify-between mb-4">
                    <h3 className="text-lg font-semibold">Benefits Grid (3x3)</h3>
                    <button
                        type="button"
                        onClick={addBenefit}
                        className="btn btn-secondary btn-sm"
                    >
                        <i className="fa-solid fa-plus mr-1"></i>
                        Add Benefit
                    </button>
                </div>

                {benefits.length === 0 ? (
                    <p className="text-gray-500 text-sm italic">No benefits added yet. Click "Add Benefit" to create one.</p>
                ) : (
                    <div className="grid grid-cols-1 gap-4">
                        {benefits.map((benefit, index) => (
                            <div key={benefit.id} className="bg-gray-50 p-4 rounded-lg border">
                                <div className="flex items-start justify-between mb-3">
                                    <span className="text-sm font-medium text-gray-700">Benefit #{index + 1}</span>
                                    <button
                                        type="button"
                                        onClick={() => removeBenefit(index)}
                                        className="text-red-500 hover:text-red-700"
                                    >
                                        <i className="fa-solid fa-times"></i>
                                    </button>
                                </div>

                                <div className="mb-3">
                                    <label className="theme-input-label pt-0 text-xs">Icon SVG Code</label>
                                    <div className="flex items-center gap-2 mb-2">
                                        <div
                                            className="w-10 h-10 rounded-full flex items-center justify-center"
                                            style={{ backgroundColor: '#E68A3D' }}
                                            dangerouslySetInnerHTML={{ __html: benefit.icon || '' }}
                                        />
                                        <span className="text-xs text-gray-500">Preview</span>
                                    </div>
                                    <input
                                        type="text"
                                        className="theme-input"
                                        value={benefit.icon || ''}
                                        onChange={(e) => updateBenefit(index, 'icon', e.target.value)}
                                        placeholder="Paste SVG code here..."
                                    />
                                </div>

                                <div className="mb-3">
                                    <label className="theme-input-label pt-0 text-xs">Title</label>
                                    <input
                                        type="text"
                                        className="theme-input"
                                        value={benefit.title}
                                        onChange={(e) => updateBenefit(index, 'title', e.target.value)}
                                        placeholder="e.g., Premium Quality"
                                    />
                                </div>

                                <div>
                                    <label className="theme-input-label pt-0 text-xs">Description</label>
                                    <textarea
                                        className="theme-input min-h-[60px]"
                                        value={benefit.description}
                                        onChange={(e) => updateBenefit(index, 'description', e.target.value)}
                                        placeholder="e.g., Made with premium materials for lasting durability"
                                    />
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <ButtonTextInput
                label="CTA Button Text"
                value={localData.button_text || ''}
                textColor={localData.highlights_button_text_color || null}
                bgColor={localData.highlights_button_bg_color || null}
                onChange={(value) => updateField('button_text', value)}
                onTextColorChange={(color) => updateField('highlights_button_text_color', color)}
                onBgColorChange={(color) => updateField('highlights_button_bg_color', color)}
                placeholder="e.g., SHOP NOW"
                primaryColor={primaryColor}
                computedTextColor={computedButtonTextColor}
            />

            <div className="flex justify-end pt-4 border-t">
                <Button variant="primary" type="button" onClick={handleSave} isLoading={saving}>
                    Save Section
                </Button>
            </div>
        </div>
    );
};

export default ProductHighlightsForm;
