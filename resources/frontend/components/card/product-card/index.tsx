import { useMemo, useState } from 'react';
import toast from 'react-hot-toast';
import { useDispatch } from 'react-redux';
import {
    useAddToCartMutation,
    useAddToWishListMutation,
    useDeleteCartProductMutation,
    useDeleteWishListMutation,
    useLazyGetProductDetailsQuery,
    useUpdateCartProductMutation,
} from '../../../store/features/api/productApi';
import { useAuth } from '../../../store/features/auth/authSlice';
import { togglePopup } from '../../../store/features/popup/popupSlice';
import { IProductShort } from '../../../types/product';
import { isProductAvailable } from '../../../utils/checkStock';
import { translate } from '../../../utils/translate';
import ProductCardThemeOne from './ProductCardThemeOne';
import ProductCardThemeFour from './ProductCardThemeFour';
import ProductCardThemeThree from './ProductCardThemeThree';
import ProductCardThemeTwo from './ProductCardThemeTwo';

import useGTMEventsNew from 'hooks/useGTMEventNew';
import { createMetaEventId } from '../../../utils/gtmCustomerData';

interface Props {
    product: IProductShort;
}

export const useProductActions = (p: IProductShort) => {
    const { user, carts, isLoading, guestUserId } = useAuth();
    const dispatch = useDispatch();
    // keep track of product in state to update wishlist icon
    const [product, setProduct] = useState<IProductShort>(p);
    const [addToWishList, { isLoading: addingToWishlist }] =
        useAddToWishListMutation();
    const [deleteFromWishList, { isLoading: deletingFromWishlist }] =
        useDeleteWishListMutation();
    const [addToCart, { isLoading: addingToCart }] = useAddToCartMutation();
    const [getProductDetails, { isLoading: gettingProduct }] =
        useLazyGetProductDetailsQuery();
    const [deleteCartProduct] = useDeleteCartProductMutation();
    const [updateCartProduct] = useUpdateCartProductMutation();

    const gtmEvent = useGTMEventsNew();

    const cartProduct = useMemo(() => {
        return carts.find((cart) => cart.product.id === product.id);
    }, [carts, product.id]);

    const handleProductPreview = () => {
        dispatch(
            togglePopup({
                popup: 'product-preview',
                size: 'lg',
                popupProps: {
                    slug: product.slug,
                },
            }),
        );
    };

    const handleAddToCart = async () => {
        // check user location is set or not
        // if (!userLocation?.area?.zone_id) {
        //     dispatch(
        //         togglePopup({
        //             popup: 'user-location',
        //             popupProps: {
        //                 redirectOnSuccess: `/products/${product.slug}`,
        //             },
        //         }),
        //     );
        //     return;
        // }

        if (product.stockQty < 1 && !window.config.generalSettings.allowBackorder) {
            toast.error(translate('Out of stock'));
            return;
        }

        // if product has variations, show product preview popup
        if (product.hasVariation) {
            handleProductPreview();
            return;
        }

        // else fetch the product and add it to cart with 1st (and only) variation id

        const productDetails = await getProductDetails(product.slug).unwrap();

        // check if the selected variation is in stock or not
        if (!isProductAvailable(productDetails.variations[0]!, carts)) {
            return;
        }

        try {
            const eventId = createMetaEventId('add_to_cart');

            await addToCart({
                productVariationId: productDetails.variations[0].id,
                qty: 1,
                warehouseId:
                    productDetails.variations[0].stocks[0]?.warehouseId,
                eventId,
            }).unwrap();
            toast.success(translate('Added to cart'));

            const selectedVariation = productDetails.variations[0];

            const variantId = `${selectedVariation.productId}-${selectedVariation.id}`;

            gtmEvent({
                event: 'add_to_cart',
                event_id: eventId,
                ecommerce: {
                    currency: window.config.currency.code,
                    value: selectedVariation.basePrice,
                    items: [
                        {
                            item_id: variantId, // <-- FIXED
                            item_name: `${product.name} (${selectedVariation.name})`,
                            price: selectedVariation.basePrice,
                            quantity: 1,
                        },
                    ],
                },
            });
        } catch (err: any) {
            toast.error(err.data.message);
        }
    };

    const handleCartQuantityChange = async (
        action: 'increase' | 'decrease',
    ) => {
        if (!cartProduct) return;

        // check if the selected variation is available in stock (skip if backorder enabled)
        if (
            action === 'increase' &&
            !window.config.generalSettings.allowBackorder &&
            !isProductAvailable(cartProduct.variation, carts)
        ) {
            return;
        }

        await updateCartProduct({
            id: cartProduct.id,
            action:
                action === 'decrease' && cartProduct.qty === 1
                    ? 'delete'
                    : action,
            guestUserId,
            warehouseId: cartProduct.variation.stocks[0]?.warehouseId!,
        }).unwrap();
    };

    const handleDeleteFromCart = async () => {
        if (!cartProduct) return;

        try {
            await deleteCartProduct({
                id: cartProduct.id,
                guestUserId,
                zoneId: 1,
            }).unwrap();

            toast.success(translate('Product removed from cart!'));

            gtmEvent({
                event: 'remove_from_cart',
                ecommerce: {
                    currency: window.config.currency.code,
                    value: cartProduct.variation.basePrice * cartProduct.qty,
                    items: [
                        {
                            item_id: `${cartProduct.product.id}-${cartProduct.variation.id}`, // <-- FIXED
                            item_name: `${cartProduct.product.name} (${cartProduct.variation.name})`,
                            price: cartProduct.variation.basePrice,
                            quantity: cartProduct.qty,
                        },
                    ],
                },
            });
        } catch (error) {
            toast.error(translate('Something went wrong!'));
        }
    };

    const handleAddToWishList = async () => {
        if (isLoading || !user) {
            dispatch(togglePopup('signin'));
            return;
        }

        try {
            const eventId = createMetaEventId('add_to_wishlist');

            await addToWishList({ productId: product.id, eventId }).unwrap();
            setProduct((prev) => ({ ...prev, inWishlist: true }));
            toast.success(translate('Added to wishlist'));

            gtmEvent({
                event: 'add_to_wishlist',
                event_id: eventId,
                ecommerce: {
                    items: [
                        {
                            item_id: product.id.toString(),
                            item_name: product.name,
                        },
                    ],
                },
            });
        } catch (err: any) {
            toast.error(err.data.message);
        }
    };

    const handleRemoveFromWishList = async () => {
        try {
            await deleteFromWishList(product.id).unwrap();
            setProduct((prev) => ({ ...prev, inWishlist: false }));
            toast.success(translate('Removed from wishlist'));

            gtmEvent({
                event: 'remove_from_wishlist',
                ecommerce: {
                    items: [
                        {
                            item_id: product.id.toString(),
                            item_name: product.name,
                        },
                    ],
                },
            });
        } catch (err: any) {
            toast.error(err.data.message);
        }
    };

    return {
        product,
        cartProduct,
        loading: {
            addingToWishlist,
            deletingFromWishlist,
            addingToCart,
            gettingProduct,
        },
        handleProductPreview,
        handleAddToCart,
        handleCartQuantityChange,
        handleDeleteFromCart,
        handleAddToWishList,
        handleRemoveFromWishList,
    };
};

const ProductCard = (props: Props) => {
    if (window.config.defaultProductCard === 'product-card-one') {
        return <ProductCardThemeOne {...props} />;
    } else if (window.config.defaultProductCard === 'product-card-two') {
        return <ProductCardThemeTwo {...props} />;
    } else if (window.config.defaultProductCard === 'product-card-three') {
        return <ProductCardThemeThree {...props} />;
    } else if (window.config.defaultProductCard === 'product-card-four') {
        return <ProductCardThemeFour {...props} />;
    } else {
        return <ProductCardThemeOne {...props} />;
    }
};

export default ProductCard;
