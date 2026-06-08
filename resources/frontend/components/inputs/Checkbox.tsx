import { DetailedHTMLProps, InputHTMLAttributes, ReactNode } from 'react';
import { Variant } from '../../types';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Props
    extends DetailedHTMLProps<
        InputHTMLAttributes<HTMLInputElement>,
        HTMLInputElement
    > {
    label: string | ReactNode;
    variant?: Variant;
    error?: string;
    touched?: boolean;
    labelClassName?: string;
}

const Checkbox = ({
    name,
    label,
    checked,
    variant,
    className,
    labelClassName,
    error,
    touched,
    ...rest
}: Props) => {
    const hasError = error && touched;

    return (
        <>
            <label className={cn(`theme-radio`, className)}>
                <input
                    type="checkbox"
                    className="theme-radio__input"
                    name={name}
                    checked={checked}
                    {...rest}
                />
                <span className="theme-radio__checkmark"></span>
                <span className={cn(`theme-radio__text`, labelClassName)}>
                    {label}
                </span>
            </label>

            {hasError && (
                <span className="text-theme-alert block !mt-0">
                    {translate(error)}
                </span>
            )}
        </>
    );
};

export default Checkbox;
