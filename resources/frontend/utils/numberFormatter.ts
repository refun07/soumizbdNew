const currency = window.config.currency;

// base formatter with defaults
const formatter = (value: number, options?: Intl.NumberFormatOptions) => {
    return new Intl.NumberFormat('en-US', {
        compactDisplay: 'short',
        notation: value >= 1000000 ? 'compact' : 'standard', // compact notation for a value greater than or equal 1 million
        maximumFractionDigits: currency.numOfDecimals,
        ...options,
    })
        .format(value)
        .replaceAll(',', currency.thousandSeparator || ',');
};

// with essential options to format number
export const numberFormatter = (
    value?: number | null,
    options?: Intl.NumberFormatOptions,
) => {
    return formatter(value || 0, options);
};

// with essential options to format currency
export const currencyFormatter = (
    value?: number | null,
    options?: Intl.NumberFormatOptions,
) => {
    const formattedNumber = formatter(value || 0, {
        minimumIntegerDigits: 2,
        ...options,
    });

    if (currency.symbol.position === 'amount_first') {
        return formattedNumber + currency.symbol.show;
    } else if (currency.symbol.position === 'symbol_first') {
        return currency.symbol.show + formattedNumber;
    } else if (currency.symbol.position === 'symbol_space') {
        return currency.symbol.show + ' ' + formattedNumber;
    } else if (currency.symbol.position === 'amount_space') {
        return formattedNumber + ' ' + currency.symbol.show;
    }
};

// with essential options to format percentage
export const paddedNumber = (num?: number, pad?: number): string => {
    return formatter(num || 0, {
        minimumIntegerDigits: pad || 2,
    });
};
