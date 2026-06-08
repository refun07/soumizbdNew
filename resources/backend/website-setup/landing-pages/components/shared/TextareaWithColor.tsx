import React, { useState, useRef } from 'react';
import { createPortal } from 'react-dom';
import ColorPicker from '../../../header/components/ColorPicker';

interface TextareaWithColorProps {
    label: string;
    value: string;
    colorValue: string | null;
    onChange: (value: string) => void;
    onColorChange: (color: string | null) => void;
    placeholder?: string;
    required?: boolean;
    rows?: number;
    helpText?: string;
    computedColor?: string;
}

const TextareaWithColor: React.FC<TextareaWithColorProps> = ({
    label,
    value,
    colorValue,
    onChange,
    onColorChange,
    placeholder = '',
    required = false,
    rows = 4,
    helpText,
    computedColor,
}) => {
    const [showColorPicker, setShowColorPicker] = useState(false);
    const buttonRef = useRef<HTMLButtonElement>(null);

    const displayColor = colorValue || computedColor || '#ffffff';
    const isAuto = !colorValue;

    return (
        <div>
            <div className="flex items-center justify-between mb-1.5">
                <label className="theme-input-label pt-0">
                    {label}
                    {required && <span className="text-red-500 ml-1">*</span>}
                </label>
                <button
                    ref={buttonRef}
                    type="button"
                    onClick={() => setShowColorPicker(!showColorPicker)}
                    className="flex items-center gap-1.5 px-2 py-1 rounded-md border border-[var(--builder-border)] hover:bg-[var(--builder-surface-hover)] transition-colors text-xs"
                    title="Change text color"
                >
                    <span
                        className="w-4 h-4 rounded-full border border-gray-300"
                        style={{ backgroundColor: displayColor }}
                    />
                    {isAuto && (
                        <span className="text-[var(--builder-text-muted)]">Auto</span>
                    )}
                    <svg className="w-3 h-3 text-[var(--builder-text-secondary)]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
            <textarea
                className="theme-input"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                placeholder={placeholder}
                rows={rows}
            />
            {helpText && (
                <p className="text-xs text-gray-500 mt-1">{helpText}</p>
            )}
            {showColorPicker && createPortal(
                <div
                    className="fixed inset-0 z-[9998]"
                    onClick={() => setShowColorPicker(false)}
                >
                    <div
                        className="fixed z-[9999] bg-white rounded-lg shadow-xl border border-gray-200 p-3"
                        style={{
                            top: buttonRef.current ? buttonRef.current.getBoundingClientRect().bottom + 8 : 0,
                            left: buttonRef.current ? Math.min(buttonRef.current.getBoundingClientRect().left, window.innerWidth - 340) : 0,
                        }}
                        onClick={(e) => e.stopPropagation()}
                    >
                        <div className="mb-2 text-xs font-medium text-gray-600">
                            {label} Color
                        </div>
                        <ColorPicker
                            value={colorValue || ''}
                            onChange={(color) => onColorChange(color || null)}
                        />
                        {isAuto && computedColor && (
                            <div className="mt-2 pt-2 border-t border-gray-100">
                                <p className="text-xs text-gray-500">
                                    Auto-calculated: <span className="font-mono">{computedColor}</span>
                                </p>
                            </div>
                        )}
                        {colorValue && (
                            <button
                                type="button"
                                onClick={() => onColorChange(null)}
                                className="mt-2 w-full text-xs text-center text-red-500 hover:text-red-600 py-1"
                            >
                                Reset to Auto
                            </button>
                        )}
                    </div>
                </div>,
                document.body
            )}
        </div>
    );
};

export default TextareaWithColor;
