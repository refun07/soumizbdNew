import { useEffect, useRef, useState } from 'react';
import { FaBars } from 'react-icons/fa';
import { FaCircleUser } from 'react-icons/fa6';
import { IoCartOutline, IoHeartOutline } from 'react-icons/io5';
import { Link, useNavigate } from 'react-router-dom';
import Avatar from '../../../../components/Avatar';
import DynamicLogo from '../../../../components/DynamicLogo';
import SearchForm from '../../../../components/inputs/SearchForm';
import { useLazyGetWishListQuery } from '../../../../store/features/api/productApi';
import { useAuth } from '../../../../store/features/auth/authSlice';
import { togglePopup } from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import { paddedNumber } from '../../../../utils/numberFormatter';
import { translate } from '../../../../utils/translate';
import AvatarDropdownMenu from '../AvatarDropdownMenu';
import HeaderSocialLinks from '../HeaderSocialLinks';

const Logobar = () => {
    const navigate = useNavigate();
    const { user, carts } = useAuth();
    const dispatch = useAppDispatch();
    const navbarEl = useRef<HTMLDivElement>(null);
    const [getWishlist, { data: wishlist }] = useLazyGetWishListQuery();
    const showCustomerMenu = window.config.customerMenu?.enabled ?? true;

    const [totalProductInCart, setTotalProductInCart] = useState(0);

    useEffect(() => {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 138) {
                navbarEl.current?.classList.add('border-b');
            } else {
                navbarEl.current?.classList.remove('border-b');
            }
        });

        return () => {
            window.removeEventListener('scroll', () => {});
        };
    }, []);

    useEffect(() => {
        let totalProducts = 0;

        carts.forEach((item) => {
            totalProducts += item.qty;
        });

        setTotalProductInCart(totalProducts);
    }, [carts]);

    // get wishlist if user is logged in
    useEffect(() => {
        if (user) {
            getWishlist(undefined, true);
        }
    }, [user]);

    // open wishlist page if user is logged in
    // otherwise open signin popup
    const handleWishlistClick = () => {
        if (user) {
            navigate('/wishlist');
        } else {
            dispatch(togglePopup('signin'));
        }
    };

    return (
        <div
            className="h-[60px] md:h-[70px] xl:h-[92px] flex items-center justify-center text-black/70 sticky top-0 bg-white z-[3]"
            ref={navbarEl}
        >
            <div className="container flex items-center justify-between gap-8 sm:gap-[50px]">
                <div className="flex items-center gap-5 lg:gap-14">
                    <button
                        className="lg:hidden text-lg text-primary"
                        onClick={() => dispatch(togglePopup('categories'))}
                    >
                        <FaBars />
                    </button>

                    <Link to="/">
                        <DynamicLogo
                            alt="Logo"
                            className="max-xs:max-w-[100px] max-sm:max-w-[140px]"
                        />
                    </Link>
                </div>

                <div className="grow max-w-[618px] max-md:hidden">
                    <SearchForm
                        placeholder="Search for products, brands and categories..."
                        searchOnType={false}
                        submittableButton={false}
                    />
                </div>

                <div className="hidden lg:flex items-center justify-end gap-7">
                    <button
                        className="inline-flex flex-col items-center gap-1 relative"
                        onClick={handleWishlistClick}
                    >
                        <span className="text-[32px] text-primary">
                            <IoHeartOutline />
                        </span>
                        {(wishlist?.length || 0) > 0 ? (
                            <span className="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 h-5 min-w-[20px] flex items-center justify-center px-1 rounded-full bg-secondary text-white text-sm">
                                {paddedNumber(wishlist?.length)}
                            </span>
                        ) : null}
                    </button>
                    <button
                        className="inline-flex flex-col items-center gap-1 relative"
                        onClick={() => dispatch(togglePopup('cart'))}
                    >
                        <span className="text-[32px] text-primary">
                            <IoCartOutline />
                        </span>
                        {totalProductInCart > 0 ? (
                            <span className="absolute top-0 right-0 translate-x-1/2 -translate-y-1/2 h-5 min-w-[20px] flex items-center justify-center px-1 rounded-full bg-secondary text-white text-sm">
                                {paddedNumber(totalProductInCart)}
                            </span>
                        ) : null}
                    </button>

                    {showCustomerMenu ? (
                        user ? (
                            <div className="option-dropdown" tabIndex={0}>
                                <div className="option-dropdown__toggler no-style no-arrow p-0 h-auto flex items-center gap-3 bg-white text-inherit min-w-0">
                                    <Avatar
                                        name={user.name}
                                        avatar={user.avatar}
                                        size="md"
                                        // className="size-8"
                                    />

                                    <div className="flex flex-col text-left">
                                        <h5 className="text-primary font-medium">
                                            {user.name.split(' ')[0]}
                                        </h5>
                                        <span className="text-black/70">
                                            {translate('My Account')}
                                        </span>
                                    </div>
                                </div>

                                <div className="option-dropdown__options shadow-md w-[170px] top-[calc(100%+20px)] left-auto right-0 rounded-none p-0">
                                    <AvatarDropdownMenu />
                                </div>
                            </div>
                        ) : (
                            <button
                                onClick={() => dispatch(togglePopup('signin'))}
                                className="inline-flex items-center gap-2"
                            >
                                <span className="text-[32px] text-primary">
                                    <FaCircleUser />
                                </span>
                                <div className="flex flex-col text-left">
                                    <span className="text-primary font-medium">
                                        {translate('Login')}
                                    </span>
                                    <span className="text-black/70">
                                        {translate('My Account')}
                                    </span>
                                </div>
                            </button>
                        )
                    ) : (
                        <HeaderSocialLinks linkClassName="text-primary" />
                    )}
                </div>
            </div>
        </div>
    );
};

export default Logobar;
