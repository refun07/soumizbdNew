import {
    EChartsOption,
    LegendComponentOption,
    TooltipComponentOption,
} from 'echarts';

export const colors = [
    {
        low: '#92A4FF',
        high: '#374CB9',
    },
    {
        low: '#FFE9A5',
        high: '#FFC514',
    },
    {
        low: '#FFC38B',
        high: '#FF8642',
    },
    {
        low: '#FFA5A5',
        high: '#FF6B6B',
    },
    {
        low: '#E3A7FF',
        high: '#C242FF',
    },
    {
        low: '#A8D9FF',
        high: '#34A6FF',
    },
];

const getValue = (param: any) => {
    if (param.data) {
        return param.data[param.dimensionNames[param.encode.y]];
    }

    return Number(param.data[param.dimensionNames[param.encode.y]]).toFixed(1);
};

export const tooltipOptions: TooltipComponentOption = {
    // reset styles
    padding: 0,
    backgroundColor: 'transparent',
    borderWidth: 0,

    formatter: function (params) {
        if (params instanceof Array) {
            return `
            <div class="bg-popover text-popover-foreground rounded-md text-[10px] px-2 py-1 lg:px-3 lg:py-3">
                <div class="mb-2">
                    <span>${params[0].name}</span>
                </div>
                ${params
                    .map((param) => {
                        const { seriesType, seriesName, value, percent } =
                            param;

                        return `
                        <div class="flex justify-between gap-10">
                            <div class="flex items-center gap-1">
                                ${param.marker}
                                <span>${seriesName}</span>
                            </div>

                            <span>
                                ${
                                    seriesType === 'pie'
                                        ? `${percent.toFixed(1)}% (${value})`
                                        : Number(value).toFixed(1)
                                }
                            </span>
                        </div>
                    `;
                    })
                    .join('')}
            </div>
        `;
        }

        const { seriesName, seriesType, name, value, percent } = params;

        return `
            <div class="bg-background rounded-md px-3 py-3">
                <div class="mb-2">
                    <span>${seriesName}</span>
                </div>
                <div class="flex justify-between gap-10">
                    <div class="flex items-center gap-1">
                        ${params.marker}
                        <span class="font-bold">${name}</span>
                    </div>
                    <span>${
                        seriesType === 'pie'
                            ? `${percent.toFixed(1)}% (${value})`
                            : Number(value).toFixed(1)
                    }</span>
                </div>
            </div>
        `;
    },
};

export const legendOptions: LegendComponentOption = {
    bottom: 0,
    left: 'center',
    itemWidth: 10,
    itemHeight: 10,
    borderRadius: 100,
    textStyle: {
        fontSize: 12,
        color: '#8A8A8A',
        padding: [0, 10],
        lineHeight: 1,
    },
};

export const areaGraphOptions: Partial<EChartsOption> = {
    grid: {
        left: 0,
        right: 0,
        top: 80,
        bottom: 35,
    },
    xAxis: {
        type: 'category',
        axisLabel: {
            color: '#8A8A8A',
        },
        axisLine: {
            lineStyle: {
                color: '#EAEFF4',
            },
        },
    },
    yAxis: {
        type: 'value',
        axisLabel: {
            color: '#8A8A8A',
            baseline: 'bottom',
            padding: [0, 0, 5, 0],
            inside: true,
        },
        axisLine: {
            lineStyle: {
                color: '#EAEFF4',
            },
        },
        splitLine: {
            show: false,
        },
    },
};
