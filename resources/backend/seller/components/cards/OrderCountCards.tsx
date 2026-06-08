import React, { useEffect, useState } from 'react';
import { FaCartShopping } from 'react-icons/fa6';
import { paddedNumber } from '../../../../frontend/utils/numberFormatter';
import { translate } from '../../../react/utils/translate';
import { IOrderCounts } from '../../types/response';
import { getOrderCounts } from '../../utils';

const OrderCountCards = () => {
    const [orderCounts, setOrderCounts] = useState<IOrderCounts>({
        confirmed: 0,
        delivered: 0,
        cancelled: 0,
        orderPlaced: 0,
        processing: 0,
        shipped: 0,
    });

    useEffect(() => {
        getOrderCounts().then(setOrderCounts);
    }, []);

    return (
        <>
            <div className="flex items-center justify-between bg-background rounded-md px-7 py-12">
                <div className="flex items-center gap-3">
                    <span className="text-2xl text-primary">
                        <FaCartShopping />
                    </span>
                    {translate('Order Placed')}
                </div>
                <span className="text-xl sm:text-[26px] font-semibold">
                    {paddedNumber(orderCounts.orderPlaced)}
                </span>
            </div>
            <div className="flex items-center justify-between bg-background rounded-md px-7 py-12">
                <div className="flex items-center gap-3">
                    <span className="text-2xl text-primary">
                        <FaCartShopping />
                    </span>
                    {translate('Order Confirmed')}
                </div>
                <span className="text-xl sm:text-[26px] font-semibold">
                    {paddedNumber(orderCounts.confirmed)}
                </span>
            </div>
            <div className="flex items-center justify-between bg-background rounded-md px-7 py-12">
                <div className="flex items-center gap-3">
                    <span className="text-2xl text-primary">
                        <FaCartShopping />
                    </span>
                    {translate('Order Shipped')}
                </div>
                <span className="text-xl sm:text-[26px] font-semibold">
                    {paddedNumber(orderCounts.shipped)}
                </span>
            </div>
            <div className="flex items-center justify-between bg-background rounded-md px-7 py-12">
                <div className="flex items-center gap-3">
                    <span className="text-2xl text-primary">
                        <FaCartShopping />
                    </span>
                    {translate('Processing')}
                </div>
                <span className="text-xl sm:text-[26px] font-semibold">
                    {paddedNumber(orderCounts.processing)}
                </span>
            </div>
            <div className="flex items-center justify-between bg-background rounded-md px-7 py-12">
                <div className="flex items-center gap-3">
                    <span className="text-2xl text-primary">
                        <FaCartShopping />
                    </span>
                    {translate('Order Delivered')}
                </div>
                <span className="text-xl sm:text-[26px] font-semibold">
                    {paddedNumber(orderCounts.delivered)}
                </span>
            </div>
            <div className="flex items-center justify-between bg-background rounded-md px-7 py-12">
                <div className="flex items-center gap-3">
                    <span className="text-2xl text-primary">
                        <FaCartShopping />
                    </span>
                    {translate('Order Canceled')}
                </div>
                <span className="text-xl sm:text-[26px] font-semibold">
                    {paddedNumber(orderCounts.cancelled)}
                </span>
            </div>
        </>
    );
};

export default OrderCountCards;
