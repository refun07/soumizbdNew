import { axiosInstance } from '../utils/axios';
import axios from 'axios';
import type { MediaFile, MediaResponse, MediaFilter } from '../types/media';

declare global {
    interface Window {
        backendApiUrl: string;
    }
}

const axiosNoCache = axios.create({
    baseURL: window.backendApiUrl,
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
});

axiosNoCache.interceptors.request.use((config) => {
    const token = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement;
    config.headers['X-CSRF-TOKEN'] = token?.content || '';
    return config;
});

const mediaManagerApi = {
    /**
     * Get paginated uploaded files
     */
    getFiles: async (params: Partial<MediaFilter>): Promise<MediaResponse> => {
        const response = await axiosNoCache.get('/file-manager/get-uploaded-files', {
            params: {
                sort: params.sort || 'newest',
                search: params.search || '',
                type: params.type || 'all',
                page: params.page || 1,
                limit: params.limit || 44,
            },
        });
        return response.data;
    },

    /**
     * Upload files
     */
    upload: async (files: File[]): Promise<MediaFile[]> => {
        const uploadedFiles: MediaFile[] = [];
        
        for (const file of files) {
            const formData = new FormData();
            formData.append('media_file', file);
            
            const response = await axiosInstance.post('/file-manager/upload', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                },
            });
            
            if (response.data) {
                uploadedFiles.push(response.data);
            }
        }
        
        return uploadedFiles;
    },

    /**
     * Delete file(s) - supports single or bulk delete
     */
    delete: async (ids: number[]): Promise<void> => {
        const idString = ids.join(',');
        await axiosInstance.delete(`/file-manager/destroy/${idString}`);
    },

    /**
     * Get files by IDs (for initial value loading)
     */
    getByIds: async (ids: number[]): Promise<MediaFile[]> => {
        if (!ids.length) return [];
        
        const response = await axiosInstance.post('/file-manager/get-file-by-ids', {
            ids: ids.join(','),
        });
        return response.data;
    },

    /**
     * AI background removal
     */
    removeBackground: async (id: number): Promise<void> => {
        await axiosInstance.post(`/file-manager/bg-remove/${id}`);
    },

    /**
     * Update alt text
     */
    updateAltText: async (id: number, altText: string): Promise<void> => {
        await axiosInstance.post(`/file-manager/alt-text/${id}`, {
            alt_text: altText,
        });
    },

    /**
     * Download file
     */
    download: (id: number): void => {
        const link = document.createElement('a');
        link.href = `${window.backendApiUrl}/file-manager/download/${id}`;
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    },

    /**
     * Copy file link to clipboard
     */
    copyLink: async (url: string): Promise<boolean> => {
        try {
            await navigator.clipboard.writeText(url);
            return true;
        } catch (err) {
            console.error('Failed to copy link:', err);
            return false;
        }
    },
};

export default mediaManagerApi;
