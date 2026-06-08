import React from 'react';

interface Props
    extends React.DetailedHTMLProps<
        React.InputHTMLAttributes<HTMLInputElement>,
        HTMLInputElement
    > {
    label?: string;
    labelInline?: boolean;
    toggler?: boolean;
    labelOnly?: boolean;
    variant?: string;

    error?: string;
    touched?: boolean;
}

const Checkbox = ({
    type,
    label,
    labelInline,
    labelOnly,
    toggler,
    variant,

    error,
    touched,

    ...rest
}: Props) => {
    return (
        <div className={`flex justify-between gap-4`}>
            {label && (
                <label className="flex items-center leading-none">
                    {label}
                </label>
            )}

            <div className="w-full max-w-[270px]">
                <div className="flex">
                    <label
                        className={`${
                            toggler
                                ? 'theme-checkbox--toggler'
                                : 'theme-checkbox-check'
                        } options theme-checkbox--${variant}`}
                    >
                        <input
                            className="theme-checkbox__input"
                            type={type || 'checkbox'}
                            {...rest}
                        />
                        <span className="theme-checkbox__checkmark"></span>
                    </label>
                </div>

                {error && touched && (
                    <span className="text-sm text-theme-alert w-full">
                        {error}
                    </span>
                )}
            </div>
        </div>
    );
};

export default Checkbox;
