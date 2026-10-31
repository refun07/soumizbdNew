import { closePopup, usePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { useEffect } from 'react';

interface Props {
    onClick?: () => void;
}

const Overlay = ({ onClick }: Props) => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    useEffect(() => {
        if (!popup) return;
        const handleEscape = (event: KeyboardEvent) => {
            if (event.key === 'Escape') dispatch(closePopup());
        };
        document.addEventListener('keydown', handleEscape);
        return () => document.removeEventListener('keydown', handleEscape);
    }, [popup, dispatch]);

    const handleClick = () => {
        if (popup) {
            dispatch(closePopup());
        }
    };

    if (popupProps?.overlay === false) {
        return null;
    }

    return (
        <div
            className={`fixed inset-0 bg-black/[.6] z-[4] ${
                popup ? 'opacity-100 visible' : 'opacity-0 invisible'
            } transition-all duration-300`}
            onClick={onClick || handleClick}
        ></div>
    );
};

export default Overlay;
