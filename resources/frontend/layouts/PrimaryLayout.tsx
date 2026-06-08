import { Suspense, useEffect, useMemo } from 'react';
import { useTranslation } from 'react-i18next';
import { LuShoppingCart } from 'react-icons/lu';
import { Outlet, useLocation } from 'react-router-dom';
import Popups from '../components/popups';
import { setAuthData, useAuth } from '../store/features/auth/authSlice';
import { closePopup, togglePopup } from '../store/features/popup/popupSlice';
import { useAppDispatch } from '../store/store';
import { currencyFormatter } from '../utils/numberFormatter';
import Copyright from './components/Copyright';
import Footer from './components/Footer';
import Header from './components/header';
import FloatingChatButtons from './components/FloatingChatButtons';

const PrimaryLayout = () => {
    const { i18n } = useTranslation();
    const dispatch = useAppDispatch();
    const location = useLocation();
    const { carts } = useAuth();

    useEffect(() => {
        dispatch(
            setAuthData({
                language: window.config.languages.find(
                    (lang) => lang.code === i18n.language,
                ),
            }),
        );
    }, []);

    useEffect(() => {
        dispatch(closePopup());
    }, [location.pathname]);

    const [totalCartPrice, totalCartQuantity] = useMemo(() => {
        return carts.reduce(
            (acc, item) => {
                acc[0] += item.variation!.discountedBasePrice * item.qty;
                acc[1] += item.qty;
                return acc;
            },
            [0, 0],
        );
    }, [carts]);

    return (
        <>
            <Header />
            <main>
                {/* floating cart button */}
                {(window.config.defaultTheme === 'theme-two' || window.config.defaultTheme === 'theme-one') && window.config.cartVisibilityToggle === '1' ? (
                    <div className="fixed top-1/3 right-0 z-[2]">
                        <button
                            className="overflow-hidden rounded-l-[4px] bg-[#b3052a] text-white shadow-[0_8px_20px_rgba(0,0,0,0.18)] transition hover:bg-[#95031f]"
                            onClick={() => dispatch(togglePopup('cart'))}
                        >
                            <span className="relative flex h-[38px] min-w-[58px] items-center justify-center">
                                <LuShoppingCart className="text-[22px] text-white" />
                                <span className="absolute right-2 top-1 inline-flex h-[15px] min-w-[15px] items-center justify-center rounded-full bg-white px-1 text-[9px] font-semibold leading-none text-[#b3052a]">
                                    {totalCartQuantity < 10
                                        ? String(totalCartQuantity).padStart(2, '0')
                                        : '9+'}
                                </span>
                            </span>

                            <span className="block min-w-[58px] bg-[#f3dfc4] px-2 py-1.5 text-center text-[11px] font-semibold text-[#1c0f0f]">
                                {currencyFormatter(totalCartPrice, {
                                    compactDisplay: 'short',
                                })}
                            </span>
                        </button>
                    </div>
                ) : null}
                {/* floating cart button */}

                <Suspense fallback={<div className="min-h-screen"></div>}>
                    <Outlet />
                </Suspense>
            </main>
            <Footer />
            <Copyright />

            {/* floating chat buttons */}
            <FloatingChatButtons />
            {/* floating chat buttons */}

            {/* popups */}
            <Popups />
        </>
    );
};

export default PrimaryLayout;
