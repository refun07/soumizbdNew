interface IOptions {
    highlightFirstWord?: boolean;
    renderBottomBorder?: boolean;
    highlightClassName?: string;
}

export const highlightOnTitle = (text: string = '', options?: IOptions) => {
    const borderClass =
        options?.renderBottomBorder !== false
            ? 'inline-block align-baseline border-b-[5px] border-[#F2C488] pb-1.5 sm:pb-3.5'
            : '';
    const highlightClassName = options?.highlightClassName ?? 'text-primary';

    if (!text.match(/\[.+\]/) && options?.highlightFirstWord) {
        let words = text.split(' ');
        words = [
            `<span class="${highlightClassName} font-bold ${borderClass}">`,
            words[0],
            '</span>',
            ...words.slice(1),
        ];
        return words.join(' ');
    }

    text = text.replaceAll(']', '</span>');
    text = text.replaceAll(
        '[',
        `<span class="${highlightClassName} font-bold ${borderClass}">`,
    );
    return text;
};
