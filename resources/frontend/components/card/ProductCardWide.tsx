import { HiStar } from 'react-icons/hi';
import { LiaTimesSolid } from 'react-icons/lia';
import { Link } from 'react-router-dom';
import { ICartProduct, IReview } from '../../types/product';
import { cn } from '../../utils/cn';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';
import Image from '../Image';
import Counter, { TCounterOrder } from '../inputs/Counter';
import ThemeRating from '../reviews/ThemeRating';
import { useProductActions } from './product-card';

interface Props {
    cartProduct: ICartProduct;
    review?: IReview | null;
    size?: 'sm' | 'md' | 'lg';
    counter?: boolean;
    onlyPrice?: boolean;
    deleteBtn?: boolean;
    /** default `false`. if true it returns review button container is rendered. */
    reviewBtn?: boolean;
    /** default `true`. if order is not delivered then pass false, to hide the button only (not the container). */
    reviewBtnActive?: boolean;
    showTotalPrice?: boolean;
    containerClassName?: string;

    onReviewClick?: () => void;
}

const ProductCardWide = ({
    cartProduct,
    review,
    size = 'sm',
    counter = true,
    onlyPrice = false,
    deleteBtn = true,
    reviewBtn = false,
    reviewBtnActive = true,
    showTotalPrice = false,
    containerClassName,
    onReviewClick,
}: Props) => {
    const { handleCartQuantityChange, handleDeleteFromCart } =
        useProductActions(cartProduct.product);

    const counterOrder: [TCounterOrder, TCounterOrder, TCounterOrder] =
        size === 'sm'
            ? ['increment', 'count', 'decrement']
            : ['decrement', 'count', 'increment'];

    const basePrice = cartProduct.variation!.basePrice;
    const discountPrice = cartProduct.variation!.discountedBasePrice;

    const totalPrice =
        cartProduct.variation!.discountedBasePrice * cartProduct.qty;

    return (
        <div
            className={cn(
                'flex items-center gap-2 sm:gap-5 py-1 sm:py-2.5',
                containerClassName,
            )}
        >
            <div>
                <Link
                    to={`/products/${cartProduct.product.slug}`}
                    className="rounded-card border border-primary-14 p-1"
                >
                    <Image
                        src={
                            cartProduct.variation?.image ||
                            cartProduct.product.thumbnailImg
                        }
                        alt={cartProduct.product.name}
                        className={`${
                            size === 'sm'
                                ? 'max-w-[52px]'
                                : 'max-w-[52px] sm:max-w-[100px]'
                        } aspect-square`}
                    />
                </Link>
            </div>

            <div className="grow">
                <h3 className="font-medium mb-3.5">
                    <Link
                        to={`/products/${cartProduct.product.slug}`}
                        className="line-clamp-2"
                    >
                        {cartProduct.product.name}
                    </Link>
                </h3>

                <div className="text-primary text-sm font-bold flex gap-1 items-center">
                    {discountPrice !== basePrice ? (
                        <>
                            <span>{currencyFormatter(discountPrice)}</span>
                            {!onlyPrice && (
                                <span className="text-black/70 line-through font-normal">
                                    {currencyFormatter(basePrice)}
                                </span>
                            )}
                        </>
                    ) : (
                        <>{currencyFormatter(basePrice)}</>
                    )}

                    {!counter && (
                        <span className="text-black/70 font-normal">
                            x {cartProduct.qty}
                        </span>
                    )}

                    {cartProduct.variation?.name && (
                        <span className="px-2 py-1 ml-2 text-[9px] sm:text-sm uppercase font-normal text-white leading-none bg-secondary rounded">
                            {cartProduct.variation?.name}
                        </span>
                    )}
                </div>
            </div>

            {showTotalPrice && (
                <div className="text-sm text-right font-medium text-black/70 w-[60px]">
                    {currencyFormatter(totalPrice)}
                </div>
            )}

            {reviewBtn && (
                <div className="w-[100px] text-right">
                    {reviewBtnActive && (
                        <button type="button" onClick={onReviewClick}>
                            {!review ? (
                                <span className="text-sm text-right text-theme-orange font-bold whitespace-nowrap uppercase">
                                    {translate('Write Review')}
                                </span>
                            ) : (
                                <>
                                    <span className="sm:hidden flex items-center gap-1">
                                        <span className="text-theme-orange text-xl">
                                            <HiStar />
                                        </span>
                                        {review.rating}
                                    </span>
                                    <ThemeRating
                                        readOnly
                                        rating={review.rating}
                                        className="max-sm:hidden"
                                        style={{
                                            maxWidth: 100,
                                        }}
                                    />
                                </>
                            )}
                        </button>
                    )}
                </div>
            )}

            {(counter || deleteBtn) && (
                <div
                    className={`h-full flex items-center justify-end ${
                        size === 'sm' ? 'gap-1' : 'gap-8'
                    }`}
                >
                    {counter && (
                        <Counter
                            value={cartProduct.qty}
                            order={counterOrder}
                            min={0}
                            max={window.config.generalSettings.allowBackorder ? (cartProduct.product.maxPurchaseQty || 999) : Math.min(cartProduct.product.stockQty, cartProduct.product.maxPurchaseQty || 999)}
                            size={size}
                            orientation={
                                size === 'sm' ? 'vertical' : 'horizontal'
                            }
                            onDecrement={() =>
                                handleCartQuantityChange('decrease')
                            }
                            onIncrement={() =>
                                handleCartQuantityChange('increase')
                            }
                        />
                    )}
                    {deleteBtn && (
                        <button className="p-1" onClick={handleDeleteFromCart}>
                            <LiaTimesSolid />
                        </button>
                    )}
                </div>
            )}
        </div>
    );
};

export default ProductCardWide;
