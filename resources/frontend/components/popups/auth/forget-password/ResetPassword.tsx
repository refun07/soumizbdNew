import { useFormik } from 'formik';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import { object, ref, string } from 'yup';
import { PasswordUpdateState } from '.';
import { 
    useResetPasswordMutation,
    useResetPasswordWithPhoneMutation 
} from '../../../../store/features/api/userApi';
import { translate } from '../../../../utils/translate';
import Button from '../../../buttons/Button';
import Input from '../../../inputs/Input';
import InputGroup from '../../../inputs/InputGroup';
import Label from '../../../inputs/Label';

interface Props {
    data: PasswordUpdateState;
    onSubmit: (values: PasswordUpdateState) => void;
}

const ResetPassword = ({ data, onSubmit }: Props) => {
    const isPhoneLogin = window.config.generalSettings.customerLoginMethod === 'phone';
    const [resetPassword] = useResetPasswordMutation();
    const [resetPasswordWithPhone] = useResetPasswordWithPhoneMutation();

    const validationSchema = object().shape({
        ...(isPhoneLogin 
            ? {
                phone: string().required(translate('Phone is required')),
              }
            : {
                email: string()
                    .email(translate('Provide a valid email'))
                    .required(translate('Email is required')),
              }
        ),
        code: string().required(translate('Code is required')),
        password: string()
            .min(6, translate('Password must be at least 6 characters'))
            .required(translate('Password is required')),
        passwordConfirmation: string()
            .required(translate('Password Confirmation is required'))
            .oneOf([ref('password')], translate('Passwords must match')),
    });

    const formik = useFormik({
        initialValues: {
            email: '',
            phone: '',
            code: '',
            password: '',
            passwordConfirmation: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                if (isPhoneLogin) {
                    const res = await resetPasswordWithPhone({
                        phone: values.phone,
                        otp: values.code,
                        password: values.password,
                        passwordConfirmation: values.passwordConfirmation,
                    }).unwrap();
                    toast.success(res.message);
                } else {
                    const res = await resetPassword(values).unwrap();
                    toast.success(res.message);
                }
                onSubmit(values);
            } catch (error: any) {
                toast.error(error.data?.message || translate('Failed to reset password'));
            }
        },
    });

    useEffect(() => {
        if (isPhoneLogin) {
            formik.setFieldValue('phone', data.phone || '');
        } else {
            formik.setFieldValue('email', data.email || '');
        }
        formik.setFieldValue('code', data.code || '');
    }, [data]);

    return (
        <form onSubmit={formik.handleSubmit}>
            <h3 className="ek-h3 mb-2">{translate('Set New Password')}</h3>
            <p className="mb-5">
                {translate('Please enter your new password and confirm it.')}
            </p>

            <InputGroup>
                <Label>{translate('password')}</Label>
                <Input
                    type="password"
                    toggleBtn={false}
                    placeholder={translate('Type your password')}
                    error={formik.errors.password}
                    touched={formik.touched.password}
                    {...formik.getFieldProps('password')}
                />
            </InputGroup>

            <InputGroup className="mt-5">
                <Label>{translate('Confirm Password')}</Label>
                <Input
                    type="password"
                    toggleBtn={false}
                    placeholder={translate('Type password again')}
                    error={formik.errors.passwordConfirmation}
                    touched={formik.touched.passwordConfirmation}
                    {...formik.getFieldProps('passwordConfirmation')}
                />
            </InputGroup>

            <Button
                type="submit"
                variant="primary"
                className="w-full font-bold mt-5 uppercase"
                isLoading={formik.isSubmitting}
                disabled={formik.isSubmitting}
            >
                {translate('Change Password')}
            </Button>
        </form>
    );
};

export default ResetPassword;
