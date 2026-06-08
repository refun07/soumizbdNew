import React, { useState, useMemo } from 'react';
import { SectionFormData } from '../../hooks/useBuilderState';
import LayoutSettings from './LayoutSettings';
import { TextInputWithColor, ButtonTextInput } from '../shared';

interface CenteredOfferHeroFormProps {
    sectionId: number;
    sectionFormData: SectionFormData;
    landingPageId: number;
    onUpdate: () => void;
    onClose: () => void;
    pageConfigs: { theme_color?: string; primary_color?: string };
    sectionOrder?: number;
    sectionIsVisible?: boolean;
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

const inputClass = "builder-input";
const labelClass = "builder-form-label";
const buttonPrimaryClass = "builder-btn builder-btn-primary w-full";

const CenteredOfferHeroForm: React.FC<CenteredOfferHeroFormProps> = ({ 
    sectionId, 
    sectionFormData, 
    landingPageId, 
    onUpdate, 
    onClose, 
    pageConfigs, 
    sectionOrder = 1, 
    sectionIsVisible = true 
}) => {
    const [localData, setLocalData] = useState<SectionFormData>({
        type: 'centered_offer_hero',
        offer_title: sectionFormData.offer_title || '',
        offer_subtitle: sectionFormData.offer_subtitle || '',
        offer_badge_text: sectionFormData.offer_badge_text || '',
        old_price: sectionFormData.old_price || '',
        new_price: sectionFormData.new_price || '',
        button_text: sectionFormData.button_text || '',
        background_color: sectionFormData.background_color || null,
        background_image_url: sectionFormData.background_image_url || null,
        background_image_position: sectionFormData.background_image_position || null,
        background_image_size: sectionFormData.background_image_size || null,
        background_image_repeat: sectionFormData.background_image_repeat || null,
        background_image_opacity: sectionFormData.background_image_opacity || null,
        background_image_overlay_color: sectionFormData.background_image_overlay_color || null,
        background_image_overlay_opacity: sectionFormData.background_image_overlay_opacity || null,
        background_image_parallax: sectionFormData.background_image_parallax || null,
        background_pattern: sectionFormData.background_pattern || null,
        image_url: sectionFormData.image_url || '',
        offer_title_color: sectionFormData.offer_title_color || null,
        offer_subtitle_color: sectionFormData.offer_subtitle_color || null,
        offer_badge_text_color: sectionFormData.offer_badge_text_color || null,
        old_price_color: sectionFormData.old_price_color || null,
        new_price_color: sectionFormData.new_price_color || null,
        centered_offer_button_text_color: sectionFormData.centered_offer_button_text_color || null,
        centered_offer_button_bg_color: sectionFormData.centered_offer_button_bg_color || null,
    });
    const [saving, setSaving] = useState(false);

    const bgColor = localData.background_color || pageConfigs.theme_color || '#1a472a';
    const primaryColor = pageConfigs.primary_color || '#E68A3D';
    
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);
    const computedSubtitleColor = useMemo(() => getSecondaryColor(bgColor), [bgColor]);
    const computedButtonTextColor = useMemo(() => getContrastColor(localData.centered_offer_button_bg_color || primaryColor), [localData.centered_offer_button_bg_color, primaryColor]);

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
                    type: 'centered_offer_hero',
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
            <LayoutSettings
                data={localData}
                pageConfigs={pageConfigs}
                onChange={(data) => setLocalData(data)}
            />

            <TextInputWithColor
                label="Offer Title"
                value={localData.offer_title || ''}
                colorValue={localData.offer_title_color || null}
                onChange={(value) => setLocalData({ ...localData, offer_title: value })}
                onColorChange={(color) => setLocalData({ ...localData, offer_title_color: color })}
                placeholder="Best Quality Products"
                computedColor={computedTitleColor}
            />

            <TextInputWithColor
                label="Subtitle"
                value={localData.offer_subtitle || ''}
                colorValue={localData.offer_subtitle_color || null}
                onChange={(value) => setLocalData({ ...localData, offer_subtitle: value })}
                onColorChange={(color) => setLocalData({ ...localData, offer_subtitle_color: color })}
                placeholder="We have prepared special discounts..."
                computedColor={computedSubtitleColor}
            />

            <TextInputWithColor
                label="Badge Text"
                value={localData.offer_badge_text || ''}
                colorValue={localData.offer_badge_text_color || null}
                onChange={(value) => setLocalData({ ...localData, offer_badge_text: value })}
                onColorChange={(color) => setLocalData({ ...localData, offer_badge_text_color: color })}
                placeholder="Limited Time Offer"
                computedColor={primaryColor}
            />

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <TextInputWithColor
                    label="Old Price"
                    value={localData.old_price || ''}
                    colorValue={localData.old_price_color || null}
                    onChange={(value) => setLocalData({ ...localData, old_price: value })}
                    onColorChange={(color) => setLocalData({ ...localData, old_price_color: color })}
                    placeholder="3000"
                    computedColor={computedSubtitleColor}
                />
                <TextInputWithColor
                    label="New Price"
                    value={localData.new_price || ''}
                    colorValue={localData.new_price_color || null}
                    onChange={(value) => setLocalData({ ...localData, new_price: value })}
                    onColorChange={(color) => setLocalData({ ...localData, new_price_color: color })}
                    placeholder="2500"
                    computedColor={computedTitleColor}
                />
            </div>

            <ButtonTextInput
                label="Button Text"
                value={localData.button_text || ''}
                textColor={localData.centered_offer_button_text_color || null}
                bgColor={localData.centered_offer_button_bg_color || null}
                onChange={(value) => setLocalData({ ...localData, button_text: value })}
                onTextColorChange={(color) => setLocalData({ ...localData, centered_offer_button_text_color: color })}
                onBgColorChange={(color) => setLocalData({ ...localData, centered_offer_button_bg_color: color })}
                placeholder="BUY WITH CASH ON DELIVERY"
                primaryColor={primaryColor}
                computedTextColor={computedButtonTextColor}
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

export default CenteredOfferHeroForm;
