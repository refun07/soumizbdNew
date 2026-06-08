import dayjs from 'dayjs';
import { timelines } from '../../seller/types/response';

export const dashboardDateFilterOptions: {
    label: string;
    value: (typeof timelines)[number];
    rangeStr?: string;
}[] = [
    {
        label: 'Today',
        value: 'today',
        rangeStr:
            dayjs().startOf('day').format('DD MMM') +
            ' - ' +
            dayjs().endOf('day').format('DD MMM'),
    },
    {
        label: 'Yesterday',
        value: 'yesterday',
        rangeStr: [
            dayjs().subtract(1, 'day').startOf('day').format('DD MMM'),
            dayjs().subtract(1, 'day').endOf('day').format('DD MMM'),
        ].join(' - '),
    },
    {
        label: 'This Week',
        value: 'thisWeek',
        rangeStr: [
            dayjs().startOf('week').format('DD MMM'),
            dayjs().endOf('week').format('DD MMM'),
        ].join(' - '),
    },
    {
        label: 'This Month',
        value: 'thisMonth',
        rangeStr: [
            dayjs().startOf('month').format('DD MMM'),
            dayjs().endOf('month').format('DD MMM'),
        ].join(' - '),
    },
    {
        label: 'This Year',
        value: 'thisYear',
        rangeStr: [
            dayjs().startOf('year').format('DD MMM, YYYY'),
            dayjs().endOf('year').format('DD MMM, YYYY'),
        ].join(' - '),
    },
    { label: 'Overall', value: 'overall', rangeStr: 'Overall' },
];

// Get the current date
const currentDate = dayjs().startOf('day');

// Calculate the difference in days to the last Monday (1 for Monday)
const daysToLastMonday = (currentDate.day() + 7 - 1) % 7;

// Subtract the days to get the last Monday
const lastMonday = currentDate.subtract(daysToLastMonday, 'day');
const nextSunday = lastMonday.add(6, 'day');

export const dateRangeFilterOptions: {
    label: string;
    value: (typeof timelines)[number];
    rangeStr?: string;
}[] = [
    {
        label: 'This Week',
        value: 'thisWeek',
        rangeStr: [
            lastMonday.format('DD MMM'),
            nextSunday.format('DD MMM'),
        ].join(' - '),
    },
    {
        label: 'This Month',
        value: 'thisMonth',
        rangeStr: [
            dayjs().startOf('month').format('DD MMM'),
            dayjs().endOf('month').format('DD MMM'),
        ].join(' - '),
    },
    {
        label: 'This Year',
        value: 'thisYear',
        rangeStr: [
            dayjs().startOf('year').format('DD MMM, YYYY'),
            dayjs().endOf('year').format('DD MMM, YYYY'),
        ].join(' - '),
    },
];
