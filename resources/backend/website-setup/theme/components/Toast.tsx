import React from 'react';

interface Toast {
    id: number;
    type: 'success' | 'error' | 'info';
    message: string;
}

interface ToastContainerProps {
    toasts: Toast[];
    onRemove: (id: number) => void;
}

const ToastContainer: React.FC<ToastContainerProps> = ({ toasts, onRemove }) => {
    return (
        <div className="fixed top-4 right-4 z-50 space-y-2">
            {toasts.map((toast) => (
                <div
                    key={toast.id}
                    className={`flex items-center justify-between px-4 py-3 rounded-md shadow-lg transform transition-all duration-300 ${
                        toast.type === 'success'
                            ? 'bg-blue-500 text-white'
                            : toast.type === 'error'
                            ? 'bg-red-500 text-white'
                            : 'bg-gray-600 text-white'
                    }`}
                >
                    <div className="flex items-center gap-2">
                        {toast.type === 'success' ? (
                            <i className="fa fa-check-circle"></i>
                        ) : toast.type === 'error' ? (
                            <i className="fa fa-exclamation-circle"></i>
                        ) : (
                            <i className="fa fa-info-circle"></i>
                        )}
                        <span>{toast.message}</span>
                    </div>
                    <button
                        onClick={() => onRemove(toast.id)}
                        className="ml-4 hover:opacity-75 transition-opacity"
                    >
                        <i className="fa fa-times"></i>
                    </button>
                </div>
            ))}
        </div>
    );
};

export default ToastContainer;
export type { Toast, ToastContainerProps };