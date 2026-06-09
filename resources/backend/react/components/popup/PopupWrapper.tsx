import { ReactNode } from 'react';
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
            <div
                className={cn(
                    'bg-background text-foreground border border-border rounded-md shadow-xl max-h-[90vh] overflow-y-auto relative',
                    sizeClasses,
                    classNames?.wrapper,
                )}
                onClick={(e) => e.stopPropagation()}
            >
                <button
                    className={cn(
                        'absolute right-3 top-3 text-lg text-muted hover:text-foreground z-10 transition-colors',
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
