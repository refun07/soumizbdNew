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
        product.categories?.[0]?.name ||
        product.tags?.[0]?.name ||
        product.shop?.name ||
        translate('Womens Care');
    const cartDisabled =
        loading.gettingProduct ||
        loading.addingToCart ||
        (!product.isPreOrder &&
            !product.hasAvailableStock &&
            !window.config.generalSettings.allowBackorder);

    return (
        <div className="group relative flex h-full flex-col overflow-hidden rounded-[5px] border border-[#F4F4F4] bg-white transition duration-300">
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
                <div className="absolute right-4 top-4 flex flex-col gap-3">
                    <button
                        type="button"
                        className={cn(
                            'flex size-9 items-center justify-center rounded-full bg-white text-[16px] text-[#ff2718] shadow-[0_6px_16px_rgba(0,0,0,0.12)] transition hover:bg-[#ff2718] hover:text-white disabled:opacity-60',
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
                        className="flex size-9 items-center justify-center rounded-full bg-white text-[18px] text-[#B3052A] shadow-[0_6px_16px_rgba(0,0,0,0.12)] transition hover:bg-[#B3052A] hover:text-white"
                        onClick={handleProductPreview}
                        aria-label={translate('Preview product')}
                    >
                        <LuEye />
                    </button>
                </div>

                {cartProduct ? (
                    <div className="absolute bottom-5 left-5 right-5 flex h-9 items-center overflow-hidden rounded-full bg-white px-4 text-[#B3052A] shadow-[0_8px_18px_rgba(0,0,0,0.12)]">
                        <button
                            type="button"
                            className="flex h-full w-8 items-center justify-start text-[17px]"
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
                            className="flex h-full w-8 items-center justify-end text-[17px]"
                            onClick={() => handleCartQuantityChange('increase')}
                            aria-label={translate('Increase quantity')}
                        >
                            <BsPlusLg />
                        </button>
                    </div>
                ) : (
                    <button
                        type="button"
                        className="absolute bottom-5 right-5 flex size-11 items-center justify-center rounded-full bg-white text-[24px] text-[#B3052A] shadow-[0_8px_18px_rgba(0,0,0,0.12)] transition duration-300 hover:bg-[#B3052A] hover:text-white disabled:cursor-not-allowed disabled:opacity-60"
                        onClick={handleAddToCart}
                        disabled={cartDisabled}
                        aria-label={translate('Add to Cart')}
                    >
                        <BsPlusLg />
                    </button>
                )}
            </div>

            <div className="flex min-h-[178px] grow flex-col px-5 pb-6 pt-5 sm:px-6">
                <p className="line-clamp-1 text-[13px] font-normal text-[#667085]">
                    {categoryLabel}
                </p>
                <h3 className="mt-2 min-h-[22px] text-[14px] font-semibold leading-[1.45] text-[#111111] transition group-hover:text-[#B3052A]">
                    <Link
                        to={`/products/${product.slug}`}
                        className="line-clamp-2"
                    >
                        {product.name}
                    </Link>
                </h3>
                {product.unit ? (
                    <p className="mt-1 line-clamp-1 text-[12px] font-bold text-[#667085]">
                        {product.unit}
                    </p>
                ) : null}
                <div className="mt-3 flex items-center gap-2">
                    <span className="text-[16px] font-semibold text-[#B3052A]">
                        {currencyFormatter(product.discountedBasePrice)}
                    </span>
                    {priceChanged ? (
                        <span className="text-[13px] font-medium text-[#575757] line-through">
                            {currencyFormatter(product.basePrice)}
                        </span>
                    ) : null}
                </div>
                <ThemeRating
                    rating={product.rating.average}
                    totalReviews={product.rating.total}
                    className="mt-3 text-[12px]"
                    ratingClasses="max-w-[82px]"
                    style={{ maxWidth: 82 }}
                />
            </div>
        </div>
    );
};

export default ProductCardThemeTwo;
