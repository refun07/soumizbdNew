/**
 * Format file size from bytes to human readable string
 */
export const formatFileSize = (bytes: number): string => {
    if (bytes === 0) return '0 B';
    
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

/**
 * Format date to readable string
 */
export const formatDate = (dateString: string): string => {
    const date = new Date(dateString);
    const options: Intl.DateTimeFormatOptions = {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    };
    return date.toLocaleDateString('en-US', options);
};

/**
 * Truncate filename for display
 */
export const truncateFilename = (filename: string, maxLength: number = 20): string => {
    if (filename.length <= maxLength) return filename;
    
    const extension = filename.split('.').pop() || '';
    const name = filename.substring(0, filename.lastIndexOf('.'));
    const truncatedName = name.substring(0, maxLength - extension.length - 3);
    
    return `${truncatedName}...${extension}`;
};

/**
 * Get icon based on file type
 */
export const getFileTypeIcon = (type: string): string => {
    const icons: Record<string, string> = {
        image: 'FaFileImage',
        video: 'FaFileVideo',
        audio: 'FaFileAudio',
        document: 'FaFileAlt',
        archive: 'FaFileArchive',
        others: 'FaFile',
    };
    
    return icons[type] || 'FaFile';
};

/**
 * Calculate items per row based on viewport width
 */
export const getItemsPerRow = (): number => {
    if (typeof window === 'undefined') return 5;
    
    const width = window.innerWidth;
    
    if (width >= 1280) return 9;
    if (width >= 768) return 7;
    if (width >= 640) return 5;
    if (width >= 490) return 4;
    return 3;
};

/**
 * Get MIME type filter from accept string
 */
export const getMimeTypeFromAccept = (accept: string): string => {
    const mimeMap: Record<string, string> = {
        'image/*': 'image',
        'video/*': 'video',
        'audio/*': 'audio',
        'application/pdf': 'document',
        'application/zip': 'archive',
    };
    
    return mimeMap[accept] || 'all';
};
