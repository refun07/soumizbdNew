import { Middleware, isRejectedWithValue } from '@reduxjs/toolkit';
import toast from 'react-hot-toast';
import { apiSlice } from '../store/features/api/apiSlice';
import { userLoggedOut } from '../store/features/auth/authSlice';
import { translate } from '../utils/translate';

export const errorHandler: Middleware = (api) => (next) => (action) => {
    if (isRejectedWithValue(action)) {
        if (action.payload?.status === 401) {
            api.dispatch(userLoggedOut());
            api.dispatch(apiSlice.util.resetApiState());
        }

        if (typeof action.payload.data === 'string') {
            toast.error(translate('Something went wrong'));
        } else if (action.payload.isAxiosError) {
            toast.error(action.payload.response.data.message);
            return next(action.payload.toJSON());
        } else {
            toast.error(action.payload?.data?.message);
        }
    }

    return next(action);
};
