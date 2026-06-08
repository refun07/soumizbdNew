import type { MediaFile } from '../../types/media';
import { translate } from '../../utils/translate';
import MediaToolbar from './MediaToolbar';
import MediaGrid from './MediaGrid';
import type { MediaFilter } from '../../types/media';

interface MediaBrowserProps {
    files: MediaFile[];
    selectedIds: number[];
    loading: boolean;
    isRemovingBackground: boolean;
    filter: MediaFilter;
    onFilterChange: (filter: Partial<MediaFilter>) => void;
    onSelect: (id: number) => void;
    onDeselect: (id: number) => void;
    onShowInfo: (file: MediaFile) => void;
    onRemoveBackground: (id: number) => void;
    onCopyLink: (url: string) => void;
    onDownload: (id: number) => void;
    onDelete: (id: number) => void;
    onUploadClick: () => void;
}

const MediaBrowser = ({
    files,
    selectedIds,
    loading,
    isRemovingBackground,
    filter,
    onFilterChange,
    onSelect,
    onDeselect,
    onShowInfo,
    onRemoveBackground,
    onCopyLink,
    onDownload,
    onDelete,
    onUploadClick,
}: MediaBrowserProps) => {

    return (
        <div className="media-manager__browser flex flex-col h-full">
            {/* Toolbar */}
            <MediaToolbar
                filter={filter}
                onFilterChange={onFilterChange}
            />

            {/* Grid */}
            <div className="flex-1 overflow-hidden">
                <MediaGrid
                    files={files}
                    selectedIds={selectedIds}
                    loading={loading}
                    isRemovingBackground={isRemovingBackground}
                    onSelect={onSelect}
                    onDeselect={onDeselect}
                    onShowInfo={onShowInfo}
                    onRemoveBackground={onRemoveBackground}
                    onCopyLink={onCopyLink}
                    onDownload={onDownload}
                    onDelete={onDelete}
                    onUploadClick={onUploadClick}
                />
            </div>

            {/* Footer */}
            <div className="flex items-center justify-end px-6 py-4 border-t border-border">
                <div className="flex items-center gap-4">
                    <span className="text-sm text-muted">
                        {selectedIds.length > 0 && 
                            `${selectedIds.length} ${selectedIds.length === 1 ? translate('selected') : translate('selected')}`
                        }
                    </span>
                </div>
            </div>
        </div>
    );
};

export default MediaBrowser;
