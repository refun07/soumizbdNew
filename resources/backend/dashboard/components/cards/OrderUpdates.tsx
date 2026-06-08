import React, { useEffect, useState } from 'react';
import { FaCartShopping } from 'react-icons/fa6';
import { translate } from '../../../react/utils/translate';
import { IOrderUpdates } from '../../types/response';
import { getOrderUpdates } from '../../utils';

const updateColors = [
    {
        bg: 'bg-[#EDF8DA]',
        text: 'text-black',
        iconBg: 'bg-[#CEE3AB]',
        icon: 'text-foreground',
    },
    {
        bg: 'bg-[#DAF8F8]',
        text: 'text-black',
        iconBg: 'bg-[#B6E8E8]',
        icon: 'text-[#E0FFFF]',
    },
    {
        bg: 'bg-[#F8DADA]',
        text: 'text-black',
        iconBg: 'bg-[#F4C5C5]',
        icon: 'text-[#FBE0E0]',
    },
    {
        bg: 'bg-[#DADBF8]',
        text: 'text-black',
        iconBg: 'bg-[#C8CAF5]',
        icon: 'text-[#DBDCFF]',
    },
];

const getColorItem = (index: number) => {
    return updateColors[index % updateColors.length];
};

const OrderUpdates = () => {
    const [orders, setOrders] = useState<IOrderUpdates[]>([]);

    useEffect(() => {
        getOrderUpdates().then(setOrders);
    }, []);

    return (
        <div className="md:col-span-6 2xl:col-span-4 card">
            <h5 className="card__title border-none">
                {translate('Order Status')}
                <span className="text-muted ml-2">({orders.length})</span>
            </h5>

            <div className="card__content pt-0 space-y-2 max-h-[450px] overflow-y-auto">
                {orders.map((order, index) => (
                    <div
                        className={`flex items-center gap-6 py-3 px-5 rounded-md text-black ${
                            getColorItem(index).bg
                        }`}
                        key={order.id}
                    >
                        <div
                            className={`flex items-center justify-center text-xl w-12 h-12 rounded-full ${
                                getColorItem(index).iconBg
                            }  ${getColorItem(index).icon}`}
                        >
                            <FaCartShopping />
                        </div>

                        <div className="grow">
                            <h4 className="text-sm">{order.note}</h4>
                            <div className="flex items-center gap-2 text-muted">
                                <span>{order.createdTime}</span>
                                <span className="w-[5px] h-[5px] bg-blue-400 rounded-full"></span>
                                <span>{order.createdDate}</span>
                                <span className="w-[5px] h-[5px] bg-blue-400 rounded-full"></span>
                                <span>{order.status}</span>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
};

export default OrderUpdates;
