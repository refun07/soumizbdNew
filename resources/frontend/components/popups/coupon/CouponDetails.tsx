import { useEffect } from 'react';
import toast from 'react-hot-toast';
import { FaRegCopy } from 'react-icons/fa';
import { LiaTimesSolid } from 'react-icons/lia';
import { useLazyGetCouponDetailsQuery } from '../../../store/features/api/shopApi';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { currencyFormatter } from '../../../utils/numberFormatter';
import { translate } from '../../../utils/translate';
import Copy from '../../Copy';
import Button from '../../buttons/Button';
import ModalWrapper from '../ModalWrapper';

const CouponDetails = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const isActive = popup === 'coupon-details';
    const [getCouponDetails, { data: coupon, isLoading }] =
        useLazyGetCouponDetailsQuery();

    useEffect(() => {
        if (isActive) {
            getCouponDetails(popupProps?.couponCode, true)
                .unwrap()
                .then((coupon) => {
                    // if coupon not found
                    // close the popup and show toast
                    if (!coupon) {
                        dispatch(closePopup());
                        toast.error(translate('Coupon not found'));
                    }
                });
        }
    }, [isActive]);

    return (
        <ModalWrapper isActive={isActive}>
            <button
                className="absolute right-5 top-5 text-xl text-white z-[1]"
                onClick={() => dispatch(closePopup())}
            >
                <LiaTimesSolid />
            </button>
            <div className="theme-modal__body min-h-[595px] w-full bg-white p-0">
                <div className="flex max-sm:flex-col items-center justify-between py-5 sm:py-8 md:py-12 gap-4 px-4 sm:px-9 md:px-[52px] bg-primary text-white">
                    <div className="leading-none">
                        {isLoading ? (
                            <span className="skeleton bg-white/30 h-10"></span>
                        ) : (
                            <span className="text-[40px] font-semibold">
                                {coupon?.discountType === 'percentage'
                                    ? coupon?.discount + '%'
                                    : currencyFormatter(coupon?.discount)}
                            </span>
                        )}
                        <br />
                        <span className="font-normal">
                            {coupon?.discountType === 'percentage'
                                ? 'Percentage Discount'
                                : 'Flat Discount'}
                        </span>
                    </div>

                    {!isLoading && (
                        <table>
                            <tbody>
                                <tr>
                                    <td>{translate('Valid until')}:</td>
                                    <td className="pl-3">{coupon?.endDate}</td>
                                </tr>
                                <tr>
                                    <td>{translate('Min Order')}:</td>
                                    <td className="pl-3">
                                        {currencyFormatter(coupon?.minSpend)}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{translate('Max Discount')}:</td>
                                    <td className="pl-3">
                                        {currencyFormatter(
                                            coupon?.maxDiscountAmount,
                                        )}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    )}

                    <div>
                        <p className="mb-0.5">{translate('Coupon code')}</p>
                        <Copy copyText={coupon?.code!}>
                            <Button
                                arrow={false}
                                variant="link"
                                className="h-[37px] min-w-[90px] bg-secondary px-4"
                            >
                                <span className="font-semibold uppercase">
                                    {coupon?.code}
                                </span>
                                <span className="text-lg text-primary">
                                    <FaRegCopy />
                                </span>
                            </Button>
                        </Copy>
                    </div>
                </div>

                <div className="py-5 sm:pt-8 sm:pb-7 px-4 sm:px-9 md:px-[52px]">
                    <h4 className="ek-h5 font-semibold text-primary">
                        {translate('Terms and Conditions')}
                    </h4>

                    <ul className="mt-4 text-sm text-black/70 list-check space-y-1">
                        {isLoading ? (
                            <>
                                <li className="skeleton bg-gray-300 w-1/2"></li>
                                <li className="skeleton bg-gray-300 w-1/4"></li>
                                <li className="skeleton bg-gray-300 w-1/3"></li>
                                <li className="skeleton bg-gray-300 w-3/4"></li>
                            </>
                        ) : (
                            coupon?.conditions.map((condition) => (
                                <li key={condition.id}>{condition.text}</li>
                            ))
                        )}
                    </ul>

                    <h4 className="ek-h5 font-semibold text-primary mt-7">
                        {translate('Eligible Products')}
                    </h4>
                    <div className="px-8 py-5 mt-5 rounded-card bg-secondary/20">
                        <h5 className="text-primary font-medium mb-1">
                            {translate('Categories')}
                        </h5>
                        <p className="text-black/70">
                            {isLoading ? (
                                <div className="flex flex-wrap gap-x-2 gap-y-1">
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-2/6"></span>
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-6/6"></span>
                                    <span className="skeleton w-2/6"></span>
                                    <span className="skeleton w-1/6"></span>
                                </div>
                            ) : (
                                coupon?.categories
                                    .map((category) => category.name)
                                    .join(' | ')
                            )}
                        </p>

                        <h5 className="text-primary font-medium mb-1 mt-4">
                            {translate('Products')}
                        </h5>
                        <p className="text-black/70">
                            {isLoading ? (
                                <div className="flex flex-wrap gap-x-2 gap-y-1">
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-6/6"></span>
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-2/6"></span>
                                    <span className="skeleton w-2/6"></span>
                                </div>
                            ) : (
                                coupon?.products
                                    .map((product) => product.name)
                                    .join(' | ')
                            )}
                        </p>
                    </div>

                    <div className="flex justify-end mt-6">
                        <Button
                            as="link"
                            variant="outline"
                            to={`/products?couponCode=${coupon?.code}`}
                        >
                            {translate('View Shop')}
                        </Button>
                    </div>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default CouponDetails;
