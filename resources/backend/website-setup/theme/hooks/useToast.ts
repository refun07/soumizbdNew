import { useState } from 'react';
import { Toast } from '../components/Toast';

export const useToast = () => {
    const [toasts, setToasts] = useState<Toast[]>([]);

    const addToast = (type: 'success' | 'error' | 'info', message: string) => {
        const id = Date.now();
        setToasts(prev => [...prev, { id, type, message }]);

        // Auto remove toast after 3 seconds
        setTimeout(() => {
            setToasts(prev => prev.filter(toast => toast.id !== id));
        }, 3000);
    };

    const removeToast = (id: number) => {
        setToasts(prev => prev.filter(toast => toast.id !== id));
    };

    const success = (message: string) => addToast('success', message);
    const error = (message: string) => addToast('error', message);
    const info = (message: string) => addToast('info', message);

    return {
        toasts,
        addToast,
        removeToast,
        success,
        error,
        info
    };
};

export default useToast;