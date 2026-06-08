import { useFormik } from 'formik';
import toast from 'react-hot-toast';
import { object, string } from 'yup';
import { PasswordUpdateState } from '.';
import { translate } from '../../../../utils/translate';
import Button from '../../../buttons/Button';
import Input from '../../../inputs/Input';
import InputGroup from '../../../inputs/InputGroup';
import Label from '../../../inputs/Label';
import { useSendPasswordResetOtpMutation } from '../../../../store/features/api/userApi';

// Bangladeshi phone validation regex
const phoneRegex = /^(8801[0-9]{9}|01[0-9]{9})$/;

interface Props {
    data: PasswordUpdateState;
    onSubmit: (values: PasswordUpdateState) => void;
}

const validationSchema = object().shape({
    phone: string()
        .matches(phoneRegex, translate('Provide a valid phone number (880XXXXXXXXXXX or 01XXXXXXXXX)'))
        .required(translate('Phone is required')),
});

const PhoneVerification = ({ onSubmit }: Props) => {
    const [sendPasswordResetOtp] = useSendPasswordResetOtpMutation();

    const formik = useFormik({
        initialValues: {
            phone: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                const res = await sendPasswordResetOtp(values).unwrap();
                toast.success(res.message);
                onSubmit({ phone: values.phone });
            } catch (error: any) {
                toast.error(error.data?.message || translate('Failed to send OTP'));
            }
        },
    });

    return (
        <form onSubmit={formik.handleSubmit}>
            <h3 className="ek-h3 mb-2">{translate('Forgot your password?')}</h3>
            <p className="mb-5">
                {translate(
                    'Don\'t worry. Please enter your phone number to receive a verification code via SMS',
                )}
            </p>

            <InputGroup>
                <Label>{translate('phone')}</Label>
                <Input
                    type="text"
                    placeholder={translate('Type your phone (880XXXXXXXXXXX or 01XXXXXXXXX)')}
                    error={formik.errors.phone}
                    touched={formik.touched.phone}
                    {...formik.getFieldProps('phone')}
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

export default PhoneVerification;
