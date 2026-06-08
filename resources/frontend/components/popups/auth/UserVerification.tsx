import { useFormik } from 'formik';
import { useEffect } from 'react';
import { LiaTimesSolid } from 'react-icons/lia';
import toast from 'react-hot-toast';
import { object, string } from 'yup';
import { verifyUser } from '../../../store/features/auth/authThunks';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { useVerifyPhoneOtpMutation } from '../../../store/features/api/userApi';
import { IAuthPayload } from '../../../types/payload';
import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';
import InputGroup from '../../inputs/InputGroup';
import Label from '../../inputs/Label';
import OTPInput from '../../inputs/OTPInput';
import ModalWrapper from '../ModalWrapper';

const UserVerificationPopup = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const isPhoneLogin = window.config.generalSettings.customerLoginMethod === 'phone';
    const [verifyPhoneOtp] = useVerifyPhoneOtpMutation();

    const isActive = popup === 'user-verification';
    
    const isPhoneVerification = !!(popupProps?.phone || isPhoneLogin);
    const identifier = popupProps?.phone || popupProps?.email;

    const getInitialValues = (): IAuthPayload => {
        if (isPhoneVerification) {
            return {
                phone: '',
                code: '',
            };
        }
        return {
            email: '',
            code: '',
        };
    };

    const validationSchema = object().shape({
        ...(isPhoneVerification
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
        initialValues: getInitialValues(),
        validationSchema,
        onSubmit: async (values) => {
            try {
                if (isPhoneVerification) {
                    await verifyPhoneOtp({
                        phone: values.phone || '',
                        otp: values.code || '',
                    }).unwrap();
                    toast.success(translate('Phone verified successfully'));
                } else {
                    await dispatch(verifyUser(values)).unwrap();
                }
                dispatch(closePopup());
            } catch (error: any) {
                toast.error(error.data?.message || translate('Verification failed'));
            }
        },
    });

    useEffect(() => {
        if (isPhoneVerification && popupProps?.phone) {
            formik.setFieldValue('phone', popupProps.phone);
        } else if (popupProps?.email) {
            formik.setFieldValue('email', popupProps.email);
        }
    }, [popupProps]);

    useEffect(() => {
        if (isActive) {
            formik.setFieldValue('code', '');
        }
    }, [isActive]);

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
                                {isPhoneVerification
                                    ? translate('Verify Your Phone')
                                    : translate('Verify Your Email')
                                }
                            </h3>
                            <p className="mt-3">
                                {isPhoneVerification
                                    ? translate('Verify your phone number to continue.')
                                    : translate('Verify your email address to continue.')
                                }
                            </p>
                        </div>
                        <img
                            src="/images/auth-2.png"
                            alt=""
                            className="max-xs:max-w-[50px] max-sm:max-w-[100px] max-sm:mr-12"
                        />
                    </div>
                </div>
                <form
                    className="flex-grow flex flex-col justify-center px-4 py-4 sm:p-10"
                    onSubmit={formik.handleSubmit}
                >
                    <h3 className="ek-h3 mb-2">
                        {isPhoneVerification
                            ? translate('Verify Your Phone')
                            : translate('Verify Your Email')
                        }
                    </h3>
                    <p className="mb-5">
                        {isPhoneVerification
                            ? translate('Please enter the code sent to your phone via SMS')
                            : translate('Please enter the code sent to your email')
                        }
                    </p>
                    
                    {identifier && (
                        <p className="mb-5 text-sm text-gray-600">
                            {isPhoneVerification
                                ? `${translate('Code sent to')}: ${identifier}`
                                : `${translate('Code sent to')}: ${identifier}`
                            }
                        </p>
                    )}

                    <InputGroup>
                        <Label htmlFor="code">{translate('code')}</Label>
                        <OTPInput
                            value={formik.values.code || ''}
                            onChange={(value) =>
                                formik.setFieldValue('code', value)
                            }
                        />
                    </InputGroup>

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
            </div>
        </ModalWrapper>
    );
};

export default UserVerificationPopup;
