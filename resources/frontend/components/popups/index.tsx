import Overlay from '../../layouts/components/Overlay';
import AbandonedCartPopup from './cart/AbandonedCartPopup';
import ConfirmationPopup from './ConfirmationPopup';
import DashboardSidebarModal from './DashboardSidebarModal';
import ReviewModal from './ReviewModal';
import SearchPopup from './SearchPopup';
import AddressModal from './address/AddressModal';
import Signin from './auth/Signin';
import Signup from './auth/Signup';
import UserVerificationPopup from './auth/UserVerification';
import ForgetPasswordPopup from './auth/forget-password';
import CartSidebar from './cart/CartSidebar';
import CategorySidebar from './category/CategorySidebar';
import CouponDetails from './coupon/CouponDetails';
import HomepagePopup from './HomepagePopup';
import ProductPreviewModal from './product/ProductPreviewModal';
import StockRequestModal from './product/StockRequestModal';
import OtpVerification from './verification/OtpVerification';
import ConfirmStatusUpdatePopup from './order/ConfirmStatusUpdatePopup';
const Popups = () => {
    return (
        <>
            <CategorySidebar />
            <CartSidebar />
            <AbandonedCartPopup />
            <DashboardSidebarModal />
            <ProductPreviewModal />
            <CouponDetails />
            <ReviewModal />
            <SearchPopup />
            <AddressModal />
            <ForgetPasswordPopup />
            <UserVerificationPopup />
            <ConfirmationPopup />
            <ConfirmStatusUpdatePopup />
            <Signin />
            <Signup />
            <OtpVerification />
            <HomepagePopup />
            <StockRequestModal />

            {/* common overlay */}
            <Overlay />
        </>
    );
};

export default Popups;
