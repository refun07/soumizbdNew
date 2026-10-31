import { useFacebookPixelEvents } from 'hooks/useFacebookPixelEvents';
// import useGTMEvent from 'hooks/useGTMEvent';
import useGTMEventsNew from 'hooks/useGTMEventNew';
import { useEffect, useMemo, useRef, useState } from 'react';
import toast from 'react-hot-toast';
import { FaRuler } from 'react-icons/fa6';
import { FiChevronLeft, FiChevronRight } from 'react-icons/fi';
import { LuHeart, LuShoppingCart } from 'react-icons/lu';
import { LiaTimesSolid } from 'react-icons/lia';
import { SideBySideMagnifier } from 'react-image-magnifiers';
import { useDispatch } from 'react-redux';
import { Link, useNavigate } from 'react-router-dom';
import { Thumbs } from 'swiper/modules';
import { Swiper, SwiperRef, SwiperSlide } from 'swiper/react';
import NotFound from '../../../pages/NotFound';
import {
    useAddToCartMutation,
    useAddToWishListMutation,
    useGetProductDetailsQuery,
} from '../../../store/features/api/productApi';
import { useAuth } from '../../../store/features/auth/authSlice';
import { setCheckoutData } from '../../../store/features/checkout/checkoutSlice';
import { togglePopup } from '../../../store/features/popup/popupSlice';
import { VariationCombination } from '../../../types/product';
import {
    isProductAvailable,
    isVariationValueInStock,
} from '../../../utils/checkStock';
import { cn } from '../../../utils/cn';
import { currencyFormatter } from '../../../utils/numberFormatter';
import { translate } from '../../../utils/translate';
import { createMetaEventId } from '../../../utils/gtmCustomerData';
import CountDown from '../../CountDown';
import Image from '../../Image';
import ContactPaymentOptions from '../../ContactPaymentOptions';
import Button from '../../buttons/Button';
import IconButton from '../../buttons/IconButton';
import ThemeLabel from '../../card/ThemeLabel';
import ColorCheckbox from '../../inputs/ColorCheckbox';
import Counter from '../../inputs/Counter';
import ThemeRating from '../../reviews/ThemeRating';
import {
    ProductPreviewSkeleton,
    ProductPreviewSkeletonMobile,
} from '../../skeletons/from-svg';
import YoutubeVideoModal from '../product/YouTubeVideoModal';
import StockRequestButton from './StockRequestButton';
import SizeChartModal from '../SizeChartModal';

const errorImgPath = '/images/image-error.png';

interface ISelectedVariationCombinations {
    [key: number]: VariationCombination['values'][0] | null;
}

interface Props extends React.HTMLAttributes<HTMLDivElement> {
    slug: string;
    stickyButtons?: boolean;
}

const generateVariationCode = (
    variationCombinations: ISelectedVariationCombinations,
) => {
    let variationCode = '';
    Object.values(variationCombinations).forEach((value) => {
        if (value) variationCode += value.matchVariationCode + '/';
    });
    return variationCode;
};

const isRenderableProductImage = (image?: string | null): image is string => {
    const src = image?.trim();

    return Boolean(src && !src.endsWith(errorImgPath));
};

const isSizeVariation = (variation: VariationCombination) => {
    return variation.name.toLowerCase().includes('size');
};

const ProductPreviewContent = ({
    slug,
    stickyButtons = true,
    className,
    ...rest
}: Props) => {
    const fbEvents = useFacebookPixelEvents();
    const gtmEvent = useGTMEventsNew();
    const navigate = useNavigate();
    const dispatch = useDispatch();

    const { user, isLoading, guestUserId, carts } = useAuth();
    const viewContentEventId = useMemo(
        () => createMetaEventId('view_content', slug),
        [slug],
    );

    const { data: product, isLoading: loadingProduct } =
        useGetProductDetailsQuery({
            slug,
            eventId: viewContentEventId,
        });
    const [addToWishList] = useAddToWishListMutation();
    const [addToCart, { isLoading: addingToCart }] = useAddToCartMutation();

    const previewSliderRef = useRef<SwiperRef>(null);
    const thumbSliderRef = useRef<SwiperRef>(null);
    const viewItemFiredRef = useRef(false);

    const [isBuyNowLoading, setIsBuyNowLoading] = useState(false);
    const [showVariationConfirm, setShowVariationConfirm] = useState(false);
    const [variationConfirmAction, setVariationConfirmAction] = useState<
        'add-to-cart' | 'buy-now'
    >('add-to-cart');
    const [cartCount, setCartCount] = useState(1);
    const [selectedVariationCombinations, setSelectedVariationCombinations] =
        useState<ISelectedVariationCombinations>({});
    const [thumbsSwiper, setThumbsSwiper] = useState<
        SwiperRef['swiper'] | null
    >(null);

    const [showModal, setShowModal] = useState(false);
    const [youtubeVideoUrl, setYoutubeVideoUrl] = useState('');
    const [showSizeChartModal, setShowSizeChartModal] = useState(false);
    const hasSizeChartContent = Boolean(
        product?.hasSizeChart &&
            (product?.sizeChartTable?.columns?.length ||
                product?.sizeChartImage),
    );

    const images = useMemo(() => {
        if (!product) return [];

        const variationImages = product.variations
            .map((variation) => variation.image)
            .filter(isRenderableProductImage);
        const productImages = product.images.filter(isRenderableProductImage);

        return Array.from(new Set([...productImages, ...variationImages]));
    }, [product]);
    const showThumbSlider = images.length > 1 || Boolean(product?.videoLink);
    const requiredVariationIds = useMemo(() => {
        if (!product?.variationCombinations.length) {
            return [];
        }

        const sizeVariationIds = product.variationCombinations
            .filter(isSizeVariation)
            .map((variation) => variation.id);

        if (sizeVariationIds.length) {
            return sizeVariationIds;
        }

        return product.hasSizeChart
            ? product.variationCombinations.map((variation) => variation.id)
            : [];
    }, [product]);

    // Reserve space only for the detail page's mobile purchase bar.
    useEffect(() => {
        if (!stickyButtons) return;
        const query = window.matchMedia('(max-width: 1023px)');
        const previousPadding = document.body.style.paddingBottom;
        const update = () => {
            document.body.style.paddingBottom = query.matches ? 'calc(88px + env(safe-area-inset-bottom))' : previousPadding;
        };
        update();
        query.addEventListener('change', update);
        return () => {
            query.removeEventListener('change', update);
            document.body.style.paddingBottom = previousPadding;
        };
    }, [stickyButtons]);

    // initialize variation selections without preselecting values for variant products
    useEffect(() => {
        if (!product) return;

        setSelectedVariationCombinations(() => {
            const newSelectedVariations: ISelectedVariationCombinations = {};

            product.variationCombinations.forEach((variation) => {
                const shouldRequireManualSelection = isSizeVariation(variation);

                newSelectedVariations[variation.id] =
                    shouldRequireManualSelection
                        ? null
                        : variation.values[0] || null;
            });

            // if (product) {
            //     fbEvents.viewContent({
            //         content_ids: [product.id.toString()],
            //         content_name: product.name,
            //         value: product.basePrice,
            //         currency: window.config.currency.code,
            //     });
            //     if (selectedVariation) {
            //         gtmEvent({
            //             event: 'view_item',
            //             ecommerce: {
            //                 currency: window.config.currency.code,
            //                 value:
            //                     selectedVariation!.basePrice *
            //                     (productCartQuantity?.qty || 1),
            //                 items: [
            //                     {
            //                         item_id: selectedVariation!.sku,
            //                         item_name: `${product.name}(${
            //                             selectedVariation!.name
            //                         })`,
            //                         price: selectedVariation!.basePrice,
            //                         quantity: productCartQuantity?.qty || 1,
            //                     },
            //                 ],
            //             },
            //         });
            //     }
            // }

            return newSelectedVariations;
        });
    }, [product]);

    // select variation image whenever selected variation combination changes
    useEffect(() => {
        if (!product) return;

        // generate the variation code from selected variations
        const selectedVariationCode = generateVariationCode(
            selectedVariationCombinations,
        );

        // find the selected variation using the generated variation code
        const selectedVariation = product.variations.find((variation) => {
            return variation.code === selectedVariationCode;
        });

        if (!selectedVariation?.image) return;

        const imageIndex = images.findIndex(
            (image) => image === selectedVariation?.image,
        );

        if (imageIndex !== -1) {
            previewSliderRef.current?.swiper.slideTo(imageIndex);
        }

    }, [selectedVariationCombinations, product, images]);

    useEffect(() => {
        if (!product || viewItemFiredRef.current) return;

        const variation = product.variations[0];

        if (!variation) return;

        viewItemFiredRef.current = true;

        // Build item_id based on variation logic
        const itemId = product.hasVariation
            ? `${product.id}-${variation.id}` // variant product ID
            : product.id.toString(); // simple product ID

        // Fire GTM event ONLY (no other updates)
        gtmEvent({
            event: 'view_item',
            event_id: viewContentEventId,
            ecommerce: {
                currency: window.config.currency.code,
                value: variation.basePrice,
                items: [
                    {
                        item_id: itemId,
                        item_name: product.hasVariation
                            ? `${product.name} (${variation.name})`
                            : product.name,
                        price: variation.basePrice,
                        quantity: 1,
                    },
                ],
            },
        });
    }, [product, viewContentEventId]);

    if (loadingProduct) {
        return (
            <>
                <ProductPreviewSkeleton className="mx-auto max-md:hidden" />
                <div className="p-4 md:hidden">
                    <ProductPreviewSkeletonMobile className=" w-full" />
                </div>
            </>
        );
    }

    if (!product) {
        return <NotFound />;
    }

    // generate selected variation code
    let selectedVariationCode = generateVariationCode(
        selectedVariationCombinations,
    );
    const hasVariants = Number(product.hasVariation) === 1;

    // find the variation from selected combination
    // if `hasVariation` is 0, then select 1st variation
    // else find the variation using the generated variation code
    const selectedVariation = !hasVariants
        ? product.variations[0]
        : product.variations.find((variation) => {
              return variation.code === selectedVariationCode;
          });
    const displayVariation = selectedVariation || product.variations[0];
    const hasIncompleteVariantSelection =
        hasVariants &&
        product.variationCombinations.some(
            (variation) => !selectedVariationCombinations[variation.id],
        );
    const openVariationSelector = (action: 'add-to-cart' | 'buy-now') => {
        setVariationConfirmAction(action);
        setShowVariationConfirm(true);
    };
    const getMissingVariationNames = () => {
        return product.variationCombinations
            .map((variation) =>
                selectedVariationCombinations[variation.id]
                    ? null
                    : variation.name,
            )
            .filter(Boolean);
    };
    const selectedVariationSummary = hasVariants
        ? product.variationCombinations
              .map((variation) => {
                  return selectedVariationCombinations[variation.id]?.name;
              })
              .filter(Boolean)
              .join(' / ')
        : '';

    // Determine if current selection is pre-order (variation level, fallback to product level)
    const isCurrentPreOrder =
        displayVariation?.isPreOrder ||
        (product.isPreOrder && !displayVariation?.preOrderData);

    // Get pre-order data (variation specific, fallback to product level)
    const currentPreOrderData =
        displayVariation?.preOrderData || product.preOrderData;

    // Calculate maximum addable quantity (use pre-order limits or stock)
    const totalStockQty =
        displayVariation?.stocks.reduce((sum, s) => sum + s.stockQty, 0) || 0;
    const maxPreOrderQty = displayVariation?.remainingOrders || 999;
    const productCartQuantity = carts.find(
        (cartProduct) => cartProduct.variation.id === displayVariation?.id,
    );

    const maxPurchaseQty = product.maxPurchaseQty || 999;
    const limitBasedOnStockOrPreOrder = isCurrentPreOrder
        ? maxPreOrderQty - (productCartQuantity?.qty || 0)
        : totalStockQty - (productCartQuantity?.qty || 0);
    const maximumAddableQuantity = Math.max(
        1,
        Math.min(maxPurchaseQty, limitBasedOnStockOrPreOrder),
    );

    const isSelectedVariationOutOfStock =
        displayVariation && !window.config.generalSettings.allowBackorder
            ? displayVariation.stocks.reduce((sum, s) => sum + s.stockQty, 0) <
              1
            : false;
    const shouldDisablePurchaseButtons =
        !hasIncompleteVariantSelection &&
        isSelectedVariationOutOfStock &&
        !isCurrentPreOrder &&
        !window.config.generalSettings.allowBackorder;

    const handleAddToCart = async () => {
        // check user location is set or not
        // if (!userLocation?.area?.zone_id) {
        //     dispatch(togglePopup('user-location'));
        //     return;
        // }

        try {
            let productVariationId = selectedVariation?.id;
            const eventId = createMetaEventId('add_to_cart');

            // if (selectedVariation) {
            //     fbEvents.addToCart({
            //         content_ids: [selectedVariation.sku],
            //         content_name: `${product.name}(${selectedVariation.name})`,
            //         value: selectedVariation.basePrice,
            //         currency: window.config.currency.code,
            //     });
            //     gtmEvent({
            //         event: 'add_to_cart',
            //         ecommerce: {
            //             currency: window.config.currency.code,
            //             value:
            //                 selectedVariation.basePrice *
            //                 (productCartQuantity?.qty || 1),
            //             items: [
            //                 {
            //                     item_id: selectedVariation.sku,
            //                     item_name: `${product.name}(${selectedVariation.name})`,
            //                     price: selectedVariation.basePrice,
            //                     quantity: productCartQuantity?.qty || 1,
            //                 },
            //             ],
            //         },
            //     });
            // }

            // if `hasVariation` is 0, then select 1st variation and add to cart
            // if `hasVariation` is grater then 0, then check if all variations are selected or not
            // if all variations are not selected,  then show error. Example: Please select color and size
            // else add to cart
            if (!hasVariants) {
                productVariationId = product.variations[0].id;
            }
            if (hasVariants && hasIncompleteVariantSelection) {
                openVariationSelector('add-to-cart');
                return false;
            }

            if (!productVariationId) {
                toast.error(
                    `Please select ${getMissingVariationNames().join(', ')}`,
                );
                return false;
            }

            // check if the selected variation is in stock or not
            if (
                !isProductAvailable(
                    selectedVariation!,
                    carts,
                    isCurrentPreOrder,
                )
            ) {
                return false;
            }
            await addToCart({
                productVariationId,
                qty: cartCount,
                warehouseId: selectedVariation?.stocks[0]?.warehouseId || 1,
                guestUserId,
                eventId,
            }).unwrap();

            if (selectedVariation) {
                gtmEvent({
                    event: 'add_to_cart',
                    event_id: eventId,
                    ecommerce: {
                        currency: window.config.currency.code,
                        value: selectedVariation.basePrice * cartCount,
                        items: [
                            {
                                item_id: `${product.id}-${selectedVariation.id}`,
                                item_name: `${product.name} (${selectedVariation.name})`,
                                price: selectedVariation.basePrice,
                                quantity: cartCount,
                            },
                        ],
                    },
                });
            }

            setCartCount(1);
            toast.success(translate('Product added to cart'));
            return true;
        } catch (err: any) {
            toast.error(err.data.message);
            return false;
        }
    };

    // add to cart and redirect to checkout page
    const handleBuyNow = async () => {
        // check user location is set or not
        // if (!userLocation?.area?.zone_id) {
        //     dispatch(togglePopup('user-location'));
        //     return;
        // }

        if (hasVariants && hasIncompleteVariantSelection) {
            openVariationSelector('buy-now');
            return;
        }

        // check if the selected variation is in stock or not
        if (!isProductAvailable(selectedVariation!, carts, isCurrentPreOrder)) {
            return;
        }

        setIsBuyNowLoading(true);

        const addedToCart = await handleAddToCart();

        setIsBuyNowLoading(false);
        if (!addedToCart) return;

        dispatch(
            setCheckoutData({
                selectedShops: {
                    [product.shop.name]: true,
                },
            }),
        );
        navigate('/checkout');
    };

    const handleToggleWishList = async () => {
        if (isLoading || !user) {
            dispatch(togglePopup('signin'));
            return;
        }

        try {
            const wasInWishlist = product.inWishlist;
            const eventId = wasInWishlist
                ? undefined
                : createMetaEventId('add_to_wishlist');

            await addToWishList(
                eventId ? { productId: product.id, eventId } : product.id,
            ).unwrap();
            toast.success(translate('Wishlist updated'));

            gtmEvent({
                event: wasInWishlist
                    ? 'remove_from_wishlist'
                    : 'add_to_wishlist',
                ...(eventId ? { event_id: eventId } : {}),
                ecommerce: {
                    items: [
                        {
                            item_id: product.hasVariation
                                ? `${product.id}-${displayVariation?.id}` // <-- FIXED for variants
                                : product.id.toString(), // <-- simple product
                            item_name: product.hasVariation
                                ? `${product.name} (${displayVariation?.name})`
                                : product.name,
                        },
                    ],
                },
            });
        } catch (err: any) {
            console.log(err);
            toast.error(err.data.message);
        }
    };

    const handleSelectVariation = ({
        variationId,
        variationValue,
        checked,
    }: any) => {
        if (!checked) {
            setSelectedVariationCombinations((prev) => ({
                ...prev,
                [variationId]: null,
            }));
            return;
        }

        setSelectedVariationCombinations((prev) => ({
            ...prev,
            [variationId]: variationValue,
        }));
    };

    const handlePrev = () => {
        if (!thumbSliderRef.current) return;
        thumbSliderRef.current.swiper.slidePrev();
    };

    const handleNext = () => {
        if (!thumbSliderRef.current) return;
        thumbSliderRef.current.swiper.slideNext();
    };

    const getYouTubeId = (url: string): string | null => {
        const regExp =
            /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?/\s]{11})/;
        const match = url.match(regExp);
        return match ? match[1] : null;
    };

    const getYouTubeThumbnail = (videoLink: string) => {
        const id = getYouTubeId(videoLink);
        return id ? `https://img.youtube.com/vi/${id}/hqdefault.jpg` : '';
    };

    const getYouTubeEmbedUrl = (videoLink: string) => {
        const id = getYouTubeId(videoLink);
        return id ? `https://www.youtube.com/embed/${id}` : '';
    };

    const handlePlayClick = () => {
        const embedUrl = getYouTubeEmbedUrl(product.videoLink);
        setYoutubeVideoUrl(embedUrl);
        setShowModal(true);
    };

    const handleClose = () => {
        setShowModal(false);
        setYoutubeVideoUrl('');
    };

    return (
        <div className={`sm:grid sm:grid-cols-9 ${className}`} {...rest}>
            <div className="sm:col-span-4">
                <div className="relative border border-primary-14 rounded-card aspect-square">
                    <div className="absolute top-5 left-5 flex flex-col items-start gap-2 z-[2]">
                        {isCurrentPreOrder &&
                        currentPreOrderData?.['is_active'] !== false &&
                        (currentPreOrderData as any)?.['enabled'] !== false ? (
                            <ThemeLabel
                                label={{
                                    text: translate('Pre-Order'),
                                    variant: 'no-color',
                                }}
                                bgColor={'#3b82f6'}
                                textColor={'#fff'}
                            />
                        ) : null}
                        {!isCurrentPreOrder &&
                        isSelectedVariationOutOfStock &&
                        !hasIncompleteVariantSelection &&
                        !window.config.generalSettings.allowBackorder ? (
                            <ThemeLabel
                                label={{
                                    text: translate('Out of stock'),
                                    variant: 'no-color',
                                }}
                                bgColor={'#a6a6a6'}
                                textColor={'#fff'}
                            />
                        ) : null}

                        {product.badges.map((item) => (
                            <ThemeLabel
                                label={{
                                    text: item.name,
                                    variant: 'no-color',
                                }}
                                bgColor={item.bgColor}
                                textColor={item.textColor}
                                key={item.id}
                            />
                        ))}
                    </div>

                    <Swiper
                        modules={[Thumbs]}
                        slidesPerView={1}
                        autoHeight={true}
                        updateOnWindowResize={true}
                        mousewheel={true}
                        className="swiper-main product-preview-slider"
                        ref={previewSliderRef}
                        thumbs={{
                            swiper:
                                thumbsSwiper && !thumbsSwiper.destroyed
                                    ? thumbsSwiper
                                    : null,
                        }}
                    >
                        {images.map((image, index) => (
                            <SwiperSlide key={index}>
                                <SideBySideMagnifier
                                    imageSrc={image}
                                    className="[&_img]:max-w-none"
                                    alwaysInPlace={true}
                                />
                            </SwiperSlide>
                        ))}
                    </Swiper>
                </div>

                {showThumbSlider ? (
                    <div className="relative mt-2.5">
                        <button
                            className="absolute top-1/2 -translate-y-1/2 left-0 z-[1] inline-flex items-center justify-center text-xl text-secondary rounded-full border border-secondary h-[38px] aspect-square hover:text-white hover:bg-primary hover:border-primary"
                            onClick={handlePrev}
                        >
                            <FiChevronLeft />
                        </button>

                        <div className="md:px-14">
                            <Swiper
                                modules={[Thumbs]}
                                className="product-details__thumbs"
                                spaceBetween={10}
                                slidesPerView={3}
                                ref={thumbSliderRef}
                                watchSlidesProgress={true}
                                onSwiper={setThumbsSwiper}
                            >
                                {images.map((image, index) => (
                                    <SwiperSlide key={index}>
                                        <div className="border border-zinc-100 rounded-card overflow-hidden">
                                            <Image
                                                src={image}
                                                alt=""
                                                // className="max-xs:w-[50px] max-sm:max-w-[100px]"
                                            />
                                        </div>
                                    </SwiperSlide>
                                ))}
                                {/* Video Thumbnail */}
                                {product.videoLink && (
                                    <SwiperSlide key={`video`}>
                                        <div
                                            className="relative border border-zinc-100 rounded-card overflow-hidden aspect-square cursor-pointer group"
                                            onClick={handlePlayClick}
                                        >
                                            {/* YouTube Thumbnail */}
                                            <img
                                                src={getYouTubeThumbnail(
                                                    product.videoLink,
                                                )}
                                                alt="Video Thumbnail"
                                                className="w-full h-full object-cover"
                                            />

                                            {/* Play Button Overlay */}
                                            <div className="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                <div className="bg-red-600 rounded-full p-2 hover:bg-red-700 transition-colors duration-200">
                                                    <svg
                                                        className="w-6 h-6 text-white ml-0.5"
                                                        fill="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >
                                                        <path d="M8 5v14l11-7z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </SwiperSlide>
                                )}
                            </Swiper>
                        </div>
                        <div className="w-64">
                            <YoutubeVideoModal
                                showModal={showModal}
                                videoUrl={youtubeVideoUrl}
                                onClose={handleClose}
                            />
                        </div>

                        <button
                            className="absolute top-1/2 -translate-y-1/2 right-0 z-[1] inline-flex items-center justify-center text-xl text-secondary rounded-full border border-secondary h-[38px] aspect-square hover:text-white hover:bg-primary hover:border-primary"
                            onClick={handleNext}
                        >
                            <FiChevronRight />
                        </button>
                    </div>
                ) : null}
            </div>
            <div className="sm:col-span-5 md:px-[55px]">
                <a href={`#customer-review`}>
                    <ThemeRating
                        readOnly
                        rating={product.rating.average}
                        totalReviews={product.rating.total}
                        className="mb-2.5 text-sm"
                    />
                </a>
                <h1 className="text-[26px] font-medium">{product.name}</h1>
                {product.unit ? (
                    <p className="mt-1 text-sm font-bold text-black/60">
                        {product.unit}
                    </p>
                ) : null}
                <hr className="mt-2.5 mb-6" />
                {hasVariants &&
                    product.variationCombinations.map((variation) => {
                        const selectedValue =
                            selectedVariationCombinations[variation.id];
                        const isColorVariation = variation.isColor;

                        return (
                            <div
                                className="flex items-center gap-2 mb-6"
                                key={variation.id}
                            >
                                <span className="min-w-[73px] uppercase font-medium">
                                    {variation.name}
                                </span>
                                <div className="flex flex-col gap-2">
                                    <div className="flex flex-wrap gap-1">
                                        {variation.values.map((item) => {
                                            const itemSelected =
                                                selectedVariationCombinations[
                                                    variation.id
                                                ]?.id === item.id;

                                            const isAvailable =
                                                window.config.generalSettings
                                                    .allowBackorder ||
                                                isVariationValueInStock(
                                                    item,
                                                    selectedVariationCombinations,
                                                    product.variations,
                                                );

                                            const onChange = () =>
                                                handleSelectVariation({
                                                    variationId: variation.id,
                                                    variationValue: item,
                                                    checked: !itemSelected,
                                                });

                                            if (isColorVariation) {
                                                // it's color variation
                                                return (
                                                    <div
                                                        key={item.id}
                                                        className={cn(
                                                            !isAvailable &&
                                                                'opacity-50 cursor-not-allowed',
                                                        )}
                                                        title={
                                                            !isAvailable
                                                                ? 'Out of stock'
                                                                : undefined
                                                        }
                                                    >
                                                        <ColorCheckbox
                                                            name="variationValueIds"
                                                            value={item.id}
                                                            code={
                                                                item.colorCode!
                                                            }
                                                            colorImage={
                                                                product?.color_image_enabled
                                                                    ? item.colorImage
                                                                    : null
                                                            }
                                                            checked={
                                                                itemSelected
                                                            }
                                                            onChange={
                                                                isAvailable
                                                                    ? onChange
                                                                    : undefined
                                                            }
                                                            disabled={
                                                                !isAvailable
                                                            }
                                                        />
                                                    </div>
                                                );
                                            }

                                            return (
                                                <button
                                                    className={cn(
                                                        'h-9 min-w-9 px-2 rounded inline-flex items-center justify-center text-sm text-black/70 border',
                                                        {
                                                            'border-primary bg-primary text-white':
                                                                itemSelected,
                                                            'border-zinc-100':
                                                                !itemSelected,
                                                            'opacity-50 line-through cursor-not-allowed pointer-events-none':
                                                                !isAvailable,
                                                        },
                                                    )}
                                                    onClick={
                                                        isAvailable
                                                            ? onChange
                                                            : undefined
                                                    }
                                                    key={item.id}
                                                    disabled={!isAvailable}
                                                    title={
                                                        !isAvailable
                                                            ? 'Out of stock'
                                                            : undefined
                                                    }
                                                >
                                                    {item.name}
                                                </button>
                                            );
                                        })}
                                    </div>
                                    {isColorVariation && selectedValue && (
                                        <span className="text-sm text-gray-600">
                                            {translate('Selected')}:{' '}
                                            <strong>
                                                {selectedValue.name}
                                            </strong>
                                        </span>
                                    )}
                                </div>
                            </div>
                        );
                    })}
                {hasSizeChartContent && (
                    <button
                        onClick={() => setShowSizeChartModal(true)}
                        className="inline-flex items-center gap-2 mb-6 text-secondary hover:text-primary transition-colors duration-200 group"
                    >
                        <FaRuler className="w-4 h-4 group-hover:scale-110 transition-transform" />
                        <span className="font-medium underline underline-offset-2">
                            {translate('Size Chart')}
                        </span>
                    </button>
                )}
                {isSelectedVariationOutOfStock &&
                    !isCurrentPreOrder &&
                    !hasIncompleteVariantSelection &&
                    !window.config.generalSettings.allowBackorder && (
                        <div className="flex items-center gap-3 mb-4">
                            <p className="text-red-500 text-sm mt-[-4px]">
                                This variation is out of stock
                            </p>
                            <StockRequestButton
                                product={product}
                                selectedVariation={selectedVariation}
                            />
                        </div>
                    )}
                <div className="flex items-center gap-2 mb-6">
                    <span className="min-w-[73px] font-medium uppercase">
                        {translate('Quantity')}
                    </span>
                    <Counter
                        value={cartCount}
                        min={1}
                        max={maximumAddableQuantity}
                        orientation="horizontal"
                        onChange={(value) => setCartCount(value)}
                        onMaxReached={() =>
                            toast.error(translate('Maximum quantity reached'))
                        }
                    />
                </div>
                <hr className="mt-7 mb-6" />
                <div className="font-semibold flex gap-3.5 items-center">
                    {displayVariation?.basePrice !==
                    displayVariation?.discountedBasePrice ? (
                        <>
                            <span className="text-black/70 line-through text-lg">
                                {currencyFormatter(
                                    displayVariation?.basePrice,
                                )}
                            </span>
                            <span className="text-primary text-2xl">
                                {currencyFormatter(
                                    displayVariation?.discountedBasePrice,
                                )}
                            </span>
                        </>
                    ) : (
                        <span className="text-primary text-2xl">
                            {currencyFormatter(
                                displayVariation?.basePrice ||
                                    product.basePrice,
                            )}
                        </span>
                    )}
                </div>
                <CountDown
                    date={Number(displayVariation?.dealEnds) * 1000}
                    className="mt-3"
                />
                <hr className="mt-7 mb-6" />
                {product.brand ? (
                    <div className="flex items-center gap-2 mb-4">
                        <span className="min-w-[73px] uppercase font-medium">
                            {translate('Brand')}
                        </span>
                        <Link
                            to={`/brands/${product.brand.slug}`}
                            className="flex items-center gap-1.5"
                        >
                            <Image
                                src={product.brand.thumbnailImage}
                                alt={product.brand.name}
                                className="w-8 h-8 rounded-full"
                            />
                            <span>{product.brand.name}</span>
                        </Link>
                        {product.collection && (
                            <Link
                                to={`/brands/${product.brand.slug}?collection=${product.collection.slug}`}
                                title={translate('View all products in this collection')}
                                className="rounded-full bg-primary/10 px-2.5 py-0.5 text-xs text-primary transition hover:bg-primary hover:text-white"
                            >
                                {product.collection.name}
                            </Link>
                        )}
                    </div>
                ) : null}

                {product.tags.length > 0 && (
                    <div className="flex flex-wrap items-center gap-2 mb-4">
                        <span className="min-w-[73px] uppercase font-medium">{translate('Tags')}</span>
                        <div className="flex flex-wrap gap-1.5 text-black/70">
                            {product.tags.map((item, index) => (
                                <Link key={index} to={`/product?tag=${encodeURIComponent(item.name)}`}>#{item.name}</Link>
                            ))}
                        </div>
                    </div>
                )}
                {isCurrentPreOrder &&
                currentPreOrderData?.['is_active'] !== false &&
                (currentPreOrderData as any)?.['enabled'] !== false ? (
                    <div className="bg-blue-50 border border-blue-100 rounded-lg p-4 mb-4">
                        <div className="flex items-center gap-2 mb-2">
                            <span className="min-w-[73px] uppercase font-medium text-blue-800">
                                {translate('Pre-Order')}
                            </span>
                        </div>
                        {currentPreOrderData?.expected_release_date ? (
                            <p className="text-sm text-blue-700 mb-2">
                                <span className="font-medium">
                                    {translate('Expected Release Date')}:{' '}
                                </span>
                                {currentPreOrderData.expected_release_date}
                            </p>
                        ) : null}
                        {currentPreOrderData?.description ? (
                            <p className="text-sm text-blue-600">
                                {currentPreOrderData.description}
                            </p>
                        ) : null}
                    </div>
                ) : null}
                <hr
                    className={cn('mt-6 mb-6', {
                        'max-lg:hidden': stickyButtons,
                    })}
                />
                <div
                    className={cn('bot-purchase-actions', { 'bot-purchase-actions--sticky': stickyButtons })}
                >
                    <Button
                        size="lg"
                        onClick={handleAddToCart}
                        className="bot-purchase-button bot-purchase-button--primary"
                        isLoading={addingToCart && !isBuyNowLoading}
                        arrow={false}
                        disabled={shouldDisablePurchaseButtons}
                    >
                        <span className="inline-flex items-center justify-center gap-2">
                            <LuShoppingCart aria-hidden="true" />
                            <span>{translate('Add To Cart')}</span>
                        </span>
                    </Button>
                    <Button
                        size="lg"
                        onClick={handleBuyNow}
                        className="bot-purchase-button bot-purchase-button--secondary"
                        variant="outline"
                        isLoading={isBuyNowLoading}
                        arrow={false}
                        disabled={shouldDisablePurchaseButtons}
                    >
                        {translate(isCurrentPreOrder ? 'PRE-ORDER' : 'Buy Now')}
                    </Button>
                    <IconButton
                        className={cn('bot-purchase-wishlist', { 'is-saved': product.inWishlist })}
                        aria-label={product.inWishlist ? 'Remove from wishlist' : 'Save to wishlist'}
                        aria-pressed={product.inWishlist}
                        onClick={handleToggleWishList}
                    >
                        <LuHeart aria-hidden="true" />
                    </IconButton>
                </div>

                <ContactPaymentOptions />
            </div>

            {/* Size Chart Modal */}
            <SizeChartModal
                showModal={showSizeChartModal}
                imageUrl={product.sizeChartImage}
                sizeChartTable={product.sizeChartTable}
                onClose={() => setShowSizeChartModal(false)}
            />
            {showVariationConfirm ? (
                <div className="fixed inset-0 z-[60] flex items-center justify-center bg-black/60 px-4">
                    <div className="w-full max-w-[680px] overflow-hidden rounded-card bg-white shadow-2xl">
                        <div className="flex items-start justify-between border-b border-gray-200 bg-stone-50 px-6 py-5">
                            <div>
                                <h4 className="text-xl font-semibold text-black">
                                    {translate('Select variant')}
                                </h4>
                                <p className="mt-1 text-sm text-black/60">
                                    {translate('Choose an option to continue.')}
                                </p>
                            </div>
                            <button
                                className="inline-flex size-9 items-center justify-center rounded-full bg-white text-lg text-black/70 shadow-sm transition hover:bg-primary hover:text-white"
                                onClick={() => setShowVariationConfirm(false)}
                            >
                                <LiaTimesSolid />
                            </button>
                        </div>
                        <div className="px-6 pb-7 pt-5">
                            <div className="rounded-[6px] border border-primary/15 bg-primary/5 px-5 py-4">
                                <p className="line-clamp-2 text-base font-semibold text-black">
                                    {product.name}
                                </p>
                                <div className="mt-3 flex flex-wrap items-center justify-between gap-3">
                                    <p className="text-sm font-medium uppercase text-black/55">
                                        {selectedVariationSummary ||
                                            displayVariation?.name ||
                                            translate('Select an option')}
                                    </p>
                                    <div className="flex items-center gap-2 font-semibold">
                                        {displayVariation?.basePrice !==
                                        displayVariation?.discountedBasePrice ? (
                                            <>
                                                <span className="text-sm text-black/50 line-through">
                                                    {currencyFormatter(
                                                        displayVariation?.basePrice,
                                                    )}
                                                </span>
                                                <span className="text-2xl text-primary">
                                                    {currencyFormatter(
                                                        displayVariation?.discountedBasePrice,
                                                    )}
                                                </span>
                                            </>
                                        ) : (
                                            <span className="text-2xl text-primary">
                                                {currencyFormatter(
                                                    displayVariation?.basePrice ||
                                                        product.basePrice,
                                                )}
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>

                            <div className="mt-5 max-h-[55vh] space-y-5 overflow-y-auto pr-1">
                                {product.variationCombinations.map(
                                    (variation) => (
                                        <div key={variation.id}>
                                            <p className="mb-3 text-sm font-semibold uppercase text-black/70">
                                                {variation.name}
                                            </p>
                                            <div className="grid grid-cols-2 gap-2.5 sm:grid-cols-3">
                                                {variation.values.map((item) => {
                                                    const itemSelected =
                                                        selectedVariationCombinations[
                                                            variation.id
                                                        ]?.id === item.id;
                                                    const isAvailable =
                                                        window.config
                                                            .generalSettings
                                                            .allowBackorder ||
                                                        isVariationValueInStock(
                                                            item,
                                                            selectedVariationCombinations,
                                                            product.variations,
                                                        );

                                                    return (
                                                        <button
                                                            type="button"
                                                            key={item.id}
                                                            className={cn(
                                                                'min-h-[54px] rounded-[6px] border px-4 py-3 text-sm font-semibold transition shadow-sm',
                                                                {
                                                                    'border-primary bg-primary text-white shadow-primary/20':
                                                                        itemSelected,
                                                                    'border-zinc-100 bg-white text-black/75 hover:border-primary/60 hover:text-primary':
                                                                        !itemSelected,
                                                                    'cursor-not-allowed opacity-45 line-through':
                                                                        !isAvailable,
                                                                },
                                                            )}
                                                            onClick={() => {
                                                                if (!isAvailable) {
                                                                    return;
                                                                }

                                                                handleSelectVariation(
                                                                    {
                                                                        variationId:
                                                                            variation.id,
                                                                        variationValue:
                                                                            item,
                                                                        checked:
                                                                            true,
                                                                    },
                                                                );
                                                            }}
                                                            disabled={!isAvailable}
                                                        >
                                                            <span className="inline-flex w-full items-center justify-center gap-2">
                                                                {variation.isColor &&
                                                                item.colorCode ? (
                                                                    <span
                                                                        className="size-5 shrink-0 rounded-full border border-black/10"
                                                                        style={{
                                                                            backgroundColor:
                                                                                item.colorCode,
                                                                        }}
                                                                    />
                                                                ) : null}
                                                                {item.name}
                                                            </span>
                                                        </button>
                                                    );
                                                })}
                                            </div>
                                        </div>
                                    ),
                                )}
                            </div>
                            <div className="mt-7 flex justify-end border-t border-gray-100 pt-5">
                                <Button
                                    size="lg"
                                    arrow={false}
                                    className="min-w-[220px] shadow-lg shadow-primary/15"
                                    onClick={async () => {
                                        if (
                                            hasIncompleteVariantSelection ||
                                            !selectedVariation
                                        ) {
                                            toast.error(
                                                `Please select ${getMissingVariationNames().join(', ')}`,
                                            );
                                            return;
                                        }

                                        setShowVariationConfirm(false);

                                        if (
                                            variationConfirmAction === 'buy-now'
                                        ) {
                                            await handleBuyNow();
                                            return;
                                        }

                                        await handleAddToCart();
                                    }}
                                    disabled={!selectedVariation}
                                >
                                    {translate('Confirm')}
                                </Button>
                            </div>
                        </div>
                    </div>
                </div>
            ) : null}
        </div>
    );
};

export default ProductPreviewContent;
