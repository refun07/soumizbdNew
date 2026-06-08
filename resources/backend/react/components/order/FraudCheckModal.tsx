import React, { useEffect, useState } from 'react';
import { useAppContext } from '../../Context';
import PopupWrapper from '../popup/PopupWrapper';
import axios from 'axios';
import { LiaTimesSolid } from 'react-icons/lia';
import { cn } from '../../utils/cn';

interface FraudCheckData {
    [courierName: string]: {
        logo?: string;
        data_type: string;
        customer_rating?: string;
        risk_level?: string;
        message?: string;
        success_rate?: string | number;
        total?: number;
        success?: number;
        cancel?: number;
        cancel_rate?: string | number;
        orders?: number;
        deliveries?: number;
        offers?: number;
        offer_rate?: string | number;
    };
}

const FraudCheckModal = () => {
    const { popup, closePopup } = useAppContext();
    const [fraudData, setFraudData] = useState<FraudCheckData | null>(null);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState<string | null>(null);

    const phoneNumber = popup.props?.phoneNumber;

    useEffect(() => {
        const fetchFraudData = async () => {
            if (!phoneNumber) return;

            setLoading(true);
            setError(null);
            try {
                const response = await axios.post('/admin/api/fraud-check/courier-info', {
                    phone_number: phoneNumber
                });

                if (response.data.success) {
                    setFraudData(response.data.data);
                } else {
                    setError(response.data.message || 'Failed to fetch fraud data');
                }
            } catch (err: any) {
                console.error('Error fetching fraud data:', err);
                setError(err.response?.data?.message || 'An error occurred while checking fraud data');
            } finally {
                setLoading(false);
            }
        };

        if (popup.name === 'fraud-check' && phoneNumber) {
            fetchFraudData();
        }
    }, [popup.name, phoneNumber]);

    if (popup.name !== 'fraud-check') return null;

    const renderStars = (rating: number) => {
        return Array.from({ length: 5 }, (_, i) => (
            <i key={i} className={`fas fa-star ${i < rating ? 'text-yellow-400' : 'text-gray-300'}`}></i>
        ));
    };

    const getOverallStats = () => {
        if (!fraudData) return { totalOrders: 0, totalDeliveries: 0, totalOffers: 0, successRate: 0 };
        
        let totalOrders = 0;
        let totalDeliveries = 0;
        let totalOffers = 0;
        
        Object.values(fraudData).forEach(data => {
            totalOrders += data.orders || data.total || 0;
            totalDeliveries += data.deliveries || data.success || 0;
            totalOffers += data.offers || 0;
        });
        
        const successRate = totalOrders > 0 ? (totalDeliveries / totalOrders) * 100 : 0;
        
        return { totalOrders, totalDeliveries, totalOffers, successRate };
    };

    const stats = getOverallStats();

    return (
        <>
            {/* Transparent Backdrop */}
            <div
                className={cn(
                    'fixed inset-0 z-[2] bg-black/60 transition-all',
                    {
                        'opacity-0 invisible': !popup.name,
                    },
                )}
                onClick={closePopup}
            />

            {/* Modal */}
            <div
                className={cn(
                    'bg-white dark:bg-gray-800 rounded-lg shadow-xl fixed max-h-[90vh] w-full max-w-6xl overflow-hidden z-[3] transition-all',
                    {
                        'opacity-0 invisible pointer-events-none': !popup.name,
                    },
                )}
                style={{
                    position: 'fixed',
                    top: '50%',
                    left: '50%',
                    transform: 'translate(-50%, -50%)',
                }}
            >
                <div className="container mx-auto px-4 py-6">
                    <div className="flex items-center justify-between mb-6">
                        <h1 className="text-2xl font-bold text-gray-800 dark:text-gray-100 border-l-4 border-blue-500 dark:border-blue-400 pl-3">
                            Delivery Performance Overview
                        </h1>
                        <button
                            onClick={closePopup}
                            className="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors"
                        >
                            <LiaTimesSolid className="text-xl" />
                        </button>
                    </div>

                    {loading ? (
                        <div className="flex justify-center items-center py-12">
                            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                        </div>
                    ) : error ? (
                        <div className="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div className="flex items-center gap-2 text-red-800 dark:text-red-400">
                                <i className="fa-solid fa-circle-exclamation"></i>
                                <span className="font-medium">Error</span>
                            </div>
                            <p className="text-sm text-red-700 dark:text-red-300 mt-2">{error}</p>
                        </div>
                    ) : (
                        <>
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                {/* Left Card - Service Quality */}
                                <div className="bg-white dark:bg-gray-700 rounded-lg p-6 shadow-sm border dark:border-gray-600">
                                    <h2 className="text-lg font-medium text-gray-800 dark:text-gray-100 mb-4">Delivery Service Quality</h2>
                                    <div className="flex justify-center mb-4">
                                        <div className="relative w-32 h-32">
                                            <div 
                                                className="absolute inset-0 rounded-full border-8 border-green-400"
                                                style={{
                                                    background: `conic-gradient(#4ade80 0deg ${stats.successRate * 3.6}deg, #e5e7eb ${stats.successRate * 3.6}deg 360deg)`
                                                }}
                                            ></div>
                                            <div className="absolute inset-2 bg-white dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                <span className="text-2xl font-bold text-gray-800 dark:text-gray-100">{Math.round(stats.successRate)}%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div className="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 px-3 py-1 rounded-md inline-block mb-4">
                                        {stats.successRate >= 95 ? 'Excellent' : stats.successRate >= 80 ? 'Good' : 'Needs Improvement'}
                                    </div>
                                    <p className="text-sm text-gray-600 dark:text-gray-300">
                                        {stats.successRate >= 95 
                                            ? 'Your service quality is so excellent that you deserve special praise for your service. Thank you for your service!'
                                            : 'You need to work more to improve your service quality.'}
                                    </p>
                                </div>
                                
                                {/* Right Card - Statistics */}
                                <div className="bg-white dark:bg-gray-700 rounded-lg p-6 shadow-sm border dark:border-gray-600">
                                    <div className="flex items-center mb-4">
                                        <i className="fas fa-check-circle text-gray-600 dark:text-gray-400 mr-2"></i>
                                        <span className="text-gray-700 dark:text-gray-200">Orders</span>
                                        <span className="ml-auto text-xl font-bold text-gray-800 dark:text-gray-100">{stats.totalOrders}</span>
                                    </div>
                                    <div className="flex items-center mb-4">
                                        <i className="fas fa-check-circle text-gray-600 dark:text-gray-400 mr-2"></i>
                                        <span className="text-gray-700 dark:text-gray-200">Deliveries</span>
                                        <span className="ml-auto text-xl font-bold text-gray-800 dark:text-gray-100">{stats.totalDeliveries}</span>
                                    </div>
                                    <div className="flex items-center">
                                        <i className="fas fa-times-circle text-gray-600 dark:text-gray-400 mr-2"></i>
                                        <span className="text-gray-700 dark:text-gray-200">Rejects</span>
                                        <span className="ml-auto text-xl font-bold text-gray-800 dark:text-gray-100">{stats.totalOffers}</span>
                                    </div>
                                </div>
                            </div>
                            
                            {/* Table */}
                            <div className="bg-white dark:bg-gray-700 rounded-lg shadow-sm overflow-hidden border dark:border-gray-600">
                                <div className="grid grid-cols-5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-medium py-3 px-4">
                                    <div>Service</div>
                                    <div>Orders</div>
                                    <div>Deliveries</div>
                                    <div className="flex items-center">
                                        <i className="fas fa-times-circle mr-2"></i>
                                        Rejects
                                    </div>
                                    <div>Reject Rate</div>
                                </div>
                                
                                {fraudData && Object.entries(fraudData).map(([courierName, data]) => (
                                    <div key={courierName} className="grid grid-cols-5 py-4 px-4 border-b dark:border-gray-600 last:border-b-0">
                                        <div className="flex items-center">
                                            {data.logo ? (
                                                <img src={data.logo} alt={courierName} className="w-6 h-6 mr-2" />
                                            ) : (
                                                <div className="w-6 h-6 mr-2 bg-gray-300 dark:bg-gray-600 rounded flex items-center justify-center text-xs font-bold text-gray-700 dark:text-gray-200">
                                                    {courierName.charAt(0).toUpperCase()}
                                                </div>
                                            )}
                                            <div className="flex flex-col">
                                                <span className="capitalize text-gray-700 dark:text-gray-200">{courierName}</span>
                                                {data.customer_rating && (
                                                    <span className="text-xs text-gray-500 dark:text-gray-400 italic">
                                                        {data.customer_rating.replace(/_/g, ' ')}
                                                    </span>
                                                )}
                                            </div>
                                        </div>
                                        <div className="text-center">
                                            {data.customer_rating ? (
                                                <div className="flex justify-center text-yellow-400">
                                                    {renderStars(parseInt(data.customer_rating) || 0)}
                                                </div>
                                            ) : (
                                                 <span className="text-gray-700 dark:text-gray-200">{data.orders || data.total || 0} Orders</span>
                                            )}
                                        </div>
                                        <div className="text-center">
                                            {data.message ? (
                                                <>
                                                    <div className="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 px-3 py-1 rounded-md inline-block mb-1">
                                                        Excellent
                                                    </div>
                                                    <div className="text-xs text-gray-500 dark:text-gray-400">Excellent Customer - Very High Success Rate</div>
                                                </>
                                            ) : (
                                                 <span className="text-gray-700 dark:text-gray-200">{data.deliveries || data.success || 0} Deliveries</span>
                                            )}
                                        </div>
                                        <div className="text-center">
                                             <span className="text-gray-700 dark:text-gray-200">{data.offers !== undefined ? `${data.offers} Rejects` : '0 Rejects'}</span>
                                        </div>
                                        <div className="text-right">
                                            {(() => {
                                                const deliveries = data.deliveries || data.success || 0;
                                                const rejects = data.offers || 0;
                                                const total = deliveries + rejects;
                                                const rejectRate = total > 0 ? (rejects / total) * 100 : 0;
                                                
                                                return data.offer_rate !== undefined ? (
                                                    <span className="text-green-600 dark:text-green-400 font-medium">{data.offer_rate}% Reject Rate</span>
                                                ) : (
                                                    <span className="text-green-600 dark:text-green-400 font-medium">{rejectRate.toFixed(1)}% Reject Rate</span>
                                                );
                                            })()}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </>
                    )}
                </div>
            </div>
        </>
    );
};

export default FraudCheckModal;
