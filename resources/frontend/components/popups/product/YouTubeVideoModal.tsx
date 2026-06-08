// YouTubeVideoModal.tsx
import { useEffect } from 'react';

interface YouTubeVideoModalProps {
    showModal: boolean;
    videoUrl: string;
    onClose: () => void;
}

const YouTubeVideoModal = ({ showModal, videoUrl, onClose }: YouTubeVideoModalProps) => {
    // Close modal on ESC key
    useEffect(() => {
        const handleEscape = (e: KeyboardEvent) => {
            if (e.key === 'Escape') onClose();
        };

        if (showModal) {
            window.addEventListener('keydown', handleEscape);
        }

        return () => {
            window.removeEventListener('keydown', handleEscape);
        };
    }, [showModal]);

    // Prevent body scroll when modal is open
    useEffect(() => {
        document.body.style.overflow = showModal ? 'hidden' : 'unset';
        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [showModal]);

    if (!showModal || !videoUrl) return null;

    return (
        <div className="fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center p-4">
            <div className="relative w-full max-w-4xl bg-black rounded-lg overflow-hidden">
                <button
                    onClick={onClose}
                    className="absolute top-4 right-4 text-white hover:text-secondary z-50 p-1 rounded-full hover:bg-white hover:bg-opacity-10 transition-colors duration-200"
                >
                    <svg
                        className="w-8 h-8"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>

                <div className="aspect-video">
                    <iframe
                        width="100%"
                        height="100%"
                        src={`${videoUrl}?rel=0&modestbranding=1&autohide=1&showinfo=0&controls=1`}
                        title="YouTube video player"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowFullScreen
                        className="rounded-lg min-h-[250px] md:min-h-[400px] lg:min-h-[500px]"
                    />
                </div>
            </div>

            {/* Click outside to close */}
            <div className="absolute inset-0 -z-10" onClick={onClose} />
        </div>
    );
};

export default YouTubeVideoModal;
