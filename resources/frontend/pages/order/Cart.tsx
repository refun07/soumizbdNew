import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { FaHome, FaTimes } from 'react-icons/fa';
import { Link, useNavigate } from 'react-router-dom';
import Breadcrumb from '../../components/Breadcrumb';
import Button from '../../components/buttons/Button';
import ProductCardWide from '../../components/card/ProductCardWide';
import Checkbox from '../../components/inputs/Checkbox';
import Input from '../../components/inputs/Input';
import { useApplyCouponMutation } from '../../store/features/api/shopApi';
import { useAuth } from '../../store/features/auth/authSlice';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { ICartProduct } from '../../types/product';
import { getSelectedShopProducts } from '../../utils/getSelectedShopProducts';
import { groupBy } from '../../utils/groupBy';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

const Cart = () => {
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const { user, carts, isLoading } = useAuth();
    const { coupons, selectedShops } = useCheckoutData();

    const [groupedCarts, setGroupedCarts] = useState<
        Record<string, ICartProduct[]>
    >({});

    const [subTotal, setSubTotal] = useState(0);
    const [tax, setTax] = useState(0);
    const [discount, setDiscount] = useState(0);
    const [total, setTotal] = useState(0);

    const [applyCoupon, { isLoading: applyingCoupon, originalArgs }] =
        useApplyCouponMutation();

    // Redirect to checkout if skipCart is enabled (for client-side routing)
    useEffect(() => {
        if (window.config.generalSettings.skipCart && !isLoading) {
            // Auto-select all shops before redirecting
            const groupedByShop = groupBy(carts, ({ shop }) => shop.name);
            const allShopsSelected = Object.keys(groupedByShop).reduce(
                (acc, shopName) => {
                    acc[shopName] = true;
                    return acc;
                },
                {} as Record<string, boolean>,
            );
            dispatch(setCheckoutData({ selectedShops: allShopsSelected }));
            navigate('/checkout');
        }
    }, [carts, isLoading]);

    useEffect(() => {
        if (isLoading) return;

        const groupedByShop = groupBy(carts, ({ shop }) => shop.name);
        setGroupedCarts(groupedByShop);

        const isSingleVendor = window.config.generalSettings.appMode === 'singleVendor';
        if (isSingleVendor) {
            Object.keys(groupedByShop).forEach((shopName) => {
                dispatch(
                    setCheckoutData({
                        selectedShops: {
                            ...selectedShops,
                            [shopName]: true,
                        },
                    }),
                );
            });
        }
    }, [carts, isLoading]);

    useEffect(() => {
        const { total, subTotal, tax, totalDiscount } = getSelectedShopProducts(
            {
                cartProducts: groupedCarts,
                coupons,
                selectedShops,
            },
        );

        setSubTotal(subTotal);
        setTax(tax);
        setDiscount(totalDiscount);
        setTotal(total);
    }, [selectedShops, coupons, groupedCarts]);

    // Show loading state while auth is initializing
    if (isLoading) {
        return (
            <>
                <Breadcrumb
                    title={translate('Your Cart')}
                    navigation={[
                        {
                            icon: <FaHome />,
                            name: translate('Home'),
                            link: '/',
                        },
                        {
                            name: translate('Cart'),
                        },
                    ]}
                />
                <div className="container">
                    <div className="bg-white rounded-card p-6 text-center">
                        <p className="text-gray-500">{translate('Loading cart...')}</p>
                    </div>
                </div>
            </>
        );
    }

    const handleApplyCoupon = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        if (!user) {
            dispatch(togglePopup('signin'));
            return;
        }

        const code = (e.target as any).code.value;
        const shopId = +(e.target as any).shopId.value;

        if (!code) {
            return;
        }

        const coupon = await applyCoupon({ code, shopId }).unwrap();
        if (coupon) {
            // add coupon to store
            dispatch(
                setCheckoutData({
                    coupons: [...coupons, coupon],
                }),
            );
        } else {
            toast.error(translate('Coupon not found!'));
        }
    };

    const handleRemoveCoupon = (shopId: number) => {
        dispatch(
            setCheckoutData({
                coupons: coupons.filter((coupon) => coupon.shopId !== shopId),
            }),
        );
    };

    const handleSelectShop = (shopName: string) => {
        dispatch(
            setCheckoutData({
                selectedShops: {
                    ...selectedShops,
                    [shopName]: !selectedShops[shopName],
                },
            }),
        );
    };

    const handleSelectAll = () => {
        dispatch(
            setCheckoutData({
                selectedShops: Object.keys(selectedShops).reduce(
                    (acc, shopName) => {
                        acc[shopName] = !isAllShopSelected;
                        return acc;
                    },
                    {} as Record<string, boolean>,
                ),
            }),
        );
    };

    const isAllShopSelected = Object.values(selectedShops).every((v) => v);

    return (
        <>
            <Breadcrumb
                title={translate('Your Cart')}
                navigation={[
                    {
                        icon: <FaHome />,
                        name: translate('Home'),
                        link: '/',
                    },
                    {
                        name: translate('Cart'),
                    },
                ]}
            />

            <div className="container no-style grid grid-cols-1 md:grid-cols-3 mb-12">
                <div className="md:col-span-2 bg-white rounded-card p-3 sm:p-6 md:p-9">
                    {window.config.generalSettings.appMode === 'multiVendor' && (
                    <Checkbox
                        label={`${translate('Select All')} (${
                            Object.keys(selectedShops).length
                        } Shops)`}
                        name="select-all"
                        className="mb-2 sm:mb-4"
                        labelClassName="text-black/70 text-sm"
                        checked={isAllShopSelected}
                        onChange={handleSelectAll}
                    />
                    )}

                    <div className="space-y-6">
                        {Object.entries(groupedCarts).map(
                            ([shopName, carts]) => (
                                <div
                                    className="rounded-card border border-zinc-100"
                                    key={shopName}
                                >
                                    {window.config.generalSettings.appMode === 'multiVendor' && (
                                    <div className="rounded-t-md bg-stone-50 flex items-center p-2 sm:p-4">
                                        <Checkbox
                                            label={
                                                <span className="text-sm">
                                                    {translate('Seller')}
                                                    <Link
                                                        to={`/shops/${carts[0].shop.slug}`}
                                                        className="text-primary ml-1"
                                                    >
                                                        {shopName}
                                                    </Link>
                                                </span>
                                            }
                                            name="select-shop"
                                            value={shopName}
                                            checked={selectedShops[shopName]}
                                            onChange={() =>
                                                handleSelectShop(shopName)
                                            }
                                        />
                                    </div>
                                    )}

                                    <div className="divide-y divide-zinc-100">
                                        {carts.map((item) => (
                                            <div
                                                className="px-2 sm:px-4 py-0.5"
                                                key={item.id}
                                            >
                                                <ProductCardWide
                                                    cartProduct={item}
                                                    size={
                                                        window.innerWidth < 640
                                                            ? 'sm'
                                                            : 'md'
                                                    }
                                                />
                                            </div>
                                        ))}

                                        {window.config.generalSettings.showCouponOnCart !== '0' && (
                                        <div className="px-2 sm:px-4 py-1.5 sm:py-3 grid sm:grid-cols-5">
                                            <form
                                                onSubmit={handleApplyCoupon}
                                                className="col-span-3 flex gap-2.5"
                                            >
                                                <input
                                                    type="hidden"
                                                    name="shopId"
                                                    value={carts[0].shop.id}
                                                    onClick={() => {}}
                                                />
                                                <div>
                                                    <Input
                                                        name="code"
                                                        placeholder={translate(
                                                            'Add coupon code',
                                                        )}
                                                        className="text-sm"
                                                    />
                                                </div>
                                                <Button
                                                    variant="primary"
                                                    disabled={coupons.some(
                                                        (coupon) =>
                                                            coupon.shopId ===
                                                            carts[0].shop.id,
                                                    )}
                                                    isLoading={
                                                        applyingCoupon &&
                                                        originalArgs?.shopId ===
                                                            carts[0].shop.id
                                                    }
                                                    arrow={false}
                                                >
                                                    {translate('Add')}{' '}
                                                    <span className="max-sm:hidden">
                                                        {translate('Coupons')}
                                                    </span>
                                                </Button>
                                            </form>

                                            <div className="col-span-2 text-sm flex flex-wrap gap-2 items-center">
                                                {coupons.map(
                                                    (coupon, index) => {
                                                        if (
                                                            coupon.shopId !==
                                                            carts[0].shop.id
                                                        ) {
                                                            return null;
                                                        }

                                                        return (
                                                            <button
                                                                key={index}
                                                                className="inline-flex justify-between bg-secondary text-white rounded px-3 py-2 leading-none text-start uppercase"
                                                                onClick={() =>
                                                                    handleRemoveCoupon(
                                                                        coupon.shopId,
                                                                    )
                                                                }
                                                            >
                                                                {coupon.code}

                                                                <span className="ml-2">
                                                                    <FaTimes />
                                                                </span>
                                                            </button>
                                                        );
                                                    },
                                                )}
                                            </div>
                                        </div>
                                        )}
                                    </div>
                                </div>
                            ),
                        )}
                    </div>
                </div>
                <div>
                    <div className="">
                        <h3 className="py-3 px-5 sm:px-6 md:px-8 bg-primary text-white rounded-t-[5px]">
                            {translate('Total Cost')}
                        </h3>

                        <div className="space-y-7">
                            <div className="border border-zinc-100 rounded-b-[5px] divide-y divide-zinc-100 uppercase">
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-8">
                                    <span>{translate('Subtotal')}</span>
                                    <span>{currencyFormatter(subTotal)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-8">
                                    <span>{translate('Tax')}</span>
                                    <span>{currencyFormatter(tax)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-8">
                                    {translate('Discount')}
                                    <span>{currencyFormatter(discount)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-8">
                                    <span>{translate('Total')}</span>
                                    <span>{currencyFormatter(total)}</span>
                                </div>
                            </div>

                            <div className="flex gap-2">
                                <Button
                                    as="link"
                                    to="/products"
                                    className="grow"
                                    size="lg"
                                    arrow={false}
                                    variant="outline"
                                >
                                    {translate('Shop More')}
                                </Button>
                                {user ? (
                                    <Button
                                        as="link"
                                        to="/checkout"
                                        className="grow"
                                        size="lg"
                                    >
                                        {translate('Checkout')}
                                    </Button>
                                ) : (
                                    <Button
                                        as="link"
                                        // onClick={() =>
                                        //     dispatch(togglePopup('signin'))
                                        // }
                                        to="/checkout"
                                        className="grow"
                                        size="lg"
                                    >
                                        {translate('Checkout')}
                                    </Button>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Cart;
