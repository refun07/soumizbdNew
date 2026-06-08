import { ICartProduct } from '@/types/product';
import { useFormik } from 'formik';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { useDispatch } from 'react-redux';
import { ActionMeta } from 'react-select';
import { object, string } from 'yup';
import { useAuth } from '../../store/features/auth/authSlice';
import {
    useLazyGetCitiesQuery,
    useLazyGetShippingChargeQuery,
    useLazyGetStatesQuery,
} from '../../store/features/checkout/checkoutApi';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { closePopup } from '../../store/features/popup/popupSlice';
import {
    IAddress,
    ICity,
    ICountry,
    IState,
} from '../../types/checkout';
import { AddressPayload } from '../../types/payload';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';
import InputGroup from '../inputs/InputGroup';
import Label from '../inputs/Label';
import SelectInput from '../inputs/SelectInput';
import Textarea from '../inputs/Textarea';
interface Props {
    // address?: IAddress;
    className?: string;
    // onSuccess?: (address: IAddress) => void;
}

const SimpleAddressForm = ({ className }: Props) => {
    const dispatch = useDispatch();
    const { shippingAddress, selectedShops, coupons } = useCheckoutData();
    const countries = window.config.countries || [];
    const bangladeshCountry =
        countries.find((country: ICountry) => country.code === 'BD') ||
        countries.find((country: ICountry) => country.name === 'Bangladesh');
    const bangladeshCountryId =
        bangladeshCountry?.id || shippingAddress?.countryId || 18;

    const initialValues: AddressPayload = {
        countryId: shippingAddress
            ? shippingAddress.countryId
            : bangladeshCountryId,
        stateId: shippingAddress ? shippingAddress.stateId : undefined,
        cityId: shippingAddress ? shippingAddress.cityId : undefined,
        areaId: undefined,
        address: shippingAddress ? shippingAddress.address : '',
        direction: '',
        postalCode: '',
        type: 'home',
    };
    const [getState, { isFetching: isStateLoading }] = useLazyGetStatesQuery();
    const [getCity, { isFetching: isCityLoading }] = useLazyGetCitiesQuery();
    const [getShippingCharge] = useLazyGetShippingChargeQuery();

    const [states, setStates] = useState<IState[]>([]);
    const [cities, setCities] = useState<ICity[]>([]);
    const [selectedState, setSelectedState] = useState<IState | null>(null);
    const [selectedCity, setSelectedCity] = useState<ICity | null>(null);
    const [address, setAddress] = useState<string | null>(null);
    const { carts, user, guestUserId } = useAuth();
    const selectedShopProducts: Record<string, ICartProduct[]> = {};
    Object.keys(selectedShops).forEach((shopName) => {
        if (!selectedShops[shopName]) return;

        const products = carts.filter((cart) => cart.shop.name === shopName);
        selectedShopProducts[shopName] = products;
    });

    const selectedShopIds = Object.values(selectedShopProducts)
        .map((products) => products[0]?.shop?.id)
        .filter((id, i, arr) => id && arr.indexOf(id) === i);

    const selectedCartIds = Object.values(selectedShopProducts)
        .map((products) => products.map((product) => product.id))
        .flat();

    const itemsWithQuantities = Object.values(selectedShopProducts)
        .flat()
        .map((product) => ({
            product_id: product.product.id,
            variation_id: product.variation.id,
            quantity: product.qty,
            shop_id: product.shop.id,
        }));

    const validationSchema = object().shape({
        countryId: string().required('Country is required'),
        stateId: string().required('State is required'),
        cityId: string().required('Select a city/town'),
    });
    const [addressData, setAddressData] = useState(() => ({
        id: 0,
        countryId: bangladeshCountryId,
        country: {
            id: bangladeshCountryId,
            code: 'BD',
            name: 'Bangladesh',
            is_active: 1,
        },
    }));
    const selectPlaceholderClassNames = {
        placeholder: () => '!text-black/40',
    };
    const setAddressToFormik = async () => {
        formik.setValues({
            countryId: shippingAddress
                ? shippingAddress.countryId
                : bangladeshCountryId,
            stateId: shippingAddress ? shippingAddress.stateId : undefined,
            cityId: shippingAddress ? shippingAddress.cityId : undefined,
            areaId: undefined,
            address: shippingAddress ? shippingAddress.address : '',
            direction: '',
            postalCode: '',
            type: 'home',
        });
    };

    useEffect(() => {
        setAddressToFormik();
    }, []);
    useEffect(() => {
        const nextAddressData = {
            ...addressData,
            ...(selectedState
                ? {
                      stateId: selectedState.id,
                      state: selectedState,
                  }
                : {}),
            ...(selectedCity
                ? {
                      cityId: selectedCity.id,
                      city: selectedCity,
                  }
                : {}),
            ...(address !== null ? { address } : {}),
        };

        setAddressData(nextAddressData);

        if (selectedState && selectedCity && address && address != '') {
            dispatch(
                setCheckoutData({
                    shippingAddress: nextAddressData as IAddress,
                    billingAddress: nextAddressData as IAddress,
                }),
            );
        }
    }, [selectedState, selectedCity, address]);

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values, formikHelpers) => {
            try {
                formikHelpers.resetForm();
                dispatch(closePopup());
            } catch (error: any) {
                toast.error(error.data.message);
            }
        },
    });

    useEffect(() => {
        const initializeLocation = async () => {
            try {
                await setAddressToFormik();
                const countryId =
                    (shippingAddress?.countryId as number) ||
                    bangladeshCountryId;
                const stateList = await getState({ countryId }).unwrap();
                setStates(stateList);

                const selectedShippingStateId = shippingAddress?.stateId;
                if (selectedShippingStateId) {
                    const state = stateList.find(
                        (item) => item.id === selectedShippingStateId,
                    );
                    if (state) {
                        setSelectedState(state);
                    }

                    const cityList = await getCity({
                        stateId: selectedShippingStateId,
                    }).unwrap();
                    setCities(cityList);

                    const stateCityId = shippingAddress?.cityId;
                    if (stateCityId) {
                        const city = cityList.find(
                            (item) => item.id === stateCityId,
                        );
                        if (city) {
                            setSelectedCity(city);
                        }
                    }
                } else {
                    setCities([]);
                }
            } catch (error) {
                console.error('Failed to load states/cities', error);
                setStates([]);
                setCities([]);
            }
        };

        initializeLocation();
    }, [bangladeshCountryId]);
    useEffect(() => {
        if (!formik.values.cityId) {
            dispatch(setCheckoutData({ shippingCharge: 0 }));
        }
    }, []);

    const handleCountryChange = async (option?: ICountry | null) => {
        const countryId = option?.id || bangladeshCountryId;
        formik.setFieldValue('countryId', countryId);
        formik.setFieldValue('stateId', null);
        formik.setFieldValue('cityId', null);
        setSelectedState(null);
        setSelectedCity(null);
        setStates([]);
        setCities([]);

        try {
            const stateList = await getState({ countryId }).unwrap();
            setStates(stateList);
        } catch (error) {
            setStates([]);
        }
    };

    const handleStateChange = async (
        option: IState | null,
        { name }: ActionMeta<IState>,
    ) => {
        formik.setFieldValue(name!, option?.id ?? null);
        formik.setFieldValue('cityId', null);
        setSelectedCity(null);
        setCities([]);
        dispatch(
            setCheckoutData({
                shippingCharge: 0,
                shippingAddress: undefined,
                billingAddress: undefined,
            }),
        );

        if (!option) {
            setSelectedState(null);
            return;
        }

        setSelectedState(option);

        try {
            const cityList = await getCity({ stateId: option.id }).unwrap();
            setCities(cityList);
        } catch (error) {
            setCities([]);
        }
    };

    // Recalculate shipping when cart quantities change
    useEffect(() => {
        if (!formik.values.cityId || selectedShopIds.length === 0) return;

        const fetchShippingCharge = async () => {
            try {
                const shippingChargeData = await getShippingCharge({
                    cityId: formik.values.cityId as number,
                    userId: user?.id || 0,
                    guestUserId: guestUserId || 0,
                    shopIds: selectedShopIds,
                    cartIds: selectedCartIds,
                    coupons: coupons.map((c) => c.code),
                    items: itemsWithQuantities,
                }).unwrap();

                dispatch(
                    setCheckoutData({ shippingCharge: shippingChargeData }),
                );
            } catch (error) {
                console.error('Shipping charge error:', error);
            }
        };

        fetchShippingCharge();
    }, [carts, selectedShops]);

    const handleCityChange = async (
        option: ICity | null,
        { name }: ActionMeta<ICity>,
    ) => {
        formik.setFieldValue(name!, option?.id ?? null);

        if (!option) {
            setSelectedCity(null);
            dispatch(
                setCheckoutData({
                    shippingCharge: 0,
                    shippingAddress: undefined,
                    billingAddress: undefined,
                }),
            );
            return;
        }

        const selectedCity = cities.find(
            (item: ICity) => item.id === option.id,
        );
        if (selectedCity) {
            setSelectedCity(selectedCity);

            if (selectedState) {
                const nextAddressData = {
                    ...addressData,
                    stateId: selectedState.id,
                    state: selectedState,
                    cityId: selectedCity.id,
                    city: selectedCity,
                    address: formik.values.address || '',
                };

                dispatch(
                    setCheckoutData({
                        shippingAddress: nextAddressData as IAddress,
                        billingAddress: nextAddressData as IAddress,
                    }),
                );
            }
        }
        // Call the API only if data exists
        if (option.id) {
            try {
                const shippingChargeData = await getShippingCharge({
                    cityId: option.id as number,
                    userId: user?.id || 0,
                    guestUserId: guestUserId || 0,
                    shopIds: selectedShopIds,
                    cartIds: selectedCartIds,
                    coupons: coupons.map((c) => c.code),
                    items: itemsWithQuantities,
                }).unwrap();

                dispatch(
                    setCheckoutData({ shippingCharge: shippingChargeData }),
                );
            } catch (error) {
                console.error('Shipping charge error:', error);
                toast.error('Failed to calculate shipping charge');
            }
        }
    };

    const handleTextareaChange = (
        e: React.ChangeEvent<HTMLTextAreaElement>,
    ) => {
        const nextAddress = e.target.value;
        formik.setFieldValue(e.target.name, nextAddress);
        setAddress(nextAddress);

        if (selectedState && selectedCity && nextAddress != '') {
            const nextAddressData = {
                ...addressData,
                stateId: selectedState.id,
                state: selectedState,
                cityId: selectedCity.id,
                city: selectedCity,
                address: nextAddress,
            };

            setAddressData(nextAddressData);
            dispatch(
                setCheckoutData({
                    shippingAddress: nextAddressData as IAddress,
                    billingAddress: nextAddressData as IAddress,
                }),
            );
        }
    };

    return (
        <form
            onSubmit={formik.handleSubmit}
            className={cn(
                'grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 ',
                className,
            )}
        >
            <InputGroup className="hidden">
                <Label>{translate('country')}</Label>
                <SelectInput
                    placeholder={translate('Select a country')}
                    name="countryId"
                    value={bangladeshCountryId}
                    onChange={handleCountryChange}
                    classNames={selectPlaceholderClassNames}
                    options={countries || []}
                    getOptionLabel={(option: ICountry) => option.name}
                    getOptionValue={(option: ICountry) => String(option.id)}
                    error={translate(formik.errors.countryId)}
                    touched={formik.touched.countryId}
                />
            </InputGroup>

            <InputGroup>
                <Label>
                    {translate('District')}
                    <span className="text-theme-alert ml-1">*</span>
                </Label>
                <SelectInput
                    placeholder={translate('Select a District')}
                    name="stateId"
                    value={
                        formik.values.stateId
                            ? String(formik.values.stateId)
                            : ''
                    }
                    isLoading={isStateLoading}
                    onChange={handleStateChange}
                    classNames={selectPlaceholderClassNames}
                    options={states || []}
                    getOptionLabel={(option: IState) => option.name}
                    getOptionValue={(option: IState) => String(option.id)}
                    error={translate(formik.errors.stateId)}
                    touched={formik.touched.stateId}
                />
            </InputGroup>

            <InputGroup>
                <Label>
                    {translate('City/Town')}
                    <span className="text-theme-alert ml-1">*</span>
                </Label>
                <SelectInput
                    placeholder={translate('Select a city/town')}
                    name="cityId"
                    value={formik.values.cityId}
                    isLoading={isCityLoading}
                    isDisabled={!formik.values.stateId}
                    onChange={handleCityChange}
                    classNames={selectPlaceholderClassNames}
                    options={cities || []}
                    getOptionLabel={(option: ICity) => option.name}
                    getOptionValue={(option: ICity) => String(option.id)}
                    error={translate(formik.errors.cityId)}
                    touched={formik.touched.cityId}
                />
            </InputGroup>
            <InputGroup className="sm:col-span-2">
                <Label>
                    {translate('address')}
                    <span className="text-theme-alert ml-1">*</span>
                </Label>
                <Textarea
                    name="address"
                    className="placeholder:text-black/40"
                    placeholder={translate('Please Enter Your Details Delivery Address In English (or Banglish eg - Amar Sonar Bangla). দয়াকরে আপনার ডেলিভারি এড্রেস ইংরেজিতে (ইংরেজি ফন্টে/বাংলিশ যেমন -  Aamar Sonar Bangla) লিখুন।')}
                    value={formik.values.address}
                    onChange={handleTextareaChange}
                    error={translate(formik.errors.address)}
                    touched={formik.touched.address}
                />
            </InputGroup>
        </form>
    );
};

export default SimpleAddressForm;
