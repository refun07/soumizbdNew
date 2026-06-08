import { useFormik } from 'formik';
import toast from 'react-hot-toast';
import { object, string } from 'yup';
import { PasswordUpdateState } from '.';
import { useSendValidationCodeMutation } from '../../../../store/features/api/userApi';
import { translate } from '../../../../utils/translate';
import Button from '../../../buttons/Button';
import Input from '../../../inputs/Input';
import InputGroup from '../../../inputs/InputGroup';
import Label from '../../../inputs/Label';

interface Props {
    data: PasswordUpdateState;
    onSubmit: (values: PasswordUpdateState) => void;
}

const validationSchema = object().shape({
    email: string()
        .email('Provide a valid email')
        .required('Email is required'),
});

const EmailVerification = ({ onSubmit }: Props) => {
    const [sendValidationCode] = useSendValidationCodeMutation();

    const formik = useFormik({
        initialValues: {
            email: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                const res = await sendValidationCode(values).unwrap();
                toast.success(res.message);
                onSubmit(values);
            } catch (error) {}
        },
    });

    return (
        <form onSubmit={formik.handleSubmit}>
            <h3 className="ek-h3 mb-2">{translate('Forgot your password?')}</h3>
            <p className="mb-5">
                {translate(
                    'Don\'t worry. Please enter your email address to receive a verification code',
                )}
            </p>

            <InputGroup>
                <Label>{translate('email')}</Label>
                <Input
                    type="email"
                    placeholder={translate('Type your email')}
                    error={formik.errors.email}
                    touched={formik.touched.email}
                    {...formik.getFieldProps('email')}
                />
            </InputGroup>

            <Button
                type="submit"
                variant="primary"
                className="w-full font-bold mt-5 uppercase"
                isLoading={formik.isSubmitting}
                disabled={formik.isSubmitting}
            >
                {translate('Get Verification Code')}
            </Button>
        </form>
    );
};

export default EmailVerification;
