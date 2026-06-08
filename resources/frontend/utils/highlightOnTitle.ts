interface IOptions {
    highlightFirstWord?: boolean;
    renderBottomBorder?: boolean;
}

export const highlightOnTitle = (text: string = '', options?: IOptions) => {
    const borderClass = options?.renderBottomBorder !== false ? 'border-b-[5px] border-secondary pb-3.5' : '';
    
    if (!text.match(/\[.+\]/) && options?.highlightFirstWord) {
        let words = text.split(' ');
        words = [
            `<span class="text-primary font-bold ${borderClass}">`,
            words[0],
            '</span>',
            ...words.slice(1),
        ];
        return words.join(' ');
    }

    text = text.replaceAll(']', '</span>');
    text = text.replaceAll(
        '[',
        `<span class="text-primary font-bold ${borderClass}">`,
    );
    return text;
};
