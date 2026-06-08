import { FaCaretDown } from 'react-icons/fa';
import Select, { Props as SelectProps } from 'react-select';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Props<T> extends SelectProps<T> {
    options: T[];
    label?: string;
    className?: string;
    groupClassName?: string;
    error?: string;
    touched?: boolean;
    statusColorClass?: string;
    statusTextClass?: string;
}

const SelectInput = ({
    options = [],
    label,
    classNames,
    groupClassName,
    placeholder,
    error,
    touched,
    statusColorClass,
    statusTextClass,
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
        <div className={groupClassName}>
            {label && <label>{label}</label>}
            <div className="rounded py-1">
                <Select
                    {...rest}
                    isSearchable
                    menuPosition="fixed"
                    menuPortalTarget={
                        typeof document !== 'undefined' ? document.body : null
                    }
                    value={selected || null}
                    options={rest.isLoading ? [] : options}
                    placeholder={placeholder || translate('Select here')}
                    styles={{
                        ...(rest.styles || {}),
                        menuPortal: (base, props) => ({
                            ...base,
                            zIndex: 9999,
                            ...(rest.styles?.menuPortal
                                ? rest.styles.menuPortal(base, props)
                                : {}),
                        }),
                    }}
                    components={{
                        IndicatorSeparator: () => null,
                        DropdownIndicator: () => (
                            <span
                                className={cn(
                                    'text-sm',
                                    statusColorClass
                                        ? statusTextClass || '!text-white'
                                        : 'text-muted',
                                )}
                            >
                                <FaCaretDown />
                            </span>
                        ),
                    }}
                    className=""
                    classNames={{
                        ...classNames,
                        placeholder: (state) =>
                            cn(
                                `!text-[13px] !text-muted`,
                                classNames?.placeholder?.(state),
                            ),
                        control: (state) =>
                            cn(
                                'rounded px-3 py-1 !min-h-[37px] min-w-[100px] !shadow-none',
                                statusColorClass
                                    ? `${statusColorClass} !border-transparent`
                                    : '!bg-background !border !border-border',
                                state.isFocused &&
                                    '!border-secondary !ring-2 !ring-secondary-light/30',
                                classNames?.control?.(state),
                            ),
                        menu: (state) =>
                            cn(
                                '!z-[9999] !overflow-hidden !rounded-md !border !border-border !bg-popover !shadow-lg',
                                classNames?.menu?.(state),
                            ),
                        menuList: (state) =>
                            cn(
                                '!max-h-64 !bg-popover !py-1',
                                classNames?.menuList?.(state),
                            ),
                        indicatorsContainer: (state) =>
                            cn(
                                'pl-2 [&>*]:!p-0',
                                classNames?.indicatorsContainer?.(state),
                            ),
                        singleValue: (state) =>
                            cn(
                                'rounded px-2 py-1 text-sm font-medium',
                                statusColorClass
                                    ? statusTextClass || '!text-white'
                                    : '!text-foreground',
                                classNames?.singleValue?.(state),
                            ),
                        indicatorSeparator: (state) =>
                            cn(
                                '!hidden',
                                classNames?.indicatorSeparator?.(state),
                            ),
                        valueContainer: (state) =>
                            cn('!p-0', classNames?.valueContainer?.(state)),
                        input: (state) =>
                            cn(
                                '!p-0 !m-0 text-sm',
                                statusColorClass
                                    ? statusTextClass || '!text-white'
                                    : '!text-foreground',
                                classNames?.input?.(state),
                            ),
                        option: (state) =>
                            cn(
                                '!cursor-pointer !px-3 !py-2 text-sm',
                                state.isSelected
                                    ? '!bg-primary !text-primary-foreground'
                                    : '!bg-popover !text-popover-foreground',
                                state.isFocused &&
                                    !state.isSelected &&
                                    '!bg-background-hover !text-foreground',
                                classNames?.option?.(state),
                            ),
                    }}
                />
            </div>

            {hasError && (
                <span className="text-sm text-theme-alert">{error}</span>
            )}
        </div>
    );
};

export default SelectInput;
