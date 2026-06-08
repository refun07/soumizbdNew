import { LiaTimesSolid } from 'react-icons/lia';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { translate } from '../../../utils/translate';
import AddressForm from '../../order/AddressForm';
import ModalWrapper from '../ModalWrapper';

const AddressModal = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const isActive = popup === 'address';

    return (
        <ModalWrapper isActive={isActive} className="p-4 sm:p-8 rounded-card">
            <div className="flex justify-between">
                <h4 className="ek-h3">{translate('Add Address')}</h4>
                <button
                    className="text-xl text-primary"
                    onClick={() => dispatch(closePopup())}
                >
                    <LiaTimesSolid />
                </button>
            </div>
            <AddressForm
                address={popupProps.address}
                className="mt-0 border-none max-sm:pt-3 "
            />
        </ModalWrapper>
    );
};

export default AddressModal;
