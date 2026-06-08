import React, { InputHTMLAttributes } from 'react';
import { cn } from '../../../react/utils/cn';

interface Props extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    classNames?: {
        group?: string;
        label?: string;
        inputWrapper?: string;
        input?: string;
    };

    error?: string;
    touched?: boolean;
}

const Input = ({
    label,
    className,
    classNames,
    error,
    touched,
    ...rest
}: Props) => {
    return (
        <div
            className={cn(
                'flex justify-between gap-4',
                className,
                classNames?.group,
            )}
        >
            {label && (
                <label
                    className={cn('flex items-center h-10', classNames?.label)}
                    htmlFor={rest.name}
                >
                    {label}
                </label>
            )}
            <div
                className={cn('w-full max-w-[270px]', classNames?.inputWrapper)}
            >
                <input
                    className={cn(
                        'h-10 px-4 block border border-border rounded-md focus:border-secondary-light focus:outline-none w-full',
                        classNames?.input,
                    )}
                    {...rest}
                />

                {error && touched && (
                    <span className="text-sm text-theme-alert w-full mt-1 block">
                        {error}
                    </span>
                )}
            </div>
        </div>
    );
};

export default Input;
