import axios from 'axios';
import React, { useEffect, useState } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import Button from '../components/inputs/Button';
import CourierDetailsModal from '../components/order/delivery/CourierDetailsModal';
import { route } from '../utils/routeHelpers';

interface OrderItem {
    id: number;
    product_name: string;
    variant: string | null;
    product_image: string | null;
    quantity: number;
    unit_price: number;
    total_price: number;
}

interface ExchangeItem {
    id: number;
    originalProductName: string;
    originalVariant: string | null;
    exchangeProductName: string;
    exchangeVariant: string | null;
    quantity: number;
    originalQuantity: number;
    originalUnitPrice: number;
    exchangeUnitPrice: number;
    priceDifference: number;
    reason: string | null;
    status: string;
}

interface ExchangeInfo {
    type: 'exchange_order' | 'original_order';
    exchangeId: number;
    status: string;
    originalOrderCode?: string | null;
    originalOrderRawCode?: string | null;
    originalOrderId?: number | null;
    exchangeOrderCode?: string | null;
    exchangeOrderRawCode?: string | null;
    exchangeOrderId?: number | null;
    priceDifference: number;
    refundAmount: number | null;
    shippingCharge: number;
    totalOriginalPrice: number;
    totalExchangePrice: number;
    items: ExchangeItem[];
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
    originalOrderId: number | null;
    tracking_number: string | null;
    courier_partner: string | null;
    courier_delivery_status: string | null;
    courier_response: Record<string, any> | null;
    ipAddress: string | null;
    exchangeInfo: ExchangeInfo | null;
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

const OrderDetailPage: React.FC = () => {
    const { orderId } = useParams<{ orderId: string }>();
    const navigate = useNavigate();
    const [order, setOrder] = useState<OrderDetail | null>(null);
    const [items, setItems] = useState<OrderItem[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);
    const [showBlockModal, setShowBlockModal] = useState(false);
    const [blockReason, setBlockReason] = useState('');
    const [blockDuration, setBlockDuration] = useState('permanent');
    const [blocking, setBlocking] = useState(false);
    const [isTrackModalOpen, setIsTrackModalOpen] = useState(false);

    useEffect(() => {
        const fetchOrderDetails = async () => {
            try {
                setLoading(true);
                const response = await axios.get(`/admin/api/orders/${orderId}`);
                if (response.data.success) {
                    setOrder(response.data.result.order);
                    setItems(response.data.result.items);
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

        if (orderId) {
            fetchOrderDetails();
        }
    }, [orderId]);

    const handleBack = () => {
        navigate(-1);
    };

    const handleBlockIP = async () => {
        if (!order?.ipAddress) return;
        
        setBlocking(true);
        try {
            const response = await axios.post('/admin/api/blocked-ips', {
                ip_address: order.ipAddress,
                reason: blockReason || `Blocked from order #${order.orderPrefix}${order.order_code}`,
                blocked_until: blockDuration === 'permanent' ? null : 
                    blockDuration === '1_hour' ? new Date(Date.now() + 60 * 60 * 1000).toISOString() :
                    blockDuration === '24_hours' ? new Date(Date.now() + 24 * 60 * 60 * 1000).toISOString() :
                    blockDuration === '7_days' ? new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString() :
                    blockDuration === '30_days' ? new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString() :
                    null
            });
            
            if (response.data.success) {
                alert('IP address blocked successfully');
                setShowBlockModal(false);
                setBlockReason('');
                setBlockDuration('permanent');
            } else {
                alert(response.data.message || 'Failed to block IP');
            }
        } catch (err: any) {
            console.error('Error blocking IP:', err);
            alert(err.response?.data?.message || 'Failed to block IP address');
        } finally {
            setBlocking(false);
        }
    };

    if (loading) {
        return (
            <div className="p-4">
                <div className="flex items-center justify-center h-64">
                    <div className="text-muted">Loading order details...</div>
                </div>
            </div>
        );
    }

    if (error || !order) {
        return (
            <div className="p-4">
                <div className="flex items-center justify-between mb-4">
                    <h1 className="text-xl font-bold text-foreground">Order Details</h1>
                    <Button variant="secondary" onClick={handleBack}>
                        <i className="fa-solid fa-arrow-left mr-2"></i>
                        Back to Orders
                    </Button>
                </div>
                <div className="card rounded-lg shadow-sm p-8 text-center">
                    <div className="text-red-500 mb-2">
                        <i className="fa-solid fa-triangle-exclamation text-3xl"></i>
                    </div>
                    <div className="text-muted">{error || 'Order not found'}</div>
                </div>
            </div>
        );
    }

    return (
        <div className="p-4">
            {/* Header */}
            <div className="flex items-center justify-between mb-4">
                <div className="flex items-center gap-3">
                    <Button variant="secondary" onClick={handleBack} className="!px-3 !py-2">
                        <i className="fa-solid fa-arrow-left"></i>
                    </Button>
                    <h1 className="text-xl font-bold text-foreground">
                        Order Details
                        {order.isExchangeOrder && (
                            <span className="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300">
                                Exchange Order
                            </span>
                        )}
                        {!order.isExchangeOrder && order.exchangeInfo && (
                            <span className="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                Has Exchange
                            </span>
                        )}
                    </h1>
                </div>
                <div className="flex items-center gap-2">
                    <a
                        href={route('admin.orders.downloadInvoice', { id: order.id })}
                        target="_blank"
                        className="btn btn-sm btn-outline-primary"
                    >
                        <i className="fa-solid fa-file-arrow-down mr-1"></i>
                        Download Invoice
                    </a>
                    {order.isPosOrder && (
                        <a
                            href={route('admin.orders.downloadChallan', { id: order.id })}
                            target="_blank"
                            className="btn btn-sm btn-outline-primary"
                        >
                            <i className="fa-light fa-file-contract mr-1"></i>
                            Challan
                        </a>
                    )}
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {/* Order Info Card */}
                <div className="lg:col-span-2 space-y-4">
                    {/* Order Summary */}
                    <div className="card rounded-lg shadow-sm border border-border p-4">
                        <div className="flex items-center justify-between mb-4">
                            <h2 className="font-semibold text-lg text-foreground">Order Summary</h2>
                            <span className={`px-3 py-1 rounded text-sm font-medium text-white ${statusColorMap[order.deliveryStatus] || '!bg-gray-500'}`}>
                                {order.deliveryStatusToShow}
                            </span>
                        </div>
                        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <div className="text-xs text-muted uppercase mb-1">Invoice No</div>
                                <div className="font-medium">
                                    {order.orderPrefix} {order.order_code}
                                </div>
                            </div>
                            <div>
                                <div className="text-xs text-muted uppercase mb-1">Date</div>
                                <div className="font-medium">{order.createdDate}</div>
                                <div className="text-sm text-muted">{order.createdTime}</div>
                            </div>
                            <div>
                                <div className="text-xs text-muted uppercase mb-1">Payment</div>
                                <div className="font-medium capitalize">{order.paymentStatus}</div>
                                {order.paymentType && (
                                    <div className="text-sm text-muted">{order.paymentType}</div>
                                )}
                            </div>
                            <div>
                                <div className="text-xs text-muted uppercase mb-1">Total Amount</div>
                                <div className="font-bold text-sky-500 text-lg">
                                    {currencyFormatter(order.totalAmount)}
                                </div>
                            </div>
                        </div>
                        {(order.tracking_number || order.courier_partner) && (
                            <div className="mt-4 pt-4 border-t border-border">
                                <div className="text-xs text-muted uppercase mb-2">Delivery Info</div>
                                <div className="flex items-center gap-4 flex-wrap">
                                    {order.courier_partner && (
                                        <div>
                                            <span className="text-sm text-muted">Partner:</span>
                                            <span className="font-medium ml-1 capitalize">{order.courier_partner}</span>
                                        </div>
                                    )}
                                    {order.tracking_number && (
                                        <div>
                                            <span className="text-sm text-muted">Tracking:</span>
                                            <span className="font-medium ml-1">{order.tracking_number}</span>
                                        </div>
                                    )}
                                    {order.courier_delivery_status && (
                                        <div>
                                            <span className="text-sm text-muted">Courier Status:</span>
                                            <span className="font-medium ml-1 capitalize">{order.courier_delivery_status}</span>
                                        </div>
                                    )}
                                    {order.courier_partner && ['pathao', 'steadfast', 'redx'].includes(order.courier_partner.toLowerCase()) && order.tracking_number && (
                                        <button
                                            onClick={() => setIsTrackModalOpen(true)}
                                            className="btn btn-sm btn-outline-primary flex items-center gap-1.5"
                                        >
                                            <i className="fa-solid fa-truck-fast"></i>
                                            Track Delivery
                                        </button>
                                    )}
                                </div>
                                {order.courier_response && (
                                    <div className="mt-3 bg-muted/5 rounded-lg p-3 text-sm">
                                        <div className="text-xs text-muted uppercase mb-1">Courier Details</div>
                                        {order.courier_partner?.toLowerCase() === 'pathao' && order.courier_response.data && (
                                            <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
                                                <div>
                                                    <span className="text-muted text-xs">Consignment ID:</span>
                                                    <span className="font-medium ml-1">{order.courier_response.data.consignment_id || 'N/A'}</span>
                                                </div>
                                                <div>
                                                    <span className="text-muted text-xs">Delivery Fee:</span>
                                                    <span className="font-medium ml-1">{order.courier_response.data.delivery_fee ?? 'N/A'}</span>
                                                </div>
                                                <div>
                                                    <span className="text-muted text-xs">Order Status:</span>
                                                    <span className="font-medium ml-1 capitalize">{order.courier_response.data.order_status || 'N/A'}</span>
                                                </div>
                                            </div>
                                        )}
                                        {order.courier_partner?.toLowerCase() === 'steadfast' && order.courier_response.consignment && (
                                            <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
                                                <div>
                                                    <span className="text-muted text-xs">Tracking Code:</span>
                                                    <span className="font-medium ml-1">{order.courier_response.consignment.tracking_code || 'N/A'}</span>
                                                </div>
                                                <div>
                                                    <span className="text-muted text-xs">Status:</span>
                                                    <span className="font-medium ml-1 capitalize">{order.courier_response.consignment.status || 'N/A'}</span>
                                                </div>
                                            </div>
                                        )}
                                        {order.courier_partner?.toLowerCase() === 'redx' && (
                                            <div className="grid grid-cols-2 md:grid-cols-3 gap-2">
                                                <div>
                                                    <span className="text-muted text-xs">Tracking ID:</span>
                                                    <span className="font-medium ml-1">{order.courier_response.tracking_id || 'N/A'}</span>
                                                </div>
                                                <div>
                                                    <span className="text-muted text-xs">Status:</span>
                                                    <span className="font-medium ml-1 capitalize">{order.courier_response.status || 'N/A'}</span>
                                                </div>
                                            </div>
                                        )}
                                        {order.courier_response.message && (
                                            <div className="mt-1 text-xs text-muted">
                                                <span className="italic">{order.courier_response.message}</span>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                        )}
                    </div>

                    {/* Order Items */}
                    <div className="card rounded-lg shadow-sm border border-border p-4">
                        <h2 className="font-semibold text-lg text-foreground mb-4">Order Items</h2>
                        <div className="overflow-x-auto">
                            <table className="w-full">
                                <thead>
                                    <tr className="border-b border-border">
                                        <th className="text-left py-3 px-2 text-xs font-medium text-muted uppercase">Product</th>
                                        <th className="text-center py-3 px-2 text-xs font-medium text-muted uppercase">Qty</th>
                                        <th className="text-right py-3 px-2 text-xs font-medium text-muted uppercase">Unit Price</th>
                                        <th className="text-right py-3 px-2 text-xs font-medium text-muted uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-border">
                                    {items.map((item) => (
                                        <tr key={item.id}>
                                            <td className="py-3 px-2">
                                                <div className="flex items-center gap-3">
                                                    {item.product_image && (
                                                        <img
                                                            src={item.product_image}
                                                            alt={item.product_name}
                                                            className="w-12 h-12 object-cover rounded border border-border"
                                                        />
                                                    )}
                                                    <div>
                                                        <div className="font-medium">{item.product_name}</div>
                                                        {item.variant && (
                                                            <div className="text-sm text-muted">{item.variant}</div>
                                                        )}
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="py-3 px-2 text-center">{item.quantity}</td>
                                            <td className="py-3 px-2 text-right">{currencyFormatter(item.unit_price)}</td>
                                            <td className="py-3 px-2 text-right font-medium">
                                                {currencyFormatter(item.total_price)}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {/* Sidebar */}
                <div className="space-y-4">
                    {/* Customer Info */}
                    <div className="card rounded-lg shadow-sm border border-border p-4">
                        <h2 className="font-semibold text-lg text-foreground mb-4">Customer Info</h2>
                        <div className="space-y-3">
                            <div>
                                <div className="text-xs text-muted uppercase mb-1">Name</div>
                                <div className="font-medium">{order.customerName || 'N/A'}</div>
                            </div>
                            {order.customerEmail && (
                                <div>
                                    <div className="text-xs text-muted uppercase mb-1">Email</div>
                                    <div className="font-medium">{order.customerEmail}</div>
                                </div>
                            )}
                            {order.customerPhone && (
                                <div>
                                    <div className="text-xs text-muted uppercase mb-1">Phone</div>
                                    <div className="font-medium">{order.customerPhone}</div>
                                </div>
                            )}
                            {order.ipAddress && (
                                <div className="pt-2 border-t border-border">
                                    <div className="text-xs text-muted uppercase mb-1">IP Address</div>
                                    <div className="flex items-center justify-between gap-2">
                                        <div className="font-medium text-sm">{order.ipAddress}</div>
                                        <button
                                            onClick={() => {
                                                setBlockReason(`Blocked from order #${order.orderPrefix}${order.order_code}`);
                                                setShowBlockModal(true);
                                            }}
                                            className="btn btn-xs btn-danger flex items-center gap-1"
                                            title="Block this IP address"
                                        >
                                            <i className="fa-solid fa-ban"></i>
                                            <span className="hidden sm:inline">Block</span>
                                        </button>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Shipping Address */}
                    <div className="card rounded-lg shadow-sm border border-border p-4">
                        <h2 className="font-semibold text-lg text-foreground mb-4">Shipping Address</h2>
                        <div className="space-y-2 text-sm">
                            {order.shipping_address && (
                                <div className="whitespace-pre-wrap">{order.shipping_address}</div>
                            )}
                            {order.shipping_city && (
                                <div>
                                    <span className="text-muted">City:</span> {order.shipping_city}
                                </div>
                            )}
                            {order.shipping_country && (
                                <div>
                                    <span className="text-muted">Country:</span> {order.shipping_country}
                                </div>
                            )}
                            {!order.shipping_address && !order.shipping_city && !order.shipping_country && (
                                <div className="text-muted">No address available</div>
                            )}
                        </div>
                    </div>

                    {/* Price Summary */}
                    <div className="card rounded-lg shadow-sm border border-border p-4">
                        <h2 className="font-semibold text-lg text-foreground mb-4">Price Summary</h2>
                        <div className="space-y-2">
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
                            <div className="border-t border-border pt-2 mt-2">
                                <div className="flex justify-between font-bold text-lg">
                                    <span>Total</span>
                                    <span className="text-sky-500">{currencyFormatter(order.totalAmount)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {order.latestDeliveryUpdate && (
                        <div className="card rounded-lg shadow-sm border border-border p-4">
                            <h2 className="font-semibold text-lg text-foreground mb-4">
                                Last Delivery Update
                            </h2>
                            <div className="space-y-2">
                                <div className="font-medium">
                                    {order.latestDeliveryUpdate.status}
                                </div>
                                <div className="text-sm text-muted">
                                    {order.latestDeliveryUpdate.note}
                                </div>
                                <div className="text-sm">
                                    {order.latestDeliveryUpdate.actorName}
                                    {order.latestDeliveryUpdate.actorRole
                                        ? ` (${order.latestDeliveryUpdate.actorRole})`
                                        : ''}
                                </div>
                                <div className="text-xs text-muted">
                                    {order.latestDeliveryUpdate.createdDate}{' '}
                                    {order.latestDeliveryUpdate.createdTime}
                                </div>
                            </div>
                        </div>
                    )}

                    {order.exchangeInfo && (
                        <div className="card rounded-lg shadow-sm border border-border p-4">
                            <div className="flex items-center justify-between mb-4">
                                <h2 className="font-semibold text-lg text-foreground">
                                    Exchange Information
                                </h2>
                                <span className={`px-2 py-0.5 rounded text-xs font-semibold ${
                                    order.exchangeInfo.status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300' :
                                    order.exchangeInfo.status === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' :
                                    order.exchangeInfo.status === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' :
                                    'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'
                                }`}>
                                    {order.exchangeInfo.status.charAt(0).toUpperCase() + order.exchangeInfo.status.slice(1)}
                                </span>
                            </div>
                            <div className="space-y-3">
                                {order.exchangeInfo.type === 'exchange_order' && order.exchangeInfo.originalOrderCode && (
                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">Original Order</div>
                                        <a
                                            href={`/admin/orders/${order.exchangeInfo.originalOrderRawCode}`}
                                            className="font-medium text-primary hover:underline"
                                        >
                                            {order.exchangeInfo.originalOrderCode}
                                            <i className="fa-solid fa-arrow-up-right-from-square ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                )}
                                {order.exchangeInfo.type === 'original_order' && order.exchangeInfo.exchangeOrderCode && (
                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">Exchange Order</div>
                                        <a
                                            href={`/admin/orders/${order.exchangeInfo.exchangeOrderRawCode}`}
                                            className="font-medium text-primary hover:underline"
                                        >
                                            {order.exchangeInfo.exchangeOrderCode}
                                            <i className="fa-solid fa-arrow-up-right-from-square ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                )}
                                {(!order.exchangeInfo.exchangeOrderCode && !order.exchangeInfo.originalOrderCode) && (
                                    <div>
                                        <div className="text-xs text-muted uppercase mb-1">
                                            {order.exchangeInfo.type === 'exchange_order' ? 'Original Order' : 'Exchange Order'}
                                        </div>
                                        <div className="text-sm text-muted">Not yet created</div>
                                    </div>
                                )}
                                <div className="border-t border-border pt-2">
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted">Original Total</span>
                                        <span>{currencyFormatter(order.exchangeInfo.totalOriginalPrice)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted">Exchange Total</span>
                                        <span>{currencyFormatter(order.exchangeInfo.totalExchangePrice)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm">
                                        <span className="text-muted">Shipping</span>
                                        <span>{currencyFormatter(order.exchangeInfo.shippingCharge)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm font-medium">
                                        <span className="text-muted">Price Difference</span>
                                        <span className={order.exchangeInfo.priceDifference > 0 ? 'text-red-500' : order.exchangeInfo.priceDifference < 0 ? 'text-green-500' : ''}>
                                            {order.exchangeInfo.priceDifference > 0 ? '+' : ''}{currencyFormatter(order.exchangeInfo.priceDifference)}
                                        </span>
                                    </div>
                                    {order.exchangeInfo.refundAmount !== null && order.exchangeInfo.refundAmount > 0 && (
                                        <div className="flex justify-between text-sm text-green-500 font-medium">
                                            <span>Refund</span>
                                            <span>-{currencyFormatter(order.exchangeInfo.refundAmount)}</span>
                                        </div>
                                    )}
                                </div>
                                {order.exchangeInfo.items.length > 0 && (
                                    <div className="border-t border-border pt-2">
                                        <div className="text-xs text-muted uppercase mb-2 font-medium">Exchange Items</div>
                                        <div className="space-y-2">
                                            {order.exchangeInfo.items.map((item) => (
                                                <div key={item.id} className="text-sm bg-muted/10 rounded p-2">
                                                    <div className="flex items-center gap-1 mb-1">
                                                        <span className="text-muted text-xs line-through">{item.originalProductName}{item.originalVariant ? ` (${item.originalVariant})` : ''}</span>
                                                        <i className="fa-solid fa-arrow-right text-[10px] text-muted mx-1"></i>
                                                        <span className="font-medium text-xs">{item.exchangeProductName}{item.exchangeVariant ? ` (${item.exchangeVariant})` : ''}</span>
                                                    </div>
                                                    <div className="flex justify-between text-xs text-muted">
                                                        <span>Return Qty: {item.originalQuantity ?? item.quantity} → Exchange Qty: {item.quantity}</span>
                                                        <span className={item.priceDifference > 0 ? 'text-red-500' : item.priceDifference < 0 ? 'text-green-500' : ''}>
                                                            Diff: {item.priceDifference > 0 ? '+' : ''}{currencyFormatter(item.priceDifference)}
                                                        </span>
                                                    </div>
                                                    {item.reason && (
                                                        <div className="text-xs text-muted mt-0.5">
                                                            Reason: {item.reason}
                                                        </div>
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                )}
                                <div className="pt-1">
                                    <a
                                        href={`/admin/exchanges/${order.exchangeInfo.exchangeId}`}
                                        className="text-sm text-primary hover:underline"
                                    >
                                        <i className="fa-solid fa-arrow-right mr-1"></i>
                                        View Full Exchange Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            {/* Courier Track Modal */}
            {order?.courier_partner && ['pathao', 'steadfast', 'redx'].includes(order.courier_partner.toLowerCase()) && order.tracking_number && (
                <CourierDetailsModal
                    isOpen={isTrackModalOpen}
                    onClose={() => setIsTrackModalOpen(false)}
                    partner={order.courier_partner.toLowerCase() as 'pathao' | 'steadfast' | 'redx'}
                    trackingId={Number(order.tracking_number)}
                    onSubmit={() => setIsTrackModalOpen(false)}
                />
            )}

            {/* Block IP Modal */}
            {showBlockModal && order?.ipAddress && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
                        <div className="flex items-center justify-between p-4 border-b">
                            <h3 className="text-lg font-semibold text-foreground">Block IP Address</h3>
                            <button
                                onClick={() => setShowBlockModal(false)}
                                className="text-muted hover:text-foreground transition-colors"
                            >
                                <i className="fa-solid fa-times text-xl"></i>
                            </button>
                        </div>
                        <div className="p-4 space-y-4">
                            <div className="bg-gray-50 p-3 rounded text-sm">
                                <span className="text-muted">IP Address:</span>
                                <span className="font-medium ml-2">{order.ipAddress}</span>
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-foreground mb-1">
                                    Reason (optional)
                                </label>
                                <textarea
                                    value={blockReason}
                                    onChange={(e) => setBlockReason(e.target.value)}
                                    className="form-input w-full"
                                    rows={2}
                                    placeholder="Enter reason for blocking..."
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-medium text-foreground mb-1">
                                    Duration
                                </label>
                                <select
                                    value={blockDuration}
                                    onChange={(e) => setBlockDuration(e.target.value)}
                                    className="form-select w-full"
                                >
                                    <option value="permanent">Permanent</option>
                                    <option value="1_hour">1 Hour</option>
                                    <option value="24_hours">24 Hours</option>
                                    <option value="7_days">7 Days</option>
                                    <option value="30_days">30 Days</option>
                                </select>
                            </div>
                        </div>
                        <div className="flex items-center justify-end gap-2 p-4 border-t bg-gray-50">
                            <button
                                onClick={() => setShowBlockModal(false)}
                                className="btn btn-secondary"
                                disabled={blocking}
                            >
                                Cancel
                            </button>
                            <button
                                onClick={handleBlockIP}
                                className="btn btn-danger"
                                disabled={blocking}
                            >
                                {blocking ? (
                                    <span className="flex items-center gap-2">
                                        <i className="fa-solid fa-spinner fa-spin"></i>
                                        Blocking...
                                    </span>
                                ) : (
                                    'Block IP'
                                )}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
};

export default OrderDetailPage;
