import { useFormik } from 'formik';
import React from 'react';
import { object, string } from 'yup';
import Button from '../Button';
import Input from '../Input';

interface Props {
    onSubmit: (data: unknown) => void;
}

const validationSchema = object().shape({
    activationCode: string()
        .length(9, 'Activation code must be 9 characters')
        .required('Activation code is required'),
});

const Activation = ({ onSubmit }: Props) => {
    const formik = useFormik({
        initialValues: {
            activationCode: '',
        },
        validationSchema,
        onSubmit: (values) => {
            onSubmit(values);
        },
    });

    return (
        <form onSubmit={formik.handleSubmit} className="max-w-[500px] mx-auto">
            <div className="text-center mb-10">
                <h2 className="text-2xl font-medium mb-3">Activation Code</h2>
                <p>
                    Thank you for choosing EpikCart as your eCommerce solution.
                    Let’s move forward with the installation process!{' '}
                </p>
            </div>

            <div className="max-w-[420px] mx-auto">
                <Input
                    label="Activation Code"
                    placeholder="**** **** **** ****"
                    {...formik.getFieldProps('activationCode')}
                    error={formik.errors.activationCode}
                    touched={formik.touched.activationCode}
                />
            </div>

            <div className="flex justify-center mt-7">
                <Button typeof="">NEXT STEP</Button>
            </div>
        </form>
    );
};

export default Activation;
