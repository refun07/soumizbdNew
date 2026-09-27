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
import {
    buildGTM4WPData,
    buildGTMCustomerContext,
    createMetaEventId,
} from './utils/gtmCustomerData';
import localAxios from './utils/axios';

const HomeIndex = lazy(() => import('./pages/home'));
const Brands = lazy(() => import('./pages/Brands'));
const Shops = lazy(() => import('./pages/shops/Shops'));

const ProductDetails = lazy(() => import('./pages/ProductDetails'));
const Brand = lazy(() => import('./pages/search-filter/Brand'));
const Category = lazy(() => import('./pages/search-filter/Category'));
const FeaturedProducts = lazy(() => import('./pages/search-filter/FeaturedProducts'));
const OnSaleProducts = lazy(() => import('./pages/search-filter/OnSaleProducts'));
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
const BkashPersonalPayment = lazy(
    () => import('./pages/order/BkashPersonalPayment'),
);

const Cart = lazy(() => import('./pages/order/Cart'));
const OrderTracking = lazy(() => import('./pages/order/OrderTracking'));

const DynamicPage = lazy(() => import('./pages/DynamicPage'));
const NotFound = lazy(() => import('./pages/NotFound'));

const installMetaPageViewEventIdPatch = () => {
    const win = window as any;

    if (win.__metaPageViewEventIdPatchInstalled || typeof win.fbq !== 'function') {
        return;
    }

    const originalFbq = win.fbq;

    const patchedFbq = (...args: any[]) => {
        const [method, eventName] = args;

        if (
            (method === 'track' || method === 'trackCustom') &&
            eventName === 'PageView' &&
            win.__metaPageViewEventId
        ) {
            args[3] = {
                ...(args[3] || {}),
                eventID: win.__metaPageViewEventId,
            };
        }

        return originalFbq(...args);
    };

    Object.assign(patchedFbq, originalFbq);
    win.fbq = patchedFbq;
    win._fbq = patchedFbq;
    win.__metaPageViewEventIdPatchInstalled = true;
};

const App = () => {
    const dispatch = useDispatch();
    const { carts, dashboardInfo, isLoading, user } = useAuth();
    const { selectedShops } = useCheckoutData();
    const location = useLocation();

    const gtmEvent = useGTMEventsNew();

    // Track previous search params for infinite scroll detection
    const prevSearchRef = useRef(location.search);
    const lastPageViewKeyRef = useRef('');

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
        const sendPageView = async () => {
            const pageViewKey = `${location.pathname}${location.search}`;

            if (lastPageViewKeyRef.current === pageViewKey) return;

            lastPageViewKeyRef.current = pageViewKey;

            const eventId = createMetaEventId('page_view', pageViewKey);
            const pageLocation = window.location.href;
            const pageReferrer = document.referrer || '';

            (window as any).__metaPageViewEventId = eventId;
            installMetaPageViewEventIdPatch();

            gtmEvent({
                event: 'page_view',
                event_id: eventId,
                eventID: eventId,
                eventId,
                meta_event_id: eventId,
                page_location: pageLocation,
                page_referrer: pageReferrer,
                ...(await buildGTM4WPData({
                    user,
                    totalOrders: dashboardInfo?.totalOrders,
                    totalOrderValue: dashboardInfo?.totalExpense,
                })),
                customer: buildGTMCustomerContext(
                    user,
                    dashboardInfo?.totalOrders,
                ),
            });

            localAxios.post('/tracking/page-view', {
                eventId,
                url: pageLocation,
                referrer: pageReferrer,
            }).catch(() => {});
        };

        sendPageView();
    }, [location.pathname, location.search, user, dashboardInfo]);

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
                <Route path="/featured-products" element={<FeaturedProducts />} />
                <Route path="/on-sale-products" element={<OnSaleProducts />} />
                {/* end::uses search and filter */}

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
                <Route path="/orders/failed" element={<OrderFailed />} />
                <Route
                    path="/orders/bkash-payment/:orderCode"
                    element={<BkashPersonalPayment />}
                />
                <Route path="/payment" element={<PaymentGateway />} />
                <Route element={<ProtectedLayout />}>
                    {/* <Route path="/checkout" element={<Checkout />} /> */}

                    {/* <Route
                        path="/orders/success/:orderCode"
                        element={<OrderSuccess />}
                    /> */}
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
