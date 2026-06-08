import { useEffect, useState } from 'react';
import { FaBars } from 'react-icons/fa6';
import { IoMdSearch } from 'react-icons/io';
import { IoCart, IoHeart } from 'react-icons/io5';
import { Link, useNavigate } from 'react-router-dom';
import DynamicLogo from '../../../../components/DynamicLogo';
import { useLazyGetWishListQuery } from '../../../../store/features/api/productApi';
import { useAuth } from '../../../../store/features/auth/authSlice';
import { togglePopup } from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import {
    currencyFormatter,
    paddedNumber,
} from '../../../../utils/numberFormatter';
import NavLinks from '../NavLinks';
import SearchDropdown from '../SearchDropdown';

interface MenuDesign {
    bg_color: string;
    text_color: string;
    hover_color: string;
}

const Logobar = () => {
    const { user, carts } = useAuth();
    const navigate = useNavigate();
    const dispatch = useAppDispatch();

    const [customLinks, setCustomLinks] = useState<Array<{
        name: string;
        url: string;
        icon_path: string;
        icon_svg?: string;
    }>>([]);
    const [customLinksEnabled, setCustomLinksEnabled] = useState(false);
    const [menuDesign, setMenuDesign] = useState<MenuDesign>({
        bg_color: '#5A9D5E',
        text_color: '#FFFFFF',
        hover_color: '#FFFFFF'
    });

    useEffect(() => {
        try {
            const headerNavbarConfig = (window as any).config?.headerNavbarConfig || {};
            setCustomLinksEnabled(headerNavbarConfig.custom_links_enabled ?? false);
            setCustomLinks(Array.isArray(headerNavbarConfig.custom_links) ? headerNavbarConfig.custom_links : []);

            const mainMenuConfig = (window as any).config?.mainMenuConfig || {};
            const design = mainMenuConfig.design || {};
            setMenuDesign({
                bg_color: design.bg_color || '#5A9D5E',
                text_color: design.text_color || '#FFFFFF',
                hover_color: design.hover_color || '#FFFFFF'
            });
        } catch (error) {
            console.error('Failed to load config:', error);
        }
    }, []);

    const [getWishlist, { data: wishlist }] = useLazyGetWishListQuery();

    const [totalProductInCart, setTotalProductInCart] = useState(0);
    const [totalCartPrice, setTotalCartPrice] = useState(0);

    useEffect(() => {
        let totalProducts = 0;
        let totalPrice = 0;

        carts.forEach((item) => {
            totalProducts += item.qty;
            totalPrice +=
                item.qty *
                (item.variation.discountedBasePrice ||
                    item.variation.basePrice ||
                    item.product.basePrice);
        });

        setTotalProductInCart(totalProducts);
        setTotalCartPrice(totalPrice);
    }, [carts]);

    useEffect(() => {
        if (user) {
            getWishlist(undefined, true);
        }
    }, [user]);

    const handleWishlistClick = () => {
        if (user) {
            navigate('/wishlist');
        } else {
            dispatch(togglePopup('signin'));
        }
    };

    return (
        <>
            <style>{`
                .logobar-container {
                    --menu-text-color: ${menuDesign.text_color};
                    --menu-hover-color: ${menuDesign.hover_color};
                }
                .logobar-container .logobar-custom-link:hover {
                    color: var(--menu-hover-color) !important;
                }
            `}</style>
            <nav
                className="sticky top-0 z-[3] logobar-container"
                style={{ backgroundColor: menuDesign.bg_color }}
            >
                <div
                    className="container h-[60px] md:h-[70px] flex items-center gap-20 relative"
                    style={{ color: menuDesign.text_color }}
                >
                    <div className="flex items-center gap-10 flex-shrink-0 w-[calc(50%-100px)]">
                        <button className="text-lg">
                            <FaBars />
                        </button>

                        <div className="flex-1">
                            <NavLinks
                                dropdownPosition="relative-center"
                                classNames={{
                                    container: 'max-xl:hidden',
                                    menuLinks: 'py-[22px]',
                                }}
                            />
                        </div>
                    </div>

                    <div className="absolute left-1/2 transform -translate-x-1/2 z-10 w-[200px] flex justify-center">
                        <Link to="/">
                            <DynamicLogo
                                alt="Logo"
                                className="max-xs:max-w-[100px] max-sm:max-w-[140px]"
                            />
                        </Link>
                    </div>

                    <div className="flex items-center gap-10 justify-end ml-auto flex-shrink-0 w-[calc(50%-100px)]">
                        {customLinksEnabled && customLinks.length > 0 && (
                            <div className="flex gap-5 md:gap-3">
                                {customLinks.map((link, index) => (
                                    <Link
                                        key={index}
                                        to={link.url || '#'}
                                        className="group inline-flex items-center gap-2 transition-colors logobar-custom-link"
                                        style={{ color: menuDesign.text_color }}
                                    >
                                        {link.icon_svg ? (
                                            <span
                                                className="w-5 h-5 inline-block"
                                                dangerouslySetInnerHTML={{ __html: link.icon_svg }}
                                            />
                                        ) : link.icon_path ? (
                                            <img
                                                src={`/${link.icon_path}`}
                                                alt={link.name}
                                                className="w-5 h-5"
                                            />
                                        ) : null}
                                        {link.name}
                                    </Link>
                                ))}
                            </div>
                        )}

                        <div className="flex items-center gap-5">
                            <button
                                className="inline-flex items-center gap-1"
                                onClick={handleWishlistClick}
                            >
                                <span className="text-2xl">
                                    <IoHeart />
                                </span>
                                {(wishlist?.length || 0) > 0 ? (
                                    <span className="text-sm" style={{ color: menuDesign.text_color, opacity: 0.7 }}>
                                        ({paddedNumber(wishlist?.length)})
                                    </span>
                                ) : null}
                            </button>
                            <button
                                className="inline-flex items-center gap-1"
                                onClick={() => dispatch(togglePopup('cart'))}
                            >
                                <span className="text-2xl">
                                    <IoCart />
                                </span>
                                {totalProductInCart > 0 ? (
                                    <span className="text-sm" style={{ color: menuDesign.text_color, opacity: 0.7 }}>
                                        <span style={{ color: menuDesign.text_color }}>
                                            {currencyFormatter(totalCartPrice)}
                                        </span>{' '}
                                        ({paddedNumber(totalProductInCart)})
                                    </span>
                                ) : null}
                            </button>

                            <div className="flex items-center gap-6">
                                <div className="group relative option-dropdown">
                                    <button className="text-2xl">
                                        <IoMdSearch />
                                    </button>
                                    <SearchDropdown />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </>
    );
};

export default Logobar;
