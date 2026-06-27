import { useCheckoutAutoSave } from 'hooks/useCheckoutAutoSave';
import { useFacebookPixelEvents } from 'hooks/useFacebookPixelEvents';
import { useEffect, useMemo, useRef, useState } from 'react';
import toast from 'react-hot-toast';
import { FaHome, FaTrash } from 'react-icons/fa';
import { useDispatch } from 'react-redux';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { object, string } from 'yup';
import { sendOTP } from '../../../backend/react/utils/actions';
import Breadcrumb from '../../components/Breadcrumb';
import Button from '../../components/buttons/Button';
import Image from '../../components/Image';
import Input from '../../components/inputs/Input';
import InputGroup from '../../components/inputs/InputGroup';
import Label from '../../components/inputs/Label';
import CustomerDetailsForm from '../../components/order/CustomerDetailsForm';
import PaymentMethodForm from '../../components/order/PaymentMethodForm';
import SimpleAddressForm from '../../components/order/SimpleAddressForm';
import {
    useCreateManualOrderMutation,
    useCreateOrderMutation,
} from '../../store/features/api/orderApi';
import {
    useLazyGetCartProductsQuery,
    useDeleteCartProductMutation,
    useUpdateCartProductMutation,
} from '../../store/features/api/productApi';
import { useAuth } from '../../store/features/auth/authSlice';
import { useLazyGetCheckoutSessionQuery } from '../../store/features/checkout/checkoutApi';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { IManualOrderPayload } from '../../types/checkout';
import { ICartProduct } from '../../types/product';
import { getSelectedShopProducts } from '../../utils/getSelectedShopProducts';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';
import useGTMEventsNew from 'hooks/useGTMEventNew';

export const customerDetailsSchema = object().shape({
    name: string().required('Name is required'),
    email: string().email('Invalid email'),
    phone: string().required('Phone is required'),
    note: string().nullable().default(''),
});

export type TStep = 'details' | 'shipping' | 'payment';
const steps: TStep[] = ['details'];
const stepQueryKey = 'checkoutStep';

const Checkout = () => {
    const [searchParams, setSearchParams] = useSearchParams();
    const dispatch = useDispatch();
    const fbEvents = useFacebookPixelEvents();
    const navigate = useNavigate();
    const [landingPageCart, setLandingPageCart] = useState<any[]>([]);
    const pixelFired = useRef(false);

    const gtmEvent = useGTMEventsNew();

    // Check URL parameters on load
    useEffect(() => {
        // Check if from landing page
        const from = searchParams.get('from');
        if (from === 'landing') {
            // Parse and set selected shops
            const selectedShopsParam = searchParams.get('selected_shops');
            if (selectedShopsParam) {
                try {
                    const shops = JSON.parse(
                        decodeURIComponent(selectedShopsParam),
                    );
                    dispatch(setCheckoutData({ selectedShops: shops }));
                } catch (err) {
                    console.error('Error parsing selected_shops:', err);
                }
            }

            // Parse and set cart items (for display purposes)
            const cartItemsParam = searchParams.get('cart_items');
            if (cartItemsParam) {
                try {
                    const cartItems = JSON.parse(
                        decodeURIComponent(cartItemsParam),
                    );
                    setLandingPageCart(cartItems);
                } catch (err) {
                    console.error('Error parsing cart_items:', err);
                }
            }
        }
    }, [dispatch]);
    const [createOrder, { isLoading: creatingOrder }] =
        useCreateOrderMutation();
    const [createManualOrder, { isLoading: creatingMnualOrder }] =
        useCreateManualOrderMutation();
    const [getCartProducts] = useLazyGetCartProductsQuery();
    const [updateCartProduct] = useUpdateCartProductMutation();
    const [deleteCartProduct] = useDeleteCartProductMutation();
    const { user, carts, isLoading, guestUserId } = useAuth();
    const isSubmittingOrder = useRef(false);
    const {
        customerDetails,
        shippingAddress,
        billingAddress,
        paymentMethod,
        coupons,
        selectedShops,
        shippingCharge,
        shippingZoneId,
        isVerified,
    } = useCheckoutData();

    // Real-time auto-save checkout data to database
    useCheckoutAutoSave({
        userId: user?.id,
        guestUserId,
        carts,
        enabled: !isLoading && carts.length > 0,
    });

    // Load saved checkout session on mount (for returning users)
    const [getCheckoutSession] = useLazyGetCheckoutSessionQuery();

    useEffect(() => {
        const loadSavedSession = async () => {
            if (isLoading) return;

            try {
                const session = await getCheckoutSession({
                    guestUserId: guestUserId || undefined,
                }).unwrap();

                if (session && !session.isConverted) {
                    // Check if we should restore the session
                    // Only restore if current form is empty
                    const hasCurrentData =
                        customerDetails?.name ||
                        customerDetails?.phone ||
                        shippingAddress?.address;

                    if (!hasCurrentData && session.customerDetails) {
                        dispatch(
                            setCheckoutData({
                                customerDetails: {
                                    ...session.customerDetails,
                                    email: '',
                                },
                            }),
                        );
                    }

                    if (!shippingAddress?.address && session.shippingAddress) {
                        dispatch(
                            setCheckoutData({
                                shippingAddress: session.shippingAddress,
                                billingAddress: session.shippingAddress,
                            }),
                        );
                    }

                    if (!paymentMethod && session.paymentMethod) {
                        dispatch(
                            setCheckoutData({
                                paymentMethod: session.paymentMethod,
                            }),
                        );
                    }
                }
            } catch (error) {
                // Silently fail if session load fails
                console.debug('Failed to load checkout session:', error);
            }
        };

        loadSavedSession();
    }, [isLoading, guestUserId]);

    // Use landing page cart if from landing page, otherwise use user's cart
    const isFromLanding = searchParams.get('from') === 'landing';

    // Build currentCarts from URL params if from landing, otherwise use user's cart
    const currentCarts = useMemo(() => {
        if (isFromLanding) {
            const cartItemsParam = searchParams.get('cart_items');
            if (cartItemsParam) {
                try {
                    const cartItems = JSON.parse(
                        decodeURIComponent(cartItemsParam),
                    );
                    // Transform to expected structure
                    return cartItems.map((item: any) => {
                        const basePrice =
                            item.product.base_price ?? item.product.price;
                        const discountedPrice = item.product.price;
                        return {
                            id: item.product_id,
                            productVariationId: item.variation_id,
                            qty: item.quantity,
                            product: {
                                id: item.product.id,
                                name: item.product.name,
                                price: item.product.price,
                                image: item.product.image,
                                slug: item.product.name
                                    .toLowerCase()
                                    .replace(/\s+/g, '-'),
                                thumbnailImg: item.product.image,
                            },
                            variation: {
                                id: item.variation_id,
                                basePrice: basePrice,
                                discountedBasePrice: discountedPrice,
                                tax: 0,
                                image: item.product.image,
                            },
                            shop: item.shop,
                        };
                    });
                } catch (err) {
                    console.error('Error parsing cart_items:', err);
                    return [];
                }
            } else {
                return [];
            }
        } else {
            return carts;
        }
    }, [isFromLanding, searchParams, carts]);

    const currentStep = Number(searchParams.get(stepQueryKey) || 1);

    const [subTotal, setSubTotal] = useState(0);
    const [tax, setTax] = useState(0);
    const [discount, setDiscount] = useState(0);
    const [total, setTotal] = useState(0);
    const [getPassword, setGetPassword] = useState(false);
    const [password, setPassword] = useState<string>('');
    // cart products grouped by shop
    const [selectedShopProducts, setSelectedShopsProducts] = useState<
        Record<string, ICartProduct[]>
    >({});
    const checkIfNotProduction = () => {
        return window.config.generalSettings.env == 'local' ? true : false;
    };
    const [showFullForm, setShowFullForm] = useState(() => {
        return window.config.generalSettings.env == 'local'
            ? checkIfNotProduction
            : isVerified;
    });

    // Auto-select all shops when skipCart is enabled
    useEffect(() => {
        if (
            window.config.generalSettings.skipCart &&
            carts.length > 0 &&
            !isFromLanding
        ) {
            const allShopsSelected = carts.reduce((acc, cart) => {
                acc[cart.shop.name] = true;
                return acc;
            }, {} as Record<string, boolean>);

            dispatch(setCheckoutData({ selectedShops: allShopsSelected }));
        }
    }, [carts, isLoading, isFromLanding]);

    // Handle empty cart scenario - redirect to products page
    useEffect(() => {
        if (!isLoading && carts.length === 0 && !isFromLanding) {
            // Clear checkout data when cart is empty
            dispatch(
                setCheckoutData({
                    selectedShops: {},
                    shippingCharge: 0,
                    coupons: [],
                }),
            );
            navigate('/products');
            toast(translate('Your cart is empty'), { icon: '🛒' });
        }
    }, [carts, isLoading, isFromLanding]);

    // Cart management handlers for skipCart mode
    const handleCartQuantityChange = async (
        cartProduct: ICartProduct,
        action: 'increase' | 'decrease',
    ) => {
        // check if the selected variation is available in stock (skip if backorder enabled)
        if (
            action === 'increase' &&
            !window.config.generalSettings.allowBackorder &&
            cartProduct.qty >= cartProduct.product.stockQty
        ) {
            toast.error(translate('Out of stock'));
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

    const handleRemoveFromCart = async (cartProduct: ICartProduct) => {
        try {
            await deleteCartProduct({
                id: cartProduct.id,
                guestUserId,
                zoneId: 1,
            }).unwrap();
            toast.success(translate('Product removed from cart!'));
        } catch (error) {
            toast.error(translate('Something went wrong!'));
        }
    };

    useEffect(() => {
        // check if checkout data is already available in the state
        if (
            customerDetails.name ||
            customerDetails.email ||
            customerDetails.phone
        ) {
            return;
        }

        // get user data and set it to checkout details state
        dispatch(
            setCheckoutData({
                customerDetails: {
                    name: user?.name || '',
                    email: '',
                    phone: user?.phone || '',
                    note: '',
                },
            }),
        );
    }, [user]);

    useEffect(() => {
        if (!checkIfNotProduction()) {
            setShowFullForm(isVerified);
            dispatch(setCheckoutData({ isVerified: true }));
        }
    }, [isVerified]);
    useEffect(() => {
        // Build selected shop products for calculation
        const calculatedSelectedShopProducts: Record<string, ICartProduct[]> =
            {};

        Object.keys(selectedShops).forEach((shopName) => {
            if (!selectedShops[shopName]) return;

            const products = currentCarts.filter(
                (cart) => cart.shop.name === shopName,
            );
            calculatedSelectedShopProducts[shopName] = products;
        });

        // calculate total, subtotal, tax, discount from selected shop products
        // eslint-disable-next-line react-hooks/exhaustive-deps
        const { total, subTotal, tax, totalDiscount } = getSelectedShopProducts(
            {
                cartProducts: calculatedSelectedShopProducts,
                coupons,
                selectedShops,
            },
        );

        setSubTotal(subTotal);
        setTax(tax);
        setDiscount(totalDiscount);
        setTotal(total + shippingCharge);
    }, [selectedShops, coupons, shippingCharge, currentCarts]);

    useEffect(() => {
        const selectedShopProducts: Record<string, ICartProduct[]> = {};

        Object.keys(selectedShops).forEach((shopName) => {
            if (!selectedShops[shopName]) return;

            const products = currentCarts.filter(
                (cart) => cart.shop.name === shopName,
            );
            selectedShopProducts[shopName] = products;
        });

        setSelectedShopsProducts(selectedShopProducts);
    }, [selectedShops, shippingCharge, currentCarts]);

    useEffect(() => {
        if (isLoading) return;
        if (isSubmittingOrder.current) return;
        // Skip this redirect if skipCart is enabled - let the empty cart handler manage it
        if (window.config.generalSettings.skipCart) return;
        if (!isFromLanding && !Object.values(selectedShops).includes(true)) {
            navigate('/cart');
            toast('Please select at least one shop', {
                icon: '🛒',
            });
            return;
        }
    }, [selectedShops, isFromLanding, isLoading]);

    useEffect(() => {
        if (
            pixelFired.current ||
            Object.keys(selectedShopProducts).length === 0
        ) {
            return;
        }

        let contentIds: string[] = [];
        let contents: string[] = [];
        let currency = window.config.currency.code;
        let num_items = 0;
        currentCarts.map((item) => {
            contentIds = [...contentIds, item.productVariationId.toString()];
            contents = [...contents, item.product.name];
            num_items += item.qty;
        });

        const { total } = getSelectedShopProducts({
            cartProducts: selectedShopProducts,
            coupons,
            selectedShops,
        });

        if (isNaN(total)) {
            return;
        }

        pixelFired.current = true;
        fbEvents.initiateCheckout({
            value: total,
            currency: currency,
            content_ids: contentIds,
            contents: contents,
            num_items: num_items,
        });
    }, [currentCarts]);

    const handleNext = async () => {
        try {
            await customerDetailsSchema.validate(customerDetails, {
                abortEarly: false,
            });
        } catch (err: any) {
            toast.error(err.errors[0]);
            return;
        }

        if (currentStep < steps.length) {
            searchParams.set(stepQueryKey, String(currentStep + 1));
            setSearchParams(searchParams);
        } else {
            if (!paymentMethod) {
                toast.error('Please select a payment method');
                return;
            }
            handleSubmitOrder();
        }
    };

    const sendOTPVerification = async () => {
        try {
            await customerDetailsSchema.validate(customerDetails, {
                abortEarly: false,
            });

            await sendOTP({
                name: customerDetails.name,
                phone: customerDetails.phone,
            });
            const expiryTime = Date.now() + 15 * 60 * 1000; // 15 minutes from now

            dispatch(
                setCheckoutData({
                    otpExpiryTimestamp: expiryTime,
                }),
            );
            toast.success('OTP sent to your phone');
            dispatch(togglePopup('otp-verification'));
        } catch (err: any) {
            toast.error(err.errors?.[0] || 'Failed to send OTP');
        }
    };
    const handlePrev = () => {
        if (currentStep > 1) {
            searchParams.set(stepQueryKey, String(currentStep - 1));
            setSearchParams(searchParams);
        }
    };

    const handleSubmitOrder = async () => {
        const selectedShopIds = Object.values(selectedShopProducts)
            .map((products) => products[0].shop.id)
            .filter((id, i, arr) => arr.indexOf(id) === i);

        const selectedCartIds = Object.values(selectedShopProducts)
            .map((products) => products.map((product) => product.id))
            .flat(1);

        if (!shippingAddress?.address) {
            toast.error('Please enter your full delivery address');
            return;
        }

        if (!shippingZoneId) {
            toast.error('Please select a delivery area');
            return;
        }

        if (shippingAddress && shippingZoneId && shippingAddress.address) {
            const eventId = `purchase_${Date.now()}_${Math.random()
                .toString(36)
                .slice(2)}`;

            const payload: IManualOrderPayload = {
                ...customerDetails,
                email: '',
                shippingAddress: shippingAddress,
                shippingZoneId,
                billingAddress: billingAddress,
                shopIds: selectedShopIds,
                couponCodes: coupons.map((coupon) => coupon.code),
                cartIds: selectedCartIds,
                paymentMethod,
                password,
                eventId: eventId,
            };

            try {
                isSubmittingOrder.current = true;
                const orderGroup = await createManualOrder(payload).unwrap();

                if (orderGroup.goToPayment) {
                    navigate('/payment', {
                        state: orderGroup.orderCode,
                    });
                    return;
                }

                const purchasedItems = carts.filter((cart) =>
                    selectedCartIds.includes(cart.id),
                );

                const items = purchasedItems.map((item) => ({
                    item_id: `${item.product.id}-${item.variation.id}`,
                    item_name: `${item.product.name} (${item.variation.name})`,
                    price: item.price ?? item.product.basePrice,
                    quantity: item.qty,
                }));

                const totalValue = Math.round(
                    purchasedItems.reduce(
                        (sum, p) =>
                            sum + (p.price ?? p.product.basePrice) * p.qty,
                        0,
                    ),
                );

                gtmEvent({
                    event: 'purchase',
                    event_id: eventId,
                    ecommerce: {
                        transaction_id: orderGroup.orderCode.toString(),
                        currency: window.config.currency.code,
                        value: totalValue,
                        items,
                    },
                });

                navigate(`/orders/success/${orderGroup.orderCode}`);
                getCartProducts();
            } catch (err: any) {
                isSubmittingOrder.current = false;

                toast.error(err.data.message);
                navigate(`/orders/failed`);
            }
        }
    };

    //  FIRE FACEBOOK INITIATE CHECKOUT EVENT (PIXEL ONLY)
    useEffect(() => {
        if (!Object.keys(selectedShopProducts).length) return;

        let content_ids: string[] = [];
        let contents: any[] = [];
        let num_items = 0;
        let currency = window.config.currency.code;

        carts.forEach((item) => {
            content_ids.push(item.product.id.toString());

            contents.push({
                id: item.product.id.toString(),
                quantity: item.qty,
                item_price: item.price ?? item.product.basePrice,
                item_name: item.product.name ?? '',
            });

            num_items += item.qty;
        });

        const { total } = getSelectedShopProducts({
            cartProducts: selectedShopProducts,
            coupons,
            selectedShops,
        });

        gtmEvent({
            event: 'begin_checkout',
            ecommerce: {
                currency: currency,
                value: total,
                items: contents.map((item: any) => ({
                    item_id: item.id.toString(), // MUST match catalog ID
                    item_name: item.item_name || '',
                    price: item.item_price || item.price,
                    quantity: item.quantity,
                })),
            },
        });
    }, [selectedShopProducts]);

    return (
        <>
            <Breadcrumb
                title={translate('Checkout')}
                navigation={[
                    { icon: <FaHome />, name: translate('Home'), link: '/' },
                    { name: translate('Cart'), link: '/cart' },
                    { name: translate('Checkout') },
                ]}
            />

            <div className="container no-style grid lg:grid-cols-5 mb-12">
                <div className="lg:col-span-3">
                    <div className="bg-white rounded-[4px] px-3 pb-3 sm:px-5 sm:pb-5 md:px-7 md:pb-7">
                        <CustomerDetailsForm />

                        {showFullForm && (
                            <>
                                <div className="rounded-[4px] border border-slate-200 mt-4 overflow-hidden">
                                    <h3 className="py-3 px-4 sm:px-5 bg-slate-100 text-sm font-bold text-slate-800 uppercase">
                                        SHIPPING ADDRESS
                                    </h3>
                                    <div className="px-4 pt-4 sm:px-5">
                                        <SimpleAddressForm />
                                    </div>
                                </div>
                                {/* <div className="-mt-6">
                                    <SelectAddress />
                                </div> */}
                                <div className="mt-3">
                                    <PaymentMethodForm />
                                </div>

                                {getPassword && (
                                    <div className="mt-4">
                                        <InputGroup>
                                            <Label>
                                                {translate('Set Password')}
                                            </Label>
                                            <Input
                                                name="password"
                                                type="password"
                                                placeholder={translate(
                                                    'Type Your Full Name',
                                                )}
                                                value={password}
                                                onChange={(e) => {
                                                    setPassword(e.target.value);
                                                }}
                                            />
                                        </InputGroup>
                                    </div>
                                )}
                            </>
                        )}

                        <div className="flex justify-end mt-7">
                            {currentStep > 1 && (
                                <Button
                                    size="lg"
                                    onClick={handlePrev}
                                    variant="outline"
                                    arrow={'left'}
                                    className="mr-auto"
                                >
                                    {translate('Previous Step')}
                                </Button>
                            )}
                            {showFullForm ? (
                                <Button
                                    variant="primary"
                                    size="lg"
                                    onClick={handleNext}
                                    arrow={false}
                                    className="grow !rounded-[3px] !bg-none !bg-[#e8174f] hover:!bg-[#c91343] !text-white"
                                    isLoading={creatingMnualOrder}
                                    disabled={
                                        creatingMnualOrder ||
                                        (currentStep === steps.length &&
                                            !window.config.paymentMethods
                                                .length)
                                    }
                                >
                                    {currentStep === steps.length
                                        ? translate('Complete Order')
                                        : translate('Next Step')}
                                </Button>
                            ) : (
                                <Button
                                    variant="primary"
                                    size="lg"
                                    onClick={sendOTPVerification}
                                >
                                    {translate('Verify phone')}
                                </Button>
                            )}
                        </div>
                    </div>
                </div>

                <div className="lg:col-span-2">
                    <div className="bg-white rounded-card overflow-hidden">
                        <div className="flex items-center justify-between gap-3 bg-primary py-3 px-4 text-white sm:px-5 md:px-8">
                            <h3>{translate('Order Summary')}</h3>
                            {!getPassword && (
                                <button
                                    type="button"
                                    className="text-right text-xs font-semibold uppercase text-white/90 underline-offset-4 hover:text-white hover:underline sm:text-sm"
                                    onClick={() => {
                                        setGetPassword(true);
                                    }}
                                >
                                    {translate('Want To Create Account')}
                                </button>
                            )}
                        </div>

                        <div>
                            <div className="grid grid-cols-[minmax(0,2fr)_minmax(64px,.75fr)_minmax(92px,1fr)_minmax(76px,.85fr)_36px] items-center gap-2 border-b border-slate-200 bg-slate-50 px-3 py-2.5 text-[10px] font-bold uppercase text-slate-600 sm:px-4 sm:text-[11px]">
                                <span>{translate('Product')}</span>
                                <span>{translate('Price')}</span>
                                <span className="text-center">
                                    {translate('Quantity')}
                                </span>
                                <span className="text-right">
                                    {translate('Subtotal')}
                                </span>
                                <span className="text-center">
                                    {translate('Clear')}
                                </span>
                            </div>

                            {Object.entries(selectedShopProducts).map(
                                ([shopName, carts]) => (
                                    <div key={shopName}>
                                        {window.config.generalSettings
                                            .appMode === 'multiVendor' && (
                                            <div className="flex items-center justify-between text-sm border-b border-primary-14 h-8 sm:h-12 px-4 bg-stone-50">
                                                <h5 className="text-black/70 uppercase">
                                                    {translate('Seller')}
                                                </h5>
                                                <Link
                                                    to="#"
                                                    className="text-primary"
                                                >
                                                    {shopName}
                                                </Link>
                                            </div>
                                        )}

                                        {carts.map((item) => (
                                            <div
                                                className="grid grid-cols-[minmax(0,2fr)_minmax(64px,.75fr)_minmax(92px,1fr)_minmax(76px,.85fr)_36px] items-center gap-2 border-b border-slate-200 px-3 py-3 text-xs sm:px-4 sm:text-sm"
                                                key={item.id}
                                            >
                                                <Link
                                                    to={`/products/${item.product.slug}`}
                                                    className="flex min-w-0 items-center gap-2 font-medium text-slate-800 hover:text-primary"
                                                >
                                                    <Image
                                                        src={
                                                            item.variation
                                                                ?.image ||
                                                            item.product
                                                                .thumbnailImg
                                                        }
                                                        alt={item.product.name}
                                                        className="h-10 w-10 shrink-0 rounded-[3px] border border-slate-200 object-cover sm:h-12 sm:w-12"
                                                    />
                                                    <span className="line-clamp-2">
                                                        {item.product.name}
                                                    </span>
                                                </Link>

                                                <span className="font-semibold text-primary">
                                                    {currencyFormatter(
                                                        item.variation
                                                            .discountedBasePrice,
                                                    )}
                                                </span>

                                                <div className="flex items-center justify-center">
                                                    <div className="inline-flex items-center overflow-hidden rounded-[3px] border border-slate-200">
                                                        <button
                                                            type="button"
                                                            className="h-7 w-7 bg-slate-50 text-base text-slate-600 hover:bg-primary hover:text-white disabled:cursor-not-allowed disabled:opacity-40 sm:h-8 sm:w-8 sm:text-lg"
                                                            onClick={() =>
                                                                handleCartQuantityChange(
                                                                    item,
                                                                    'decrease',
                                                                )
                                                            }
                                                            disabled={
                                                                item.qty <= 1
                                                            }
                                                        >
                                                            -
                                                        </button>
                                                        <span className="min-w-7 px-0.5 text-center font-semibold sm:min-w-9 sm:px-1">
                                                            {item.qty}
                                                        </span>
                                                        <button
                                                            type="button"
                                                            className="h-7 w-7 bg-slate-50 text-base text-slate-600 hover:bg-primary hover:text-white disabled:cursor-not-allowed disabled:opacity-40 sm:h-8 sm:w-8 sm:text-lg"
                                                            onClick={() =>
                                                                handleCartQuantityChange(
                                                                    item,
                                                                    'increase',
                                                                )
                                                            }
                                                            disabled={
                                                                !window.config
                                                                    .generalSettings
                                                                    .allowBackorder &&
                                                                item.qty >=
                                                                    item.product
                                                                        .stockQty
                                                            }
                                                        >
                                                            +
                                                        </button>
                                                    </div>
                                                </div>

                                                <span className="text-right font-bold text-slate-800">
                                                    {currencyFormatter(
                                                        item.variation
                                                            .discountedBasePrice *
                                                            item.qty,
                                                    )}
                                                </span>

                                                <button
                                                    type="button"
                                                    className="mx-auto inline-flex h-8 w-8 items-center justify-center rounded-[3px] text-slate-400 transition-colors hover:bg-red-50 hover:text-red-500"
                                                    onClick={() =>
                                                        handleRemoveFromCart(
                                                            item,
                                                        )
                                                    }
                                                    title={translate('Clear')}
                                                    aria-label={translate(
                                                        'Clear',
                                                    )}
                                                >
                                                    <FaTrash size={13} />
                                                </button>
                                            </div>
                                        ))}
                                    </div>
                                ),
                            )}
                        </div>

                        <div className="space-y-7">
                            <div className="divide-y divide-zinc-100 border-t border-zinc-100 uppercase">
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('Subtotal')}</span>
                                    <span>{currencyFormatter(subTotal)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('tax')}</span>
                                    <span>{currencyFormatter(tax)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('discount')}</span>
                                    <span>{currencyFormatter(discount)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('Shipping Charge')}</span>
                                    <span>
                                        {Number(shippingCharge) <= 0 &&
                                        Boolean(shippingZoneId)
                                            ? translate('Free shipping')
                                            : currencyFormatter(shippingCharge)}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('Total')}</span>
                                    <span>{currencyFormatter(total)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Checkout;
