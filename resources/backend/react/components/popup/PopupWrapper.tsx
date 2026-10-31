import { ReactNode, useEffect, useRef } from 'react';
import { usePanelFocus } from '../../../../frontend/hooks/usePanelFocus';
import { LiaTimesSolid } from 'react-icons/lia';
import { cn } from '../../../react/utils/cn';
import { useAppContext } from '../../Context';

interface Props {
    children: ReactNode | ReactNode[];
    size?: 'sm' | 'md' | 'lg';
    isOpen: boolean;
    closeOnBackdropClick?: boolean;
    classNames?: {
        wrapper?: string;
        button?: string;
    };
}

const PopupWrapper = ({
    children,
    size = 'sm',
    classNames,
    isOpen,
    closeOnBackdropClick = true,
}: Props) => {
    const { closePopup } = useAppContext();
    const panel = useRef<HTMLDivElement>(null);
    usePanelFocus(isOpen, panel);
    useEffect(() => {
        if (!isOpen) return;
        const dismiss = (event: KeyboardEvent) => { if (event.key === 'Escape') closePopup(); };
        document.addEventListener('keydown', dismiss);
        return () => document.removeEventListener('keydown', dismiss);
    }, [isOpen, closePopup]);

    const sizeClasses = {
        sm: 'max-w-[400px]',
        md: 'max-w-[600px]',
        lg: 'max-w-[800px]',
    }[size];

    return (
        <div
            className={cn(
                'fixed inset-0 bg-black/60 flex items-center justify-center z-[100] transition-all duration-200',
                {
                    'opacity-0 invisible': !isOpen,
                    'opacity-100 visible': isOpen,
                },
            )}
            onClick={() => {
                if (closeOnBackdropClick) {
                    closePopup();
                }
            }}
        >
            <div ref={panel} role="dialog" aria-modal={isOpen || undefined} aria-label="Admin dialog" tabIndex={-1}
                className={cn(
                    'w-[calc(100vw-24px)] bg-background text-foreground border border-border rounded-md shadow-xl max-h-[90vh] overflow-y-auto relative',
                    sizeClasses,
                    classNames?.wrapper,
                )}
                onClick={(e) => e.stopPropagation()}
            >
                <button type="button" aria-label="Close dialog"
                    className={cn(
                        'min-w-11 min-h-11 inline-flex items-center justify-center absolute right-3 top-3 text-lg text-muted hover:text-foreground z-10 transition-colors',
                        classNames?.button,
                    )}
                    onClick={closePopup}
                >
                    <LiaTimesSolid />
                </button>
                {children}
            </div>
        </div>
    );
};

export default PopupWrapper;
