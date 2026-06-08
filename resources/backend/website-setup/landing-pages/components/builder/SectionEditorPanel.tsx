import React, { useState, useMemo } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { LandingPageSection, SectionFormData } from '../../hooks/useBuilderState';

// Import existing form components
import CenteredProductOfferHeroForm from '../sections/CenteredProductOfferHeroForm';
import CenteredVideoHeroForm from '../sections/CenteredVideoHeroForm';
import CenteredOfferHeroForm from '../sections/CenteredOfferHeroForm';
import QualityProductsForm from '../sections/QualityProductsForm';
import QualityAboutForm from '../sections/QualityAboutForm';
import MultipleProductsForm from '../sections/MultipleProductsForm';
import CarouselSectionForm from '../sections/CarouselSectionForm';
import LayoutSettings from '../sections/LayoutSettings';

// Import Media Manager
import { MediaManagerModal, MediaFile } from '../../../../react/components/gallery';

// Import shared color input components
import { TextInputWithColor, ButtonTextInput } from '../shared';

interface SectionEditorPanelProps {
    isOpen: boolean;
    onClose: () => void;
    section: LandingPageSection | null;
    landingPageId: number;
    onUpdate: () => void;
    pageConfigs: { theme_color?: string; primary_color?: string };
}

const SectionEditorPanel: React.FC<SectionEditorPanelProps> = ({
    isOpen,
    onClose,
    section,
    landingPageId,
    onUpdate,
    pageConfigs,
}) => {
    const [formData, setFormData] = useState<SectionFormData | null>(null);
    const [saving, setSaving] = useState(false);

    if (!section) return null;

    const handleSave = async () => {
        if (!formData) return;
        
        setSaving(true);
        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPageId}/sections/${section.id}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    type: section.type,
                    section_data: formData,
                    order: section.order,
                    is_visible: section.is_visible,
                }),
            });

            if (!response.ok) throw new Error('Failed to save');
            
            onUpdate();
            onClose();
        } catch (err) {
            console.error('Error saving section:', err);
            if (window.flash) window.flash('Failed to save section', 'error');
        } finally {
            setSaving(false);
        }
    };

    const renderForm = () => {
        const initialData: SectionFormData = {
            type: section.type,
            image_url: '',
            ...section.section_data,
        };

        // Forms with onChange/onSave interface
        const onChangeForms = ['centered_product_offer_hero', 'centered_video_hero', 'quality_products', 'quality_about', 'multiple_products'];
        
        // Forms with landingPageId/onUpdate interface
        const onUpdateForms = ['carousel_images'];

        if (onChangeForms.includes(section.type)) {
            const commonProps = {
                sectionId: section.id,
                sectionFormData: formData || initialData,
                pageConfigs: pageConfigs,
                onChange: setFormData,
                onSave: handleSave,
                saving: saving,
            };

            switch (section.type) {
                case 'centered_product_offer_hero':
                    return <CenteredProductOfferHeroForm {...commonProps} />;
                case 'centered_video_hero':
                    return <CenteredVideoHeroForm {...commonProps} />;
                case 'quality_products':
                    return <QualityProductsForm {...commonProps} />;
                case 'quality_about':
                    return <QualityAboutForm {...commonProps} />;
                case 'multiple_products':
                    return <MultipleProductsForm {...commonProps} />;
            }
        }

        if (onUpdateForms.includes(section.type)) {
            const commonProps = {
                sectionId: section.id,
                landingPageId: landingPageId,
                sectionFormData: initialData,
                pageConfigs: pageConfigs,
                onUpdate: () => {
                    onUpdate();
                    onClose();
                },
                sectionOrder: section.order || 1,
                sectionIsVisible: section.is_visible ?? true,
            };

            switch (section.type) {
                case 'carousel_images':
                    return <CarouselSectionForm {...commonProps} />;
            }
        }

        // Inline forms for simpler sections
        const inlineProps = {
            sectionId: section.id,
            sectionFormData: initialData,
            landingPageId: landingPageId,
            onUpdate: onUpdate,
            onClose: onClose,
            pageConfigs: pageConfigs,
            sectionOrder: section.order || 1,
            sectionIsVisible: section.is_visible ?? true,
        };

        switch (section.type) {
            case 'centered_offer_hero':
                return <CenteredOfferHeroForm {...inlineProps} />;
            case 'product_benefits':
                return <ProductBenefitsForm {...inlineProps} />;
            case 'product_highlights':
                return <ProductHighlightsForm {...inlineProps} />;
            case 'faq_section':
                return <FaqSectionForm {...inlineProps} />;
            case 'featured_products':
                return <FeaturedProductsForm {...inlineProps} />;
            default:
                return (
                    <div className="p-6 text-center"
                        style={{ color: 'var(--builder-text-muted)' }}
                    >
                        <i className="fa-solid fa-circle-info text-3xl mb-3 block"></i>
                        <p>Form for {section.type} is not yet implemented.</p>
                    </div>
                );
        }
    };

    const getSectionLabel = (type: string) => {
        const labels: Record<string, string> = {
            'centered_product_offer_hero': 'Centered Product Offer Hero',
            'centered_video_hero': 'Centered Video Hero',
            'centered_offer_hero': 'Centered Offer Hero',
            'quality_products': 'Quality Products',
            'quality_about': 'Quality Products About',
            'multiple_products': 'Multiple Products',
            'product_benefits': 'Product Benefits',
            'product_highlights': 'Product Highlights',
            'carousel_images': 'Carousel Images',
            'faq_section': 'FAQ Section',
            'featured_products': 'Featured Products',
        };
        return labels[type] || type;
    };

    return (
        <AnimatePresence>
            {isOpen && (
                <>
                    {/* Backdrop */}
                    <motion.div
                        className="fixed inset-0 z-50"
                        style={{ backgroundColor: 'rgba(28, 25, 23, 0.4)' }}
                        initial={{ opacity: 0 }}
                        animate={{ opacity: 1 }}
                        exit={{ opacity: 0 }}
                        transition={{ duration: 0.2 }}
                        onClick={onClose}
                    />
                    
                    {/* Panel */}
                    <motion.div
                        className="fixed right-0 top-[64px] bottom-0 w-[480px] z-50 overflow-hidden flex flex-col"
                        style={{
                            backgroundColor: 'var(--builder-surface)',
                            borderLeft: '1px solid var(--builder-border)',
                        }}
                        initial={{ x: '100%' }}
                        animate={{ x: 0 }}
                        exit={{ x: '100%' }}
                        transition={{ type: 'spring', damping: 28, stiffness: 220 }}
                    >
                        {/* Header */}
                        <div className="p-5 border-b flex items-center justify-between"
                            style={{ borderColor: 'var(--builder-border)' }}
                        >
                            <div>
                                <h2 className="text-lg font-semibold"
                                    style={{ color: 'var(--builder-text)' }}
                                >
                                    Edit Section
                                </h2>
                                <p className="text-sm builder-mono mt-0.5"
                                    style={{ color: 'var(--builder-text-muted)' }}
                                >
                                    {getSectionLabel(section.type)}
                                </p>
                            </div>
                            
                            <button
                                onClick={onClose}
                                className="builder-btn builder-btn-icon builder-btn-ghost"
                                title="Close editor"
                            >
                                <i className="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        
                        {/* Form Content */}
                        <div className="flex-1 overflow-y-auto"
                        >
                            <div className="p-5"
                            >
                                {renderForm()}
                            </div>
                        </div>
                    </motion.div>
                </>
            )}
        </AnimatePresence>
    );
};

// Inline form components for simpler sections

interface InlineFormProps {
    sectionId: number;
    sectionFormData: SectionFormData;
    landingPageId: number;
    onUpdate: () => void;
    onClose: () => void;
    pageConfigs: { theme_color?: string; primary_color?: string };
    sectionOrder?: number;
    sectionIsVisible?: boolean;
}

const inputClass = "builder-input";
const labelClass = "builder-form-label";
const buttonPrimaryClass = "builder-btn builder-btn-primary w-full";

// Helper functions for color calculations
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

const ProductBenefitsForm: React.FC<InlineFormProps> = ({ sectionId, sectionFormData, landingPageId, onUpdate, onClose, pageConfigs, sectionOrder = 1, sectionIsVisible = true }) => {
    const [localData, setLocalData] = useState({
        benefits_title: sectionFormData.benefits_title || '',
        benefits_items: sectionFormData.benefits_items || [{ text: '' }],
        benefits_button_text: sectionFormData.benefits_button_text || '',
        benefits_button_url: sectionFormData.benefits_button_url || '',
        background_color: sectionFormData.background_color || null,
        benefits_title_color: sectionFormData.benefits_title_color || null,
        benefits_item_color: sectionFormData.benefits_item_color || null,
        benefits_button_text_color: sectionFormData.benefits_button_text_color || null,
        benefits_button_bg_color: sectionFormData.benefits_button_bg_color || null,
    });
    const [saving, setSaving] = useState(false);

    const bgColor = localData.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedItemColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.benefits_button_bg_color || primaryColor), [localData.benefits_button_bg_color, primaryColor]);

    const handleSave = async () => {
        setSaving(true);
        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPageId}/sections/${sectionId}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    type: 'product_benefits',
                    section_data: localData,
                    order: sectionOrder,
                    is_visible: sectionIsVisible,
                }),
            });

            if (!response.ok) throw new Error('Failed to save');
            onUpdate();
            onClose();
        } catch (err) {
            console.error('Error saving section:', err);
            if (window.flash) window.flash('Failed to save section', 'error');
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="space-y-5">
            <TextInputWithColor
                label="Section Title"
                value={localData.benefits_title}
                colorValue={localData.benefits_title_color}
                onChange={(value) => setLocalData({ ...localData, benefits_title: value })}
                onColorChange={(color) => setLocalData({ ...localData, benefits_title_color: color })}
                placeholder="Product Benefits"
                computedColor={computedTitleColor}
            />

            <div>
                <label className={labelClass}>Benefit Items</label>
                <div className="space-y-2">
                    {localData.benefits_items.map((item: any, idx: number) => (
                        <div key={idx} className="flex gap-2"
                        >
                            <input
                                type="text"
                                value={item.text}
                                onChange={(e) => {
                                    const newItems = [...localData.benefits_items];
                                    newItems[idx] = { text: e.target.value };
                                    setLocalData({ ...localData, benefits_items: newItems });
                                }}
                                className="flex-1 builder-input"
                                placeholder={`Benefit ${idx + 1}`}
                            />
                            <button
                                onClick={() => {
                                    const newItems = localData.benefits_items.filter((_, i) => i !== idx);
                                    setLocalData({ ...localData, benefits_items: newItems });
                                }}
                                className="builder-btn builder-btn-ghost builder-btn-icon"
                                style={{ color: 'var(--builder-error)' }}
                            >
                                <i className="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    ))}
                </div>
                
                <button
                    onClick={() => setLocalData({
                        ...localData,
                        benefits_items: [...localData.benefits_items, { text: '' }]
                    })}
                    className="builder-btn builder-btn-secondary builder-btn-sm mt-3"
                >
                    <i className="fa-solid fa-plus"></i>
                    Add Item
                </button>

                {/* Benefit Item Color Config */}
                <div className="mt-4 p-3 bg-gray-50 rounded-lg border">
                    <p className="text-xs text-gray-600 mb-2">Colors apply to all benefit items:</p>
                    <TextInputWithColor
                        label="Item Text Color"
                        value=""
                        colorValue={localData.benefits_item_color}
                        onChange={() => {}}
                        onColorChange={(color) => setLocalData({ ...localData, benefits_item_color: color })}
                        computedColor={computedItemColor}
                    />
                </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label className={labelClass}>Button URL</label>
                    <input
                        type="text"
                        value={localData.benefits_button_url}
                        onChange={(e) => setLocalData({ ...localData, benefits_button_url: e.target.value })}
                        className={inputClass}
                        placeholder="#"
                    />
                </div>
            </div>

            <ButtonTextInput
                label="Button Text"
                value={localData.benefits_button_text}
                textColor={localData.benefits_button_text_color}
                bgColor={localData.benefits_button_bg_color}
                onChange={(value) => setLocalData({ ...localData, benefits_button_text: value })}
                onTextColorChange={(color) => setLocalData({ ...localData, benefits_button_text_color: color })}
                onBgColorChange={(color) => setLocalData({ ...localData, benefits_button_bg_color: color })}
                placeholder="SHOP NOW"
                primaryColor={primaryColor}
                computedTextColor={computedButtonTextColor}
            />

            {/* Layout Settings */}
            <LayoutSettings
                data={localData as unknown as SectionFormData}
                pageConfigs={pageConfigs}
                onChange={(data) => setLocalData({ ...localData, background_color: data.background_color || null })}
            />

            <button
                onClick={handleSave}
                disabled={saving}
                className={buttonPrimaryClass}
            >
                {saving ? (
                    <>
                        <span className="spinner" style={{ width: '16px', height: '16px', borderWidth: '2px' }}></span>
                        <span>Saving...</span>
                    </>
                ) : (
                    <span>Save Section</span>
                )}
            </button>
        </div>
    );
};

const ProductHighlightsForm: React.FC<InlineFormProps> = ({ sectionId, sectionFormData, landingPageId, onUpdate, onClose, pageConfigs, sectionOrder = 1, sectionIsVisible = true }) => {
    const [localData, setLocalData] = useState({
        benefits_title: sectionFormData.benefits_title || '',
        button_text: sectionFormData.button_text || '',
        benefits_items: sectionFormData.benefits_items || [],
        background_color: sectionFormData.background_color || null,
        highlights_title_color: sectionFormData.highlights_title_color || null,
        benefit_title_color: sectionFormData.benefit_title_color || null,
        benefit_description_color: sectionFormData.benefit_description_color || null,
        highlights_button_text_color: sectionFormData.highlights_button_text_color || null,
        highlights_button_bg_color: sectionFormData.highlights_button_bg_color || null,
    });
    const [saving, setSaving] = useState(false);

    const bgColor = localData.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedBenefitTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedBenefitDescColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.highlights_button_bg_color || primaryColor), [localData.highlights_button_bg_color, primaryColor]);

    const handleSave = async () => {
        setSaving(true);
        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPageId}/sections/${sectionId}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    type: 'product_highlights',
                    section_data: localData,
                    order: sectionOrder,
                    is_visible: sectionIsVisible,
                }),
            });

            if (!response.ok) throw new Error('Failed to save');
            onUpdate();
            onClose();
        } catch (err) {
            console.error('Error saving section:', err);
            if (window.flash) window.flash('Failed to save section', 'error');
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="space-y-5">
            <TextInputWithColor
                label="Section Title"
                value={localData.benefits_title}
                colorValue={localData.highlights_title_color}
                onChange={(value) => setLocalData({ ...localData, benefits_title: value })}
                onColorChange={(color) => setLocalData({ ...localData, highlights_title_color: color })}
                placeholder="Product Highlights"
                computedColor={computedTitleColor}
            />

            <div className="mb-4 p-3 bg-gray-50 rounded-lg border">
                <p className="text-xs text-gray-600 mb-2">Colors apply to all highlights:</p>
                <div className="grid grid-cols-2 gap-3">
                    <TextInputWithColor
                        label="Title Color"
                        value=""
                        colorValue={localData.benefit_title_color}
                        onChange={() => {}}
                        onColorChange={(color) => setLocalData({ ...localData, benefit_title_color: color })}
                        computedColor={computedBenefitTitleColor}
                    />
                    <TextInputWithColor
                        label="Description Color"
                        value=""
                        colorValue={localData.benefit_description_color}
                        onChange={() => {}}
                        onColorChange={(color) => setLocalData({ ...localData, benefit_description_color: color })}
                        computedColor={computedBenefitDescColor}
                    />
                </div>
            </div>

            <ButtonTextInput
                label="Button Text"
                value={localData.button_text}
                textColor={localData.highlights_button_text_color}
                bgColor={localData.highlights_button_bg_color}
                onChange={(value) => setLocalData({ ...localData, button_text: value })}
                onTextColorChange={(color) => setLocalData({ ...localData, highlights_button_text_color: color })}
                onBgColorChange={(color) => setLocalData({ ...localData, highlights_button_bg_color: color })}
                placeholder="BUY WITH CASH ON DELIVERY"
                primaryColor={primaryColor}
                computedTextColor={computedButtonTextColor}
            />

            {/* Layout Settings */}
            <LayoutSettings
                data={localData as unknown as SectionFormData}
                pageConfigs={pageConfigs}
                onChange={(data) => setLocalData({ ...localData, background_color: data.background_color || null })}
            />

            <button
                onClick={handleSave}
                disabled={saving}
                className={buttonPrimaryClass}
            >
                {saving ? (
                    <>
                        <span className="spinner" style={{ width: '16px', height: '16px', borderWidth: '2px' }}></span>
                        <span>Saving...</span>
                    </>
                ) : (
                    <span>Save Section</span>
                )}
            </button>
        </div>
    );
};

const FaqSectionForm: React.FC<InlineFormProps> = ({ sectionId, sectionFormData, landingPageId, onUpdate, onClose, pageConfigs, sectionOrder = 1, sectionIsVisible = true }) => {
    const [localData, setLocalData] = useState({
        faq_title: sectionFormData.faq_title || '',
        button_text: sectionFormData.button_text || '',
        faq_items: sectionFormData.faq_items || [],
        background_color: sectionFormData.background_color || null,
        faq_title_color: sectionFormData.faq_title_color || null,
        faq_question_color: sectionFormData.faq_question_color || null,
        faq_answer_color: sectionFormData.faq_answer_color || null,
        faq_button_text_color: sectionFormData.faq_button_text_color || null,
        faq_button_bg_color: sectionFormData.faq_button_bg_color || null,
    });
    const [saving, setSaving] = useState(false);

    const bgColor = localData.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedQuestionColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedAnswerColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.faq_button_bg_color || primaryColor), [localData.faq_button_bg_color, primaryColor]);

    const handleSave = async () => {
        setSaving(true);
        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPageId}/sections/${sectionId}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    type: 'faq_section',
                    section_data: localData,
                    order: sectionOrder,
                    is_visible: sectionIsVisible,
                }),
            });

            if (!response.ok) throw new Error('Failed to save');
            onUpdate();
            onClose();
        } catch (err) {
            console.error('Error saving section:', err);
            if (window.flash) window.flash('Failed to save section', 'error');
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="space-y-5">
            <TextInputWithColor
                label="FAQ Title"
                value={localData.faq_title}
                colorValue={localData.faq_title_color}
                onChange={(value) => setLocalData({ ...localData, faq_title: value })}
                onColorChange={(color) => setLocalData({ ...localData, faq_title_color: color })}
                placeholder="Frequently Asked Questions"
                computedColor={computedTitleColor}
            />

            <div>
                <label className={labelClass}>FAQ Items</label>
                
                {/* FAQ Color Config */}
                <div className="mb-4 p-3 bg-gray-50 rounded-lg border">
                    <p className="text-xs text-gray-600 mb-2">Colors apply to all FAQ items:</p>
                    <div className="flex gap-4">
                        <TextInputWithColor
                            label="Question Color"
                            value=""
                            colorValue={localData.faq_question_color}
                            onChange={() => {}}
                            onColorChange={(color) => setLocalData({ ...localData, faq_question_color: color })}
                            computedColor={computedQuestionColor}
                        />
                        <TextInputWithColor
                            label="Answer Color"
                            value=""
                            colorValue={localData.faq_answer_color}
                            onChange={() => {}}
                            onColorChange={(color) => setLocalData({ ...localData, faq_answer_color: color })}
                            computedColor={computedAnswerColor}
                        />
                    </div>
                </div>

                <div className="space-y-3">
                    {localData.faq_items.map((item: any, idx: number) => (
                        <div 
                            key={idx} 
                            className="p-4 rounded-lg border"
                            style={{ 
                                backgroundColor: 'var(--builder-surface-hover)',
                                borderColor: 'var(--builder-border)',
                            }}
                        >
                            <input
                                type="text"
                                value={item.question}
                                onChange={(e) => {
                                    const newItems = [...localData.faq_items];
                                    newItems[idx] = { ...newItems[idx], question: e.target.value };
                                    setLocalData({ ...localData, faq_items: newItems });
                                }}
                                className="builder-input mb-2"
                                placeholder="Question"
                            />
                            <textarea
                                value={item.answer}
                                onChange={(e) => {
                                    const newItems = [...localData.faq_items];
                                    newItems[idx] = { ...newItems[idx], answer: e.target.value };
                                    setLocalData({ ...localData, faq_items: newItems });
                                }}
                                className="builder-input-textarea"
                                placeholder="Answer"
                                rows={3}
                            />
                            <button
                                onClick={() => {
                                    const newItems = localData.faq_items.filter((_, i) => i !== idx);
                                    setLocalData({ ...localData, faq_items: newItems });
                                }}
                                className="builder-btn builder-btn-ghost builder-btn-sm mt-2"
                                style={{ color: 'var(--builder-error)' }}
                            >
                                <i className="fa-solid fa-trash mr-1"></i>
                                Remove
                            </button>
                        </div>
                    ))}
                </div>
                
                <button
                    onClick={() => setLocalData({
                        ...localData,
                        faq_items: [...localData.faq_items, { id: `faq-${Date.now()}`, question: '', answer: '' }]
                    })}
                    className="builder-btn builder-btn-secondary builder-btn-sm mt-3"
                >
                    <i className="fa-solid fa-plus"></i>
                    Add FAQ
                </button>
            </div>

            <ButtonTextInput
                label="CTA Button Text"
                value={localData.button_text}
                textColor={localData.faq_button_text_color}
                bgColor={localData.faq_button_bg_color}
                onChange={(value) => setLocalData({ ...localData, button_text: value })}
                onTextColorChange={(color) => setLocalData({ ...localData, faq_button_text_color: color })}
                onBgColorChange={(color) => setLocalData({ ...localData, faq_button_bg_color: color })}
                placeholder="BUY WITH CASH ON DELIVERY"
                primaryColor={primaryColor}
                computedTextColor={computedButtonTextColor}
            />

            {/* Layout Settings */}
            <LayoutSettings
                data={localData as unknown as SectionFormData}
                pageConfigs={pageConfigs}
                onChange={(data) => setLocalData({ ...localData, background_color: data.background_color || null })}
            />

            <button
                onClick={handleSave}
                disabled={saving}
                className={buttonPrimaryClass}
            >
                {saving ? (
                    <>
                        <span className="spinner" style={{ width: '16px', height: '16px', borderWidth: '2px' }}></span>
                        <span>Saving...</span>
                    </>
                ) : (
                    <span>Save Section</span>
                )}
            </button>
        </div>
    );
};

const FeaturedProductsForm: React.FC<InlineFormProps> = ({ sectionId, sectionFormData, landingPageId, onUpdate, onClose, pageConfigs, sectionOrder = 1, sectionIsVisible = true }) => {
    const [localData, setLocalData] = useState({
        section_title: sectionFormData.section_title || '',
        featured_products: sectionFormData.featured_products || [],
        background_color: sectionFormData.background_color || null,
        featured_section_title_color: sectionFormData.featured_section_title_color || null,
        featured_product_title_color: sectionFormData.featured_product_title_color || null,
        featured_product_description_color: sectionFormData.featured_product_description_color || null,
        featured_button_text_color: sectionFormData.featured_button_text_color || null,
        featured_button_bg_color: sectionFormData.featured_button_bg_color || null,
    });
    const [saving, setSaving] = useState(false);
    
    const bgColor = localData.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedProductTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedProductDescColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.featured_button_bg_color || primaryColor), [localData.featured_button_bg_color, primaryColor]);
    
    // Media Manager modal state
    const [isMediaModalOpen, setIsMediaModalOpen] = useState(false);
    const [activeProductIndex, setActiveProductIndex] = useState<number | null>(null);

    const handleSave = async () => {
        setSaving(true);
        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPageId}/sections/${sectionId}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    type: 'featured_products',
                    section_data: localData,
                    order: sectionOrder,
                    is_visible: sectionIsVisible,
                }),
            });

            if (!response.ok) throw new Error('Failed to save');
            onUpdate();
            onClose();
        } catch (err) {
            console.error('Error saving section:', err);
            if ((window as any).flash) (window as any).flash('Failed to save section', 'error');
        } finally {
            setSaving(false);
        }
    };

    const openMediaManager = (productIndex: number) => {
        setActiveProductIndex(productIndex);
        setIsMediaModalOpen(true);
    };

    const handleMediaSelect = (files: MediaFile[]) => {
        if (activeProductIndex !== null && files.length > 0) {
            const selectedFile = files[0];
            let path = selectedFile.media_file;
            if (path && !path.startsWith('/')) {
                path = '/' + path;
            }
            
            const newProducts = [...localData.featured_products];
            newProducts[activeProductIndex] = { 
                ...newProducts[activeProductIndex], 
                image: path 
            };
            setLocalData({ ...localData, featured_products: newProducts });
        }
        setIsMediaModalOpen(false);
        setActiveProductIndex(null);
    };

    const handleRemoveImage = (productIndex: number) => {
        const newProducts = [...localData.featured_products];
        newProducts[productIndex] = { 
            ...newProducts[productIndex], 
            image: '' 
        };
        setLocalData({ ...localData, featured_products: newProducts });
    };

    return (
        <div className="space-y-5">
            <TextInputWithColor
                label="Section Title"
                value={localData.section_title}
                colorValue={localData.featured_section_title_color}
                onChange={(value) => setLocalData({ ...localData, section_title: value })}
                onColorChange={(color) => setLocalData({ ...localData, featured_section_title_color: color })}
                placeholder="Featured Products"
                computedColor={computedTitleColor}
            />

            <div>
                <label className={labelClass}>Products</label>
                
                {/* Product Color Config */}
                <div className="mb-4 p-3 bg-gray-50 rounded-lg border">
                    <p className="text-xs text-gray-600 mb-2">Colors apply to all products:</p>
                    <div className="grid grid-cols-3 gap-3">
                        <TextInputWithColor
                            label="Title Color"
                            value=""
                            colorValue={localData.featured_product_title_color}
                            onChange={() => {}}
                            onColorChange={(color) => setLocalData({ ...localData, featured_product_title_color: color })}
                            computedColor={computedProductTitleColor}
                        />
                        <TextInputWithColor
                            label="Description Color"
                            value=""
                            colorValue={localData.featured_product_description_color}
                            onChange={() => {}}
                            onColorChange={(color) => setLocalData({ ...localData, featured_product_description_color: color })}
                            computedColor={computedProductDescColor}
                        />
                        <ButtonTextInput
                            label="Button Colors"
                            value=""
                            textColor={localData.featured_button_text_color}
                            bgColor={localData.featured_button_bg_color}
                            onChange={() => {}}
                            onTextColorChange={(color) => setLocalData({ ...localData, featured_button_text_color: color })}
                            onBgColorChange={(color) => setLocalData({ ...localData, featured_button_bg_color: color })}
                            primaryColor={primaryColor}
                            computedTextColor={computedButtonTextColor}
                        />
                    </div>
                </div>
                <div className="space-y-3">
                    {localData.featured_products.map((product: any, idx: number) => (
                        <div 
                            key={idx} 
                            className="p-4 rounded-lg border"
                            style={{ 
                                backgroundColor: 'var(--builder-surface-hover)',
                                borderColor: 'var(--builder-border)',
                            }}
                        >
                            {/* Product Image */}
                            <div className="mb-3">
                                <label className={`${labelClass} text-xs`}>Product Image</label>
                                <div className="flex items-center gap-3 mt-1">
                                    {product.image && (
                                        <div className="relative">
                                            <img
                                                src={product.image}
                                                alt="Product Preview"
                                                className="h-16 w-16 object-cover rounded"
                                            />
                                            <button
                                                type="button"
                                                onClick={() => handleRemoveImage(idx)}
                                                className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600"
                                                title="Remove image"
                                            >
                                                <i className="fa-solid fa-times"></i>
                                            </button>
                                        </div>
                                    )}
                                    <button
                                        type="button"
                                        onClick={() => openMediaManager(idx)}
                                        className="builder-btn builder-btn-secondary builder-btn-sm"
                                    >
                                        <i className="fa-solid fa-image mr-1"></i>
                                        {product.image ? 'Change Image' : 'Select Image'}
                                    </button>
                                </div>
                            </div>

                            <input
                                type="text"
                                value={product.title}
                                onChange={(e) => {
                                    const newProducts = [...localData.featured_products];
                                    newProducts[idx] = { ...newProducts[idx], title: e.target.value };
                                    setLocalData({ ...localData, featured_products: newProducts });
                                }}
                                className="builder-input mb-2"
                                placeholder="Product Title"
                            />
                            <textarea
                                value={product.description}
                                onChange={(e) => {
                                    const newProducts = [...localData.featured_products];
                                    newProducts[idx] = { ...newProducts[idx], description: e.target.value };
                                    setLocalData({ ...localData, featured_products: newProducts });
                                }}
                                className="builder-input-textarea mb-2"
                                placeholder="Description"
                                rows={2}
                            />
                            <input
                                type="text"
                                value={product.button_text}
                                onChange={(e) => {
                                    const newProducts = [...localData.featured_products];
                                    newProducts[idx] = { ...newProducts[idx], button_text: e.target.value };
                                    setLocalData({ ...localData, featured_products: newProducts });
                                }}
                                className="builder-input"
                                placeholder="Button Text"
                            />
                            
                            <button
                                onClick={() => {
                                    const newProducts = localData.featured_products.filter((_, i) => i !== idx);
                                    setLocalData({ ...localData, featured_products: newProducts });
                                }}
                                className="builder-btn builder-btn-ghost builder-btn-sm mt-2"
                                style={{ color: 'var(--builder-error)' }}
                            >
                                <i className="fa-solid fa-trash mr-1"></i>
                                Remove Product
                            </button>
                        </div>
                    ))}
                </div>
                
                <button
                    onClick={() => setLocalData({
                        ...localData,
                        featured_products: [...localData.featured_products, { id: `product-${Date.now()}`, title: '', description: '', image: '', button_text: '' }]
                    })}
                    className="builder-btn builder-btn-secondary builder-btn-sm mt-3"
                >
                    <i className="fa-solid fa-plus"></i>
                    Add Product
                </button>
            </div>

            {/* Layout Settings */}
            <LayoutSettings
                data={localData as unknown as SectionFormData}
                pageConfigs={pageConfigs}
                onChange={(data) => setLocalData({ ...localData, background_color: data.background_color || null })}
            />

            <button
                onClick={handleSave}
                disabled={saving}
                className={buttonPrimaryClass}
            >
                {saving ? (
                    <>
                        <span className="spinner" style={{ width: '16px', height: '16px', borderWidth: '2px' }}></span>
                        <span>Saving...</span>
                    </>
                ) : (
                    <span>Save Section</span>
                )}
            </button>

            {/* Media Manager Modal */}
            <MediaManagerModal
                isOpen={isMediaModalOpen}
                onClose={() => {
                    setIsMediaModalOpen(false);
                    setActiveProductIndex(null);
                }}
                onSelect={handleMediaSelect}
                multiple={false}
                accept="image/*"
            />
        </div>
    );
};

export default SectionEditorPanel;
