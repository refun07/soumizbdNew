import {
    FaBoxOpen,
    FaHome,
    FaShoppingCart,
    FaTags,
    FaUser,
} from 'react-icons/fa';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../../../store/features/auth/authSlice';
import {
    closePopup,
    togglePopup,
    usePopup,
} from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import { paddedNumber } from '../../../../utils/numberFormatter';

interface Props {
    accentColor?: string;
}

const BottomBar = ({ accentColor = 'var(--color-primary)' }: Props) => {
    const { user, carts } = useAuth();
    const { popup } = usePopup();
    const dispatch = useAppDispatch();
    const { pathname } = useLocation();
    const totalProductInCart = carts.reduce(
        (total, item) => total + item.qty,
        0,
    );

    const handleCartClick = () => {
        if (popup === 'cart') {
            dispatch(closePopup());
        } else {
            dispatch(togglePopup('cart'));
        }
    };

    const getItemClassName = () => {
        return 'bot-mobile-navigation-item';
    };

    const getItemStyle = (active?: boolean) => ({
        color: active ? accentColor : undefined,
        borderBottomColor: active ? accentColor : undefined,
    });

    const scrollToPageTop = () => {
        dispatch(closePopup());
        window.scrollTo({
            top: 0,
            behavior: window.matchMedia('(prefers-reduced-motion: reduce)')
                .matches
                ? 'auto'
                : 'smooth',
        });
    };

    return (
        <nav className="bot-mobile-navigation" aria-label="Mobile navigation">
            <Link
                to={'/'}
                className={getItemClassName()}
                style={getItemStyle(!popup && pathname === '/')}
                aria-current={pathname === '/' ? 'page' : undefined}
                onClick={scrollToPageTop}
            >
                <span className="text-base">
                    <FaHome aria-hidden="true" />
                </span>
                <span className="text-[10px]">Home</span>
            </Link>

            <Link
                to={'/product'}
                className={getItemClassName()}
                style={getItemStyle(
                    !popup &&
                        (pathname.startsWith('/product') ||
                            pathname.startsWith('/categories')),
                )}
                aria-current={pathname === '/product' ? 'page' : undefined}
                onClick={scrollToPageTop}
            >
                <span className="text-base">
                    <FaBoxOpen aria-hidden="true" />
                </span>
                <span className="text-[10px]">All Products</span>
            </Link>

            <Link
                to={'/brands'}
                className={getItemClassName()}
                style={getItemStyle(!popup && pathname.startsWith('/brands'))}
                aria-current={pathname === '/brands' ? 'page' : undefined}
                onClick={scrollToPageTop}
            >
                <span className="text-base">
                    <FaTags aria-hidden="true" />
                </span>
                <span className="text-[10px]">Brands</span>
            </Link>

            <button
                type="button"
                aria-label={`My Cart, ${totalProductInCart} ${totalProductInCart === 1 ? 'item' : 'items'}`}
                aria-expanded={popup === 'cart'}
                className={getItemClassName()}
                style={getItemStyle(popup === 'cart')}
                onClick={handleCartClick}
            >
                <span className="relative text-base">
                    <FaShoppingCart aria-hidden="true" />
                    {totalProductInCart > 0 ? (
                        <span
                            className="absolute -right-3 -top-3 flex h-4 min-w-[16px] items-center justify-center rounded-full px-1 text-[9px] font-semibold leading-none text-white"
                            style={{ backgroundColor: accentColor }}
                        >
                            {paddedNumber(totalProductInCart)}
                        </span>
                    ) : null}
                </span>
                <span className="text-[10px]">My Cart</span>
            </button>

            {user ? (
                <button
                    type="button"
                    aria-expanded={popup === 'dashboard-sidebar'}
                    style={getItemStyle(
                        popup === 'dashboard-sidebar' ||
                            pathname.startsWith('/dashboard'),
                    )}
                    onClick={() => dispatch(togglePopup('dashboard-sidebar'))}
                    className={getItemClassName()}
                >
                    <span className="text-base">
                        <FaUser aria-hidden="true" />
                    </span>
                    <span className="text-[10px]">Account</span>
                </button>
            ) : (
                <button
                    type="button"
                    aria-haspopup="dialog"
                    aria-expanded={popup === 'signin'}
                    style={getItemStyle(popup === 'signin')}
                    className={getItemClassName()}
                    onClick={() => dispatch(togglePopup('signin'))}
                >
                    <span className="text-base">
                        <FaUser aria-hidden="true" />
                    </span>
                    <span className="text-[10px]">Login</span>
                </button>
            )}
        </nav>
    );
};

export default BottomBar;
