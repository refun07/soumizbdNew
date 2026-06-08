import toast from 'react-hot-toast';
import { ICartProduct, IProductShort, IProductVariation, VariationCombination } from '../types/product';
import { translate } from './translate';

interface ISelectedVariationCombinations {
    [key: number]: VariationCombination['values'][0] | null;
}

/**
 * Check if a product or any of its variations has stock available
 * Used for product card "Add to Cart" button disabled state
 * @param product IProductShort
 * @param isPreOrder boolean - if true, skip stock validation (allow ordering when out of stock)
 */
export const isProductOrVariationInStock = (
    product: IProductShort,
    isPreOrder: boolean = false,
): boolean => {
    // Backorder enabled: always allow ordering regardless of stock
    if (window.config.generalSettings.allowBackorder || isPreOrder) {
        return true;
    }

    return product.hasAvailableStock;
};

/**
 * Check if the selected variation is available in stock or not
 * if not, show error toast and return false
 * if yes, return true
 * @param selectedVariation IProductVariation
 * @param carts ICartProduct[]
 * @param isPreOrder boolean - if true, skip stock validation (allow ordering when out of stock)
 */
export const isProductAvailable = (
    selectedVariation: IProductVariation,
    carts: ICartProduct[],
    isPreOrder: boolean = false,
) => {
    // Backorder or pre-order enabled: always allow ordering regardless of stock
    if (window.config.generalSettings.allowBackorder || isPreOrder) {
        return true;
    }

    if (!selectedVariation.stocks.length) {
        toast.error(translate('Out of stock'));
        return false;
    }

    const totalStockQty = selectedVariation.stocks.reduce(
        (sum, stock) => sum + (stock.stockQty || 0),
        0,
    );

    if (totalStockQty < 1) {
        toast.error(translate('Out of stock'));
        return false;
    }

    const productCartQuantity = carts.find(
        (cartProduct) => cartProduct.variation.id === selectedVariation?.id,
    );

    const maximumAddableQuantity = totalStockQty - (productCartQuantity?.qty || 0);

    if (maximumAddableQuantity < 1) {
        toast.error(translate('Maximum quantity added to cart'));
        return false;
    }

    return true;
};

export const isVariationValueInStock = (
    candidateValue: VariationCombination['values'][0],
    currentSelections: ISelectedVariationCombinations,
    allVariations: IProductVariation[],
): boolean => {
    // Backorder enabled: always return true regardless of stock
    if (window.config.generalSettings.allowBackorder) {
        return true;
    }

    const potentialSelections = { ...currentSelections };
    const candidateVariationId = Object.keys(currentSelections).find(
        key => potentialSelections[parseInt(key)]?.matchVariationCode === candidateValue.matchVariationCode
    );
    if (!candidateVariationId) return true;
    potentialSelections[parseInt(candidateVariationId)] = candidateValue;
    
    const code = Object.values(potentialSelections)
        .filter(Boolean)
        .map(v => v!.matchVariationCode)
        .join('/') + '/';
    
    const matchingVariation = allVariations.find(v => v.code === code);
    if (!matchingVariation) return false;
    
    const totalStock = matchingVariation.stocks.reduce((sum, s) => sum + s.stockQty, 0);
    return totalStock > 0;
};
