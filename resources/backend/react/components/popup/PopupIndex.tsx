import GoalPopup from './GoalPopup';
import ProductListModal from '../order/ProductListModal';
import FraudCheckModal from '../order/FraudCheckModal';
import EditOrderModal from '../order/EditOrderModal';
import OrderQuickViewModal from '../order/OrderQuickViewModal';

const PopupIndex = () => {

    return (
        <>
            <GoalPopup />
            <ProductListModal />
            <FraudCheckModal />
            <EditOrderModal />
            <OrderQuickViewModal />
        </>
    );
};

export default PopupIndex;
