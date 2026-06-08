import { useRef } from 'react';
import { FaBars, FaPhoneVolume, FaRegUserCircle, FaTag } from 'react-icons/fa';
import { IoMdSearch } from 'react-icons/io';
import { Link } from 'react-router-dom';
import DynamicLogo from '../../../../components/DynamicLogo';
import { togglePopup } from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import AvatarDropdownMenu from '../AvatarDropdownMenu';
import HeaderSocialLinks from '../HeaderSocialLinks';
import NavLinks from '../NavLinks';
import SearchDropdown from '../SearchDropdown';

const HeaderThemeTwo = () => {
    const navbarEl = useRef<HTMLDivElement>(null);
    const dispatch = useAppDispatch();
    const showCustomerMenu = window.config.customerMenu?.enabled ?? true;

    return (
        <header className="sticky top-0 z-[3] bg-white shadow-sm">
            <div className="bg-[#0b0b0b] text-white">
                <div className="container grid min-h-[46px] grid-cols-2 items-center gap-3 text-[13px] font-medium md:grid-cols-3 md:text-[15px]">
                    <div className="flex min-w-0 items-center gap-2 text-white">
                        <FaPhoneVolume className="shrink-0 text-[#ff8200]" />
                        <span>{window.config.topbarHotlineLabel}</span>
                    </div>
                    <p className="hidden items-center justify-center gap-2 text-center text-white md:flex">
                        <FaTag className="shrink-0 text-[#b3052a]" />
                        {window.config.topbarPromoText}
                    </p>
                    <Link
                        to="/track-order"
                        className="justify-self-end text-white transition hover:text-[#ff8200]"
                    >
                        Track Order
                    </Link>
                </div>
            </div>
            <div
                className="flex h-[68px] items-center justify-center bg-white text-[#0f0f0f] md:h-[78px] xl:h-[86px]"
                ref={navbarEl}
            >
                <div className="container flex items-center justify-between gap-8 sm:gap-[50px]">
                    <div className="flex items-center gap-5 lg:gap-14">
                        <button
                            className="text-[#b3052a] lg:hidden"
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

                    <NavLinks
                        dropdownPosition="relative-center"
                        classNames={{
                            container: 'max-xl:hidden',
                            menuLinks: 'py-[31px] text-[14px] font-medium',
                        }}
                    />

                    <div className="flex items-center gap-5 text-[#b3052a]">
                        <div className="relative group option-dropdown">
                            <button className="text-2xl">
                                <IoMdSearch />
                            </button>
                            <SearchDropdown />
                        </div>

                        {showCustomerMenu ? (
                            <div className="option-dropdown" tabIndex={0}>
                                <button className="option-dropdown__toggler no-style text-2xl">
                                    <FaRegUserCircle />
                                </button>

                                <div className="option-dropdown__options shadow-md w-[170px] top-[calc(100%+20px)] left-auto right-0 rounded-none p-0">
                                    <AvatarDropdownMenu />
                                </div>
                            </div>
                        ) : (
                            <HeaderSocialLinks linkClassName="border-current/30 hover:text-inherit hover:opacity-80" />
                        )}
                    </div>
                </div>
            </div>
        </header>
    );
};

export default HeaderThemeTwo;
