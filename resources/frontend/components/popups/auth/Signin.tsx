import { useFormik } from 'formik';
import { LiaTimesSolid } from 'react-icons/lia';
import { boolean, object, string } from 'yup';
import { signinUser } from '../../../store/features/auth/authThunks';
import {
    closePopup,
    togglePopup,
    usePopup,
} from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { IAuthPayload } from '../../../types/payload';
import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';
import Checkbox from '../../inputs/Checkbox';
import Input from '../../inputs/Input';
import InputGroup from '../../inputs/InputGroup';
import Label from '../../inputs/Label';
import ModalWrapper from '../ModalWrapper';
import toast from 'react-hot-toast';

// Bangladeshi phone validation regex
// Supports: 880XXXXXXXXXXX or 01XXXXXXXXX
const phoneRegex = /^(8801[0-9]{9}|01[0-9]{9})$/;

interface SigninFormValues extends IAuthPayload {}

const Signin = () => {
    const { popup } = usePopup();
    const dispatch = useAppDispatch();
    const isActive = popup === 'signin';
    
    const isPhoneLogin = window.config.generalSettings.customerLoginMethod === 'phone';

    const signinSchema = object().shape({
        ...(isPhoneLogin 
            ? {
                phone: string()
                    .matches(phoneRegex, translate('Provide a valid phone number (880XXXXXXXXXXX or 01XXXXXXXXX)'))
                    .required(translate('Phone is required')),
              }
            : {
                email: string()
                    .email(translate('Provide a valid email'))
                    .required(translate('Email is required')),
              }
        ),
        password: string().required(translate('Password is required')),
        remember: boolean(),
    });
    
    const getInitialValues = (): SigninFormValues => {
        if (isPhoneLogin) {
            return {
                phone: '',
                password: window.config.generalSettings.demoMode == 'On' ? '123456' : '',
                remember: false,
            };
        }
        return {
            email: window.config.generalSettings.demoMode == 'On' ? 'customer@epikcart.com' : '',
            password: window.config.generalSettings.demoMode == 'On' ? '123456' : '',
            remember: false,
        };
    };
    
    const formik = useFormik({
        validateOnChange: false,
        initialValues: getInitialValues(),
        validationSchema: signinSchema,
        onSubmit: async (values: SigninFormValues) => {
            try {
                const res = await dispatch(signinUser(values)).unwrap();

                if (res.authStatus === 'verify') {
                    dispatch(
                        togglePopup({
                            popup: 'user-verification',
                            popupProps: {
                                ...(isPhoneLogin ? { phone: values.phone } : { email: values.email }),
                            },
                        }),
                    );
                } else {
                    dispatch(closePopup());
                    toast.success(translate('Logged In Successfully!'));
                }
            } catch (err: any) {}
        },
    });

    return (
        <ModalWrapper isActive={isActive}>
            <button
                className="absolute right-5 top-5 text-xl text-white sm:text-primary z-[1]"
                onClick={() => dispatch(closePopup())}
            >
                <LiaTimesSolid />
            </button>
            <div className="theme-modal__body min-h-[595px] flex max-sm:flex-col bg-white p-0">
                <div className="bg-primary text-white flex flex-col justify-center">
                    <div className="flex gap-8 sm:flex-col max-sm:items-center sm:w-[242px] py-4 px-4 sm:px-9">
                        <div className="grow">
                            <h3 className="ek-h4">
                                {translate('Already Have an Account?')}
                            </h3>
                            <p className="mt-3">
                                {translate(
                                    'Login to get all your histories and order updates',
                                )}
                            </p>
                        </div>
                        <img
                            src="/images/auth-2.png"
                            alt=""
                            className="max-xs:max-w-[50px] max-sm:max-w-[100px] max-sm:mr-12"
                        />
                    </div>
                </div>
                <div className="flex-grow flex flex-col justify-center px-4 py-4 sm:p-10">
                    <form
                        onSubmit={formik.handleSubmit}
                        className="space-y-2.5"
                    >
                        {isPhoneLogin ? (
                            <InputGroup>
                                <Label htmlFor="phone">{translate('phone')}</Label>
                                <Input
                                    type="text"
                                    id="login-phone"
                                    placeholder={translate('Type Your Phone (880XXXXXXXXXXX or 01XXXXXXXXX)')}
                                    {...formik.getFieldProps('phone')}
                                    error={formik.errors.phone}
                                    touched={formik.touched.phone}
                                />
                            </InputGroup>
                        ) : (
                            <InputGroup>
                                <Label htmlFor="email">{translate('email')}</Label>
                                <Input
                                    type="email"
                                    id="login-email"
                                    placeholder={translate('Type Your Email')}
                                    {...formik.getFieldProps('email')}
                                />
                            </InputGroup>
                        )}
                        
                        <InputGroup>
                            <Label htmlFor="password">
                                {translate('password')}
                            </Label>
                            <Input
                                type="password"
                                id="login-password"
                                placeholder={translate('Type Your Password')}
                                {...formik.getFieldProps('password')}
                            />
                        </InputGroup>

                        <div className="!mt-4">
                            <Button
                                type="submit"
                                variant="primary"
                                className="w-full font-bold"
                                isLoading={formik.isSubmitting}
                            >
                                {translate('Sign In')}
                            </Button>
                        </div>

                        <Checkbox
                            className="!mt-5"
                            labelClassName="text-sm"
                            label={translate('Remember me')}
                            {...formik.getFieldProps('remember')}
                        />
                        <div className="">
                            {translate('Forgot The Password?')}
                            <button
                                type="button"
                                className="text-primary ml-1"
                                onClick={() =>
                                    dispatch(togglePopup('forget-password'))
                                }
                            >
                                {translate('Recover Now')}
                            </button>
                        </div>
                    </form>

                    {/* Social login section commented out - to be implemented later */}
                    {/* <div className="mt-6">
                        <div className="relative flex items-center justify-center mb-3">
                            <div className="px-3 bg-white relative z-[1]">
                                {translate('Login with Social')}
                            </div>
                            <span className="absolute border-b border-primary-14 w-full left-0 top-1/2 -translate-y-1/2"></span>
                        </div>

                        <SocialLogin />
                    </div> */}

                    <div className="text-center text-sm text-black/70 mt-5">
                        {translate('Don&#39;t Have an Account?')}
                        <button
                            onClick={() => dispatch(togglePopup('signup'))}
                            className="ml-1 text-primary"
                        >
                            {translate('Register Now')}
                        </button>
                    </div>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default Signin;
