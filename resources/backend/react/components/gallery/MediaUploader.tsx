import { useRef } from 'react';
import { FaCloudUploadAlt } from 'react-icons/fa';
import { FaXmark } from 'react-icons/fa6';
import { translate } from '../../utils/translate';
import { cn } from '../../utils/cn';
import Spinner from '../loader/Spinner';

interface MediaUploaderProps {
    onUpload: (files: File[]) => void;
    onClose: () => void;
    isUploading: boolean;
    accept?: string;
}

const MediaUploader = ({ onUpload, onClose, isUploading, accept = '*' }: MediaUploaderProps) => {
    const fileInputRef = useRef<HTMLInputElement>(null);

    const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
        const files = e.target.files;
        if (files && files.length > 0) {
            onUpload(Array.from(files));
        }
    };

    const handleDrop = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        e.stopPropagation();
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            onUpload(Array.from(files));
        }
    };

    const handleDragOver = (e: React.DragEvent<HTMLDivElement>) => {
        e.preventDefault();
        e.stopPropagation();
    };

    const handleClick = () => {
        fileInputRef.current?.click();
    };

    const getAcceptString = () => {
        if (accept === 'image/*') {
            return 'image/*';
        }
        if (accept === 'video/*') {
            return 'video/*';
        }
        if (accept === 'audio/*') {
            return 'audio/*';
        }
        return '*';
    };

    return (
        <div className="media-manager__uploader flex flex-col h-full">
            {/* Header */}
            <div className="card__title flex items-center justify-between p-4 border-b border-border">
                <h4 className="text-base font-bold text-foreground">
                    {translate('Upload Files')}
                </h4>

                <button
                    type="button"
                    onClick={onClose}
                    className="text-lg text-secondary-light hover:text-foreground transition-colors"
                    disabled={isUploading}
                >
                    <FaXmark />
                </button>
            </div>

            {/* Upload Zone */}
            <div className="card__content grow min-h-[300px] p-4">
                <input
                    ref={fileInputRef}
                    type="file"
                    multiple
                    accept={getAcceptString()}
                    onChange={handleFileSelect}
                    className="hidden"
                />

                <div
                    onClick={handleClick}
                    onDrop={handleDrop}
                    onDragOver={handleDragOver}
                    className={cn(
                        "h-full w-full border-2 border-dashed rounded-lg flex flex-col items-center justify-center cursor-pointer transition-all border-secondary bg-secondary/5 hover:bg-secondary/10",
                        isUploading && "pointer-events-none opacity-50"
                    )}
                >
                    {isUploading ? (
                        <div className="text-center">
                            <Spinner size="xlarge" className="mx-auto mb-4" />
                            <p className="text-muted">{translate('Uploading...')}</p>
                        </div>
                    ) : (
                        <>
                            <FaCloudUploadAlt className="text-6xl mb-4 text-secondary" />

                            <p className="text-lg text-foreground mb-2">
                                {translate('Drag & drop files here')}
                            </p>

                            <p className="text-sm text-muted">
                                {translate('or')}{' '}
                                <span className="text-primary hover:underline">
                                    {translate('browse')}
                                </span>
                            </p>

                            <p className="text-xs text-muted mt-4">
                                {accept === 'image/*' && translate('Supported: JPEG, PNG, GIF, WebP, SVG')}
                                {accept === 'video/*' && translate('Supported: MP4, WebM, OGG, MOV, AVI')}
                                {accept === 'audio/*' && translate('Supported: MP3, WAV, OGG, AAC, WMA')}
                                {accept === '*' && translate('All file types supported')}
                            </p>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

export default MediaUploader;
