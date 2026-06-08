import React from 'react';
import { FiChevronDown } from 'react-icons/fi';
import Select, { Props as SelectProps } from 'react-select';

interface Props<T> extends SelectProps<T> {
    options: T[];
    label?: string;
    className?: string;
    error?: string;
    touched?: boolean;
}

const SelectInput = ({
    options = [],
    label,
    classNames,
    placeholder,
    error,
    touched,
    ...rest
}: Props<any>) => {
    const hasError = error && touched;

    const selected = options.find((opt) => {
        if (rest.getOptionValue) {
            return rest.getOptionValue(opt) == rest.value;
        }

        return opt.value == rest.value;
    });

    return (
        <div className="flex justify-between gap-4">
            {label && <label className="flex items-center h-10">{label}</label>}

            <div className="w-full max-w-[270px]">
                <Select
                    {...rest}
                    isSearchable
                    value={selected || null}
                    options={rest.isLoading ? [] : options}
                    placeholder={placeholder || 'Select here'}
                    components={{
                        IndicatorSeparator: () => null,
                        DropdownIndicator: () => (
                            <span className="text-secondary-light text-lg">
                                <FiChevronDown />
                            </span>
                        ),
                    }}
                    classNames={{
                        ...classNames,
                        placeholder: (state) =>
                            `!text-[13px] !text-neutral-400 ${classNames?.placeholder?.(
                                state,
                            )}`,
                        control: (state) =>
                            `rounded px-3 py-1 h-10 min-w-[150px] w-full !bg-transparent !border !border-primary-14 ${classNames?.control}`,
                        menu: (state) => `${classNames?.menu?.(state)}`,
                        indicatorsContainer: (state) =>
                            `!text-secondary-light pl-2 [&>*]:!p-0 ${classNames?.indicatorsContainer?.(
                                state,
                            )}`,
                        singleValue: (state) =>
                            `text-sm ${classNames?.singleValue?.(state)}`,
                        indicatorSeparator: (state) =>
                            `!hidden ${classNames?.indicatorSeparator?.(
                                state,
                            )}`,
                        valueContainer: (state) =>
                            `!p-0 ${classNames?.valueContainer?.(state)}`,
                        input: (state) =>
                            `!p-0 !m-0 text-sm ${classNames?.input?.(state)}`,
                        option: (state) =>
                            `!py-1 ${
                                state.isSelected
                                    ? '!bg-secondary-light !text-white'
                                    : state.isFocused &&
                                      '!bg-secondary-light/30 !text-black'
                            }`,
                    }}
                />

                {hasError && (
                    <span className="text-sm text-theme-alert">{error}</span>
                )}
            </div>
        </div>
    );
};

export default SelectInput;
