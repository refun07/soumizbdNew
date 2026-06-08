import {
    FaCommentDots,
    FaHeart,
    FaPowerOff,
    FaShoppingCart,
    FaStar,
    FaUser,
} from 'react-icons/fa';
import { FaGear } from 'react-icons/fa6';
import { NavLink, Outlet } from 'react-router-dom';
import Avatar from '../../components/Avatar';
import {
    DashboardReviewsSkeleton,
    DashboardSkeleton,
    OrderDetailsSkeleton,
    WishlistSkeleton,
} from '../../components/skeletons/from-svg';
import { apiSlice } from '../../store/features/api/apiSlice';
import { useAuth, userLoggedOut } from '../../store/features/auth/authSlice';
import { useAppDispatch } from '../../store/store';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Props {
    className?: string;
}

export const profileLinks = [
    {
        icon: <FaUser />,
        name: 'Dashboard', // lang key for translation
        urlRegex: /\/dashboard/,
        url: '/dashboard',
        showInSidebar: true,
        preloader: <DashboardSkeleton />,
    },
    {
        icon: <FaShoppingCart />,
        name: 'My Orders',
        urlRegex: /\/orders/,
        url: '/orders',
        showInSidebar: true,
        preloader: <Outlet />, // just to avoid here, because /orders page is already handing the preloader
    },
    {
        icon: <FaShoppingCart />,
        name: 'Order Details',
        urlRegex: /\/orders\/.+/,
        url: '/orders/:orderId',
        showInSidebar: false,
        preloader: <OrderDetailsSkeleton />,
    },
    {
        icon: <FaHeart />,
        name: 'Wishlist',
        urlRegex: /\/wishlist/,
        url: '/wishlist',
        showInSidebar: true,
        preloader: <WishlistSkeleton />,
    },
    {
        icon: <FaCommentDots />,
        name: 'Chats',
        urlRegex: /\/chats/,
        url: '/chats',
        showInSidebar: true,
        preloader: <DashboardSkeleton />,
    },
    {
        icon: <FaStar />,
        name: 'My Reviews',
        urlRegex: /\/reviews/,
        url: '/reviews',
        showInSidebar: true,
        preloader: <DashboardReviewsSkeleton />,
    },
    {
        icon: <FaGear />,
        name: 'Settings',
        urlRegex: /\/settings/,
        url: '/settings',
        showInSidebar: true,
        preloader: <DashboardSkeleton />,
    },
];

const DashboardSidebar = ({ className }: Props) => {
    const dispatch = useAppDispatch();
    const { user } = useAuth();

    if (!user) {
        return <></>;
    }

    return (
        <div className={cn('pt-10 sticky top-[calc(70px+20px)]', className)}>
            <div className="flex flex-col items-center">
                <Avatar name={user!.name} avatar={user!.avatar} size="xxl" />
                <h2 className="mt-2.5 text-lg">{user!.name}</h2>
                <p className="mt-px text-black/70 text-sm">
                    {user?.phone || user?.email}
                </p>
            </div>

            <ul className="mt-4 divide-y divide-primary-14">
                {profileLinks.map(
                    (item) =>
                        item.showInSidebar && (
                            <li key={item.name}>
                                <NavLink
                                    to={item.url}
                                    className={({ isActive }) =>
                                        cn(
                                            `option-dropdown__option gap-5 !px-8 !py-3.5 rounded-none`,
                                            {
                                                active: isActive,
                                            },
                                        )
                                    }
                                >
                                    {({ isActive }) => (
                                        <>
                                            <span
                                                className={cn('text-lg', {
                                                    'text-secondary': !isActive,
                                                    'text-white': isActive,
                                                })}
                                            >
                                                {item.icon}
                                            </span>
                                            <span>{translate(item.name)}</span>
                                        </>
                                    )}
                                </NavLink>
                            </li>
                        ),
                )}
                <li>
                    <button
                        className="option-dropdown__option gap-5 !px-8 !py-3.5 rounded-none"
                        onClick={() => {
                            dispatch(userLoggedOut());
                            dispatch(apiSlice.util.resetApiState());
                        }}
                    >
                        <span className="text-lg text-red-400">
                            <FaPowerOff />
                        </span>
                        <span className="text-red-400">
                            {translate('Logout')}
                        </span>
                    </button>
                </li>
            </ul>
        </div>
    );
};

export default DashboardSidebar;
