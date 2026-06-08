import { ReactNode } from 'react';
import { usePopup } from '../../store/features/popup/popupSlice';
import { cn } from '../../utils/cn';

interface Props {
    isActive: boolean;
    className?: string;
    children?: ReactNode | ReactNode[];
    size?: 'sm' | 'md' | 'lg' | 'xl';
}

const ModalWrapper = ({ isActive, size, className, children }: Props) => {
    // console.log('is active',isActive)

    const { size: sizeFromState } = usePopup();

    const modalSize = size || sizeFromState;

    const sizeClasses = {
        sm: 'theme-modal--sm',
        md: 'theme-modal--md',
        lg: 'theme-modal--lg',
        xl: 'theme-modal--xl',
    };

    return (
        <div
            className={cn(`theme-modal`, sizeClasses[modalSize], className, {
                active: isActive,
                'pointer-events-none': !isActive,
            })}
            aria-hidden={!isActive}
        >
            {children}
        </div>
    );
};

export default ModalWrapper;
