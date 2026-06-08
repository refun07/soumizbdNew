import React, { useEffect, useState } from 'react';
import { CiBoxList } from 'react-icons/ci';
import { FaRegTrashAlt } from 'react-icons/fa';
import { LiaTimesSolid } from 'react-icons/lia';
import Button from '../../react/components/inputs/Button';
import { IHoldCartGroup, IPosCartGroup } from '../types';
import { deleteHoldCart, getHoldOrders } from '../utils/actions';
import ModalWrapper from './ModalWrapper';

interface Props {
    onSelectOrder: (order: IPosCartGroup) => void;
}

const HoldOrdersModal = ({ onSelectOrder }: Props) => {
    const [isActive, setIsActive] = useState(false);
    const [holdOrders, setHoldOrders] = useState<IHoldCartGroup[]>([]);

    useEffect(() => {
        if (!isActive) return;

        getHoldOrders().then(setHoldOrders);
    }, [isActive]);

    const handleClose = () => setIsActive(false);

    const handleSelectOrder = (order: IPosCartGroup) => {
        onSelectOrder(order);
        setIsActive(false);
    };

    const handleDeleteOrder = async (orderId: number) => {
        await deleteHoldCart(orderId);
        setHoldOrders(
            holdOrders.filter((order) => order.posCartGroupId !== orderId),
        );
    };

    return (
        <>
            <Button
                variant="warning"
                size="md"
                className="aspect-square text-3xl !p-0"
                onClick={() => setIsActive(true)}
            >
                <CiBoxList />
            </Button>

            <ModalWrapper isActive={isActive} onClose={handleClose}>
                <div className="theme-modal__header">
                    <h5>Hold Orders</h5>

                    <button
                        className="text-xl text-white sm:text-secondary-light"
                        onClick={handleClose}
                    >
                        <LiaTimesSolid />
                    </button>
                </div>

                <div className="theme-modal__body space-y-3">
                    {!holdOrders.length ? (
                        <p className="text-center py-5">No orders on hold</p>
                    ) : (
                        holdOrders.map((order) => (
                            <div className="relative">
                                <div
                                    key={order.posCartGroupId}
                                    className="px-5 py-4 border border-border rounded-md cursor-pointer hover:bg-background-hover"
                                    onClick={() => handleSelectOrder(order)}
                                >
                                    <h5>
                                        {order.posCarts.length} items on hold:
                                    </h5>
                                    <div className="mt-2">
                                        {order.posCarts.map((cart) => (
                                            <div key={cart.id}>
                                                <p className="flex text-muted">
                                                    {cart.qty}x
                                                    <span className="max-w-[200px] line-clamp-1 ml-2">
                                                        {cart.product.name}
                                                    </span>
                                                </p>
                                            </div>
                                        ))}
                                    </div>
                                </div>

                                <button
                                    className="h-8 w-8 text-theme-alert bg-theme-alert/10 rounded-sm flex items-center justify-center absolute top-3 right-3"
                                    onClick={() =>
                                        handleDeleteOrder(order.posCartGroupId)
                                    }
                                >
                                    <FaRegTrashAlt />
                                </button>
                            </div>
                        ))
                    )}
                </div>
            </ModalWrapper>
        </>
    );
};

export default HoldOrdersModal;
