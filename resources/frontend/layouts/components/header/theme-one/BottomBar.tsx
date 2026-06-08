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

const BottomBar = () => {
    const { user } = useAuth();
    const { popup } = usePopup();
    const dispatch = useAppDispatch();
    const { pathname } = useLocation();

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

    const getItemClassName = (active?: boolean) => {
        return cn(`flex flex-col items-center gap-0.5 px-2 py-4 border-b-4`, {
            'text-primary border-secondary': active,
            'text-stone-400 border-transparent': !active,
        });
    };

    return (
        <div
            className={cn(
                'flex lg:hidden items-center justify-evenly gap-2 bg-white fixed bottom-0 left-0 right-0 z-[4] rounded-t-2xl shadow-[0px_-8px_30px_0px_#0000002e]',
            )}
        >
            <Link to={'/'} className={getItemClassName(pathname === '/')}>
                <span className="text-base">
                    <FaHome />
                </span>
                <span className="text-[10px]">Home</span>
            </Link>

            <button
                className={getItemClassName(popup === 'cart')}
                onClick={handleCartClick}
            >
                <span className="text-base">
                    <FaShoppingCart />
                </span>
                <span className="text-[10px]">My Cart</span>
            </button>

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

            <button
                className={getItemClassName(popup === 'search')}
                onClick={handleSearchClick}
            >
                <span className="">
                    {popup === 'search' ? <IoMdClose /> : <FaSearch />}
                </span>
                <span className="text-[10px]">Search</span>
            </button>

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
