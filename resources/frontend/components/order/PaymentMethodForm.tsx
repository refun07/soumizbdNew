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
        <div className="rounded-card border border-zinc-100">
            <h3 className="py-3.5 px-4 sm:px-5 md:px-7 bg-stone-50">
                {translate('Select Payment Method')}
            </h3>
            <div className="py-3 xl:py-6 px-3 md:px-6 xl:px-9 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                {!window.config.paymentMethods.length ? (
                    <div className="col-span-2 md:col-span-3 lg:col-span-4 text-center py-5">
                        {translate('No payment methods available')}
                    </div>
                ) : (
                    window.config.paymentMethods.map((method) => (
                        <button
                            className={cn(
                                'relative flex items-center justify-center px-4 py-4 border border-neutral-200 rounded-card',
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
        </div>
    );
};

export default PaymentMethodForm;
