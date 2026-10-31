import { useState } from 'react';
import {
    LuArrowUpRight,
    LuHeart,
    LuLoader2,
    LuShoppingCart,
    LuStar,
} from 'react-icons/lu';
import { Link } from 'react-router-dom';
import { useProductActions } from '../card/product-card';
import { IProductShort } from '../../types/product';
import { currencyFormatter } from '../../utils/numberFormatter';

export default function BotanicalProductCard({
    product: initial,
}: {
    product: IProductShort;
}) {
    const {
        product,
        loading,
        handleAddToCart,
        handleProductPreview,
        handleAddToWishList,
        handleRemoveFromWishList,
    } = useProductActions(initial);
    const [pending, setPending] = useState(false);
    const busy = pending || loading.addingToCart || loading.gettingProduct;
    const soldOut =
        product.stockQty < 1 && !window.config.generalSettings.allowBackorder;
    async function add() {
        setPending(true);
        try {
            await handleAddToCart();
        } catch {
            /* API middleware displays the request error. */
        } finally {
            setPending(false);
        }
    }
    return (
        <article className="bot-product" data-product-card="catalog">
            <div className="bot-product-image-wrap">
                {product.isPreOrder && product.preOrderData?.is_active && (
                    <span className="bot-product-label">Pre-order</span>
                )}
                <Link
                    to={`/product/${product.slug}`}
                    className="bot-product-image-link"
                    aria-label={`View ${product.name}`}
                >
                    <img
                        src={product.thumbnailImg || '/images/no-media.png'}
                        onError={(event) => {
                            event.currentTarget.onerror = null;
                            event.currentTarget.src = '/images/no-media.png';
                        }}
                        alt={product.name}
                        loading="lazy"
                        width="360"
                        height="360"
                    />
                </Link>
                <button type="button"
                    className={`bot-wishlist ${
                        product.inWishlist ? 'is-saved' : ''
                    }`}
                    aria-label={`${product.inWishlist ? 'Remove' : 'Save'} ${
                        product.name
                    } ${product.inWishlist ? 'from' : 'to'} wishlist`}
                    aria-pressed={product.inWishlist}
                    disabled={
                        loading.addingToWishlist || loading.deletingFromWishlist
                    }
                    onClick={
                        product.inWishlist
                            ? handleRemoveFromWishList
                            : handleAddToWishList
                    }
                >
                    <LuHeart />
                </button>
                <button type="button"
                    className="bot-product-preview bot-product-preview--mobile"
                    onClick={handleProductPreview}
                    aria-label={`Quick view ${product.name}`}
                >
                    <LuArrowUpRight />
                </button>
            </div>
            <div className="bot-product-content">
                <h3>
                    <Link to={`/product/${product.slug}`} title={product.name}>{product.name}</Link>
                </h3>
                <div className="bot-product-meta">
                    <strong>
                        {currencyFormatter(product.discountedBasePrice)}
                        {product.basePrice > product.discountedBasePrice && (
                            <del>{currencyFormatter(product.basePrice)}</del>
                        )}
                    </strong>
                    <span>
                        <LuStar aria-hidden="true" />{' '}
                        {product.rating?.total
                            ? `${Number(product.rating.average).toFixed(1)} (${
                                  product.rating.total
                              })`
                            : 'New'}
                    </span>
                </div>
                <div className="bot-product-actions">
                    <button type="button"
                        className={`bot-button bot-button--small${soldOut ? ' is-sold-out' : ''}`}
                        disabled={busy || soldOut}
                        aria-busy={busy || undefined}
                        onClick={add}
                    >
                        {busy ? (
                            <LuLoader2 className="bot-spin" />
                        ) : !soldOut ? (
                            <LuShoppingCart />
                        ) : null}
                        <span>{soldOut
                            ? 'Sold out'
                            : busy
                            ? 'Adding…'
                            : 'Add to cart'}</span>
                    </button>
                    <button type="button"
                        className="bot-product-preview"
                        onClick={handleProductPreview}
                        aria-label={`Quick view ${product.name}`}
                    >
                        <LuArrowUpRight />
                    </button>
                </div>
            </div>
        </article>
    );
}
