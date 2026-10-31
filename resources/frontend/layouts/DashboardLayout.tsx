import { FaHome } from 'react-icons/fa';
import { NavLink, Outlet, useLocation, useNavigate } from 'react-router-dom';
import Breadcrumb from '../components/Breadcrumb';
import { DashboardSidebarSkeleton, DashboardSkeleton } from '../components/skeletons/from-svg';
import { useAuth, userLoggedOut } from '../store/features/auth/authSlice';
import { apiSlice } from '../store/features/api/apiSlice';
import { useAppDispatch } from '../store/store';
import { translate } from '../utils/translate';
import { togglePopup } from '../store/features/popup/popupSlice';
import DashboardSidebar, { profileLinks } from './components/DashboardSidebar';

const DashboardLayout = () => {
    const navigate = useNavigate();
    const dispatch = useAppDispatch();
    const location = useLocation();
    const { isLoading, user } = useAuth();

    const activeLink = profileLinks.find((item) => {
        const regex = new RegExp(`^${item.urlRegex.source}$`);
        return location.pathname.match(regex);
    });

    const breadcrumbDom = (
        <Breadcrumb headingLevel="p"
            title={translate('Dashboard')}
            navigation={[
                { name: translate('Home'), link: '/', icon: <FaHome /> },
                { name: translate('My Account'), link: '/dashboard' },
                { name: translate(activeLink?.name) || '' },
            ]}
        />
    );

    if (isLoading) {
        return (
            <>
                {breadcrumbDom}
                <section className="mt-8">
                    <div className="container no-style grid grid-cols-1 lg:grid-cols-4">
                        <div className="hidden lg:block"><DashboardSidebarSkeleton /></div>
                        <div className="min-w-0 lg:col-span-3">
                            {activeLink?.preloader || <DashboardSkeleton />}
                        </div>
                    </div>
                </section>
            </>
        );
    }

    if (!user) {
        return <section className="container py-16 text-center">
            <h1 className="text-2xl font-semibold">Sign in to your account</h1>
            <p className="mt-3">Sign in to view your orders, wishlist and account details.</p>
            <button type="button" className="mt-6 min-h-11 rounded-full bg-[#087c3e] px-6 text-white" onClick={() => dispatch(togglePopup('signin'))}>Sign in</button>
        </section>;
    }

    return (
        <>
            {breadcrumbDom}
            <section className="mt-8">
                <div className="container no-style grid grid-cols-1 lg:grid-cols-4">
                    <div className="hidden lg:block border rounded-card">
                        <DashboardSidebar />
                    </div>

                    <div className="min-w-0 lg:col-span-3">
                        <nav aria-label="Account navigation" className="mb-5 grid grid-cols-2 gap-2 pb-2 sm:flex sm:overflow-x-auto lg:hidden">
                            {profileLinks.filter((link) => link.showInSidebar).map((link) => (
                                <NavLink key={link.url} to={link.url} className={({ isActive }) => `inline-flex min-h-11 shrink-0 items-center gap-2 rounded-md border px-3 text-sm ${isActive ? 'border-primary bg-primary text-white' : 'border-slate-200 text-slate-700'}`}>
                                    {link.icon}{translate(link.name)}
                                </NavLink>
                            ))}
                            <button type="button" className="col-span-2 inline-flex min-h-11 shrink-0 items-center justify-center rounded-md border border-slate-200 px-3 text-sm text-red-600 sm:col-auto" onClick={() => { dispatch(userLoggedOut()); dispatch(apiSlice.util.resetApiState()); navigate('/'); }}>
                                {translate('Logout')}
                            </button>
                        </nav>
                        <Outlet />
                    </div>
                </div>
            </section>
        </>
    );
};

export default DashboardLayout;
