// Chart data interfaces for Bizana Dashboard

export interface GrowthData {
    month: string;
    primary: number;
    secondary: number;
}

export interface ExpenseData {
    label: string;
    value: number;
    color: string;
}

export interface ChartProps {
    data: any[];
    height?: number;
    colors?: string[];
}

export interface BarChartProps extends ChartProps {
    title?: string;
    showLegend?: boolean;
    primaryLabel?: string;
    secondaryLabel?: string;
}

export interface PieChartProps extends ChartProps {
    title?: string;
    showLegend?: boolean;
    labelsPosition?: 'inside' | 'outside' | 'right';
    donutMode?: boolean;
}