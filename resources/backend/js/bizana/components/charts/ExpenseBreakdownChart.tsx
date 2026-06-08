import React, { useEffect, useState, useRef } from 'react';
import { ExpenseData, PieChartProps } from '../../types/charts';

const ExpenseBreakdownChart: React.FC<PieChartProps> = ({
    data = null,
    height = 300,
    showLegend = true,
    donutMode = true,
}) => {
    // Default expense data if none provided (matches cached data structure)
    const defaultData: ExpenseData[] = [
        { label: 'Salaries', value: 50000, color: '#4285F4' },
        { label: 'Office Rent', value: 45000, color: '#34A853' },
        { label: 'Marketing & Advertising', value: 18000, color: '#FBBC04' },
        { label: 'Professional Services', value: 14000, color: '#EA4335' },
        { label: 'Travel Expenses', value: 11000, color: '#9C27B0' },
        { label: 'Training & Development', value: 10000, color: '#FF6B6B' },
        { label: 'Insurance', value: 9500, color: '#00BCD4' },
        { label: 'Utilities', value: 4300, color: '#FF9800' },
    ];

    // Color palette for categories
    const colorPalette = [
        '#4285F4', '#34A853', '#FBBC04', '#EA4335', '#9C27B0',
        '#FF6B6B', '#00BCD4', '#FF9800', '#795548', '#607D8B',
        '#E91E63', '#9C27B0', '#673AB7', '#3F51B5', '#2196F3'
    ];

    // Transform API data to chart format
    const transformApiData = (apiData: any): ExpenseData[] => {
        if (!apiData || !apiData.categories || !apiData.values) {
            return defaultData;
        }

        return apiData.categories.map((category: string, index: number) => ({
            label: category,
            value: parseFloat(apiData.values[index]) || 0,
            color: colorPalette[index % colorPalette.length],
        }));
    };

    const chartData = data ? transformApiData(data) : defaultData;
    const total = chartData.reduce((sum, item) => sum + item.value, 0);

    const options: any = {
        series: chartData.map((item) => item.value),
        chart: {
            type: 'pie',
            height: height,
            toolbar: {
                show: false,
            },
            background: 'transparent',
            animations: {
                enabled: true,
                easing: 'easeout',
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 50,
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 300,
                },
            },
            redrawOnParentResize: true,
            redrawOnWindowResize: true,
        },
        labels: chartData.map((item) => item.label),
        colors: chartData.map((item) => item.color),
        plotOptions: {
            pie: {
                donut: {
                    size: donutMode ? '65%' : '0',
                    labels: {
                        show: true,
                        total: {
                            show: false,
                            showAlways: true,
                            label: 'Total',
                            fontSize: '14px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 500,
                            color: '#6B7280',
                            formatter: function () {
                                return (
                                    new Intl.NumberFormat('en-BD', {
                                        style: 'currency',
                                        currency: 'BDT',
                                        minimumFractionDigits: 0,
                                    }).format(total) + ' Tk'
                                );
                            },
                        },
                        value: {
                            show: true,
                            fontSize: '16px',
                            fontFamily: 'Inter, sans-serif',
                            fontWeight: 600,
                            color: '#111827',
                            offsetY: -8,
                        },
                    },
                },
                expandOnClick: true,
                offsetX: 0,
                offsetY: 0,
                customScale: 1.1,
            },
        },
        dataLabels: {
            enabled: true,
            formatter: function (val: number, opts?: any) {
                const percentage = val.toFixed(1);
                return `${percentage}%`;
            },
            style: {
                fontSize: '11px',
                fontFamily: 'Inter, sans-serif',
                fontWeight: 500,
                colors: ['#FFFFFF'],
            },
            dropShadow: {
                enabled: false,
            },
        },
        tooltip: {
            y: {
                formatter: function (val: number, opts?: any) {
                    const percentage = ((val / total) * 100).toFixed(1);
                    const formattedValue = new Intl.NumberFormat('en-BD', {
                        style: 'currency',
                        currency: 'BDT',
                        minimumFractionDigits: 0,
                    }).format(val);
                    return `${formattedValue} Tk (${percentage}%)`;
                },
            },
            style: {
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif',
            },
            theme: 'light',
        },
        legend: {
            show: showLegend,
            position: 'bottom',
            horizontalAlign: 'center',
            fontSize: '10px',
            fontFamily: 'Inter, sans-serif',
            fontWeight: 400,
            labels: {
                colors: ['#6B7280'],
                useSeriesColors: false,
            },
            markers: {},
            itemMargin: {},
            floating: false,
            offsetY: 8,
            offsetX: 0,
        },
        grid: {
            padding: {
                top: 0,
                bottom: 0,
                left: 0,
                right: 0,
            },
        },
        responsive: [
            {
                breakpoint: 1024,
                options: {
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '9px',
                        itemMargin: {
                            horizontal: 6,
                            vertical: 3,
                        },
                        markers: {
                            width: 5,
                            height: 5,
                        },
                        maxHeight: 50,
                    },
                    plotOptions: {
                        pie: {
                            customScale: 1,
                        },
                    },
                },
            },
            {
                breakpoint: 640,
                options: {
                    chart: {
                        height: 320,
                    },
                    plotOptions: {
                        pie: {
                            customScale: 1.0,
                        },
                    },
                    legend: {
                        position: 'bottom',
                        horizontalAlign: 'center',
                        fontSize: '9px',
                        itemMargin: {
                            horizontal: 5,
                            vertical: 2,
                        },
                        markers: {
                            width: 4,
                            height: 4,
                        },
                        maxHeight: 45,
                    },
                    dataLabels: {
                        style: {
                            fontSize: '10px',
                        },
                    },
                },
            },
        ],
    };

    const [Chart, setChart] = useState<any>(null);

    useEffect(() => {
        import('react-apexcharts').then((module) => {
            setChart(() => module.default);
        });
    }, []);

    if (!Chart) {
        return (
            <div
                className="w-full flex items-center justify-center"
                style={{ height }}
            >
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    return (
        <div className="w-full flex items-center justify-center px-2">
            <Chart
                options={options}
                series={options.series}
                type={donutMode ? 'donut' : 'pie'}
                height={height}
            />
        </div>
    );
};

export default ExpenseBreakdownChart;
