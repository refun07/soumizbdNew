import { IClientReview } from '../../store/features/api/homeApis/commonHomeTypes';
import { cn } from '../../utils/cn';

type Props = {
    reviews?: IClientReview[];
    visible?: boolean | string | number;
};

const TestimonialsSection = ({ reviews = [], visible = true }: Props) => {
    const isVisible = visible === true || visible === 1 || visible === '1';

    if (!isVisible || reviews.length === 0) return null;

    return (
        <section className="container px-4 sm:px-6">
            <div className="rounded-lg bg-white px-5 py-12 shadow-[0_2px_16px_rgba(0,0,0,0.08)] md:px-10 lg:py-14">
                <div className="mb-10 text-center">
                    <p className="mb-2 text-sm font-semibold uppercase tracking-[1px] text-[#B3052A]">
                        Testimonials
                    </p>
                    <h2 className="text-3xl font-bold uppercase leading-tight text-[#151515] md:text-4xl">
                        Customer <span className="text-[#B3052A]">Review</span>
                    </h2>
                </div>

                <div className="grid gap-10 md:grid-cols-3 md:gap-8">
                    {reviews.map((review, index) => (
                        <article key={`${review.name}-${index}`} className="flex flex-col items-center text-center">
                            <div className="mb-6 h-24 w-24 overflow-hidden rounded-full bg-[#f2f2f2]">
                                {review.avatar ? (
                                    <img src={review.avatar} alt={review.name} className="h-full w-full object-cover" />
                                ) : (
                                    <div className="flex h-full w-full items-center justify-center text-2xl font-bold text-[#B3052A]">
                                        {review.name?.charAt(0)?.toUpperCase() || '?'}
                                    </div>
                                )}
                            </div>
                            <p className="min-h-[96px] max-w-sm whitespace-pre-line text-sm leading-6 text-[#777]">
                                {review.text}
                            </p>
                            <div className="mt-5 text-base font-bold text-[#151515]">
                                {review.name}
                                {review.designation ? (
                                    <span className="ml-2 font-semibold text-[#B3052A]">{review.designation}</span>
                                ) : null}
                            </div>
                            {review.rating ? (
                                <div className="mt-2 text-xs tracking-[3px] text-[#B3052A]" aria-label={`${review.rating} out of 5 stars`}>
                                    {'★'.repeat(Math.max(0, Math.min(5, Number(review.rating))))}
                                </div>
                            ) : null}
                        </article>
                    ))}
                </div>

                <div className="mt-10 flex justify-center gap-2" aria-hidden="true">
                    {reviews.map((_, index) => (
                        <span key={index} className={cn('h-2 w-2 rounded-full', index === 0 ? 'bg-black' : 'bg-[#B3052A]')} />
                    ))}
                </div>
            </div>
        </section>
    );
};

export default TestimonialsSection;
