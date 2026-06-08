import { MouseEvent } from 'react';
import { TfiPlus } from 'react-icons/tfi';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Props {
    onClick?: (e: MouseEvent<HTMLButtonElement>) => void;
    className?: string;
}

const AddAddressCard = ({ className, onClick }: Props) => {
    const dispatch = useAppDispatch();

    const handleClick = (e: MouseEvent<HTMLButtonElement>) => {
        dispatch(togglePopup('address'));
        onClick?.(e);
    };
    return (
        <div className={cn(className)}>
            <button
                className="h-full aspect-square p-5 border border-zinc-100 rounded-card inline-flex flex-col items-center justify-center gap-2.5 hover:border-secondary transition-all"
                onClick={handleClick}
            >
                <span className="text-4xl text-black/30">
                    <TfiPlus />
                </span>
                <span className="text-primary font-medium">
                    {translate('Add New')}
                </span>
            </button>
        </div>
    );
};

export default AddAddressCard;
