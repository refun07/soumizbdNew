import { useFormik } from 'formik';
import React from 'react';
import { FaUser, FaUsers } from 'react-icons/fa';
import { number, object, string } from 'yup';
import { useAppContext } from '../../Context';
import { IAdminConfig, ICountry } from '../../types';
import { postAdminConfig } from '../../utils/actions';
import { cn } from '../../utils/cn';
import Button from '../Button';
import Checkbox from '../Checkbox';
import Input from '../Input';
import SelectInput from '../SelectInput';

interface Props {
    onSubmit: () => void;
}

const validationSchema = object().shape({
    name: string().required('Name is required'),
    email: string().email().required('Email is required'),
    password: string().required('Password is required'),
    currencyCode: string().required('Currency code is required'),
    currencySymbol: string().required('Currency symbol is required'),
    countryId: number().required('Country is required'),
    shopName: string().required('Shop name is required'),
    appMode: string()
        .oneOf(['singleVendor', 'multiVendor'], 'Select a vendor')
        .required('Vendor is required'),
    useInventory: number().oneOf([0, 1]),
});

const initialValues: IAdminConfig = {
    name: '',
    email: '',
    password: '',
    currencyCode: '',
    currencySymbol: '',
    countryId: 0,
    appMode: 'multiVendor',
    shopName: '',
    useInventory: 0,
};

const AdminConfiguration = ({ onSubmit }: Props) => {
    const { countries } = useAppContext();

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values) => {
            await postAdminConfig(values);
            onSubmit();
        },
    });

    return (
        <form onSubmit={formik.handleSubmit} className="max-w-[500px] mx-auto">
            <div className="text-center mb-10">
                <h2 className="text-2xl font-medium mb-3">Admin Setup</h2>
                <p>
                    Thank you for choosing EpikCart as your eCommerce solution.
                    Let’s move forward with the installation process!
                </p>
            </div>

            <div className="max-w-[450px] mx-auto space-y-4">
                <Input
                    label="Admin Name"
                    placeholder="Admin Name"
                    {...formik.getFieldProps('name')}
                    error={formik.errors.name}
                    touched={formik.touched.name}
                />
                <Input
                    label="Admin Email"
                    placeholder="Admin Email"
                    {...formik.getFieldProps('email')}
                    error={formik.errors.email}
                    touched={formik.touched.email}
                />
                <Input
                    label="Admin Password"
                    placeholder="Admin Password"
                    type="password"
                    {...formik.getFieldProps('password')}
                    error={formik.errors.password}
                    touched={formik.touched.password}
                />
                <Input
                    label="Currency Code"
                    placeholder="Currency Code"
                    {...formik.getFieldProps('currencyCode')}
                    error={formik.errors.currencyCode}
                    touched={formik.touched.currencyCode}
                />
                <Input
                    label="Currency Symbol"
                    placeholder="Currency Symbol"
                    {...formik.getFieldProps('currencySymbol')}
                    error={formik.errors.currencySymbol}
                    touched={formik.touched.currencySymbol}
                />
                <SelectInput
                    label="Country"
                    placeholder="Country"
                    options={countries}
                    value={formik.values.countryId}
                    error={formik.errors.countryId}
                    touched={formik.touched.countryId}
                    getOptionValue={(option) => option.id}
                    getOptionLabel={(option) => option.name}
                    onChange={(e: ICountry) => {
                        formik.setFieldValue('countryId', e.id);
                    }}
                />

                <Input
                    label="Shop Name"
                    placeholder="Shop Name"
                    {...formik.getFieldProps('shopName')}
                    error={formik.errors.shopName}
                    touched={formik.touched.shopName}
                />

                <div className="flex justify-between gap-4">
                    <label className="h-10 flex items-center">
                        Single/Multi vendor
                    </label>
                    <div className="w-full max-w-[270px] flex gap-5">
                        <button
                            type="button"
                            className={cn(
                                'rounded-md border border-border flex flex-col items-center px-3 pt-7 pb-4',
                                {
                                    'border-secondary-light':
                                        formik.values.appMode ===
                                        'singleVendor',
                                },
                            )}
                            onClick={() => {
                                formik.setFieldValue('appMode', 'singleVendor');
                            }}
                        >
                            <span className="text-3xl text-secondary-light">
                                <FaUser />
                            </span>
                            <p className="text-xs mt-4">Single Vendor</p>
                        </button>
                        <button
                            type="button"
                            className={cn(
                                'rounded-md border border-border flex flex-col items-center px-3 pt-7 pb-4',
                                {
                                    'border-secondary-light':
                                        formik.values.appMode === 'multiVendor',
                                },
                            )}
                            onClick={() => {
                                formik.setFieldValue('appMode', 'multiVendor');
                            }}
                        >
                            <span className="text-3xl text-secondary-light">
                                <FaUsers />
                            </span>
                            <p className="text-xs mt-4">Multi Vendor</p>
                        </button>
                    </div>
                </div>

                <Checkbox
                    toggler
                    label="Use Inventory"
                    placeholder="Use Inventory"
                    {...formik.getFieldProps('useInventory')}
                    onChange={(e) => {
                        formik.setFieldValue(
                            'useInventory',
                            e.target.checked ? 1 : 0,
                        );
                    }}
                    error={formik.errors.useInventory}
                    touched={formik.touched.useInventory}
                />
            </div>

            <div className="flex justify-center mt-7">
                <Button
                    isLoading={formik.isSubmitting}
                    disabled={formik.isSubmitting}
                >
                    FINISH SETUP
                </Button>
            </div>
        </form>
    );
};

export default AdminConfiguration;
