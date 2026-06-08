import React from 'react';
import {
    IoTime,
    IoCheckmarkCircle,
    IoReturnDownBack,
    IoWarningOutline,
    IoCloseCircle,
} from 'react-icons/io5';

interface StatusCardsProps {
    quickData: any;
    loading: {
        quick: boolean;
    };
}

const StatusCards: React.FC<StatusCardsProps> = ({ quickData, loading }) => {
    return (
        <div className="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
            {loading.quick && !quickData ? (
                Array.from({ length: 5 }).map((_, i) => (
                    <div
                        key={i}
                        className="card p-4 rounded-lg shadow-sm animate-pulse"
                    >
                        <div className="flex justify-between items-center py-8">
                            <div className="flex items-center gap-2">
                                <div className="w-8 h-8 bg-muted rounded"></div>
                                <div className="h-3 bg-muted rounded w-16"></div>
                            </div>
                            <div className="h-5 bg-muted rounded w-8"></div>
                        </div>
                    </div>
                ))
            ) : (
                <>
                    <div className="card p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                        <div className="flex items-center gap-2">
                            <IoTime className="w-8 h-8 text-blue-500" />
                            <p className="text-xs text-muted text-center">
                                Order Placed
                            </p>
                        </div>
                        <p className="text-lg font-bold text-foreground">
                            {quickData?.orderStats?.placed || 0}
                        </p>
                    </div>
                    <div className="card p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                        <div className="flex items-center gap-2">
                            <IoCheckmarkCircle className="w-8 h-8 text-green-500" />
                            <p className="text-xs text-muted text-center">
                                Successful Order
                            </p>
                        </div>
                        <p className="text-lg font-bold text-foreground">
                            {quickData?.orderStats?.successful || 0}
                        </p>
                    </div>
                    <div className="card p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                        <div className="flex items-center gap-2">
                            <IoReturnDownBack className="w-8 h-8 text-orange-500" />
                            <p className="text-xs text-muted text-center">
                                Returned Order
                            </p>
                        </div>
                        <p className="text-lg font-bold text-foreground">
                            {quickData?.orderStats?.returned || 0}
                        </p>
                    </div>
                    <div className="card p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                        <div className="flex items-center gap-2">
                            <IoWarningOutline className="w-8 h-8 text-yellow-500" />
                            <p className="text-xs text-muted text-center">
                                Damaged Order
                            </p>
                        </div>
                        <p className="text-lg font-bold text-foreground">
                            {quickData?.orderStats?.damaged || 0}
                        </p>
                    </div>
                    <div className="card p-4 rounded-lg shadow-sm flex justify-between items-center py-8">
                        <div className="flex items-center gap-2">
                            <IoCloseCircle className="w-8 h-8 text-red-500" />
                            <p className="text-xs text-muted text-center">
                                Order Cancelled
                            </p>
                        </div>
                        <p className="text-lg font-bold text-foreground">
                            {quickData?.orderStats?.cancelled || 0}
                        </p>
                    </div>
                </>
            )}
        </div>
    );
};

export default StatusCards;