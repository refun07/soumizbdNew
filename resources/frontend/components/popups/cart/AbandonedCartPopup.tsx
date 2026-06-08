import { Link } from 'react-router-dom';
import { LiaTimesSolid } from 'react-icons/lia';
import { PiShoppingCartSimpleLight } from 'react-icons/pi';
import { useEffect, useMemo } from 'react';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';
import ModalWrapper from '../ModalWrapper';
import Image from '../../Image';
import { useAuth } from '../../../store/features/auth/authSlice';
import { groupBy } from '../../../utils/groupBy';
import { currencyFormatter } from '../../../utils/numberFormatter';
import { ICartProduct } from '../../../types/product';

const ABANDONED_CART_POPUP_SHOWN = 'abandoned_cart_popup_shown';

const CartItem = ({ item }: { item: ICartProduct }) => {
    const image = item.variation?.image || item.product.thumbnailImg;
    const basePrice = item.variation.basePrice;
    const discountPrice = item.variation.discountedBasePrice;

    return (
        <div className="flex items-center gap-3 py-3">
            <Link
                to={`/products/${item.product.slug}`}
                className="rounded-card border border-primary-14 p-1 shrink-0"
            >
                <Image
                    src={image}
                    alt={item.product.name}
                    className="max-w-[52px] aspect-square"
                />
            </Link>

            <div className="grow min-w-0">
                <Link
                    to={`/products/${item.product.slug}`}
                    className="font-medium text-sm line-clamp-2 hover:text-primary"
                >
                    {item.product.name}
                </Link>

                <div className="text-primary text-sm font-bold flex gap-1 items-center mt-1">
                    {discountPrice !== basePrice ? (
                        <>
                            <span>{currencyFormatter(discountPrice)}</span>
                            <span className="text-black/70 line-through font-normal">
                                {currencyFormatter(basePrice)}
                            </span>
                        </>
                    ) : (
                        <span>{currencyFormatter(basePrice)}</span>
                    )}

                    <span className="text-black/70 font-normal">x {item.qty}</span>
                </div>
            </div>
        </div>
    );
};

const AbandonedCartPopup = () => {
    const dispatch = useAppDispatch();
    const { popup } = usePopup();
    const { carts } = useAuth();
    const isActive = popup === 'abandoned-cart';

    useEffect(() => {
        if (isActive) {
            sessionStorage.setItem(ABANDONED_CART_POPUP_SHOWN, 'true');
        }
    }, [isActive]);

    const { groupedByShop, hasMultipleShops, subtotal } = useMemo(() => {
        const grouped = groupBy(carts, ({ shop }) => shop.name);
        const shopCount = Object.keys(grouped).length;
        const total = carts.reduce(
            (sum, item) => sum + item.variation.discountedBasePrice * item.qty,
            0
        );
        return {
            groupedByShop: grouped,
            hasMultipleShops: shopCount > 1,
            subtotal: total,
        };
    }, [carts]);

    // Don't render if there are no cart items
    if (carts.length === 0) {
        return null;
    }

    const handleClose = () => {
        dispatch(closePopup());
    };

    const handleViewCart = () => {
        handleClose();
    };

    const renderCartItems = () => {
        if (hasMultipleShops) {
            return Object.entries(groupedByShop).map(([shopName, shopCarts]) => (
                <div key={shopName}>
                    <div className="flex items-center justify-between text-sm border-b border-gray-200 py-2 px-4 bg-stone-50">
                        <span className="text-black/70">{translate('seller')}</span>
                        <Link
                            to={`/shops/${shopCarts[0].shop.slug}`}
                            className="text-primary"
                        >
                            {shopName}
                        </Link>
                    </div>
                    {shopCarts.map((item) => (
                        <div
                            className="px-4 border-b border-gray-100 last:border-b-0"
                            key={item.id}
                        >
                            <CartItem item={item} />
                        </div>
                    ))}
                </div>
            ));
        }

        return carts.map((item) => (
            <div
                className="px-4 border-b border-gray-100 last:border-b-0"
                key={item.id}
            >
                <CartItem item={item} />
            </div>
        ));
    };

    return (
        <ModalWrapper isActive={isActive} className="max-w-[500px] rounded-card">
            <div className="flex justify-between px-5 py-4 border-b border-gray-200 bg-primary text-white">
                <h4 className="ek-h3 flex items-center gap-2">
                    <PiShoppingCartSimpleLight className="text-xl" />
                    {translate('Cart Reminder')}
                </h4>

                <button
                    className="text-lg hover:text-white/80"
                    onClick={handleClose}
                >
                    <LiaTimesSolid />
                </button>
            </div>

            <div className="px-5 pt-4 pb-6">
                <div className="text-center mb-4">
                    <h3 className="text-xl font-medium mb-1">
                        {translate('You have items in your cart!')}
                    </h3>
                    <p className="text-gray-600 text-sm">
                        {translate(
                            'It looks like you left some items behind. Complete your purchase now!'
                        )}
                    </p>
                </div>

                {carts.length > 0 && (
                    <div className="mb-4 max-h-[300px] overflow-y-auto border border-gray-200 rounded-lg">
                        {renderCartItems()}
                    </div>
                )}

                <div className="flex justify-between items-center px-4 py-3 bg-stone-50 rounded-lg mb-4">
                    <span className="font-medium">{translate('Subtotal')}</span>
                    <span className="text-lg font-semibold text-primary">
                        {currencyFormatter(subtotal)}
                    </span>
                </div>

                <div className="flex gap-3">
                    <Button
                        as="link"
                        to="/cart"
                        onClick={handleViewCart}
                        variant="primary"
                        size="lg"
                        className="grow"
                    >
                        {translate('View Cart')}
                    </Button>
                    <Button
                        variant="outline"
                        size="lg"
                        onClick={handleClose}
                        className="grow"
                    >
                        {translate('Continue Shopping')}
                    </Button>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default AbandonedCartPopup;
