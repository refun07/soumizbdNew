import React, { useState } from 'react';
import { useSearchParams } from 'react-router-dom';
import toast from 'react-hot-toast';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import Image from '../../react/components/Image';
import { objectToFormData } from '../../react/utils/ObjectFormData';
import { translate } from '../../react/utils/translate';
import { IPosCartGroup, IPosProductVariation } from '../types';
import {
    getRemainingStockForVariation,
    getStockBadgeMeta,
} from '../utils/helpers';
import { addToPosCartGroup, updatePosCartItem } from '../utils/actions';

interface Props {
    posCartGroup?: IPosCartGroup;
    posProduct: IPosProductVariation;
    warehouseId?: number | string | null;
    stockOverride?: boolean;
    onSuccess?: (posCartGroup: IPosCartGroup) => void;
}

const ProductCard = ({
    posCartGroup,
    posProduct,
    warehouseId,
    stockOverride = false,
    onSuccess,
}: Props) => {
    const [searchParams] = useSearchParams();
    const [isLoading, setIsLoading] = useState(false);
    const remainingStock = getRemainingStockForVariation(
        posProduct,
        warehouseId,
        posCartGroup?.posCarts || [],
        posProduct.id,
    );
    const stockBadge = getStockBadgeMeta(Math.max(remainingStock, 0));
    const canAddToCart = stockOverride || remainingStock > 0;

    const handleAddToCart = async () => {
        if (!canAddToCart) {
            toast.error(translate('Stock not available for this item'));
            return;
        }

        setIsLoading(true);

        try {
            const productInCart = posCartGroup?.posCarts.find(
                (cart) => cart.productVariationId === posProduct.id,
            );

            if (productInCart) {
                const posCarts = await updatePosCartItem(
                    objectToFormData({
                        id: productInCart.id,
                        action: 'increase',
                        overrideStock: stockOverride ? 1 : 0,
                    }),
                );
                onSuccess?.({
                    posCartGroupId: posCartGroup!.posCartGroupId,
                    posCarts,
                });
            } else {
                const newPosCartGroup = await addToPosCartGroup(
                    objectToFormData({
                        firstCartProduct: posCartGroup?.posCartGroupId ? null : 1,
                        posCartGroupId: posCartGroup?.posCartGroupId || null,
                        productVariationId: posProduct.id,
                        warehouseId: searchParams.get('warehouseId') || null,
                        qty: 1,
                        overrideStock: stockOverride ? 1 : 0,
                    }),
                );
                onSuccess?.({
                    posCartGroupId:
                        posCartGroup?.posCartGroupId ||
                        newPosCartGroup.posCartGroupId,
                    posCarts: newPosCartGroup.posCarts,
                });
            }
        } catch (error) {
            const message =
                error instanceof Error
                    ? error.message
                    : translate('Failed to add product');
            toast.error(message);
        } finally {
            setIsLoading(false);
        }
    };

    return (
        <div
            className={`group relative flex h-full cursor-pointer flex-col overflow-hidden rounded-md border border-border bg-background transition-colors ${
                canAddToCart ? 'hover:border-primary/40' : 'cursor-not-allowed'
            }`}
            onClick={() => {
                void handleAddToCart();
            }}
            onKeyDown={(event) => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    void handleAddToCart();
                }
            }}
            role="button"
            tabIndex={0}
            aria-disabled={isLoading || !canAddToCart}
        >
            <div
                className={`pointer-events-none absolute inset-0 z-[2] hidden items-center justify-center overflow-hidden transition-all duration-300 lg:flex ${
                    canAddToCart
                        ? 'bg-primary/90 opacity-0 lg:group-hover:opacity-100'
                        : 'bg-red-600/90 opacity-0 lg:group-hover:opacity-100'
                }`}
            >
                <div
                    className={`flex flex-col items-center gap-4 text-white transition-all duration-300 ${
                        canAddToCart
                            ? 'translate-y-3 scale-95 lg:group-hover:translate-y-0 lg:group-hover:scale-100'
                            : 'translate-y-3 scale-95 lg:group-hover:translate-y-0 lg:group-hover:scale-100'
                    }`}
                >
                    <div className="relative h-16 w-16">
                        <span className="absolute left-1/2 top-0 h-full w-1 -translate-x-1/2 rounded-full bg-white" />
                        <span className="absolute left-0 top-1/2 h-1 w-full -translate-y-1/2 rounded-full bg-white" />
                    </div>
                    <span className="text-base font-semibold">
                        {canAddToCart
                            ? translate('Add Product')
                            : translate('Out of stock')}
                    </span>
                </div>
            </div>

            <div className="absolute left-4 top-4 flex gap-1 flex-wrap items-start z-[3]">
                {posProduct.combinations.map((combination) => (
                    <span
                        key={combination.id}
                        className="bg-theme-yellow text-black px-2 py-1 rounded text-xs"
                    >
                        {combination.name}
                    </span>
                ))}
            </div>

            <Image
                className="aspect-[4/3] bg-background-primary-light"
                src={posProduct.image || posProduct.product.thumbnailImg}
                alt="Product Image"
            />

            <div className="grow px-3 pb-2.5 pt-2.5 sm:px-4 sm:pb-3 sm:pt-3">
                <h3 className="font-public-sans text-sm font-medium leading-5 whitespace-normal break-words">
                    {posProduct.product.name}
                </h3>
                <div className="mt-2 flex items-center justify-between gap-3">
                    <p className="text-secondary text-sm font-bold sm:text-[15px]">
                        {currencyFormatter(posProduct.discountedBasePrice)}
                    </p>
                    <span
                        className={`shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold ${stockBadge.className}`}
                    >
                        {stockOverride && remainingStock <= 0
                            ? translate('Override')
                            : stockBadge.label}
                    </span>
                </div>
                <div className="mt-1.5 flex justify-end">
                    {isLoading ? (
                        <span className="text-xs font-semibold text-primary">
                            {translate('Adding...')}
                        </span>
                    ) : null}
                </div>
            </div>
        </div>
    );
};

export default ProductCard;
