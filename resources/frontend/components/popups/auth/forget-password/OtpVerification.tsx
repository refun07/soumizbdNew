import { useFormik } from 'formik';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import { object, string } from 'yup';
import { PasswordUpdateState } from '.';
import { 
    useVerifyCodeMutation,
    useResendPhoneOtpMutation 
} from '../../../../store/features/api/userApi';
import { translate } from '../../../../utils/translate';
import Button from '../../../buttons/Button';
import InputGroup from '../../../inputs/InputGroup';
import Label from '../../../inputs/Label';
import OTPInput from '../../../inputs/OTPInput';

interface Props {
    data: PasswordUpdateState;
    onSubmit: (values: PasswordUpdateState) => void;
}

const OtpVerification = ({ data, onSubmit }: Props) => {
    const isPhoneLogin = window.config.generalSettings.customerLoginMethod === 'phone';
    const [verifyCode] = useVerifyCodeMutation();
    const [resendPhoneOtp] = useResendPhoneOtpMutation();

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
        code: string().length(6, translate('Code must be 6 digits')).required(translate('Code is required')),
    });

    const formik = useFormik({
        initialValues: {
            email: '',
            phone: '',
            code: '',
        },
        validationSchema,
        onSubmit: async (values) => {
            try {
                const res = await verifyCode(values).unwrap();
                toast.success(res.message);
                onSubmit(values);
            } catch (error: any) {
                toast.error(error.data?.message || translate('Verification failed'));
            }
        },
    });

    useEffect(() => {
        if (isPhoneLogin) {
            formik.setFieldValue('phone', data.phone || '');
        } else {
            formik.setFieldValue('email', data.email || '');
        }
    }, [data]);

    const handleResendOtp = async () => {
        if (!isPhoneLogin) return;
        
        try {
            const res = await resendPhoneOtp({ phone: data.phone || '' }).unwrap();
            toast.success(res.message);
        } catch (error: any) {
            toast.error(error.data?.message || translate('Failed to resend OTP'));
        }
    };

    return (
        <form onSubmit={formik.handleSubmit}>
            <h3 className="ek-h3 mb-2">
                {isPhoneLogin 
                    ? translate('Verify Your Phone') 
                    : translate('Verify Your Email')
                }
            </h3>
            <p className="mb-5">
                {isPhoneLogin
                    ? translate('Please enter the code sent to your phone via SMS to verify')
                    : translate('Please enter the code sent to your email to verify your email')
                }
            </p>

            <InputGroup>
                <Label htmlFor="code">{translate('code')}</Label>
                <OTPInput
                    value={formik.values.code}
                    onChange={(value) => formik.setFieldValue('code', value)}
                />
            </InputGroup>

            {isPhoneLogin && (
                <div className="mt-4 text-center">
                    <button
                        type="button"
                        onClick={handleResendOtp}
                        className="text-primary text-sm underline"
                    >
                        {translate('Resend OTP')}
                    </button>
                </div>
            )}

            <Button
                type="submit"
                variant="primary"
                className="w-full font-bold mt-5 uppercase"
                isLoading={formik.isSubmitting}
                disabled={formik.isSubmitting}
            >
                {translate('Verify')}
            </Button>
        </form>
    );
};

export default OtpVerification;
