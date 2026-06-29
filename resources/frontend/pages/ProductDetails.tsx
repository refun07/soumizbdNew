import { useEffect, useState } from 'react';
import { FaCog, FaCoins, FaHome, FaPiggyBank, FaTruck } from 'react-icons/fa';
import { useDispatch } from 'react-redux';
import { Link, useParams, useSearchParams } from 'react-router-dom';
import { SwiperSlide } from 'swiper/react';
import Badge from '../components/Badge';
import Breadcrumb from '../components/Breadcrumb';
import Button from '../components/buttons/Button';
import ProductCard from '../components/card/product-card';
import ProductCardSmall from '../components/card/ProductCardSmall';
import Image from '../components/Image';
import Pagination from '../components/pagination/Pagination';
import ProductSizeChartTable from '../components/product/ProductSizeChartTable';
import ProductPreviewContent from '../components/popups/product/ProductPreviewContent';
import ReviewItem from '../components/reviews/ReviewItem';
import ThemeRating from '../components/reviews/ThemeRating';
import {
    ProductPreviewSkeleton,
    ProductPreviewSkeletonMobile,
} from '../components/skeletons/from-svg';
import ProductsSlider from '../components/slider/ProductsSlider';
import SectionTitle from '../components/titles/SectionTitle';
import { useGetProductDetailsQuery } from '../store/features/api/productApi';
import { useLazyGetAllProductReviewsQuery } from '../store/features/api/reviewApi';
import { togglePopup } from '../store/features/popup/popupSlice';
import { IBreadcrumbNavigation } from '../types';
import { numberFormatter, paddedNumber } from '../utils/numberFormatter';
import { translate } from '../utils/translate';
import NotFound from './NotFound';

const ProductDetails = () => {
    const dispatch = useDispatch();

    const [activeTab, setActiveTab] = useState<'description' | 'reviews'>(
        'description',
    );
    const [searchParams] = useSearchParams();
    const [starPercentage, setStarPercentage] = useState([0, 0, 0, 0, 0]);
    const { slug } = useParams<{ slug: string }>();
    const { data: product, isLoading } = useGetProductDetailsQuery(slug!);
    const [getProductReviews, { data: reviewsRes }] =
        useLazyGetAllProductReviewsQuery();

    const reviews = reviewsRes?.reviews.data;
    const reviewPagination = reviewsRes?.reviews.meta;
    const summary = reviewsRes?.summary;

    useEffect(() => {
        if (!product) return;
        getProductReviews({
            productId: product.id,
            page: searchParams.get('page') || 1,
            limit: searchParams.get('limit') || 10,
        });

    }, [product, searchParams]);

    useEffect(() => {
        if (!summary) return;

        const starPercentage = [
            summary.fiveStarsCount,
            summary.fourStarsCount,
            summary.threeStarsCount,
            summary.twoStarsCount,
            summary.oneStarsCount,
        ].map((count) => {
            return (count! / summary.total) * 100 || 0;
        });

        setStarPercentage(starPercentage);
    }, [summary]);

    if (isLoading) {
        return (
            <section className="mt-5">
                <div className="container">
                    <div className="grid lg:grid-cols-4">
                        <div className="lg:col-span-3">
                            <ProductPreviewSkeleton className="mx-auto max-md:hidden" />
                            <ProductPreviewSkeletonMobile className="md:hidden w-full" />
                        </div>
                    </div>
                </div>
            </section>
        );
    }

    if (!product) {
        return <NotFound />;
    }

    const breadCrumbNavigationArray: IBreadcrumbNavigation[] = [
        {
            icon: <FaHome />,
            name: translate('home'),
            link: '/',
        },
        {
            name: translate('Products'),
            link: '/products',
        },
    ];

    if (product.rootCategory) {
        breadCrumbNavigationArray.push({
            name: product.rootCategory?.name,
            link: `/categories/${product.rootCategory?.slug}`,
        });
    }
    breadCrumbNavigationArray.push({
        name: product.name,
    });

    return (
        <>
            <Breadcrumb
                className="max-sm:hidden"
                navigation={breadCrumbNavigationArray}
            />

            <section className="mt-5">
                <div className="container">
                    <div className="lg:grid lg:grid-cols-4">
                        <div className="lg:col-span-3">
                            <ProductPreviewContent slug={product.slug} />

                            <div className="flex gap-9 border-b mt-10 mb-5 text-[20px] font-medium">
                                <button
                                    className={`pb-3.5 border-b-4 ${
                                        activeTab === 'description'
                                            ? 'text-primary border-secondary'
                                            : 'text-black/70 border-transparent'
                                    }`}
                                    onClick={() => setActiveTab('description')}
                                >
                                    {translate('Description')}
                                </button>
                                <button
                                    className={`pb-3.5 border-b-4 ${
                                        activeTab === 'reviews'
                                            ? 'text-primary border-secondary'
                                            : 'text-black/70 border-transparent'
                                    }`}
                                    onClick={() => setActiveTab('reviews')}
                                >
                                    {translate('Reviews')}
                                </button>
                            </div>

                            {activeTab === 'description' ? (
                                <div>
                                    <div
                                        className="max-h-[1000px] overflow-y-auto overflow-x-hidden product-description"
                                        dangerouslySetInnerHTML={{
                                            __html: product.description,
                                        }}
                                    />

                                    {product.hasSizeChart &&
                                    (product.sizeChartTable ||
                                        product.sizeChartImage) ? (
                                        <div className="mt-8">
                                            <h4 className="text-lg font-medium mb-3">
                                                {translate('Size Chart')}
                                            </h4>
                                            {product.sizeChartTable ? (
                                                <ProductSizeChartTable
                                                    chart={
                                                        product.sizeChartTable
                                                    }
                                                    className="max-w-3xl"
                                                />
                                            ) : null}

                                            {product.sizeChartImage ? (
                                                <Image
                                                    src={product.sizeChartImage}
                                                    alt={translate(
                                                        'Size Chart',
                                                    )}
                                                    className={`w-full max-w-3xl rounded border border-black/10 ${
                                                        product.sizeChartTable
                                                            ? 'mt-6'
                                                            : ''
                                                    }`}
                                                />
                                            ) : null}
                                        </div>
                                    ) : null}
                                </div>
                            ) : null}
                            {activeTab === 'reviews' ? (
                                <div className="pt-10">
                                    <div className="flex max-sm:flex-col gap-8 sm:gap-[64px]">
                                        <div className="pr-12 border-r border-primary-14">
                                            <h4 className="text-sm mb-4 sm:mb-9 uppercase">
                                                {translate('Summary')}
                                            </h4>

                                            <div className="flex gap-6">
                                                <div>
                                                    <h4 className="text-3xl mb-1.5">
                                                        {summary?.average.toFixed(
                                                            2,
                                                        )}
                                                    </h4>
                                                    <ThemeRating
                                                        readOnly
                                                        rating={
                                                            summary?.average!
                                                        }
                                                    />
                                                </div>

                                                <div>
                                                    <h4 className="text-3xl mb-1.5">
                                                        {paddedNumber(
                                                            summary?.total,
                                                        )}
                                                    </h4>
                                                    <p className="ek-h4 text-black/70">
                                                        {translate(
                                                            'Total Reviews',
                                                        )}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div className="grow space-y-2.5 whitespace-nowrap">
                                            {starPercentage.map(
                                                (percentage, index) => (
                                                    <div
                                                        className="flex items-center gap-2 max-w-[530px]"
                                                        key={index}
                                                    >
                                                        <span className="w-10 text-black/70">
                                                            {5 - index}{' '}
                                                            {translate('Star')}
                                                        </span>
                                                        <span className="grow inline-flex bg-black/5 rounded-sm overflow-hidden h-2">
                                                            <span
                                                                className="bg-theme-yellow h-2 inline-block"
                                                                style={{
                                                                    width: `${percentage}%`,
                                                                }}
                                                            ></span>
                                                        </span>
                                                        <span className="w-10 text-black/70">
                                                            {numberFormatter(
                                                                percentage,
                                                            )}
                                                            %
                                                        </span>
                                                    </div>
                                                ),
                                            )}
                                        </div>
                                    </div>

                                    <hr className="mt-9 mb-7" />

                                    {reviews?.length ? (
                                        <div>
                                            <h4 className="text-sm mb-7 uppercase">
                                                {translate('Reviews')}
                                            </h4>

                                            {reviews?.map((review) => (
                                                <div key={review.id}>
                                                    <ReviewItem
                                                        review={review}
                                                    />
                                                    <hr className="mt-4 mb-8" />
                                                </div>
                                            ))}

                                            <Pagination
                                                pagination={reviewPagination}
                                            />
                                        </div>
                                    ) : null}
                                </div>
                            ) : null}
                        </div>
                        <div>
                            {window.config.generalSettings.appMode ===
                                'multiVendor' && (
                                <div className="mb-4">
                                    <h5 className="font-medium text-black/70 mb-4 uppercase">
                                        {translate('Seller')}
                                    </h5>

                                    <div className="flex items-center gap-3 bg-stone-50 rounded-card border border-zinc-100 py-4 px-7">
                                        <div className="bg-white rounded-full overflow-hidden size-[70px]">
                                            <Link
                                                to={`/shops/${product.shop.slug}`}
                                                className="h-full block"
                                            >
                                                <Image
                                                    src={product.shop.logo}
                                                    alt={product.shop.name}
                                                    className="h-full"
                                                />
                                            </Link>
                                        </div>
                                        <div className="">
                                            <h4 className="font-medium">
                                                <Link
                                                    to={`/shops/${product.shop.slug}`}
                                                >
                                                    {product.shop.name}
                                                </Link>
                                            </h4>
                                            <ThemeRating
                                                rating={
                                                    product.shop.rating.average
                                                }
                                                totalReviews={
                                                    product.shop.rating.total
                                                }
                                                className="mt-[7px] mb-[11px]"
                                            />
                                            <Button
                                                as={'link'}
                                                to={`/shops/${product.shop.slug}`}
                                                variant="outline"
                                                arrow={false}
                                            >
                                                {translate('Visit Shop')}
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            )}

                            <h5 className="font-medium text-black/70 mb-3 uppercase">
                                {translate('Service')}
                            </h5>

                            <div className="space-y-2">
                                {product.deliveryHours ? (
                                    <div className="flex items-center gap-5 bg-stone-50 rounded-card border border-zinc-100 px-4 py-3">
                                        <div className="text-2xl text-secondary">
                                            <FaTruck />
                                        </div>
                                        <div>
                                            <h5 className="text-black/70 text-sm">
                                                {translate(
                                                    'Estimated Delivery',
                                                )}
                                            </h5>
                                            <p>{product.deliveryHours}</p>
                                        </div>
                                    </div>
                                ) : null}
                                {product.codAvailable ? (
                                    <>
                                        <div className="flex items-center gap-5 bg-stone-50 rounded-card border border-zinc-100 px-4 py-3">
                                            <div className="text-2xl text-secondary">
                                                <FaCoins />
                                            </div>
                                            <div>
                                                <h5 className="text-black/70 text-sm">
                                                    {translate(
                                                        'Cash On Delivery',
                                                    )}
                                                </h5>
                                                <p>
                                                    {product.codAvailable
                                                        ? translate('Available')
                                                        : translate(
                                                              'Not Available',
                                                          )}
                                                </p>
                                            </div>
                                        </div>
                                    </>
                                ) : null}
                                {product.hasEmi ? (
                                    <>
                                        <div className="flex items-center gap-5 bg-stone-50 rounded-card border border-zinc-100 px-4 py-3">
                                            <div className="text-2xl text-secondary">
                                                <FaPiggyBank />
                                            </div>
                                            <div>
                                                <h5 className="text-black/70">
                                                    {translate('EMI Facility')}
                                                </h5>
                                                <p>
                                                    {product.hasEmi
                                                        ? product.emiInfo
                                                        : translate(
                                                              'Not Available',
                                                          )}
                                                </p>
                                            </div>
                                        </div>
                                    </>
                                ) : null}
                                {product.hasWarranty ? (
                                    <>
                                        <div className="flex items-center gap-5 bg-stone-50 rounded-card border border-zinc-100 px-4 py-3">
                                            <div className="text-2xl text-secondary">
                                                <FaCog />
                                            </div>
                                            <div>
                                                <h5 className="text-black/70">
                                                    {translate('Warranty')}
                                                </h5>
                                                <p>
                                                    {product.hasWarranty
                                                        ? product.warrantyInfo
                                                        : translate(
                                                              'Not Available',
                                                          )}
                                                </p>
                                            </div>
                                        </div>
                                    </>
                                ) : null}
                            </div>

                            {product.promoCode ? (
                                <>
                                    <h5 className="font-medium text-black/70 mb-4 mt-8 uppercase">
                                        {translate('PROMOTIONS')}
                                    </h5>

                                    <button
                                        onClick={() =>
                                            dispatch(
                                                togglePopup({
                                                    popup: 'coupon-details',
                                                    popupProps: {
                                                        couponCode:
                                                            product.promoCode!
                                                                .code,
                                                    },
                                                }),
                                            )
                                        }
                                    >
                                        <Badge>{product.promoCode.code}</Badge>
                                    </button>
                                </>
                            ) : null}

                            {product.relatedProductsSidebarEnabled && (
                                <>
                                    <SectionTitle
                                        title={translate('[Related] Products')}
                                        classNames={{
                                            title: 'ek-h3',
                                        }}
                                        className="mt-10 mb-8"
                                    />

                                    <div className="space-y-5">
                                        {product.relatedProducts.map((product) => (
                                            <ProductCardSmall
                                                key={product.id}
                                                product={product}
                                            />
                                        ))}
                                    </div>
                                </>
                            )}
                        </div>
                    </div>
                </div>
            </section>

            {product.relatedProductsBottomEnabled && (
                <section className="mt-6">
                    <div className="container">
                        <SectionTitle
                            connector
                            subTitle={translate('You may also like')}
                            title={translate('[Related] Products')}
                            className="mb-8"
                            link={
                                product.rootCategory
                                    ? `/categories/${product.rootCategory?.slug}`
                                    : '/products'
                            }
                        />

                        <ProductsSlider>
                            {product.relatedProducts.map((product) => (
                                <SwiperSlide key={product.id}>
                                    <ProductCard product={product} />
                                </SwiperSlide>
                            ))}
                        </ProductsSlider>
                    </div>
                </section>
            )}
        </>
    );
};

export default ProductDetails;
