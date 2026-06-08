import React, { useEffect, useState, useRef } from 'react';
import { GrowthData, BarChartProps } from '../../types/charts';

const GrowthComparisonChart: React.FC<BarChartProps> = ({
    data = null,
    height = 300,
    showLegend = true,
    primaryLabel = 'Current Period',
    secondaryLabel = 'Previous Period'
}) => {
    // Default data if none provided (matches cached data structure)
    const defaultData: GrowthData[] = [
        { month: 'Jan', primary: 4500, secondary: 3800 },
        { month: 'Feb', primary: 5200, secondary: 4100 },
        { month: 'Mar', primary: 4800, secondary: 3900 },
        { month: 'Apr', primary: 6100, secondary: 4500 },
        { month: 'May', primary: 5800, secondary: 4200 },
        { month: 'Jun', primary: 6700, secondary: 5100 },
    ];

    // Transform API data to chart format
    const transformApiData = (apiData: any): GrowthData[] => {
        if (!apiData || !apiData.current || !apiData.previous) {
            return defaultData;
        }

        // Create a map of dates to data for easier lookup
        const currentMap = new Map(
            apiData.current.map((item: any) => [
                new Date(item.date).toLocaleDateString('en-US', { month: 'short' }),
                item.revenue
            ])
        );

        const previousMap = new Map(
            apiData.previous.map((item: any) => [
                new Date(item.date).toLocaleDateString('en-US', { month: 'short' }),
                item.revenue
            ])
        );

        // Get all unique months from both periods
        const allMonths = Array.from(new Set([
            ...Array.from(currentMap.keys()),
            ...Array.from(previousMap.keys())
        ]));

        return allMonths.map(month => ({
            month,
            primary: currentMap.get(month) || 0,
            secondary: previousMap.get(month) || 0,
        }));
    };

    const chartData = data ? transformApiData(data) : defaultData;

    const options: any = {
        series: [
            {
                name: primaryLabel,
                data: chartData.map(item => item.primary),
            },
            {
                name: secondaryLabel,
                data: chartData.map(item => item.secondary),
            },
        ],
        chart: {
            type: 'bar',
            height: height,
            toolbar: {
                show: false,
            },
            background: 'transparent',
            animations: {
                enabled: true,
                easing: 'easeout',
                speed: 700,
                animateGradually: {
                    enabled: true,
                    delay: 40,
                },
                dynamicAnimation: {
                    enabled: true,
                    speed: 250,
                },
            },
            redrawOnParentResize: true,
            redrawOnWindowResize: true,
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '60%',
                borderRadius: 4,
                borderRadiusApplication: 'end',
                borderRadiusWhenStacked: 'last',
            },
        },
        dataLabels: {
            enabled: false,
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent'],
        },
        xaxis: {
            categories: chartData.map(item => item.month),
            labels: {
                style: {
                    colors: ['#6B7280'],
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif',
                },
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: ['#6B7280'],
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif',
                },
                formatter: function (value: number) {
                    return value >= 1000 ? `${(value / 1000).toFixed(0)}k` : value.toString();
                },
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        fill: {
            opacity: 1,
            colors: ['#4285F4', '#FBBC04'],
        },
        colors: ['#4285F4', '#FBBC04'],
        tooltip: {
            y: {
                formatter: function (val: number) {
                    return new Intl.NumberFormat('en-BD', {
                        style: 'currency',
                        currency: 'BDT',
                        minimumFractionDigits: 0,
                    }).format(val) + ' Tk';
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
            position: 'top',
            horizontalAlign: 'center',
            fontSize: '12px',
            fontFamily: 'Inter, sans-serif',
            labels: {
                colors: ['#6B7280'],
            },
            markers: {
                radius: 4,
            },
            itemMargin: {
                horizontal: 16,
                vertical: 8,
            },
        },
        grid: {
            borderColor: '#F3F4F6',
            strokeDashArray: 4,
            yaxis: {
                lines: {
                    show: true,
                },
            },
            xaxis: {
                lines: {
                    show: false,
                },
            },
        },
        responsive: [
            {
                breakpoint: 640,
                options: {
                    plotOptions: {
                        bar: {
                            columnWidth: '80%',
                        },
                    },
                    xaxis: {
                        labels: {
                            style: {
                                fontSize: '10px',
                            },
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
            <div className="w-full flex items-center justify-center" style={{ height }}>
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    return (
        <div className="w-full">
            <Chart
                options={options}
                series={options.series}
                type="bar"
                height={height}
            />
        </div>
    );
};

export default GrowthComparisonChart;