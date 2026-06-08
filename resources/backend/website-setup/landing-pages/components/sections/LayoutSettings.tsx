import React, { useState } from 'react';
import { SectionFormData, PageConfigs } from '../../hooks/useBuilderState';
import ColorPicker from '../../../header/components/ColorPicker';
import { MediaManagerModal, MediaFile } from '../../../../react/components/gallery';

interface LayoutSettingsProps {
    data: SectionFormData;
    pageConfigs: PageConfigs;
    onChange: (data: SectionFormData) => void;
}

const PRESET_PATTERNS = [
    { id: 'none', name: 'None', preview: 'transparent' },
    { id: 'dots', name: 'Dots', preview: 'radial-gradient(circle, rgba(255,255,255,0.15) 1px, transparent 1px)', size: '20px 20px' },
    { id: 'grid', name: 'Grid', preview: 'linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px)', size: '30px 30px' },
    { id: 'diagonal-lines', name: 'Diagonal Lines', preview: 'repeating-linear-gradient(45deg, rgba(255,255,255,0.1) 0px, rgba(255,255,255,0.1) 2px, transparent 2px, transparent 10px)' },
    { id: 'waves', name: 'Waves', preview: 'repeating-radial-gradient(circle at 0 0, transparent 0, rgba(255,255,255,0.05) 10px), repeating-linear-gradient(rgba(255,255,255,0.05), rgba(255,255,255,0.05))' },
    { id: 'hexagons', name: 'Hexagons', preview: 'conic-gradient(from 60deg at 56.25% calc(425%/6), rgba(255,255,255,0.1) 120deg, transparent 0%), conic-gradient(from 180deg at 43.75% calc(425%/6), rgba(255,255,255,0.1) 120deg, transparent 0%), conic-gradient(from 300deg at calc(100%/12) 50%, rgba(255,255,255,0.1) 60deg, transparent 0%)', size: '40px 35px' },
    { id: 'dots-dark', name: 'Dots (Dark)', preview: 'radial-gradient(circle, rgba(0,0,0,0.15) 1px, transparent 1px)', size: '20px 20px' },
    { id: 'grid-dark', name: 'Grid (Dark)', preview: 'linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px)', size: '30px 30px' },
];

const POSITION_OPTIONS = [
    { value: 'top-left', label: 'Top Left', icon: 'M3 3h4v4H3V3z' },
    { value: 'top-center', label: 'Top Center', icon: 'M10 3h4v4h-4V3z' },
    { value: 'top-right', label: 'Top Right', icon: 'M17 3h4v4h-4V3z' },
    { value: 'center-left', label: 'Center Left', icon: 'M3 10h4v4H3v-4z' },
    { value: 'center', label: 'Center', icon: 'M10 10h4v4h-4v-4z' },
    { value: 'center-right', label: 'Center Right', icon: 'M17 10h4v4h-4v-4z' },
    { value: 'bottom-left', label: 'Bottom Left', icon: 'M3 17h4v4H3v-4z' },
    { value: 'bottom-center', label: 'Bottom Center', icon: 'M10 17h4v4h-4v-4z' },
    { value: 'bottom-right', label: 'Bottom Right', icon: 'M17 17h4v4h-4v-4z' },
];

const SIZE_OPTIONS = [
    { value: 'cover', label: 'Cover (Fill Section)', description: 'Image covers entire section' },
    { value: 'contain', label: 'Contain (Fit Inside)', description: 'Image fits within section' },
    { value: '100%', label: 'Stretch (100% Width)', description: 'Image stretches to full width' },
    { value: '100% 100%', label: 'Stretch Both', description: 'Image stretches to fill entirely' },
    { value: 'auto', label: 'Original Size', description: 'Image at original dimensions' },
];

const LayoutSettings: React.FC<LayoutSettingsProps> = ({ data, pageConfigs, onChange }) => {
    const [isExpanded, setIsExpanded] = useState(false);
    const [isMediaModalOpen, setIsMediaModalOpen] = useState(false);

    const updateField = (field: keyof SectionFormData, value: any) => {
        const newData = { ...data, [field]: value };
        onChange(newData);
    };

    const hasCustomBgColor = data.background_color && data.background_color !== '';
    const hasBackgroundImage = data.background_image_url && data.background_image_url !== '';
    const hasBackgroundPattern = data.background_pattern && data.background_pattern !== '' && data.background_pattern !== 'none';
    const pageBgColor = pageConfigs.theme_color || '#1a472a';

    const handleMediaSelect = (files: MediaFile[]) => {
        if (files.length > 0) {
            const selectedFile = files[0];
            let path = selectedFile.media_file;
            if (path && !path.startsWith('/')) {
                path = '/' + path;
            }
            
            const newData = { 
                ...data, 
                background_image_url: path,
                background_image_position: data.background_image_position || 'center',
                background_image_size: data.background_image_size || 'cover',
                background_image_repeat: data.background_image_repeat || 'no-repeat',
                background_image_opacity: data.background_image_opacity ?? 100
            };
            onChange(newData);
        }
        setIsMediaModalOpen(false);
    };

    const handleRemoveImage = () => {
        updateField('background_image_url', null);
    };

    const isModified = hasCustomBgColor || hasBackgroundImage || hasBackgroundPattern;

    return (
        <div className="border border-[var(--builder-border)] rounded-lg mb-6 bg-[var(--builder-surface)]">
            <button
                type="button"
                onClick={() => setIsExpanded(!isExpanded)}
                className="w-full px-4 py-3 flex items-center justify-between hover:bg-[var(--builder-surface-hover)] transition-colors duration-200"
            >
                <h4 className="text-sm font-semibold text-[var(--builder-text)] flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                        <line x1="3" y1="9" x2="21" y2="9"/>
                        <line x1="9" y1="21" x2="9" y2="9"/>
                    </svg>
                    Layout Settings
                    {isModified && (
                        <span className="bg-[var(--builder-primary)] text-gray-900 text-[10px] px-2 py-0.5 rounded-full font-medium">
                            Modified
                        </span>
                    )}
                </h4>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="16"
                    height="16"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className={`text-[var(--builder-text-secondary)] transition-transform duration-200 ${isExpanded ? 'rotate-180' : ''}`}
                >
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>

            <div className={`transition-all duration-200 ease-in-out overflow-hidden ${isExpanded ? 'max-h-[2000px] opacity-100' : 'max-h-0 opacity-0'}`}>
                <div className="px-4 pb-4 pt-2 border-t border-[var(--builder-border)] space-y-6">
                    
                    <div>
                        <label className="block text-xs font-medium text-[var(--builder-text-secondary)] mb-2">
                            Background Color
                        </label>
                        <ColorPicker
                            value={data.background_color || ''}
                            onChange={(color) => updateField('background_color', color || null)}
                        />
                        
                        {!hasCustomBgColor && (
                            <div className="mt-2 flex items-center gap-2 text-xs text-[var(--builder-text-secondary)]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    <path d="m9 12 2 2 4-4"/>
                                </svg>
                                <span>
                                    Using page-level background color: 
                                    <span 
                                        className="inline-block w-3 h-3 rounded-sm ml-1 align-middle border border-gray-300" 
                                        style={{ backgroundColor: pageBgColor }}
                                    />
                                    <span className="ml-1 font-mono text-[10px]">{pageBgColor}</span>
                                </span>
                            </div>
                        )}
                        
                        {hasCustomBgColor && (
                            <div className="mt-2 flex items-center gap-2 text-xs text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span>Using custom background color</span>
                                <button
                                    type="button"
                                    onClick={() => updateField('background_color', null)}
                                    className="ml-2 text-[var(--builder-text-secondary)] hover:text-red-500 underline"
                                >
                                    Reset to page default
                                </button>
                            </div>
                        )}
                    </div>

                    <div className="border-t border-[var(--builder-border)] pt-4">
                        <label className="block text-xs font-medium text-[var(--builder-text-secondary)] mb-3">
                            Background Image
                        </label>
                        
                        <div className="mb-4">
                            {hasBackgroundImage ? (
                                <div className="relative group">
                                    <div className="relative h-32 rounded-lg overflow-hidden border border-[var(--builder-border)] bg-gray-100">
                                        <img
                                            src={data.background_image_url!}
                                            alt="Background"
                                            className="w-full h-full object-cover"
                                            onError={(e) => {
                                                console.error('Image failed to load:', data.background_image_url);
                                                (e.target as HTMLImageElement).style.display = 'none';
                                            }}
                                        />
                                        <div className="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                            <button
                                                type="button"
                                                onClick={() => setIsMediaModalOpen(true)}
                                                className="px-3 py-1.5 bg-white text-gray-900 rounded-md text-xs font-medium hover:bg-gray-100"
                                            >
                                                Change
                                            </button>
                                            <button
                                                type="button"
                                                onClick={handleRemoveImage}
                                                className="px-3 py-1.5 bg-red-500 text-white rounded-md text-xs font-medium hover:bg-red-600"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <button
                                    type="button"
                                    onClick={() => setIsMediaModalOpen(true)}
                                    className="w-full h-32 border-2 border-dashed border-[var(--builder-border)] rounded-lg flex flex-col items-center justify-center gap-2 hover:border-[var(--builder-primary)] hover:bg-[var(--builder-surface-hover)] transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="text-[var(--builder-text-secondary)]">
                                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                        <circle cx="8.5" cy="8.5" r="1.5"/>
                                        <polyline points="21 15 16 10 5 21"/>
                                    </svg>
                                    <span className="text-sm text-[var(--builder-text-secondary)]">Click to upload background image</span>
                                    <span className="text-xs text-[var(--builder-text-muted)]">Supports: JPG, PNG, SVG, WebP</span>
                                </button>
                            )}
                        </div>

                        {hasBackgroundImage && (
                            <div className="space-y-4">
                                <div>
                                    <label className="block text-xs font-medium text-[var(--builder-text-secondary)] mb-2">
                                        Image Position
                                    </label>
                                    <div className="grid grid-cols-3 gap-2 w-fit p-2 bg-[var(--builder-surface-hover)] rounded-lg border border-[var(--builder-border)]">
                                        {POSITION_OPTIONS.map((pos) => {
                                            const isSelected = data.background_image_position === pos.value;
                                            return (
                                                <button
                                                    key={pos.value}
                                                    type="button"
                                                    onClick={() => updateField('background_image_position', pos.value as any)}
                                                    className={`w-10 h-10 flex items-center justify-center rounded-md transition-all duration-200 ${
                                                        isSelected
                                                            ? 'bg-[var(--builder-primary)] text-white shadow-md ring-2 ring-[var(--builder-primary)] ring-offset-1 ring-offset-[var(--builder-surface-hover)]'
                                                            : 'hover:bg-[var(--builder-surface)] text-[var(--builder-text-secondary)] hover:text-[var(--builder-text)]'
                                                    }`}
                                                    title={pos.label}
                                                >
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" className={isSelected ? 'opacity-100' : 'opacity-70'}>
                                                        <path d={pos.icon} />
                                                    </svg>
                                                </button>
                                            );
                                        })}
                                    </div>
                                </div>

                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <label className="block text-xs font-medium text-[var(--builder-text-secondary)] mb-1">
                                            Image Size
                                        </label>
                                        <select
                                            value={data.background_image_size || 'cover'}
                                            onChange={(e) => updateField('background_image_size', e.target.value as any)}
                                            className="w-full text-xs bg-[var(--builder-surface-hover)] border border-[var(--builder-border)] rounded-md px-2 py-1.5 text-[var(--builder-text)] focus:outline-none focus:border-[var(--builder-primary)]"
                                        >
                                            {SIZE_OPTIONS.map((opt) => (
                                                <option key={opt.value} value={opt.value}>{opt.label}</option>
                                            ))}
                                        </select>
                                    </div>

                                    <div>
                                        <label className="block text-xs font-medium text-[var(--builder-text-secondary)] mb-1">
                                            Repeat
                                        </label>
                                        <select
                                            value={data.background_image_repeat || 'no-repeat'}
                                            onChange={(e) => updateField('background_image_repeat', e.target.value as any)}
                                            className="w-full text-xs bg-[var(--builder-surface-hover)] border border-[var(--builder-border)] rounded-md px-2 py-1.5 text-[var(--builder-text)] focus:outline-none focus:border-[var(--builder-primary)]"
                                        >
                                            <option value="no-repeat">No Repeat</option>
                                            <option value="repeat">Repeat</option>
                                            <option value="repeat-x">Repeat X</option>
                                            <option value="repeat-y">Repeat Y</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <div className="flex items-center justify-between mb-1">
                                        <label className="text-xs font-medium text-[var(--builder-text-secondary)]">
                                            Image Opacity
                                        </label>
                                        <span className="text-xs text-[var(--builder-text-muted)]">
                                            {data.background_image_opacity ?? 100}%
                                        </span>
                                    </div>
                                    <input
                                        type="range"
                                        min="0"
                                        max="100"
                                        value={data.background_image_opacity ?? 100}
                                        onChange={(e) => updateField('background_image_opacity', parseInt(e.target.value))}
                                        className="w-full h-1.5 bg-[var(--builder-border)] rounded-lg appearance-none cursor-pointer accent-[var(--builder-primary)]"
                                    />
                                </div>

                                <div className="border-t border-[var(--builder-border)] pt-3">
                                    <label className="block text-xs font-medium text-[var(--builder-text-secondary)] mb-2">
                                        Color Overlay (Optional)
                                    </label>
                                    <ColorPicker
                                        value={data.background_image_overlay_color || ''}
                                        onChange={(color) => updateField('background_image_overlay_color', color || null)}
                                    />
                                    
                                    {data.background_image_overlay_color && (
                                        <div className="mt-2">
                                            <div className="flex items-center justify-between mb-1">
                                                <span className="text-xs text-[var(--builder-text-secondary)]">Overlay Opacity</span>
                                                <span className="text-xs text-[var(--builder-text-muted)]">
                                                    {data.background_image_overlay_opacity ?? 50}%
                                                </span>
                                            </div>
                                            <input
                                                type="range"
                                                min="0"
                                                max="100"
                                                value={data.background_image_overlay_opacity ?? 50}
                                                onChange={(e) => updateField('background_image_overlay_opacity', parseInt(e.target.value))}
                                                className="w-full h-1.5 bg-[var(--builder-border)] rounded-lg appearance-none cursor-pointer accent-[var(--builder-primary)]"
                                            />
                                        </div>
                                    )}
                                </div>

                                <div className="flex items-center justify-between pt-3 border-t border-[var(--builder-border)]">
                                    <div className="flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" className="text-[var(--builder-text-secondary)]">
                                            <path d="M12 2v4"/>
                                            <path d="m16.2 7.8 2.9-2.9"/>
                                            <path d="M18 12h4"/>
                                            <path d="m16.2 16.2 2.9 2.9"/>
                                            <path d="M12 18v4"/>
                                            <path d="m4.9 19.1 2.9-2.9"/>
                                            <path d="M2 12h4"/>
                                            <path d="m4.9 4.9 2.9 2.9"/>
                                        </svg>
                                        <span className="text-xs font-medium text-[var(--builder-text)]">Parallax Effect</span>
                                    </div>
                                    <label className="relative inline-flex items-center cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={data.background_image_parallax || false}
                                            onChange={(e) => updateField('background_image_parallax', e.target.checked)}
                                            className="sr-only peer"
                                        />
                                        <div className={`
                                            w-11 h-6 rounded-full transition-all duration-300 ease-in-out
                                            ${data.background_image_parallax 
                                                ? 'bg-[var(--builder-primary)] shadow-inner' 
                                                : 'bg-[var(--builder-border)]'}
                                        `}>
                                            <div className={`
                                                absolute top-1 left-1 bg-white rounded-full shadow-md transition-all duration-300 ease-in-out
                                                ${data.background_image_parallax ? 'translate-x-5 w-4 h-4' : 'w-4 h-4'}
                                            `}>
                                                {data.background_image_parallax && (
                                                    <svg className="w-3 h-3 text-[var(--builder-primary)] absolute top-0.5 left-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3">
                                                        <polyline points="20 6 9 17 4 12"/>
                                                    </svg>
                                                )}
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        )}
                    </div>

                    <div className="border-t border-[var(--builder-border)] pt-4">
                        <label className="block text-xs font-medium text-[var(--builder-text-secondary)] mb-3">
                            Background Patterns
                        </label>
                        <div className="grid grid-cols-4 gap-2">
                            {PRESET_PATTERNS.map((pattern) => (
                                <button
                                    key={pattern.id}
                                    type="button"
                                    onClick={() => updateField('background_pattern', pattern.id === 'none' ? null : pattern.id)}
                                    className={`relative aspect-square rounded-lg border-2 overflow-hidden transition-all ${
                                        (data.background_pattern === pattern.id) ||
                                        (pattern.id === 'none' && !data.background_pattern)
                                            ? 'border-[var(--builder-primary)] ring-2 ring-[var(--builder-primary)]/20'
                                            : 'border-[var(--builder-border)] hover:border-[var(--builder-primary)]/50'
                                    }`}
                                    title={pattern.name}
                                    style={{
                                        backgroundColor: pageBgColor,
                                        backgroundImage: pattern.preview !== 'transparent' ? pattern.preview : undefined,
                                        backgroundSize: pattern.size || 'cover',
                                    }}
                                >
                                    {pattern.id === 'none' && (
                                        <div className="absolute inset-0 flex items-center justify-center text-[var(--builder-text-secondary)]">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                <line x1="18" y1="6" x2="6" y2="18"/>
                                                <line x1="6" y1="6" x2="18" y2="18"/>
                                            </svg>
                                        </div>
                                    )}
                                    <span className="absolute bottom-1 left-1 right-1 text-[8px] text-center text-white bg-black/50 rounded px-1 truncate">
                                        {pattern.name}
                                    </span>
                                </button>
                            ))}
                        </div>
                        {hasBackgroundPattern && (
                            <div className="mt-2 flex items-center gap-2 text-xs text-green-600">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                                <span>Pattern applied</span>
                                <button
                                    type="button"
                                    onClick={() => updateField('background_pattern', null)}
                                    className="ml-2 text-[var(--builder-text-secondary)] hover:text-red-500 underline"
                                >
                                    Remove pattern
                                </button>
                            </div>
                        )}
                    </div>
                </div>
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

export default LayoutSettings;
