import { useFormik } from 'formik';
import React from 'react';
import toast from 'react-hot-toast';
import { object, string } from 'yup';
import { IDatabaseInfo } from '../../types';
import { postDatabaseInfo } from '../../utils/actions';
import Button from '../Button';
import Input from '../Input';

interface Props {
    onSubmit: () => void;
}

const validationSchema = object().shape({
    DB_DATABASE: string().required('Database name is required'),
    DB_USERNAME: string().required('Database username is required'),
    DB_PASSWORD: string(),
    DB_HOST: string().required('Database host is required'),
    DB_PORT: string().required('Database port is required'),
});

const initialValues: IDatabaseInfo = {
    DB_DATABASE: '',
    DB_USERNAME: '',
    DB_PASSWORD: '',
    DB_HOST: '',
    DB_PORT: '',
};

const DatabaseInfo = ({ onSubmit }: Props) => {
    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values) => {
            try {
                await postDatabaseInfo(values);
                onSubmit();
            } catch (error: any) {
                toast.error(error.response.data.message);
            }
        },
    });

    return (
        <form onSubmit={formik.handleSubmit} className="max-w-[500px] mx-auto">
            <div className="text-center mb-10">
                <h2 className="text-2xl font-medium mb-3">Database Setup</h2>
                <p>
                    Thank you for choosing EpikCart as your eCommerce solution.
                    Let’s move forward with the installation process!
                </p>
            </div>

            <div className="max-w-[450px] mx-auto space-y-4">
                <Input
                    label="Database Name"
                    placeholder="**** **** **** ****"
                    {...formik.getFieldProps('DB_DATABASE')}
                    error={formik.errors.DB_DATABASE}
                    touched={formik.touched.DB_DATABASE}
                />
                <Input
                    label="Database Username"
                    placeholder="**** **** **** ****"
                    {...formik.getFieldProps('DB_USERNAME')}
                    error={formik.errors.DB_USERNAME}
                    touched={formik.touched.DB_USERNAME}
                />
                <Input
                    label="Database Password"
                    placeholder="**** **** **** ****"
                    {...formik.getFieldProps('DB_PASSWORD')}
                    error={formik.errors.DB_PASSWORD}
                    touched={formik.touched.DB_PASSWORD}
                />
                <Input
                    label="Database Host"
                    placeholder="**** **** **** ****"
                    {...formik.getFieldProps('DB_HOST')}
                    error={formik.errors.DB_HOST}
                    touched={formik.touched.DB_HOST}
                />
                <Input
                    label="Database Port"
                    placeholder="**** **** **** ****"
                    {...formik.getFieldProps('DB_PORT')}
                    error={formik.errors.DB_PORT}
                    touched={formik.touched.DB_PORT}
                />
            </div>

            <div className="flex justify-center mt-7">
                <Button
                    type="submit"
                    disabled={formik.isSubmitting}
                    isLoading={formik.isSubmitting}
                >
                    NEXT STEP
                </Button>
            </div>
        </form>
    );
};

export default DatabaseInfo;
