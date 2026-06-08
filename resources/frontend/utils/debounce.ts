export const debounce = (fn: any, delay: number = 500) => {
    let timerId: number | null = null;

    return function (...args: any) {
        if (timerId) {
            clearTimeout(timerId);
        }

        timerId = setTimeout(() => {
            fn(...args);
        }, delay);
    };
};
