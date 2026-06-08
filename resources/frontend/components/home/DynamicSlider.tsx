import React from 'react';
import { motion } from 'framer-motion';
import { SwiperSlide } from 'swiper/react';
import { Autoplay } from 'swiper/modules';
import ThemeSlider from '../slider/ThemeSlider';
import SectionTitle from '../titles/SectionTitle';
import { containerAnimation, itemAnimation } from '../../utils/animation';

interface DynamicSliderProps {
    config: {
        title?: string;
        subtitle?: string;
        images: Array<{
            url: string;
            alt: string;
            order: number;
        }>;
        settings: {
            slidesPerView: number;
            spaceBetween: number;
            autoplay: {
                delay: number;
                pauseOnMouseEnter: boolean;
            };
        };
    };
    theme: string;
}

const DynamicSlider: React.FC<DynamicSliderProps> = ({ config, theme }) => {
    const images = config.images?.sort((a, b) => a.order - b.order) || [];

    // Fallback for sections without title/subtitle fields
    const title = config.title || '';
    const subtitle = config.subtitle || '';

    if (images.length === 0) {
        return null;
    }

    return (
        <section>
            <div className="container">
                {/* Section Title */}
                {(title || subtitle) && (
                    <SectionTitle
                        animated
                        title={title}
                        subTitle={subtitle}
                        highlightFirstWord={true}
                    />
                )}

                {/* Slider */}
                <motion.div
                    variants={containerAnimation}
                    initial="initial"
                    whileInView="whileInView"
                    viewport={{ once: true }}
                >
                    <ThemeSlider
                        slidesPerView={config.settings?.slidesPerView || 1}
                        spaceBetween={config.settings?.spaceBetween || 15}
                        autoplay={config.settings?.autoplay || { delay: 2000 }}
                        modules={[Autoplay]}
                        breakpoints={{
                            568: { slidesPerView: Math.min(config.settings?.slidesPerView || 1, 2) },
                            768: { slidesPerView: Math.min(config.settings?.slidesPerView || 1, 3) },
                            992: { slidesPerView: config.settings?.slidesPerView || 1 },
                        }}
                    >
                        {images.map((image, index) => (
                            <SwiperSlide key={`${image.url}-${index}`}>
                                <motion.div variants={itemAnimation}>
                                    <img
                                        src={image.url}
                                        alt={image.alt || `Slide ${index + 1}`}
                                        className="w-full h-auto rounded-banner"
                                        style={{
                                            maxHeight: '400px',
                                            objectFit: 'cover'
                                        }}
                                    />
                                </motion.div>
                            </SwiperSlide>
                        ))}
                    </ThemeSlider>
                </motion.div>
            </div>
        </section>
    );
};

export default DynamicSlider;
