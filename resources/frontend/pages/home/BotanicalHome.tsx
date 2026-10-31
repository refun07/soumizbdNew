import { ReactNode, useEffect } from 'react';
import '../../components/storefront/homepage-refinement.css';
import { LuArrowRight, LuCreditCard, LuLeaf, LuTruck } from 'react-icons/lu';
import { Link, useLocation } from 'react-router-dom';
import DynamicSectionRenderer from '../../components/home/DynamicSectionRenderer';
import { getHomeContent } from '../../components/storefront/homeContent';
import BotanicalConcerns from '../../components/storefront/BotanicalConcerns';
import BotanicalCategoryGrid from '../../components/storefront/BotanicalCategoryGrid';
import BotanicalTestimonials from '../../components/storefront/BotanicalTestimonials';
import BotanicalFlashSale from '../../components/storefront/BotanicalFlashSale';
import BotanicalProductCard from '../../components/storefront/BotanicalProductCard';
import {
    useGetBestSellerProductsQuery,
    useGetNewArrivalsQuery,
} from '../../store/features/api/homeApis/commonHomeApi';
import {
    useGetBotanicalCategoriesQuery,
    useGetBotanicalReviewsQuery,
} from '../../store/features/api/homeApis/botanicalApi';
import { IClientReview } from '../../store/features/api/homeApis/commonHomeTypes';
import {
    IGetSingleImageBanner,
    IMainBannerItem,
} from '../../store/features/api/homeApis/theme-one/types';
import { ICategoryShort } from '../../types';
import { IProductShort } from '../../types/product';

function SectionHeading({
    eyebrow,
    title,
    href = '/product',
    linkLabel = 'See all',
}: {
    eyebrow: string;
    title: string;
    href?: string;
    linkLabel?: string;
}) {
    return (
        <div className="bot-section-heading">
            <div>
                <p className="bot-eyebrow">{eyebrow}</p>
                <h2>{title}</h2>
            </div>

            <Link to={href}>
                {linkLabel} <LuArrowRight aria-hidden="true" />
            </Link>
        </div>
    );
}

function CatalogState({
    loading,
    error,
    children,
    retry,
}: {
    loading?: boolean;
    error?: boolean;
    children: ReactNode;
    retry?: () => void;
}) {
    if (loading)
        return (
            <div
                className="bot-loading-grid"
                role="status"
                aria-label="Loading products"
            >
                {[0, 1, 2].map((i) => (
                    <div className="bot-skeleton" key={i} />
                ))}
                <span className="bot-sr-only">Loading…</span>
            </div>
        );
    if (error)
        return (
            <div className="bot-catalog-state">
                <p>We couldn’t load this collection.</p>
                <button className="bot-text-button" onClick={retry}>
                    Try again <LuArrowRight />
                </button>
            </div>
        );
    return <>{children}</>;
}

export interface BotanicalHomeProps {
    banner?: IMainBannerItem;
    heroEnabled?: boolean;
    categories?: ICategoryShort[];
    bestSellers?: IProductShort[];
    arrivals?: IProductShort[];
    reviews?: IClientReview[];
    promotion?: IGetSingleImageBanner;
    categoriesEnabled?: boolean;
    promotionEnabled?: boolean;
    reviewsEnabled?: boolean;
    bestLoading?: boolean;
    arrivalsLoading?: boolean;
    bestError?: boolean;
    arrivalsError?: boolean;
    retryBest?: () => void;
    retryArrivals?: () => void;
    reviewsLoading?: boolean;
    reviewsError?: boolean;
    retryReviews?: () => void;
}

export function BotanicalHomeView({
    heroEnabled = true,
    categories = [],
    bestSellers = [],
    arrivals = [],
    reviews = [],
    categoriesEnabled = true,
    promotionEnabled = true,
    reviewsEnabled = true,
    bestLoading,
    arrivalsLoading,
    bestError,
    arrivalsError,
    retryBest,
    retryArrivals,
    reviewsLoading,
    reviewsError,
    retryReviews,
}: BotanicalHomeProps) {
    const location = useLocation();
    useEffect(() => {
        if (!location.hash) return;
        const id = decodeURIComponent(location.hash.slice(1));
        const frame = requestAnimationFrame(() =>
            document.getElementById(id)?.scrollIntoView({ block: 'start' }),
        );
        return () => cancelAnimationFrame(frame);
    }, [location.hash, reviews.length]);
    const content = getHomeContent();
    const selectedIds = content.categories.selection.map(Number);
    const featuredCategories = (selectedIds.length
        ? selectedIds.map((id) => categories.find((category) => category.id === id)).filter((category): category is ICategoryShort => Boolean(category))
        : categories.filter((category) => !category.parent_category)
    ).slice(0, content.categories.limit);
    const heroImage = content.hero.image;

    return (
        <div className="bot-home bot-home--refined">
            <BotanicalConcerns />
            {heroEnabled && content.hero.enabled && (
                <section className="bot-hero">
                    <div className="bot-container bot-hero-inner">
                        <div className="bot-hero-copy">
                            <p className="bot-eyebrow bot-hero-kicker">
                                {content.hero.kicker}
                            </p>
                            <h1>{content.hero.title}</h1>
                            <p className="bot-script">{content.hero.script}</p>
                            <p className="bot-hero-description">
                                {content.hero.description}
                            </p>
                            <div className="bot-hero-buttons">
                                <Link
                                    className="bot-button"
                                    to={content.hero.link}
                                >
                                    {content.hero.button_label} <LuArrowRight />
                                </Link>
                                <Link
                                    className="bot-underlined-link"
                                    to={content.hero.secondary_link}
                                >
                                    {content.hero.secondary_label}
                                </Link>
                            </div>
                        </div>
                        <div className="bot-hero-art">
                            <div
                                className="bot-hero-orbit"
                                aria-hidden="true"
                            />
                            <img
                                className="bot-hero-photo"
                                src={heroImage}
                                alt={content.hero.image_alt}
                                width="1536"
                                height="1024"
                                loading="eager"
                            />
                            <span className="bot-sparkle" aria-hidden="true">
                                ✦
                            </span>
                            <div className="bot-floating-label bot-floating-label--delivery">
                                <LuTruck />
                                <span>{content.hero.delivery_label}</span>
                            </div>
                            <div className="bot-floating-label bot-floating-label--payment">
                                <LuCreditCard />
                                <span>{content.hero.payment_label}</span>
                            </div>
                            <div className="bot-hero-stamp">
                                <LuLeaf />
                                <span>
                                    {content.hero.stamp_line_one}
                                    <br />
                                    {content.hero.stamp_line_two}
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
            )}
            {categoriesEnabled && content.categories.enabled && (
                <section
                    className="bot-container bot-category-section"
                    id="categories"
                >
                    <SectionHeading
                        eyebrow={content.categories.eyebrow}
                        title={content.categories.title}
                        href={content.categories.link}
                        linkLabel={content.categories.link_label}
                    />
                    <BotanicalCategoryGrid categories={featuredCategories} />
                    {!categories.length && (
                        <p className="bot-catalog-state">
                            Fresh discoveries are on their way.{' '}
                            <Link to="/product">
                                Explore all products <LuArrowRight />
                            </Link>
                        </p>
                    )}
                </section>
            )}
            <BotanicalFlashSale />
            {content.best.enabled && (
                <section className="bot-bestsellers" id="best-sellers">
                    <div className="bot-container">
                        <div className="bot-bestsellers-heading">
                            <div>
                                <p className="bot-eyebrow">
                                    {content.best.eyebrow}
                                </p>
                                <h2>{content.best.title}</h2>
                                <p className="bot-bestsellers-description">
                                    {content.best.description}
                                </p>
                            </div>
                            <Link to={content.best.link}>
                                {content.best.link_label}{' '}
                                <LuArrowRight aria-hidden="true" />
                            </Link>
                        </div>
                        <CatalogState
                            loading={bestLoading}
                            error={bestError}
                            retry={retryBest}
                        >
                            {bestSellers.length ? (
                                <div className="bot-home-product-grid bot-home-product-grid--featured" aria-label="Bestselling products">
                                    {bestSellers.map(product => <BotanicalProductCard key={product.id} product={product} />)}
                                </div>
                            ) : (
                                <p className="bot-catalog-state">
                                    Your next favourite is coming soon.{' '}
                                    <Link to="/product">
                                        Explore the collection <LuArrowRight />
                                    </Link>
                                </p>
                            )}
                        </CatalogState>
                    </div>
                </section>
            )}
            {promotionEnabled &&
                content.promotions.enabled &&
                (content.promo_left.enabled || content.promo_right.enabled) && (
                    <section
                        className="bot-container bot-promotion-section bot-promo-banners"
                        aria-label="Discover your daily care"
                        id="summer-combos"
                    >
                        {content.promo_left.enabled && (
                            <Link
                                className="bot-ritual-banner"
                                to={content.promo_left.link}
                            >
                                <div className="bot-ritual-copy">
                                    <span className="bot-ritual-kicker">
                                        <LuLeaf aria-hidden="true" />{' '}
                                        {content.promo_left.kicker}
                                    </span>
                                    <h2>
                                        {content.promo_left.title}
                                        <br />
                                        <em>{content.promo_left.accent}</em>
                                    </h2>
                                    <p>{content.promo_left.description}</p>
                                    <span className="bot-ritual-cta">
                                        {content.promo_left.button_label}{' '}
                                        <LuArrowRight aria-hidden="true" />
                                    </span>
                                </div>
                                <div className="bot-ritual-image">
                                    <img
                                        src={content.promo_left.image}
                                        alt={content.promo_left.image_alt}
                                        width="1536"
                                        height="1024"
                                        loading="lazy"
                                    />
                                </div>
                            </Link>
                        )}
                        {content.promo_right.enabled && (
                            <Link
                                className="bot-combo-banner"
                                to={content.promo_right.link}
                            >
                                <LuLeaf
                                    className="bot-combo-leaf"
                                    aria-hidden="true"
                                />
                                <div className="bot-ritual-copy">
                                    <span className="bot-ritual-kicker">
                                        {content.promo_right.kicker}
                                    </span>
                                    <h2>
                                        {content.promo_right.title}
                                        <br />
                                        <em>{content.promo_right.accent}</em>
                                    </h2>
                                    <p>{content.promo_right.description}</p>
                                    <span className="bot-ritual-cta">
                                        {content.promo_right.button_label}{' '}
                                        <LuArrowRight aria-hidden="true" />
                                    </span>
                                </div>
                                <span className="bot-combo-note">
                                    {content.promo_right.note}
                                </span>
                            </Link>
                        )}
                    </section>
                )}
            {content.arrivals.enabled && (
                <section
                    className="bot-container bot-arrivals"
                    id="new-arrivals"
                >
                    <SectionHeading
                        eyebrow={content.arrivals.eyebrow}
                        title={content.arrivals.title}
                        href={content.arrivals.link}
                        linkLabel={content.arrivals.link_label}
                    />
                    <div
                        className={`bot-arrivals-layout ${
                            !content.offer.enabled
                                ? 'bot-arrivals-layout--no-banner'
                                : ''
                        }`}
                    >
                        {content.offer.enabled && (
                            <Link
                                className="bot-arrivals-banner"
                                to={content.offer.link}
                            >
                                <img
                                    src={content.offer.image}
                                    alt={content.offer.image_alt}
                                    width="440"
                                    height="760"
                                    loading="lazy"
                                />
                                <div>
                                    <span className="bot-eyebrow">
                                        {content.offer.kicker}
                                    </span>
                                    <h3>{content.offer.title}</h3>
                                    <p>{content.offer.description}</p>
                                    <span className="bot-button">
                                        {content.offer.button_label}{' '}
                                        <LuArrowRight />
                                    </span>
                                </div>
                            </Link>
                        )}
                        <div>
                            <CatalogState
                                loading={arrivalsLoading}
                                error={arrivalsError}
                                retry={retryArrivals}
                            >
                                {arrivals.length ? (
                                    <div className="bot-home-product-grid" aria-label={content.arrivals.title}>
                                        {arrivals.map(product => <BotanicalProductCard key={product.id} product={product} />)}
                                    </div>
                                ) : (
                                    <p className="bot-catalog-state">
                                        Products are on their way.{' '}
                                        <Link to={content.arrivals.link}>
                                            Browse our products <LuArrowRight />
                                        </Link>
                                    </p>
                                )}
                            </CatalogState>
                        </div>
                    </div>
                </section>
            )}
            {reviewsEnabled && (
                <BotanicalTestimonials
                    reviews={reviews}
                    loading={reviewsLoading}
                    error={reviewsError}
                    retry={retryReviews}
                />
            )}
            {(window as any).config?.dynamicSections?.length > 0 && (
                <DynamicSectionRenderer
                    sections={(window as any).config.dynamicSections}
                    theme="theme-one"
                />
            )}
        </div>
    );
}

export default function BotanicalHome() {
    const categories = useGetBotanicalCategoriesQuery();
    const reviews = useGetBotanicalReviewsQuery();
    const best = useGetBestSellerProductsQuery();
    const arrivals = useGetNewArrivalsQuery();
    return (
        <BotanicalHomeView
            categories={categories.data?.categories}
            reviews={reviews.data?.reviews}
            reviewsLoading={reviews.isLoading}
            reviewsError={reviews.isError}
            retryReviews={reviews.refetch}
            reviewsEnabled={getHomeContent().reviews.enabled}
            bestSellers={best.data}
            arrivals={arrivals.data}
            bestLoading={best.isLoading}
            arrivalsLoading={arrivals.isLoading}
            bestError={best.isError}
            arrivalsError={arrivals.isError}
            retryBest={best.refetch}
            retryArrivals={arrivals.refetch}
        />
    );
}
