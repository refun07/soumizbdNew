import { motion } from 'framer-motion';
import { BsInstagram } from 'react-icons/bs';
import { Link } from 'react-router-dom';
import { Autoplay } from 'swiper/modules';
import { Swiper, SwiperSlide } from 'swiper/react';
import Button from '../../components/buttons/Button';
import ProductCard from '../../components/card/product-card';
import CategoryCard from '../../components/category-card';
import CategorySlider from '../../components/slider/CategorySlider';
import ProductsSlider from '../../components/slider/ProductsSlider';
import ThemeSlider from '../../components/slider/ThemeSlider';
import SocialLinks from '../../components/SocialLinks';
import DynamicSectionRenderer from '../../components/home/DynamicSectionRenderer';
import SectionTitle from '../../components/titles/SectionTitle';
import {
    useGetBannersQuery,
    useGetBottomTwoImageBannerQuery,
    useGetCategoryProductsQuery,
    useGetFeaturedCategoriesQuery,
    useGetFeaturedProductsQuery,
    useGetFlashSaleProductsQuery,
    useGetPopularCategoriesQuery,
    useGetTwoImageBannerQuery,
} from '../../store/features/api/homeApis/theme-three/homeApiThemeThree';
import { containerAnimation, itemAnimation } from '../../utils/animation';
import { translate } from '../../utils/translate';

const HomeThemeThree = () => {
    const { data: banners, isLoading: isBannerLoading } = useGetBannersQuery();
    const { data: featuredCategories, isLoading: isFeaturedCategoriesLoading } =
        useGetFeaturedCategoriesQuery();
    const { data: featuredProducts, isLoading: isFeaturedProductsLoading } =
        useGetFeaturedProductsQuery();
    const { data: popularCategories, isLoading: isPopularCategoriesLoading } =
        useGetPopularCategoriesQuery();
    const { data: twoImageBanner, isLoading: isTwoImageBannerLoading } =
        useGetTwoImageBannerQuery();
    const { data: flashSaleProducts, isLoading: isFlashSaleProductsLoading } =
        useGetFlashSaleProductsQuery();
    const { data: categoryProducts, isLoading: isCategoryProductsLoading } =
        useGetCategoryProductsQuery();
    const {
        data: bottomTwoImageBanner,
        isLoading: isBottomTwoImageBannerLoading,
    } = useGetBottomTwoImageBannerQuery();

    // Get dynamic sections from window.config
    const dynamicSections = (window as any).config?.dynamicSections || [];

    return (
        <div className="space-y-10 lg:space-y-16 pb-20">
            {/* start::banner */}
            {(banners?.showThemeThreeSliderSection === '1' || !banners?.showThemeThreeSliderSection) && (
            <section id="banner">
                <Swiper slidesPerView={1} spaceBetween={10}>
                    {banners?.map((item) => (
                        <SwiperSlide
                            key={item.title}
                            style={{
                                backgroundImage: `url(${item.image})`,
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                            }}
                        >
                            <div className="container">
                                <div className="h-[688px] flex items-center">
                                    <div className="max-w-[660px] max-md:text-center">
                                        <p className="ek-headline text-white mb-3.5">
                                            {item.headline}
                                        </p>
                                        <motion.h1
                                            initial={{
                                                opacity: 0,
                                                y: 20,
                                            }}
                                            whileInView={{
                                                opacity: 1,
                                                y: 0,
                                            }}
                                            viewport={{ once: true }}
                                            transition={{
                                                duration: 0.5,
                                                delay: 0.2,
                                            }}
                                            className="ek-h1 font-semibold text-white mb-3"
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
                                            viewport={{ once: true }}
                                            transition={{
                                                duration: 0.5,
                                                delay: 0.4,
                                            }}
                                            className="text-base text-white mb-12"
                                        >
                                            {item.title}
                                        </motion.p>

                                        <motion.div
                                            initial={{
                                                opacity: 0,
                                                y: 20,
                                            }}
                                            whileInView={{
                                                opacity: 1,
                                                y: 0,
                                            }}
                                            viewport={{ once: true }}
                                            transition={{
                                                duration: 0.5,
                                                delay: 0.5,
                                            }}
                                        >
                                            <Button
                                                as="link"
                                                to={item.url}
                                                variant="secondary"
                                                size="lg"
                                            >
                                                {translate('VISIT SHOP')}
                                            </Button>
                                        </motion.div>
                                    </div>
                                </div>
                            </div>
                        </SwiperSlide>
                    ))}
                </Swiper>
            </section>
            )}
            {/* end::banner */}

            {/* start::categories */}
            {featuredCategories?.showFeaturedCategories ? (
                <section id="categories" className="">
                    <div className="container">
                        <SectionTitle
                            title="[Featured] categories"
                            subTitle={
                                featuredCategories?.themeThreeFeaturedCategoriesHeadline
                            }
                            text={
                                featuredCategories?.themeThreeFeaturedCategoriesSubtitle
                            }
                            classNames={{
                                container: 'text-center',
                                titleContainer: 'justify-center',
                                title: '[&>span]:border-none',
                                text: 'max-w-[780px] mx-auto',
                            }}
                        />

                        <ThemeSlider
                            title="Popular Categories"
                            slidesPerView={5}
                            spaceBetween={30}
                            slidesPerGroup={5}
                            loop={true}
                            autoplay={{
                                pauseOnMouseEnter: true,
                                delay: 5000,
                            }}
                            breakpoints={{
                                0: {
                                    slidesPerView: 2,
                                    slidesPerGroup: 2,
                                },
                                640: {
                                    slidesPerView: 3,
                                    slidesPerGroup: 3,
                                },
                                768: {
                                    slidesPerView: 4,
                                    slidesPerGroup: 4,
                                },
                                1024: {
                                    slidesPerView: 5,
                                    slidesPerGroup: 5,
                                },
                                1280: {
                                    slidesPerView: 6,
                                    slidesPerGroup: 6,
                                },
                            }}
                        >
                            {featuredCategories?.categories.map((item) => (
                                <SwiperSlide key={item.id}>
                                    <CategoryCard
                                        image={item.thumbnailImage}
                                        title={item.name}
                                        to={`/categories/${item.slug}`}
                                    />
                                </SwiperSlide>
                            ))}
                        </ThemeSlider>
                    </div>
                </section>
            ) : null}

            {featuredProducts?.showthemeThreeFeaturedProducts ? (
                <section className="">
                    <div className="container">
                        <SectionTitle
                            title="[Featured] products"
                            subTitle="Handpicked"
                            link={featuredProducts?.viewMoreUrl}
                        />

                        <ProductsSlider>
                            {featuredProducts?.products.map((item) => (
                                <SwiperSlide key={item.id}>
                                    <ProductCard product={item} />
                                </SwiperSlide>
                            ))}
                        </ProductsSlider>
                    </div>
                </section>
            ) : null}

            {popularCategories?.showPopularCategories ? (
                <section className="bg-gradient-to-r from-primary/10 to-transparent from-[50%] to-[50%] py-[84px] overflow-x-hidden">
                    <div className="container container-fluid-r flex items-center gap-10">
                        <SectionTitle
                            title="[Popular] categories"
                            subTitle={
                                popularCategories.themeThreePopularCategoriesHeadline
                            }
                            text={
                                popularCategories.themeThreePopularCategoriesSubtitle
                            }
                            classNames={{
                                container: 'max-w-[500px]',
                            }}
                        />

                        <ThemeSlider
                            title="Popular Categories"
                            slidesPerView={'auto'}
                            spaceBetween={12}
                            loop={true}
                            modules={[Autoplay]}
                            autoplay={{
                                pauseOnMouseEnter: true,
                                delay: 5000,
                            }}
                        >
                            {popularCategories?.categories.map((item) => (
                                <SwiperSlide key={item.id} className="!w-auto">
                                    <Link
                                        to={`/categories/${item.slug}`}
                                        className="block relative md:w-[400px] aspect-[8/11]"
                                    >
                                        <div className="absolute inset-0 z-[1] bg-gradient-to-t from-black/50 to-transparent to-[20%]"></div>
                                        <img
                                            src={item.thumbnailImage}
                                            alt={item.name}
                                            className="w-full h-full object-cover"
                                        />

                                        <div className="absolute bottom-5 left-10 z-[1] text-white">
                                            {item.name}
                                        </div>
                                    </Link>
                                </SwiperSlide>
                            ))}
                        </ThemeSlider>
                    </div>
                </section>
            ) : null}

            {twoImageBanner?.showThemeThreeTwoImageBanner ? (
                <section className="">
                    <div className="container">
                        <div className="grid grid-cols-2 text-center">
                            <div className="">
                                <Link
                                    to={
                                        twoImageBanner.themeThreeTwoImageBannerImageOneLink
                                    }
                                >
                                    <img
                                        src={
                                            twoImageBanner.themeThreeTwoImageBannerImageOne
                                        }
                                        alt={
                                            twoImageBanner.themeThreeTwoImageBannerImageOneTitle
                                        }
                                    />
                                </Link>

                                <div className="max-w-[540px] mx-auto">
                                    <h4 className="ek-h4 font-semibold mt-9">
                                        {
                                            twoImageBanner.themeThreeTwoImageBannerImageOneTitle
                                        }
                                    </h4>
                                    <p className="text-base text-black/70 mt-3">
                                        {
                                            twoImageBanner.themeThreeTwoImageBannerImageOneSubtitle
                                        }
                                    </p>
                                </div>
                            </div>

                            <div className="">
                                <Link
                                    to={
                                        twoImageBanner.themeThreeTwoImageBannerImageTwoLink
                                    }
                                >
                                    <img
                                        src={
                                            twoImageBanner.themeThreeTwoImageBannerImageTwo
                                        }
                                        alt={
                                            twoImageBanner.themeThreeTwoImageBannerImageTwoTitle
                                        }
                                    />
                                </Link>

                                <div className="max-w-[540px] mx-auto">
                                    <h4 className="ek-h4 font-semibold mt-9">
                                        {
                                            twoImageBanner.themeThreeTwoImageBannerImageTwoTitle
                                        }
                                    </h4>
                                    <p className="text-base text-black/70 mt-3">
                                        {
                                            twoImageBanner.themeThreeTwoImageBannerImageTwoSubtitle
                                        }
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            ) : null}

            {flashSaleProducts?.showHomeFlashSale ? (
                <section
                    className="py-[120px] bg-cover bg-center"
                    style={{
                        backgroundImage: `url(${flashSaleProducts.flashSaleCampaign.banner})`,
                    }}
                >
                    <div className="container">
                        <div className="max-w-[795px] bg-white px-20 py-16">
                            <SectionTitle
                                subTitle="Limited time"
                                title="[Flash] Sale"
                                countTo={
                                    new Date(
                                        Number(
                                            flashSaleProducts.flashSaleCampaign
                                                .endDate,
                                        ) * 1000,
                                    )
                                }
                                classNames={{
                                    titleContainer: 'justify-between',
                                }}
                            />
                            <h4 className="ek-h4 font-medium mt-5">
                                {flashSaleProducts.flashSaleCampaign.name}
                            </h4>
                            <p className="mt-3">
                                {
                                    flashSaleProducts.flashSaleCampaign
                                        .shortDescription
                                }
                            </p>

                            <Button
                                variant="secondary"
                                size="lg"
                                className="mt-8"
                            >
                                {translate('View Products')}
                            </Button>
                        </div>
                    </div>
                </section>
            ) : null}

            {/* start::category product sections */}
            {(categoryProducts?.showThemeThreeCategoryProducts === '1' || !categoryProducts?.showThemeThreeCategoryProducts) && categoryProducts?.map((item, idx) => (
                <section
                    className=""
                    key={item.category.id}
                    id={`category-products--${item.category.name}`}
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
                            highlightFirstWord
                            title={item.category.name}
                            subTitle={translate('Handpicked')}
                            link={`/categories/${item.category.slug}`}
                        />
                        <ProductsSlider>
                            {item.products.map((product) => (
                                <SwiperSlide key={product.id}>
                                    <motion.div variants={itemAnimation}>
                                        <ProductCard product={product} />
                                    </motion.div>
                                </SwiperSlide>
                            ))}
                        </ProductsSlider>
                    </motion.div>
                </section>
            ))}
            {/* end::category product sections */}

            {bottomTwoImageBanner?.showThemeThreeBottomTwoImageBanner ? (
                <section className="mt-24">
                    <div className="container">
                        <div className="grid grid-cols-2">
                            <Link
                                to={
                                    bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageOneLink
                                }
                                className="bg-cover bg-right aspect-video flex items-center px-16"
                                style={{
                                    backgroundImage: `url(${bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageOne})`,
                                }}
                            >
                                <div className="max-w-[420px]">
                                    <p className="ek-headline text-white">
                                        {
                                            bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageOneHeadline
                                        }
                                    </p>
                                    <h4 className="ek-h2-sm leading-[1.2] mt-2.5 mb-4 text-white">
                                        {
                                            bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageOneTitle
                                        }
                                    </h4>
                                    <p className="text-base text-white/70 mt-3">
                                        {
                                            bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageOneSubtitle
                                        }
                                    </p>
                                </div>
                            </Link>

                            <Link
                                to={
                                    bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageTwoLink
                                }
                                className="bg-cover bg-left aspect-video flex items-center px-16"
                                style={{
                                    backgroundImage: `url(${bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageTwo})`,
                                }}
                            >
                                <div className="max-w-[420px]">
                                    <p className="ek-headline text-white">
                                        {
                                            bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageTwoHeadline
                                        }
                                    </p>
                                    <h4 className="ek-h2-sm leading-[1.2] mt-2.5 mb-4 text-white">
                                        {
                                            bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageTwoTitle
                                        }
                                    </h4>
                                    <p className="text-base text-white/70 mt-3">
                                        {
                                            bottomTwoImageBanner.themeThreeBottomTwoImageBannerImageTwoSubtitle
                                        }
                                    </p>
                                </div>
                            </Link>
                        </div>
                    </div>
                </section>
            ) : null}

            <section className="">
                <div className="container">
                    <SectionTitle
                        subTitle="Get connected"
                        title="[Follow] Our Socials"
                        text={translate(
                            'We have prepared special discounts for you on organic have prepared special discounts for you on organic breakfast We have prepared special discounts for you on organic breakfast',
                        )}
                        classNames={{
                            container: 'text-center',
                            titleContainer: 'justify-center',
                            title: '[&>span]:border-none',
                            text: 'max-w-[780px] mx-auto',
                        }}
                    />
                    <SocialLinks
                        links={{
                            facebook: 'https://lksdjf.com',
                            twitter: 'https://lksdjf.com',
                            instagram: 'https://lksdjf.com',
                            youtube: 'https://lksdjf.com',
                            linkedin: 'https://lksdjf.com',
                        }}
                        classNames={{
                            container: 'justify-center mt-5 mb-12',
                        }}
                    />

                    <CategorySlider>
                        {[1, 2, 3, 4, 5, 6, 7].map((item) => (
                            <SwiperSlide>
                                <Link
                                    to={''}
                                    key={item}
                                    className="group relative aspect-square"
                                >
                                    <img
                                        src="https://placehold.co/300"
                                        alt=""
                                        className="aspect-square w-full"
                                    />
                                    <div className="absolute inset-1.5 flex items-center justify-center bg-black/80 text-white text-2xl scale-0 opacity-0 group-hover:scale-100 group-hover:opacity-100 transition-all duration-300">
                                        <BsInstagram />
                                    </div>
                                </Link>
                            </SwiperSlide>
                        ))}
                    </CategorySlider>
                </div>
            </section>

            {/* Dynamic Sections */}
            {dynamicSections.length > 0 && (
                <DynamicSectionRenderer
                    sections={dynamicSections}
                    theme="theme-three"
                />
            )}
        </div>
    );
};

export default HomeThemeThree;
