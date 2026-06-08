import { useEffect } from 'react';
import { useDispatch } from 'react-redux';
import HomeThemeOne from './HomeThemeOne';
import HomeThemeThree from './HomeThemeThree';
import HomeThemeTwo from './HomeThemeTwo';
import { useCheckAbandonedCartQuery } from '../../store/features/api/productApi';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { updateCartProducts, useAuth } from '../../store/features/auth/authSlice';
import { STORAGE_KEYS } from '../../types';
import { cookies } from '../../utils/cookie';

const ABANDONED_CART_POPUP_SHOWN = 'abandoned_cart_popup_shown';

const HomeIndex = () => {
    const dispatch = useDispatch();
    const { user, guestUserId } = useAuth();

    const guestId = cookies.get(STORAGE_KEYS.GUEST_ID_KEY);
    const { data: abandonedCartData } = useCheckAbandonedCartQuery(
        { guestUserId: guestUserId || (user ? undefined : Number(guestId)) },
        { skip: !guestUserId && !user },
    );

    useEffect(() => {
        const hasAbandonedCart = abandonedCartData?.is_abandoned;
        const hasShownPopup = sessionStorage.getItem(ABANDONED_CART_POPUP_SHOWN);

        if (hasAbandonedCart && !hasShownPopup) {
            // Store cart items for the popup to display
            if (abandonedCartData?.carts) {
                dispatch(updateCartProducts(abandonedCartData.carts));
            }
            dispatch(togglePopup('abandoned-cart'));
        }
    }, [abandonedCartData, dispatch]);

    if (window.config.defaultTheme === 'theme-one') {
        return <HomeThemeOne />;
    } else if (window.config.defaultTheme === 'theme-two') {
        return <HomeThemeTwo />;
    } else if (window.config.defaultTheme === 'theme-three') {
        return <HomeThemeThree />;
    }
    return <HomeThemeOne />;
};

export default HomeIndex;
