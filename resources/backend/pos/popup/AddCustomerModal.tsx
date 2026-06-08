import { useFormik } from 'formik';
import { useState } from 'react';
import toast from 'react-hot-toast';
import { AiOutlinePlus } from 'react-icons/ai';
import { LiaTimesSolid } from 'react-icons/lia';
import { object, string } from 'yup';
import Button from '../../react/components/inputs/Button';
import Input from '../../react/components/inputs/Input';
import { objectToFormData } from '../../react/utils/ObjectFormData';
import { translate } from '../../react/utils/translate';
import { ICustomer } from '../types';
import { addCustomer } from '../utils/actions';
import ModalWrapper from './ModalWrapper';

interface Props {
    onSuccess: (customer: ICustomer) => void;
}

interface FormValues {
    name: string;
    email: string;
    phone?: string;
    password?: string;
}

const initialValues: FormValues = {
    name: '',
    email: '',
    phone: '',
    password: '',
};

const validationSchema = object().shape({
    name: string().required('Name is required'),
    email: string(),
    phone: string(),
    password: string(),
});

const AddCustomerModal = ({ onSuccess }: Props) => {
    const [isActive, setIsActive] = useState(false);

    const handleClose = () => {
        setIsActive(false);
    };

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values, helpers) => {
            try {
                const data = await addCustomer(objectToFormData(values));
                setIsActive(false);
                onSuccess(data.newCustomer);
                toast.success('Customer created successfully', {
                    position: 'top-right',
                });
                helpers.resetForm();
            } catch (error) {
                toast.error(
                    error.response?.data?.message || 'Failed to add customer',
                );
            }
        },
    });

    return (
        <>
            <Button
                variant="primary"
                className="text-2xl w-10"
                onClick={() => setIsActive(true)}
            >
                <AiOutlinePlus />
            </Button>

            <ModalWrapper isActive={isActive} onClose={handleClose}>
                <form onSubmit={formik.handleSubmit}>
                    <div className="theme-modal__header">
                        <h5>{translate('Add Customer')}</h5>

                        <button
                            className="text-xl text-white sm:text-secondary-light"
                            onClick={handleClose}
                        >
                            <LiaTimesSolid />
                        </button>
                    </div>

                    <div className="theme-modal__body space-y-3">
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Name')}
                                <span className="text-theme-alert ml-1">*</span>
                            </label>
                            <Input
                                {...formik.getFieldProps('name')}
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.name}
                                error={formik.errors.name}
                            />
                        </div>
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Email')}
                            </label>
                            <Input
                                {...formik.getFieldProps('email')}
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.email}
                                error={formik.errors.email}
                            />
                        </div>
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Phone')}
                            </label>
                            <Input
                                {...formik.getFieldProps('phone')}
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.phone}
                                error={formik.errors.phone}
                            />
                        </div>
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Password')}
                            </label>
                            <Input
                                {...formik.getFieldProps('password')}
                                type="password"
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.password}
                                error={formik.errors.password}
                            />
                        </div>
                    </div>

                    <div className="theme-modal__footer">
                        <Button
                            as="button"
                            type="button"
                            variant="light"
                            onClick={handleClose}
                            className="mr-3"
                        >
                            {translate('Cancel')}
                        </Button>
                        <Button variant="primary" as="button" type="submit">
                            {translate('Submit')}
                        </Button>
                    </div>
                </form>
            </ModalWrapper>
        </>
    );
};

export default AddCustomerModal;
