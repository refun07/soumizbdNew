import { BsDashLg, BsPlusLg } from 'react-icons/bs';
import { FaHeart, FaRegHeart } from 'react-icons/fa';
import { LuEye } from 'react-icons/lu';
import { Link } from 'react-router-dom';
import { useProductActions } from '.';
import { IProductShort } from '../../../types/product';
import { cn } from '../../../utils/cn';
import { currencyFormatter } from '../../../utils/numberFormatter';
import { translate } from '../../../utils/translate';
import Image from '../../Image';
import ThemeRating from '../../reviews/ThemeRating';
import ThemeLabel from '../ThemeLabel';

interface Props {
    product: IProductShort;
}

const ProductCardThemeTwo = (props: Props) => {
    const {
        product, // using this variable instead of p to show wishlist toggle icon after wishlist action
        cartProduct,
        loading,
        handleAddToCart,
        handleAddToWishList,
        handleCartQuantityChange,
        handleProductPreview,
        handleRemoveFromWishList,
    } = useProductActions(props.product);
    const priceChanged = product.basePrice !== product.discountedBasePrice;
    const categoryLabel =
        product.tags?.[0]?.name || product.shop?.name || translate('Womens Care');
    const cartDisabled =
        loading.gettingProduct ||
        loading.addingToCart ||
        (!product.isPreOrder &&
            !product.hasAvailableStock &&
            !window.config.generalSettings.allowBackorder);

    return (
        <div className="group relative flex h-full flex-col overflow-hidden rounded-[5px] border border-transparent bg-white transition duration-300 hover:border-[#bc2444] hover:shadow-[0_14px_35px_rgba(179,5,42,0.14)]">
            <div className="absolute left-4 top-4 z-[1] flex flex-col items-start gap-2">
                {product.isPreOrder && product.preOrderData?.is_active ? (
                    <ThemeLabel
                        label={{
                            text: translate('Pre-Order'),
                            variant: 'no-color',
                        }}
                        bgColor={'#3b82f6'}
                        textColor={'#fff'}
                    />
                ) : null}
                {!product.isPreOrder && !product.hasAvailableStock && !window.config.generalSettings.allowBackorder ? (
                    <ThemeLabel
                        label={{
                            text: translate('Out of stock'),
                            variant: 'no-color',
                        }}
                        bgColor={'#a6a6a6'}
                        textColor={'#fff'}
                    />
                ) : null}
            </div>

            <div className="relative aspect-square overflow-hidden bg-white">
                <Link
                    to={`/products/${product.slug}`}
                    className="flex size-full items-center justify-center bg-[#f7f7f7]"
                >
                    <Image
                        src={product.thumbnailImg}
                        alt={product.name}
                        className="size-full object-cover transition duration-300 group-hover:scale-105"
                    />
                </Link>
                <div className="absolute right-5 top-5 flex translate-x-5 flex-col gap-4 opacity-0 transition duration-300 group-hover:translate-x-0 group-hover:opacity-100">
                    <button
                        type="button"
                        className={cn(
                            'flex size-[46px] items-center justify-center rounded-full bg-white text-[18px] text-[#ff2718] shadow-[0_8px_18px_rgba(0,0,0,0.12)] transition hover:bg-[#ff2718] hover:text-white disabled:opacity-60',
                            product.inWishlist && 'text-[#ff2718]',
                        )}
                        onClick={
                            product.inWishlist
                                ? handleRemoveFromWishList
                                : handleAddToWishList
                        }
                        disabled={
                            loading.addingToWishlist ||
                            loading.deletingFromWishlist
                        }
                        aria-label={
                            product.inWishlist
                                ? translate('Remove from wishlist')
                                : translate('Add to wishlist')
                        }
                    >
                        {product.inWishlist ? <FaHeart /> : <FaRegHeart />}
                    </button>
                    <button
                        type="button"
                        className="flex size-[46px] items-center justify-center rounded-full bg-white text-[22px] text-[#bc2444] shadow-[0_8px_18px_rgba(0,0,0,0.12)] transition hover:bg-[#bc2444] hover:text-white"
                        onClick={handleProductPreview}
                        aria-label={translate('Preview product')}
                    >
                        <LuEye />
                    </button>
                </div>

                {cartProduct ? (
                    <div className="absolute bottom-5 left-5 right-5 flex h-[38px] translate-y-8 items-center overflow-hidden rounded-full bg-white px-5 text-[#bc2444] opacity-0 shadow-[0_8px_18px_rgba(0,0,0,0.12)] transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                        <button
                            type="button"
                            className="flex h-full w-8 items-center justify-start text-[18px]"
                            onClick={() => handleCartQuantityChange('decrease')}
                            aria-label={translate('Decrease quantity')}
                        >
                            <BsDashLg />
                        </button>
                        <span className="grow text-center text-[12px] font-semibold text-[#111111]">
                            {String(cartProduct.qty).padStart(2, '0')}{' '}
                            {translate('Items')}
                        </span>
                        <button
                            type="button"
                            className="flex h-full w-8 items-center justify-end text-[18px]"
                            onClick={() => handleCartQuantityChange('increase')}
                            aria-label={translate('Increase quantity')}
                        >
                            <BsPlusLg />
                        </button>
                    </div>
                ) : (
                    <button
                        type="button"
                        className="absolute bottom-5 right-5 flex size-[46px] translate-y-8 items-center justify-center rounded-full bg-white text-[26px] text-[#bc2444] opacity-0 shadow-[0_8px_18px_rgba(0,0,0,0.12)] transition duration-300 hover:bg-[#bc2444] hover:text-white group-hover:translate-y-0 group-hover:opacity-100 disabled:cursor-not-allowed disabled:opacity-60"
                        onClick={handleAddToCart}
                        disabled={cartDisabled}
                        aria-label={translate('Add to Cart')}
                    >
                        <BsPlusLg />
                    </button>
                )}
            </div>

            <div className="flex grow flex-col px-5 pb-7 pt-5 sm:px-6 xl:px-8 xl:pt-6">
                <p className="line-clamp-1 text-[14px] font-medium text-[#667085] sm:text-[15px]">
                    {categoryLabel}
                </p>
                <h3 className="mt-1 min-h-[24px] text-[15px] font-semibold leading-[1.45] text-[#111111] transition group-hover:text-[#bc2444]">
                    <Link
                        to={`/products/${product.slug}`}
                        className="line-clamp-2"
                    >
                        {product.name}
                    </Link>
                </h3>
                <div className="mt-4 flex items-center gap-2">
                    <span className="text-[20px] font-semibold text-[#bc1734]">
                        {currencyFormatter(product.discountedBasePrice)}
                    </span>
                    {priceChanged ? (
                        <span className="text-[16px] font-semibold text-[#575757] line-through">
                            {currencyFormatter(product.basePrice)}
                        </span>
                    ) : null}
                </div>
                <ThemeRating
                    rating={product.rating.average}
                    totalReviews={product.rating.total}
                    className="mt-3 text-[13px]"
                    ratingClasses="max-w-[92px]"
                    style={{ maxWidth: 92 }}
                />
            </div>
        </div>
    );
};

export default ProductCardThemeTwo;
