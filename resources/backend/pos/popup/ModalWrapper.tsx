import React, { ReactNode } from 'react';
import { cn } from '../../react/utils/cn';
import Overlay from './Overlay';

interface Props {
    isActive: boolean;
    className?: string;
    size?: 'sm' | 'md' | 'lg' | 'xl';
    children?: ReactNode | ReactNode[];
    onClose?: () => void;
}

const ModalWrapper = ({
    isActive,
    className,
    size = 'sm',
    onClose,
    children,
}: Props) => {
    const sizeClasses = {
        sm: 'theme-modal--sm',
        md: 'theme-modal--md',
        lg: 'theme-modal--lg',
        xl: 'theme-modal--xl',
    };

    return (
        <>
            <div
                className={cn(`theme-modal`, sizeClasses[size], className, {
                    active: isActive,
                    'pointer-events-none': !isActive,
                })}
                aria-hidden={!isActive}
            >
                {children}
            </div>
            <Overlay open={isActive} onClick={onClose} />
        </>
    );
};

export default ModalWrapper;
