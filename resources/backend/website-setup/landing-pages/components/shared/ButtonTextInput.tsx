import React, { useState, useRef } from 'react';
import { createPortal } from 'react-dom';
import ColorPicker from '../../../header/components/ColorPicker';

interface ButtonTextInputProps {
    label: string;
    value: string;
    textColor: string | null;
    bgColor: string | null;
    onChange: (value: string) => void;
    onTextColorChange: (color: string | null) => void;
    onBgColorChange: (color: string | null) => void;
    placeholder?: string;
    primaryColor?: string;
    computedTextColor?: string;
}

const ButtonTextInput: React.FC<ButtonTextInputProps> = ({
    label,
    value,
    textColor,
    bgColor,
    onChange,
    onTextColorChange,
    onBgColorChange,
    placeholder = '',
    primaryColor = '#E68A3D',
    computedTextColor,
}) => {
    const [showTextColorPicker, setShowTextColorPicker] = useState(false);
    const [showBgColorPicker, setShowBgColorPicker] = useState(false);
    const textColorBtnRef = useRef<HTMLButtonElement>(null);
    const bgColorBtnRef = useRef<HTMLButtonElement>(null);

    const displayTextColor = textColor || computedTextColor || '#ffffff';
    const displayBgColor = bgColor || primaryColor;
    const isTextColorAuto = !textColor;
    const isBgColorDefault = !bgColor;

    const renderColorDropdown = (
        show: boolean,
        setShow: (v: boolean) => void,
        buttonRef: React.RefObject<HTMLButtonElement>,
        currentValue: string | null,
        onChange: (color: string | null) => void,
        computedValue: string | undefined,
        isAuto: boolean,
        isDefault: boolean,
        defaultLabel: string,
        title: string
    ) => {
        if (!show || !buttonRef.current) return null;

        const rect = buttonRef.current.getBoundingClientRect();
        const dropdownHeight = 450;
        const spaceBelow = window.innerHeight - rect.bottom - 8;
        const spaceAbove = rect.top - 8;
        const showAbove = spaceBelow < dropdownHeight && spaceAbove > spaceBelow;

        const top = showAbove
            ? rect.top - dropdownHeight - 8
            : rect.bottom + 8;

        return createPortal(
            <div
                className="fixed inset-0 z-[9998]"
                onClick={() => setShow(false)}
            >
                <div
                    className="fixed z-[9999] bg-white rounded-lg shadow-xl border border-gray-200 p-3"
                    style={{
                        top,
                        left: Math.min(rect.left, window.innerWidth - 340),
                    }}
                    onClick={(e) => e.stopPropagation()}
                >
                    <div className="mb-2 text-xs font-medium text-gray-600">
                        {title}
                    </div>
                    <ColorPicker
                        value={currentValue || ''}
                        onChange={(color) => onChange(color || null)}
                    />
                    {(isAuto || isDefault) && computedValue && (
                        <div className="mt-2 pt-2 border-t border-gray-100">
                            <p className="text-xs text-gray-500">
                                {isDefault ? `Default (${defaultLabel}): ` : 'Auto-calculated: '}
                                <span className="font-mono">{computedValue}</span>
                            </p>
                        </div>
                    )}
                    {currentValue && (
                        <button
                            type="button"
                            onClick={() => onChange(null)}
                            className="mt-2 w-full text-xs text-center text-red-500 hover:text-red-600 py-1"
                        >
                            Reset to {isDefault ? 'Default' : 'Auto'}
                        </button>
                    )}
                </div>
            </div>,
            document.body
        );
    };

    return (
        <div>
            <label className="theme-input-label pt-0">{label}</label>
            <input
                type="text"
                className="theme-input"
                value={value}
                onChange={(e) => onChange(e.target.value)}
                placeholder={placeholder}
            />
            <div className="flex items-center gap-4 mt-2">
                <button
                    ref={textColorBtnRef}
                    type="button"
                    onClick={() => setShowTextColorPicker(!showTextColorPicker)}
                    className="flex items-center gap-1.5 px-2 py-1 rounded-md border border-[var(--builder-border)] hover:bg-[var(--builder-surface-hover)] transition-colors text-xs"
                    title="Button text color"
                >
                    <span className="text-[var(--builder-text-muted)]">Text:</span>
                    <span
                        className="w-4 h-4 rounded-full border border-gray-300"
                        style={{ backgroundColor: displayTextColor }}
                    />
                    {isTextColorAuto && (
                        <span className="text-[var(--builder-text-muted)]">Auto</span>
                    )}
                </button>
                <button
                    ref={bgColorBtnRef}
                    type="button"
                    onClick={() => setShowBgColorPicker(!showBgColorPicker)}
                    className="flex items-center gap-1.5 px-2 py-1 rounded-md border border-[var(--builder-border)] hover:bg-[var(--builder-surface-hover)] transition-colors text-xs"
                    title="Button background color"
                >
                    <span className="text-[var(--builder-text-muted)]">Bg:</span>
                    <span
                        className="w-4 h-4 rounded-full border border-gray-300"
                        style={{ backgroundColor: displayBgColor }}
                    />
                    {isBgColorDefault && (
                        <span className="text-[var(--builder-text-muted)]">Primary</span>
                    )}
                </button>
            </div>

            {renderColorDropdown(
                showTextColorPicker,
                setShowTextColorPicker,
                textColorBtnRef,
                textColor,
                onTextColorChange,
                computedTextColor,
                isTextColorAuto,
                false,
                '',
                'Button Text Color'
            )}

            {renderColorDropdown(
                showBgColorPicker,
                setShowBgColorPicker,
                bgColorBtnRef,
                bgColor,
                onBgColorChange,
                primaryColor,
                false,
                isBgColorDefault,
                'Primary',
                'Button Background Color'
            )}
        </div>
    );
};

export default ButtonTextInput;
