import { useEffect, useRef, useState } from 'react';
import { useGetHomepagePopupQuery } from '../../store/features/api/homeApis/commonHomeApi';
import { togglePopup, usePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { cn } from '../../utils/cn';

const HomepagePopup = () => {
    const dispatch = useAppDispatch();
    const { popup } = usePopup();
    const isActive = popup === 'popup-image';

    const { data: popupData, isLoading } = useGetHomepagePopupQuery();
    const [showPopup, setShowPopup] = useState(false);
    const [isExiting, setIsExiting] = useState(false);
    const closeTimeoutRef = useRef<number | null>(null);
    const autoHideTimeoutRef = useRef<number | null>(null);

    const clearTimers = () => {
        if (closeTimeoutRef.current) {
            window.clearTimeout(closeTimeoutRef.current);
            closeTimeoutRef.current = null;
        }

        if (autoHideTimeoutRef.current) {
            window.clearTimeout(autoHideTimeoutRef.current);
            autoHideTimeoutRef.current = null;
        }
    };

    useEffect(() => {
        // Check if popup should be shown
        if (
            popupData?.showHomepagePopup === '1' &&
            popupData?.homepagePopupImage &&
            !sessionStorage.getItem('homepagePopupClosed')
        ) {
            setShowPopup(true);
            setIsExiting(false);
            dispatch(togglePopup('popup-image'));
        }
    }, [popupData, dispatch]);

    const handleClose = () => {
        if (isExiting) {
            return;
        }

        clearTimers();
        setIsExiting(true);
        sessionStorage.setItem('homepagePopupClosed', 'true');
        closeTimeoutRef.current = window.setTimeout(() => {
            setShowPopup(false);
            setIsExiting(false);
            dispatch(togglePopup(null));
        }, 200);
    };

    const handleImageClick = () => {
        if (popupData?.homepagePopupLink) {
            window.open(popupData.homepagePopupLink, '_blank');
        }
    };

    useEffect(() => {
        if (
            !showPopup ||
            !isActive ||
            popupData?.homepagePopupAutoHideEnabled !== '1'
        ) {
            return;
        }

        const durationSeconds = Math.max(
            1,
            Number(popupData?.homepagePopupDuration) || 5,
        );

        autoHideTimeoutRef.current = window.setTimeout(() => {
            handleClose();
        }, durationSeconds * 1000);

        return () => {
            if (autoHideTimeoutRef.current) {
                window.clearTimeout(autoHideTimeoutRef.current);
                autoHideTimeoutRef.current = null;
            }
        };
    }, [
        showPopup,
        isActive,
        popupData?.homepagePopupAutoHideEnabled,
        popupData?.homepagePopupDuration,
    ]);

    useEffect(() => {
        return () => {
            clearTimers();
        };
    }, []);

    // Don't render anything if not active or loading or no data
    if (isLoading || !showPopup || !isActive) {
        return null;
    }

    return (
        <div
            className={cn(
                'fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm transition-opacity duration-200',
                isExiting ? 'opacity-0' : 'opacity-100'
            )}
            onClick={handleClose}
        >
            <div
                className={cn(
                    'relative h-screen w-screen transition-all duration-200 ease-out',
                    isExiting ? 'opacity-0 scale-95' : 'opacity-100 scale-100'
                )}
                onClick={(e) => e.stopPropagation()}
            >
                {/* Close button (X) in top-right corner */}
                <button
                    onClick={handleClose}
                    className={cn(
                        'absolute text-white hover:text-gray-300 transition-all duration-200 z-10 rounded-full bg-black/45 p-1.5',
                        'top-4 right-4 sm:top-6 sm:right-6',
                        isExiting ? 'opacity-0 translate-x-2' : 'opacity-100 translate-x-0'
                    )}
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        className="w-8 h-8"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

                {/* Popup Image */}
                {popupData?.homepagePopupImage && (
                    <button
                        onClick={handleImageClick}
                        className={cn(
                            'block h-full w-full overflow-hidden bg-transparent transition-transform duration-200 hover:scale-[1.01]',
                            popupData.homepagePopupLink ? 'cursor-pointer' : 'cursor-default'
                        )}
                        disabled={!popupData?.homepagePopupLink}
                    >
                        <img
                            src={popupData.homepagePopupImage}
                            alt="Homepage Popup"
                            className="h-full w-full object-fill"
                        />
                    </button>
                )}
            </div>
        </div>
    );
};

export default HomepagePopup;
