import { FaPhoneAlt, FaTrashAlt } from 'react-icons/fa';
import { FaLocationDot, FaPenToSquare } from 'react-icons/fa6';
import { useDeleteAddressMutation } from '../../store/features/checkout/checkoutApi';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { IAddress } from '../../types/checkout';
import { cn } from '../../utils/cn';

interface Props {
    address: IAddress;
    className?: string;

    editable?: boolean;
    deletable?: boolean;
    selectable?: boolean;

    onClick?: () => void;
    onEdit?: () => void;
    onDelete?: () => void;
}

const AddressCard = ({
    address,
    className,
    editable = true,
    deletable = true,
    selectable = true,
    onEdit,
    onDelete,
    onClick,
}: Props) => {
    const dispatch = useAppDispatch();
    const [deleteAddress, { isLoading: isDeletingAddress }] =
        useDeleteAddressMutation();

    const handleDelete = async () => {
        await deleteAddress({ id: address.id });
        onDelete?.();
    };

    const handleEdit = () => {
        dispatch(
            togglePopup({
                popup: 'address',
                popupProps: { address },
            }),
        );
        onEdit?.();
    };

    return (
        <div className="relative">
            <div
                className={cn(
                    `border border-zinc-100 rounded-card py-5 px-7 transition-all`,
                    className,
                    {
                        'cursor-pointer hover:border-secondary': selectable,
                    },
                )}
                onClick={onClick}
            >
                <h5 className="text-black/70 mb-2 uppercase">{address.type}</h5>

                <p className="mb-1.5 flex gap-2">
                    <span className="text-xl text-primary mt-1">
                        <FaLocationDot />
                    </span>
                    <span>{address.address}</span>
                </p>
                {address.direction ? (
                    <p className="text-black/70 pl-7 mt-2">
                        {address.direction}
                    </p>
                ) : null}
                {address.phone ? (
                    <p className="flex items-center gap-2 mt-2">
                        <span className="text-xl text-primary mt-1">
                            <FaPhoneAlt />
                        </span>
                        <span>{address.phone}</span>
                    </p>
                ) : null}
            </div>

            <div className="absolute top-4 right-4 flex gap-2.5">
                {deletable && (
                    <button
                        onClick={handleDelete}
                        className="text-rose-500"
                        disabled={isDeletingAddress}
                    >
                        <FaTrashAlt />
                    </button>
                )}
                {editable && (
                    <button onClick={handleEdit} className="text-stone-300">
                        <FaPenToSquare />
                    </button>
                )}
            </div>
        </div>
    );
};

export default AddressCard;
