import { useState, useRef, useEffect } from 'react';
import {
    FaCheck,
    FaEllipsisV,
    FaInfoCircle,
    FaMagic,
    FaLink,
    FaDownload,
    FaTrash,
    FaFileImage,
    FaFileVideo,
    FaFileAudio,
    FaFileAlt,
    FaFileArchive,
    FaFile,
} from 'react-icons/fa';
import type { MediaFile } from '../../types/media';
import { translate } from '../../utils/translate';
import { cn } from '../../utils/cn';

interface MediaCardProps {
    file: MediaFile;
    isSelected: boolean;
    onSelect: (id: number) => void;
    onDeselect: (id: number) => void;
    onShowInfo: (file: MediaFile) => void;
    onRemoveBackground: (id: number) => void;
    onCopyLink: (url: string) => void;
    onDownload: (id: number) => void;
    onDelete: (id: number) => void;
    isRemovingBackground?: boolean;
}

const MediaCard = ({
    file,
    isSelected,
    onSelect,
    onDeselect,
    onShowInfo,
    onRemoveBackground,
    onCopyLink,
    onDownload,
    onDelete,
    isRemovingBackground = false,
}: MediaCardProps) => {
    const [isDropdownOpen, setIsDropdownOpen] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);

    // Close dropdown when clicking outside
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
                setIsDropdownOpen(false);
            }
        };

        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    const handleClick = () => {
        if (isSelected) {
            onDeselect(file.id);
        } else {
            onSelect(file.id);
        }
    };

    const getFileIcon = () => {
        switch (file.media_type) {
            case 'image':
                return <FaFileImage className="text-4xl text-primary" />;
            case 'video':
                return <FaFileVideo className="text-4xl text-purple-500" />;
            case 'audio':
                return <FaFileAudio className="text-4xl text-yellow-500" />;
            case 'document':
                return <FaFileAlt className="text-4xl text-blue-500" />;
            case 'archive':
                return <FaFileArchive className="text-4xl text-orange-500" />;
            default:
                return <FaFile className="text-4xl text-gray-500" />;
        }
    };

    const handleAction = (action: string) => {
        setIsDropdownOpen(false);
        
        switch (action) {
            case 'info':
                onShowInfo(file);
                break;
            case 'bg-remove':
                onRemoveBackground(file.id);
                break;
            case 'copy-link':
                onCopyLink(`/${file.media_file}`);
                break;
            case 'download':
                onDownload(file.id);
                break;
            case 'delete':
                onDelete(file.id);
                break;
        }
    };

    return (
        <div className="relative group">
            {/* Media Card */}
            <div
                className={cn(
                    "media-manager__item aspect-square relative rounded-md overflow-hidden cursor-pointer border-2 transition-all",
                    isSelected
                        ? "border-primary ring-2 ring-primary/30"
                        : "border-transparent hover:border-secondary-light/50"
                )}
                onClick={handleClick}
                tabIndex={0}
                role="button"
                aria-pressed={isSelected}
            >
                {/* Checkmark */}
                <span
                    className={cn(
                        "media-manager__check-mark absolute top-2 right-2 z-10 h-6 w-6 rounded-full bg-primary text-white flex items-center justify-center transition-all",
                        isSelected ? "opacity-100 scale-100" : "opacity-0 scale-75"
                    )}
                >
                    <FaCheck className="text-xs" />
                </span>

                {/* Image or Icon */}
                {file.media_type === 'image' ? (
                    <img
                        src={`/${file.media_file}`}
                        alt={file.alt_text || file.media_name}
                        className="w-full h-full object-cover"
                        loading="lazy"
                    />
                ) : (
                    <div className="w-full h-full bg-secondary/10 flex items-center justify-center">
                        {getFileIcon()}
                    </div>
                )}

                {/* Background removal loading overlay */}
                {isRemovingBackground && (
                    <div className="absolute inset-0 bg-black/60 flex items-center justify-center">
                        <div className="text-center text-white">
                            <FaMagic className="text-2xl mx-auto mb-2 animate-pulse" />
                            <p className="text-xs">{translate('Processing...')}</p>
                        </div>
                    </div>
                )}
            </div>

            {/* Dropdown Menu */}
            <div
                ref={dropdownRef}
                className="absolute top-2 right-2 z-20"
            >
                <button
                    type="button"
                    onClick={(e) => {
                        e.stopPropagation();
                        setIsDropdownOpen(!isDropdownOpen);
                    }}
                    className={cn(
                        "h-8 w-8 rounded bg-white text-secondary-light flex items-center justify-center shadow-md transition-all hover:bg-gray-100",
                        isDropdownOpen ? "opacity-100" : "opacity-0 group-hover:opacity-100"
                    )}
                >
                    <FaEllipsisV className="text-sm" />
                </button>

                {/* Dropdown Options */}
                {isDropdownOpen && (
                    <div className="absolute top-full right-0 mt-1 w-[150px] bg-white rounded-md shadow-lg border border-border overflow-hidden z-30">
                        <ul className="py-1 text-xs">
                            <li>
                                <button
                                    type="button"
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        handleAction('info');
                                    }}
                                    className="w-full px-3 py-2 text-left flex items-center gap-2 hover:bg-gray-100 text-foreground"
                                >
                                    <FaInfoCircle />
                                    <span>{translate('Details')}</span>
                                </button>
                            </li>
                            
                            {file.media_type === 'image' && (
                                <li>
                                    <button
                                        type="button"
                                        onClick={(e) => {
                                            e.stopPropagation();
                                            handleAction('bg-remove');
                                        }}
                                        className="w-full px-3 py-2 text-left flex items-center gap-2 hover:bg-gray-100 text-foreground"
                                    >
                                        <FaMagic className="text-purple-500" />
                                        <span>{translate('Remove Background')}</span>
                                    </button>
                                </li>
                            )}
                            
                            <li>
                                <button
                                    type="button"
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        handleAction('copy-link');
                                    }}
                                    className="w-full px-3 py-2 text-left flex items-center gap-2 hover:bg-gray-100 text-foreground"
                                >
                                    <FaLink />
                                    <span>{translate('Copy Link')}</span>
                                </button>
                            </li>
                            
                            <li>
                                <a
                                    href={`/${file.media_file}`}
                                    download
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        handleAction('download');
                                    }}
                                    className="w-full px-3 py-2 text-left flex items-center gap-2 hover:bg-gray-100 text-foreground block"
                                >
                                    <FaDownload />
                                    <span>{translate('Download')}</span>
                                </a>
                            </li>
                            
                            <li className="border-t border-border mt-1 pt-1">
                                <button
                                    type="button"
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        handleAction('delete');
                                    }}
                                    className="w-full px-3 py-2 text-left flex items-center gap-2 hover:bg-theme-alert/10 text-theme-alert"
                                >
                                    <FaTrash />
                                    <span>{translate('Delete')}</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                )}
            </div>
        </div>
    );
};

export default MediaCard;
