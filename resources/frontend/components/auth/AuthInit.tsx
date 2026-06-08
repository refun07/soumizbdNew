import { ReactNode, useEffect } from 'react';
import toast from 'react-hot-toast';
import { useSearchParams } from 'react-router-dom';
import { apiSlice } from '../../store/features/api/apiSlice';
import { useLazyGetCartProductsQuery } from '../../store/features/api/productApi';
import {
    useAuth,
    userLoggedIn,
    userLoggedOut,
} from '../../store/features/auth/authSlice';
import { getUser } from '../../store/features/auth/authThunks';
import { useAppDispatch } from '../../store/store';
import { STORAGE_KEYS } from '../../types';
import { cookies } from '../../utils/cookie';
import { translate } from '../../utils/translate';

const AuthInit = ({ children }: { children: ReactNode | ReactNode[] }) => {
    const [searchParams, setSearchParams] = useSearchParams();
    const dispatch = useAppDispatch();
    const { user } = useAuth();
    const [getCartProducts] = useLazyGetCartProductsQuery();

    const localAccessToken = localStorage.getItem(STORAGE_KEYS.AUTH_KEY);

    const getUserAndCart = async () => {
        if (localAccessToken) {
            try {
                await dispatch(getUser());
                return;
            } catch (err: any) {
                toast.error(translate('Session expired! Please login again.'));

                dispatch(userLoggedOut());
                dispatch(apiSlice.util.resetApiState());
            }
        }

        const guestUserId =
            Number(cookies.get(STORAGE_KEYS.GUEST_ID_KEY)) || undefined;

        // get cart products of the guest user
        const carts = await getCartProducts(guestUserId).unwrap();

        dispatch(
            userLoggedIn({
                isLoading: false,
                user: null,
                guestUserId,
                carts,
                countries: [],
                accessToken: null,
            }),
        );
    };

    useEffect(() => {
        const socialLogin = searchParams.get('socialLogin');
        searchParams.delete('accessToken');
        setSearchParams(searchParams);

        if (socialLogin) {
            toast.error(translate('Failed to Login'));
        }

        // if user does not exist in state, get user data
        if (!user) {
            getUserAndCart();
        }

        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, []);

    return <>{children}</>;
};

export { AuthInit };
