import { FaExclamationTriangle } from 'react-icons/fa';
import Button from '../inputs/Button';
import { translate } from '../../utils/translate';
import { cn } from '../../utils/cn';

interface DeleteConfirmModalProps {
    isOpen: boolean;
    onClose: () => void;
    onConfirm: () => void;
    count: number;
    isLoading?: boolean;
}

const DeleteConfirmModal = ({ 
    isOpen, 
    onClose, 
    onConfirm, 
    count,
    isLoading = false,
}: DeleteConfirmModalProps) => {
    if (!isOpen) return null;

    return (
        <div 
            className={cn(
                "fixed inset-0 bg-black/60 flex items-center justify-center z-[110] transition-all duration-200",
                isOpen ? "opacity-100 visible" : "opacity-0 invisible"
            )}
            onClick={onClose}
        >
            <div 
                className="bg-background rounded-md shadow-xl max-w-[400px] w-full mx-4 overflow-hidden"
                onClick={(e) => e.stopPropagation()}
            >
                <div className="p-6">
                    <div className="flex items-center gap-4 mb-4">
                        <div className="h-12 w-12 rounded-full bg-theme-alert/20 flex items-center justify-center flex-shrink-0">
                            <FaExclamationTriangle className="text-theme-alert text-xl" />
                        </div>
                        <div>
                            <h3 className="text-lg font-semibold text-foreground">
                                {translate('Delete Files')}
                            </h3>
                            <p className="text-muted text-sm mt-1">
                                {count === 1 
                                    ? translate('Are you sure you want to delete this file?')
                                    : translate(`Are you sure you want to delete ${count} files?`)
                                }
                            </p>
                        </div>
                    </div>

                    <p className="text-muted text-sm mb-6">
                        {translate('This action cannot be undone.')}
                    </p>

                    <div className="flex gap-3 justify-end">
                        <Button
                            variant="light"
                            onClick={onClose}
                            disabled={isLoading}
                        >
                            {translate('Cancel')}
                        </Button>
                        <Button
                            variant="danger"
                            onClick={onConfirm}
                            isLoading={isLoading}
                        >
                            {translate('Delete')}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default DeleteConfirmModal;
