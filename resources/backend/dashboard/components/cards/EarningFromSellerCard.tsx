import React, { useEffect, useState } from 'react';
import { paddedNumber } from '../../../../frontend/utils/numberFormatter';
import { translate } from '../../../react/utils/translate';
import { ISellers } from '../../types/response';
import { getEarningsFromSellers } from '../../utils';
import SellerChart from '../charts/SellerChart';

const EarningFromSellerCard = () => {
    const [sellers, setSellers] = useState<ISellers[]>([]);
    const [chartData, setChartData] = useState<
        { name: string; value: number }[]
    >([]);

    useEffect(() => {
        getEarningsFromSellers().then((data) => {
            setSellers(data);

            const chartData: { name: string; value: number }[] = [];
            for (let i = data.length - 1; i >= 0; i--) {
                chartData.push({
                    name: data[i].name,
                    value: data[i].earningFromSellers,
                });
            }

            setChartData(chartData);
        });
    }, []);

    return (
        <div className="xl:col-span-3 card">
            <h5 className="card__title border-none pb-0">
                {translate('Earnings From Seller')}
                <span className="text-muted ml-2">
                    ({paddedNumber(sellers.length)})
                </span>
            </h5>

            <div className="card__content h-[450px]">
                <SellerChart data={chartData} />
            </div>
        </div>
    );
};

export default EarningFromSellerCard;
