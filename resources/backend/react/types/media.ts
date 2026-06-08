export interface MediaFile {
    id: number;
    user_id: number;
    media_file: string;
    media_name: string;
    media_extension: string;
    media_type: 'image' | 'video' | 'audio' | 'document' | 'archive' | 'others';
    media_size: number;
    alt_text?: string;
    created_at: string;
    updated_at: string;
}

export interface MediaFilter {
    sort: 'newest' | 'oldest' | 'smallest' | 'largest';
    search: string;
    type: string;
    page: number;
    limit: number;
}

export interface MediaMeta {
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
}

export interface MediaResponse {
    data: MediaFile[];
    current_page: number;
    per_page: number;
    total: number;
    last_page: number;
}

export interface MediaManagerProps {
    isOpen: boolean;
    onClose: () => void;
    onSelect: (files: MediaFile[]) => void;
    multiple?: boolean;
    accept?: string;
    initialValue?: number[];
}

export type MediaView = 'browser' | 'uploader';
