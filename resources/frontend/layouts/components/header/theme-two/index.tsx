import { useRef } from 'react';
import { FaBars, FaPhoneVolume, FaRegUserCircle, FaTag } from 'react-icons/fa';
import { IoMdSearch } from 'react-icons/io';
import { IoArrowForward, IoCartOutline } from 'react-icons/io5';
import { Link } from 'react-router-dom';
import DynamicLogo from '../../../../components/DynamicLogo';
import { useAuth } from '../../../../store/features/auth/authSlice';
import { togglePopup } from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import { paddedNumber } from '../../../../utils/numberFormatter';
import AvatarDropdownMenu from '../AvatarDropdownMenu';
import HeaderSocialLinks from '../HeaderSocialLinks';
import NavLinks from '../NavLinks';
import SearchDropdown from '../SearchDropdown';
import BottomBar from '../theme-one/BottomBar';

const HeaderThemeTwo = () => {
    const navbarEl = useRef<HTMLDivElement>(null);
    const dispatch = useAppDispatch();
    const { carts } = useAuth();
    const showCustomerMenu = window.config.customerMenu?.enabled ?? true;
    const actionButton = window.config.headerNavbarConfig?.action_button;
    const topbarConfig = window.config.headerTopbarConfig;
    const showTopbar = topbarConfig?.enabled ?? true;
    const topbarHotline =
        topbarConfig?.hotline_number || window.config.topbarHotlineLabel;
    const topbarPromoText =
        topbarConfig?.promo_text || window.config.topbarPromoText;
    const topbarPhone =
        topbarConfig?.hotline ||
        topbarHotline?.replace(/\D/g, '') ||
        window.config.topbarHotline;
    const topbarStyle = {
        backgroundColor: topbarConfig?.bg_color || '#0b0b0b',
        color: topbarConfig?.text_color || '#ffffff',
    };
    const actionButtonName = actionButton?.name?.trim() || '';
    const actionButtonLink = actionButton?.link?.trim() || '#';
    const showActionButton = Boolean(actionButton?.enabled && actionButtonName);
    const isExternalActionButtonLink =
        /^(https?:)?\/\//i.test(actionButtonLink) ||
        /^[a-z][a-z\d+.-]*:/i.test(actionButtonLink);
    const actionButtonClassName =
        'group inline-flex h-9 max-w-[120px] items-center justify-center gap-1.5 rounded-button bg-[#B3052A] px-3 text-xs font-semibold uppercase leading-none text-white transition hover:bg-[#900421] sm:h-10 sm:max-w-[190px] sm:gap-2 sm:px-4 sm:text-sm';
    const iconButtonClassName =
        'relative inline-flex h-10 w-10 shrink-0 items-center justify-center text-2xl leading-none text-[#b3052a] transition hover:text-[#900421]';
    const actionButtonContent = (
        <>
            <span className="truncate">{actionButtonName}</span>
            <IoArrowForward className="shrink-0 text-lg text-[#F2C488] transition-transform duration-300 group-hover:translate-x-1" />
        </>
    );
    const totalProductInCart = carts.reduce((total, item) => total + item.qty, 0);
    const scrollToPageTop = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    return (
        <>
        <style>
            {`
            `}
        </style>
        <header className="sticky top-0 z-[3] bg-white shadow-sm">
            {showTopbar && <div style={topbarStyle}>
                <div className="container grid min-h-[46px] grid-cols-2 items-center gap-3 text-[13px] font-medium md:grid-cols-[minmax(170px,0.85fr)_minmax(0,1.8fr)_minmax(110px,0.65fr)] md:text-[15px]">
                    <a href={`tel:${topbarPhone}`} className="flex min-w-0 items-center gap-2 text-inherit">
                        <FaPhoneVolume className="shrink-0 text-[#ff8200]" />
                        <span>{topbarHotline}</span>
                    </a>
                    <div className="mx-4 hidden min-w-0 overflow-hidden text-inherit lg:mx-8 md:block">
                        <p className="mx-auto flex w-max max-w-full items-center justify-center gap-2 whitespace-nowrap text-center">
                            {topbarConfig?.promo_text_icon ? (
                                <img
                                    src={`/${topbarConfig.promo_text_icon}`}
                                    alt=""
                                    className="h-4 w-4 shrink-0 object-contain"
                                />
                            ) : (
                                <FaTag className="shrink-0 text-[#b3052a]" />
                            )}
                            <span className="truncate">{topbarPromoText}</span>
                        </p>
                    </div>
                    <Link
                        to="/track-order"
                        className="justify-self-end text-inherit transition hover:text-[#ff8200]"
                    >
                        Track Order
                    </Link>
                </div>
            </div>}
            <div
                className="flex h-[68px] items-center justify-center bg-white text-[#0f0f0f] md:h-[78px] xl:h-[86px]"
                ref={navbarEl}
            >
                <div className="container flex items-center justify-between gap-4 sm:gap-[50px]">
                    <div className="flex items-center gap-5 lg:gap-14">
                        <button
                            className="text-[#b3052a] lg:hidden"
                            onClick={() => dispatch(togglePopup('categories'))}
                        >
                            <FaBars />
                        </button>

                        <Link to="/" onClick={scrollToPageTop}>
                            <DynamicLogo
                                alt="Logo"
                                className="max-xs:max-w-[100px] max-sm:max-w-[140px]"
                            />
                        </Link>
                    </div>

                    <NavLinks
                        dropdownPosition="relative-center"
                        classNames={{
                            container: 'max-xl:hidden',
                            menuLinks: 'py-[31px] text-[14px] font-medium',
                        }}
                    />

                    <div className="flex items-center justify-end gap-2 text-[#b3052a] sm:gap-3">
                        {showActionButton &&
                            (isExternalActionButtonLink ? (
                                <a
                                    href={actionButtonLink}
                                    className={actionButtonClassName}
                                    title={actionButtonName}
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    {actionButtonContent}
                                </a>
                            ) : (
                                <Link
                                    to={actionButtonLink}
                                    className={actionButtonClassName}
                                    title={actionButtonName}
                                >
                                    {actionButtonContent}
                                </Link>
                            ))}

                        <div className="relative group option-dropdown flex items-center">
                            <button className={iconButtonClassName} aria-label="Search" title="Search">
                                <IoMdSearch />
                            </button>
                            <SearchDropdown />
                        </div>

                        <button
                            className={`${iconButtonClassName} max-lg:hidden`}
                            onClick={() => dispatch(togglePopup('cart'))}
                            aria-label="Cart"
                            title="Cart"
                        >
                            <IoCartOutline />
                            {totalProductInCart > 0 ? (
                                <span className="absolute right-0 top-0 flex h-5 min-w-[20px] items-center justify-center rounded-full bg-[#b3052a] px-1 text-xs font-semibold leading-none text-white">
                                    {paddedNumber(totalProductInCart)}
                                </span>
                            ) : null}
                        </button>

                        {showCustomerMenu ? (
                            <div className="option-dropdown flex items-center max-lg:hidden" tabIndex={0}>
                                <button className={`option-dropdown__toggler no-style ${iconButtonClassName}`}>
                                    <FaRegUserCircle />
                                </button>

                                <div className="option-dropdown__options shadow-md w-[170px] top-[calc(100%+20px)] left-auto right-0 rounded-none p-0">
                                    <AvatarDropdownMenu />
                                </div>
                            </div>
                        ) : (
                            <HeaderSocialLinks linkClassName="max-lg:hidden border-current/30 hover:text-inherit hover:opacity-80" />
                        )}
                    </div>
                </div>
            </div>
        </header>
        <BottomBar showShop={false} showSearch={false} accentColor="#b3052a" />
        </>
    );
};

export default HeaderThemeTwo;
