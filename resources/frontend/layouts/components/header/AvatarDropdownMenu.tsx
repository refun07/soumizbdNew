import { FaPowerOff, FaShoppingCart } from 'react-icons/fa';
import { Link } from 'react-router-dom';
import { apiSlice } from '../../../store/features/api/apiSlice';
import { useAuth, userLoggedOut } from '../../../store/features/auth/authSlice';
import { useAppDispatch } from '../../../store/store';
import { togglePopup } from '../../../store/features/popup/popupSlice';
import { translate } from '../../../utils/translate';
import { FaCommentDots, FaGear, FaHeart, FaUser } from 'react-icons/fa6';
import toast from 'react-hot-toast';

const AvatarDropdownMenu = () => {
    const dispatch = useAppDispatch();
  const { user } = useAuth();
    const avatarDropdownMenu = [
        {
            icon: <FaUser />,
            name: 'Dashboard',
            url: '/dashboard',
        },
        {
            icon: <FaShoppingCart />,
            name: 'My Orders',
            url: '/orders',
        },
        {
            icon: <FaHeart />,
            name: 'Wishlist',
            url: '/wishlist',
        },
        {
            icon: <FaCommentDots />,
            name: 'Chats',
            url: '/chats',
        },
        {
            icon: <FaGear />,
            name: 'Settings',
            url: '/settings',
        },
    ];

    return (
        <ul>
            {avatarDropdownMenu.map((item) => (
                <li key={item.name}>
                    <Link
                        to={item.url}
                        className="option-dropdown__option rounded-none"
                    >
                        <span className="text-lg text-stone-300">
                            {item.icon}
                        </span>
                        <span>{item.name}</span>
                    </Link>
                </li>
            ))}
            <li>
                {user ? (
                     <button
                        className="option-dropdown__option rounded-none"
                        onClick={() => {
                            dispatch(userLoggedOut());
                            dispatch(apiSlice.util.resetApiState());
                            toast.success(translate('Logged Out Successfully!'));
                        }}
                    >
                        <span className="text-lg text-red-400">
                            <FaPowerOff />
                        </span>
                        <span className="text-red-400">{translate('logout')}</span>
                    </button>
                ):(
                     <button
                        className="option-dropdown__option rounded-none"
                        onClick={() => {
                            dispatch(togglePopup('signin'));
                        }}
                    >
                        <span className="text-lg text-green-400">
                            <FaPowerOff />
                        </span>
                        <span className="text-black-400">{translate('login')}</span>
                    </button>
                )}


            </li>
        </ul>
    );
};

export default AvatarDropdownMenu;
