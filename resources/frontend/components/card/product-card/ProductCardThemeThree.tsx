import { FaHeart, FaRegHeart } from 'react-icons/fa';
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

const ProductCardThemeThree = (props: Props) => {
    const {
        product, // using this variable instead of p to show wishlist toggle icon after wishlist action
        loading,
        handleAddToCart,
        handleAddToWishList,
        handleProductPreview,
        handleRemoveFromWishList,
    } = useProductActions(props.product);

    return (
        <div className="relative flex flex-col group transition-all overflow-hidden h-full bg-white">
            <div className="absolute top-5 left-5 flex flex-col items-start gap-2">
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
                    className="aspect-[4/5] bg-white flex items-center justify-center"
                >
                    <Image
                        src={product.thumbnailImg}
                        alt={product.name}
                        className="w-full h-full group-hover:scale-110 transition-all duration-300"
                    />
                </Link>
            </div>

            <div className="px-3 sm:px-5 pb-3 sm:pb-5 pt-3 flex flex-col items-center text-center rounded-b-[5px]">
                <ThemeRating
                    ratingClasses="max-sm:max-w-[60px] mb-3.5 mt-2"
                    rating={product.rating.average}
                    totalReviews={product.rating.total}
                />
                <h3 className="font-medium mb-4">
                    <Link
                        to={`/products/${product.slug}`}
                        className="line-clamp-2"
                    >
                        {product.name}
                    </Link>
                </h3>
                <div className="flex gap-1 items-center">
                    {product.basePrice !== product.discountedBasePrice ? (
                        <>
                            <span className="text-primary text-lg font-semibold">
                                {currencyFormatter(product.discountedBasePrice)}
                            </span>
                            <span className="text-black/70 text-[17px] font-medium line-through">
                                {currencyFormatter(product.basePrice)}
                            </span>
                        </>
                    ) : (
                        <span className="text-black text-lg font-semibold">
                            {currencyFormatter(product.basePrice)}
                        </span>
                    )}
                </div>

                <Button
                    arrow={false}
                    size="md"
                    variant="secondary"
                    className="rounded-none mt-5 disabled:opacity-100 disabled:text-white"
                    onClick={handleAddToCart}
                    isLoading={loading.gettingProduct || loading.addingToCart}
                    disabled={!product.isPreOrder && !product.hasAvailableStock && !window.config.generalSettings.allowBackorder}
                >
                    {!product.isPreOrder && !product.hasAvailableStock && !window.config.generalSettings.allowBackorder
                        ? translate('Out of stock')
                        : translate('ADD TO CART')}
                </Button>
            </div>
        </div>
    );
};

export default ProductCardThemeThree;
