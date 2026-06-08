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
}

const Checkbox = ({
    type,
    label,
    labelInline,
    labelOnly,
    toggler,
    variant,
    ...rest
}: Props) => {
    return (
        <div className={`theme-input-group ${!label ? 'no-label' : ''}`}>
            {label && <label className="">{label}</label>}

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
        </div>
    );
};

export default Checkbox;
