import { useEffect } from 'react';
import { ISizeChartTable } from '../../types/product';
import { FaXmark } from 'react-icons/fa6';
import { translate } from '../../utils/translate';
import ProductSizeChartTable from '../product/ProductSizeChartTable';
import Image from '../Image';

interface SizeChartModalProps {
    showModal: boolean;
    imageUrl?: string | null;
    sizeChartTable?: ISizeChartTable | null;
    onClose: () => void;
}

const SizeChartModal = ({
    showModal,
    imageUrl,
    sizeChartTable,
    onClose,
}: SizeChartModalProps) => {
    // Close modal on ESC key
    useEffect(() => {
        const handleEscape = (e: KeyboardEvent) => {
            if (e.key === 'Escape') onClose();
        };

        if (showModal) {
            window.addEventListener('keydown', handleEscape);
        }

        return () => {
            window.removeEventListener('keydown', handleEscape);
        };
    }, [showModal, onClose]);

    // Prevent body scroll when modal is open
    useEffect(() => {
        document.body.style.overflow = showModal ? 'hidden' : 'unset';
        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [showModal]);

    if (!showModal || (!imageUrl && !sizeChartTable)) return null;

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            {/* Backdrop */}
            <div
                className="absolute inset-0 bg-black/60 backdrop-blur-sm"
                onClick={onClose}
            />

            {/* Modal */}
            <div className="relative w-full max-w-3xl max-h-[90vh] bg-white rounded-2xl shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                {/* Header */}
                <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 className="text-lg font-semibold text-gray-900">
                        {translate('Size Chart')}
                    </h3>
                    <button
                        onClick={onClose}
                        className="inline-flex items-center justify-center w-8 h-8 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors duration-200"
                        aria-label={translate('Close')}
                    >
                        <FaXmark className="w-5 h-5" />
                    </button>
                </div>

                {/* Content */}
                <div className="p-6 overflow-y-auto max-h-[calc(90vh-80px)]">
                    {sizeChartTable ? (
                        <ProductSizeChartTable chart={sizeChartTable} />
                    ) : null}

                    {imageUrl ? (
                        <div
                            className={`flex justify-center ${
                                sizeChartTable ? 'mt-6' : ''
                            }`}
                        >
                            <Image
                                src={imageUrl}
                                alt={translate('Size Chart')}
                                className="max-w-full h-auto rounded-lg"
                            />
                        </div>
                    ) : null}
                </div>
            </div>
        </div>
    );
};

export default SizeChartModal;
