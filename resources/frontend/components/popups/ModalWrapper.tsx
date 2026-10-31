import { ReactNode, useRef } from 'react';
import { usePopup } from '../../store/features/popup/popupSlice';
import { cn } from '../../utils/cn';
import { usePanelFocus } from '../../hooks/usePanelFocus';

interface Props {
    isActive: boolean;
    className?: string;
    children?: ReactNode | ReactNode[];
    size?: 'sm' | 'md' | 'lg' | 'xl';
    ariaLabel?: string;
}

const ModalWrapper = ({ isActive, size, className, children, ariaLabel = 'Dialog' }: Props) => {
    const element = useRef<HTMLDivElement>(null);
    usePanelFocus(isActive, element);

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
            ref={element}
            role="dialog"
            aria-modal={isActive || undefined}
            aria-label={ariaLabel}
            tabIndex={-1}
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
