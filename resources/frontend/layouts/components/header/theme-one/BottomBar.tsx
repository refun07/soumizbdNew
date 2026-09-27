import {
    FaHome,
    FaSearch,
    FaShoppingCart,
    FaStore,
    FaUser,
} from 'react-icons/fa';
import { IoMdClose } from 'react-icons/io';
import { Link, useLocation } from 'react-router-dom';
import { useAuth } from '../../../../store/features/auth/authSlice';
import {
    closePopup,
    togglePopup,
    usePopup,
} from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import { cn } from '../../../../utils/cn';
import { paddedNumber } from '../../../../utils/numberFormatter';

interface Props {
    showShop?: boolean;
    showSearch?: boolean;
    accentColor?: string;
}

const BottomBar = ({
    showShop = true,
    showSearch = true,
    accentColor = 'var(--color-primary)',
}: Props) => {
    const { user, carts } = useAuth();
    const { popup } = usePopup();
    const dispatch = useAppDispatch();
    const { pathname } = useLocation();
    const totalProductInCart = carts.reduce((total, item) => total + item.qty, 0);

    const handleSearchClick = () => {
        if (popup === 'search') {
            dispatch(closePopup());
        } else {
            dispatch(
                togglePopup({
                    popup: 'search',
                    popupProps: {
                        overlay: false,
                    },
                }),
            );
        }
    };

    const handleCartClick = () => {
        if (popup === 'cart') {
            dispatch(closePopup());
        } else {
            dispatch(togglePopup('cart'));
        }
    };

    const getItemClassName = () => {
        return 'flex flex-col items-center gap-0.5 px-2 py-4 border-b-4 border-transparent text-stone-400';
    };

    const getItemStyle = (active?: boolean) => ({
        color: active ? accentColor : undefined,
        borderBottomColor: active ? accentColor : undefined,
    });

    const scrollToPageTop = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    return (
        <div
            className={cn(
                'flex lg:hidden items-center justify-evenly gap-2 bg-white fixed bottom-0 left-0 right-0 z-[4] rounded-t-2xl shadow-[0px_-8px_30px_0px_#0000002e]',
            )}
        >
            <Link
                to={'/'}
                className={getItemClassName()}
                style={getItemStyle(pathname === '/')}
                onClick={scrollToPageTop}
            >
                <span className="text-base">
                    <FaHome />
                </span>
                <span className="text-[10px]">Home</span>
            </Link>

            <button
                className={getItemClassName()}
                style={getItemStyle(popup === 'cart')}
                onClick={handleCartClick}
            >
                <span className="relative text-base">
                    <FaShoppingCart />
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

            {showShop ? (
                <Link
                    to={
                        window.config.generalSettings.appMode === 'multiVendor'
                            ? '/shops'
                            : '/products'
                    }
                    className={getItemClassName()}
                >
                    <span className="text-base">
                        <FaStore />
                    </span>
                    <span className="text-[10px]">Shop</span>
                </Link>
            ) : null}

            {showSearch ? (
                <button
                    className={getItemClassName()}
                    style={getItemStyle(popup === 'search')}
                    onClick={handleSearchClick}
                >
                    <span className="">
                        {popup === 'search' ? <IoMdClose /> : <FaSearch />}
                    </span>
                    <span className="text-[10px]">Search</span>
                </button>
            ) : null}

            {user ? (
                <button
                    onClick={() => dispatch(togglePopup('dashboard-sidebar'))}
                    className={getItemClassName()}
                >
                    <span className="text-base">
                        <FaUser />
                    </span>
                    <span className="text-[10px]">Account</span>
                </button>
            ) : (
                <button
                    className={getItemClassName()}
                    onClick={() => dispatch(togglePopup('signin'))}
                >
                    <span className="text-base">
                        <FaUser />
                    </span>
                    <span className="text-[10px]">Login</span>
                </button>
            )}
        </div>
    );
};

export default BottomBar;
