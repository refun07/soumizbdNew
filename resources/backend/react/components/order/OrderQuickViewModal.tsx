import React, { useEffect, useState } from 'react';
import axios from 'axios';
import { useAppContext } from '../../Context';
import PopupWrapper from '../popup/PopupWrapper';
import { currencyFormatter } from '../../../../frontend/utils/numberFormatter';
import { route } from '../../utils/routeHelpers';

interface OrderItem {
    id: number;
    product_name: string;
    variant: string | null;
    product_image: string | null;
    quantity: number;
    unit_price: number;
    total_price: number;
}

interface OrderDetail {
    id: number;
    order_code: number;
    orderPrefix: string;
    customerName: string | null;
    customerEmail: string | null;
    customerPhone: string | null;
    createdDate: string;
    createdTime: string;
    deliveryStatus: string;
    deliveryStatusToShow: string;
    paymentStatus: string;
    paymentType: string | null;
    shipping_address: string | null;
    shipping_city: string | null;
    shipping_country: string | null;
    subtotalAmount: number;
    taxAmount: number;
    shippingChargeAmount: number;
    discountAmount: number;
    couponDiscountAmount: number;
    totalAmount: number;
    isPosOrder: boolean;
    isExchangeOrder: boolean;
    tracking_number: string | null;
    courier_partner: string | null;
    courier_delivery_status: string | null;
    latestDeliveryUpdate?: {
        status: string;
        note: string;
        actorName: string;
        actorRole?: string | null;
        createdDate: string;
        createdTime: string;
    } | null;
}

const statusColorMap: Record<string, string> = {
    order_placed: '!bg-green-500',
    processing: '!bg-orange-500',
    confirmed: '!bg-yellow-400',
    cancelled: '!bg-red-500',
    shipped: '!bg-orange-500',
    delivered: '!bg-blue-500',
};

const OrderQuickViewModal: React.FC = () => {
    const { popup, closePopup } = useAppContext();
    const [order, setOrder] = useState<OrderDetail | null>(null);
    const [items, setItems] = useState<OrderItem[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const orderId = popup.props?.orderId;

    useEffect(() => {
        if (popup.name === 'quick-view-order' && orderId) {
            fetchOrderDetails();
        }
    }, [popup.name, orderId]);

    const fetchOrderDetails = async () => {
        if (!orderId) return;
        setLoading(true);
        setError(null);
        try {
            const response = await axios.get(`/admin/api/orders/${orderId}`);
            if (response.data.success) {
                setOrder(response.data.result.order);
                setItems(response.data.result.items || []);
            } else {
                setError('Failed to load order details');
            }
        } catch (err) {
            console.error('Error fetching order details:', err);
            setError('Something went wrong');
        } finally {
            setLoading(false);
        }
    };

    if (popup.name !== 'quick-view-order') return null;

    return (
        <PopupWrapper isOpen={popup.name === 'quick-view-order'} size="lg">
            <div className="p-5">
                {loading ? (
                    <div className="flex justify-center items-center py-16">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    </div>
                ) : error ? (
                    <div className="py-8 text-center">
                        <i className="fa-solid fa-triangle-exclamation text-red-500 text-3xl mb-3 block"></i>
                        <div className="text-muted">{error}</div>
                    </div>
                ) : order ? (
                    <>
                        <div className="flex items-center justify-between mb-5">
                            <div className="flex items-center gap-3">
                                <h2 className="text-lg font-semibold text-foreground">
                                    {order.orderPrefix}{order.order_code}
                                </h2>
                                <span className={`px-2.5 py-0.5 rounded text-xs font-medium text-white ${statusColorMap[order.deliveryStatus] || '!bg-gray-500'}`}>
                                    {order.deliveryStatusToShow}
                                </span>
                                {order.isExchangeOrder && (
                                    <span className="px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                        Exchange
                                    </span>
                                )}
                            </div>
                            <div className="text-sm text-muted">
                                {order.createdDate} {order.createdTime}
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4 mb-5">
                            <div className="card rounded-lg border border-border p-3">
                                <div className="text-xs text-muted uppercase mb-2">Customer</div>
                                <div className="font-medium">{order.customerName || 'N/A'}</div>
                                {order.customerPhone && (
                                    <div className="text-sm text-muted mt-0.5">{order.customerPhone}</div>
                                )}
                                {order.customerEmail && (
                                    <div className="text-sm text-muted mt-0.5">{order.customerEmail}</div>
                                )}
                            </div>
                            <div className="card rounded-lg border border-border p-3">
                                <div className="text-xs text-muted uppercase mb-2">Shipping Address</div>
                                {order.shipping_address ? (
                                    <div className="text-sm whitespace-pre-wrap">{order.shipping_address}</div>
                                ) : (
                                    <div className="text-sm text-muted">No address</div>
                                )}
                                {order.shipping_city && (
                                    <div className="text-sm text-muted mt-0.5">{order.shipping_city}{order.shipping_country ? `, ${order.shipping_country}` : ''}</div>
                                )}
                            </div>
                        </div>

                        <div className="card rounded-lg border border-border p-3 mb-5">
                            <div className="text-xs text-muted uppercase mb-2">Order Items ({items.length})</div>
                            <div className="overflow-x-auto -mx-1">
                                <table className="w-full text-sm">
                                    <thead>
                                        <tr className="border-b border-border">
                                            <th className="text-left py-2 px-1 text-xs font-medium text-muted uppercase">Product</th>
                                            <th className="text-center py-2 px-1 text-xs font-medium text-muted uppercase w-12">Qty</th>
                                            <th className="text-right py-2 px-1 text-xs font-medium text-muted uppercase w-20">Price</th>
                                            <th className="text-right py-2 px-1 text-xs font-medium text-muted uppercase w-20">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border">
                                        {items.map((item) => (
                                            <tr key={item.id}>
                                                <td className="py-2 px-1">
                                                    <div className="flex items-center gap-2">
                                                        {item.product_image && (
                                                            <img
                                                                src={item.product_image}
                                                                alt={item.product_name}
                                                                className="w-8 h-8 object-cover rounded border border-border"
                                                            />
                                                        )}
                                                        <div className="min-w-0">
                                                            <div className="font-medium truncate">{item.product_name}</div>
                                                            {item.variant && (
                                                                <div className="text-xs text-muted truncate">{item.variant}</div>
                                                            )}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="py-2 px-1 text-center">{item.quantity}</td>
                                                <td className="py-2 px-1 text-right">{currencyFormatter(item.unit_price)}</td>
                                                <td className="py-2 px-1 text-right font-medium">{currencyFormatter(item.total_price)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4 mb-5">
                            <div className="card rounded-lg border border-border p-3">
                                <div className="text-xs text-muted uppercase mb-2">Payment</div>
                                <div className="font-medium capitalize">{order.paymentStatus}</div>
                                {order.paymentType && (
                                    <div className="text-sm text-muted mt-0.5">{order.paymentType}</div>
                                )}
                            </div>
                            {(order.tracking_number || order.courier_partner) && (
                                <div className="card rounded-lg border border-border p-3">
                                    <div className="text-xs text-muted uppercase mb-2">Delivery</div>
                                    {order.courier_partner && (
                                        <div className="text-sm">
                                            <span className="text-muted">Partner:</span> <span className="font-medium capitalize">{order.courier_partner}</span>
                                        </div>
                                    )}
                                    {order.tracking_number && (
                                        <div className="text-sm mt-0.5">
                                            <span className="text-muted">Tracking:</span> <span className="font-medium">{order.tracking_number}</span>
                                        </div>
                                    )}
                                    {order.courier_delivery_status && (
                                        <div className="text-sm mt-0.5">
                                            <span className="text-muted">Status:</span> <span className="font-medium capitalize">{order.courier_delivery_status}</span>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>

                        {order.latestDeliveryUpdate && (
                            <div className="card rounded-lg border border-border p-3 mb-5">
                                <div className="text-xs text-muted uppercase mb-2">Latest Update</div>
                                <div className="font-medium">{order.latestDeliveryUpdate.status}</div>
                                {order.latestDeliveryUpdate.note && (
                                    <div className="text-sm text-muted mt-0.5">{order.latestDeliveryUpdate.note}</div>
                                )}
                                <div className="text-xs text-muted mt-1">
                                    by {order.latestDeliveryUpdate.actorName}
                                    {order.latestDeliveryUpdate.actorRole ? ` (${order.latestDeliveryUpdate.actorRole})` : ''} &middot; {order.latestDeliveryUpdate.createdDate} {order.latestDeliveryUpdate.createdTime}
                                </div>
                            </div>
                        )}

                        <div className="card rounded-lg border border-border p-3 mb-5">
                            <div className="space-y-1.5 text-sm">
                                <div className="flex justify-between">
                                    <span className="text-muted">Subtotal</span>
                                    <span>{currencyFormatter(order.subtotalAmount)}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted">Tax</span>
                                    <span>{currencyFormatter(order.taxAmount)}</span>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted">Shipping</span>
                                    <span>{currencyFormatter(order.shippingChargeAmount)}</span>
                                </div>
                                {order.couponDiscountAmount > 0 && (
                                    <div className="flex justify-between text-green-500">
                                        <span>Coupon Discount</span>
                                        <span>-{currencyFormatter(order.couponDiscountAmount)}</span>
                                    </div>
                                )}
                                {order.discountAmount > 0 && order.couponDiscountAmount === 0 && (
                                    <div className="flex justify-between text-green-500">
                                        <span>Discount</span>
                                        <span>-{currencyFormatter(order.discountAmount)}</span>
                                    </div>
                                )}
                                <div className="border-t border-border pt-1.5 mt-1.5">
                                    <div className="flex justify-between font-bold text-base">
                                        <span>Total</span>
                                        <span className="text-sky-500">{currencyFormatter(order.totalAmount)}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="flex items-center justify-end gap-2 pt-3 border-t border-border">
                            <a
                                href={route('admin.orders.downloadInvoice', { id: order.id })}
                                target="_blank"
                                className="btn btn-sm btn-outline-primary"
                            >
                                <i className="fa-solid fa-file-arrow-down mr-1"></i>
                                Invoice
                            </a>
                            <a
                                href={`/admin/orders/${order.order_code}`}
                                className="btn btn-sm btn-primary"
                            >
                                <i className="fa-solid fa-arrow-right mr-1"></i>
                                View Full Details
                            </a>
                        </div>
                    </>
                ) : null}
            </div>
        </PopupWrapper>
    );
};

export default OrderQuickViewModal;
