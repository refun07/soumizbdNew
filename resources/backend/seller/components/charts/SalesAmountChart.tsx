import { EChartsOption } from 'echarts';
import ReactEcharts from 'echarts-for-react';
import React from 'react';
import { currencyFormatter } from '../../../../frontend/utils/numberFormatter';
import { dateFormatter } from '../../../react/utils/dateFormatter';
import { ISaleAmountChart } from '../../types/response';
import { areaGraphOptions, tooltipOptions } from './common';

interface Props {
    data: ISaleAmountChart;
}

const SalesAmountChart = ({ data }: Props) => {
    const graphEl = React.useRef<ReactEcharts>(null);

    const options: EChartsOption = {
        ...areaGraphOptions,
        grid: {
            ...areaGraphOptions.grid,
            top: 20,
        },
        xAxis: {
            ...areaGraphOptions.xAxis,
            type: 'category',
            data: data.days.map((day) => {
                return dateFormatter(day, 'DD MMM');
            }),
        },
        yAxis: {
            ...areaGraphOptions.yAxis,
            axisLabel: {
                ...(areaGraphOptions.yAxis as any)?.axisLabel!,
                formatter: (value) => {
                    return currencyFormatter(value);
                },
            },
        },
        tooltip: {
            ...tooltipOptions,
            trigger: 'axis',
        },
        legend: {
            show: false,
        },
        series: [
            {
                type: 'line',
                name: 'This Week',
                smooth: true,
                data: data.totalAmounts,
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

export default SalesAmountChart;
