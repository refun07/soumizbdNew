import { useState } from 'react';
import { LiaTimesSolid } from 'react-icons/lia';
import { FaSave } from 'react-icons/fa';
import type { MediaFile } from '../../types/media';
import { formatFileSize, formatDate, truncateFilename } from '../../utils/formatters';
import { translate } from '../../utils/translate';
import { cn } from '../../utils/cn';
import Button from '../inputs/Button';
import Input from '../inputs/Input';

interface MediaInfoModalProps {
    isOpen: boolean;
    onClose: () => void;
    file: MediaFile | null;
    onUpdate: (id: number, altText: string) => Promise<void>;
}

const MediaInfoModal = ({ isOpen, onClose, file, onUpdate }: MediaInfoModalProps) => {
    const [altText, setAltText] = useState(file?.alt_text || '');
    const [isUpdating, setIsUpdating] = useState(false);

    // Update local state when file changes
    if (file && altText !== (file.alt_text || '')) {
        setAltText(file.alt_text || '');
    }

    if (!isOpen || !file) return null;

    const handleUpdate = async () => {
        setIsUpdating(true);
        try {
            await onUpdate(file.id, altText);
            onClose();
        } catch (error) {
            console.error('Failed to update alt text:', error);
        } finally {
            setIsUpdating(false);
        }
    };

    const fullFilename = `${file.media_name}.${file.media_extension}`;

    return (
        <div 
            className={cn(
                "fixed inset-0 bg-black/60 flex items-center justify-center z-[110] transition-all duration-200",
                isOpen ? "opacity-100 visible" : "opacity-0 invisible"
            )}
            onClick={onClose}
        >
            <div 
                className="bg-background rounded-md shadow-xl max-w-[500px] w-full mx-4 max-h-[90vh] overflow-y-auto"
                onClick={(e) => e.stopPropagation()}
            >
                {/* Header */}
                <div className="flex items-center justify-between p-4 border-b border-border">
                    <h4 className="text-base font-bold text-foreground">
                        {translate('File Details')}
                    </h4>
                    <button
                        onClick={onClose}
                        className="text-lg text-secondary-light hover:text-foreground transition-colors"
                    >
                        <LiaTimesSolid />
                    </button>
                </div>

                {/* Content */}
                <div className="p-5">
                    {/* Image Preview */}
                    <div className="flex gap-4 mb-6">
                        <div className="max-w-[150px] flex-shrink-0">
                            <img 
                                src={`/${file.media_file}`}
                                alt={file.alt_text || fullFilename}
                                className="w-full h-auto max-h-[150px] object-cover rounded-md"
                            />
                        </div>
                        <div className="grow text-muted space-y-1 text-sm">
                            <h5 className="text-foreground font-medium">
                                {truncateFilename(fullFilename, 25)}
                            </h5>
                            <p>{formatFileSize(file.media_size)}</p>
                            <p>{formatDate(file.created_at)}</p>
                            <p>
                                <span className="text-foreground">{translate('By')}</span>
                                {' '}Admin
                            </p>
                        </div>
                    </div>

                    {/* Alt Text Form */}
                    <div className="mt-4">
                        <div className="flex gap-3 items-center">
                            <div className="whitespace-nowrap text-sm text-muted">
                                {translate('Alternate Text')}
                            </div>
                            <Input
                                name="alt_text"
                                placeholder={translate('Type Here...')}
                                value={altText}
                                onChange={(e) => setAltText(e.target.value)}
                                classNames={{
                                    group: 'grow',
                                    input: 'w-full',
                                }}
                            />
                        </div>
                    </div>

                    {/* Update Button */}
                    <div className="mt-4">
                        <Button
                            type="button"
                            onClick={handleUpdate}
                            isLoading={isUpdating}
                            className="w-full justify-center"
                        >
                            <FaSave />
                            {translate('UPDATE')}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default MediaInfoModal;
