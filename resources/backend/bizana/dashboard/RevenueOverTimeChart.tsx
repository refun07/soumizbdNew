import React, { useEffect, useState } from 'react';

interface RevenueOverTimeData {
    date: string;
    revenue: number;
}

interface RevenueOverTimeChartProps {
    data?: RevenueOverTimeData[];
    height?: number;
}

const RevenueOverTimeChart: React.FC<RevenueOverTimeChartProps> = ({
    data = null,
    height = 96
}) => {
    // Default data if none provided (matches net profit trend pattern)
    const defaultData: RevenueOverTimeData[] = [
        { date: '2024-01-01', revenue: 45000 },
        { date: '2024-02-01', revenue: 52000 },
        { date: '2024-03-01', revenue: 48000 },
        { date: '2024-04-01', revenue: 61000 },
        { date: '2024-05-01', revenue: 58000 },
        { date: '2024-06-01', revenue: 67000 },
    ];

    // Transform API data to chart format
    const transformApiData = (apiData: RevenueOverTimeData[]): { x: string; y: number }[] => {
        // Always use default data as fallback when API data is empty/undefined
        if (!apiData || apiData.length === 0) {
            return defaultData.map(item => ({
                x: new Date(item.date).toLocaleDateString('en-US', { month: 'short' }),
                y: item.revenue
            }));
        }

        // Transform API data
        const transformed = apiData.map(item => ({
            x: new Date(item.date).toLocaleDateString('en-US', { month: 'short' }),
            y: item.revenue
        }));

        // Validate transformed data - if any invalid values, fall back to default
        const hasValidData = transformed.some(item => item && typeof item.y === 'number' && item.y > 0);
        if (!hasValidData) {
            return defaultData.map(item => ({
                x: new Date(item.date).toLocaleDateString('en-US', { month: 'short' }),
                y: item.revenue
            }));
        }

        return transformed;
    };

    const chartData = transformApiData(data || defaultData);

    const options: any = {
        series: [
            {
                name: 'Revenue',
                data: chartData.map(item => item.y), // ApexCharts expects array of values for area chart
            },
        ],
        chart: {
            type: 'area',
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
            sparkline: {
                enabled: true,
            },
            redrawOnParentResize: true,
            redrawOnWindowResize: true,
        },
        stroke: {
            curve: 'smooth',
            width: 2,
            colors: ['#ffffff'],
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.5,
                gradientToColors: ['rgba(255, 255, 255, 0.1)'],
                inverseColors: false,
                opacityFrom: 0.6,
                opacityTo: 0.1,
                stops: [0, 90, 100],
            },
            colors: ['rgba(255, 255, 255, 0.8)'],
        },
        grid: {
            show: false,
            borderColor: 'transparent',
        },
        xaxis: {
            categories: chartData.map(item => item.x),
            labels: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
            crosshairs: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                show: false,
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        tooltip: {
            enabled: true,
            theme: 'dark',
            style: {
                fontSize: '12px',
                fontFamily: 'Inter, sans-serif',
            },
            y: {
                formatter: function (val: number) {
                    return new Intl.NumberFormat('en-BD', {
                        style: 'currency',
                        currency: 'BDT',
                        minimumFractionDigits: 0,
                    }).format(val) + ' Tk';
                },
            },
            x: {
                formatter: function (val: string) {
                    return val;
                },
            },
            marker: {
                show: false,
            },
            fixed: {
                enabled: false,
                position: 'topRight',
            },
        },
        markers: {
            size: 0,
            colors: '#ffffff',
            strokeColors: '#ffffff',
            strokeWidth: 2,
            strokeOpacity: 0.9,
            fillOpacity: 1,
            discrete: [],
            shape: 'circle',
            radius: 2,
            hover: {
                size: 4,
                sizeOffset: 2,
            },
        },
        responsive: [
            {
                breakpoint: 640,
                options: {
                    chart: {
                        height: height - 16,
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
            <div className="w-full h-full flex items-center justify-center">
                <div className="animate-pulse w-full h-4 bg-white/20 rounded-full"></div>
            </div>
        );
    }

    return (
        <div className="w-full h-full">
            <Chart
                options={options}
                series={options.series}
                type="area"
                height={height}
            />
        </div>
    );
};

export default RevenueOverTimeChart;