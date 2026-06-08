import { EChartsOption, graphic } from 'echarts';
import ReactEcharts from 'echarts-for-react';
import React from 'react';
import { currencyFormatter } from '../../../../frontend/utils/numberFormatter';
import { areaGraphOptions, colors, tooltipOptions } from './common';

interface Props {
    data: { name: string; value: number }[];
}

const SellerChart = ({ data }: Props) => {
    const graphEl = React.useRef<ReactEcharts>(null);

    const options: EChartsOption = {
        ...areaGraphOptions,
        tooltip: {
            ...tooltipOptions,
            trigger: 'axis',
        },
        grid: {
            top: 10,
            left: 0,
            right: 5,
            bottom: 10,
            containLabel: true,
        },
        xAxis: {
            ...areaGraphOptions.xAxis,
            type: 'value',
            min: 10,
            axisLabel: {
                show: false,
            },
            splitLine: {
                show: false,
            },
        },
        yAxis: [
            {
                ...areaGraphOptions.yAxis,
                type: 'category',
                axisLabel: {
                    ...(areaGraphOptions.yAxis as any)?.axisLabel!,
                    fontSize: 14,
                    padding: [18, 0],
                    margin: 0,
                },
                axisLine: {
                    show: false,
                },
                data: data.map((item) => item.name),
            },
            {
                ...areaGraphOptions.yAxis,
                type: 'category',
                axisLabel: {
                    padding: 0,
                    margin: 0,
                    baseline: 'middle',
                    inside: false,
                    formatter: (value) => {
                        return currencyFormatter(+value);
                    },
                },
                axisLine: {
                    show: false,
                },
                axisTick: {
                    show: false,
                },
                data: data.map((item) => item.value),
            },
        ],
        series: [
            {
                name: '2011',
                type: 'bar',
                barWidth: 18,
                data: data.map((item, index) => {
                    return {
                        value: item.value,
                        itemStyle: {
                            color: new graphic.LinearGradient(1, 0, 0, 1, [
                                { offset: 0, color: colors[index % 6].low },
                                { offset: 1, color: colors[index % 6].high },
                            ]),
                        },
                    };
                }),
            },
        ],
    };

    return (
        <ReactEcharts
            option={options}
            notMerge={true}
            lazyUpdate={true}
            ref={graphEl}
            className="!h-full"
        />
    );
};

export default SellerChart;
