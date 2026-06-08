import dayjs from 'dayjs';

export const dateFormatter = (
    date: string | number | Date,
    template?: string,
) => {
    return dayjs(date).format(template || 'DD MMM, YYYY');
};
