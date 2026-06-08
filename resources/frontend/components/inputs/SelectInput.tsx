import { FiChevronDown } from 'react-icons/fi';
import Select, { Props as SelectProps } from 'react-select';
import { translate } from '../../utils/translate';

interface Props<T> extends SelectProps<T> {
    options: T[];
    label?: string;
    className?: string;
    error?: string;
    touched?: boolean;
}

const SelectInput = ({
    options,
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
        <div>
            {label && <label>{label}</label>}
            <Select
                {...rest}
                isSearchable
                value={selected || null}
                options={rest.isLoading ? [] : options}
                placeholder={placeholder || translate('Select here')}
                components={{
                    IndicatorSeparator: () => null,
                    DropdownIndicator: () => (
                        <span className="text-secondary text-lg">
                            <FiChevronDown />
                        </span>
                    ),
                }}
                classNames={{
                    ...classNames,
                    placeholder: (state) =>
                        `!text-sm !text-black/70 ${classNames?.placeholder?.(
                            state,
                        )}`,
                    control: (state) =>
                        `rounded-card px-3 py-1 !min-h-[37px] min-w-[150px] !bg-transparent !border ${
                            state.isFocused
                                ? '!border-secondary'
                                : '!border-primary-14'
                        } !shadow-none ${classNames?.control?.(state)}`,
                    menu: (state) => `${classNames?.menu?.(state)}`,
                    indicatorsContainer: (state) =>
                        `!text-secondary pl-2 [&>*]:!p-0 ${classNames?.indicatorsContainer?.(
                            state,
                        )}`,
                    singleValue: (state) =>
                        `text-sm ${classNames?.singleValue?.(state)}`,
                    indicatorSeparator: (state) =>
                        `!hidden ${classNames?.indicatorSeparator?.(state)}`,
                    valueContainer: (state) =>
                        `!p-0 ${classNames?.valueContainer?.(state)}`,
                    input: (state) =>
                        `!p-0 !m-0 text-sm ${classNames?.input?.(state)}`,
                    option: (state) =>
                        `!py-1 ${
                            state.isSelected
                                ? '!bg-secondary !text-white'
                                : state.isFocused &&
                                  '!bg-secondary/30 !text-black'
                        }`,
                }}
            />

            {hasError && (
                <span className="text-sm text-theme-alert">
                    {translate(error)}
                </span>
            )}
        </div>
    );
};

export default SelectInput;
