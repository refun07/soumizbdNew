import { FaPlus } from 'react-icons/fa';
import type { MediaFile } from '../../types/media';
import { translate } from '../../utils/translate';
import { cn } from '../../utils/cn';
import MediaCard from './MediaCard';
import EmptyState from './EmptyState';
import Spinner from '../loader/Spinner';

interface MediaGridProps {
    files: MediaFile[];
    selectedIds: number[];
    loading: boolean;
    isRemovingBackground: boolean;
    onSelect: (id: number) => void;
    onDeselect: (id: number) => void;
    onShowInfo: (file: MediaFile) => void;
    onRemoveBackground: (id: number) => void;
    onCopyLink: (url: string) => void;
    onDownload: (id: number) => void;
    onDelete: (id: number) => void;
    onUploadClick: () => void;
}

const MediaGrid = ({
    files,
    selectedIds,
    loading,
    isRemovingBackground,
    onSelect,
    onDeselect,
    onShowInfo,
    onRemoveBackground,
    onCopyLink,
    onDownload,
    onDelete,
    onUploadClick,
}: MediaGridProps) => {
    const showEmptyState = !loading && files.length === 0;
    const showInitialLoading = loading && files.length === 0;

    return (
        <div
            className={cn(
                "media-manager grid gap-2 p-2 overflow-y-auto overscroll-contain relative h-full content-start",
                "grid-cols-3",
                "xs:grid-cols-3",
                "sm:grid-cols-4",
                "md:grid-cols-5",
                "lg:grid-cols-7",
                "xl:grid-cols-9"
            )}
        >
            {showInitialLoading ? (
                <div className="col-span-full flex items-center justify-center h-full">
                    <Spinner size="xlarge" />
                </div>
            ) : showEmptyState ? (
                <div className="col-span-full h-full">
                    <EmptyState onUploadClick={onUploadClick} />
                </div>
            ) : (
                <>
                    {/* Upload Button Card - Always First */}
                    <div
                        onClick={onUploadClick}
                        className="aspect-square border-2 border-dashed border-secondary bg-secondary/[.02] rounded-md cursor-pointer flex items-center flex-col justify-center hover:bg-secondary/5 transition-colors group"
                        role="button"
                        tabIndex={0}
                    >
                        <span className="leading-tight">
                            <FaPlus className="text-secondary text-2xl lg:text-5xl group-hover:scale-110 transition-transform" />
                        </span>
                        <p className="text-xs lg:text-sm text-secondary mt-2">{translate('Add Media')}</p>
                    </div>

                    {/* Media Cards */}
                    {files.map((file) => (
                        <MediaCard
                            key={file.id}
                            file={file}
                            isSelected={selectedIds.includes(file.id)}
                            onSelect={onSelect}
                            onDeselect={onDeselect}
                            onShowInfo={onShowInfo}
                            onRemoveBackground={onRemoveBackground}
                            onCopyLink={onCopyLink}
                            onDownload={onDownload}
                            onDelete={onDelete}
                            isRemovingBackground={isRemovingBackground}
                        />
                    ))}
                </>
            )}

            {/* Overlay loading indicator for filter/sort operations */}
            {loading && files.length > 0 && (
                <div className="absolute inset-0 bg-background/50 backdrop-blur-sm flex items-center justify-center z-10 pointer-events-none">
                    <Spinner size="xlarge" />
                </div>
            )}
        </div>
    );
};

export default MediaGrid;
