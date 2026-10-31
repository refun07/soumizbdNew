import StorefrontSeo from '../components/seo/StorefrontSeo';
import PageErrorBoundary from '../components/PageErrorBoundary';
import { Suspense, useEffect, useMemo } from 'react';
import { useTranslation } from 'react-i18next';
import { LuShoppingCart } from 'react-icons/lu';
import { Outlet, useLocation } from 'react-router-dom';
import Popups from '../components/popups';
import { setAuthData, useAuth } from '../store/features/auth/authSlice';
import { closePopup, togglePopup } from '../store/features/popup/popupSlice';
import { useAppDispatch } from '../store/store';
import { currencyFormatter } from '../utils/numberFormatter';
import Footer from '../components/storefront/BotanicalFooter';
import Header from '../components/storefront/BotanicalHeader';
import '../components/storefront/botanical.css';
import '../components/storefront/storefront-consistency.css';
import BottomBar from './components/header/theme-one/BottomBar';
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
        <div className="botanical-storefront">
            <StorefrontSeo />
            <Header />
            <main id="storefront-main" tabIndex={-1}>
                {/* floating cart button */}
                {(window.config.defaultTheme === 'theme-two' || window.config.defaultTheme === 'theme-one') && window.config.cartVisibilityToggle === '1' ? (
                    <div className="hidden lg:block fixed top-1/3 right-0 z-[2]">
                        <button
                            className="overflow-hidden rounded-l-[4px] bg-[#087c3e] text-white shadow-[0_8px_20px_rgba(0,0,0,0.18)] transition hover:bg-[#005b2d]"
                            onClick={() => dispatch(togglePopup('cart'))}
                        >
                            <span className="relative flex h-[38px] min-w-[58px] items-center justify-center">
                                <LuShoppingCart className="text-[22px] text-white" />
                                <span className="absolute right-2 top-1 inline-flex h-[15px] min-w-[15px] items-center justify-center rounded-full bg-white px-1 text-[9px] font-semibold leading-none text-[#087c3e]">
                                    {totalCartQuantity < 10
                                        ? String(totalCartQuantity).padStart(2, '0')
                                        : '9+'}
                                </span>
                            </span>

                            <span className="block min-w-[58px] bg-[#e9f3ed] px-2 py-1.5 text-center text-[11px] font-semibold text-[#1c0f0f]">
                                {currencyFormatter(totalCartPrice, {
                                    compactDisplay: 'short',
                                })}
                            </span>
                        </button>
                    </div>
                ) : null}
                {/* floating cart button */}

                <PageErrorBoundary key={location.pathname}>
                    <Suspense fallback={<div className="container min-h-64 py-16" role="status">Loading page…</div>}>
                        <Outlet />
                    </Suspense>
                </PageErrorBoundary>
            </main>
            <Footer />
            <BottomBar accentColor="var(--bot-green)" />

            {/* floating chat buttons */}
            <FloatingChatButtons />
            {/* floating chat buttons */}

            {/* popups */}
            <Popups />
        </div>
    );
};

export default PrimaryLayout;
