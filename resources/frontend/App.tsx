import { lazy, Suspense, useEffect, useRef } from 'react';
import { useDispatch } from 'react-redux';
import { Route, Routes, useLocation } from 'react-router-dom';
import PaymentGateway from './components/order/PaymentGateway';
import DashboardLayout from './layouts/DashboardLayout';
import PrimaryLayout from './layouts/PrimaryLayout';
import ProtectedLayout from './layouts/ProtectedLayout';
import ShopLayout from './layouts/ShopLayout';
import Blogs from './pages/home/blogs';
import BlogDetails from './pages/home/blogs/BlogDetails';
import { useAuth } from './store/features/auth/authSlice';
import {
    setCheckoutData,
    useCheckoutData,
} from './store/features/checkout/checkoutSlice';
import { translate } from './utils/translate';

import useGTMEventsNew from 'hooks/useGTMEventNew';


const HomeIndex = lazy(() => import('./pages/home'));
const Brands = lazy(() => import('./pages/Brands'));
const Shops = lazy(() => import('./pages/shops/Shops'));

const ProductDetails = lazy(() => import('./pages/ProductDetails'));
const Brand = lazy(() => import('./pages/search-filter/Brand'));
const Category = lazy(() => import('./pages/search-filter/Category'));
const Search = lazy(() => import('./pages/search-filter/Search'));

const SellerSignup = lazy(() => import('./pages/seller/Signup'));
const SellerSignupSuccess = lazy(() => import('./pages/seller/SignupSuccess'));

const ShopDetails = lazy(() => import('./pages/shops/ShopDetails'));
const ShopProducts = lazy(() => import('./pages/shops/ShopProducts'));
const ShopOffers = lazy(() => import('./pages/shops/ShopOffers'));
const ShopProfile = lazy(() => import('./pages/shops/ShopProfile'));

const Discounts = lazy(() => import('./pages/Discounts'));
const Coupons = lazy(() => import('./pages/Coupons'));
const Campaigns = lazy(() => import('./pages/campaign/Campaigns'));
const CampaignDetails = lazy(() => import('./pages/campaign/CampaignDetails'));

const Dashboard = lazy(() => import('./pages/dashboard/Dashboard'));
const Orders = lazy(() => import('./pages/dashboard/Orders'));
const OrderDetails = lazy(() => import('./pages/dashboard/OrderDetails'));
const Wishlist = lazy(() => import('./pages/dashboard/Wishlist'));
const ChatHistory = lazy(() => import('./pages/dashboard/ChatHistory'));
const Reviews = lazy(() => import('./pages/dashboard/Reviews'));
const Settings = lazy(() => import('./pages/dashboard/Settings'));
const TicketCreate = lazy(() => import('./pages/support/TicketCreate'));
const TicketDetail = lazy(() => import('./pages/support/TicketDetail'));

const Checkout = lazy(() => import('./pages/order/Checkout'));
const OrderSuccess = lazy(() => import('./pages/order/OrderSuccess'));
const OrderFailed = lazy(() => import('./pages/order/OrderFailed'));

const Cart = lazy(() => import('./pages/order/Cart'));
const OrderTracking = lazy(() => import('./pages/order/OrderTracking'));

const DynamicPage = lazy(() => import('./pages/DynamicPage'));
const NotFound = lazy(() => import('./pages/NotFound'));

const App = () => {
    const dispatch = useDispatch();
    const { carts, isLoading } = useAuth();
    const { selectedShops } = useCheckoutData();
    const location = useLocation();

      const gtmEvent = useGTMEventsNew();

    // Track previous search params for infinite scroll detection
    const prevSearchRef = useRef(location.search);

    // Scroll to top if path changes (but not for infinite scroll page changes)
    useEffect(() => {
        if (location.state?.scrollToTop === false) return;

        // Check if only the page param changed (infinite scroll pagination)
        // in which case we should NOT scroll to top
        const isInfiniteScrollEnabled = window.config.generalSettings.productInfiniteScroll;
        if (isInfiniteScrollEnabled) {
            const prevParams = new URLSearchParams(prevSearchRef.current);
            const currentParams = new URLSearchParams(location.search);

            // Check if only page changed and all other params are the same
            const prevPage = prevParams.get('page') || '1';
            const currentPage = currentParams.get('page') || '1';

            prevParams.delete('page');
            currentParams.delete('page');

            // If only page changed (all other params are identical), don't scroll
            if (prevParams.toString() === currentParams.toString() && prevPage !== currentPage) {
                prevSearchRef.current = location.search;
                return;
            }
        }

        prevSearchRef.current = location.search;

        window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'instant',
        });
    }, [location.pathname, location.search]);

    useEffect(() => {
        if (document.activeElement?.closest('.option-dropdown')) {
            (document.activeElement as HTMLElement).blur();
        }
        gtmEvent({
            event: 'page_view',
            page_location: window.location.href,
            page_referrer: document.referrer || '',
        });
    }, [location.pathname]);

    // update selected shop state when cart is changed
    useEffect(() => {
        if (isLoading) {
            return;
        }

        // find all shops
        const shops: Record<string, boolean> = {};
        carts.forEach(({ shop }) => {
            // if shop is already selected, then keep it true
            // else false
            shops[shop.name] =
                selectedShops[shop.name] === false ? false : true;
        });
        dispatch(
            setCheckoutData({
                selectedShops: shops,
            }),
        );
    }, [carts]);

    // if appMode is singleVendor, then hide the seller related pages
    const { appMode } = window.config.generalSettings;

    return (
        <Routes>
            <Route element={<PrimaryLayout />}>
                <Route path="/" element={<HomeIndex />} />

                <Route path="/blogs" element={<Blogs />} />
                <Route path="/blogs/:blogSlug" element={<BlogDetails />} />

                <Route path="/products/:slug" element={<ProductDetails />} />

                <Route path="/brands" element={<Brands />} />
                {/* start::uses search and filter */}
                <Route path="/brands/:brandSlug" element={<Brand />} />
                <Route
                    path="/categories/:categorySlug"
                    element={<Category />}
                />
                <Route path="/products" element={<Search />} />
                {/* end::uses search and filter */}

                {appMode === 'multiVendor' ? (
                    <>
                        <Route path="/shops" element={<Shops />} />
                        <Route
                            path="/shops/:shopSlug"
                            element={
                                <Suspense
                                    fallback={<>{translate('loading')}</>}
                                >
                                    <ShopLayout />
                                </Suspense>
                            }
                        >
                            <Route index element={<ShopDetails />} />
                            <Route path="products" element={<ShopProducts />} />
                            <Route path="offers" element={<ShopOffers />} />
                            <Route path="profile" element={<ShopProfile />} />
                        </Route>
                    </>
                ) : null}

                {appMode === 'multiVendor' ? (
                    <>
                        {/* seller registration */}
                        <Route
                            path="/seller/signup"
                            element={<SellerSignup />}
                        />
                        <Route
                            path="/seller/signup/success"
                            element={<SellerSignupSuccess />}
                        />
                    </>
                ) : null}

                <Route path="/discounts" element={<Discounts />} />
                <Route path="/coupons" element={<Coupons />} />
                <Route path="/campaigns" element={<Campaigns />} />
                <Route path="/campaigns/:slug" element={<CampaignDetails />} />

                {/* dashboard pages */}
                <Route element={<DashboardLayout />}>
                    <Route path="/dashboard" element={<Dashboard />} />
                    <Route path="/orders" element={<Orders />} />
                    {/* <Route
                        path="/orders/:orderCode"
                        element={<OrderDetails />}
                    /> */}
                    <Route path="/wishlist" element={<Wishlist />} />
                    <Route path="/chats" element={<ChatHistory />} />
                    <Route path="/reviews" element={<Reviews />} />
                    <Route path="/settings" element={<Settings />} />
                </Route>
                
                {/* Support tickets - outside dashboard layout */}
                <Route path="/support/ticket/create" element={<TicketCreate />} />
                <Route path="/support/ticket/:publicId" element={<TicketDetail />} />
                
                {/* made order deatils page open */}
                <Route path="/orders/:orderCode" element={<OrderDetails />} />
                <Route path="/checkout" element={<Checkout />} />
                <Route
                    path="/orders/success/:orderCode"
                    element={<OrderSuccess />}
                />
                <Route path="/payment" element={<PaymentGateway />} />
                <Route element={<ProtectedLayout />}>
                    {/* <Route path="/checkout" element={<Checkout />} /> */}

                    {/* <Route
                        path="/orders/success/:orderCode"
                        element={<OrderSuccess />}
                    /> */}
                    <Route path="/orders/failed" element={<OrderFailed />} />
                </Route>

                <Route path="/cart" element={<Cart />} />
                <Route path="/orders/track" element={<OrderTracking />} />
                <Route
                    path="/orders/track/:orderCode"
                    element={<OrderTracking />}
                />

                <Route path="/pages/:slug" element={<DynamicPage />} />

                <Route path="/*" element={<NotFound />} />
            </Route>
        </Routes>
    );
};

export default App;
