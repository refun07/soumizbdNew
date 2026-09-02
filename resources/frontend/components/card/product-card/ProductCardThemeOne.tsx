import { FaHeart, FaRegHeart } from 'react-icons/fa';
import { FaCartShopping } from 'react-icons/fa6';
import { LuEye } from 'react-icons/lu';
import { Link } from 'react-router-dom';
import { useProductActions } from '.';
import { IProductShort } from '../../../types/product';
import { cn } from '../../../utils/cn';
import { currencyFormatter } from '../../../utils/numberFormatter';
import { translate } from '../../../utils/translate';
import Image from '../../Image';
import Button from '../../buttons/Button';
import IconButton from '../../buttons/IconButton';
import ThemeRating from '../../reviews/ThemeRating';
import ThemeLabel from '../ThemeLabel';

interface Props {
    product: IProductShort;
}

const ProductCardThemeOne = (props: Props) => {
    const {
        product, // using this variable instead of p to show wishlist toggle icon after wishlist action
        loading,
        handleAddToCart,
        handleAddToWishList,
        handleProductPreview,
        handleRemoveFromWishList,
    } = useProductActions(props.product);

    return (
        <div className="relative flex flex-col group transition-all rounded-card overflow-hidden h-full border border-gray-100 bg-white">
            <div className="absolute top-5 left-5 flex flex-col items-start gap-2 z-[1]">
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

            <div className="absolute top-3 right-3 translate-x-14 z-[1] flex flex-col shadow-[3px_3px_8px_0_#0000000a] group-hover:translate-x-0 transition-all">
                <IconButton
                    as="button"
                    isLoading={
                        loading.addingToWishlist || loading.deletingFromWishlist
                    }
                    className={cn(
                        'text-primary sm:text-xl border-none rounded-b-none',
                        {
                            'text-theme-alert': product.inWishlist,
                        },
                    )}
                    onClick={
                        product.inWishlist
                            ? handleRemoveFromWishList
                            : handleAddToWishList
                    }
                >
                    {product.inWishlist ? <FaHeart /> : <FaRegHeart />}
                </IconButton>

                <IconButton
                    as="button"
                    className="text-primary sm:text-xl border-transparent border-t-black/5 rounded-t-none"
                    onClick={handleProductPreview}
                >
                    <LuEye />
                </IconButton>
            </div>

            <div className="overflow-hidden relative">
                <Link
                    to={`/products/${product.slug}`}
                    className="aspect-square bg-white flex items-center justify-center"
                >
                    <Image
                        src={product.thumbnailImg}
                        alt={product.name}
                        className="w-full h-full group-hover:scale-110 transition-all duration-300"
                    />
                </Link>

                <Button
                    arrow={false}
                    size="md"
                    className="group/button absolute bottom-0 left-0 right-0 w-full translate-y-full rounded-none !h-auto min-h-[44px] px-3 py-2 text-center text-[12px] leading-tight group-hover:translate-y-0 disabled:opacity-100 disabled:text-white sm:min-h-[36px] sm:text-[15px] sm:py-0"
                    onClick={handleAddToCart}
                    isLoading={loading.gettingProduct || loading.addingToCart}
                    disabled={!product.isPreOrder && !product.hasAvailableStock && !window.config.generalSettings.allowBackorder}
                >
                    <span className="flex w-full items-center justify-center gap-2 text-center">
                        <span className="max-xs:hidden text-lg group-hover/button:text-white transition-all">
                            <FaCartShopping />
                        </span>
                        {!product.isPreOrder && !product.hasAvailableStock && !window.config.generalSettings.allowBackorder
                            ? translate('Out of stock')
                            : product.isPreOrder && product.preOrderData?.is_active
                                ? translate('PRE-ORDER')
                                : translate('ADD TO CART')}
                    </span>
                </Button>
            </div>

            <div className="px-3 sm:px-5 pb-3 sm:pb-5 pt-3 flex flex-col rounded-b-[5px]">
                {product.shop?.name && (
                    <p className="text-black/70">{product.shop.name}</p>
                )}
                <ThemeRating
                    ratingClasses="max-sm:max-w-[60px] mb-3 mt-2"
                    rating={product.rating.average}
                    totalReviews={product.rating.total}
                />
                <h3 className="font-medium mb-1.5">
                    <Link
                        to={`/products/${product.slug}`}
                        className="line-clamp-2"
                    >
                        {product.name}
                    </Link>
                </h3>
                {product.unit ? (
                    <p className="mb-3.5 text-xs font-bold text-black/60 line-clamp-1">
                        {product.unit}
                    </p>
                ) : null}
                <div className="flex flex-row gap-1.5 items-center" dir="ltr">
                    {product.basePrice !== product.discountedBasePrice ? (
                        <>
                            <span className="text-primary text-xl font-semibold">
                                {currencyFormatter(product.discountedBasePrice)}
                            </span>
                            <span className="text-black/70 text-lg font-medium line-through">
                                {currencyFormatter(product.basePrice)}
                            </span>
                        </>
                    ) : (
                        <span className="text-primary text-xl font-semibold">
                            {currencyFormatter(product.basePrice)}
                        </span>
                    )}
                </div>
            </div>
        </div>
    );
};

export default ProductCardThemeOne;
