import { ICartProduct } from '@/types/product';
import { useEffect, useMemo } from 'react';
import { useAuth } from '../../store/features/auth/authSlice';
import { useLazyGetDeliveryAreasQuery } from '../../store/features/checkout/checkoutApi';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { useAppDispatch } from '../../store/store';
import { IAddress, ICountry, IDeliveryArea } from '../../types/checkout';
import { cn } from '../../utils/cn';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';
import InputGroup from '../inputs/InputGroup';
import Label from '../inputs/Label';
import Textarea from '../inputs/Textarea';

interface Props {
    className?: string;
}

const SimpleAddressForm = ({ className }: Props) => {
    const dispatch = useAppDispatch();
    const { carts, user, guestUserId } = useAuth();
    const { shippingAddress, shippingZoneId, selectedShops, coupons } =
        useCheckoutData();
    const [getDeliveryAreas, { data: deliveryAreas = [], isFetching }] =
        useLazyGetDeliveryAreasQuery();

    const countries = window.config.countries || [];
    const bangladeshCountry =
        countries.find((country: ICountry) => country.code === 'BD') ||
        countries.find((country: ICountry) => country.name === 'Bangladesh');
    const bangladeshCountryId = bangladeshCountry?.id || 18;

    const selectedProducts = useMemo(() => {
        const products: ICartProduct[] = [];

        Object.keys(selectedShops).forEach((shopName) => {
            if (!selectedShops[shopName]) return;
            products.push(
                ...carts.filter((cart) => cart.shop.name === shopName),
            );
        });

        return products;
    }, [carts, selectedShops]);

    const quotePayload = useMemo(() => {
        const shopIds = selectedProducts
            .map((product) => product.shop.id)
            .filter((id, index, ids) => ids.indexOf(id) === index);

        return {
            userId: user?.id || 0,
            guestUserId: guestUserId || 0,
            shopIds,
            cartIds: selectedProducts.map((product) => product.id),
            coupons: coupons.map((coupon) => coupon.code),
            items: selectedProducts.map((product) => ({
                product_id: product.product.id,
                variation_id: product.variation.id,
                quantity: product.qty,
                shop_id: product.shop.id,
            })),
        };
    }, [coupons, guestUserId, selectedProducts, user?.id]);

    useEffect(() => {
        if (!quotePayload.shopIds.length) return;

        getDeliveryAreas(quotePayload, false)
            .unwrap()
            .then((areas) => {
                const selectedArea = areas.find(
                    (area) => area.id === shippingZoneId,
                );

                if (selectedArea) {
                    dispatch(
                        setCheckoutData({
                            shippingCharge: selectedArea.charge,
                            shippingZoneName: selectedArea.name,
                        }),
                    );
                }
            });
    }, [getDeliveryAreas, quotePayload, shippingZoneId]);

    const selectDeliveryArea = (area: IDeliveryArea) => {
        dispatch(
            setCheckoutData({
                shippingZoneId: area.id,
                shippingZoneName: area.name,
                shippingCharge: area.charge,
            }),
        );
    };

    const updateAddress = (address: string) => {
        const nextAddress = {
            id: shippingAddress?.id || 0,
            countryId: bangladeshCountryId,
            country: bangladeshCountry || {
                id: bangladeshCountryId,
                code: 'BD',
                name: 'Bangladesh',
                is_active: 1,
                created_by: null,
            },
            address,
            type: 'home',
            phone: shippingAddress?.phone || '',
            isDefault: shippingAddress?.isDefault || 0,
            created_at: shippingAddress?.created_at || '',
            updated_at: shippingAddress?.updated_at || '',
        } as IAddress;

        dispatch(
            setCheckoutData({
                shippingAddress: nextAddress,
                billingAddress: nextAddress,
            }),
        );
    };

    return (
        <div className={cn('space-y-4 pb-4', className)}>
            <InputGroup>
                <Label className="!mb-1.5 !normal-case font-semibold text-slate-800">
                    {translate('Full Delivery Address')}
                    <span className="text-theme-alert ml-1">*</span>
                </Label>
                <Textarea
                    name="address"
                    rows={3}
                    className="!rounded-[3px] !border-slate-200 !bg-slate-50 placeholder:!text-slate-400 focus:!border-primary"
                    placeholder={translate(
                        'House, road, area and nearby landmark',
                    )}
                    value={shippingAddress?.address || ''}
                    onChange={(event) => updateAddress(event.target.value)}
                />
            </InputGroup>

            <fieldset>
                <legend className="mb-2 text-sm font-semibold text-slate-800">
                    {translate('Delivery Area')}
                    <span className="text-theme-alert ml-1">*</span>
                </legend>

                <div className="space-y-2">
                    {isFetching && !deliveryAreas.length ? (
                        <div className="bg-slate-100 px-4 py-3 text-sm text-slate-500">
                            {translate('Calculating delivery charges')}...
                        </div>
                    ) : !deliveryAreas.length ? (
                        <div className="bg-slate-100 px-4 py-3 text-sm text-theme-alert">
                            {translate('No delivery areas available')}
                        </div>
                    ) : (
                        deliveryAreas.map((area) => (
                            <label
                                key={area.id}
                                className={cn(
                                    'flex cursor-pointer items-center gap-3 rounded-[3px] border px-4 py-3 text-sm font-semibold transition-colors',
                                    shippingZoneId === area.id
                                        ? 'border-primary bg-primary/10 text-primary'
                                        : 'border-slate-200 bg-slate-100 text-slate-700 hover:border-primary/50',
                                )}
                            >
                                <input
                                    type="radio"
                                    name="shippingZoneId"
                                    className="h-4 w-4 accent-primary"
                                    checked={shippingZoneId === area.id}
                                    onChange={() => selectDeliveryArea(area)}
                                />
                                <span>{translate(area.name)}</span>
                                <span className="ml-auto">
                                    {currencyFormatter(area.charge)}
                                </span>
                            </label>
                        ))
                    )}
                </div>
            </fieldset>
        </div>
    );
};

export default SimpleAddressForm;
