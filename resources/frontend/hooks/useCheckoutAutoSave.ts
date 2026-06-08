import { debounce } from 'lodash';
import { useEffect, useMemo, useRef } from 'react';
import { useSaveCheckoutSessionMutation } from '../store/features/checkout/checkoutApi';
import {
    setCheckoutData,
    useCheckoutData,
} from '../store/features/checkout/checkoutSlice';
import { useAppDispatch } from '../store/store';
import { ICartProduct } from '../types/product';

interface UseCheckoutAutoSaveProps {
    userId?: number;
    guestUserId?: string;
    carts: ICartProduct[];
    enabled?: boolean;
}

/**
 * Hook to automatically save checkout data to the database
 * as the user types (with 1-second debounce).
 * This enables abandoned cart recovery and analytics.
 */
export const useCheckoutAutoSave = ({
    userId,
    guestUserId,
    carts,
    enabled = true,
}: UseCheckoutAutoSaveProps) => {
    const dispatch = useAppDispatch();
    const [saveSession] = useSaveCheckoutSessionMutation();
    
    const {
        customerDetails,
        shippingAddress,
        billingAddress,
        paymentMethod,
        coupons,
        selectedShops,
        shippingCharge,
    } = useCheckoutData();

    // Refs to store previous values for comparison
    const prevCustomerDetails = useRef(customerDetails);
    const prevShippingAddress = useRef(shippingAddress);
    const prevPaymentMethod = useRef(paymentMethod);
    const prevCarts = useRef(carts);
    const prevShippingCharge = useRef(shippingCharge);

    // Calculate cart total
    const cartTotal = useMemo(() => {
        return carts.reduce((total, cart) => {
            const price = cart.price ?? cart.product?.basePrice ?? 0;
            return total + price * cart.qty;
        }, 0);
    }, [carts]);

    // Determine current step based on available data
    const currentStep = useMemo(() => {
        if (paymentMethod) {
            return 'payment';
        }
        if (shippingAddress?.address && shippingAddress?.cityId) {
            return 'shipping';
        }
        return 'details';
    }, [paymentMethod, shippingAddress]);

    // Create debounced save function
    const debouncedSave = useMemo(
        () =>
            debounce(
                async (sessionData: {
                    userId?: number;
                    guestUserId?: string;
                    customerDetails: any;
                    shippingAddress: any;
                    paymentMethod?: string;
                    currentStep: string;
                    cartData: any[];
                    cartTotal: number;
                    shippingCharge: number;
                }) => {
                    try {
                        // Only save if we have a user identifier
                        if (!sessionData.userId && !sessionData.guestUserId) {
                            return;
                        }

                        // Only save if we have at least some customer data or cart data
                        const hasCustomerData =
                            sessionData.customerDetails?.name ||
                            sessionData.customerDetails?.email ||
                            sessionData.customerDetails?.phone;

                        const hasCartData =
                            sessionData.cartData &&
                            sessionData.cartData.length > 0;

                        if (!hasCustomerData && !hasCartData) {
                            return;
                        }

                        await saveSession(sessionData).unwrap();

                        // Optional: You could dispatch an action to update UI
                        // indicating that progress has been saved
                    } catch (error) {
                        // Silently fail - we don't want to interrupt the user
                        // if the auto-save fails
                        console.debug('Auto-save failed:', error);
                    }
                },
                1000 // 1 second debounce
            ),
        [saveSession]
    );

    // Watch for changes and trigger save
    useEffect(() => {
        if (!enabled) {
            return;
        }

        // Check if any meaningful data has changed
        const customerChanged =
            JSON.stringify(prevCustomerDetails.current) !==
            JSON.stringify(customerDetails);
        const addressChanged =
            JSON.stringify(prevShippingAddress.current) !==
            JSON.stringify(shippingAddress);
        const paymentChanged = prevPaymentMethod.current !== paymentMethod;
        const cartsChanged =
            JSON.stringify(prevCarts.current) !== JSON.stringify(carts);
        const shippingChargeChanged =
            prevShippingCharge.current !== shippingCharge;

        const hasChanges =
            customerChanged ||
            addressChanged ||
            paymentChanged ||
            cartsChanged ||
            shippingChargeChanged;

        if (hasChanges) {
            // Update refs
            prevCustomerDetails.current = customerDetails;
            prevShippingAddress.current = shippingAddress;
            prevPaymentMethod.current = paymentMethod;
            prevCarts.current = carts;
            prevShippingCharge.current = shippingCharge;

            // Prepare cart data for storage
            const cartData = carts.map((cart) => ({
                id: cart.id,
                product: {
                    id: cart.product?.id,
                    name: cart.product?.name,
                    price: cart.price ?? cart.product?.basePrice,
                    image: cart.product?.image,
                    slug: cart.product?.slug,
                    thumbnailImg: cart.product?.thumbnailImg,
                },
                variation: {
                    id: cart.variation?.id,
                    basePrice: cart.variation?.basePrice,
                    discountedBasePrice: cart.variation?.discountedBasePrice,
                    tax: cart.variation?.tax,
                    image: cart.variation?.image,
                },
                shop: {
                    id: cart.shop?.id,
                    name: cart.shop?.name,
                    slug: cart.shop?.slug,
                },
                qty: cart.qty,
                price: cart.price,
            }));

            // Trigger debounced save
            debouncedSave({
                userId,
                guestUserId,
                customerDetails,
                shippingAddress,
                paymentMethod,
                currentStep,
                cartData,
                cartTotal,
                shippingCharge,
            });
        }

        // Cleanup debounce on unmount - cancel pending save to avoid API call
        return () => {
            debouncedSave.cancel();
        };
    }, [
        enabled,
        userId,
        guestUserId,
        customerDetails,
        shippingAddress,
        paymentMethod,
        currentStep,
        carts,
        cartTotal,
        shippingCharge,
        debouncedSave,
    ]);

    return {
        currentStep,
        cartTotal,
    };
};

export default useCheckoutAutoSave;
