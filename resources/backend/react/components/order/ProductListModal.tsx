import React, { useEffect, useState, useRef } from 'react';
import { useAppContext } from '../../Context';
import PopupWrapper from '../popup/PopupWrapper';
import axios from 'axios';
import { LiaTimesSolid } from 'react-icons/lia';
import { cn } from '../../utils/cn';

interface OrderItem {
    id: number;
    product_name: string;
    variant: string | null;
    product_image: string | null;
    quantity: number;
    unit_price: number;
    total_price: number;
}

const ProductListModal = () => {
    const { popup, closePopup } = useAppContext();
    const [cachedOrderItems, setCachedOrderItems] = useState<Record<number, OrderItem[]>>({});
    const [loading, setLoading] = useState(false);
    const modalRef = useRef<HTMLDivElement>(null);

    const orderId = popup.props?.orderId;
    const position = popup.props?.position;

    // Get current order items (either from cache or empty array)
    const currentOrderItems = orderId ? (cachedOrderItems[orderId] || []) : [];

    useEffect(() => {
        const fetchOrderItems = async () => {
            if (!orderId) return;

            // Check if we already have data for this order
            if (cachedOrderItems[orderId]) {
                return; // Data already cached, no need to fetch
            }

            setLoading(true);
            try {
                const response = await axios.get(`/admin/api/orders/${orderId}/items`);
                const items = response.data.result;

                // Cache the items for this order ID
                setCachedOrderItems(prev => ({
                    ...prev,
                    [orderId]: items
                }));
            } catch (error) {
                console.error('Error fetching order items:', error);
            } finally {
                setLoading(false);
            }
        };

        if (popup.name === 'product-list' && orderId) {
            fetchOrderItems();
        }
    }, [popup.name, orderId, cachedOrderItems]);

    if (popup.name !== 'product-list') return null;

    const getModalStyle = () => {
        if (!position) {
            return { right: '20px', top: '50%', transform: 'translateY(-50%)' };
        }

        const { x, y } = position;
        const modalWidth = 320; // w-80 = 320px
        const modalMaxHeight = 400;
        const padding = 20;

        // Calculate estimated modal height based on content
        const baseModalHeight = 100; // Header + padding + min content
        const itemHeight = 60; // Height per item
        const itemsCount = currentOrderItems.length || 1;
        const estimatedHeight = Math.min(
            baseModalHeight + (itemsCount * itemHeight),
            modalMaxHeight
        );

        // Check if modal would overflow bottom of screen
        const adjustedY = () => {
            // Position after centering (y is the center point)
            const topPosition = y - (estimatedHeight / 2);
            const bottomPosition = y + (estimatedHeight / 2);

            // If it would overflow bottom, move it up
            if (bottomPosition > window.innerHeight - padding) {
                return window.innerHeight - padding - estimatedHeight;
            }
            // If it would overflow top, move it down
            if (topPosition < padding) {
                return padding;
            }
            // Otherwise use the centered position
            return y - (estimatedHeight / 2);
        };

        // Check if modal would overflow right side of screen
        const adjustedX = () => {
            const rightPosition = x + 20 + modalWidth;
            if (rightPosition > window.innerWidth - padding) {
                // Position on the left side instead
                return x - modalWidth - 20;
            }
            return x + 20;
        };

        return {
            left: adjustedX(),
            top: adjustedY(),
        };
    };

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
                ref={modalRef}
                className={cn(
                    'bg-white rounded-md shadow-lg fixed max-h-[400px] min-w-[300px] w-80 overflow-y-auto z-[3]',
                    {
                        'opacity-0 invisible pointer-events-none': !popup.name,
                    },
                )}
                style={getModalStyle()}
            >
                <button
                    onClick={closePopup}
                    className="absolute right-3 top-3 text-lg text-gray-400 hover:text-gray-600"
                >
                    <LiaTimesSolid />
                </button>

                <div className="p-4">
                    <h3 className="text-lg font-semibold mb-4">
                        Order Items
                    </h3>

                    {loading ? (
                        <div className="flex justify-center items-center py-8">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                        </div>
                    ) : (
                        <div className="space-y-0">
                            {currentOrderItems.map((item, index) => (
                                <React.Fragment key={item.id}>
                                    <div className="flex items-center gap-4 py-3">
                                        <div className="flex-shrink-0">
                                            {item.product_image ? (
                                                <img
                                                    src={item.product_image}
                                                    alt={item.product_name}
                                                    className="w-12 h-12 object-cover border border-gray-200 rounded"
                                                />
                                            ) : (
                                                <div className="w-12 h-12 bg-gray-100 border border-gray-200 rounded flex items-center justify-center">
                                                    <span className="text-gray-400 text-xs">No img</span>
                                                </div>
                                            )}
                                        </div>
                                        <div className="flex-1 min-w-0">
                                            <h4 className="text-sm font-medium text-gray-900 truncate">
                                                {item.product_name}
                                            </h4>
                                            {item.variant && (
                                                <span className="inline-block mt-1 px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded">
                                                    {item.variant}
                                                </span>
                                            )}
                                            <div className="mt-1 text-xs text-gray-500">
                                                Qty: {item.quantity} × ${item.unit_price.toFixed(2)}
                                            </div>
                                        </div>
                                    </div>
                                    {index < currentOrderItems.length - 1 && (
                                        <hr className="border-gray-200" />
                                    )}
                                </React.Fragment>
                            ))}
                            {currentOrderItems.length === 0 && (
                                <div className="text-center py-8 text-gray-500">
                                    No items found for this order.
                                </div>
                            )}
                        </div>
                    )}
                </div>
            </div>
        </>
    );
};

export default ProductListModal;
