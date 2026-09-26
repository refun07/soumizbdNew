import { FaCheck } from 'react-icons/fa6';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { useAppDispatch } from '../../store/store';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

const PaymentMethodForm = () => {
    const dispatch = useAppDispatch();
    const { paymentMethod } = useCheckoutData();

    const selectPaymentMethod = (paymentMethod: string) => {
        dispatch(setCheckoutData({ paymentMethod }));
    };

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
                            {method.value === 'bkash_personal' ? (
                                <span className="flex flex-col items-center gap-1">
                                    <img
                                        src={`/images/payment/${method.value}.png`}
                                        alt={method.name}
                                    />
                                    <span className="text-xs font-semibold text-slate-600">
                                        {translate('Send Money')}
                                    </span>
                                </span>
                            ) : (
                                <img
                                    src={`/images/payment/${method.value}.png`}
                                    alt={method.name}
                                />
                            )}
                        </button>
                    ))
                )}
            </div>
        </div>
    );
};

export default PaymentMethodForm;
