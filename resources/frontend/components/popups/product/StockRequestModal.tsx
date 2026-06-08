import { useState } from 'react';
import { LiaTimesSolid } from 'react-icons/lia';
import { FaBell, FaImage, FaMinus, FaPlus } from 'react-icons/fa6';
import toast from 'react-hot-toast';
import { useDispatch } from 'react-redux';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAuth } from '../../../store/features/auth/authSlice';
import { useRequestStockMutation } from '../../../store/features/api/stockRequestApi';
import { translate } from '../../../utils/translate';
import ModalWrapper from '../ModalWrapper';
import Button from '../../buttons/Button';
import Input from '../../inputs/Input';
import InputGroup from '../../inputs/InputGroup';
import Label from '../../inputs/Label';
import Textarea from '../../inputs/Textarea';

const StockRequestModal = () => {
    const dispatch = useDispatch();
    const { popup, popupProps } = usePopup();
    const isActive = popup === 'stock-request';
    const { user, guestUserId } = useAuth();
    const [requestStock, { isLoading }] = useRequestStockMutation();

    const [quantity, setQuantity] = useState(1);
    const [note, setNote] = useState('');
    const [email, setEmail] = useState(user?.email || '');
    const [phone, setPhone] = useState(user?.phone || '');

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();

        const productId = popupProps.productId;
        const variationId = popupProps.variationId;

        if (!productId) {
            toast.error('Product information is missing. Please try again.');
            return;
        }

        const payload: any = {
            product_id: productId,
            quantity,
            note,
            guest_user_id: guestUserId,
        };

        if (variationId) {
            payload.variation_id = variationId;
        }

        if (user?.email) {
            payload.email = user.email;
        } else if (email) {
            payload.email = email;
        }

        if (user?.phone) {
            payload.phone = user.phone;
        } else if (phone) {
            payload.phone = phone;
        }

        try {
            const response: any = await requestStock(payload).unwrap();
            if (response.success) {
                toast.success(response.message || 'Stock request submitted successfully. We will notify you when stock is available.');
                dispatch(closePopup());
            } else if (response.already_requested) {
                toast.error(response.message || 'You have already requested stock for this item');
            } else {
                toast.error(response.message || 'Something went wrong. Please try again.');
            }
        } catch (error: any) {
            let errorMessage = 'Something went wrong. Please try again.';
            if (error?.data?.message) {
                errorMessage = error.data.message;
            } else if (error?.message) {
                errorMessage = error.message;
            } else if (typeof error === 'string') {
                errorMessage = error;
            }
            toast.error(errorMessage);
        }
    };

    const handleClose = () => {
        dispatch(closePopup());
    };

    if (!isActive) return null;

    const product = popupProps.product || {};
    const variation = popupProps.variation || {};

    return (
        <ModalWrapper isActive={isActive}>
            <button
                className="absolute right-5 top-5 text-xl text-white sm:text-primary z-[1]"
                onClick={handleClose}
            >
                <LiaTimesSolid />
            </button>
            <div className="theme-modal__body bg-white p-0">
                <div className="bg-primary text-white">
                    <div className="flex gap-4 items-center py-4 px-6">
                        <div className="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <FaBell className="text-xl" />
                        </div>
                        <div>
                            <h3 className="text-lg font-bold">
                                {translate('Request for Stock')}
                            </h3>
                            <p className="text-sm text-white/80">
                                {translate('We will notify you when stock is available')}
                            </p>
                        </div>
                    </div>
                </div>

                <div className="px-6 py-6">
                    {product.name && (
                        <div className="flex items-center gap-3 mb-6 p-3 bg-gray-50 rounded-lg">
                            <div className="w-12 h-12 rounded-md bg-gray-200 flex items-center justify-center overflow-hidden">
                                {popupProps.productImage ? (
                                    <img
                                        src={popupProps.productImage}
                                        alt={product.name}
                                        className="w-full h-full object-cover"
                                    />
                                ) : (
                                    <FaImage className="text-gray-400" />
                                )}
                            </div>
                            <div className="flex-grow">
                                <p className="font-medium text-sm line-clamp-2">{product.name}</p>
                                {variation.name && (
                                    <p className="text-xs text-gray-500">{variation.name}</p>
                                )}
                            </div>
                        </div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <InputGroup>
                            <Label htmlFor="quantity">{translate('Quantity')}</Label>
                            <div className="flex items-center gap-3">
                                <button
                                    type="button"
                                    className="w-10 h-10 rounded border border-primary-14 flex items-center justify-center hover:bg-gray-50"
                                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                                >
                                    <FaMinus />
                                </button>
                                <Input
                                    type="number"
                                    id="quantity"
                                    value={quantity}
                                    onChange={(e) => setQuantity(Math.max(1, Math.min(10, parseInt(e.target.value) || 1)))}
                                    min={1}
                                    max={10}
                                    className="text-center"
                                />
                                <button
                                    type="button"
                                    className="w-10 h-10 rounded border border-primary-14 flex items-center justify-center hover:bg-gray-50"
                                    onClick={() => setQuantity(Math.min(10, quantity + 1))}
                                >
                                    <FaPlus />
                                </button>
                            </div>
                        </InputGroup>

                        <InputGroup>
                            <Label htmlFor="note">{translate('Note (Optional)')}</Label>
                            <Textarea
                                id="note"
                                value={note}
                                onChange={(e) => setNote(e.target.value)}
                                placeholder={translate('Any special request or note...')}
                                rows={3}
                            />
                        </InputGroup>

                        {!user && (
                            <>
                                <InputGroup>
                                    <Label htmlFor="email">{translate('Email')} *</Label>
                                    <Input
                                        type="email"
                                        id="email"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        placeholder={translate('Enter your email')}
                                        required
                                    />
                                </InputGroup>

                                <InputGroup>
                                    <Label htmlFor="phone">{translate('Phone (Optional)')}</Label>
                                    <Input
                                        type="tel"
                                        id="phone"
                                        value={phone}
                                        onChange={(e) => setPhone(e.target.value)}
                                        placeholder={translate('Enter your phone number')}
                                    />
                                </InputGroup>
                            </>
                        )}

                        <div className="flex gap-3 pt-2">
                            <Button
                                type="button"
                                variant="outline"
                                className="flex-1"
                                onClick={handleClose}
                            >
                                {translate('Cancel')}
                            </Button>
                            <Button
                                type="submit"
                                variant="primary"
                                className="flex-1 font-bold"
                                isLoading={isLoading}
                            >
                                {translate('Request Stock')}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default StockRequestModal;
