import { Placement } from '@floating-ui/dom';
import React from 'react';
import { FaCaretDown } from 'react-icons/fa6';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Option {
    label: string;
    value: string;
    href?: string;
}

interface Props {
    className?: string;
    togglerClassName?: string;
    optionsClassName?: string;
    optionClassName?: string;

    name?: string;
    value?: string;
    noArrow?: boolean;
    noStyle?: boolean;
    options: Option[];
    placement?: Placement;

    components?: {
        Toggler?: (option?: Option) => React.ReactNode;
        Options?: (options?: Option[]) => React.ReactNode;
        Option?: (option: Option) => React.ReactNode;
    };

    /** if `href` is defined in option, onChange will not work */
    onChange?: (name: string, selectedOption: Option) => void;
}

const OptionDropdown = ({
    name,
    value,
    noArrow,
    noStyle,
    options,
    onChange,
    placement = 'bottom-start',

    className,
    togglerClassName,
    optionsClassName,
    optionClassName,

    components,
}: Props) => {
    const { Toggler } = components || {};

    const selectedOption = options.find((option) => option.value === value);

    return (
        <div
            className={cn('option-dropdown', className)}
            tabIndex={0}
            data-placement={placement}
        >
            {Toggler ? (
                Toggler(selectedOption)
            ) : (
                <div
                    className={cn(
                        'option-dropdown__toggler cursor-pointer inline-flex items-center gap-1',
                        {
                            'no-arrow': noArrow,
                            'no-style': noStyle,
                        },
                        togglerClassName,
                    )}
                >
                    <span className="">{translate(selectedOption?.label)}</span>
                    <FaCaretDown />
                </div>
            )}

            <div
                className={cn(
                    'option-dropdown__options w-[150px]',
                    optionsClassName,
                )}
            >
                <ul>
                    {options.map((option) => (
                        <li key={option.value}>
                            {option.href ? (
                                <a
                                    href={option.href}
                                    className={cn(
                                        'option-dropdown__option',
                                        optionClassName,
                                    )}
                                >
                                    {translate(option.label)}
                                </a>
                            ) : (
                                <button
                                    className={cn(
                                        'option-dropdown__option',
                                        optionClassName,
                                    )}
                                    onClick={() =>
                                        onChange?.(name || '', option)
                                    }
                                >
                                    {translate(option.label)}
                                </button>
                            )}
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default OptionDropdown;
