import { EChartsOption, SeriesOption, graphic } from 'echarts';
import ReactEcharts from 'echarts-for-react';
import React from 'react';
import { translate } from '../../../react/utils/translate';

import { colors, legendOptions, tooltipOptions } from './common';

interface Props {
    data?: { name: string; value: number }[];
}

const TopCategoryChart = ({ data = [] }: Props) => {
    const commonSeriesOptions: Partial<SeriesOption> = {
        name: translate('Search Category'),
        type: 'pie',
        top: '-13%',
        bottom: '5%',
        emphasis: {
            disabled: true,
        },
        data: data.map((item, index) => {
            return {
                ...item,
                itemStyle: {
                    color: new graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: colors[index].low },
                        { offset: 1, color: colors[index].high },
                    ]),
                },
            };
        }),
    };

    const options: EChartsOption = {
        tooltip: {
            ...tooltipOptions,
            trigger: 'item',
        },
        legend: {
            ...legendOptions,
        },
        series: [
            {
                ...commonSeriesOptions,
                radius: ['60%', '70%'],

                label: {
                    width: 35,
                    height: 35,
                    position: 'inside',
                    backgroundColor: '#fff',
                    borderRadius: 100,
                    fontSize: 10,
                    color: '#A6A6A6',

                    shadowColor: '#00000044',
                    shadowOffsetX: 5,
                    shadowOffsetY: 5,
                    shadowBlur: 20,

                    formatter: (series) => {
                        return Math.round(series.percent!) + '%';
                    },
                },
            },
            {
                ...commonSeriesOptions,
                radius: ['50%', '68%'],

                label: {
                    show: false,
                },
            },
        ],
    };

    return <ReactEcharts option={options} notMerge={true} lazyUpdate={true} />;
};

export default TopCategoryChart;
