import { PasswordUpdateState } from '../../../components/popups/auth/forget-password';
import { objectToFormData } from '../../../utils/ObjectFormData';
import { apiSlice } from './apiSlice';

const userApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['User'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            subscribe: builder.mutation<any, string>({
                query: (email) => ({
                    url: '/subscribe',
                    method: 'POST',
                    body: objectToFormData({ email }),
                }),
            }),

            updateProfileInfo: builder.mutation<
                any,
                { name: string; phone: string; avatar?: File }
            >({
                query: (body) => ({
                    url: '/user/update-info',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),

            updatePassword: builder.mutation<
                any,
                { password: string; passwordConfirmation: string }
            >({
                query: (body) => ({
                    url: '/user/update-password',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),

            // forget password apis
            sendValidationCode: builder.mutation<any, PasswordUpdateState>({
                query: (body) => ({
                    url: '/auth/resend-code',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),

            verifyCode: builder.mutation<any, PasswordUpdateState>({
                query: (body) => ({
                    url: '/auth/verify/code',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),
            resetPassword: builder.mutation<any, PasswordUpdateState>({
                query: (body) => ({
                    url: '/auth/password/reset',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),
            verifyOtp: builder.mutation<any,{ phone: string; otp: string }>({
                query: (body) => ({
                    url: '/verify-otp',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),
            
            // Phone-based authentication endpoints
            sendPasswordResetOtp: builder.mutation<any, { phone: string }>({
                query: (body) => ({
                    url: '/auth/password/reset-phone-otp',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),
            
            resetPasswordWithPhone: builder.mutation<any, { phone: string; otp: string; password: string; passwordConfirmation: string }>({
                query: (body) => ({
                    url: '/auth/password/reset-phone',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),
            
            resendPhoneOtp: builder.mutation<any, { phone: string }>({
                query: (body) => ({
                    url: '/auth/resend-phone-otp',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),
            
            verifyPhoneOtp: builder.mutation<any, { phone: string; otp: string }>({
                query: (body) => ({
                    url: '/auth/verify-phone-otp',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),
        }),
    });

export const {
    useSubscribeMutation,
    useUpdateProfileInfoMutation,
    useUpdatePasswordMutation,
    useSendValidationCodeMutation,
    useVerifyCodeMutation,
    useResetPasswordMutation,
    useVerifyOtpMutation,
    useSendPasswordResetOtpMutation,
    useResetPasswordWithPhoneMutation,
    useResendPhoneOtpMutation,
    useVerifyPhoneOtpMutation,
} = userApi;
