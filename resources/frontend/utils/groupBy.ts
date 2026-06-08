export const groupBy = <T extends Record<string, any>>(
    array: T[],
    gutterFunc: (item: T) => string | number,
) => {
    const grouped: Record<string, T[]> = {};
    array.forEach((item) => {
        const gutter = gutterFunc(item);
        if (!grouped[gutter]) {
            grouped[gutter] = [];
        }
        grouped[gutter].push(item);
    });
    return grouped;
};
