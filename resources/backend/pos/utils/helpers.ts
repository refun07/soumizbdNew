export const normalizeAssetPath = (path: string): string => {
    if (!path) return path;
    if (path.startsWith('https://') || path.startsWith('http://')) {
        return path;
    }
    if (!path.startsWith('/')) {
        return '/' + path;
    }
    return path;
};

interface StockLike {
    warehouseId: number;
    stockQty: number;
}

interface ProductLike {
    id: number;
    availableStock?: number;
    stocks?: StockLike[];
}

interface CartLike {
    productVariationId: number;
    qty: number;
}

export const getStockForWarehouse = (
    product: ProductLike,
    warehouseId?: number | string | null,
) => {
    if (!warehouseId) {
        return product.availableStock ?? 0;
    }

    const matchedStock = product.stocks?.find(
        (stock) => Number(stock.warehouseId) === Number(warehouseId),
    );

    return matchedStock?.stockQty ?? product.availableStock ?? 0;
};

export const getCartQtyForVariation = (
    carts: CartLike[] = [],
    productVariationId: number,
) => {
    return carts
        .filter((cart) => cart.productVariationId === productVariationId)
        .reduce((total, cart) => total + cart.qty, 0);
};

export const getRemainingStockForVariation = (
    product: ProductLike,
    warehouseId: number | string | null | undefined,
    carts: CartLike[] = [],
    productVariationId: number,
) => {
    return (
        getStockForWarehouse(product, warehouseId) -
        getCartQtyForVariation(carts, productVariationId)
    );
};

export const getStockBadgeMeta = (stockQty: number) => {
    if (stockQty <= 0) {
        return {
            label: 'Out of stock',
            className: 'bg-red-100 text-red-700 border border-red-200',
        };
    }

    if (stockQty <= 5) {
        return {
            label: `Low stock: ${stockQty}`,
            className: 'bg-amber-100 text-amber-800 border border-amber-200',
        };
    }

    return {
        label: `In stock: ${stockQty}`,
        className: 'bg-emerald-100 text-emerald-700 border border-emerald-200',
    };
};
