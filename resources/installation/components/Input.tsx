import React, { InputHTMLAttributes } from 'react';
import { cn } from '../utils/cn';

interface Props extends InputHTMLAttributes<HTMLInputElement> {
    label?: string;
    classNames?: {
        group?: string;
        label?: string;
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
            <div className="w-full max-w-[270px]">
                <input
                    className={cn(
                        'h-10 px-4 block border border-border rounded-md focus:ring ring-primary-light focus:border-primary-light focus:outline-none w-full',
                        classNames?.input,
                    )}
                    {...rest}
                />

                {error && touched && (
                    <span className="text-sm text-theme-alert w-full">
                        {error}
                    </span>
                )}
            </div>
        </div>
    );
};

export default Input;
