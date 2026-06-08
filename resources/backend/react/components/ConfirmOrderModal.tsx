import axios from 'axios';
import React, { useEffect, useState, useMemo } from 'react';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';

interface CartItem {
    id: number;
    product: { id: number; name: string; thumbnail?: string } | null;
    variation: { id: number; sku?: string } | null;
    shop: { id: number; name: string } | null;
    quantity: number;
    unit_price: number;
    total: number;
}

interface CheckoutSessionData {
    id?: number;
    customer_name?: string;
    customer_email?: string;
    customer_phone?: string;
    customer_note?: string;
    country_id?: number;
    state_id?: number;
    city_id?: number;
    address_line?: string;
    payment_method?: string;
    shipping_charge?: number;
    formatted_address?: string;
}

interface CustomerCart {
    id: string;
    customer: { id: number; name: string; email?: string; phone?: string; type: string };
    total_amount: number;
    total_qty: number;
    items: CartItem[];
    checkout_session?: CheckoutSessionData | null;
}

interface State { id: number; name: string }
interface City { id: number; name: string }

interface ConfirmOrderModalProps {
    cart: CustomerCart;
    onClose: () => void;
    onSuccess: (orderCode: string) => void;
}

const ConfirmOrderModal: React.FC<ConfirmOrderModalProps> = ({ cart, onClose, onSuccess }) => {
    const cs = cart.checkout_session;

    const [name, setName] = useState(cs?.customer_name || cart.customer.name || '');
    const [phone, setPhone] = useState(cs?.customer_phone || cart.customer.phone || '');
    const [email, setEmail] = useState(cs?.customer_email || cart.customer.email || '');
    const [note, setNote] = useState(cs?.customer_note || '');
    const [paymentMethod, setPaymentMethod] = useState(cs?.payment_method || 'cash_on_delivery');
    const [shippingCharge, setShippingCharge] = useState(cs?.shipping_charge ? Number(cs.shipping_charge) : 0);

    const [countryId] = useState<number>(cs?.country_id || 247);
    const [stateId, setStateId] = useState<number | ''>(cs?.state_id || '');
    const [cityId, setCityId] = useState<number | ''>(cs?.city_id || '');
    const [addressLine, setAddressLine] = useState(cs?.address_line || '');

    const [couponCode, setCouponCode] = useState('');
    const [couponDiscount, setCouponDiscount] = useState(0);
    const [couponFreeShipping, setCouponFreeShipping] = useState(false);
    const [appliedCoupon, setAppliedCoupon] = useState<{ id: number; code: string } | null>(null);
    const [isValidatingCoupon, setIsValidatingCoupon] = useState(false);
    const [couponError, setCouponError] = useState('');

    const [states, setStates] = useState<State[]>([]);
    const [cities, setCities] = useState<City[]>([]);

    const [isSubmitting, setIsSubmitting] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        axios.get(`/admin/api/carts/get-states/${countryId}`).then(res => {
            setStates(res.data?.result || []);
            if (cs?.state_id) setStateId(cs.state_id);
        }).catch(() => {});
    }, []);

    useEffect(() => {
        if (!stateId) { setCities([]); setCityId(''); return; }
        axios.get(`/admin/api/carts/get-cities/${stateId}`).then(res => {
            setCities(res.data?.result || []);
            if (cs?.city_id) setCityId(cs.city_id);
        }).catch(() => {});
    }, [stateId]);

    const subtotal = useMemo(() => cart.items.reduce((sum, item) => sum + item.total, 0), [cart.items]);
    const effectiveShipping = couponFreeShipping ? 0 : shippingCharge;
    const grandTotal = useMemo(() => subtotal - couponDiscount + effectiveShipping, [subtotal, couponDiscount, effectiveShipping]);

    const handleApplyCoupon = async () => {
        if (!couponCode.trim()) return;
        setIsValidatingCoupon(true);
        setCouponError('');
        try {
            const res = await axios.post('/admin/api/carts/validate-coupon', {
                coupon_code: couponCode.trim(),
                customer_id: cart.id,
            });
            if (res.data.success) {
                setCouponDiscount(res.data.data.amount);
                setCouponFreeShipping(res.data.data.free_shipping);
                setAppliedCoupon({ id: res.data.data.coupon_id, code: res.data.data.coupon_code });
            }
        } catch (err: any) {
            setCouponDiscount(0);
            setCouponFreeShipping(false);
            setAppliedCoupon(null);
            setCouponError(err?.response?.data?.message || 'Invalid coupon');
        } finally {
            setIsValidatingCoupon(false);
        }
    };

    const handleRemoveCoupon = () => {
        setCouponCode('');
        setCouponDiscount(0);
        setCouponFreeShipping(false);
        setAppliedCoupon(null);
        setCouponError('');
    };

    const handleSubmit = async () => {
        if (!name.trim() || !phone.trim()) {
            setError('Name and phone are required');
            return;
        }
        if (!stateId || !cityId || !addressLine.trim()) {
            setError('District, city, and address are required');
            return;
        }

        setIsSubmitting(true);
        setError('');

        try {
            const res = await axios.post(`/admin/api/carts/${cart.id}/confirm-order`, {
                name: name.trim(),
                phone: phone.trim(),
                email: email.trim() || null,
                note: note.trim() || null,
                payment_method: paymentMethod,
                coupon_code: appliedCoupon?.code || null,
                shipping_charge: effectiveShipping,
                country_id: countryId,
                state_id: stateId,
                city_id: cityId,
                address_line: addressLine.trim(),
            });

            if (res.data.success) {
                onSuccess(res.data.data.order_code);
            }
        } catch (err: any) {
            const msg = err?.response?.data?.message;
            if (typeof msg === 'object') {
                setError(JSON.stringify(msg));
            } else {
                setError(msg || 'Failed to confirm order');
            }
        } finally {
            setIsSubmitting(false);
        }
    };

    return (
        <div className="fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50 p-4">
            <div className="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                <div className="p-6">
                    <div className="flex items-center justify-between mb-6">
                        <h2 className="text-xl font-bold text-foreground">Confirm Order</h2>
                        <button onClick={onClose} className="text-muted hover:text-foreground text-xl">
                            <i className="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    {error && (
                        <div className="mb-4 p-3 bg-red-100 dark:bg-red-900 text-red-700 dark:text-red-200 rounded text-sm">
                            {error}
                        </div>
                    )}

                    <div className="space-y-6">
                        {/* Customer Details */}
                        <div>
                            <h3 className="text-sm font-medium text-muted uppercase mb-3">Customer Details</h3>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs text-muted mb-1">Name <span className="text-red-500">*</span></label>
                                    <input type="text" value={name} onChange={e => setName(e.target.value)}
                                        className="theme-input w-full" placeholder="Type Your Full Name" />
                                </div>
                                <div>
                                    <label className="block text-xs text-muted mb-1">Email</label>
                                    <input type="email" value={email} onChange={e => setEmail(e.target.value)}
                                        className="theme-input w-full" placeholder="Type Your Email" />
                                </div>
                                <div>
                                    <label className="block text-xs text-muted mb-1">Phone <span className="text-red-500">*</span></label>
                                    <input type="text" value={phone} onChange={e => setPhone(e.target.value)}
                                        className="theme-input w-full" placeholder="Valid Phone No." />
                                </div>
                            </div>
                        </div>

                        {/* Shipping Address */}
                        <div>
                            <h3 className="text-sm font-medium text-muted uppercase mb-3">Shipping Address</h3>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs text-muted mb-1">District <span className="text-red-500">*</span></label>
                                    <select value={stateId} onChange={e => setStateId(Number(e.target.value))}
                                        className="theme-input theme-input-select w-full">
                                        <option value="">Select a District</option>
                                        {states.map(s => <option key={s.id} value={s.id}>{s.name}</option>)}
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs text-muted mb-1">City/Town <span className="text-red-500">*</span></label>
                                    <select value={cityId} onChange={e => setCityId(Number(e.target.value))}
                                        className="theme-input theme-input-select w-full" disabled={!stateId}>
                                        <option value="">Select a city/town</option>
                                        {cities.map(c => <option key={c.id} value={c.id}>{c.name}</option>)}
                                    </select>
                                </div>
                                <div className="sm:col-span-2">
                                    <label className="block text-xs text-muted mb-1">Address <span className="text-red-500">*</span></label>
                                    <textarea value={addressLine} onChange={e => setAddressLine(e.target.value)}
                                        className="theme-input w-full" rows={2} placeholder="Type Your Address" />
                                </div>
                            </div>
                        </div>

                        {/* Items */}
                        <div>
                            <h3 className="text-sm font-medium text-muted uppercase mb-3">Items ({cart.items.length})</h3>
                            <div className="max-h-48 overflow-y-auto border border-border rounded-lg">
                                <table className="w-full text-sm">
                                    <thead className="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th className="text-left px-3 py-2 text-xs text-muted">Product</th>
                                            <th className="text-center px-3 py-2 text-xs text-muted">Qty</th>
                                            <th className="text-right px-3 py-2 text-xs text-muted">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody className="divide-y divide-border">
                                        {cart.items.map(item => (
                                            <tr key={item.id}>
                                                <td className="px-3 py-2">
                                                    <div className="flex items-center gap-2">
                                                        {item.product?.thumbnail && (
                                                            <img src={item.product.thumbnail} alt="" className="w-8 h-8 rounded object-cover"
                                                                onError={(e) => { (e.target as HTMLImageElement).src = '/images/image-error.png'; }} />
                                                        )}
                                                        <div>
                                                            <div className="font-medium">{item.product?.name || 'Unknown'}</div>
                                                            {item.variation?.sku && <div className="text-xs text-muted">SKU: {item.variation.sku}</div>}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="text-center px-3 py-2">{item.quantity}</td>
                                                <td className="text-right px-3 py-2 font-medium text-sky-500">{currencyFormatter(item.total)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {/* Coupon + Shipping */}
                        <div>
                            <h3 className="text-sm font-medium text-muted uppercase mb-3">Discount & Shipping</h3>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs text-muted mb-1">Coupon Code</label>
                                    <div className="flex gap-2">
                                        <input type="text" value={couponCode} onChange={e => { setCouponCode(e.target.value); setCouponError(''); }}
                                            className="theme-input flex-1" placeholder="Enter coupon code"
                                            disabled={!!appliedCoupon} />
                                        {appliedCoupon ? (
                                            <button onClick={handleRemoveCoupon}
                                                className="px-3 py-2 text-sm border border-red-300 text-red-600 rounded hover:bg-red-50 dark:border-red-700 dark:text-red-400">
                                                <i className="fa-solid fa-xmark"></i>
                                            </button>
                                        ) : (
                                            <button onClick={handleApplyCoupon} disabled={isValidatingCoupon || !couponCode.trim()}
                                                className="px-3 py-2 text-sm bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50">
                                                {isValidatingCoupon ? '...' : 'Apply'}
                                            </button>
                                        )}
                                    </div>
                                    {appliedCoupon && (
                                        <div className="mt-1 text-xs text-green-600 dark:text-green-400">
                                            <i className="fa-solid fa-check mr-1"></i>
                                            Coupon &quot;{appliedCoupon.code}&quot; applied
                                            {couponFreeShipping && <span className="ml-1 text-blue-600">(Free Shipping)</span>}
                                        </div>
                                    )}
                                    {couponError && (
                                        <div className="mt-1 text-xs text-red-600">{couponError}</div>
                                    )}
                                </div>
                                <div>
                                    <label className="block text-xs text-muted mb-1">Shipping Charge</label>
                                    <input type="number" value={shippingCharge} onChange={e => setShippingCharge(Number(e.target.value))}
                                        className="theme-input w-full" min="0" step="0.01"
                                        disabled={couponFreeShipping} />
                                    {couponFreeShipping && (
                                        <div className="mt-1 text-xs text-blue-600">Free shipping via coupon</div>
                                    )}
                                </div>
                            </div>
                        </div>

                        {/* Payment Method */}
                        <div>
                            <h3 className="text-sm font-medium text-muted uppercase mb-3">Payment</h3>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-xs text-muted mb-1">Payment Method</label>
                                    <select value={paymentMethod} onChange={e => setPaymentMethod(e.target.value)}
                                        className="theme-input theme-input-select w-full">
                                        <option value="cash_on_delivery">Cash on Delivery</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs text-muted mb-1">Note</label>
                                    <textarea value={note} onChange={e => setNote(e.target.value)}
                                        className="theme-input w-full" rows={2} placeholder="Order note" />
                                </div>
                            </div>
                        </div>

                        {/* Totals */}
                        <div className="border-t border-border pt-4">
                            <div className="space-y-2">
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted">Subtotal</span>
                                    <span>{currencyFormatter(subtotal)}</span>
                                </div>
                                {couponDiscount > 0 && (
                                    <div className="flex justify-between text-sm text-green-600">
                                        <span>Coupon Discount</span>
                                        <span>-{currencyFormatter(couponDiscount)}</span>
                                    </div>
                                )}
                                <div className="flex justify-between text-sm">
                                    <span className="text-muted">Shipping</span>
                                    <span>{couponFreeShipping ? <span className="text-blue-600">Free</span> : currencyFormatter(effectiveShipping)}</span>
                                </div>
                                <div className="flex justify-between text-lg font-bold border-t border-border pt-2">
                                    <span>Grand Total</span>
                                    <span className="text-sky-500">{currencyFormatter(grandTotal)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="mt-6 pt-4 border-t border-border flex justify-end gap-3">
                        <button onClick={onClose}
                            className="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button onClick={handleSubmit} disabled={isSubmitting}
                            className={`px-6 py-2 rounded text-white ${isSubmitting ? 'bg-blue-400 cursor-not-allowed' : 'bg-blue-600 hover:bg-blue-700'}`}>
                            {isSubmitting ? (
                                <><i className="fa-solid fa-circle-notch fa-spin mr-2"></i>Confirming...</>
                            ) : (
                                <><i className="fa-solid fa-check mr-2"></i>Confirm Order</>
                            )}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default ConfirmOrderModal;
