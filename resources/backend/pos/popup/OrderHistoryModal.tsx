import React, { useEffect, useState } from 'react';
import { LiaPrintSolid, LiaTimesSolid } from 'react-icons/lia';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import Button from '../../react/components/inputs/Button';
import { IRecentOrder, InvoiceData } from '../types';
import { getOrderInvoice, getRecentOrders } from '../utils/actions';
import { printThermalReceipt } from '../utils/thermalReceiptTemplate';
import ModalWrapper from './ModalWrapper';

const OrderHistoryModal = () => {
    const [isActive, setIsActive] = useState(false);
    const [orders, setOrders] = useState<IRecentOrder[]>([]);
    const [isLoading, setIsLoading] = useState(false);
    const [printingOrderId, setPrintingOrderId] = useState<number | null>(null);

    useEffect(() => {
        if (!isActive) return;

        setIsLoading(true);
        getRecentOrders()
            .then(setOrders)
            .catch((error) => {
                console.error('Failed to load recent orders:', error);
            })
            .finally(() => setIsLoading(false));
    }, [isActive]);

    const handleClose = () => setIsActive(false);

    const handlePrintInvoice = async (orderId: number) => {
        try {
            setPrintingOrderId(orderId);
            const invoiceData = await getOrderInvoice(orderId);
            printThermalReceipt(invoiceData);
        } catch (error) {
            console.error('Failed to get invoice:', error);
        } finally {
            setPrintingOrderId(null);
        }
    };

    const paymentMethodLabels: Record<string, string> = {
        cash: 'Cash',
        card: 'Card',
        cash_on_delivery: 'Cash on Delivery',
        card_on_delivery: 'Card on Delivery',
    };

    return (
        <>
            <Button
                variant="primary"
                size="md"
                onClick={() => setIsActive(true)}
            >
                Order History
            </Button>

            <ModalWrapper isActive={isActive} onClose={handleClose} size="lg">
                <div className="theme-modal__header">
                    <h5>Recent Orders</h5>

                    <button
                        className="text-xl text-white sm:text-secondary-light"
                        onClick={handleClose}
                    >
                        <LiaTimesSolid />
                    </button>
                </div>

                <div className="theme-modal__body">
                    {isLoading ? (
                        <p className="text-center py-5">Loading orders...</p>
                    ) : !orders.length ? (
                        <p className="text-center py-5">No recent orders found</p>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="theme-table">
                                <thead className="theme-table__head">
                                    <tr>
                                        <th>Order</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th className="w-[80px]">Action</th>
                                    </tr>
                                </thead>
                                <tbody className="theme-table__body">
                                    {orders.map((order) => (
                                        <tr key={order.id} className="theme-table__row">
                                            <td className="font-medium">{order.order_code}</td>
                                            <td>{order.created_at}</td>
                                            <td>{order.customer_name}</td>
                                            <td>{currencyFormatter(order.total_amount)}</td>
                                            <td>
                                                {paymentMethodLabels[order.payment_method] || order.payment_method}
                                            </td>
                                            <td>
                                                <Button
                                                    variant="primary"
                                                    size="sm"
                                                    className="!p-2"
                                                    onClick={() => handlePrintInvoice(order.id)}
                                                    isLoading={printingOrderId === order.id}
                                                >
                                                    <LiaPrintSolid className="w-4 h-4" />
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </ModalWrapper>
        </>
    );
};

export default OrderHistoryModal;
