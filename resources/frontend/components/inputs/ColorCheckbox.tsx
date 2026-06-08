import { DetailedHTMLProps, InputHTMLAttributes } from 'react';
import { FaCheck } from 'react-icons/fa6';
import { Variant } from '../../types';

interface Props
    extends Omit<
        DetailedHTMLProps<
            InputHTMLAttributes<HTMLInputElement>,
            HTMLInputElement
        >,
        'color'
    > {
    name: string;
    label?: string;
    value: string | number;
    code: string;
    colorImage?: string | null;
    variant?: Variant;
}

const ColorCheckbox = ({
    name,
    label,
    value,
    code,
    colorImage,
    checked,
    variant,
    ...rest
}: Props) => {
    return (
        <div className="flex items-center gap-3">
            <label
                className={`theme-checkbox-check ${colorImage ? '!h-[44px] !w-[44px]' : '!h-[30px] !w-[30px]'} options ${
                    variant && `theme-checkbox--${variant}`
                } ${colorImage ? '!bg-transparent !p-0' : ''}`}
                style={{
                    backgroundColor: colorImage ? 'transparent' : code,
                }}
            >
                <input
                    type="checkbox"
                    id={name}
                    className="theme-checkbox__input"
                    name={name}
                    value={value}
                    checked={checked}
                    {...rest}
                />
                {colorImage ? (
                    <img
                        src={colorImage}
                        alt={label || 'Color'}
                        className="w-full h-full object-cover rounded border-2 border-gray-200"
                    />
                ) : (
                    <span
                        className="theme-checkbox__checkmark !text-base"
                        style={{
                            backgroundColor: code,
                        }}
                    >
                        {checked ? <FaCheck /> : null}
                    </span>
                )}
            </label>

            {label && (
                <label htmlFor={name} className="text-black">
                    {label}
                </label>
            )}
        </div>
    );
};

export default ColorCheckbox;
