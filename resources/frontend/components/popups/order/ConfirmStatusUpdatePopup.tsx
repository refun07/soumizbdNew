import { LiaTimesSolid } from 'react-icons/lia';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { STORAGE_KEYS } from '../../../types';
import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';
import ModalWrapper from '../ModalWrapper';
import axios from 'axios';

interface Order {
    id: number;
    code: number;
    customerName?: string;
    customerPhone?: string;
    createdDate?: string;
    orderCount?: string;
    totalAmount: number;
    deliveryStatus?: string;
    deliveryStatusToShow?: string;
    paymentStatus: string;
    address?: string;
    orderPrefix?: string;
    latestDeliveryUpdate?: {
        status: string;
        note: string;
        actorName: string;
        actorRole?: string | null;
        createdDate: string;
        createdTime: string;
    } | null;
}
const ConfirmStatusUpdatePopup = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const isActive = popup === 'confirmation-status-update';

    const handleConfirm = () => {
        updateOrderStatus()
        dispatch(closePopup());
    };

    const handleCancel = () => {
        dispatch(closePopup());
    };
    const updateOrderStatus = async ()=>{
        const {order} = popupProps;
        const config = {
            method: 'post',
            url: '/admin/api/orders/update-order-status',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem(STORAGE_KEYS.AUTH_KEY)}`,
                'Content-Type': 'application/json',
                'Accept-Language': localStorage.getItem('i18nextLng')!
            },
            data: {
               order_id:order.order_id,
               status:order.id
            }
        };

        const response = await axios(config);
        if(response.data.status == 200){
            const updatedOrder = response.data.result?.order;
            popupProps.order.setOrders((prev:Order[])=>{
                return prev.map((item:Order)=>{
                    if(item.id==order.order_id){
                        return {
                            ...item,
                            deliveryStatus: updatedOrder?.deliveryStatus ?? order.id,
                            deliveryStatusToShow: updatedOrder?.deliveryStatusToShow ?? order.status,
                            latestDeliveryUpdate: updatedOrder?.latestDeliveryUpdate ?? item.latestDeliveryUpdate ?? null,
                        };
                    }
                    return item;
                })

            })
        }
    }
    return (
        <ModalWrapper
            isActive={isActive}
            className="max-w-[400px] rounded-card"
        >
            <div className="flex justify-between px-5 py-4 border-b border-gray-200">
                <h4 className="ek-h3">{translate('Confirmation')}</h4>

                <button
                    className="text-lg"
                    onClick={() => dispatch(closePopup())}
                >
                    <LiaTimesSolid />
                </button>
            </div>

            <div className="px-5 pt-3 pb-6">
                <h3 className="text-lg font-medium mb-2">{popupProps.title}</h3>
                <p>{popupProps.message}</p>

                <div className="flex gap-3 mt-6">
                    <Button variant="primary" onClick={handleConfirm}>
                        {translate('Confirm')}
                    </Button>
                    <Button variant="secondary" onClick={handleCancel}>
                        {translate('Cancel')}
                    </Button>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default ConfirmStatusUpdatePopup;
