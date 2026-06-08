import React, { useState, useEffect, useMemo } from 'react';
import { SectionFormData, PageConfigs } from '../../hooks/useBuilderState';
import Button from '../../../../react/components/inputs/Button';
import LayoutSettings from './LayoutSettings';
import { TextInputWithColor, TextareaWithColor, ButtonTextInput } from '../shared';

interface FaqSectionFormProps {
    sectionId: number;
    landingPageId: number;
    sectionFormData: SectionFormData;
    pageConfigs: PageConfigs;
    onUpdate?: () => void;
}

interface FaqItem {
    id: string;
    question: string;
    answer: string;
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

const FaqSectionForm: React.FC<FaqSectionFormProps> = ({
    sectionId,
    landingPageId,
    sectionFormData,
    pageConfigs,
    onUpdate,
}) => {
    const [localData, setLocalData] = useState<SectionFormData>({
        ...sectionFormData,
        type: 'faq_section',
        faq_title: sectionFormData.faq_title || '',
        faq_items: sectionFormData.faq_items || [],
        button_text: sectionFormData.button_text || '',
        faq_title_color: sectionFormData.faq_title_color || null,
        faq_question_color: sectionFormData.faq_question_color || null,
        faq_answer_color: sectionFormData.faq_answer_color || null,
        faq_button_text_color: sectionFormData.faq_button_text_color || null,
        faq_button_bg_color: sectionFormData.faq_button_bg_color || null,
    });
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        setLocalData(prev => ({
            ...prev,
            ...sectionFormData
        }));
    }, [sectionId, JSON.stringify(sectionFormData)]);

    const updateField = (field: string, value: any) => {
        setLocalData(prev => ({
            ...prev,
            [field]: value
        }));
    };

    const bgColor = localData.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedQuestionColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedAnswerColor = useMemo(() => '#1a1a1a', []);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.faq_button_bg_color || primaryColor), [localData.faq_button_bg_color, primaryColor]);

    const getFaqItems = (): FaqItem[] => {
        if (localData.faq_items && Array.isArray(localData.faq_items)) {
            return localData.faq_items as unknown as FaqItem[];
        }
        return [];
    };

    const addFaqItem = () => {
        const currentItems = getFaqItems();
        const newItem: FaqItem = {
            id: `faq-${Date.now()}`,
            question: '',
            answer: '',
        };
        updateField('faq_items', [...currentItems, newItem]);
    };

    const updateFaqItem = (index: number, field: keyof FaqItem, value: string) => {
        const currentItems = getFaqItems();
        const updatedItems = [...currentItems];
        updatedItems[index] = { ...updatedItems[index], [field]: value };
        updateField('faq_items', updatedItems);
    };

    const removeFaqItem = (index: number) => {
        const currentItems = getFaqItems();
        const updatedItems = currentItems.filter((_, i) => i !== index);
        updateField('faq_items', updatedItems);
    };

    const handleSave = async () => {
        setSaving(true);
        const payload = {
            type: 'faq_section',
            section_data: {
                faq_title: localData.faq_title || '',
                faq_items: localData.faq_items || [],
                button_text: localData.button_text || '',
                width: localData.width || 'minimum',
                background_color: localData.background_color || null,
                faq_title_color: localData.faq_title_color || null,
                faq_question_color: localData.faq_question_color || null,
                faq_answer_color: localData.faq_answer_color || null,
                faq_button_text_color: localData.faq_button_text_color || null,
                faq_button_bg_color: localData.faq_button_bg_color || null,
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

    const faqItems = getFaqItems();

    return (
        <div className="space-y-6">
            <LayoutSettings
                data={localData}
                pageConfigs={pageConfigs}
                onChange={(data) => setLocalData(data)}
            />

            <TextInputWithColor
                label="Section Title"
                value={localData.faq_title || ''}
                colorValue={localData.faq_title_color || null}
                onChange={(value) => updateField('faq_title', value)}
                onColorChange={(color) => updateField('faq_title_color', color)}
                placeholder="e.g., Frequently Asked Questions"
                computedColor={computedTitleColor}
            />

            <div className="border-t pt-4">
                <div className="flex items-center justify-between mb-4">
                    <h3 className="text-lg font-semibold">FAQ Items</h3>
                    <button
                        type="button"
                        onClick={addFaqItem}
                        className="btn btn-secondary btn-sm"
                    >
                        <i className="fa-solid fa-plus mr-1"></i>
                        Add FAQ Item
                    </button>
                </div>

                <div className="mb-4 p-3 bg-gray-50 rounded-lg border">
                    <p className="text-xs text-gray-600 mb-2">Colors apply to all FAQ items:</p>
                    <div className="flex gap-4">
                        <TextInputWithColor
                            label="Question Color"
                            value=""
                            colorValue={localData.faq_question_color || null}
                            onChange={() => {}}
                            onColorChange={(color) => updateField('faq_question_color', color)}
                            computedColor={computedQuestionColor}
                        />
                        <TextInputWithColor
                            label="Answer Color"
                            value=""
                            colorValue={localData.faq_answer_color || null}
                            onChange={() => {}}
                            onColorChange={(color) => updateField('faq_answer_color', color)}
                            computedColor={computedAnswerColor}
                        />
                    </div>
                </div>

                {faqItems.length === 0 ? (
                    <p className="text-gray-500 text-sm italic">No FAQ items added yet. Click "Add FAQ Item" to create one.</p>
                ) : (
                    <div className="space-y-4">
                        {faqItems.map((item, index) => (
                            <div key={item.id} className="bg-gray-50 p-4 rounded-lg border">
                                <div className="flex items-start justify-between mb-3">
                                    <span className="text-sm font-medium text-gray-700">FAQ #{index + 1}</span>
                                    <button
                                        type="button"
                                        onClick={() => removeFaqItem(index)}
                                        className="text-red-500 hover:text-red-700"
                                    >
                                        <i className="fa-solid fa-times"></i>
                                    </button>
                                </div>

                                <div className="mb-3">
                                    <label className="theme-input-label pt-0 text-xs">Question</label>
                                    <input
                                        type="text"
                                        className="theme-input"
                                        value={item.question}
                                        onChange={(e) => updateFaqItem(index, 'question', e.target.value)}
                                        placeholder="e.g., What is your return policy?"
                                    />
                                </div>

                                <div>
                                    <label className="theme-input-label pt-0 text-xs">Answer</label>
                                    <textarea
                                        className="theme-input min-h-[80px]"
                                        value={item.answer}
                                        onChange={(e) => updateFaqItem(index, 'answer', e.target.value)}
                                        placeholder="e.g., We offer a 30-day hassle-free return policy..."
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
                textColor={localData.faq_button_text_color || null}
                bgColor={localData.faq_button_bg_color || null}
                onChange={(value) => updateField('button_text', value)}
                onTextColorChange={(color) => updateField('faq_button_text_color', color)}
                onBgColorChange={(color) => updateField('faq_button_bg_color', color)}
                placeholder="e.g., BUY WITH CASH ON DELIVERY"
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

export default FaqSectionForm;
