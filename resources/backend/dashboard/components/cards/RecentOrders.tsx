import React, { useEffect, useState } from 'react';
import { GoArrowRight } from 'react-icons/go';
import {
    currencyFormatter,
    paddedNumber,
} from '../../../../frontend/utils/numberFormatter';
import Label from '../../../react/components/inputs/Label';
import { translate } from '../../../react/utils/translate';
import { IOrder } from '../../types/response';
import { getRecentOrders } from '../../utils';

const orderStatusColors = {
    processing: 'bg-badge-processing',
    confirmed: 'bg-badge-confirmed',
    shipped: 'bg-badge-shipped',
    delivered: 'bg-badge-success',
    cancelled: 'bg-badge-danger',
    default: 'bg-badge-default',
};

const RecentOrders = () => {
    const [orders, setOrders] = useState<IOrder[]>([]);

    useEffect(() => {
        getRecentOrders().then(setOrders);
    }, []);

    return (
        <div className="md:col-span-6 2xl:col-span-8 card">
            <div className="card__title border-none flex justify-between">
                <h5 className="text-sm sm:text-base font-medium">
                    {translate('Recent Orders')}
                    <span className="text-muted ml-2">
                        ({paddedNumber(orders.length)})
                    </span>
                </h5>

                <a
                    href="/admin/orders"
                    className="inline-flex items-center gap-1 text-secondary"
                >
                    {translate('View All')}
                    <GoArrowRight />
                </a>
            </div>

            <div className="max-h-[450px] overflow-auto">
                <table className="w-full min-w-[600px] ">
                    <thead className="theme-table__head">
                        <tr>
                            <th>{translate('Customer')}</th>
                            <th>{translate('Invoice No')}</th>
                            <th>{translate('Value')}</th>
                            <th>{translate('Date')}</th>
                            <th>{translate('Status')}</th>
                        </tr>
                    </thead>

                    <tbody className="theme-table__body">
                        {!orders.length ? (
                            <tr>
                                <td colSpan={5} className="text-center">
                                    <p>{translate('No orders found')}</p>
                                </td>
                            </tr>
                        ) : (
                            orders.map((order) => (
                                <tr
                                    className="theme-table__row "
                                    key={order.id}
                                >
                                    <td>{order.customerName}</td>
                                    <td>{order.codeToShow}</td>
                                    <td>
                                        {currencyFormatter(order.totalAmount)}
                                    </td>
                                    <td>{order.createdDate}</td>
                                    <td>
                                        <Label
                                            className={`py-1 capitalize text-white ${
                                                orderStatusColors[
                                                    order.deliveryStatus
                                                ]
                                            }`}
                                        >
                                            {order.deliveryStatusToShow}
                                        </Label>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default RecentOrders;
