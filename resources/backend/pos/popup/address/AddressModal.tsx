import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { FiTruck } from 'react-icons/fi';
import { LiaTimesSolid } from 'react-icons/lia';
import Button from '../../../react/components/inputs/Button';
import { translate } from '../../../react/utils/translate';
import { IAddress } from '../../types';
import { getAddresses } from '../../utils/actions';
import ModalWrapper from '../ModalWrapper';
import AddressForm from './AddressForm';

interface Props {
    customerId: number | undefined;
    onSuccess?: (address: IAddress) => void;
}

const AddressModal = ({ customerId, onSuccess }: Props) => {
    const [isActive, setIsActive] = useState(false);
    const [isAddAddressFormActive, setIsAddAddressFormActive] = useState(false);

    const [addresses, setAddresses] = useState<IAddress[]>([]);

    useEffect(() => {
        if (!customerId) return;

        getAddresses(customerId).then((data) => {
            setAddresses(data);
        });
    }, [customerId]);

    const handleClose = () => {
        setIsActive(false);
        setIsAddAddressFormActive(false);
    };

    const handleAddressAdded = (address: IAddress) => {
        onSuccess?.(address);
        toast.success('Address created successfully', {
            position: 'top-right',
        });
        setIsActive(false);
        setIsAddAddressFormActive(false);

        if (customerId) {
            getAddresses(customerId).then((data) => {
                setAddresses(data);
            });
        }
    };

    const handleSelectAddress = (address: IAddress) => {
        onSuccess?.(address);
        setIsActive(false);
    };

    return (
        <>
            <Button
                variant="primary"
                className="text-2xl w-10"
                onClick={() => setIsActive(true)}
                disabled={!customerId}
            >
                <FiTruck />
            </Button>

            <ModalWrapper isActive={isActive} onClose={handleClose}>
                <div className="theme-modal__header">
                    <h5>{translate('Address')}</h5>

                    <button
                        className="text-xl text-white sm:text-secondary-light"
                        onClick={handleClose}
                    >
                        <LiaTimesSolid />
                    </button>
                </div>

                <div className="theme-modal__body">
                    {!isAddAddressFormActive ? (
                        <div className="space-y-3">
                            {addresses.map((address) => (
                                <div
                                    key={address.id}
                                    className="px-5 py-4 border border-border rounded-md cursor-pointer hover:bg-background-hover"
                                    onClick={() => handleSelectAddress(address)}
                                >
                                    <div>
                                        <p>{address.fullAddress}</p>
                                    </div>
                                </div>
                            ))}

                            <button
                                className="px-5 py-4 border border-border rounded-md cursor-pointer bg-background-primary-light hover:bg-background-hover text-center w-full"
                                onClick={() => setIsAddAddressFormActive(true)}
                            >
                                Add New Address
                            </button>
                        </div>
                    ) : (
                        <AddressForm
                            customerId={customerId}
                            onSuccess={handleAddressAdded}
                        />
                    )}
                </div>
            </ModalWrapper>
        </>
    );
};

export default AddressModal;
