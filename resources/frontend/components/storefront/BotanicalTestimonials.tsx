import { useRef, useState } from 'react';
import { LuArrowRight, LuQuote, LuStar } from 'react-icons/lu';
import { Swiper, SwiperSlide } from 'swiper/react';
import type { Swiper as SwiperInstance } from 'swiper';
import { A11y, Keyboard, Pagination } from 'swiper/modules';
import { IClientReview } from '../../store/features/api/homeApis/commonHomeTypes';
import { getHomeContent } from './homeContent';

export default function BotanicalTestimonials({
    reviews,
    loading,
    error,
    retry,
}: {
    reviews: IClientReview[];
    loading?: boolean;
    error?: boolean;
    retry?: () => void;
}) {
    const content = getHomeContent().reviews;
    const [showAll, setShowAll] = useState(false);
    const swiperRef = useRef<SwiperInstance | null>(null);
    const [bounds, setBounds] = useState({
        beginning: true,
        end: false,
        locked: false,
    });
    const updateBounds = (swiper: SwiperInstance) =>
        setBounds({
            beginning: swiper.isBeginning,
            end: swiper.isEnd,
            locked: !!swiper.isLocked,
        });
    if (!content.enabled) return null;
    const allEntries = reviews.filter((review) => review.name.trim() && review.text.trim());
    const entries = allEntries.slice(0, content.limit);
    const renderReview = (review: IClientReview) => {
        const rating = Number(review.rating);
        return (
        <figure className="bot-testimonial">
            {review.avatar ? (
                <img
                    src={review.avatar}
                    alt={review.name}
                    width="112"
                    height="112"
                    loading="lazy"
                />
            ) : (
                <div
                    className="bot-testimonial-initial"
                    aria-hidden="true"
                >
                    {Array.from(review.name)[0]}
                </div>
            )}
            <LuQuote
                className="bot-testimonial-quote"
                aria-hidden="true"
            />
            <blockquote>{review.text}</blockquote>
            <figcaption>
                <strong>{review.name}</strong>
                {review.designation && (
                    <span>
                        {review.designation}
                    </span>
                )}
            </figcaption>
            {rating >= 1 && rating <= 5 && (
                <div
                    className="bot-testimonial-stars"
                    role="img"
                    aria-label={`${rating} out of 5 stars`}
                >
                    {[1, 2, 3, 4, 5].map((star) => (
                        <LuStar
                            key={star}
                            className={
                                star <= rating
                                    ? 'is-filled'
                                    : ''
                            }
                            aria-hidden="true"
                        />
                    ))}
                </div>
            )}
        </figure>
        );
    };
    return (
        <section
            className="bot-container bot-testimonials-section"
            id="customer-reviews"
            aria-labelledby="customer-reviews-title"
        >
            <div className="bot-testimonials-panel">
                <header className="bot-testimonials-heading">
                    <p className="bot-eyebrow">{content.eyebrow}</p>
                    <h2 id="customer-reviews-title">
                        {content.title}
                        {content.accent && (
                            <>
                                {' '}
                                <span>{content.accent}</span>
                            </>
                        )}
                    </h2>
                </header>
                {error ? (
                    <div className="bot-testimonials-state" role="alert">
                        We couldn’t load customer reviews.{' '}
                        <button onClick={retry}>Try again</button>
                    </div>
                ) : loading ? (
                    <div className="bot-testimonials-state" role="status">
                        Loading customer reviews…
                    </div>
                ) : entries.length ? showAll ? (
                    <div className="bot-testimonials-grid" id="customer-review-items">
                        {allEntries.map((review, index) => (
                            <div key={`${review.name}-${index}`}>{renderReview(review)}</div>
                        ))}
                    </div>
                ) : (
                    <Swiper
                        id="customer-review-items"
                        className="bot-testimonials-slider"
                        modules={[A11y, Keyboard, Pagination]}
                        pagination={{ clickable: true }}
                        keyboard={{ enabled: true, onlyInViewport: true }}
                        a11y={{
                            containerMessage: 'Customer testimonials slider',
                        }}
                        slidesPerView={1}
                        spaceBetween={24}
                        breakpoints={{
                            700: { slidesPerView: 2 },
                            1100: { slidesPerView: 3, spaceBetween: 32 },
                        }}
                        watchOverflow
                        onSwiper={(swiper) => {
                            swiperRef.current = swiper;
                            updateBounds(swiper);
                        }}
                        onSlideChange={updateBounds}
                        onResize={updateBounds}
                        onBreakpoint={updateBounds}
                    >
                        {entries.map((review, index) => {
                            return (
                                <SwiperSlide key={`${review.name}-${index}`}>
                                    {renderReview(review)}
                                </SwiperSlide>
                            );
                        })}
                        <button
                            slot="container-end"
                            type="button"
                            className={`bot-testimonial-prev${
                                bounds.locked ? ' is-locked' : ''
                            }`}
                            aria-label="Previous customer reviews"
                            disabled={bounds.beginning || bounds.locked}
                            onClick={() => swiperRef.current?.slidePrev()}
                        >
                            <LuArrowRight aria-hidden="true" />
                        </button>
                        <button
                            slot="container-end"
                            type="button"
                            className={`bot-testimonial-next${
                                bounds.locked ? ' is-locked' : ''
                            }`}
                            aria-label="Next customer reviews"
                            disabled={bounds.end || bounds.locked}
                            onClick={() => swiperRef.current?.slideNext()}
                        >
                            <LuArrowRight aria-hidden="true" />
                        </button>
                    </Swiper>
                ) : (
                    <p className="bot-testimonials-state">
                        Customer stories are on their way.
                    </p>
                )}
                {!loading && !error && allEntries.length > 0 && (
                    <button className="bot-testimonials-link" type="button"
                        aria-expanded={showAll} aria-controls="customer-review-items"
                        onClick={() => setShowAll(!showAll)}>
                        {showAll ? 'Show less' : 'See all'}
                        <LuArrowRight aria-hidden="true" />
                    </button>
                )}
            </div>
        </section>
    );
}
