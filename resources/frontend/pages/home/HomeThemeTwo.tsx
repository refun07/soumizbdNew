import { BsDashLg, BsPlusLg } from 'react-icons/bs';
import { FaHeart, FaRegHeart } from 'react-icons/fa';
import { LuEye } from 'react-icons/lu';
import { Link } from 'react-router-dom';
import { useProductActions } from '../../components/card/product-card';
import DynamicSectionRenderer from '../../components/home/DynamicSectionRenderer';
import ThemeRating from '../../components/reviews/ThemeRating';
import { useGetFlashSaleProductsQuery } from '../../store/features/api/homeApis/theme-one/homeApiThemeOne';
import {
    useGetBannerQuery,
    useGetCategoryProductsQuery,
    useGetFeaturedCategoriesQuery,
    useGetFourImageBannerQuery,
    useGetThreeImageBannerQuery,
    useGetTrendyProductsQuery,
} from '../../store/features/api/homeApis/theme-two/homeApiThemeTwo';
import { ICategoryShort } from '../../types';
import { IProductShort } from '../../types/product';
import { cn } from '../../utils/cn';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

const ASSETS = {
    hero: 'https://www.figma.com/api/mcp/asset/7e33b5c8-4ac4-4605-8917-8c60eb8edad5',
    sideHero: 'https://www.figma.com/api/mcp/asset/b13a7725-a174-4f36-a8bc-0586f715565c',
    brandBanner: 'https://www.figma.com/api/mcp/asset/0b9d9889-7ae8-4e47-b00f-e557971ab5e0',
    promoOne: 'https://www.figma.com/api/mcp/asset/8c401f17-f835-4f43-8a6c-8a1e34d7db93',
    promoTwo: 'https://www.figma.com/api/mcp/asset/9ebdf04c-f4a6-4eab-8c4d-b1cf9ee790c4',
    promoThree: 'https://www.figma.com/api/mcp/asset/8f20e3a8-ae95-4fd1-ba09-8112223a2fa5',
    promoFour: 'https://www.figma.com/api/mcp/asset/45035140-5a06-4b1c-b511-1181071df283',
    categoryFallbacks: [
        'https://www.figma.com/api/mcp/asset/0b5a1752-e0ef-4590-aa98-92dc3b858b68',
        'https://www.figma.com/api/mcp/asset/125f2f06-7495-49a4-b4a8-864417f80a87',
        'https://www.figma.com/api/mcp/asset/fff0285b-c857-48bc-994d-acfaa5c38b3e',
        'https://www.figma.com/api/mcp/asset/a13fb685-bf50-4265-b77d-c298626d5e07',
        'https://www.figma.com/api/mcp/asset/a1d84469-d28c-4fad-ad74-f718c6308b1b',
        'https://www.figma.com/api/mcp/asset/164b48d1-2335-4285-8313-3922f858b14b',
    ],
};

type ProductSectionProps = {
    products: IProductShort[];
    title: string;
    subtitle?: string;
    variant?: 'default' | 'sale';
    link?: string;
    limit?: number;
};

const SectionHeading = ({
    subtitle = 'Handpicked',
    title,
    inverse = false,
    link,
}: {
    subtitle?: string;
    title: string;
    inverse?: boolean;
    link?: string;
}) => {
    const [firstWord, ...rest] = title.split(' ');

    return (
        <div className="mb-7 flex items-end justify-between gap-5">
            <div className="min-w-0 grow">
                <p
                    className={cn(
                        'mb-2 text-xs uppercase tracking-[7px]',
                        inverse ? 'text-white/70' : 'text-black/40',
                    )}
                >
                    {translate(subtitle)}
                </p>
                <div className="flex items-center gap-5">
                    <h2
                        className={cn(
                            'shrink-0 text-[26px] font-light leading-tight md:text-[34px]',
                            inverse ? 'text-white' : 'text-black',
                        )}
                    >
                        <span
                            className={cn(
                                'font-bold',
                                inverse ? 'text-white' : 'text-[#b3052a]',
                            )}
                        >
                            {firstWord}
                        </span>{' '}
                        {rest.join(' ')}
                    </h2>
                    <span
                        className={cn(
                            'hidden h-px grow sm:block',
                            inverse ? 'bg-white/25' : 'bg-black/10',
                        )}
                    />
                </div>
            </div>
            {link ? (
                <Link
                    to={link}
                    className={cn(
                        'hidden h-[42px] shrink-0 items-center rounded-[3px] border px-5 text-sm font-medium md:flex',
                        inverse
                            ? 'border-white/60 text-white hover:bg-white hover:text-[#b3052a]'
                            : 'border-black/35 text-[#b3052a] hover:border-[#b3052a]',
                    )}
                >
                    {translate('View More')}
                </Link>
            ) : null}
        </div>
    );
};

const RaisaProductCard = ({ product }: { product: IProductShort }) => {
    const {
        cartProduct,
        loading,
        handleAddToCart,
        handleCartQuantityChange,
        handleProductPreview,
        handleAddToWishList,
    } = useProductActions(product);
    const priceChanged = product.basePrice !== product.discountedBasePrice;
    const categoryLabel =
        product.tags?.[0]?.name || product.shop?.name || translate('Womens Care');
    const cartDisabled =
        loading.gettingProduct ||
        loading.addingToCart ||
        (!product.isPreOrder &&
            !product.hasAvailableStock &&
            !window.config.generalSettings.allowBackorder);

    return (
        <article className="group relative flex h-full flex-col overflow-hidden rounded-[5px] border border-transparent bg-white transition duration-300 hover:border-[#bc2444] hover:shadow-[0_14px_35px_rgba(179,5,42,0.14)]">
            <div className="relative aspect-square overflow-hidden bg-white">
                <Link
                    to={`/products/${product.slug}`}
                    className="flex size-full items-center justify-center bg-[#f7f7f7]"
                >
                    <img
                        src={product.thumbnailImg}
                        alt={product.name}
                        className="size-full object-cover transition duration-300 group-hover:scale-105"
                    />
                </Link>
                <div className="absolute right-5 top-5 flex translate-x-5 flex-col gap-4 opacity-0 transition duration-300 group-hover:translate-x-0 group-hover:opacity-100">
                    <button
                        className="flex size-[46px] items-center justify-center rounded-full bg-white text-[18px] text-[#ff2718] shadow-[0_8px_18px_rgba(0,0,0,0.12)] transition hover:bg-[#ff2718] hover:text-white"
                        onClick={handleAddToWishList}
                        aria-label={translate('Add to wishlist')}
                    >
                        {product.inWishlist ? <FaHeart /> : <FaRegHeart />}
                    </button>
                    <button
                        className="flex size-[46px] items-center justify-center rounded-full bg-white text-[22px] text-[#bc2444] shadow-[0_8px_18px_rgba(0,0,0,0.12)] transition hover:bg-[#bc2444] hover:text-white"
                        onClick={handleProductPreview}
                        aria-label={translate('Preview product')}
                    >
                        <LuEye />
                    </button>
                </div>
                {cartProduct ? (
                    <div className="absolute bottom-5 left-5 right-5 flex h-[38px] translate-y-8 items-center overflow-hidden rounded-full bg-white px-5 text-[#bc2444] opacity-0 shadow-[0_8px_18px_rgba(0,0,0,0.12)] transition duration-300 group-hover:translate-y-0 group-hover:opacity-100">
                        <button
                            className="flex h-full w-8 items-center justify-start text-[18px]"
                            onClick={() => handleCartQuantityChange('decrease')}
                            aria-label={translate('Decrease quantity')}
                        >
                            <BsDashLg />
                        </button>
                        <span className="grow text-center text-[12px] font-semibold text-[#111111]">
                            {String(cartProduct.qty).padStart(2, '0')}{' '}
                            {translate('Items')}
                        </span>
                        <button
                            className="flex h-full w-8 items-center justify-end text-[18px]"
                            onClick={() => handleCartQuantityChange('increase')}
                            aria-label={translate('Increase quantity')}
                        >
                            <BsPlusLg />
                        </button>
                    </div>
                ) : (
                    <button
                        className="absolute bottom-5 right-5 flex size-[46px] translate-y-8 items-center justify-center rounded-full bg-white text-[26px] text-[#bc2444] opacity-0 shadow-[0_8px_18px_rgba(0,0,0,0.12)] transition duration-300 hover:bg-[#bc2444] hover:text-white group-hover:translate-y-0 group-hover:opacity-100 disabled:opacity-60"
                        onClick={handleAddToCart}
                        disabled={cartDisabled}
                        aria-label={translate('Add to Cart')}
                    >
                        <BsPlusLg />
                    </button>
                )}
            </div>
            <div className="flex grow flex-col px-8 pb-7 pt-6">
                <p className="line-clamp-1 text-[15px] font-medium text-[#667085]">
                    {categoryLabel}
                </p>
                <h3 className="mt-1 min-h-[24px] text-[15px] font-semibold leading-[1.45] text-[#111111] transition group-hover:text-[#bc2444]">
                    <Link to={`/products/${product.slug}`} className="line-clamp-2">
                        {product.name}
                    </Link>
                </h3>
                <div className="mt-4 flex items-center gap-2">
                    <span className="text-[20px] font-semibold text-[#bc1734]">
                        {currencyFormatter(product.discountedBasePrice)}
                    </span>
                    {priceChanged ? (
                        <span className="text-[16px] font-semibold text-[#575757] line-through">
                            {currencyFormatter(product.basePrice)}
                        </span>
                    ) : null}
                </div>
                <ThemeRating
                    rating={product.rating.average}
                    totalReviews={product.rating.total}
                    className="mt-3 text-[13px]"
                    ratingClasses="max-w-[92px]"
                    style={{ maxWidth: 92 }}
                />
            </div>
        </article>
    );
};

const ProductSection = ({
    products,
    title,
    subtitle,
    variant = 'default',
    link,
    limit,
}: ProductSectionProps) => {
    const visibleProducts = products.slice(0, limit || products.length);
    if (!visibleProducts.length) return null;

    return (
        <section
            className={cn(
                variant === 'sale'
                    ? 'bg-[#b3052a] py-8 text-white md:py-11'
                    : 'py-2',
            )}
        >
            <div className="container">
                <SectionHeading
                    title={title}
                    subtitle={subtitle}
                    inverse={variant === 'sale'}
                    link={link}
                />
                <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                    {visibleProducts.map((product) => (
                        <RaisaProductCard product={product} key={product.id} />
                    ))}
                </div>
            </div>
        </section>
    );
};

const DealCard = ({
    image,
    title,
    subtitle,
}: {
    image: string;
    title: string;
    subtitle: string;
}) => (
    <div className="flex min-h-[112px] items-center gap-5 rounded-[5px] bg-white px-5 shadow-sm">
        <img src={image} alt="" className="size-[72px] object-contain" />
        <div>
            <p className="text-[18px] font-bold leading-tight text-[#b3052a]">
                {title}
            </p>
            <p className="text-[18px] font-light leading-tight text-black">
                {subtitle}
            </p>
        </div>
    </div>
);

const CategoryTile = ({
    category,
    image,
}: {
    category: ICategoryShort;
    image: string;
}) => (
    <Link
        to={`/categories/${category.slug}`}
        className="group flex min-h-[190px] flex-col items-center justify-center rounded-[5px] bg-white p-5 text-center transition hover:bg-[#f8eeee]"
    >
        <img
            src={category.thumbnailImage || image}
            alt={category.name}
            className="mb-4 size-[95px] object-contain transition group-hover:scale-105"
        />
        <h3 className="line-clamp-1 text-[15px] text-[#030712]">
            {category.name}
        </h3>
        <p className="mt-2 text-sm text-black/70">
            <span className="text-[#b3052a]">
                {String(category.productsCount || 0).padStart(2, '0')}
            </span>{' '}
            {translate('Items')}
        </p>
    </Link>
);

const HomeThemeTwo = () => {
    const { data: banner } = useGetBannerQuery();
    const { data: featuredCategories } = useGetFeaturedCategoriesQuery();
    const { data: categoryProducts } = useGetCategoryProductsQuery();
    const { data: threeImageBanner } = useGetThreeImageBannerQuery();
    const { data: flashSaleProduct } = useGetFlashSaleProductsQuery();
    const { data: fourImageBanner } = useGetFourImageBannerQuery();
    const { data: trendyProducts } = useGetTrendyProductsQuery();

    const dynamicSections = (window as any).config?.dynamicSections || [];
    const categoryProductGroups = categoryProducts || [];
    const firstCategoryProducts = categoryProductGroups[0]?.products || [];
    const allCategoryProducts = categoryProductGroups.flatMap(
        (item) => item.products,
    );
    const saleProducts = flashSaleProduct?.flashSaleProducts?.length
        ? flashSaleProduct.flashSaleProducts
        : firstCategoryProducts;
    const featuredProducts = trendyProducts?.products?.length
        ? trendyProducts.products
        : allCategoryProducts;

    return (
        <div className="bg-[#fafafa] pb-16">
            <section className="container pt-4">
                <div className="grid gap-4 xl:grid-cols-[2fr_1fr]">
                    <Link
                        to={banner?.themeTwoBannerOneLink || '/products'}
                        className="relative block min-h-[300px] overflow-hidden rounded-[10px] bg-[#220f10] md:min-h-[440px] xl:min-h-[520px]"
                    >
                        <img
                            src={banner?.themeTwoBannerImageOne || ASSETS.hero}
                            alt=""
                            className="absolute inset-0 size-full object-cover"
                        />
                    </Link>

                    <Link
                        to={banner?.themeTwoBannerTwoLink || '/products'}
                        className="relative block min-h-[300px] overflow-hidden rounded-[10px] bg-white md:min-h-[440px] xl:min-h-[520px]"
                    >
                        <img
                            src={banner?.themeTwoBannerImageTwo || ASSETS.sideHero}
                            alt=""
                            className="absolute inset-0 size-full object-cover"
                        />
                    </Link>
                </div>
            </section>

            <section className="container py-8">
                <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <DealCard
                        image={ASSETS.categoryFallbacks[0]}
                        title="Kurbani"
                        subtitle="Special Deals"
                    />
                    <DealCard
                        image={ASSETS.categoryFallbacks[1]}
                        title="Summer"
                        subtitle="Fest Carnival"
                    />
                    <DealCard
                        image={ASSETS.categoryFallbacks[2]}
                        title="Flash Deals"
                        subtitle="Limited"
                    />
                    <DealCard
                        image={ASSETS.categoryFallbacks[3]}
                        title="Monsoon"
                        subtitle="Fest"
                    />
                </div>
            </section>

            <ProductSection
                products={saleProducts}
                title="On Sale Products"
                subtitle="Popular"
                variant="sale"
                link={
                    flashSaleProduct?.flashSaleCampaign
                        ? `/campaigns/${flashSaleProduct.flashSaleCampaign.slug}`
                        : '/products'
                }
                limit={5}
            />

            {featuredCategories?.categories?.length ? (
                <section className="container py-10">
                    <SectionHeading
                        title="Featured Categories"
                        subtitle="Handpicked"
                        link="/categories"
                    />
                    <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 xl:grid-cols-7">
                        {featuredCategories.categories
                            .slice(0, 7)
                            .map((category, index) => (
                                <CategoryTile
                                    key={category.id}
                                    category={category}
                                    image={
                                        ASSETS.categoryFallbacks[
                                            index % ASSETS.categoryFallbacks.length
                                        ]
                                    }
                                />
                            ))}
                    </div>
                </section>
            ) : null}

            <ProductSection
                products={featuredProducts}
                title="Featured Products"
                subtitle="Popular"
                link="/products"
                limit={5}
            />

            <section className="container py-10">
                <Link
                    to={threeImageBanner?.themeTwoThreeBannerOneLink || '/products'}
                    className="block overflow-hidden rounded-[5px] bg-white"
                >
                    <img
                        src={
                            threeImageBanner?.themeTwoThreeBannerImageOne ||
                            ASSETS.brandBanner
                        }
                        alt=""
                        className="aspect-[1260/430] w-full object-cover"
                    />
                </Link>
            </section>

            <section className="container pb-10">
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {[
                        fourImageBanner?.themeTwoFourBannerImageOne || ASSETS.promoOne,
                        fourImageBanner?.themeTwoFourBannerImageTwo || ASSETS.promoTwo,
                        fourImageBanner?.themeTwoFourBannerImageThree ||
                            ASSETS.promoThree,
                        fourImageBanner?.themeTwoFourBannerImageFour ||
                            ASSETS.promoFour,
                    ].map((image, index) => (
                        <Link
                            to="/products"
                            className="group relative block overflow-hidden rounded-[8px] bg-[#9c0820]"
                            key={image}
                        >
                            <img
                                src={image}
                                alt=""
                                className="aspect-[1.34/1] w-full object-cover transition duration-300 group-hover:scale-105"
                            />
                            <span className="absolute inset-x-4 bottom-5 block text-center text-[18px] font-semibold text-white drop-shadow-sm">
                                {
                                    [
                                        'Deals of The Week',
                                        'Combo Offers',
                                        'Month Deals',
                                        'Buy One Get One',
                                    ][index]
                                }
                            </span>
                        </Link>
                    ))}
                </div>
            </section>

            <ProductSection
                products={allCategoryProducts}
                title="On Sale Products"
                subtitle="Popular"
                link="/products"
                limit={20}
            />

            {dynamicSections.length > 0 && (
                <DynamicSectionRenderer
                    sections={dynamicSections}
                    theme="theme-two"
                />
            )}
        </div>
    );
};

export default HomeThemeTwo;
