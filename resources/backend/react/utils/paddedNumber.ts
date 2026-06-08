export const paddedNumber = (num?: number, pad?: number): string => {
    return (num || 0).toString().padStart(pad || 2, '0');
};
