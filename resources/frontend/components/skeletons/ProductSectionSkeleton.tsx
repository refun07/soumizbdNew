import { SwiperSlide } from 'swiper/react';
import ThemeSlider from '../slider/ThemeSlider';
import ProductCardSkeleton from './ProductCardSkeleton';

interface Props {
    withCategoryImage?: boolean;
}

const ProductSectionSkeleton = ({ withCategoryImage = false }: Props) => {
    const sliderDom = (
        <ThemeSlider
            slidesPerView={'auto'}
            spaceBetween={10}
            breakpoints={undefined}
        >
            {Array(7)
                .fill(0)
                .map((_, i) => (
                    <SwiperSlide
                        key={i}
                        className="max-w-[180px] md:max-w-[220px]"
                    >
                        <ProductCardSkeleton />
                    </SwiperSlide>
                ))}
        </ThemeSlider>
    );

    return (
        <section className="featured-category-section">
            <div className="container">
                {/* section title skeleton */}
                <div className="flex items-center gap-3 mb-8">
                    <div className="skeleton h-8 w-8"></div>
                    <div className="skeleton h-8 w-[200px]"></div>
                </div>

                {withCategoryImage ? (
                    <div className="max-sm:space-y-2.5 sm:grid sm:grid-cols-12 gap-2.5 mt-4 lg:mt-8">
                        <div className="skeleton sm:col-span-5 md:col-span-4 aspect-square h-auto"></div>

                        <div className="sm:col-span-7 md:col-span-8">
                            {sliderDom}
                        </div>
                    </div>
                ) : (
                    sliderDom
                )}
            </div>
        </section>
    );
};

export default ProductSectionSkeleton;
