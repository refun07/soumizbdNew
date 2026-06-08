import React, { useState, useEffect, useRef, useCallback } from 'react';
import { createPortal } from 'react-dom';

interface ColorPickerProps {
    value: string;
    onChange: (color: string) => void;
    label?: string;
    id?: string;
}

const ColorPicker: React.FC<ColorPickerProps> = ({ value, onChange, label, id }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [hexValue, setHexValue] = useState('#000000');
    const [opacity, setOpacity] = useState(100);
    const [savedColors, setSavedColors] = useState<string[]>([]);
    const [copied, setCopied] = useState(false);
    const [dropdownPosition, setDropdownPosition] = useState<{ top: number; left: number } | null>(null);

    const pickerRef = useRef<HTMLDivElement>(null);
    const triggerRef = useRef<HTMLButtonElement>(null);

    const inputId = id || `color-picker-${Math.random().toString(36).substr(2, 9)}`;

    // Calculate dropdown position when opening
    const updateDropdownPosition = useCallback(() => {
        if (triggerRef.current) {
            const rect = triggerRef.current.getBoundingClientRect();
            const dropdownHeight = 450;
            const spaceBelow = window.innerHeight - rect.bottom - 8;
            const spaceAbove = rect.top - 8;
            const showAbove = spaceBelow < dropdownHeight && spaceAbove > spaceBelow;

            const top = showAbove
                ? rect.top - dropdownHeight - 8
                : rect.bottom + 8;

            setDropdownPosition({
                top,
                left: rect.left
            });
        }
    }, []);

    // Load saved colors from localStorage
    useEffect(() => {
        const saved = localStorage.getItem('epikcart_saved_colors');
        if (saved) {
            try {
                setSavedColors(JSON.parse(saved));
            } catch (e) {
                console.error('Error loading saved colors:', e);
            }
        }
    }, []);

    // Parse hex color and opacity from value
    useEffect(() => {
        if (value?.startsWith('#')) {
            if (value.length === 9) {
                setHexValue(value.slice(0, 7));
                const alphaHex = value.slice(7, 9);
                const alpha = parseInt(alphaHex, 16);
                setOpacity(Math.round((alpha / 255) * 100));
            } else if (value.length === 7) {
                setHexValue(value);
                setOpacity(100);
            }
        } else if (value?.startsWith('rgba')) {
            const match = value.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([\d.]+))?\)/);
            if (match) {
                const r = parseInt(match[1]).toString(16).padStart(2, '0');
                const g = parseInt(match[2]).toString(16).padStart(2, '0');
                const b = parseInt(match[3]).toString(16).padStart(2, '0');
                setHexValue(`#${r}${g}${b}`);
                setOpacity(match[4] ? Math.round(parseFloat(match[4]) * 100) : 100);
            }
        }
    }, [value]);

    // Close on click outside
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            const target = event.target as Node;
            // Close if click is outside both the trigger button and the picker dropdown
            if (
                pickerRef.current &&
                !pickerRef.current.contains(target) &&
                triggerRef.current &&
                !triggerRef.current.contains(target)
            ) {
                setIsOpen(false);
            }
        };

        if (isOpen) {
            document.addEventListener('mousedown', handleClickOutside);
        }

        return () => {
            document.removeEventListener('mousedown', handleClickOutside);
        };
    }, [isOpen]);

    const handleColorChange = (color: string) => {
        const newValue = opacity < 100 
            ? `${color}${Math.round(opacity * 255 / 100).toString(16).padStart(2, '0')}` 
            : color;
        onChange(newValue);
        setHexValue(color);
    };

    const handleHexChange = (hex: string) => {
        setHexValue(hex);
        if (/^#[0-9A-Fa-f]{6}$/.test(hex)) {
            const newValue = opacity < 100 
                ? `${hex}${Math.round(opacity * 255 / 100).toString(16).padStart(2, '0')}` 
                : hex;
            onChange(newValue);
        }
    };

    const handleOpacityChange = (newOpacity: number) => {
        const clampedOpacity = Math.max(0, Math.min(100, newOpacity));
        setOpacity(clampedOpacity);
        if (/^#[0-9A-Fa-f]{6}$/.test(hexValue)) {
            const alpha = Math.round(clampedOpacity * 255 / 100).toString(16).padStart(2, '0');
            const newValue = clampedOpacity < 100 ? `${hexValue}${alpha}` : hexValue;
            onChange(newValue);
        }
    };

    const saveColorToList = () => {
        if (!savedColors.includes(hexValue)) {
            const newSavedColors = [hexValue, ...savedColors].slice(0, 12);
            setSavedColors(newSavedColors);
            localStorage.setItem('epikcart_saved_colors', JSON.stringify(newSavedColors));
        }
    };

    const removeSavedColor = (colorToRemove: string, e: React.MouseEvent) => {
        e.stopPropagation();
        const newSavedColors = savedColors.filter(c => c !== colorToRemove);
        setSavedColors(newSavedColors);
        localStorage.setItem('epikcart_saved_colors', JSON.stringify(newSavedColors));
    };

    const copyToClipboard = async () => {
        try {
            await navigator.clipboard.writeText(opacity < 100 ? `${hexValue}${Math.round(opacity * 255 / 100).toString(16).padStart(2, '0')}` : hexValue);
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    };

    const formatColorForPreview = (color: string) => {
        if (!color) return 'transparent';
        if (color.startsWith('#') && color.length === 9) {
            const hex = color.slice(0, 7);
            const alpha = parseInt(color.slice(7, 9), 16) / 255;
            return `rgba(${parseInt(hex.slice(1, 3), 16)}, ${parseInt(hex.slice(3, 5), 16)}, ${parseInt(hex.slice(5, 7), 16)}, ${alpha})`;
        }
        return color;
    };

    // Generate hexagonal honeycomb color palette using HSL color space
    const generateHoneycombPalette = (centerColor: string) => {
        const center = centerColor.replace('#', '');
        const r = parseInt(center.slice(0, 2), 16);
        const g = parseInt(center.slice(2, 4), 16);
        const b = parseInt(center.slice(4, 6), 16);

        const rgbToHsl = (r: number, g: number, b: number) => {
            r /= 255;
            g /= 255;
            b /= 255;
            const max = Math.max(r, g, b);
            const min = Math.min(r, g, b);
            let h = 0, s = 0, l = (max + min) / 2;

            if (max !== min) {
                const d = max - min;
                s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
                switch (max) {
                    case r: h = (g - b) / d + (g < b ? 6 : 0); break;
                    case g: h = (b - r) / d + 2; break;
                    case b: h = (r - g) / d + 4; break;
                }
                h /= 6;
            }
            return { h: h * 360, s: s * 100, l: l * 100 };
        };

        const hslToHex = (h: number, s: number, l: number) => {
            l /= 100;
            const a = s * Math.min(l, 1 - l) / 100;
            const f = (n: number) => {
                const k = (n + h / 30) % 12;
                const color = l - a * Math.max(Math.min(k - 3, 9 - k, 1), -1);
                return Math.round(255 * color).toString(16).padStart(2, '0');
            };
            return `#${f(0)}${f(8)}${f(4)}`;
        };

        const { h, s, l } = rgbToHsl(r, g, b);

        // Inner Ring - Tints (8 colors: selected variations)
        const innerRingVariations = [
            { h: h, s: Math.min(100, s * 0.2), l: Math.min(98, l + 40) },
            { h: h, s: Math.min(100, s * 0.3), l: Math.min(92, l + 30) },
            { h: h, s: Math.min(100, s * 0.4), l: Math.min(85, l + 20) },
            { h: h, s: Math.min(100, s * 0.5), l: Math.min(80, l + 15) },
            { h: h, s: Math.min(100, s * 0.6), l: Math.max(15, l - 5) },
            { h: h, s: Math.min(100, s * 0.8), l: Math.max(10, l - 15) },
            { h: h, s: Math.min(100, s * 1.0), l: Math.max(5, l - 25) },
            { h: h, s: Math.min(100, s * 1.2), l: Math.max(3, l - 35) }
        ];

        // Middle Ring - Saturation variations (12 colors)
        const middleRingVariations = [
            { h: h, s: Math.min(100, s * 0.2), l: l },
            { h: h, s: Math.min(100, s * 0.4), l: l },
            { h: h, s: Math.min(100, s * 0.6), l: l },
            { h: h, s: Math.min(100, s * 0.8), l: l },
            { h: h, s: Math.min(100, s * 1.2), l: l },
            { h: h, s: Math.min(100, s * 1.4), l: l },
            { h: (h + 15) % 360, s: Math.max(50, s), l: Math.min(85, l + 5) },
            { h: (h + 45) % 360, s: Math.max(50, s), l: Math.min(80, l) },
            { h: (h + 75) % 360, s: Math.max(50, s), l: Math.min(75, l - 5) },
            { h: (h + 105) % 360, s: Math.max(50, s), l: Math.min(80, l) },
            { h: (h + 135) % 360, s: Math.max(50, s), l: Math.min(85, l + 5) },
            { h: (h + 165) % 360, s: Math.max(50, s), l: Math.min(80, l) }
        ];

        // Outer Ring - Complementary and Analogous Colors (18 colors)
        const outerRingVariations = [
            { h: (h + 20) % 360, s: Math.max(50, s), l: Math.min(85, l + 15) },
            { h: (h + 40) % 360, s: Math.max(50, s), l: Math.min(82, l + 12) },
            { h: (h + 60) % 360, s: Math.max(50, s), l: Math.min(80, l + 10) },
            { h: (h + 80) % 360, s: Math.max(50, s), l: Math.min(78, l + 8) },
            { h: (h + 100) % 360, s: Math.max(50, s), l: Math.min(75, l + 5) },
            { h: (h + 120) % 360, s: Math.max(50, s), l: l },
            { h: (h + 140) % 360, s: Math.max(50, s), l: Math.min(72, l - 3) },
            { h: (h + 160) % 360, s: Math.max(50, s), l: Math.min(70, l - 5) },
            { h: (h + 180) % 360, s: Math.max(40, s * 0.8), l: l },
            { h: (h + 200) % 360, s: Math.max(50, s), l: Math.min(70, l - 5) },
            { h: (h + 220) % 360, s: Math.max(50, s), l: Math.min(72, l - 3) },
            { h: (h + 240) % 360, s: Math.max(50, s), l: l },
            { h: (h + 260) % 360, s: Math.max(50, s), l: Math.min(75, l + 5) },
            { h: (h + 280) % 360, s: Math.max(50, s), l: Math.min(78, l + 8) },
            { h: (h + 300) % 360, s: Math.max(50, s), l: Math.min(80, l + 10) },
            { h: (h + 320) % 360, s: Math.max(50, s), l: Math.min(82, l + 12) },
            { h: (h + 340) % 360, s: Math.max(50, s), l: Math.min(85, l + 15) },
            { h: (h + 10) % 360, s: Math.max(50, s), l: Math.min(88, l + 18) }
        ];

        return {
            inner: innerRingVariations.map(v => hslToHex(v.h, v.s, v.l)),
            middle: middleRingVariations.map(v => hslToHex(v.h, v.s, v.l)),
            outer: outerRingVariations.map(v => hslToHex(v.h, v.s, v.l))
        };
    };

    const honeycombPalette = generateHoneycombPalette(hexValue);

    return (
        <div className="relative" ref={pickerRef}>
            {/* Label */}
            {label && (
                <label 
                    htmlFor={inputId}
                    className="block text-sm font-medium text-stone-600 mb-2"
                >
                    {label}
                </label>
            )}

            {/* Trigger Button + Input Group */}
            <div className="flex items-center gap-2">
                <button
                    ref={triggerRef}
                    id={inputId}
                    type="button"
                    onClick={() => {
                        if (!isOpen) {
                            updateDropdownPosition();
                        }
                        setIsOpen(!isOpen);
                    }}
                    className={`
                        w-10 h-10 rounded-lg cursor-pointer border-2 transition-all duration-150
                        hover:border-stone-400 hover:shadow-md
                        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-stone-500
                        ${isOpen ? 'border-stone-500 ring-2 ring-stone-500 ring-offset-2' : 'border-stone-300'}
                    `}
                    style={{ backgroundColor: formatColorForPreview(value) }}
                    aria-label={`${label || 'Color'} picker. Current color: ${hexValue}`}
                    aria-haspopup="dialog"
                    aria-expanded={isOpen}
                >
                    {!value || value === '#ffffff' ? (
                        <div className="w-full h-full rounded-lg flex items-center justify-center bg-stone-100">
                            <svg className="w-5 h-5 text-stone-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                            </svg>
                        </div>
                    ) : null}
                </button>

                <input
                    type="text"
                    value={hexValue}
                    onChange={(e) => handleHexChange(e.target.value)}
                    placeholder="#000000"
                    className="
                        w-24 px-3 py-2 text-sm font-mono
                        border border-stone-300 rounded-lg
                        focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-stone-500
                        transition-all duration-150
                    "
                    aria-label="Hex color value"
                />

                {/* Opacity Badge */}
                <div className="flex items-center gap-1.5 text-sm text-stone-600">
                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span className="font-medium">{opacity}%</span>
                </div>
            </div>

            {/* Dropdown Panel - Rendered via Portal to avoid overflow clipping */}
            {isOpen && dropdownPosition && createPortal(
                <div
                    ref={pickerRef}
                    className="
                        fixed z-[9999]
                        bg-white border border-stone-200 rounded-xl shadow-2xl
                        w-[340px] sm:w-[560px] max-w-[calc(100vw-2rem)]
                        overflow-hidden
                        animate-in fade-in slide-in-from-top-2 duration-200
                    "
                    style={{
                        top: `${dropdownPosition.top}px`,
                        left: `${Math.min(dropdownPosition.left, window.innerWidth - 580)}px`
                    }}
                    role="dialog"
                    aria-label="Color picker panel"
                >
                    {/* Preview Bar */}
                    <div 
                        className="h-12 w-full border-b border-stone-100 relative"
                        style={{ backgroundColor: formatColorForPreview(value) }}
                    >
                        <div className="absolute inset-0 flex items-center justify-center">
                            <span className="text-xs font-mono px-2 py-1 rounded bg-white/90 text-stone-700 shadow-sm">
                                {opacity < 100 
                                    ? `${hexValue}${Math.round(opacity * 255 / 100).toString(16).padStart(2, '0')}` 
                                    : hexValue}
                            </span>
                        </div>
                    </div>

                    {/* Responsive Layout: Stack on mobile, side-by-side on large */}
                    <div className="flex flex-col sm:flex-row">
                        {/* Left Column: Controls */}
                        <div className="flex-1 p-4 space-y-4 sm:border-r sm:border-stone-100">
                            {/* Native Color Input */}
                            <div>
                                <label className="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-2">
                                    Custom Color
                                </label>
                                <input
                                    type="color"
                                    value={hexValue}
                                    onChange={(e) => handleColorChange(e.target.value)}
                                    className="
                                        w-full h-10 cursor-pointer rounded-lg
                                        border-2 border-stone-200
                                        hover:border-stone-300 transition-colors
                                    "
                                />
                            </div>

                            {/* Opacity Slider */}
                            <div>
                                <div className="flex items-center justify-between mb-2">
                                    <label className="text-xs font-semibold text-stone-500 uppercase tracking-wider">
                                        Opacity
                                    </label>
                                    <span className="text-sm font-medium text-stone-700">{opacity}%</span>
                                </div>
                                <input
                                    type="range"
                                    min="0"
                                    max="100"
                                    value={opacity}
                                    onChange={(e) => handleOpacityChange(parseInt(e.target.value))}
                                    className="
                                        w-full h-2 rounded-lg appearance-none cursor-pointer
                                        bg-stone-200 accent-stone-700
                                        focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-2
                                    "
                                    aria-label="Opacity slider"
                                />
                                <div className="flex justify-between mt-1 text-xs text-stone-400">
                                    <span>0%</span>
                                    <span>50%</span>
                                    <span>100%</span>
                                </div>
                            </div>

                            {/* Saved Colors */}
                            {savedColors.length > 0 && (
                                <div>
                                    <div className="flex items-center justify-between mb-2">
                                        <label className="text-xs font-semibold text-stone-500 uppercase tracking-wider">
                                            Saved
                                        </label>
                                        <button
                                            onClick={() => {
                                                setSavedColors([]);
                                                localStorage.removeItem('epikcart_saved_colors');
                                            }}
                                            className="text-xs text-red-500 hover:text-red-700 transition-colors"
                                        >
                                            Clear
                                        </button>
                                    </div>
                                    <div className="grid grid-cols-8 gap-1.5">
                                        {savedColors.slice(0, 16).map((color, index) => (
                                            <div key={`saved-${index}`} className="relative group">
                                                <button
                                                    type="button"
                                                    onClick={() => handleColorChange(color)}
                                                    className={`
                                                        w-7 h-7 rounded-md border-2 transition-all duration-150
                                                        hover:scale-110 hover:shadow-sm
                                                        focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-1
                                                        ${hexValue === color ? 'border-stone-700 ring-2 ring-stone-500' : 'border-stone-200'}
                                                    `}
                                                    style={{ backgroundColor: color }}
                                                    aria-label={`Select saved color ${color}`}
                                                />
                                                <button
                                                    onClick={(e) => removeSavedColor(color, e)}
                                                    className="
                                                        absolute -top-1 -right-1 w-3.5 h-3.5 
                                                        bg-red-500 text-white rounded-full text-[8px]
                                                        opacity-0 group-hover:opacity-100 
                                                        transition-opacity duration-150
                                                        flex items-center justify-center
                                                        hover:bg-red-600
                                                    "
                                                    aria-label="Remove saved color"
                                                >
                                                    ×
                                                </button>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            {/* Actions */}
                            <div className="flex items-center gap-2 pt-3 border-t border-stone-100">
                                <button
                                    type="button"
                                    onClick={saveColorToList}
                                    disabled={savedColors.includes(hexValue)}
                                    className="
                                        flex-1 px-3 py-2 bg-stone-800 text-white text-sm font-medium rounded-lg
                                        hover:bg-stone-900 transition-all duration-150
                                        disabled:bg-stone-300 disabled:cursor-not-allowed
                                        focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-2
                                    "
                                >
                                    {savedColors.includes(hexValue) ? 'Saved' : 'Save'}
                                </button>
                                <button
                                    type="button"
                                    onClick={copyToClipboard}
                                    className={`
                                        px-3 py-2 border-2 border-stone-300 rounded-lg text-sm font-medium
                                        transition-all duration-150
                                        hover:border-stone-400 hover:bg-stone-50
                                        focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-2
                                        ${copied ? 'border-green-500 text-green-700 bg-green-50' : 'text-stone-700'}
                                    `}
                                    aria-label="Copy color code"
                                >
                                    {copied ? (
                                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                                        </svg>
                                    ) : (
                                        <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    )}
                                </button>
                            </div>

                            {/* Hex Input */}
                            <input
                                type="text"
                                value={hexValue}
                                onChange={(e) => handleHexChange(e.target.value)}
                                placeholder="#000000"
                                className="
                                    w-full px-3 py-2 text-sm font-mono
                                    border border-stone-300 rounded-lg
                                    focus:outline-none focus:ring-2 focus:ring-stone-500 focus:border-stone-500
                                    transition-all duration-150
                                "
                                aria-label="Hex color code input"
                            />
                        </div>

                        {/* Right Column: Honeycomb Palette */}
                        <div className="flex-1 p-4 border-t sm:border-t-0 border-stone-100">
                            <label className="block text-xs font-semibold text-stone-500 uppercase tracking-wider mb-3">
                                Color Harmony
                            </label>
                            <div className="flex justify-center">
                                <div className="relative w-56 h-56">
                                    {/* Center - Current Color */}
                                    <button
                                        type="button"
                                        className="
                                            absolute w-12 h-12 rounded-full border-3 border-white shadow-lg
                                            hover:scale-110 hover:shadow-xl transition-all duration-200 z-20
                                            focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-2
                                        "
                                        style={{ 
                                            backgroundColor: hexValue,
                                            left: 'calc(50% - 24px)',
                                            top: 'calc(50% - 24px)',
                                            borderWidth: '3px'
                                        }}
                                        aria-label={`Current color: ${hexValue}`}
                                    />

                                    {/* Inner Ring - 8 colors (Tints & Shades) */}
                                    {honeycombPalette.inner.map((color, index) => {
                                        const angle = (index * (360 / 8) - 90) * Math.PI / 180;
                                        const radius = 38;
                                        const x = Math.cos(angle) * radius + 112;
                                        const y = Math.sin(angle) * radius + 112;

                                        return (
                                            <button
                                                key={`inner-${index}`}
                                                type="button"
                                                onClick={() => handleColorChange(color)}
                                                className={`
                                                    absolute w-7 h-7 rounded-full border-2 transition-all duration-150
                                                    hover:scale-125 hover:shadow-md hover:z-30
                                                    focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-1 focus:z-30
                                                    ${hexValue === color ? 'border-stone-700 ring-2 ring-stone-500 z-30' : 'border-white shadow-md'}
                                                `}
                                                style={{
                                                    backgroundColor: color,
                                                    left: `${x - 14}px`,
                                                    top: `${y - 14}px`
                                                }}
                                                aria-label={`Select color ${color}`}
                                            />
                                        );
                                    })}

                                    {/* Middle Ring - 12 colors (Saturation & Hue variations) */}
                                    {honeycombPalette.middle.map((color, index) => {
                                        const angle = (index * (360 / 12) - 90 + 15) * Math.PI / 180;
                                        const radius = 64;
                                        const x = Math.cos(angle) * radius + 112;
                                        const y = Math.sin(angle) * radius + 112;

                                        return (
                                            <button
                                                key={`middle-${index}`}
                                                type="button"
                                                onClick={() => handleColorChange(color)}
                                                className={`
                                                    absolute w-8 h-8 rounded-full border-2 transition-all duration-150
                                                    hover:scale-125 hover:shadow-md hover:z-30
                                                    focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-1 focus:z-30
                                                    ${hexValue === color ? 'border-stone-700 ring-2 ring-stone-500 z-30' : 'border-white shadow-md'}
                                                `}
                                                style={{
                                                    backgroundColor: color,
                                                    left: `${x - 16}px`,
                                                    top: `${y - 16}px`
                                                }}
                                                aria-label={`Select color ${color}`}
                                            />
                                        );
                                    })}

                                    {/* Outer Ring - 18 colors (Complementary & Analogous) */}
                                    {honeycombPalette.outer.map((color, index) => {
                                        const angle = (index * (360 / 18) - 90) * Math.PI / 180;
                                        const radius = 92;
                                        const x = Math.cos(angle) * radius + 112;
                                        const y = Math.sin(angle) * radius + 112;

                                        return (
                                            <button
                                                key={`outer-${index}`}
                                                type="button"
                                                onClick={() => handleColorChange(color)}
                                                className={`
                                                    absolute w-9 h-9 rounded-full border-2 transition-all duration-150
                                                    hover:scale-125 hover:shadow-md hover:z-30
                                                    focus:outline-none focus:ring-2 focus:ring-stone-500 focus:ring-offset-1 focus:z-30
                                                    ${hexValue === color ? 'border-stone-700 ring-2 ring-stone-500 z-30' : 'border-white shadow-md'}
                                                `}
                                                style={{
                                                    backgroundColor: color,
                                                    left: `${x - 18}px`,
                                                    top: `${y - 18}px`
                                                }}
                                                aria-label={`Select color ${color}`}
                                            />
                                        );
                                    })}
                                </div>
                            </div>
                            
                            {/* Legend */}
                            <div className="flex justify-center gap-4 mt-4 text-xs text-stone-500">
                                <div className="flex items-center gap-1.5">
                                    <div className="w-3 h-3 rounded-full bg-stone-300"></div>
                                    <span>Tints & Shades</span>
                                </div>
                                <div className="flex items-center gap-1.5">
                                    <div className="w-3 h-3 rounded-full bg-stone-400"></div>
                                    <span>Saturation</span>
                                </div>
                                <div className="flex items-center gap-1.5">
                                    <div className="w-3 h-3 rounded-full bg-stone-500"></div>
                                    <span>Accents</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>,
                document.body
            )}
        </div>
    );
};

export default ColorPicker;
