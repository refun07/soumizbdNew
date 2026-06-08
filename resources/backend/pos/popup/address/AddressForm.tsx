import { useFormik } from 'formik';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { ActionMeta } from 'react-select';
import { number, object, string } from 'yup';
import Button from '../../../react/components/inputs/Button';
import SelectInput from '../../../react/components/inputs/SelectInput';
import Textarea from '../../../react/components/inputs/Textarea';
import { objectToFormData } from '../../../react/utils/ObjectFormData';
import { translate } from '../../../react/utils/translate';
import { IAddress, ICity, ICountry, IState } from '../../types';
import { addAddress, getCities, getStates } from '../../utils/actions';

interface Props {
    customerId: number | undefined;
    onSuccess: (address: IAddress) => void;
}

interface FormValues {
    id: number | null;
    countryId: number | null;
    stateId: number | null;
    cityId: number | null;
    address: string;
}

const initialValues: FormValues = {
    id: null,
    countryId: null,
    stateId: null,
    cityId: null,
    address: '',
};

const validationSchema = object().shape({
    id: number().required('Id is required'),
    countryId: number().required('Country is required'),
    stateId: number().required('District is required'),
    cityId: number().nullable(),
    address: string().required('Address details are required'),
});

const AddressForm = ({ customerId, onSuccess }: Props) => {
    const countries = window.config?.countries;
    const bangladeshCountry =
        countries?.find((country: ICountry) => country.code === 'BD') ||
        countries?.find((country: ICountry) => country.name === 'Bangladesh');
    const bangladeshCountryId = bangladeshCountry?.id || null;

    const [states, setStates] = useState<IState[]>([]);
    const [cities, setCities] = useState<ICity[]>([]);

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values, helpers) => {
            try {
                const data = await addAddress(objectToFormData(values));
                onSuccess(data);
                helpers.resetForm();
            } catch (error) {
                toast.error(
                    error.response?.data?.message || 'Failed to add address',
                );
            }
        },
    });

    useEffect(() => {
        formik.setFieldValue('id', customerId);
    }, [customerId]); // eslint-disable-line react-hooks/exhaustive-deps

    useEffect(() => {
        const initialize = async () => {
            if (!bangladeshCountryId) return;

            formik.setFieldValue('countryId', bangladeshCountryId);
            formik.setFieldValue('stateId', null);
            formik.setFieldValue('cityId', null);
            setCities([]);

            try {
                const stateList = await getStates(bangladeshCountryId);
                setStates(stateList);
            } catch (error) {
                setStates([]);
            }
        };

        initialize();
    }, [bangladeshCountryId]); // eslint-disable-line react-hooks/exhaustive-deps

    const handleCountryChange = async (option: ICountry) => {
        formik.setFieldValue('countryId', option.id);
        formik.setFieldValue('stateId', null);
        formik.setFieldValue('cityId', null);

        setCities([]);
        const states = await getStates(option.id);
        setStates(states);
    };

    const handleStateChange = async (
        option: IState | null,
        { name }: ActionMeta<IState>,
    ) => {
        formik.setFieldValue(name!, option?.id ?? null);
        formik.setFieldValue('cityId', null);

        if (!option) {
            setCities([]);
            return;
        }

        const cities = await getCities(option.id);
        setCities(cities);
    };

    const handleCityChange = (
        option: ICity | null,
        { name }: ActionMeta<ICity>,
    ) => {
        formik.setFieldValue(name!, option?.id ?? null);
    };

    return (
        <form onSubmit={formik.handleSubmit} className="space-y-4">
            <div className="hidden">
                <label className="min-w-[100px] py-2">
                    {translate('Country')}
                    <span className="text-theme-alert ml-1">*</span>
                </label>
                <SelectInput
                    {...formik.getFieldProps('countryId')}
                    name="countryId"
                    options={countries}
                    groupClassName="grow"
                    onChange={handleCountryChange}
                    value={formik.values.countryId || ''}
                    getOptionValue={(option: ICountry) => option.id.toString()}
                    getOptionLabel={(option: ICountry) => option.name}
                    touched={formik.touched.countryId}
                    error={formik.errors.countryId}
                />
            </div>
            <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('District')}
                    <span className="text-theme-alert ml-1">*</span>
                </label>
                <SelectInput
                    {...formik.getFieldProps('stateId')}
                    name="stateId"
                    options={states}
                    groupClassName="grow"
                    onChange={handleStateChange}
                    value={formik.values.stateId || ''}
                    getOptionValue={(option: IState) => option.id.toString()}
                    getOptionLabel={(option: IState) => option.name}
                    touched={formik.touched.stateId}
                    error={formik.errors.stateId}
                />
            </div>
            <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('City')}/{translate('Town')}
                </label>
                <SelectInput
                    {...formik.getFieldProps('cityId')}
                    name="cityId"
                    options={cities}
                    groupClassName="grow"
                    isDisabled={!formik.values.stateId}
                    onChange={handleCityChange}
                    value={formik.values.cityId || ''}
                    getOptionValue={(option: ICity) => option.id.toString()}
                    getOptionLabel={(option: ICity) => option.name}
                    touched={formik.touched.cityId}
                    error={formik.errors.cityId}
                />
            </div>
            <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('Address')}  {translate('Details')}
                    <span className="text-theme-alert ml-1">*</span>
                </label>
                <Textarea
                    {...formik.getFieldProps('address')}
                    classNames={{
                        group: 'grow',
                        inputWrapper: 'max-w-none',
                    }}
                    rows={4}
                    touched={formik.touched.address}
                    error={formik.errors.address}
                />
            </div>

            <div>
                <Button as="button" type="submit" variant="secondary">
                    {translate('Add Address')}
                </Button>
            </div>
        </form>
    );
};

export default AddressForm;
