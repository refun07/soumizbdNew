import { useState, useEffect, useCallback } from 'react';
import { LiaTimesSolid } from 'react-icons/lia';
import { FaTrash, FaCheck } from 'react-icons/fa';
import type { MediaFile, MediaManagerProps } from '../../types/media';
import { useMediaManager } from '../../hooks/useMediaManager';
import { translate } from '../../utils/translate';
import { cn } from '../../utils/cn';
import Button from '../inputs/Button';
import MediaBrowser from './MediaBrowser';
import MediaUploader from './MediaUploader';
import MediaInfoModal from './MediaInfoModal';
import DeleteConfirmModal from './DeleteConfirmModal';
import mediaManagerApi from '../../services/mediaManagerApi';

const MediaManagerModal = ({
    isOpen,
    onClose,
    onSelect,
    multiple = false,
    accept = 'all',
    initialValue = [],
}: MediaManagerProps) => {
    const [selectedFileForInfo, setSelectedFileForInfo] = useState<MediaFile | null>(null);
    const [isInfoModalOpen, setIsInfoModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [showSuccessToast, setShowSuccessToast] = useState(false);
    const [toastMessage, setToastMessage] = useState('');

    const {
        files,
        selectedIds,
        loading,
        uploading,
        view,
        filter,
        meta,
        hasSelected,
        selectFile,
        deselectFile,
        deleteSelected,
        deleteFile,
        uploadFiles,
        removeBackground,
        updateAltText,
        nextPage,
        prevPage,
        setView,
        setFilter,
        reset,
        fetchFiles,
    } = useMediaManager({
        multiple,
        accept,
        initialValue,
    });

    useEffect(() => {
        if (isOpen && files.length === 0) {
            fetchFiles();
        }
    }, [isOpen, files.length, fetchFiles]);

    useEffect(() => {
        if (!isOpen) {
            reset();
            setSelectedFileForInfo(null);
            setIsInfoModalOpen(false);
            setIsDeleteModalOpen(false);
        }
    }, [isOpen, reset]);

    const handleConfirm = () => {
        const selectedFiles = files.filter(f => selectedIds.includes(f.id));
        onSelect(selectedFiles);
        onClose();
    };

    const handleShowInfo = useCallback((file: MediaFile) => {
        setSelectedFileForInfo(file);
        setIsInfoModalOpen(true);
    }, []);

    const handleCopyLink = useCallback(async (url: string) => {
        const success = await mediaManagerApi.copyLink(url);
        if (success) {
            showToast(translate('Link copied to clipboard'));
        }
    }, []);

    const handleDownload = useCallback((id: number) => {
        mediaManagerApi.download(id);
    }, []);

    const handleDelete = useCallback(async (id: number) => {
        try {
            await deleteFile(id);
            showToast(translate('File deleted successfully'));
        } catch (error) {
            showToast(translate('Failed to delete file'));
        }
    }, [deleteFile]);

    const handleDeleteSelected = async () => {
        try {
            await deleteSelected();
            setIsDeleteModalOpen(false);
            showToast(translate('Files deleted successfully'));
        } catch (error) {
            showToast(translate('Failed to delete files'));
        }
    };

    const handleRemoveBackground = useCallback(async (id: number) => {
        try {
            await removeBackground(id);
            showToast(translate('Background removed successfully'));
        } catch (error) {
            showToast(translate('Failed to remove background'));
        }
    }, [removeBackground]);

    const showToast = (message: string) => {
        setToastMessage(message);
        setShowSuccessToast(true);
        setTimeout(() => setShowSuccessToast(false), 3000);
    };

    useEffect(() => {
        const handleEscape = (e: KeyboardEvent) => {
            if (e.key === 'Escape' && isOpen) {
                if (isInfoModalOpen) {
                    setIsInfoModalOpen(false);
                } else if (isDeleteModalOpen) {
                    setIsDeleteModalOpen(false);
                } else {
                    onClose();
                }
            }
        };

        document.addEventListener('keydown', handleEscape);
        return () => document.removeEventListener('keydown', handleEscape);
    }, [isOpen, isInfoModalOpen, isDeleteModalOpen, onClose]);

    useEffect(() => {
        if (isOpen) {
            const originalOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            return () => {
                document.body.style.overflow = originalOverflow;
            };
        }
    }, [isOpen]);

    if (!isOpen) return null;

    return (
        <>
            <div
                className="fixed inset-0 bg-black/60 flex items-center justify-center z-[100] transition-all duration-200"
                onClick={onClose}
            >
                <div
                    className="bg-background rounded-md shadow-xl w-[95vw] h-[90vh] max-w-[1400px] flex flex-col overflow-hidden"
                    onClick={(e) => e.stopPropagation()}
                >
                    <div className="card__title flex items-center justify-between p-4 border-b border-border flex-shrink-0">
                        <h4 className="text-base font-bold text-foreground">
                            {view === 'browser' ? translate('Upload Files') : translate('Upload Files')}
                        </h4>

                        <div className="flex gap-5 items-center">
                            {hasSelected && view === 'browser' && (
                                <button
                                    type="button"
                                    onClick={() => setIsDeleteModalOpen(true)}
                                    className="text-lg text-theme-alert hover:text-theme-alert/80 transition-colors"
                                    title={translate('Delete selected')}
                                >
                                    <FaTrash />
                                </button>
                            )}

                            <button
                                type="button"
                                onClick={onClose}
                                className="text-lg text-secondary-light hover:text-foreground transition-colors"
                            >
                                <LiaTimesSolid />
                            </button>
                        </div>
                    </div>

                    <div className="card__content grow flex flex-col overflow-hidden relative">
                        {view === 'browser' ? (
                            <MediaBrowser
                                files={files}
                                selectedIds={selectedIds}
                                loading={loading}
                                isRemovingBackground={loading}
                                filter={filter}
                                onFilterChange={setFilter}
                                onSelect={selectFile}
                                onDeselect={deselectFile}
                                onShowInfo={handleShowInfo}
                                onRemoveBackground={handleRemoveBackground}
                                onCopyLink={handleCopyLink}
                                onDownload={handleDownload}
                                onDelete={handleDelete}
                                onUploadClick={() => setView('uploader')}
                            />
                        ) : (
                            <MediaUploader
                                onUpload={uploadFiles}
                                onClose={() => setView('browser')}
                                isUploading={uploading}
                                accept={accept}
                            />
                        )}
                    </div>

                    {view === 'browser' && (
                        <div className="flex items-center justify-between px-6 py-4 border-t border-border flex-shrink-0">
                            <div className="flex items-center gap-4">
                                <Button
                                    variant="dark"
                                    onClick={prevPage}
                                    disabled={meta.current_page === 1 || loading}
                                >
                                    {translate('Prev')}
                                </Button>
                                <Button
                                    onClick={nextPage}
                                    disabled={meta.current_page === meta.last_page || loading}
                                >
                                    {translate('Next')}
                                </Button>
                            </div>

                            <div className="flex items-center gap-4">
                                {hasSelected && (
                                    <span className="text-sm text-muted">
                                        {selectedIds.length} {selectedIds.length === 1 ? translate('media selected') : translate('media selected')}
                                    </span>
                                )}

                                <Button
                                    onClick={handleConfirm}
                                    disabled={!hasSelected}
                                    className="set-files"
                                >
                                    <FaCheck className="mr-2" />
                                    {translate('Confirm')}
                                </Button>
                            </div>
                        </div>
                    )}
                </div>
            </div>

            <MediaInfoModal
                isOpen={isInfoModalOpen}
                onClose={() => setIsInfoModalOpen(false)}
                file={selectedFileForInfo}
                onUpdate={updateAltText}
            />

            <DeleteConfirmModal
                isOpen={isDeleteModalOpen}
                onClose={() => setIsDeleteModalOpen(false)}
                onConfirm={handleDeleteSelected}
                count={selectedIds.length}
                isLoading={loading}
            />

            {showSuccessToast && (
                <div className="fixed bottom-4 right-4 z-[120] bg-theme-green text-white px-4 py-3 rounded-md shadow-lg flex items-center gap-2 animate-fade-in">
                    <FaCheck />
                    <span>{toastMessage}</span>
                </div>
            )}
        </>
    );
};

export default MediaManagerModal;
