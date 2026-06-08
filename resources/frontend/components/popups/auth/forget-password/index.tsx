import { ReactElement, useState } from 'react';
import { LiaTimesSolid } from 'react-icons/lia';
import {
    closePopup,
    usePopup,
} from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import { translate } from '../../../../utils/translate';
import ModalWrapper from '../../ModalWrapper';
import EmailVerification from './EmailVerification';
import PhoneVerification from './PhoneVerification';
import OtpVerification from './OtpVerification';
import ResetPassword from './ResetPassword';

type TStep = 'email' | 'phone' | 'OTP' | 'password';
export interface PasswordUpdateState {
    email?: string;
    phone?: string;
    code?: string;
    password?: string;
    passwordConfirmation?: string;
}

const ForgetPasswordPopup = () => {
    const [currentStep, setCurrentStep] = useState(1);
    const [data, setData] = useState<PasswordUpdateState>({});
    const { popup } = usePopup();
    const dispatch = useAppDispatch();
    
    const isPhoneLogin = window.config.generalSettings.customerLoginMethod === 'phone';

    const isActive = popup === 'forget-password';

    const handleSubmit = (newData: Partial<PasswordUpdateState>) => {
        setData((prev) => ({ ...prev, ...newData }));

        if (currentStep === steps.length) {
            setCurrentStep(1);
            setData({});
            dispatch(closePopup());
            return;
        }
        setCurrentStep((prev) => prev + 1);
    };

    const steps: TStep[] = isPhoneLogin ? ['phone', 'OTP', 'password'] : ['email', 'OTP', 'password'];
    const stepComponents: Record<TStep, ReactElement> = {
        email: <EmailVerification data={data} onSubmit={handleSubmit} />,
        phone: <PhoneVerification data={data} onSubmit={handleSubmit} />,
        OTP: <OtpVerification data={data} onSubmit={handleSubmit} />,
        password: <ResetPassword data={data} onSubmit={handleSubmit} />,
    };

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
                                {translate('Recover Password')}
                            </h3>
                            <p className="mt-3">
                                {isPhoneLogin 
                                    ? translate('Recover your password by entering your phone number.')
                                    : translate('Recover your password by entering your email address.')
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
                <div className="flex-grow flex flex-col justify-center px-4 py-4 sm:p-10">
                    {stepComponents[steps[currentStep - 1]]}
                </div>
            </div>
        </ModalWrapper>
    );
};

export default ForgetPasswordPopup;
