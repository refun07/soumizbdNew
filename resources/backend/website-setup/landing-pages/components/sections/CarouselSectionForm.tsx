import React, { useState, useEffect, useCallback, useMemo } from 'react';
import { SectionFormData, PageConfigs } from '../../hooks/useBuilderState';
import Button from '../../../../react/components/inputs/Button';
import LayoutSettings from './LayoutSettings';
import { MediaManagerModal, MediaFile } from '../../../../react/components/gallery';
import { TextInputWithColor } from '../shared';

interface CarouselSectionFormProps {
    sectionId: number;
    landingPageId: number;
    sectionFormData: any;
    pageConfigs: PageConfigs;
    onUpdate?: () => void;
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

const CarouselSectionForm: React.FC<CarouselSectionFormProps> = ({
    sectionId,
    landingPageId,
    sectionFormData: initialData,
    pageConfigs,
    onUpdate,
    sectionOrder = 1,
    sectionIsVisible = true,
}) => {
    const [images, setImages] = useState<string[]>(initialData.carousel_image_urls || []);
    const [title, setTitle] = useState(initialData.carousel_title || '');
    const [titleColor, setTitleColor] = useState<string | null>(initialData.carousel_title_color || null);
    const [width, setWidth] = useState<'full' | 'medium' | 'minimum'>(initialData.width || 'minimum');
    const [backgroundColor, setBackgroundColor] = useState<string | null>(initialData.background_color || null);
    const [saving, setSaving] = useState(false);
    
    const [isMediaModalOpen, setIsMediaModalOpen] = useState(false);

    const bgColor = backgroundColor || pageConfigs.theme_color || '#1a472a';
    const computedTitleColor = useMemo(() => getContrastColor(bgColor), [bgColor]);

    const handleRemoveImage = useCallback((index: number) => {
        const newImages = images.filter((_, i) => i !== index);
        setImages(newImages);
    }, [images]);

    const openMediaManager = useCallback(() => {
        setIsMediaModalOpen(true);
    }, []);

    const handleMediaSelect = useCallback((files: MediaFile[]) => {
        const newPaths = files
            .map(file => {
                let path = file.media_file;
                if (path && !path.startsWith('/')) {
                    path = '/' + path;
                }
                return path;
            })
            .filter(path => !images.includes(path));
        
        setImages(prev => [...prev, ...newPaths]);
        setIsMediaModalOpen(false);
    }, [images]);

    const handleSave = async () => {
        if (images.length === 0) {
            alert('Please add at least one carousel image');
            return;
        }

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
                    type: 'carousel_images',
                    section_data: {
                        carousel_title: title,
                        carousel_image_urls: images,
                        carousel_title_color: titleColor,
                        width: width,
                        background_color: backgroundColor,
                    },
                    order: sectionOrder,
                    is_visible: sectionIsVisible,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to save carousel images');
            }

            onUpdate?.();

            if ((window as any).flash) {
                (window as any).flash('Carousel saved successfully', 'success');
            }
        } catch (error) {
            console.error('Error saving carousel images:', error);
            alert('Failed to save carousel images');
        } finally {
            setSaving(false);
        }
    };

    const sectionFormData: SectionFormData = {
        type: 'carousel_images',
        image_url: '',
        width,
        background_color: backgroundColor,
        carousel_title: title,
        carousel_image_urls: images,
        carousel_title_color: titleColor,
    };

    const handleLayoutChange = (data: SectionFormData) => {
        setWidth(data.width || 'minimum');
        setBackgroundColor(data.background_color || null);
    };

    return (
        <div className="space-y-6">
            <LayoutSettings
                data={sectionFormData}
                pageConfigs={pageConfigs}
                onChange={handleLayoutChange}
            />

            <TextInputWithColor
                label="Section Title"
                value={title}
                colorValue={titleColor}
                onChange={setTitle}
                onColorChange={setTitleColor}
                placeholder="Image Gallery"
                computedColor={computedTitleColor}
            />

            <div>
                <label className="theme-input-label pt-0">
                    Carousel Images <span className="text-red-500">*</span>
                </label>
                <div className="theme-input-wrapper">
                    <div className="flex flex-col gap-3">
                        {images.length > 0 && (
                            <div className="grid grid-cols-4 gap-3">
                                {images.map((imageUrl: string, index: number) => (
                                    <div key={index} className="relative group">
                                        <img
                                            src={imageUrl}
                                            alt={`Carousel image ${index + 1}`}
                                            className="h-20 w-full object-cover rounded"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => handleRemoveImage(index)}
                                            className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity"
                                            title="Remove image"
                                        >
                                            <i className="fa-solid fa-times text-xs"></i>
                                        </button>
                                    </div>
                                ))}
                            </div>
                        )}
                        <div className="flex items-center gap-3">
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
                                    {images.length > 0 ? `${images.length} file${images.length !== 1 ? 's' : ''}` : 'No file'}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <p className="text-xs text-gray-500 mt-1">
                    Click Browse to add images. Images will be appended to the list.
                </p>
                <p className="text-xs text-gray-500 mt-1">
                    Recommended: 1200x400 pixels
                </p>
            </div>

            <div className="flex justify-end pt-4">
                <Button variant="primary" type="button" onClick={handleSave} isLoading={saving}>
                    Save Section
                </Button>
            </div>

            <MediaManagerModal
                isOpen={isMediaModalOpen}
                onClose={() => setIsMediaModalOpen(false)}
                onSelect={handleMediaSelect}
                multiple={true}
                accept="image/*"
            />
        </div>
    );
};

export default CarouselSectionForm;
