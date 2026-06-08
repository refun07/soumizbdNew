import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { useParams } from 'react-router-dom';
import Button from '../../components/buttons/Button';
import SvgOrderSuccess from '../../components/icons/OrderSuccess';
import { useGetOrderSuccessDataQuery } from '../../store/features/api/orderApi';
import { useLazyGetCartProductsQuery } from '../../store/features/api/productApi';
import { clearCheckoutData } from '../../store/features/checkout/checkoutSlice';
import { dateFormatter } from '../../utils/dateFormatter';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

const OrderSuccess = () => {
    const params = useParams<{ orderCode: string }>();
    const dispatch = useDispatch();
    const [getCartProducts] = useLazyGetCartProductsQuery();
    const { data: orderSuccessData } = useGetOrderSuccessDataQuery(
        params.orderCode!,
    );

    useEffect(() => {
        // if order is placed successfully, clear checkout data and reload cart
        dispatch(clearCheckoutData());
        // getCartProducts(); // reload cart
    }, []);

    return (
        <div className="container">
            <div className="flex flex-col items-center mx-auto py-10 max-w-[480px]">
                <div className="w-full">
                    <SvgOrderSuccess className="w-full max-w-[470px] mx-auto" />
                </div>
                <h2 className="ek-h2 text-center">
                    {translate('Thank you. Your order has been received.')}
                </h2>
                <p className="mt-2 text-center">
                    {translate(
                        'We have emailed your order confirmation, and will send you an update when your order has shipped.',
                    )}
                </p>
            </div>

            <div className="rounded-card border border-zinc-100">
                <h3 className="py-3.5 px-4 sm:px-5 md:px-7 bg-stone-50 uppercase">
                    {translate('Customer Details')}
                </h3>

                <div className="py-4 sm:py-6 px-2 sm:px-5 md:px-9 grid grid-cols-2 lg:grid-cols-3 gap-x-3 md:gap-x-10 lg:gap-x-16 xl:gap-x-[100px] gap-y-5">
                    <div className="space-y-5">
                        <div>
                            <p className="text-black/70 mb-1">
                                {translate('Customer Name')}
                            </p>
                            <p className="text-black">
                                {orderSuccessData?.summary?.customerName}
                            </p>
                        </div>
                        <div>
                            <p className="text-black/70 mb-1">
                                {translate('Phone No.')}
                            </p>
                            <p className="text-black">
                                {orderSuccessData?.summary?.phone}
                            </p>
                        </div>
                        <div>
                            <p className="text-black/70 mb-1">
                                {translate('Email')}
                            </p>
                            <p className="text-black">
                                {orderSuccessData?.summary?.email}
                            </p>
                        </div>
                    </div>
                    <div className="space-y-5">
                        <div>
                            <p className="text-black/70 mb-1">
                                {translate('Shipping Address')}
                            </p>
                            <p className="text-black">
                                {orderSuccessData?.summary?.shippingAddress}
                            </p>
                        </div>
                        {orderSuccessData?.summary?.billingAddress ? (
                            <div>
                                <p className="text-black/70 mb-1">
                                    {translate('Billing Address')}
                                </p>
                                <p className="text-black">
                                    {orderSuccessData?.summary?.billingAddress}
                                </p>
                            </div>
                        ) : null}
                    </div>
                    <div className="space-y-5">
                        <div>
                            <p className="text-black/70 mb-3">
                                {translate('Payment Method')}
                            </p>
                            <div className="px-4 py-4 border border-neutral-200 rounded-card inline-block">
                                <img
                                    src={`/images/payment/${orderSuccessData?.summary?.paymentMethod}.png`}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div className="rounded-card border border-zinc-100 mt-4 md:mt-8">
                <h3 className="py-3.5 px-4 sm:px-5 md:px-7 bg-stone-50 uppercase">
                    {translate('Order Information')}
                </h3>

                <div className="py-4 sm:py-6 px-2 sm:px-5 md:px-9 text-center space-y-10">
                    {orderSuccessData?.orders?.map((order) => (
                        <div className="divide-x divide-zinc-100 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            {window.config.generalSettings.appMode === 'multiVendor' && (
                            <div>
                                <p className="text-black/70 mb-1">
                                    {translate('Seller Name')}
                                </p>
                                <p className="text-black">{order.shop.name}</p>
                            </div>
                            )}
                            <div>
                                <p className="text-black/70 mb-1">
                                    {translate('Order Number')}
                                </p>
                                <p className="text-black">{order.code}</p>
                            </div>
                            <div>
                                <p className="text-black/70 mb-1">
                                    {translate('Order Date')}
                                </p>
                                <p className="text-black">
                                    {dateFormatter(order.createdDate)}
                                </p>
                            </div>
                            <div>
                                <p className="text-black/70 mb-1">
                                    {translate('Total Bill')}
                                </p>
                                <p className="text-black">
                                    {currencyFormatter(order.totalAmount)}
                                </p>
                            </div>
                            <div className="max-md:col-span-2">
                                <Button
                                    as="link"
                                    to={`/orders/${order.code}`}
                                    size="lg"
                                >
                                    {translate('Order Details')}
                                </Button>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default OrderSuccess;
