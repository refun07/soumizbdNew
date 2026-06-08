import { FaRegCopy } from 'react-icons/fa';
import { useDispatch } from 'react-redux';
import { togglePopup } from '../../store/features/popup/popupSlice';

import { IoArrowForward } from 'react-icons/io5';
import { ICouponShort } from '../../types/checkout';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';
import Copy from '../Copy';
import Button from '../buttons/Button';

interface Props {
    coupon: ICouponShort;
}

const CouponCard = ({ coupon }: Props) => {
    const dispatch = useDispatch();

    return (
        <div className="relative overflow-hidden rounded-card py-4 px-4 md:px-12 bg-secondary/20">
            {/* half round, left */}
            <span className="absolute top-1/2 -left-4 md:-left-[calc(52px/2)] -translate-y-1/2 size-8 md:size-[52px] rounded-full bg-white"></span>
            {/* half round, right */}
            <span className="absolute top-1/2 -right-4 md:-right-[calc(52px/2)] -translate-y-1/2 size-8 md:size-[52px] rounded-full bg-white"></span>

            {/* middle line */}
            <span className="absolute top-1/2 -translate-y-1/2 left-4 md:left-[26px] right-4 md:right-[26px] border-t border-secondary border-dashed"></span>

            <div className="flex justify-between">
                <div>
                    <p className="mb-2 capitalize font-medium">
                        {translate('Coupon code')}
                    </p>

                    <Copy copyText={coupon.code}>
                        <Button
                            variant="primary"
                            className="from-secondary to-secondary text-white md:px-4"
                            arrow={false}
                        >
                            <span className="font-semibold uppercase">
                                {coupon.code}
                            </span>
                            <span className="text-lg text-primary">
                                <FaRegCopy />
                            </span>
                        </Button>
                    </Copy>
                </div>

                <div className="text-right">
                    <h6 className="text-xl md:text-[34px] text-primary leading-none font-semibold">
                        {coupon.discountType === 'percentage'
                            ? coupon.discount + '%'
                            : currencyFormatter(coupon.discount)}
                    </h6>
                    <p className="text-black/70">{translate('discount')}</p>
                </div>
            </div>

            <div className="flex gap-4 mt-8">
                <div className="grow">
                    <table className="text-sm">
                        <tbody>
                            <tr>
                                <td className="capitalize">
                                    {translate('Valid until')}:
                                </td>
                                <td className="pl-3 text-black/70">
                                    {coupon.endDate}
                                </td>
                            </tr>
                            <tr>
                                <td className="capitalize">
                                    {translate('Min Order')}:
                                </td>
                                <td className="pl-3 text-black/70">
                                    {currencyFormatter(coupon.minSpend)}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button
                        className="mt-3 inline-flex items-center gap-2"
                        onClick={() =>
                            dispatch(
                                togglePopup({
                                    popup: 'coupon-details',
                                    popupProps: { couponCode: coupon.code },
                                }),
                            )
                        }
                    >
                        <span className="text-primary uppercase font-semibold">
                            {translate('View Details')}
                        </span>
                        <span className="text-xl text-secondary">
                            <IoArrowForward />
                        </span>
                    </button>
                </div>

                <div className="self-end">
                    {window.config.generalSettings.appMode ===
                        'multiVendor' && (
                        <Button
                            as="link"
                            variant="outline"
                            to={`/shops/${coupon.shopSlug}`}
                            className="from-transparent"
                        >
                            {translate('Visit Shop')}
                        </Button>
                    )}
                </div>
            </div>
        </div>
    );
};

export default CouponCard;
