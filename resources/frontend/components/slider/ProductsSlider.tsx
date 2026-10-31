import { SwiperProps } from 'swiper/react';
import ThemeSlider from './ThemeSlider';

type BreakPoints = 320 | 400 | 640 | 768 | 1024 | 1280;
type BreakPointsObj = {
    [key in BreakPoints]?: {
        slidesPerView?: number | 'auto';
        spaceBetween?: number;
    };
};

interface Props extends Omit<SwiperProps, 'breakpoints'> {
    breakpoints?: BreakPointsObj;
    withDefaultBreakpoints?: boolean;
}

const ProductsSlider = ({
    slidesPerView,
    spaceBetween,
    breakpoints,
    withDefaultBreakpoints = true,
    children,
    className,
    ...rest
}: Props) => {
    const defaultBreakpoints = {
        320: {
            slidesPerView: 2,
            spaceBetween: 12,
        },
        768: {
            slidesPerView: 3,
            spaceBetween: 16,
        },
        1280: {
            slidesPerView: 4,
            spaceBetween: 20,
        },
    };

    return (
        <ThemeSlider
            containerClass={className}
            slidesPerView={slidesPerView || 2}
            spaceBetween={spaceBetween || 20}
            breakpoints={
                withDefaultBreakpoints
                    ? {
                          ...defaultBreakpoints,
                          ...(breakpoints as any),
                      }
                    : breakpoints
            }
            {...rest}
        >
            {children}
        </ThemeSlider>
    );
};

export default ProductsSlider;
