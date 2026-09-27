import { FaCheck, FaCopy, FaXmark } from 'react-icons/fa6';
import { useEffect, useRef, useState } from 'react';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { useAppDispatch } from '../../store/store';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

const PaymentMethodForm = () => {
    const dispatch = useAppDispatch();
    const { paymentMethod, bkashAccountNumber, bkashTransactionId } = useCheckoutData();
    const [showBkash, setShowBkash] = useState(false);
    const accountInputRef = useRef<HTMLInputElement>(null);
    const merchantNumber = '01988303011';

    const selectPaymentMethod = (paymentMethod: string) => {
        dispatch(setCheckoutData({ paymentMethod }));
        setShowBkash(paymentMethod === 'bkash');
    };

    useEffect(() => {
        if (showBkash) setTimeout(() => accountInputRef.current?.focus(), 0);
    }, [showBkash]);

    return (
        <div className="rounded-[4px] border border-slate-200 overflow-hidden">
            <h3 className="py-3 px-4 sm:px-5 bg-slate-100 text-sm font-bold text-slate-800 uppercase">
                {translate('Select Payment Method')}
            </h3>
            <div className="p-4 sm:p-5 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                {!window.config.paymentMethods.length ? (
                    <div className="col-span-2 md:col-span-3 lg:col-span-4 text-center py-5">
                        {translate('No payment methods available')}
                    </div>
                ) : (
                    window.config.paymentMethods.map((method) => (
                        <button
                            className={cn(
                                'relative flex items-center justify-center px-4 py-3 border border-slate-200 bg-slate-50 rounded-[3px]',
                                {
                                    'ring ring-secondary/60':
                                        paymentMethod === method.value,
                                },
                            )}
                            onClick={() => selectPaymentMethod(method.value)}
                            key={method.value}
                        >
                            {paymentMethod === method.value && (
                                <span className="absolute top-2 right-2 rounded-full h-5 aspect-square bg-secondary text-white flex items-center justify-center">
                                    <FaCheck />
                                </span>
                            )}
                            <img
                                src={`/images/payment/${method.value}.png`}
                                alt={method.name}
                            />
                        </button>
                    ))
                )}
            </div>
            {showBkash && (
                <div className="fixed inset-0 z-[1000] flex items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true" aria-labelledby="bkash-title">
                    <div className="w-full max-w-lg rounded-md bg-white p-5 shadow-xl">
                        <div className="flex items-start justify-between gap-4">
                            <div><h3 id="bkash-title" className="text-xl font-bold text-[#c50046]">Pay with bKash</h3><p className="mt-1 text-sm text-slate-600">Send the exact order amount, then enter your payment details.</p></div>
                            <button type="button" onClick={() => setShowBkash(false)} className="flex h-11 w-11 items-center justify-center" aria-label="Close bKash payment details"><FaXmark /></button>
                        </div>
                        <div className="mt-5 rounded border border-pink-100 bg-pink-50 p-4">
                            <p className="text-xs text-slate-600">bKash Personal Number</p>
                            <div className="mt-1 flex items-center justify-between gap-3"><strong className="text-xl text-[#c50046]">{merchantNumber}</strong><button type="button" onClick={() => navigator.clipboard.writeText(merchantNumber)} className="inline-flex min-h-11 items-center gap-2 rounded border border-pink-200 px-3 text-sm font-semibold text-[#c50046]"><FaCopy /> Copy</button></div>
                        </div>
                        <div className="mt-5 space-y-4">
                            <label className="block text-sm font-semibold">Your bKash Account Number <span className="text-red-600">*</span><input ref={accountInputRef} type="tel" inputMode="numeric" value={bkashAccountNumber || ''} onChange={(e) => dispatch(setCheckoutData({ bkashAccountNumber: e.target.value.replace(/\D/g, '').slice(0, 11) }))} placeholder="01XXXXXXXXX" className="theme-input mt-2 w-full" /></label>
                            <label className="block text-sm font-semibold">Payment Transaction ID (TrxID) <span className="text-red-600">*</span><input type="text" value={bkashTransactionId || ''} onChange={(e) => dispatch(setCheckoutData({ bkashTransactionId: e.target.value.trim().toUpperCase().slice(0, 30) }))} placeholder="e.g. 2M7A5" className="theme-input mt-2 w-full" /></label>
                        </div>
                        <button type="button" onClick={() => setShowBkash(false)} disabled={!/^01\d{9}$/.test(bkashAccountNumber || '') || !(bkashTransactionId || '').trim()} className="mt-5 min-h-11 w-full rounded bg-[#c50046] px-4 font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">Confirm bKash Details</button>
                    </div>
                </div>
            )}
        </div>
    );
};

export default PaymentMethodForm;
