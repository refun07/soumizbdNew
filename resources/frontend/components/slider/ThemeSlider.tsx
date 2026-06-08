import { useRef } from 'react';
import { Autoplay, Pagination } from 'swiper/modules';
import { Swiper, SwiperProps, SwiperRef } from 'swiper/react';
import { cn } from '../../utils/cn';

interface Props extends SwiperProps {
    arrows?: boolean;
    arrowClassNames?: {
        prev?: string;
        next?: string;
    };
    containerClass?: string;
}

const ThemeSlider = ({
    containerClass,
    className,
    arrows = true,
    modules,
    arrowClassNames,
    children,
    ...rest
}: Props) => {
    const options = rest;

    const sliderEl = useRef<SwiperRef>(null);
    const bulletContainerEl = useRef<HTMLDivElement>(null);

    return (
        <div className={cn('relative min-w-0 min-h-0', containerClass)}>
            <Swiper
                className={`theme-slider ${className}`}
                ref={sliderEl}
                pagination={{
                    enabled: true,
                    el: bulletContainerEl.current,
                    clickable: true,
                    bulletActiveClass: 'w-10 bg-secondary',
                    renderBullet: function (_index, className) {
                        return `<span class="size-1.5 inline-block rounded-full bg-primary transition-all cursor-pointer ${className}"></span>`;
                    },
                }}
                modules={[Pagination, Autoplay, ...(modules || [])]}
                {...options}
            >
                {children}
            </Swiper>

            <div
                className="flex justify-center gap-2.5 mt-7"
                ref={bulletContainerEl}
            ></div>
        </div>
    );
};

export default ThemeSlider;
