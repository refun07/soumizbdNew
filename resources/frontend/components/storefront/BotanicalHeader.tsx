import { useEffect, useRef, useState } from 'react';
import {
    LuHeart,
    LuMenu,
    LuSearch,
    LuShoppingBag,
    LuUser,
    LuX,
} from 'react-icons/lu';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useAuth } from '../../store/features/auth/authSlice';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import BotanicalSearch from './BotanicalSearch';

export function BotanicalLogo({ footer = false }: { footer?: boolean }) {
    const { appName, logo } = window.config.generalSettings;
    const logoUrl = logo || '/images/storefront/soumis-logo.png';
    const logoName = logoUrl.includes('soumis-logo.png')
        ? "The Soumi’s Can Product Bangladesh"
        : appName;
    return (
        <Link
            className={`bot-logo ${footer ? 'bot-logo--footer' : ''}`}
            to="/"
            aria-label={`${logoName} home`}
        >
            <img src={logoUrl} alt={logoName} width="492" height="314" />
        </Link>
    );
}

export default function BotanicalHeader() {
    const { user, carts } = useAuth();
    const headerRef = useRef<HTMLElement>(null);
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const location = useLocation();
    const [menuOpen, setMenuOpen] = useState(false);
    const [searchOpen, setSearchOpen] = useState(false);
    const total = carts.reduce((sum, item) => sum + item.qty, 0);
    const custom = window.config.headerNavbarConfig;
    const links =
        custom?.custom_links_enabled && custom.custom_links?.length
            ? custom.custom_links.map(({ name, url }) => ({ name, url }))
            : [
                  { name: 'Home', url: '/' },
                  { name: 'Best sellers', url: '/#best-sellers' },
                  { name: 'Products', url: '/product' },
                  { name: 'Reviews', url: '/#customer-reviews' },
                  { name: 'Contact us', url: '/#contact' },
              ];

    useEffect(() => {
        setMenuOpen(false);
        setSearchOpen(false);
    }, [location.pathname, location.hash]);
    useEffect(() => {
        function close(event: KeyboardEvent) {
            if (event.key === 'Escape') {
                setMenuOpen(false);
                setSearchOpen(false);
            }
        }
        document.addEventListener('keydown', close);
        return () => document.removeEventListener('keydown', close);
    }, []);

    useEffect(() => {
        function closeOutside(event: PointerEvent) {
            if (headerRef.current && !headerRef.current.contains(event.target as Node)) {
                setMenuOpen(false);
                setSearchOpen(false);
            }
        }
        document.addEventListener('pointerdown', closeOutside);
        return () => document.removeEventListener('pointerdown', closeOutside);
    }, []);

    return (
        <header className="bot-header" ref={headerRef}>
            <a className="bot-skip-link" href="#storefront-main">
                Skip to content
            </a>
            <div className="bot-container bot-header-row">
                <BotanicalLogo />
                <nav
                    className={`bot-navigation ${menuOpen ? 'is-open' : ''}`}
                    aria-label="Main navigation"
                    id="bot-navigation"
                >
                    {links.map(({ name, url }) => (
                        <Link
                            key={`${name}-${url}`}
                            to={url}
                            className={
                                location.pathname === '/' && url === '/'
                                    ? 'is-active'
                                    : ''
                            }
                            aria-current={
                                location.pathname === url ? 'page' : undefined
                            }
                            onClick={() => setMenuOpen(false)}
                        >
                            {name}
                        </Link>
                    ))}
                </nav>
                <div className="bot-header-actions">
                    <button
                        aria-label={user ? 'My account' : 'Sign in'}
                        onClick={() =>
                            user
                                ? navigate('/dashboard')
                                : dispatch(togglePopup('signin'))
                        }
                    >
                        <LuUser />
                    </button>
                    <button
                        className="bot-cart-button"
                        aria-label={`Shopping bag, ${total} items`}
                        onClick={() => dispatch(togglePopup('cart'))}
                    >
                        <LuShoppingBag />
                        {total > 0 && <span>{total}</span>}
                    </button>
                    <button
                        aria-label="Wishlist"
                        onClick={() =>
                            user
                                ? navigate('/wishlist')
                                : dispatch(togglePopup('signin'))
                        }
                    >
                        <LuHeart />
                    </button>
                    <button
                        aria-label={
                            searchOpen ? 'Close search' : 'Search products'
                        }
                        aria-expanded={searchOpen}
                        aria-controls="bot-search"
                        onClick={() => {
                            setMenuOpen(false);
                            setSearchOpen(!searchOpen);
                        }}
                    >
                        {searchOpen ? <LuX /> : <LuSearch />}
                    </button>
                    <button
                        className="bot-menu-toggle"
                        aria-label={menuOpen ? 'Close menu' : 'Open menu'}
                        aria-expanded={menuOpen}
                        aria-controls="bot-navigation"
                        onClick={() => {
                            setSearchOpen(false);
                            setMenuOpen(!menuOpen);
                        }}
                    >
                        {menuOpen ? <LuX /> : <LuMenu />}
                    </button>
                </div>
            </div>
            {searchOpen && <BotanicalSearch onClose={() => setSearchOpen(false)} />}
        </header>
    );
}
