import { useRef, useState } from 'react';
import type { Swiper as SwiperInstance } from 'swiper';
import { Swiper, SwiperSlide } from 'swiper/react';
import { A11y, Keyboard, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/a11y';
import { IProductShort } from '../../types/product';
import BotanicalProductCard from './BotanicalProductCard';

export default function BotanicalProductSlider({
    products,
    label,
    className = '',
    featured = false,
    desktopSlides,
}: {
    products: IProductShort[];
    label: string;
    className?: string;
    featured?: boolean;
    desktopSlides?: number;
}) {
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
    return (
        <Swiper
            className={`bot-product-carousel ${className}`}
            modules={[A11y, Keyboard, Pagination]}
            onSwiper={(swiper) => {
                swiperRef.current = swiper;
                updateBounds(swiper);
            }}
            onSlideChange={updateBounds}
            onResize={updateBounds}
            onBreakpoint={updateBounds}
            pagination={{ clickable: true }}
            keyboard={{ enabled: true, onlyInViewport: true }}
            a11y={{
                containerMessage: label,
                prevSlideMessage: 'Previous products',
                nextSlideMessage: 'Next products',
            }}
            slidesPerView={2}
            spaceBetween={12}
            breakpoints={{
                480: { slidesPerView: 2, spaceBetween: 20 },
                900: { slidesPerView: 3, spaceBetween: 34 },
                1200: {
                    slidesPerView: desktopSlides ?? (featured ? 4 : 3),
                    spaceBetween: desktopSlides ? 16 : featured ? 20 : 34,
                },
            }}
            watchOverflow
        >
            {products.map((product) => (
                <SwiperSlide key={product.id}>
                    <BotanicalProductCard product={product} />
                </SwiperSlide>
            ))}
            <button
                slot="container-end"
                type="button"
                className={`swiper-button-prev${
                    bounds.beginning ? ' swiper-button-disabled' : ''
                }${bounds.locked ? ' swiper-button-lock' : ''}`}
                aria-label="Previous products"
                disabled={bounds.beginning || bounds.locked}
                onClick={() => swiperRef.current?.slidePrev()}
            />
            <button
                slot="container-end"
                type="button"
                className={`swiper-button-next${
                    bounds.end ? ' swiper-button-disabled' : ''
                }${bounds.locked ? ' swiper-button-lock' : ''}`}
                aria-label="Next products"
                disabled={bounds.end || bounds.locked}
                onClick={() => swiperRef.current?.slideNext()}
            />
        </Swiper>
    );
}
