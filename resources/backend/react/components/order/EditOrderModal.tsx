import React, { useState, useEffect } from 'react';
import axios from 'axios';
import { useAppContext } from '../../Context';
import PopupWrapper from '../popup/PopupWrapper';
import { currencyFormatter } from '../../../../frontend/utils/numberFormatter';

interface OrderItem {
    id: number;
    product_name: string;
    variant?: string;
    product_image?: string;
    quantity: number;
    unit_price: number;
    total_price: number;
    product_variation_id?: number;
}

interface OrderDetails {
    id: number;
    order_code: number;
    orderPrefix: string;
    customerName: string;
    customerEmail?: string;
    customerPhone?: string;
    shipping_address?: string;
    billing_address?: string;
    paymentStatus: string;
    paymentType?: string;
    subtotalAmount: number;
    taxAmount: number;
    shippingChargeAmount: number;
    discountAmount: number;
    totalAmount: number;
    courier_name?: string;
    tracking_number?: string;
}

const EditOrderModal: React.FC = () => {
    const { popup, closePopup } = useAppContext();
    const [loading, setLoading] = useState(false);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [success, setSuccess] = useState(false);
    const [orderDetails, setOrderDetails] = useState<OrderDetails | null>(null);
    const [orderItems, setOrderItems] = useState<OrderItem[]>([]);

    // Form state
    const [formData, setFormData] = useState({
        customer_name: '',
        customer_phone: '',
        customer_email: '',
        shipping_address: '',
        billing_address: '',
        payment_method: '',
        shipping_charge: 0,
        discount_amount: 0,
    });

    // Product search state
    const [searchQuery, setSearchQuery] = useState('');
    const [searchResults, setSearchResults] = useState<any[]>([]);
    const [searchingProducts, setSearchingProducts] = useState(false);
    const [showProductSearch, setShowProductSearch] = useState(false);

    const orderId = popup.props?.orderId;

    useEffect(() => {
        if (popup.name === 'edit-order' && orderId) {
            fetchOrderDetails();
        }
    }, [popup.name, orderId]);

    const fetchOrderDetails = async () => {
        if (!orderId) return;
        setLoading(true);
        setError(null);
        try {
            const response = await axios.get(`/admin/api/orders/${orderId}`);
            const data = response.data;

            if (data.success) {
                setOrderDetails(data.result.order);
                setOrderItems(data.result.items || []);

                // Initialize form data
                setFormData({
                    customer_name: data.result.order.customerName || '',
                    customer_phone: data.result.order.customerPhone || '',
                    customer_email: data.result.order.customerEmail || '',
                    shipping_address: data.result.order.shipping_address || '',
                    billing_address: data.result.order.billing_address || '',
                    payment_method: data.result.order.paymentType || '',
                    shipping_charge: data.result.order.shippingChargeAmount || 0,
                    discount_amount: data.result.order.discountAmount || 0,
                });
            } else {
                setError(data.message || 'Failed to fetch order details');
            }
        } catch (err: any) {
            setError(err.response?.data?.message || 'Error fetching order details');
        } finally {
            setLoading(false);
        }
    };

    const searchProducts = async (query: string) => {
        if (query.length < 2) {
            setSearchResults([]);
            return;
        }
        setSearchingProducts(true);
        try {
            const response = await axios.get(`/admin/api/products/search?q=${encodeURIComponent(query)}`);
            setSearchResults(response.data.products || []);
        } catch (err) {
            console.error('Error searching products:', err);
            setSearchResults([]);
        } finally {
            setSearchingProducts(false);
        }
    };

    const handleAddProduct = (product: any, variation: any) => {
        const newItem: OrderItem = {
            id: Date.now(), // Temporary negative ID for new items
            product_name: product.name,
            variant: variation.name,
            product_image: product.image,
            quantity: 1,
            unit_price: variation.price,
            total_price: variation.price,
            product_variation_id: variation.id,
            is_new: true,
        };
        setOrderItems([...orderItems, newItem]);
        setSearchQuery('');
        setSearchResults([]);
        setShowProductSearch(false);
    };

    const handleRemoveProduct = (itemId: number) => {
        setOrderItems(orderItems.filter(item => item.id !== itemId));
    };

    const handleQuantityChange = (itemId: number, quantity: number) => {
        if (quantity < 1) return;
        setOrderItems(orderItems.map(item => {
            if (item.id === itemId) {
                return {
                    ...item,
                    quantity,
                    total_price: quantity * item.unit_price,
                };
            }
            return item;
        }));
    };

    const handleSubmit = async () => {
        if (!orderId || !orderDetails) return;
        setSaving(true);
        setError(null);

        try {
            const items = orderItems.map(item => ({
                id: item.id > 0 ? item.id : undefined,
                product_variation_id: item.product_variation_id,
                qty: item.quantity,
                unit_price: item.unit_price,
                total_price: item.total_price,
                is_new: item.is_new || false,
            }));

            const response = await axios.put(`/admin/api/orders/${orderId}`, {
                ...formData,
                items,
            });

            if (response.data.status === 200) {
                setSuccess(true);
                setTimeout(() => {
                    closePopup();
                    window.location.reload();
                }, 1500);
            } else {
                setError(response.data.message || 'Failed to update order');
            }
        } catch (err: any) {
            setError(err.response?.data?.message || 'Error updating order');
        } finally {
            setSaving(false);
        }
    };

    if (popup.name !== 'edit-order') return null;

    const calculateTotal = () => {
        const subtotal = orderItems.reduce((sum, item) => sum + item.total_price, 0);
        const tax = subtotal * 0.15; // Simplified tax calculation
        return subtotal + tax + (formData.shipping_charge || 0) - (formData.discount_amount || 0);
    };

    return (
        <PopupWrapper isOpen={popup.name === 'edit-order'} size="lg" closeOnBackdropClick={false}>
            <div className="p-4 sm:p-6 text-foreground">
                <div className="mb-6 border-b border-border pb-4">
                    <h2 className="text-xl font-semibold text-foreground">
                        Edit Order #{orderDetails?.orderPrefix}{orderDetails?.order_code}
                    </h2>
                    <p className="mt-1 text-sm text-muted">
                        Update customer, address, payment, and product information in one form.
                    </p>
                </div>

                {loading ? (
                    <div className="flex justify-center items-center py-12">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
                    </div>
                ) : error && !success ? (
                    <div className="bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300 p-4 rounded-md mb-4">
                        {error}
                    </div>
                ) : success ? (
                    <div className="bg-primary/10 text-primary p-4 rounded-md mb-4">
                        Order updated successfully!
                    </div>
                ) : (
                    <>
                        <div className="space-y-5">
                            <section className="rounded-lg border border-border bg-background-primary-light/30 p-4">
                                <h3 className="mb-4 font-semibold text-foreground">Customer Information</h3>
                                <div className="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="block text-sm font-medium text-foreground mb-1">
                                        Customer Name
                                    </label>
                                    <input
                                        type="text"
                                        value={formData.customer_name}
                                        onChange={(e) => setFormData({ ...formData, customer_name: e.target.value })}
                                        className="theme-input w-full"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-foreground mb-1">
                                        Phone
                                    </label>
                                    <input
                                        type="text"
                                        value={formData.customer_phone}
                                        onChange={(e) => setFormData({ ...formData, customer_phone: e.target.value })}
                                        className="theme-input w-full"
                                    />
                                </div>
                                <div className="sm:col-span-2">
                                    <label className="block text-sm font-medium text-foreground mb-1">
                                        Email
                                    </label>
                                    <input
                                        type="email"
                                        value={formData.customer_email}
                                        onChange={(e) => setFormData({ ...formData, customer_email: e.target.value })}
                                        className="theme-input w-full"
                                    />
                                </div>
                                </div>
                            </section>

                            <section className="rounded-lg border border-border bg-background-primary-light/30 p-4">
                                <h3 className="mb-4 font-semibold text-foreground">Address Information</h3>
                                <div className="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label className="block text-sm font-medium text-foreground mb-1">
                                        Shipping Address
                                    </label>
                                    <textarea
                                        value={formData.shipping_address}
                                        onChange={(e) => setFormData({ ...formData, shipping_address: e.target.value })}
                                        rows={3}
                                        className="theme-input w-full"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-foreground mb-1">
                                        Billing Address
                                    </label>
                                    <textarea
                                        value={formData.billing_address}
                                        onChange={(e) => setFormData({ ...formData, billing_address: e.target.value })}
                                        rows={3}
                                        className="theme-input w-full"
                                    />
                                </div>
                                </div>
                            </section>

                            <section className="rounded-lg border border-border bg-background-primary-light/30 p-4">
                                <h3 className="mb-4 font-semibold text-foreground">Payment & Totals</h3>
                                <div className="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label className="block text-sm font-medium text-foreground mb-1">
                                        Payment Method
                                    </label>
                                    <select
                                        value={formData.payment_method}
                                        onChange={(e) => setFormData({ ...formData, payment_method: e.target.value })}
                                        className="theme-input theme-input-select w-full"
                                    >
                                        <option value="">Select Payment Method</option>
                                        <option value="cash_on_delivery">Cash on Delivery</option>
                                        <option value="online">Online Payment</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-foreground mb-1">
                                        Shipping Charge
                                    </label>
                                    <input
                                        type="number"
                                        value={formData.shipping_charge}
                                        onChange={(e) => setFormData({ ...formData, shipping_charge: parseFloat(e.target.value) || 0 })}
                                        className="theme-input w-full"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-medium text-foreground mb-1">
                                        Discount Amount
                                    </label>
                                    <input
                                        type="number"
                                        value={formData.discount_amount}
                                        onChange={(e) => setFormData({ ...formData, discount_amount: parseFloat(e.target.value) || 0 })}
                                        className="theme-input w-full"
                                    />
                                </div>
                                </div>
                                <div className="mt-4 rounded-md border border-border bg-background p-4">
                                    <div className="flex justify-between text-sm text-muted">
                                        <span>Subtotal:</span>
                                        <span>{currencyFormatter(orderItems.reduce((sum, item) => sum + item.total_price, 0))}</span>
                                    </div>
                                    <div className="flex justify-between text-sm text-muted mt-2">
                                        <span>Shipping:</span>
                                        <span>{currencyFormatter(formData.shipping_charge || 0)}</span>
                                    </div>
                                    <div className="flex justify-between text-sm text-muted mt-2">
                                        <span>Discount:</span>
                                        <span>-{currencyFormatter(formData.discount_amount || 0)}</span>
                                    </div>
                                    <div className="flex justify-between font-semibold text-foreground mt-2 pt-2 border-t border-border">
                                        <span>Estimated Total:</span>
                                        <span>{currencyFormatter(calculateTotal())}</span>
                                    </div>
                                </div>
                            </section>

                            <section className="rounded-lg border border-border bg-background-primary-light/30 p-4">
                                <div className="space-y-4">
                                <div className="flex justify-between items-center">
                                    <h3 className="font-semibold text-foreground">Order Items</h3>
                                    <button
                                        type="button"
                                        onClick={() => setShowProductSearch(!showProductSearch)}
                                        className="px-3 py-1 bg-primary text-white text-sm rounded-md hover:bg-primary/90"
                                    >
                                        Add Product
                                    </button>
                                </div>

                                {showProductSearch && (
                                    <div className="bg-background border border-border p-4 rounded-md mb-4">
                                        <div className="relative">
                                            <input
                                                type="text"
                                                placeholder="Search products..."
                                                value={searchQuery}
                                                onChange={(e) => {
                                                    setSearchQuery(e.target.value);
                                                    searchProducts(e.target.value);
                                                }}
                                                className="theme-input w-full"
                                            />
                                            {searchingProducts && (
                                                <div className="absolute right-3 top-3">
                                                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                                                </div>
                                            )}
                                            {searchResults.length > 0 && (
                                                <div className="absolute z-10 w-full mt-1 bg-background text-foreground border border-border rounded-md shadow-lg max-h-48 overflow-y-auto">
                                                    {searchResults.map((product) => (
                                                        <div key={product.id} className="p-2 hover:bg-muted/20">
                                                            <div className="font-medium">{product.name}</div>
                                                            {product.variations?.map((variation: any) => (
                                                                <button
                                                                    type="button"
                                                                    key={variation.id}
                                                                    onClick={() => handleAddProduct(product, variation)}
                                                                    className="w-full text-left px-2 py-1 text-sm text-muted hover:bg-muted/20 hover:text-foreground"
                                                                >
                                                                    {variation.name} - {currencyFormatter(variation.price)} (Stock: {variation.stock})
                                                                </button>
                                                            ))}
                                                        </div>
                                                    ))}
                                                </div>
                                            )}
                                        </div>
                                    </div>
                                )}

                                <div className="space-y-2">
                                    {orderItems.map((item) => (
                                        <div key={item.id} className="flex flex-wrap items-center gap-3 p-3 border border-border bg-background rounded-md sm:flex-nowrap">
                                            {item.product_image && (
                                                <img
                                                    src={item.product_image}
                                                    alt={item.product_name}
                                                    className="w-12 h-12 object-cover rounded"
                                                />
                                            )}
                                            <div className="min-w-[180px] flex-1">
                                                <div className="font-medium text-foreground">{item.product_name}</div>
                                                {item.variant && (
                                                    <div className="text-sm text-muted">{item.variant}</div>
                                                )}
                                                <div className="text-sm text-muted">
                                                    {currencyFormatter(item.unit_price)} × {item.quantity} = {currencyFormatter(item.total_price)}
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    onClick={() => handleQuantityChange(item.id, item.quantity - 1)}
                                                    className="w-8 h-8 flex items-center justify-center border border-border rounded text-foreground hover:bg-muted/20"
                                                >
                                                    -
                                                </button>
                                                <input
                                                    type="number"
                                                    value={item.quantity}
                                                    onChange={(e) => handleQuantityChange(item.id, parseInt(e.target.value) || 1)}
                                                    className="theme-input w-16 px-2 py-1 text-center"
                                                    min="1"
                                                />
                                                <button
                                                    type="button"
                                                    onClick={() => handleQuantityChange(item.id, item.quantity + 1)}
                                                    className="w-8 h-8 flex items-center justify-center border border-border rounded text-foreground hover:bg-muted/20"
                                                >
                                                    +
                                                </button>
                                            </div>
                                            <button
                                                type="button"
                                                onClick={() => handleRemoveProduct(item.id)}
                                                className="text-red-500 hover:text-red-700 p-1"
                                                title="Remove item"
                                            >
                                                <i className="fa-solid fa-trash"></i>
                                            </button>
                                        </div>
                                    ))}
                                    {orderItems.length === 0 && (
                                        <div className="text-center py-8 text-muted">
                                            No items in this order
                                        </div>
                                    )}
                                </div>
                                </div>
                            </section>
                        </div>

                        {/* Actions */}
                        <div className="sticky bottom-0 -mx-4 sm:-mx-6 -mb-4 sm:-mb-6 flex justify-end gap-3 mt-6 px-4 sm:px-6 py-4 border-t border-border bg-background">
                            <button
                                type="button"
                                onClick={closePopup}
                                className="px-4 py-2 border border-border rounded-md text-foreground hover:bg-muted/20"
                                disabled={saving}
                            >
                                Cancel
                            </button>
                            <button
                                type="button"
                                onClick={handleSubmit}
                                disabled={saving}
                                className="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 disabled:opacity-50"
                            >
                                {saving ? 'Saving...' : 'Save Changes'}
                            </button>
                        </div>
                    </>
                )}
            </div>
        </PopupWrapper>
    );
};

export default EditOrderModal;
