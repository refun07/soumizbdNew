import { useCallback, useEffect, useRef, useState } from 'react';
import { IPaginationMeta } from '../types';

interface UseInfiniteScrollOptions {
    pagination?: IPaginationMeta;
    onLoadMore: () => void;
    isLoading?: boolean;
}

interface UseInfiniteScrollReturn {
    loadMoreRef: React.RefObject<HTMLDivElement>;
    hasMore: boolean;
    isLoadingMore: boolean;
    reset: () => void;
}

export const useInfiniteScroll = ({
    pagination,
    onLoadMore,
    isLoading = false,
}: UseInfiniteScrollOptions): UseInfiniteScrollReturn => {
    const [isLoadingMore, setIsLoadingMore] = useState(false);
    const loadMoreRef = useRef<HTMLDivElement>(null);
    const hasTriggeredRef = useRef(false);

    const hasMore = pagination ? pagination.current_page < pagination.last_page : false;

    const reset = useCallback(() => {
        hasTriggeredRef.current = false;
        setIsLoadingMore(false);
    }, []);

    useEffect(() => {
        if (!loadMoreRef.current || !hasMore || isLoading) return;

        const observer = new IntersectionObserver(
            (entries) => {
                const [entry] = entries;
                if (entry.isIntersecting && !hasTriggeredRef.current && !isLoading) {
                    hasTriggeredRef.current = true;
                    setIsLoadingMore(true);
                    onLoadMore();
                }
            },
            {
                root: null,
                rootMargin: '100px',
                threshold: 0.1,
            }
        );

        observer.observe(loadMoreRef.current);

        return () => {
            observer.disconnect();
        };
    }, [hasMore, isLoading, onLoadMore]);

    // Reset the trigger flag when loading completes
    useEffect(() => {
        if (!isLoading && isLoadingMore) {
            setIsLoadingMore(false);
            hasTriggeredRef.current = false;
        }
    }, [isLoading, isLoadingMore]);

    return {
        loadMoreRef,
        hasMore,
        isLoadingMore,
        reset,
    };
};

export default useInfiniteScroll;
