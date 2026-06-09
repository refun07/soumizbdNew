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
    paginationContainerClass?: string;
}

const ThemeSlider = ({
    containerClass,
    paginationContainerClass,
    className,
    arrows = true,
    modules,
    arrowClassNames,
    children,
    ...rest
}: Props) => {
    const { pagination, ...options } = rest;
    const hasPagination = pagination !== false;
    const paginationOptions =
        typeof pagination === 'object' && pagination !== null
            ? pagination
            : {};

    const sliderEl = useRef<SwiperRef>(null);
    const paginationTargetClassName = useRef(
        `theme-slider-pagination-${Math.random().toString(36).slice(2, 10)}`,
    ).current;

    return (
        <div className={cn('relative min-w-0 min-h-0', containerClass)}>
            <Swiper
                className={cn('theme-slider', className)}
                ref={sliderEl}
                pagination={
                    hasPagination
                        ? {
                              enabled: true,
                              el: `.${paginationTargetClassName}`,
                              clickable: true,
                              bulletActiveClass: 'w-10 !bg-[#FF8200]',
                              renderBullet: function (_index, className) {
                                  return `<span class="size-1.5 inline-block rounded-full bg-[#B3052A] transition-all cursor-pointer ${className}"></span>`;
                              },
                              ...paginationOptions,
                          }
                        : false
                }
                modules={[Pagination, Autoplay, ...(modules || [])]}
                {...options}
            >
                {children}
            </Swiper>

            {hasPagination && (
                <div
                    className={cn(
                        'mt-7 flex justify-center gap-2.5',
                        paginationContainerClass,
                        paginationTargetClassName,
                    )}
                ></div>
            )}
        </div>
    );
};

export default ThemeSlider;
