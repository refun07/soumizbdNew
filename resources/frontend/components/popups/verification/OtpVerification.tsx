import { useFormik } from 'formik';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { LiaTimesSolid } from 'react-icons/lia';
import { setCheckoutData, useCheckoutData } from '../../../store/features/checkout/checkoutSlice';
import { useDispatch } from 'react-redux';
import { useVerifyOtpMutation } from '../../../store/features/api/userApi';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAuth } from '../../../store/features/auth/authSlice';
import { object, string } from 'yup';
import { reSendOTP } from '../../../../backend/react/utils/actions';

import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';
import OTPInput from '../../inputs/OTPInput';
import ModalWrapper from '../ModalWrapper';

const OTP_EXPIRY_SECONDS = 180; // 3 minutes

const OtpVerification = () => {
  const dispatch = useDispatch();
  const { popup } = usePopup();
  const { customerDetails, otpExpiryTimestamp } = useCheckoutData();
  const isActive = popup === 'otp-verification';

  const [verifyOtp] = useVerifyOtpMutation();
  const [timeLeft, setTimeLeft] = useState(0);
  const [expired, setExpired] = useState(false);

  useEffect(() => {
    if (!isActive || !otpExpiryTimestamp) return;

    const updateRemainingTime = () => {
      const now = Date.now();
      const diff = Math.floor((otpExpiryTimestamp - now) / 1000);
      setTimeLeft(diff);
      if (diff <= 0) {
        setExpired(true);
        setTimeLeft(0);
      }
    };

    updateRemainingTime(); // initialize immediately

    const interval = setInterval(() => {
      updateRemainingTime();
    }, 1000);

    return () => clearInterval(interval);
  }, [isActive, otpExpiryTimestamp]);

  const formik = useFormik({
    initialValues: {
      phone: customerDetails.phone ?? '',
      otp: '',
    },
    validationSchema: object().shape({
      otp: string().length(6, 'OTP must be 6 digits').required('Code is required'),
    }),
    onSubmit: async (values) => {
      if (expired) {
        toast.error('OTP expired. Please resend.');
        return;
      }

      try {
        const res = await verifyOtp(values).unwrap();
        toast.success(res.message);
        dispatch(setCheckoutData({ isVerified: true }));
        dispatch(closePopup());
      } catch (error: any) {
        toast.error(error?.data?.message || 'OTP verification failed');
      }
    },
  });

  const handleResend = async () => {
    try {
      const newExpiry = Date.now() + 15 * 60 * 1000;
      await reSendOTP({
        name: customerDetails.name,
        phone: customerDetails.phone,
      }).then((data)=>{
        dispatch(setCheckoutData({
            otpExpiryTimestamp: newExpiry,
        }));
        if(data.success){
            toast.success(data.message);
        }else{
            toast.error(data.message);
        }
      });

    } catch (err) {
      toast.error('Failed to resend OTP');
    }
  };

  const formatTime = (seconds: number) => {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
  };

    return (
        <ModalWrapper size="md" isActive={isActive} className="rounded-card">
            <button
                className="absolute right-5 top-5 text-xl text-white sm:text-primary z-[1]"
                onClick={() => dispatch(closePopup())}
            >
                <LiaTimesSolid />
            </button>
            <div className="theme-modal__body min-h-[595px] flex max-sm:flex-col bg-white p-0">
                <div className="bg-primary text-white flex flex-col justify-center">
                    <div className="flex gap-8 sm:flex-col max-sm:items-center sm:w-[300px] py-4 px-4 sm:px-9">
                        <div className="grow">
                            <h3 className="ek-h4">{translate('Verify Your Contact')}</h3>
                            <p className="mt-3">
                                {translate('Please enter the code sent to your phone to verify.')}
                            </p>
                        </div>
                        <img src="/images/auth-2.png" alt="" />
                    </div>
                </div>
                <div className="flex-grow flex flex-col justify-center px-4 py-4 sm:p-10">
                    <form onSubmit={formik.handleSubmit}>
                        <OTPInput


                           value={formik.values.otp}
                            onChange={(value) => formik.setFieldValue('otp', value)}
                            // error={formik.touched.otp && formik.errors.otp}
                        />
                        <div className="mt-4 text-sm text-zinc-600">
                            {expired ? (
                                <span className="text-red-500">OTP expired</span>
                            ) : (
                                <>Expires in <strong>{formatTime(timeLeft)}</strong></>
                            )}
                        </div>
                        <div className="mt-6 flex justify-between items-center">
                            <Button
                                type="submit"
                                variant="primary"
                                disabled={expired}
                            >
                                {translate('Verify')}
                            </Button>

                            {expired && (
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={handleResend}
                                >
                                    Resend
                                </Button>
                            )}
                        </div>
                    </form>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default OtpVerification;
