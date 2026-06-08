import { ICouponShort } from '../types/checkout';
import { ICartProduct } from '../types/product';

interface Arguments {
    cartProducts: Record<string, ICartProduct[]>;
    selectedShops: Record<string, boolean>;
    coupons?: ICouponShort[];
}

/**
 * Get total, subTotal, tax, totalDiscount of selected shop products from cart
 * @param {Object} cartProducts
 * @param {Object} selectedShops
 * @param {Array} coupons
 * @returns {Object} total, subTotal, tax, totalDiscount
 * @example
 * const { total, subTotal, tax, totalDiscount } = getSelectedShopProducts({
 *    cartProducts,
 *    selectedShops,
 *    coupons,
 * });
 */
export const getSelectedShopProducts = ({
    cartProducts,
    selectedShops,
    coupons,
}: Arguments) => {
    let total = 0;
    let subTotal = 0;
    let tax = 0;
    let totalDiscount = 0;

    Object.entries(cartProducts).forEach(([shopName, carts]) => {
        if (!selectedShops[shopName]) return;

        let shopSubTotal = 0;
        let shopTax = 0;
        let shopTotal = 0;

        carts.forEach((cart) => {
            shopSubTotal += cart.qty * cart.variation.discountedBasePrice;
            shopTax += cart.qty * cart.variation.tax;
        });

        // check if coupon is applied
        //
        const coupon = coupons?.find(
            (coupon) => coupon.shopId === carts[0].shop.id,
        );

        subTotal += shopSubTotal;
        tax += shopTax;

        if (!coupon) {
            total += shopSubTotal + shopTax;
            return;
        }

        if (coupon?.discountType === 'amount') {
            shopTotal = shopSubTotal + shopTax - coupon.discount;
            totalDiscount += coupon.discount;
        } else {
            shopTotal = shopSubTotal + shopTax;

            let discount = shopTotal * (coupon.discount / 100);
            if (coupon.maxDiscountAmount) {
                discount = Math.min(discount, coupon.maxDiscountAmount);
            }
            shopTotal -= discount;
            totalDiscount += discount;
        }

        total += shopTotal;
    });

    return {
        total,
        subTotal,
        tax,
        totalDiscount,
    };
};
