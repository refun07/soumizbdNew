import { useState, useCallback, useEffect, useRef } from 'react';
import type { MediaFile, MediaFilter, MediaMeta, MediaView } from '../types/media';
import mediaManagerApi from '../services/mediaManagerApi';

interface UseMediaManagerProps {
    multiple?: boolean;
    accept?: string;
    initialValue?: number[];
}

interface UseMediaManagerReturn {
    // State
    files: MediaFile[];
    selectedIds: number[];
    loading: boolean;
    uploading: boolean;
    view: MediaView;
    filter: MediaFilter;
    meta: MediaMeta;
    hasSelected: boolean;
    selectedFiles: MediaFile[];
    
    // Actions
    fetchFiles: (skipLoading?: boolean) => Promise<void>;
    selectFile: (id: number) => void;
    deselectFile: (id: number) => void;
    selectAll: () => void;
    deselectAll: () => void;
    deleteSelected: () => Promise<void>;
    deleteFile: (id: number) => Promise<void>;
    uploadFiles: (files: File[]) => Promise<void>;
    removeBackground: (id: number) => Promise<void>;
    updateAltText: (id: number, altText: string) => Promise<void>;
    nextPage: () => void;
    prevPage: () => void;
    setView: (view: MediaView) => void;
    setFilter: (filter: Partial<MediaFilter>) => void;
    getItemsPerRow: () => number;
    reset: () => void;
}

const initialFilter: MediaFilter = {
    sort: 'newest',
    search: '',
    type: 'all',
    page: 1,
    limit: 44,
};

const initialMeta: MediaMeta = {
    current_page: 1,
    per_page: 44,
    total: 0,
    last_page: 1,
};

export const useMediaManager = ({
    multiple = false,
    accept = 'all',
    initialValue = [],
}: UseMediaManagerProps = {}): UseMediaManagerReturn => {
    const [files, setFiles] = useState<MediaFile[]>([]);
    const [selectedIds, setSelectedIds] = useState<number[]>(initialValue);
    const [loading, setLoading] = useState(false);
    const [uploading, setUploading] = useState(false);
    const [view, setView] = useState<MediaView>('browser');
    const [filter, setFilterState] = useState<MediaFilter>(initialFilter);
    const [meta, setMeta] = useState<MediaMeta>(initialMeta);
    
    const debounceTimer = useRef<ReturnType<typeof setTimeout> | null>(null);

    // Load initial files if initialValue provided
    useEffect(() => {
        const loadInitialFiles = async () => {
            if (initialValue.length > 0) {
                try {
                    const files = await mediaManagerApi.getByIds(initialValue);
                    // Validate selected IDs exist
                    const validIds = files.map(f => f.id);
                    setSelectedIds(prev => prev.filter(id => validIds.includes(id)));
                } catch (error) {
                    console.error('Error loading initial files:', error);
                }
            }
        };
        loadInitialFiles();
    }, []);

    // Fetch files from API
    const fetchFiles = useCallback(async (skipLoading = false) => {
        if (!skipLoading) setLoading(true);
        
        try {
            const response = await mediaManagerApi.getFiles(filter);
            setFiles(response.data || []);
            setMeta({
                current_page: response.current_page || 1,
                per_page: response.per_page || 44,
                total: response.total || 0,
                last_page: response.last_page || 1,
            });
        } catch (error) {
            console.error('Error fetching files:', error);
            setFiles([]);
        } finally {
            if (!skipLoading) setLoading(false);
        }
    }, [filter]);

    // Debounced fetch for filters
    const debouncedFetch = useCallback(() => {
        if (debounceTimer.current) {
            clearTimeout(debounceTimer.current);
        }
        debounceTimer.current = setTimeout(() => {
            fetchFiles(true);
        }, 500);
    }, [fetchFiles]);

    // Select a file
    const selectFile = useCallback((id: number) => {
        setSelectedIds(prev => {
            if (multiple) {
                if (prev.includes(id)) return prev;
                return [...prev, id];
            }
            return [id];
        });
    }, [multiple]);

    // Deselect a file
    const deselectFile = useCallback((id: number) => {
        setSelectedIds(prev => prev.filter(selectedId => selectedId !== id));
    }, []);

    // Select all visible files
    const selectAll = useCallback(() => {
        setSelectedIds((files || []).map(f => f.id));
    }, [files]);

    // Deselect all
    const deselectAll = useCallback(() => {
        setSelectedIds([]);
    }, []);

    // Delete selected files
    const deleteSelected = useCallback(async () => {
        if (selectedIds.length === 0) return;
        
        try {
            await mediaManagerApi.delete(selectedIds);
            setSelectedIds([]);
            await fetchFiles(true);
        } catch (error) {
            console.error('Error deleting files:', error);
            throw error;
        }
    }, [selectedIds, fetchFiles]);

    // Delete single file
    const deleteFile = useCallback(async (id: number) => {
        try {
            await mediaManagerApi.delete([id]);
            setSelectedIds(prev => prev.filter(selectedId => selectedId !== id));
            await fetchFiles(true);
        } catch (error) {
            console.error('Error deleting file:', error);
            throw error;
        }
    }, [fetchFiles]);

    // Upload files
    const uploadFiles = useCallback(async (newFiles: File[]) => {
        setUploading(true);
        
        try {
            const uploadedFiles = await mediaManagerApi.upload(newFiles);
            
            // Select uploaded files
            if (multiple) {
                setSelectedIds(prev => [...prev, ...uploadedFiles.map(f => f.id)]);
            } else if (uploadedFiles.length > 0) {
                setSelectedIds([uploadedFiles[0].id]);
            }
            
            // Switch back to browser view
            setView('browser');
            
            // Refresh file list
            await fetchFiles(true);
        } catch (error) {
            console.error('Error uploading files:', error);
            throw error;
        } finally {
            setUploading(false);
        }
    }, [multiple, fetchFiles]);

    // AI background removal
    const removeBackground = useCallback(async (id: number) => {
        try {
            setLoading(true);
            await mediaManagerApi.removeBackground(id);
            await fetchFiles(true);
        } catch (error) {
            console.error('Error removing background:', error);
            throw error;
        } finally {
            setLoading(false);
        }
    }, [fetchFiles]);

    // Update alt text
    const updateAltText = useCallback(async (id: number, altText: string) => {
        try {
            await mediaManagerApi.updateAltText(id, altText);
            // Update local state
            setFiles(prev => prev.map(f => 
                f.id === id ? { ...f, alt_text: altText } : f
            ));
        } catch (error) {
            console.error('Error updating alt text:', error);
            throw error;
        }
    }, []);

    // Pagination
    const nextPage = useCallback(() => {
        if (meta.current_page < meta.last_page) {
            setFilterState(prev => ({ ...prev, page: prev.page + 1 }));
        }
    }, [meta]);

    const prevPage = useCallback(() => {
        if (meta.current_page > 1) {
            setFilterState(prev => ({ ...prev, page: prev.page - 1 }));
        }
    }, [meta]);

    // Set filter with debounce
    const setFilter = useCallback((newFilter: Partial<MediaFilter>) => {
        setFilterState(prev => {
            const updated = { ...prev, ...newFilter };
            // Reset to page 1 when search/sort/type changes
            if (newFilter.search !== undefined || newFilter.sort !== undefined || newFilter.type !== undefined) {
                updated.page = 1;
            }
            return updated;
        });
    }, []);

    // Get items per row based on viewport
    const getItemsPerRow = useCallback((): number => {
        if (typeof window === 'undefined') return 5;
        
        const width = window.innerWidth;
        if (width >= 1280) return 9;
        if (width >= 768) return 7;
        if (width >= 640) return 5;
        if (width >= 490) return 4;
        return 3;
    }, []);

    // Reset state
    const reset = useCallback(() => {
        setFiles([]);
        setSelectedIds([]);
        setLoading(false);
        setUploading(false);
        setView('browser');
        setFilterState(initialFilter);
        setMeta(initialMeta);
    }, []);

    // Fetch files when filter changes (except for search which uses debounce)
    useEffect(() => {
        if (!filter.search) {
            fetchFiles();
        }
    }, [filter.sort, filter.type, filter.page, filter.limit, fetchFiles]);

    // Debounced search
    useEffect(() => {
        debouncedFetch();
        return () => {
            if (debounceTimer.current) {
                clearTimeout(debounceTimer.current);
            }
        };
    }, [filter.search]);

    const selectedFiles = (files || []).filter(f => selectedIds.includes(f.id));
    const hasSelected = selectedIds.length > 0;

    return {
        files,
        selectedIds,
        loading,
        uploading,
        view,
        filter,
        meta,
        hasSelected,
        selectedFiles,
        fetchFiles,
        selectFile,
        deselectFile,
        selectAll,
        deselectAll,
        deleteSelected,
        deleteFile,
        uploadFiles,
        removeBackground,
        updateAltText,
        nextPage,
        prevPage,
        setView,
        setFilter,
        getItemsPerRow,
        reset,
    };
};
