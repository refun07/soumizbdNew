import { useFormik } from 'formik';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { AiOutlinePlus } from 'react-icons/ai';
import { FiEdit2, FiTrash2, FiUser } from 'react-icons/fi';
import { LiaTimesSolid } from 'react-icons/lia';
import { MdArrowBack } from 'react-icons/md';
import { ActionMeta } from 'react-select';
import { object, string } from 'yup';
import { Drawer } from 'vaul';
import Button from '../../react/components/inputs/Button';
import Input from '../../react/components/inputs/Input';
import SelectInput from '../../react/components/inputs/SelectInput';
import Textarea from '../../react/components/inputs/Textarea';
import { objectToFormData } from '../../react/utils/ObjectFormData';
import { translate } from '../../react/utils/translate';
import { IAddress, ICity, ICountry, ICustomer, IState } from '../types';
import {
    addAddress,
    addCustomer,
    deleteAddress,
    getAddresses,
    getCities,
    getStates,
    updateAddress,
    updateCustomer,
} from '../utils/actions';
import ModalWrapper from './ModalWrapper';

interface Props {
    isOpen: boolean;
    onClose: () => void;
    initialCustomer?: ICustomer;
    initialAddress?: IAddress;
    initialAddresses?: IAddress[];
    onSuccess: (customer: ICustomer, address?: IAddress) => void;
    customerFilters: ICustomer[];
    onCustomerFiltersUpdate: (customers: ICustomer[]) => void;
    isMobile?: boolean;
    defaultViewMode?: ViewMode;
}

type ViewMode = 'select' | 'manage' | 'address-picker' | 'address-form';

interface CustomerFormValues {
    name: string;
    email: string;
    phone: string;
    password: string;
}

interface AddressFormValues {
    countryId: number | null;
    stateId: number | null;
    cityId: number | null;
    type: string;
    address: string;
}

const customerInitialValues: CustomerFormValues = {
    name: '',
    email: '',
    phone: '',
    password: '',
};

const addressInitialValues: AddressFormValues = {
    countryId: null,
    stateId: null,
    cityId: null,
    type: 'home',
    address: '',
};

const CustomerModal = ({
    isOpen,
    onClose,
    initialCustomer,
    initialAddress,
    initialAddresses,
    onSuccess,
    customerFilters,
    onCustomerFiltersUpdate,
    isMobile = false,
    defaultViewMode,
}: Props) => {
    const [viewMode, setViewMode] = useState<ViewMode>(defaultViewMode || 'select');
    const [selectedCustomer, setSelectedCustomer] = useState<ICustomer | null>(
        initialCustomer || null,
    );
    const [selectedAddress, setSelectedAddress] = useState<IAddress | null>(
        initialAddress || null,
    );
    const [editingAddress, setEditingAddress] = useState<IAddress | null>(null);
    const [addresses, setAddresses] = useState<IAddress[]>(
        initialAddresses || [],
    );
    const [states, setStates] = useState<IState[]>([]);
    const [cities, setCities] = useState<ICity[]>([]);

    useEffect(() => {
        if (isOpen) {
            setViewMode(defaultViewMode || 'select');
            if (initialAddresses) {
                setAddresses(initialAddresses);
            }
        }
    }, [isOpen, defaultViewMode, initialAddresses]);

    const countries = window.config?.countries || [];
    const bangladeshCountry =
        countries.find((c: ICountry) => c.code === 'BD') ||
        countries.find((c: ICountry) => c.name === 'Bangladesh');
    const bangladeshCountryId = bangladeshCountry?.id || null;

    const customerFormik = useFormik({
        initialValues: customerInitialValues,
        validationSchema: object().shape({
            name: string().required('Name is required'),
            email: string(),
            phone: string(),
            password: string(),
        }),
        onSubmit: async (values, helpers) => {
            try {
                if (selectedCustomer && selectedCustomer.id !== 0) {
                    const result = await updateCustomer(
                        selectedCustomer.id,
                        objectToFormData(values),
                    );
                    onCustomerFiltersUpdate(result.customers);
                    setSelectedCustomer(result.customer);
                    toast.success('Customer updated successfully');
                    customerFormik.setValues({
                        name: result.customer.name,
                        email: result.customer.email || '',
                        phone: result.customer.phone || '',
                        password: '',
                    });
                } else {
                    const result = await addCustomer(objectToFormData(values));
                    onCustomerFiltersUpdate([
                        result.newCustomer,
                        ...customerFilters,
                    ]);
                    setSelectedCustomer(result.newCustomer);
                    toast.success('Customer created successfully');
                    customerFormik.setValues({
                        name: result.newCustomer.name,
                        email: result.newCustomer.email || '',
                        phone: result.newCustomer.phone || '',
                        password: '',
                    });

                    if (addressFormik.values.address && addressFormik.values.stateId) {
                        try {
                            const addressPayload = {
                                ...addressFormik.values,
                                id: result.newCustomer.id,
                                addressId: undefined,
                            };
                            const addressResult = await addAddress(objectToFormData(addressPayload));
                            const newAddress = addressResult.addresses?.[0];
                            toast.success('Address created successfully');
                            onSuccess(result.newCustomer, newAddress);
                            handleClose();
                            return;
                        } catch {
                            toast.error('Customer created, but address failed');
                        }
                    }
                }
                helpers.resetForm({ values: customerFormik.values });
            } catch (error: any) {
                toast.error(
                    error.message || 'Failed to save customer',
                );
            }
        },
    });

    const addressFormik = useFormik({
        initialValues: addressInitialValues,
        validationSchema: object().shape({
            stateId: string().required('District is required'),
            cityId: string().required('City is required'),
            address: string().required('Address is required'),
            type: string()
                .oneOf(['home', 'office', 'other'])
                .required('Save as is required'),
        }),
        onSubmit: async (values, helpers) => {
            if (!selectedCustomer) {
                toast.error('Please select a customer first');
                return;
            }

            try {
                const payload = {
                    ...values,
                    id: selectedCustomer.id,
                    addressId: editingAddress?.id,
                };

                if (editingAddress) {
                    await updateAddress(
                        editingAddress.id,
                        objectToFormData(payload),
                    );
                    toast.success('Address updated successfully');
                    await loadAddresses(selectedCustomer.id);
                    setEditingAddress(null);
                    helpers.resetForm();
                    setViewMode(viewMode === 'address-picker' ? 'address-picker' : 'manage');
                } else {
                    await addAddress(objectToFormData(payload));
                    toast.success('Address created successfully');
                    await loadAddresses(selectedCustomer.id);
                    setEditingAddress(null);
                    helpers.resetForm();
                    setViewMode(viewMode === 'address-picker' ? 'address-picker' : 'manage');
                }
            } catch (error: any) {
                toast.error(
                    error.message || 'Failed to save address',
                );
            }
        },
    });

    const loadAddresses = async (customerId: number) => {
        try {
            const data = await getAddresses(customerId);
            setAddresses(data);
        } catch {
            setAddresses([]);
        }
    };

    useEffect(() => {
        if (!isOpen) return;
        if (viewMode === 'address-picker' && initialAddresses?.length) return;
        if (selectedCustomer && selectedCustomer.id !== 0) {
            loadAddresses(selectedCustomer.id);
        } else if (selectedCustomer && selectedCustomer.id === 0) {
            setAddresses([]);
        }
    }, [selectedCustomer, isOpen]);

    useEffect(() => {
        if (!bangladeshCountryId) return;

        const initialize = async () => {
            addressFormik.setFieldValue('countryId', bangladeshCountryId);
            try {
                const stateList = await getStates(bangladeshCountryId);
                setStates(stateList);
            } catch {
                setStates([]);
            }
        };

        initialize();
    }, [bangladeshCountryId]);

    useEffect(() => {
        if (!isOpen) return;
        if (viewMode === 'manage' || viewMode === 'address-picker') {
            if (initialCustomer) {
                setSelectedCustomer(initialCustomer);
                if (viewMode === 'manage') {
                    customerFormik.setValues({
                        name: initialCustomer.name,
                        email: initialCustomer.email || '',
                        phone: initialCustomer.phone || '',
                        password: '',
                    });
                }
            } else {
                setSelectedCustomer(null);
                customerFormik.resetForm();
            }
        }
    }, [isOpen, initialCustomer, viewMode]);

    const handleCustomerSelect = (customer: ICustomer) => {
        setSelectedCustomer(customer);
        setSelectedAddress(null);
        customerFormik.setValues({
            name: customer.name,
            email: customer.email || '',
            phone: customer.phone || '',
            password: '',
        });
        setViewMode('manage');
    };

    const handleAddressSelect = (address: IAddress) => {
        setSelectedAddress(address);
        onSuccess(selectedCustomer!, address);
        handleClose();
    };

    const handleEditAddress = (address: IAddress) => {
        setEditingAddress(address);
        setAddressFormValues(address);
        setViewMode('address-form');
    };

    const handleDeleteAddress = async (addressId: number) => {
        if (!selectedCustomer) return;

        try {
            const result = await deleteAddress(addressId);
            setAddresses(result.addresses);
            toast.success('Address deleted successfully');
        } catch {
            toast.error('Failed to delete address');
        }
    };

    const setAddressFormValues = (address: IAddress) => {
        addressFormik.setValues({
            countryId: address.countryId,
            stateId: address.stateId,
            cityId: address.cityId || null,
            type: address.type || 'home',
            address: address.address,
        });

        const loadLocationData = async () => {
            try {
                const stateList = await getStates(address.countryId);
                setStates(stateList);

                if (address.stateId) {
                    const cityList = await getCities(address.stateId);
                    setCities(cityList);
                }
            } catch {
                setStates([]);
                setCities([]);
            }
        };

        loadLocationData();
    };

    const handleNewAddress = () => {
        setEditingAddress(null);
        addressFormik.resetForm();
        addressFormik.setFieldValue('countryId', bangladeshCountryId);
    };

    const handleStateChange = async (
        option: IState | null,
        { name }: ActionMeta<IState>,
    ) => {
        addressFormik.setFieldValue(name!, option?.id ?? null);
        addressFormik.setFieldValue('cityId', null);
        setCities([]);

        if (!option) return;

        try {
            const cityList = await getCities(option.id);
            setCities(cityList);
        } catch {
            setCities([]);
        }
    };

    const handleCountryChange = async (option: ICountry) => {
        addressFormik.setFieldValue('countryId', option.id);
        addressFormik.setFieldValue('stateId', null);
        addressFormik.setFieldValue('cityId', null);
        setCities([]);

        try {
            const stateList = await getStates(option.id);
            setStates(stateList);
        } catch {
            setStates([]);
        }
    };

    const handleCityChange = (option: ICity | null, { name }: ActionMeta<ICity>) => {
        addressFormik.setFieldValue(name!, option?.id ?? null);
    };

    const handleClose = () => {
        setViewMode('select');
        setSelectedCustomer(initialCustomer || null);
        setSelectedAddress(initialAddress || null);
        setEditingAddress(null);
        setAddresses([]);
        customerFormik.resetForm();
        addressFormik.resetForm();
        onClose();
    };

    const handleBack = () => {
        switch (viewMode) {
            case 'manage':
                setViewMode('select');
                customerFormik.resetForm();
                break;
            case 'address-picker':
                handleClose();
                break;
            case 'address-form':
                setViewMode(addresses.length > 0 ? 'address-picker' : 'manage');
                setEditingAddress(null);
                addressFormik.resetForm();
                break;
            default:
                handleClose();
        }
    };

    const renderHeader = () => {
        const titles: Record<ViewMode, string> = {
            select: translate('Manage Customer'),
            manage: translate('Customer Management'),
            'address-picker': translate('Select Address'),
            'address-form': editingAddress
                ? translate('Edit Address')
                : translate('Add Address'),
        };

        return (
            <div className="flex items-center gap-3 border-b border-border px-4 py-3">
                {viewMode !== 'select' && (
                    <button
                        type="button"
                        onClick={handleBack}
                        className="text-lg text-muted hover:text-foreground"
                    >
                        <MdArrowBack />
                    </button>
                )}
                <h5 className="flex-1 text-base font-semibold">
                    {titles[viewMode]}
                </h5>
                <button
                    type="button"
                    onClick={handleClose}
                    className="text-xl text-muted hover:text-foreground"
                >
                    <LiaTimesSolid />
                </button>
            </div>
        );
    };

    const renderSelectView = () => (
        <div className="space-y-4 p-4">
            <div>
                <label className="mb-2 block text-sm font-medium">
                    {translate('Select Customer')}
                </label>
                <select
                    className="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm"
                    value={selectedCustomer?.id || ''}
                    onChange={(e) => {
                        const val = e.target.value;
                        if (val === '') {
                            onSuccess({ id: 0, name: 'Walk In Customer' } as ICustomer);
                            handleClose();
                        } else {
                            const customer = customerFilters.find(
                                (c) => c.id.toString() === val,
                            );
                            if (customer) {
                                handleCustomerSelect(customer);
                            }
                        }
                    }}
                >
                    <option value="">{translate('Walk In Customer')}</option>
                    {customerFilters.map((customer) => (
                        <option key={customer.id} value={customer.id}>
                            {customer.name}
                        </option>
                    ))}
                </select>
            </div>

            {selectedCustomer && (
                <div className="rounded-lg border border-border p-4">
                    <div className="flex items-start justify-between">
                        <div className="flex items-start gap-3">
                            <div className="flex h-10 w-10 items-center justify-center rounded-full bg-primary/10 text-primary">
                                <FiUser />
                            </div>
                            <div>
                                <p className="font-medium">{selectedCustomer.name}</p>
                                <p className="text-sm text-muted">
                                    {translate('Customer selected')}
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={() => {
                                customerFormik.setValues({
                                    name: selectedCustomer.name,
                                    email: selectedCustomer.email || '',
                                    phone: selectedCustomer.phone || '',
                                    password: '',
                                });
                                setViewMode('manage');
                            }}
                            className="rounded p-1 text-muted hover:bg-background-hover hover:text-foreground"
                            title={translate('Edit Customer')}
                        >
                            <FiEdit2 />
                        </button>
                    </div>
                </div>
            )}

            <div className="flex gap-2">
                <Button
                    type="button"
                    variant="primary"
                    className="flex-1"
                    onClick={() => {
                        setSelectedCustomer(null);
                        customerFormik.resetForm();
                        setViewMode('manage');
                    }}
                >
                    <AiOutlinePlus className="mr-2" />
                    {translate('Add New Customer')}
                </Button>
            </div>
        </div>
    );

    const renderManageView = () => (
        <div className="flex max-h-[85vh] flex-col">
            <div className="flex-1 overflow-y-auto p-4 space-y-6">
                {/* Customer Selector */}
                {initialCustomer && (
                <div>
                    <label className="mb-2 block text-sm font-medium">
                        {translate('Select Customer')}
                    </label>
                    <select
                        className="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm"
                        value={selectedCustomer?.id || ''}
                        onChange={(e) => {
                            const val = e.target.value;
                            if (val === '') {
                                setSelectedCustomer({ id: 0, name: 'Walk In Customer' } as ICustomer);
                                customerFormik.setValues({ name: 'Walk In Customer', email: '', phone: '', password: '' });
                                setAddresses([]);
                            } else {
                                const customer = customerFilters.find(
                                    (c) => c.id.toString() === val,
                                );
                                if (customer) {
                                    handleCustomerSelect(customer);
                                }
                            }
                        }}
                    >
                        <option value="">{translate('Walk In Customer')}</option>
                        {customerFilters.map((customer) => (
                            <option key={customer.id} value={customer.id}>
                                {customer.name}
                            </option>
                        ))}
                    </select>
                </div>
                )}

                {/* Customer Form Section */}
                <div>
                    <h6 className="text-sm font-semibold mb-3 text-foreground">
                        {translate('Customer Details')}
                    </h6>
                    <form onSubmit={customerFormik.handleSubmit} className="space-y-3">
                        <div>
                            <label className="mb-1 block text-sm font-medium">
                                {translate('Name')}
                                <span className="ml-1 text-red-500">*</span>
                            </label>
                            <Input
                                {...customerFormik.getFieldProps('name')}
                                touched={customerFormik.touched.name}
                                error={customerFormik.errors.name}
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm font-medium">
                                {translate('Email')}
                            </label>
                            <Input
                                {...customerFormik.getFieldProps('email')}
                                type="email"
                                touched={customerFormik.touched.email}
                                error={customerFormik.errors.email}
                            />
                        </div>
                        <div>
                            <label className="mb-1 block text-sm font-medium">
                                {translate('Phone')}
                            </label>
                            <Input
                                {...customerFormik.getFieldProps('phone')}
                                type="tel"
                                touched={customerFormik.touched.phone}
                                error={customerFormik.errors.phone}
                            />
                        </div>
                        {!selectedCustomer?.id && (
                                <div>
                                    <label className="mb-1 block text-sm font-medium">
                                        {translate('Password')}
                                    </label>
                                    <Input
                                        {...customerFormik.getFieldProps('password')}
                                        type="password"
                                        placeholder="Leave blank for default (123456)"
                                        touched={customerFormik.touched.password}
                                        error={customerFormik.errors.password}
                                    />
                                </div>
                            )}
                            {!initialCustomer && (
                                <>
                                    <h6 className="text-sm font-semibold pt-3 text-foreground">
                                        {translate('Delivery Address')}
                                        <span className="ml-2 text-xs font-normal text-muted">
                                            ({translate('Optional')})
                                        </span>
                                    </h6>
                                    <div>
                                        <label className="mb-1 block text-sm font-medium">
                                            {translate('District')}
                                        </label>
                                        <select
                                            className="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm"
                                            name="stateId"
                                            value={addressFormik.values.stateId || ''}
                                            onChange={(e) => {
                                                const state = states.find(
                                                    (s) => s.id.toString() === e.target.value,
                                                );
                                                handleStateChange(state || null, { name: 'stateId' } as ActionMeta<IState>);
                                            }}
                                        >
                                            <option value="">{translate('Select here')}</option>
                                            {states.map((state) => (
                                                <option key={state.id} value={state.id}>
                                                    {state.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <label className="mb-1 block text-sm font-medium">
                                            {translate('City')}/{translate('Town')}
                                        </label>
                                        <select
                                            className="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm disabled:opacity-50"
                                            name="cityId"
                                            value={addressFormik.values.cityId || ''}
                                            disabled={!addressFormik.values.stateId}
                                            onChange={(e) => {
                                                const city = cities.find(
                                                    (c) => c.id.toString() === e.target.value,
                                                );
                                                handleCityChange(city || null, { name: 'cityId' } as ActionMeta<ICity>);
                                            }}
                                        >
                                            <option value="">{translate('Select here')}</option>
                                            {cities.map((city) => (
                                                <option key={city.id} value={city.id}>
                                                    {city.name}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div>
                                        <label className="mb-1 block text-sm font-medium">
                                            {translate('Save As')}
                                        </label>
                                        <select
                                            className="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm"
                                            name="type"
                                            value={addressFormik.values.type}
                                            onChange={(e) => addressFormik.setFieldValue('type', e.target.value)}
                                        >
                                            <option value="home">{translate('home')}</option>
                                            <option value="office">{translate('office')}</option>
                                            <option value="other">{translate('other')}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label className="mb-1 block text-sm font-medium">
                                            {translate('Address')}
                                        </label>
                                        <Textarea
                                            {...addressFormik.getFieldProps('address')}
                                            placeholder={translate(
                                                'Please Enter Your Details Delivery Address In English (or Banglish eg - Aamar Sonar Bangla). দয়াকরে আপনার ডেলিভারি এড্রেস ইংরেজিতে (ইংরেজি ফন্টে/বাংলিশ যেমন - Aamar Sonar Bangla) লিখুন।',
                                            )}
                                            rows={3}
                                        />
                                    </div>
                                </>
                            )}
                            <Button
                                type="submit"
                                variant="primary"
                                className="w-full"
                                isLoading={customerFormik.isSubmitting}
                            >
                                {!initialCustomer
                                    ? (addressFormik.values.address
                                        ? translate('Create Customer & Address')
                                        : translate('Create Customer'))
                                    : translate('Update Customer')}
                            </Button>
                        </form>
                    </div>

                {/* Address Section */}
                {selectedCustomer && selectedCustomer.id !== 0 && (
                    <div>
                        <h6 className="text-sm font-semibold mb-3 text-foreground">
                            {translate('Delivery Addresses')}
                        </h6>
                        {addresses.length === 0 ? (
                            <div className="rounded-lg border border-dashed border-border py-6 text-center text-muted text-sm">
                                {translate('No addresses saved yet')}
                            </div>
                        ) : (
                            <div className="space-y-3">
                                {addresses.map((address) => (
                                    <div
                                        key={address.id}
                                        className="rounded-lg border border-border p-3"
                                    >
                                        <div className="flex items-start justify-between">
                                            <div className="flex-1">
                                                <div className="mb-1 flex items-center gap-2">
                                                    <span className="rounded bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">
                                                        {address.type || 'other'}
                                                    </span>
                                                    {address.isDefault === 1 && (
                                                        <span className="rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                                            {translate('Default')}
                                                        </span>
                                                    )}
                                                </div>
                                                <p className="text-sm">{address.fullAddress || address.address}</p>
                                                {address.direction && (
                                                    <p className="mt-1 text-xs text-muted">
                                                        {translate('Direction')}: {address.direction}
                                                    </p>
                                                )}
                                            </div>
                                            <div className="flex gap-1 shrink-0 ml-2">
                                                <button
                                                    type="button"
                                                    onClick={() => handleEditAddress(address)}
                                                    className="rounded p-1.5 text-muted hover:bg-background-hover hover:text-primary"
                                                    title={translate('Edit')}
                                                >
                                                    <FiEdit2 />
                                                </button>
                                                <button
                                                    type="button"
                                                    onClick={() => handleDeleteAddress(address.id)}
                                                    className="rounded p-1.5 text-muted hover:bg-background-hover hover:text-red-500"
                                                    title={translate('Delete')}
                                                >
                                                    <FiTrash2 />
                                                </button>
                                            </div>
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() => handleAddressSelect(address)}
                                            className="mt-2 w-full rounded bg-primary/10 py-2 text-sm font-medium text-primary hover:bg-primary/20"
                                        >
                                            {translate('Select This Address')}
                                        </button>
                                    </div>
                                ))}
                            </div>
                        )}
                        <Button
                            type="button"
                            variant="secondary"
                            className="w-full mt-3"
                            onClick={() => { handleNewAddress(); setViewMode('address-form'); }}
                        >
                            <AiOutlinePlus className="mr-2" />
                            {translate('Add New Address')}
                        </Button>
                    </div>
                )}
            </div>
        </div>
    );

    const renderAddressPickerView = () => (
        <div className="p-4 space-y-4">
            {selectedCustomer && (
                <div className="rounded-lg border border-border p-3 bg-background-primary-light">
                    <p className="font-medium text-sm">{selectedCustomer.name}</p>
                    {selectedCustomer.phone && (
                        <p className="text-xs text-muted">{selectedCustomer.phone}</p>
                    )}
                </div>
            )}

            {addresses.length === 0 ? (
                <div className="rounded-lg border border-dashed border-border py-6 text-center text-muted text-sm">
                    {translate('No addresses saved yet')}
                </div>
            ) : (
                <div className="space-y-3">
                    {addresses.map((address) => (
                        <div
                            key={address.id}
                            className="rounded-lg border border-border p-3"
                        >
                            <div className="flex items-start justify-between">
                                <div className="flex-1">
                                    <div className="mb-1 flex items-center gap-2">
                                        <span className="rounded bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">
                                            {address.type || 'other'}
                                        </span>
                                        {address.isDefault === 1 && (
                                            <span className="rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">
                                                {translate('Default')}
                                            </span>
                                        )}
                                    </div>
                                    <p className="text-sm">{address.fullAddress || address.address}</p>
                                    {address.direction && (
                                        <p className="mt-1 text-xs text-muted">
                                            {translate('Direction')}: {address.direction}
                                        </p>
                                    )}
                                </div>
                            </div>
                            <button
                                type="button"
                                onClick={() => handleAddressSelect(address)}
                                className="mt-2 w-full rounded bg-primary/10 py-2 text-sm font-medium text-primary hover:bg-primary/20"
                            >
                                {translate('Select This Address')}
                            </button>
                        </div>
                    ))}
                </div>
            )}

            <div className="flex gap-2 pt-2">
                <Button
                    type="button"
                    variant="secondary"
                    className="flex-1"
                    onClick={() => {
                        handleNewAddress();
                        setViewMode('address-form');
                    }}
                >
                    <AiOutlinePlus className="mr-2" />
                    {translate('Add New Address')}
                </Button>
                <Button
                    type="button"
                    variant="light"
                    className="flex-1"
                    onClick={handleClose}
                >
                    {translate('Cancel')}
                </Button>
            </div>
        </div>
    );

    const renderAddressForm = () => (
        <form onSubmit={addressFormik.handleSubmit} className="space-y-4 p-4">
            <div className="hidden">
                <label>{translate('Country')}</label>
                <select
                    name="countryId"
                    value={addressFormik.values.countryId || ''}
                    onChange={(e) => {
                        const country = countries.find(
                            (c: ICountry) => c.id.toString() === e.target.value,
                        );
                        if (country) handleCountryChange(country);
                    }}
                >
                    {countries.map((c: ICountry) => (
                        <option key={c.id} value={c.id}>
                            {c.name}
                        </option>
                    ))}
                </select>
            </div>

            <div>
                <label className="mb-2 block text-sm font-medium">
                    {translate('District')}
                    <span className="ml-1 text-red-500">*</span>
                </label>
                <select
                    className="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm"
                    name="stateId"
                    value={addressFormik.values.stateId || ''}
                    onChange={(e) => {
                        const state = states.find(
                            (s) => s.id.toString() === e.target.value,
                        );
                        handleStateChange(state || null, { name: 'stateId' } as ActionMeta<IState>);
                    }}
                >
                    <option value="">{translate('Select here')}</option>
                    {states.map((state) => (
                        <option key={state.id} value={state.id}>
                            {state.name}
                        </option>
                    ))}
                </select>
                {addressFormik.touched.stateId && addressFormik.errors.stateId && (
                    <span className="text-sm text-theme-alert">{addressFormik.errors.stateId}</span>
                )}
            </div>

            <div>
                <label className="mb-2 block text-sm font-medium">
                    {translate('City')}/{translate('Town')}
                    <span className="ml-1 text-red-500">*</span>
                </label>
                <select
                    className="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm disabled:opacity-50"
                    name="cityId"
                    value={addressFormik.values.cityId || ''}
                    disabled={!addressFormik.values.stateId}
                    onChange={(e) => {
                        const city = cities.find(
                            (c) => c.id.toString() === e.target.value,
                        );
                        handleCityChange(city || null, { name: 'cityId' } as ActionMeta<ICity>);
                    }}
                >
                    <option value="">{translate('Select here')}</option>
                    {cities.map((city) => (
                        <option key={city.id} value={city.id}>
                            {city.name}
                        </option>
                    ))}
                </select>
                {addressFormik.touched.cityId && addressFormik.errors.cityId && (
                    <span className="text-sm text-theme-alert">{addressFormik.errors.cityId}</span>
                )}
            </div>

            <div>
                <label className="mb-2 block text-sm font-medium">
                    {translate('Save As')}
                    <span className="ml-1 text-red-500">*</span>
                </label>
                <select
                    className="w-full rounded-lg border border-border bg-background px-3 py-2.5 text-sm"
                    name="type"
                    value={addressFormik.values.type}
                    onChange={(e) => addressFormik.setFieldValue('type', e.target.value)}
                >
                    <option value="home">{translate('home')}</option>
                    <option value="office">{translate('office')}</option>
                    <option value="other">{translate('other')}</option>
                </select>
                {addressFormik.touched.type && addressFormik.errors.type && (
                    <span className="text-sm text-theme-alert">{addressFormik.errors.type}</span>
                )}
            </div>

            <div>
                <label className="mb-2 block text-sm font-medium">
                    {translate('address')}
                    <span className="ml-1 text-red-500">*</span>
                </label>
                <Textarea
                    {...addressFormik.getFieldProps('address')}
                    placeholder={translate(
                        'Please Enter Your Details Delivery Address In English (or Banglish eg - Aamar Sonar Bangla). দয়াকরে আপনার ডেলিভারি এড্রেস ইংরেজিতে (ইংরেজি ফন্টে/বাংলিশ যেমন - Aamar Sonar Bangla) লিখুন।',
                    )}
                    rows={3}
                    touched={addressFormik.touched.address}
                    error={addressFormik.errors.address}
                />
            </div>

            <div className="flex gap-2 pt-2">
                <Button
                    type="button"
                    variant="light"
                    className="flex-1"
                    onClick={handleBack}
                >
                    {translate('Cancel')}
                </Button>
                <Button
                    type="submit"
                    variant="primary"
                    className="flex-1"
                    isLoading={addressFormik.isSubmitting}
                >
                    {editingAddress ? translate('Update') : translate('Save')}
                </Button>
            </div>
        </form>
    );

    const renderContent = () => {
        switch (viewMode) {
            case 'select':
                return renderSelectView();
            case 'manage':
                return renderManageView();
            case 'address-picker':
                return renderAddressPickerView();
            case 'address-form':
                return renderAddressForm();
        }
    };

    const modalContent = (
        <div className="flex max-h-[85vh] flex-col bg-white dark:bg-background-secondary">
            {renderHeader()}
            <div className="flex-1 overflow-y-auto">{renderContent()}</div>
        </div>
    );

    return isMobile ? (
        <Drawer.Root open={isOpen} onOpenChange={(open) => !open && handleClose()}>
            <Drawer.Portal>
                <Drawer.Overlay className="fixed inset-0 z-40 bg-black/40" />
                <Drawer.Content className="fixed bottom-0 left-0 right-0 z-50 flex max-h-[90vh] flex-col rounded-t-xl bg-white dark:bg-background-secondary outline-none">
                    <div className="mx-auto mt-3 h-1 w-12 shrink-0 rounded-full bg-gray-300 dark:bg-gray-600" />
                    {modalContent}
                </Drawer.Content>
            </Drawer.Portal>
        </Drawer.Root>
    ) : (
        <ModalWrapper isActive={isOpen} onClose={handleClose} size="md">
            {modalContent}
        </ModalWrapper>
    );
};

export default CustomerModal;
