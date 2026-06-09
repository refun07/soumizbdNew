import { motion } from 'framer-motion';
import { FaArrowRight } from 'react-icons/fa';
import { useEffect, useRef, useState } from 'react';
import { Link } from 'react-router-dom';
import { Autoplay } from 'swiper/modules';
import { SwiperSlide } from 'swiper/react';
import Badge from '../../components/Badge';
import Button from '../../components/buttons/Button';
import ProductCard from '../../components/card/product-card';
import TallBannerCard from '../../components/card/TallBannerCard';
import CategoryCard from '../../components/category-card';
import DynamicSectionRenderer from '../../components/home/DynamicSectionRenderer';
import ProductSectionSkeleton from '../../components/skeletons/ProductSectionSkeleton';
import CategorySlider from '../../components/slider/CategorySlider';
import ProductsSlider from '../../components/slider/ProductsSlider';
import ThemeSlider from '../../components/slider/ThemeSlider';
import SectionTitle from '../../components/titles/SectionTitle';
import {
    useGetBannerQuery,
    useGetBannerSliderQuery,
    useGetBottomImageBannersQuery,
    useGetCategoryProductsQuery,
    useGetFeaturedCategoriesQuery,
    useGetFlashSaleProductsQuery,
    useGetFourImageBannersQuery,
    useGetOnSaleProductsQuery,
    useGetPromoCardsQuery,
    useGetSingleImageBannerQuery,
    useGetTopProductsQuery,
} from '../../store/features/api/homeApis/theme-one/homeApiThemeOne';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { containerAnimation, itemAnimation } from '../../utils/animation';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

declare global {
    interface Window {
        YT?: any;
        onYouTubeIframeAPIReady?: () => void;
        __youtubeIframeApiPromise?: Promise<void>;
    }
}

const getYoutubeVideoId = (url: string) => {
    if (!url) return null;

    const patterns = [
        /(?:youtube\.com\/watch\?v=|youtube\.com\/embed\/|youtu\.be\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/,
        /^([a-zA-Z0-9_-]{11})$/,
    ];

    for (const pattern of patterns) {
        const match = url.match(pattern);
        if (match?.[1]) {
            return match[1];
        }
    }

    return null;
};

const loadYoutubeIframeApi = () => {
    if (window.YT?.Player) return Promise.resolve();
    if (window.__youtubeIframeApiPromise)
        return window.__youtubeIframeApiPromise;

    window.__youtubeIframeApiPromise = new Promise<void>((resolve) => {
        const existingScript = document.querySelector<HTMLScriptElement>(
            'script[src="https://www.youtube.com/iframe_api"]',
        );

        if (!existingScript) {
            const script = document.createElement('script');
            script.src = 'https://www.youtube.com/iframe_api';
            document.body.appendChild(script);
        }

        const previousReady = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = () => {
            previousReady?.();
            resolve();
        };

        // In case API is already ready before callback assignment.
        if (window.YT?.Player) resolve();
    });

    return window.__youtubeIframeApiPromise;
};

const LazyBackgroundVideo = ({
    src,
    audioEnabled = false,
    isActive = true,
}: {
    src: string;
    audioEnabled?: boolean;
    isActive?: boolean;
}) => {
    const playerHostRef = useRef<HTMLDivElement | null>(null);
    const playerRef = useRef<any>(null);
    const loopTimerRef = useRef<number | null>(null);
    const audioEnabledRef = useRef(audioEnabled);
    const isActiveRef = useRef(isActive);
    const isPlayingRef = useRef(false);
    const [shouldLoad] = useState(true);
    const [isPlaying, setIsPlaying] = useState(false);
    const [hasPlayerError, setHasPlayerError] = useState(false);
    const videoId = getYoutubeVideoId(src);

    const stretchPlayerFrame = (player: any) => {
        const iframe = player?.getIframe?.();
        if (!iframe) return;

        iframe.style.position = 'absolute';
        iframe.style.top = '50%';
        iframe.style.left = '50%';
        iframe.style.transform = 'translate(-50%, -50%)';
        iframe.style.width = 'max(100vw, 177.77vh)';
        iframe.style.height = 'max(56.25vw, 100vh)';
        iframe.style.border = '0';
        iframe.style.pointerEvents = 'none';
    };

    useEffect(() => {
        audioEnabledRef.current = audioEnabled;
        isActiveRef.current = isActive;
    }, [audioEnabled, isActive]);

    useEffect(() => {
        if (!shouldLoad || !videoId || !playerHostRef.current || hasPlayerError)
            return;

        let isMounted = true;
        const failSafeTimer = isActiveRef.current
            ? window.setTimeout(() => {
                  if (isActiveRef.current && !isPlayingRef.current)
                      setHasPlayerError(true);
              }, 5000)
            : null;

        loadYoutubeIframeApi().then(() => {
            if (!isMounted || !playerHostRef.current || !window.YT?.Player)
                return;

            playerRef.current = new window.YT.Player(playerHostRef.current, {
                videoId,
                playerVars: {
                    autoplay: 1,
                    mute: audioEnabledRef.current ? 0 : 1,
                    controls: 0,
                    modestbranding: 1,
                    rel: 0,
                    playsinline: 1,
                    disablekb: 1,
                    fs: 0,
                    iv_load_policy: 3,
                },
                events: {
                    onReady: (event: any) => {
                        stretchPlayerFrame(event.target);
                        const shouldPlay = isActiveRef.current;

                        if (audioEnabledRef.current && shouldPlay) {
                            event.target.unMute();
                            event.target.setVolume(100);
                        } else {
                            event.target.mute();
                        }

                        if (shouldPlay) {
                            event.target.playVideo();
                        } else {
                            event.target.pauseVideo();
                        }
                    },
                    onStateChange: (event: any) => {
                        stretchPlayerFrame(event.target);
                        if (event.data === window.YT.PlayerState.PLAYING) {
                            isPlayingRef.current = true;
                            setIsPlaying(true);
                            if (failSafeTimer) {
                                window.clearTimeout(failSafeTimer);
                            }

                            if (!loopTimerRef.current) {
                                loopTimerRef.current = window.setInterval(() => {
                                    const player = playerRef.current;
                                    if (!player?.getCurrentTime || !player?.seekTo)
                                        return;

                                    const currentTime = player.getCurrentTime();
                                    if (currentTime >= 20) {
                                        player.seekTo(0, true);
                                    }
                                }, 500);
                            }
                        } else if (event.data === window.YT.PlayerState.ENDED) {
                            if (loopTimerRef.current) {
                                window.clearInterval(loopTimerRef.current);
                                loopTimerRef.current = null;
                            }
                            if (isActiveRef.current) {
                                event.target.seekTo(0, true);
                                event.target.playVideo();
                            }
                        } else if (event.data === window.YT.PlayerState.PAUSED) {
                            if (loopTimerRef.current) {
                                window.clearInterval(loopTimerRef.current);
                                loopTimerRef.current = null;
                            }
                            if (!isActiveRef.current) {
                                isPlayingRef.current = false;
                                setIsPlaying(false);
                            }
                        }
                    },
                    onError: () => {
                        isPlayingRef.current = false;
                        setHasPlayerError(true);
                        if (failSafeTimer) {
                            window.clearTimeout(failSafeTimer);
                        }
                    },
                },
            });
        });

        return () => {
            isMounted = false;
            isPlayingRef.current = false;
            if (failSafeTimer) {
                window.clearTimeout(failSafeTimer);
            }
            if (loopTimerRef.current) {
                window.clearInterval(loopTimerRef.current);
                loopTimerRef.current = null;
            }
            playerRef.current?.destroy?.();
            playerRef.current = null;
        };
    }, [shouldLoad, videoId, hasPlayerError]);

    useEffect(() => {
        const player = playerRef.current;
        if (!player) return;

        if (!isActive) {
            isPlayingRef.current = false;
            setIsPlaying(false);
            player.mute?.();
            player.pauseVideo?.();
            return;
        }

        if (audioEnabled) {
            player.unMute?.();
            player.setVolume?.(100);
        } else {
            player.mute?.();
        }

        player.playVideo?.();
    }, [audioEnabled, isActive]);

    if (!videoId) return null;

    return (
        <div className="absolute inset-0 pointer-events-none overflow-hidden">
            {shouldLoad && !hasPlayerError && (
                <div className="absolute inset-0">
                    <div
                        className={cn(
                            'absolute inset-0 transition-opacity duration-300',
                            isActive && isPlaying
                                ? 'opacity-100 visible'
                                : 'opacity-0 invisible',
                        )}
                    >
                        <div
                            ref={playerHostRef}
                            className="relative h-full w-full overflow-hidden"
                        />
                    </div>
                </div>
            )}
        </div>
    );
};

const OnSaleCountdown = ({ endsAt }: { endsAt?: string | null }) => {
    const [now, setNow] = useState(Date.now());
    const endTime = endsAt ? new Date(endsAt).getTime() : NaN;

    useEffect(() => {
        if (!endsAt || Number.isNaN(endTime)) return;

        const timer = window.setInterval(() => setNow(Date.now()), 1000);
        return () => window.clearInterval(timer);
    }, [endsAt, endTime]);

    if (!endsAt || Number.isNaN(endTime)) return null;

    const remaining = Math.max(0, endTime - now);
    const totalSeconds = Math.floor(remaining / 1000);
    const days = Math.floor(totalSeconds / 86400);
    const hours = Math.floor((totalSeconds % 86400) / 3600);
    const minutes = Math.floor((totalSeconds % 3600) / 60);
    const seconds = totalSeconds % 60;
    const parts = [
        { value: days, label: translate('Days') },
        { value: hours, label: translate('Hour') },
        { value: minutes, label: translate('Min') },
        { value: seconds, label: translate('Sec') },
    ];

    return (
        <div className="flex items-center gap-1.5 text-white">
            {parts.map((part, index) => (
                <div key={part.label} className="flex items-start gap-1.5">
                    {index > 0 && (
                        <span className="text-xl font-semibold leading-none md:text-2xl">
                            :
                        </span>
                    )}
                    <div className="flex min-w-8 flex-col items-center">
                        <span className="text-xl font-semibold leading-none md:text-2xl">
                            {String(part.value).padStart(2, '0')}
                        </span>
                        <span className="mt-0.5 text-[10px] leading-none text-white/80">
                            {part.label}
                        </span>
                    </div>
                </div>
            ))}
        </div>
    );
};

const HomeThemeOne = () => {
    const dispatch = useAppDispatch();
    const { data: banner, isLoading: isBannerLoading } = useGetBannerQuery();
    const promoCardsRes = useGetPromoCardsQuery();
    const onSaleProductRes = useGetOnSaleProductsQuery();
    const bannerSliderRes = useGetBannerSliderQuery();
    const featuredCategoryRes = useGetFeaturedCategoriesQuery();
    const topProductRes = useGetTopProductsQuery();
    const singleImageBannerRes = useGetSingleImageBannerQuery();
    const flashSaleProductRes = useGetFlashSaleProductsQuery();
    const fourImageBannerRes = useGetFourImageBannersQuery();
    const categoryProductsRes = useGetCategoryProductsQuery();
    const bottomImageBannerRes = useGetBottomImageBannersQuery();

    // Get dynamic sections from window.config
    const dynamicSections = (window as any).config?.dynamicSections || [];
    const mainSideBanners =
        banner?.sideBanners?.filter((item) => item.backgroundImage) ?? [];
    const mainBannerAutoplay = banner?.settings?.autoplay
        ? {
              delay: banner?.settings?.delay || 5000,
              disableOnInteraction: false,
              pauseOnMouseEnter: true,
          }
        : false;
    const promoCards = promoCardsRes.data?.cards ?? [];
    const shouldShowPromoCards =
        promoCardsRes.data?.showThemeOnePromoCards === '1' &&
        promoCards.length > 0;
    const allOnSaleProducts = onSaleProductRes.data?.products ?? [];
    const onSaleProducts = allOnSaleProducts.slice(0, 15);
    const secondaryOnSaleProducts = allOnSaleProducts.slice(0, 20);
    const shouldShowOnSaleProducts =
        onSaleProductRes.data?.showThemeOneOnSaleProducts === '1' &&
        onSaleProducts.length > 0;
    const shouldShowSecondaryOnSaleProducts =
        (onSaleProductRes.data?.showThemeOneSecondaryOnSaleProducts ?? '1') ===
            '1' && secondaryOnSaleProducts.length > 0;
    const featuredProducts = topProductRes.data?.products ?? [];
    const shouldShowFeaturedProducts =
        topProductRes.data?.showThemeOneTopProducts === '1' &&
        featuredProducts.length > 0;
    const featuredProductCategoryTabs = featuredProducts.reduce<
        { id: number; name: string; slug: string }[]
    >((categories, product) => {
        product.categories?.forEach((category) => {
            if (!categories.some((item) => item.id === category.id)) {
                categories.push({
                    id: category.id,
                    name: category.name,
                    slug: category.slug,
                });
            }
        });

        return categories;
    }, []);
    const visibleFeaturedProductCategoryTabs =
        featuredProductCategoryTabs.slice(0, 4);

    return (
        <div className="space-y-10 lg:space-y-16 pb-5">
            {/* start::banner */}
            {banner?.showThemeOneMainBanner === '1' && (
                <section
                    id="banner"
                    className="relative left-1/2 mt-[8px] w-screen -translate-x-1/2 overflow-hidden"
                >
                    {isBannerLoading ? (
                        <div className="h-[520px] md:h-[636px] skeleton"></div>
                    ) : (
                        <div
                            className={cn(
                                'grid w-full grid-cols-1 gap-4 px-3.5 md:gap-5 md:px-5 lg:gap-6 lg:px-6',
                                mainSideBanners.length > 0 &&
                                    'lg:grid-cols-[minmax(0,60fr)_minmax(0,40fr)]',
                            )}
                        >
                        <ThemeSlider
                            slidesPerView={1}
                            spaceBetween={0}
                            autoplay={mainBannerAutoplay}
                            loop={false}
                            rewind={
                                !!(banner?.banners && banner.banners.length > 1)
                            }
                            pagination={
                                !!(
                                    banner?.banners &&
                                    banner.banners.length > 1
                                )
                            }
                            containerClass="min-w-0"
                            paginationContainerClass="absolute inset-x-0 bottom-4 z-20 mt-0"
                        >
                            {banner?.banners?.map((item, idx) => (
                                <SwiperSlide key={idx}>
                                    {({ isActive: isSlideActive }) => {
                                        const shouldShowVideo =
                                            item.useBackgroundVideo === '1' &&
                                            !!item.backgroundVideoLink;
                                        const shouldShowForegroundImage =
                                            item.showForegroundImage === '1' &&
                                            !!item.foregroundImage;

                                        return (
                                            <div
                                                className="relative aspect-[16/9] w-full overflow-hidden rounded-[5px] bg-center md:aspect-[21/9] lg:h-[420px] lg:aspect-auto xl:h-[636px] 3xl:h-[636px]"
                                                style={{
                                                    backgroundImage:
                                                        item.backgroundImage
                                                            ? `url(${item.backgroundImage})`
                                                            : 'none',
                                                    backgroundSize: '100% 100%',
                                                    backgroundRepeat:
                                                        'no-repeat',
                                                }}
                                            >
                                                {shouldShowVideo &&
                                                    isSlideActive && (
                                                        <>
                                                            <LazyBackgroundVideo
                                                                src={
                                                                    item.backgroundVideoLink
                                                                }
                                                                audioEnabled={
                                                                    item.backgroundVideoAudio ===
                                                                    '1'
                                                                }
                                                            />
                                                            <div className="absolute inset-0 bg-black/20"></div>
                                                        </>
                                                    )}

                                                <div
                                                    className={`relative z-10 flex h-full items-center gap-y-8 px-4 sm:px-6 lg:px-8 xl:px-10 ${
                                                        banner?.settings
                                                            ?.contentAlignment ===
                                                        'center'
                                                            ? 'flex-col justify-center'
                                                            : banner?.settings
                                                                  ?.contentAlignment ===
                                                              'right'
                                                            ? 'flex-row-reverse justify-between'
                                                            : 'max-md:flex-col justify-between'
                                                    }`}
                                                >
                                                    <div
                                                        className={`max-w-[560px] ${
                                                            banner?.settings
                                                                ?.contentAlignment ===
                                                            'center'
                                                                ? 'text-center relative z-10'
                                                                : banner
                                                                      ?.settings
                                                                      ?.contentAlignment ===
                                                                  'right'
                                                                ? 'text-right'
                                                                : 'max-md:text-center'
                                                        }`}
                                                    >
                                                        {item.badgeText &&
                                                            item.badgeText !=
                                                                '' && (
                                                                <motion.div
                                                                    initial={{
                                                                        opacity: 0,
                                                                        y: 20,
                                                                    }}
                                                                    whileInView={{
                                                                        opacity: 1,
                                                                        y: 0,
                                                                    }}
                                                                    viewport={{
                                                                        once: true,
                                                                    }}
                                                                    transition={{
                                                                        duration: 0.5,
                                                                    }}
                                                                >
                                                                    <Badge>
                                                                        {
                                                                            item.badgeText
                                                                        }
                                                                    </Badge>
                                                                </motion.div>
                                                            )}

                                                        <motion.h1
                                                            initial={{
                                                                opacity: 0,
                                                                y: 20,
                                                            }}
                                                            whileInView={{
                                                                opacity: 1,
                                                                y: 0,
                                                            }}
                                                            viewport={{
                                                                once: true,
                                                            }}
                                                            transition={{
                                                                duration: 0.5,
                                                                delay: 0.2,
                                                            }}
                                                            className="ek-h1 font-semibold text-primary mt-5 mb-2"
                                                            style={
                                                                item.titleColor
                                                                    ? {
                                                                          color: item.titleColor,
                                                                      }
                                                                    : undefined
                                                            }
                                                        >
                                                            {item.title}
                                                        </motion.h1>
                                                        <motion.p
                                                            initial={{
                                                                opacity: 0,
                                                                y: 20,
                                                            }}
                                                            whileInView={{
                                                                opacity: 1,
                                                                y: 0,
                                                            }}
                                                            viewport={{
                                                                once: true,
                                                            }}
                                                            transition={{
                                                                duration: 0.5,
                                                                delay: 0.4,
                                                            }}
                                                            className="mb-7 text-base text-primary/70 md:text-lg"
                                                            style={
                                                                item.subTitleColor
                                                                    ? {
                                                                          color: item.subTitleColor,
                                                                      }
                                                                    : undefined
                                                            }
                                                        >
                                                            {item.subTitle}
                                                        </motion.p>
                                                        {item.link &&
                                                            item.link != '' && (
                                                                <motion.div
                                                                    initial={{
                                                                        opacity: 0,
                                                                        y: 20,
                                                                    }}
                                                                    whileInView={{
                                                                        opacity: 1,
                                                                        y: 0,
                                                                    }}
                                                                    viewport={{
                                                                        once: true,
                                                                    }}
                                                                    transition={{
                                                                        duration: 0.5,
                                                                        delay: 0.5,
                                                                    }}
                                                                >
                                                                    <Button
                                                                        as="link"
                                                                        to={
                                                                            item.link ||
                                                                            '#'
                                                                        }
                                                                        variant="primary"
                                                                        size="lg"
                                                                    >
                                                                        {translate(
                                                                            'VISIT SHOP',
                                                                        )}
                                                                    </Button>
                                                                </motion.div>
                                                            )}
                                                    </div>

                                                    {banner?.settings
                                                        ?.contentAlignment ===
                                                        'center' &&
                                                    shouldShowForegroundImage ? (
                                                        <motion.div
                                                            initial={{
                                                                opacity: 0,
                                                            }}
                                                            whileInView={{
                                                                opacity: 1,
                                                            }}
                                                            viewport={{
                                                                once: true,
                                                            }}
                                                            transition={{
                                                                duration: 0.5,
                                                                delay: 0.5,
                                                            }}
                                                            className="absolute inset-0 z-0"
                                                        >
                                                            <img
                                                                src={
                                                                    item.foregroundImage
                                                                }
                                                                alt=""
                                                                className="w-full h-full object-fill object-center"
                                                            />
                                                        </motion.div>
                                                    ) : shouldShowForegroundImage ? (
                                                        <motion.div
                                                            initial={{
                                                                opacity: 0,
                                                                y: 20,
                                                            }}
                                                            whileInView={{
                                                                opacity: 1,
                                                                y: 0,
                                                            }}
                                                            viewport={{
                                                                once: true,
                                                            }}
                                                            transition={{
                                                                duration: 0.5,
                                                                delay: 0.5,
                                                            }}
                                                        >
                                                            <img
                                                                src={
                                                                    item.foregroundImage
                                                                }
                                                                alt=""
                                                                className="mx-auto max-w-[72%] xl:max-w-[320px] 3xl:max-w-[430px]"
                                                            />
                                                        </motion.div>
                                                    ) : null}
                                                </div>
                                            </div>
                                        );
                                    }}
                                </SwiperSlide>
                            ))}
                        </ThemeSlider>

                            {mainSideBanners.length > 0 && (
                                <ThemeSlider
                                    slidesPerView={1}
                                    spaceBetween={0}
                                    autoplay={mainBannerAutoplay}
                                    loop={false}
                                    rewind={mainSideBanners.length > 1}
                                    pagination={mainSideBanners.length > 1}
                                    containerClass="min-w-0"
                                    paginationContainerClass="absolute inset-x-0 bottom-4 z-20 mt-0"
                                >
                                    {mainSideBanners.map((item, idx) => {
                                        const image = (
                                            <img
                                                src={item.backgroundImage}
                                                alt=""
                                                loading={
                                                    idx === 0
                                                        ? 'eager'
                                                        : 'lazy'
                                                }
                                                className="h-full w-full object-fill"
                                            />
                                        );

                                        return (
                                            <SwiperSlide key={idx}>
                                                <div className="relative aspect-[16/9] w-full overflow-hidden rounded-[5px] bg-stone-100 md:aspect-[21/9] lg:h-[420px] lg:aspect-auto xl:h-[636px] 3xl:h-[636px]">
                                                    {item.link ? (
                                                        <Link
                                                            to={item.link}
                                                            className="block h-full w-full"
                                                            aria-label={translate(
                                                                'View banner',
                                                            )}
                                                        >
                                                            {image}
                                                        </Link>
                                                    ) : (
                                                        image
                                                    )}
                                                </div>
                                            </SwiperSlide>
                                        );
                                    })}
                                </ThemeSlider>
                            )}
                        </div>
                    )}
                </section>
            )}

            {shouldShowPromoCards && (
                <section id="promo-cards" className="!mt-2.5">
                    <div className="container">
                        <div
                            className="overflow-hidden rounded-[5px] px-3 py-6 md:px-5"
                            style={{
                                backgroundColor:
                                    promoCardsRes.data
                                        ?.sectionBackgroundColor || '#f7f7f7',
                            }}
                        >
                            <ThemeSlider
                                slidesPerView={2}
                                spaceBetween={10}
                                loop={false}
                                rewind={promoCards.length > 1}
                                pagination={promoCards.length > 1}
                                paginationContainerClass="mt-4"
                                breakpoints={{
                                    480: {
                                        slidesPerView: 2,
                                        spaceBetween: 12,
                                    },
                                    768: {
                                        slidesPerView: 3,
                                    },
                                    1024: {
                                        slidesPerView: 4,
                                        spaceBetween: 16,
                                    },
                                }}
                            >
                                {promoCards.map((card, idx) => {
                                    return (
                                        <SwiperSlide key={idx}>
                                            <div
                                                className="flex h-[152px] flex-col items-center justify-center gap-2.5 rounded-[5px] px-2.5 py-4 text-center shadow-sm md:h-[86px] md:flex-row md:justify-start md:gap-4 md:px-5 md:py-4 md:text-left"
                                                style={{
                                                    backgroundColor:
                                                        promoCardsRes.data
                                                            ?.backgroundColor ||
                                                        '#ffffff',
                                                }}
                                            >
                                                {card.image && (
                                                    <img
                                                        src={card.image}
                                                        alt=""
                                                        loading={
                                                            idx < 4
                                                                ? 'eager'
                                                                : 'lazy'
                                                        }
                                                        className="h-12 w-12 shrink-0 object-contain md:h-16 md:w-16"
                                                    />
                                                )}
                                                <div className="min-w-0">
                                                    <h3
                                                        className="line-clamp-2 text-sm font-semibold leading-tight md:text-base"
                                                        style={{
                                                            color:
                                                                promoCardsRes
                                                                    .data
                                                                    ?.mainTextColor ||
                                                                '#B3052A',
                                                        }}
                                                    >
                                                        {card.mainText}
                                                    </h3>
                                                    <p
                                                        className="mt-1 line-clamp-2 text-xs leading-tight md:text-sm"
                                                        style={{
                                                            color:
                                                                promoCardsRes
                                                                    .data
                                                                    ?.secondaryTextColor ||
                                                                '#3f3f46',
                                                        }}
                                                    >
                                                        {card.subText}
                                                    </p>
                                                </div>
                                            </div>
                                        </SwiperSlide>
                                    );
                                })}
                            </ThemeSlider>
                        </div>
                    </div>
                </section>
            )}

            {shouldShowOnSaleProducts && (
                <section id="on-sale-products" className="!mt-2.5">
                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="container"
                    >
                        <div
                            className="overflow-hidden rounded-[5px] px-3.5 py-5 md:px-6 md:py-7"
                            style={{
                                backgroundColor:
                                    onSaleProductRes.data?.backgroundColor ||
                                    '#B3052A',
                            }}
                        >
                            <div className="mb-4 flex flex-wrap items-center justify-between gap-3 border-b border-white/25 pb-3">
                                <div className="flex flex-wrap items-end gap-x-5 gap-y-2">
                                    <h2 className="text-xl font-normal leading-tight text-white md:text-2xl">
                                        <span className="border-b-[5px] border-[#F2C488] pb-2 font-semibold">
                                            {translate('On Sale')}
                                        </span>{' '}
                                        {translate('Products')}
                                    </h2>
                                    <OnSaleCountdown
                                        endsAt={onSaleProductRes.data?.endsAt}
                                    />
                                </div>

                                <Link
                                    to="/on-sale-products"
                                    className="inline-flex h-9 items-center justify-center gap-2 rounded-[3px] border border-white/45 px-4 text-xs font-semibold text-white transition hover:bg-white hover:text-primary"
                                >
                                    {translate('View More')}
                                    <FaArrowRight
                                        className="text-[10px]"
                                        aria-hidden="true"
                                    />
                                </Link>
                            </div>

                            <p className="mb-4 text-sm font-medium text-white">
                                {translate('Winter Special Offer')}
                            </p>

                            <ProductsSlider
                                withDefaultBreakpoints={false}
                                slidesPerView={2}
                                spaceBetween={12}
                                loop={false}
                                rewind={onSaleProducts.length > 2}
                                pagination={
                                    onSaleProducts.length > 2
                                        ? {
                                              bulletActiveClass:
                                                  'w-10 !bg-white',
                                              renderBullet: function (
                                                  _index,
                                                  className,
                                              ) {
                                                  return `<span class="size-1.5 inline-block rounded-full bg-white/60 transition-all cursor-pointer ${className}"></span>`;
                                              },
                                          }
                                        : false
                                }
                                paginationContainerClass="mt-6"
                                breakpoints={{
                                    640: {
                                        slidesPerView: 3,
                                        spaceBetween: 14,
                                    },
                                    1024: {
                                        slidesPerView: 4,
                                        spaceBetween: 16,
                                    },
                                    1280: {
                                        slidesPerView: 5,
                                        spaceBetween: 20,
                                    },
                                }}
                            >
                                {onSaleProducts.map((product) => (
                                    <SwiperSlide key={product.id}>
                                        <motion.div
                                            variants={itemAnimation}
                                            className="h-full"
                                        >
                                            <ProductCard product={product} />
                                        </motion.div>
                                    </SwiperSlide>
                                ))}
                            </ProductsSlider>
                        </div>
                    </motion.div>
                </section>
            )}

            {/* start::featured-category */}
            {featuredCategoryRes.isLoading ? (
                <ProductSectionSkeleton />
            ) : featuredCategoryRes.data?.showFeaturedCategories === '1' ? (
                <section
                    className="featured-category-section"
                    id="featured-categories"
                >
                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="container"
                    >
                        <SectionTitle
                            animated
                            title={translate('[Featured] Categories')}
                            highlightClassName="text-[#B3052A]"
                            subTitle={translate('Handpicked')}
                            onClick={() => dispatch(togglePopup('categories'))}
                        />
                        {featuredCategoryRes.data?.categories?.length > 4 ? (
                            <CategorySlider>
                                {featuredCategoryRes.data?.categories.map(
                                    (category) => (
                                        <SwiperSlide key={category.id}>
                                            <motion.div
                                                variants={itemAnimation}
                                            >
                                                <CategoryCard
                                                    title={category.name}
                                                    quantity={
                                                        category.productsCount
                                                    }
                                                    to={`/categories/${category.slug}`}
                                                    image={
                                                        category.thumbnailImage
                                                    }
                                                />
                                            </motion.div>
                                        </SwiperSlide>
                                    ),
                                )}
                            </CategorySlider>
                        ) : (
                            <div className="flex justify-center">
                                <div className="grid grid-cols-2 md:grid-cols-4 gap-4 ">
                                    {featuredCategoryRes.data?.categories.map(
                                        (category) => (
                                            <div key={category.id}>
                                                <CategoryCard
                                                    title={category.name}
                                                    quantity={
                                                        category.productsCount
                                                    }
                                                    to={`/categories/${category.slug}`}
                                                    image={
                                                        category.thumbnailImage
                                                    }
                                                />
                                            </div>
                                        ),
                                    )}
                                </div>
                            </div>
                        )}
                    </motion.div>
                </section>
            ) : null}
            {/* end::featured-category */}

            {/* start::featured-products */}
            {topProductRes.isLoading ? (
                <ProductSectionSkeleton withCategoryImage />
            ) : shouldShowFeaturedProducts ? (
                <section
                    className="featured-products-section"
                    id="featured-products"
                >
                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="container"
                    >
                        <div className="relative mb-10 flex flex-col items-start justify-between gap-4 2xl:flex-row">
                            <div className="relative z-[1] w-full grow 2xl:pr-4">
                                <motion.p
                                    variants={itemAnimation}
                                    className="ek-headline"
                                >
                                    {translate('Popular')}
                                </motion.p>

                                <div className="flex items-center max-sm:justify-between sm:gap-10">
                                    <motion.h2
                                        variants={itemAnimation}
                                        className="ek-h2 pb-4 font-light capitalize"
                                    >
                                        <span className="border-b-[5px] border-[#F2C488] pb-3.5 font-bold text-[#B3052A]">
                                            {translate('Featured')}
                                        </span>{' '}
                                        {translate('Products')}
                                    </motion.h2>
                                </div>

                                <hr className="border-b border-[#EDEDED]" />
                            </div>

                            {featuredProductCategoryTabs.length > 0 ? (
                                <motion.div
                                    variants={itemAnimation}
                                    className="relative z-[1] flex min-h-[42px] w-full max-w-full items-center gap-6 overflow-x-auto pb-1 2xl:mt-16 2xl:w-auto 2xl:max-w-[52%] 2xl:shrink-0 2xl:justify-end"
                                >
                                    <Link
                                        to="/products"
                                        className="shrink-0 whitespace-nowrap text-xs font-medium text-[#B3052A] transition md:text-sm"
                                    >
                                        {translate('All Products')}
                                    </Link>
                                    {visibleFeaturedProductCategoryTabs.map(
                                        (category) => (
                                            <Link
                                                to={`/categories/${category.slug}`}
                                                key={category.id}
                                                className="shrink-0 whitespace-nowrap text-xs font-medium text-foreground transition hover:text-[#B3052A] md:text-sm"
                                            >
                                                {category.name}
                                            </Link>
                                        ),
                                    )}
                                </motion.div>
                            ) : null}
                        </div>

                        <ProductsSlider>
                            {featuredProducts.map((product) => (
                                <SwiperSlide key={product.id}>
                                    <motion.div variants={itemAnimation}>
                                        <ProductCard product={product} />
                                    </motion.div>
                                </SwiperSlide>
                            ))}
                        </ProductsSlider>
                    </motion.div>
                </section>
            ) : null}
            {/* end::featured-products */}

            {bannerSliderRes.data?.[0]?.showThemeOneSliderSection == '1' && (
                <motion.section
                    variants={{
                        hidden: { opacity: 1, translateY: 20 },
                        whileInView: {
                            opacity: 1,
                            translateY: 0,
                            transition: {
                                staggerChildren: 0.3,
                            },
                        },
                    }}
                    className="!mt-2.5"
                    id="banner-slider"
                >
                    <div className="container">
                        <ThemeSlider
                            slidesPerView={1}
                            spaceBetween={15}
                            autoplay={{
                                delay: 2000,
                                pauseOnMouseEnter: true,
                            }}
                            modules={[Autoplay]}
                            breakpoints={{
                                568: {
                                    slidesPerView: 2,
                                },
                                768: {
                                    slidesPerView: 3,
                                },
                                992: {
                                    slidesPerView: 4,
                                },
                            }}
                        >
                            {bannerSliderRes.isLoading
                                ? [1, 2, 3, 4].map((item) => (
                                      <SwiperSlide key={item}>
                                          <div className="skeleton aspect-[14/10] h-auto w-full rounded-banner" />
                                      </SwiperSlide>
                                  ))
                                : bannerSliderRes?.data?.map((item, idx) => (
                                      <SwiperSlide key={idx}>
                                          <Link
                                              to={item.url}
                                              className="block h-full w-full"
                                          >
                                              <motion.img
                                                  variants={{
                                                      hidden: {
                                                          y: 20,
                                                          opacity: 0,
                                                      },
                                                      whileInView: {
                                                          y: 0,
                                                          opacity: 1,
                                                      },
                                                  }}
                                                  viewport={{ once: true }}
                                                  src={item.image}
                                                  alt=""
                                                  className="block aspect-[14/10] h-full w-full rounded-banner object-cover"
                                              />
                                          </Link>
                                      </SwiperSlide>
                                  ))}
                        </ThemeSlider>
                    </div>
                </motion.section>
            )}

            {/* end::banner */}
            {banner?.showThemeOneSecondaryBanner === '1' && (
                <section id="secondary-banner">
                    <div className="container">
                        {(() => {
                            const item = banner.secondaryBanner;
                            const shouldShowVideo =
                                item.useBackgroundVideo === '1' &&
                                !!item.backgroundVideoLink;
                            const secondaryContentAlignment =
                                item.contentAlignment ?? 'left';

                            return (
                                <div
                                    className="relative h-[520px] w-full overflow-hidden rounded-banner bg-center md:h-[510px]"
                                    style={{
                                        backgroundImage: item.backgroundImage
                                            ? `url(${item.backgroundImage})`
                                            : 'none',
                                        backgroundSize: 'cover',
                                        backgroundRepeat: 'no-repeat',
                                        backgroundPosition: 'center center',
                                    }}
                                >
                                {shouldShowVideo && (
                                    <>
                                        <LazyBackgroundVideo
                                            src={item.backgroundVideoLink}
                                            audioEnabled={
                                                item.backgroundVideoAudio ===
                                                '1'
                                            }
                                        />
                                        <div className="absolute inset-0 bg-black/20"></div>
                                    </>
                                )}

                                <div className="max-w-[1212px] h-full mx-auto px-6 max-md:pt-8 max-md:pb-8 flex items-center justify-center relative z-10">
                                    <div
                                        className={cn(
                                            'max-w-[660px] w-full',
                                            secondaryContentAlignment ===
                                                'center'
                                                ? 'mx-auto text-center'
                                                : secondaryContentAlignment ===
                                                  'right'
                                                ? 'ml-auto text-right'
                                                : 'mr-auto text-left',
                                        )}
                                    >
                                        {item.badgeText &&
                                            item.badgeText != '' && (
                                                <Badge>{item.badgeText}</Badge>
                                            )}

                                        <motion.h1
                                            initial={{ opacity: 0, y: 20 }}
                                            whileInView={{ opacity: 1, y: 0 }}
                                            viewport={{ once: true }}
                                            transition={{
                                                duration: 0.5,
                                                delay: 0.2,
                                            }}
                                            className="ek-h1 font-semibold text-primary mt-5 mb-2"
                                            style={
                                                item.titleColor
                                                    ? { color: item.titleColor }
                                                    : undefined
                                            }
                                        >
                                            {item.title}
                                        </motion.h1>

                                        <motion.p
                                            initial={{ opacity: 0, y: 20 }}
                                            whileInView={{ opacity: 1, y: 0 }}
                                            viewport={{ once: true }}
                                            transition={{
                                                duration: 0.5,
                                                delay: 0.3,
                                            }}
                                            className="text-primary/70 text-lg mb-7"
                                            style={
                                                item.subTitleColor
                                                    ? {
                                                          color: item.subTitleColor,
                                                      }
                                                    : undefined
                                            }
                                        >
                                            {item.subTitle}
                                        </motion.p>

                                        {item.link && item.link != '' && (
                                            <motion.div
                                                initial={{ opacity: 0, y: 20 }}
                                                whileInView={{
                                                    opacity: 1,
                                                    y: 0,
                                                }}
                                                viewport={{ once: true }}
                                                transition={{
                                                    duration: 0.5,
                                                    delay: 0.4,
                                                }}
                                            >
                                                <Button
                                                    as="link"
                                                    to={item.link || '#'}
                                                    variant="primary"
                                                    size="lg"
                                                >
                                                    {translate('VISIT SHOP')}
                                                </Button>
                                            </motion.div>
                                        )}
                                    </div>
                                </div>
                                </div>
                            );
                        })()}
                    </div>
                </section>
            )}

            {shouldShowSecondaryOnSaleProducts ? (
                <section id="secondary-on-sale-products">
                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="container"
                    >
                        <SectionTitle
                            animated
                            title={translate('[On Sale] Products')}
                            subTitle={translate('Popular')}
                            link="/on-sale-products"
                        />

                        <div className="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 xl:gap-5">
                            {secondaryOnSaleProducts.map((product) => (
                                <motion.div
                                    variants={itemAnimation}
                                    key={product.id}
                                    className="h-full"
                                >
                                    <ProductCard product={product} />
                                </motion.div>
                            ))}
                        </div>

                        <div className="mt-6 flex justify-end md:hidden">
                            <Button
                                as="link"
                                to="/on-sale-products"
                                variant="outline"
                            >
                                {translate('View More')}
                            </Button>
                        </div>
                    </motion.div>
                </section>
            ) : null}

            {singleImageBannerRes.data?.showThemeOneSingleImageBanner ? (
                <section id="single-image-banner">
                    <div className="container">
                        <motion.div
                            variants={containerAnimation}
                            initial="initial"
                            whileInView="whileInView"
                            viewport={{ once: true }}
                            className="md:h-[644px] rounded-banner bg-gray-100 max-md:pt-[60px] max-md:pb-5 px-4 bg-center relative"
                            style={{
                                backgroundImage: `url(${singleImageBannerRes?.data.themeOneSingleImageBannerBackgroundImage})`,
                                backgroundSize: 'cover',
                                backgroundRepeat: 'no-repeat',
                            }}
                        >
                            <div className="max-w-[1212px] h-full mx-auto flex max-md:flex-col items-center justify-between gap-y-8">
                                <div className="max-w-[660px] max-md:text-center">
                                    {singleImageBannerRes.data
                                        .themeOneSingleImageBannerBadgeText &&
                                        singleImageBannerRes.data
                                            .themeOneSingleImageBannerBadgeText !=
                                            '' && (
                                            <Badge
                                                reverse
                                                textColor={
                                                    singleImageBannerRes.data
                                                        .themeOneSingleImageBannerBadgeTextColor
                                                }
                                                backgroundColor={
                                                    singleImageBannerRes.data
                                                        .themeOneSingleImageBannerBadgeBackgroundColor
                                                }
                                                classNames={{
                                                    container:
                                                        'sm:absolute sm:top-10 sm:right-0 mb-4',
                                                }}
                                            >
                                                {
                                                    singleImageBannerRes.data
                                                        .themeOneSingleImageBannerBadgeText
                                                }
                                            </Badge>
                                        )}
                                    <motion.p
                                        variants={itemAnimation}
                                        className="ek-headline"
                                        style={
                                            singleImageBannerRes.data
                                                .themeOneSingleImageBannerHeadlineColor
                                                ? {
                                                      color: singleImageBannerRes
                                                          .data
                                                          .themeOneSingleImageBannerHeadlineColor,
                                                  }
                                                : undefined
                                        }
                                    >
                                        {
                                            singleImageBannerRes.data
                                                .themeOneSingleImageBannerHeadline
                                        }
                                    </motion.p>
                                    <motion.h1
                                        variants={itemAnimation}
                                        className="ek-h1 font-semibold text-primary mt-4"
                                        style={
                                            singleImageBannerRes.data
                                                .themeOneSingleImageBannerTitleColor
                                                ? {
                                                      color: singleImageBannerRes
                                                          .data
                                                          .themeOneSingleImageBannerTitleColor,
                                                  }
                                                : undefined
                                        }
                                    >
                                        {
                                            singleImageBannerRes.data
                                                .themeOneSingleImageBannerTitle
                                        }
                                    </motion.h1>
                                    <div className="border-b border-secondary/34 mt-2.5 mb-6">
                                        <span className="border-b-4 border-theme-orange block max-w-[100px]"></span>
                                    </div>

                                    <motion.p
                                        variants={itemAnimation}
                                        className="text-primary/70 text-lg mb-7"
                                        style={
                                            singleImageBannerRes.data
                                                .themeOneSingleImageBannerSubTitleColor
                                                ? {
                                                      color: singleImageBannerRes
                                                          .data
                                                          .themeOneSingleImageBannerSubTitleColor,
                                                  }
                                                : undefined
                                        }
                                    >
                                        {
                                            singleImageBannerRes.data
                                                .themeOneSingleImageBannerSubTitle
                                        }
                                    </motion.p>

                                    {singleImageBannerRes.data
                                        .themeOneSingleImageBannerLink &&
                                        singleImageBannerRes.data
                                            .themeOneSingleImageBannerLink !=
                                            '' && (
                                            <motion.div
                                                variants={itemAnimation}
                                            >
                                                <Button
                                                    as="link"
                                                    to={
                                                        singleImageBannerRes
                                                            .data
                                                            .themeOneSingleImageBannerLink ||
                                                        '#'
                                                    }
                                                    variant="primary"
                                                    size="lg"
                                                    textColor={
                                                        singleImageBannerRes
                                                            .data
                                                            .themeOneSingleImageBannerButtonTextColor
                                                    }
                                                    backgroundColor={
                                                        singleImageBannerRes
                                                            .data
                                                            .themeOneSingleImageBannerButtonBackgroundColor
                                                    }
                                                >
                                                    {translate('VISIT SHOP')}
                                                </Button>
                                            </motion.div>
                                        )}
                                </div>

                                <motion.div variants={itemAnimation}>
                                    <img
                                        src={
                                            singleImageBannerRes.data
                                                .themeOneSingleImageBannerForegroundImage
                                        }
                                        alt=""
                                        className="max-w-[80%] xl:max-w-[592px] 3xl:max-w-[550px] mx-auto"
                                    />
                                </motion.div>
                            </div>
                        </motion.div>
                    </div>
                </section>
            ) : null}

            {flashSaleProductRes.data?.showHomeFlashSale === '1' &&
            flashSaleProductRes.data.flashSaleCampaign ? (
                <section id="flash-sale-products">
                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="container"
                    >
                        <SectionTitle
                            animated
                            title={translate('[On Sale] Products')}
                            subTitle={translate('Popular')}
                            countTo={
                                +flashSaleProductRes.data.flashSaleCampaign
                                    .endDate * 1000
                            }
                            text={
                                flashSaleProductRes.data.flashSaleCampaign.name
                            }
                            link={`/campaigns/${flashSaleProductRes.data.flashSaleCampaign.slug}`}
                        />

                        <ProductsSlider>
                            {flashSaleProductRes.data.flashSaleProducts.map(
                                (product) => (
                                    <SwiperSlide key={product.id}>
                                        <motion.div variants={itemAnimation}>
                                            <ProductCard product={product} />
                                        </motion.div>
                                    </SwiperSlide>
                                ),
                            )}
                        </ProductsSlider>
                    </motion.div>
                </section>
            ) : null}

            {fourImageBannerRes.data?.showThemeOneFourImageBanner ? (
                <section id="4-banner-section">
                    <motion.div
                        variants={{
                            whileInView: {
                                transition: {
                                    delay: 0.4,
                                    delayChildren: 0.3,
                                    staggerChildren: 0.2,
                                },
                            },
                        }}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="container grid grid-cols-2 md:grid-cols-4 gap-0"
                    >
                        <Link
                            to={
                                fourImageBannerRes.data
                                    ?.themeOneFourImageBannerImageOneLink
                            }
                        >
                            <motion.img
                                variants={{
                                    initial: { opacity: 0, scale: 0.8 },
                                    whileInView: {
                                        opacity: 1,
                                        scale: 1,
                                    },
                                }}
                                src={
                                    fourImageBannerRes.data
                                        ?.themeOneFourImageBannerImageOne
                                }
                                alt=""
                                className="w-full aspect-square"
                            />
                        </Link>
                        <Link
                            to={
                                fourImageBannerRes.data
                                    ?.themeOneFourImageBannerImageTwoLink
                            }
                        >
                            <motion.img
                                variants={{
                                    initial: { opacity: 0, scale: 0.8 },
                                    whileInView: {
                                        opacity: 1,
                                        scale: 1,
                                    },
                                }}
                                src={
                                    fourImageBannerRes.data
                                        ?.themeOneFourImageBannerImageTwo
                                }
                                alt=""
                                className="w-full aspect-square"
                            />
                        </Link>
                        <Link
                            to={
                                fourImageBannerRes.data
                                    ?.themeOneFourImageBannerImageThreeLink
                            }
                            className="col-span-2 row-span-2"
                        >
                            <motion.img
                                variants={{
                                    initial: { opacity: 0, scale: 0.8 },
                                    whileInView: {
                                        opacity: 1,
                                        scale: 1,
                                    },
                                }}
                                src={
                                    fourImageBannerRes.data
                                        ?.themeOneFourImageBannerImageThree
                                }
                                alt=""
                                className="w-full h-full"
                            />
                        </Link>
                        <Link
                            to={
                                fourImageBannerRes.data
                                    ?.themeOneFourImageBannerImageFourLink
                            }
                            className="col-span-2"
                        >
                            <motion.img
                                variants={{
                                    initial: { opacity: 0, scale: 0.8 },
                                    whileInView: {
                                        opacity: 1,
                                        scale: 1,
                                    },
                                }}
                                src={
                                    fourImageBannerRes.data
                                        ?.themeOneFourImageBannerImageFour
                                }
                                alt=""
                                className="w-full h-full"
                            />
                        </Link>
                    </motion.div>
                </section>
            ) : null}

            {/* start::category product sections */}
            {(categoryProductsRes.data?.showThemeOneCategoryProducts === '1' ||
                !categoryProductsRes.data?.showThemeOneCategoryProducts) &&
                categoryProductsRes.data?.map((item, idx) => (
                    <section
                        className=""
                        key={item.category.id}
                        id={`category-products--${item.category.name}`}
                    >
                        <motion.div
                            variants={containerAnimation}
                            initial="initial"
                            whileInView="whileInView"
                            // viewport={{ once: true }}
                            className="container"
                        >
                            <SectionTitle
                                animated
                                highlightFirstWord
                                title={item.category.name}
                                subTitle={translate('Popular')}
                                link={`/categories/${item.category.slug}`}
                            />
                            <div
                                className={cn(
                                    'flex max-lg:flex-col gap-5 mt-8',
                                    {
                                        'lg:flex-row-reverse': idx % 2 === 1,
                                    },
                                )}
                            >
                                {item.image && item.image != '' && (
                                    <div className="w-full grow lg:max-w-[530px] xl:max-w-[460px] 3xl:max-w-[631px] relative">
                                        <motion.img
                                            variants={{
                                                initial: {
                                                    opacity: 0,
                                                    scale: 0.9,
                                                },
                                                whileInView: {
                                                    opacity: 1,
                                                    scale: 1,
                                                    transition: {
                                                        duration: 0.5,
                                                    },
                                                },
                                            }}
                                            src={item.image}
                                            alt=""
                                            className="rounded-banner h-[calc(100%-34px)] w-full absolute"
                                        />
                                    </div>
                                )}

                                <ThemeSlider
                                    slidesPerView={1}
                                    containerClass="grow"
                                    breakpoints={{
                                        320: {
                                            slidesPerView: 2,
                                            spaceBetween: 16,
                                        },
                                        640: {
                                            slidesPerView: 2,
                                            spaceBetween: 16,
                                        },
                                        1280: {
                                            slidesPerView: 3,
                                            spaceBetween: 20,
                                        },
                                    }}
                                >
                                    {item.products.map((product) => (
                                        <SwiperSlide key={product.id}>
                                            <motion.div
                                                variants={itemAnimation}
                                            >
                                                <ProductCard
                                                    product={product}
                                                />
                                            </motion.div>
                                        </SwiperSlide>
                                    ))}
                                </ThemeSlider>
                            </div>
                        </motion.div>
                    </section>
                ))}
            {/* end::category product sections */}

            {bottomImageBannerRes.data?.showThemeOneBottomFourImageBanner ? (
                <section id="bottom-image-banner">
                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="container"
                    >
                        <SectionTitle
                            animated
                            highlightFirstWord
                            title={
                                bottomImageBannerRes.data
                                    .themeOneBottomFourImageBannerTitle || ''
                            }
                            subTitle={
                                bottomImageBannerRes.data
                                    .themeOneBottomFourImageBannerSubTitle || ''
                            }
                        />
                    </motion.div>
                    <motion.div
                        variants={containerAnimation}
                        initial="initial"
                        whileInView="whileInView"
                        viewport={{ once: true }}
                        className="container grid sm:grid-cols-2 xl:grid-cols-4 gap-6 mt-8"
                    >
                        <motion.div variants={itemAnimation}>
                            <TallBannerCard
                                image={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageOne
                                }
                                bg={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageOneBG
                                }
                                subTitle={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageOneSubTitle
                                }
                                title={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageOneTitle
                                }
                                description={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageOneDescription
                                }
                                link={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageOneLink
                                }
                            />
                        </motion.div>
                        <motion.div variants={itemAnimation}>
                            <TallBannerCard
                                image={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageTwo
                                }
                                bg={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageTwoBG
                                }
                                subTitle={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageTwoSubTitle
                                }
                                title={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageTwoTitle
                                }
                                description={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageTwoDescription
                                }
                                link={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageTwoLink
                                }
                            />
                        </motion.div>
                        <motion.div variants={itemAnimation}>
                            <TallBannerCard
                                image={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageThree
                                }
                                bg={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageThreeBG
                                }
                                subTitle={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageThreeSubTitle
                                }
                                title={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageThreeTitle
                                }
                                description={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageThreeDescription
                                }
                                link={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageThreeLink
                                }
                            />
                        </motion.div>
                        <motion.div variants={itemAnimation}>
                            <TallBannerCard
                                image={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageFour
                                }
                                bg={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageFourBG
                                }
                                subTitle={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageFourSubTitle
                                }
                                title={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageFourTitle
                                }
                                description={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageFourDescription
                                }
                                link={
                                    bottomImageBannerRes.data
                                        .themeOneBottomFourImageBannerImageFourLink
                                }
                            />
                        </motion.div>
                    </motion.div>
                </section>
            ) : null}

            {/* Dynamic Sections */}
            {dynamicSections.length > 0 && (
                <DynamicSectionRenderer
                    sections={dynamicSections}
                    theme="theme-one"
                />
            )}
        </div>
    );
};

export default HomeThemeOne;
