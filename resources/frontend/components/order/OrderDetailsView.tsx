import { FaCheckCircle } from 'react-icons/fa';
import { IoMdCheckmark } from 'react-icons/io';
import { Link, useParams } from 'react-router-dom';
import AddressCard from '../../components/card/AddressCard';
import ProductCardWide from '../../components/card/ProductCardWide';
import { OrderDetailsSkeleton } from '../../components/skeletons/from-svg';
import NoDataFound from '../../layouts/components/not-found/NoDataFound';
import { useGetOrderByCodeQuery } from '../../store/features/api/orderApi';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { IProductShort, IReview } from '../../types/product';
import { cn } from '../../utils/cn';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

interface Props {
    reviewActive?: boolean;
}

const orderStatuses = [
    'order_placed',
    'confirmed',
    'processing',
    'shipped',
    'delivered',
];

// this component will be used in order details page and order tracking page
// in tracking page, review button should not be shown
// so we need to pass reviewActive prop to show/hide review button
const OrderDetailsView = ({ reviewActive = true }: Props) => {
    const dispatch = useAppDispatch();
    const params = useParams<{ orderCode?: string }>();

    const { currentData: order, isFetching } = useGetOrderByCodeQuery(
        params.orderCode!,
    );

    if (isFetching) return <OrderDetailsSkeleton />;
    if (!order) {
        return (
            <div className="container">
                <NoDataFound
                    title="No Order Found"
                    description="Seems like the order is not available"
                    image="/images/no-media.png"
                />
            </div>
        );
    }

    // open review modal
    const handleReviewClick = ({
        product,
        review,
        orderId,
        shopId,
    }: {
        product: IProductShort;
        review: IReview;
        orderId: number;
        shopId: number;
    }) => {
        dispatch(
            togglePopup({
                popup: 'product-review',
                popupProps: { product, review, orderId, shopId },
            }),
        );
    };

    const statusIndex = orderStatuses.indexOf(order.deliveryStatus);

    return (
        <div className="flex max-md:flex-col md:divide-x divide-primary-14 border h-full rounded-card">
            <div className="grow py-5 md:py-10">
                <div className="flex justify-between mb-3.5 px-3 sm:px-5 md:px-8">
                    <div>
                        <h6 className="font-medium uppercase">
                            {translate('Order No')}
                        </h6>
                        <h1 className="ek-h3 text-primary font-semibold mt-1">
                            {order.codeToShow}
                        </h1>
                    </div>

                    <div className="text-right">
                        <p className="inline-block py-2.5 px-3.5 bg-teal-600 text-white rounded-card ">
                            {order.deliveryStatusToShow}
                        </p>
                        <p className="mt-2 mb-1">
                            {translate('Delivery Time')}: {order.createdDate}
                        </p>
                    </div>
                </div>

                <div className="relative inline-flex gap-6 mx-3 sm:mx-5 md:mx-8">
                    <span className="absolute top-4 left-0 right-0 border-b border-zinc-300 border-dashed z-0"></span>

                    {orderStatuses.map((status) => (
                        <div
                            className="flex flex-col gap-3 items-center"
                            key={status}
                        >
                            <span
                                className={`w-8 h-8 flex items-center justify-center text-lg rounded-full relative bg-white border ${
                                    statusIndex >= orderStatuses.indexOf(status)
                                        ? 'border-theme-green text-theme-green'
                                        : 'border-neutral-300 text-black/40 '
                                }`}
                            >
                                <IoMdCheckmark />
                            </span>
                            <span
                                className={cn('capitalize text-center', {
                                    'text-black/70':
                                        statusIndex <
                                        orderStatuses.indexOf(status),
                                })}
                            >
                                {status.replaceAll('_', ' ')}
                            </span>
                        </div>
                    ))}
                </div>

                <div className="border-t border-zinc-100 mt-9 px-3 sm:px-5 md:px-8 py-4 text-black ">
                    {translate('Order Details')}
                </div>

                <div>
                    {window.config.generalSettings.appMode ===
                        'multiVendor' && (
                        <div className="flex items-center gap-3 text-sm border-b border-primary-14 h-12 px-3 sm:px-5 md:px-8 bg-secondary/20">
                            <h5 className="text-black/70 uppercase">
                                {translate('Seller')}
                            </h5>

                            <Link
                                to={`/shops/${order.shop.slug}`}
                                className="text-primary"
                            >
                                {order.shop.name}
                            </Link>
                        </div>
                    )}

                    {order.items.map((item) => (
                        <div
                            className="px-3 sm:px-5 md:px-8 py-0.5 border-b border-primary-14"
                            key={item.id}
                        >
                            <ProductCardWide
                                cartProduct={item}
                                review={item.review}
                                counter={false}
                                deleteBtn={false}
                                reviewBtn={true}
                                reviewBtnActive={
                                    reviewActive &&
                                    order.deliveryStatus === 'delivered'
                                }
                                showTotalPrice={true}
                                size="sm"
                                onReviewClick={() =>
                                    handleReviewClick({
                                        product: item.product,
                                        review: item.review!,
                                        orderId: order.id,
                                        shopId: order.shop.id,
                                    })
                                }
                            />
                        </div>
                    ))}
                </div>

                <div className="px-3 sm:px-5 md:px-8 w-[calc(100%-120px)] py-5 uppercase space-y-5">
                    <div className="flex justify-between">
                        <span>{translate('subtotal')}</span>
                        <span>{currencyFormatter(order.subtotalAmount)}</span>
                    </div>
                    <div className="flex justify-between">
                        <span>{translate('discount')}</span>
                        <span>{currencyFormatter(order.discountAmount)}</span>
                    </div>
                    <div className="flex justify-between">
                        <span>{translate('tax')}</span>
                        <span>{currencyFormatter(order.taxAmount)}</span>
                    </div>
                    {order.couponDiscountAmount ? (
                        <div className="flex justify-between">
                            <span>{translate('coupon')}</span>
                            <span>
                                {currencyFormatter(order.couponDiscountAmount)}
                            </span>
                        </div>
                    ) : null}
                    <div className="flex justify-between">
                        <span>{translate('Shipping charge')}</span>
                        <span className="">
                            {currencyFormatter(order.shippingChargeAmount)}
                        </span>
                    </div>
                    <div className="flex justify-between">
                        <span>{translate('total')}</span>
                        <span className="text-primary text-lg font-bold">
                            {currencyFormatter(order.totalAmount)}
                        </span>
                    </div>
                </div>

                <div className="border-t border-primary-14 px-3 sm:px-5 md:px-8 py-5 md:py-8">
                    <h6 className="mb-3 text-sm uppercase">
                        {translate('Payment Method')}
                    </h6>

                    <div className="flex items-center gap-4">
                        <div className="py-2.5 px-3 sm:px-5 md:px-7 text-black/70 bg-primary/20 rounded-card capitalize">
                            {order.paymentMethod}
                        </div>
                        <div className="inline-flex items-center capitalize gap-1.5">
                            <span
                                className={cn(`text-2xl `, {
                                    'text-teal-600':
                                        order.paymentStatus === 'paid',
                                    ' text-black/70 ':
                                        order.paymentStatus === 'unpaid',
                                })}
                            >
                                <FaCheckCircle />
                            </span>
                            <span>{order.paymentStatus}</span>
                        </div>
                    </div>
                </div>
                <div className="border-t border-primary-14 px-3 sm:px-5 md:px-8 pt-7">
                    <h6 className="mb-5 text-sm uppercase">
                        {translate('Delivered To')}
                    </h6>

                    <div>
                        <AddressCard
                            editable={false}
                            deletable={false}
                            selectable={false}
                            address={order.deliveryAddress}
                            className="max-w-[360px]"
                        />
                    </div>
                </div>
            </div>
            <div className="md:w-[388px] py-5 md:py-10 px-3 sm:px-5 md:px-8">
                <h5 className="text-black font-medium uppercase mb-6">
                    {translate('Order Timeline')}
                </h5>

                <div className="flex flex-col">
                    {order.orderTimeline.map((item) => (
                        <div
                            className="relative flex gap-4 pb-9 [&:last-child>span]:hidden"
                            key={item.id}
                        >
                            <span className="absolute left-[5px] z-0 h-full border-l border-zinc-300 border-dashed"></span>

                            <div>
                                <span className="relative w-3 h-3 inline-block rounded-full bg-secondary"></span>
                            </div>

                            <div>
                                <h6 className="font-medium mb-2 capitalize">
                                    {item.status}
                                </h6>
                                <p className="text-black/70 mb-3">
                                    {item.note}
                                </p>
                                <time className="text-sm">
                                    {item.createdDate}
                                </time>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
};

export default OrderDetailsView;
