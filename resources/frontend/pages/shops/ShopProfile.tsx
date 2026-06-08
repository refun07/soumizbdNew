import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { SwiperSlide } from 'swiper/react';
import Avatar from '../../components/Avatar';
import ProductCard from '../../components/card/product-card';
import Pagination from '../../components/pagination/Pagination';
import { impressions } from '../../components/popups/ReviewModal';
import ReviewItem from '../../components/reviews/ReviewItem';
import ThemeRating from '../../components/reviews/ThemeRating';
import ProductsSlider from '../../components/slider/ProductsSlider';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetShopProfileInfoQuery } from '../../store/features/api/shopApi';
import { numberFormatter, paddedNumber } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

const initialImpressionPercentage = [
    {
        name: 'Positive',
        percentage: '0',
        count: 0,
    },
    {
        name: 'Negative',
        percentage: '0',
        count: 0,
    },
    {
        name: 'Neutral',
        percentage: '0',
        count: 0,
    },
];

const ShopProfile = () => {
    const params = useParams<{ shopSlug: string }>();
    const [starPercentage, setStarPercentage] = useState([0, 0, 0, 0, 0]);
    const [impressionPercentage, setImpressionPercentage] = useState(
        initialImpressionPercentage,
    );
    // const { data: shop } = useGetShopDetailsQuery(params.shopSlug!);
    let { data: shopProfile, isLoading } = useGetShopProfileInfoQuery(
        params.shopSlug!,
    );

    // calculating star percentage
    useEffect(() => {
        if (!shopProfile) return;
        const { summary } = shopProfile?.productReviews;

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
    }, [shopProfile?.productReviews?.summary]);

    // calculating impression percentage, count, and name
    useEffect(() => {
        if (!shopProfile) return;
        const { summary } = shopProfile.shopReviews;

        const shopReviewPercentage = [
            {
                name: 'Positive',
                percentage: numberFormatter(
                    (summary.positive / summary.total) * 100,
                    {
                        maximumFractionDigits: 1,
                    },
                ),
                count: summary.positive,
            },
            {
                name: 'Negative',
                percentage: numberFormatter(
                    (summary.negative / summary.total) * 100,
                    { maximumFractionDigits: 1 },
                ),
                count: summary.negative,
            },
            {
                name: 'Neutral',
                percentage: numberFormatter(
                    (summary.neutral / summary.total) * 100,
                    { maximumFractionDigits: 1 },
                ),
                count: summary.neutral,
            },
        ];

        setImpressionPercentage(shopReviewPercentage);
    }, [shopProfile?.shopReviews.summary]);

    const { overview, productReviews, shopReviews, bestSellingProducts } =
        shopProfile || {};

    return (
        <>
            <section>
                <div className="container bg-white rounded-card py-5 md:py-10 lg:py-14 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 items-center max-md:gap-y-6 gap-3">
                    <div>
                        <div className="uppercase text-sm text-black/70">
                            {translate('Shop Age')}
                        </div>
                        {isLoading ? (
                            <div className="skeleton h-[42px] mt-3"></div>
                        ) : (
                            <div className="flex gap-2 items-center mt-1 sm:mt-3">
                                <span className="text-black text-xl md:text-[28px]">
                                    {overview?.age.count}
                                </span>
                                <span className="text-black/70">
                                    {overview?.age.unit}
                                </span>
                            </div>
                        )}
                    </div>
                    <div>
                        <div className="uppercase text-sm text-black/70">
                            {translate('Total')}
                        </div>
                        <div className="flex gap-2 items-center mt-1 sm:mt-3">
                            {isLoading ? (
                                <div className="skeleton h-[42px] w-[70px]"></div>
                            ) : (
                                <span className="text-black text-xl md:text-[28px]">
                                    {overview?.totalProducts}
                                </span>
                            )}
                            <span className="text-black/70">
                                {translate('Products')}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div className="uppercase text-sm text-black/70">
                            {translate('Delivered')}
                        </div>
                        <div className="flex gap-2 items-center mt-1 sm:mt-3">
                            {isLoading ? (
                                <div className="skeleton h-[42px] w-[70px]"></div>
                            ) : (
                                <span className="text-black text-xl md:text-[28px]">
                                    {overview?.deliveryPercentage}
                                </span>
                            )}
                            <span className="text-black/70">
                                {translate('Orders')}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div className="uppercase text-sm text-black/70">
                            {translate('Product Reviews')}
                        </div>
                        <div className="flex gap-2 items-center mt-1 sm:mt-3">
                            {isLoading ? (
                                <div className="skeleton h-[42px] w-[50px]"></div>
                            ) : (
                                <span className="text-black text-xl md:text-[28px]">
                                    {overview?.overallProductReview}
                                </span>
                            )}
                            <span className="text-black/70">
                                {translate('Overall Review')}
                            </span>
                        </div>
                    </div>
                    <div>
                        <div className="uppercase text-sm text-black/70">
                            {translate('Shop Review')}
                        </div>
                        <div className="flex gap-2 items-center mt-1 sm:mt-3">
                            {isLoading ? (
                                <div className="skeleton h-[42px] w-[70px]"></div>
                            ) : (
                                <span className="text-black text-xl md:text-[28px]">
                                    {overview?.positiveShopReviewPercentage}
                                </span>
                            )}
                            <span className="text-black/70">
                                {translate('Positive Review')}
                            </span>
                        </div>
                    </div>
                </div>
            </section>

            <section className="mt-8">
                <div className="container no-style grid grid-cols-1 md:grid-cols-3">
                    <div className="col-span-2 container !mx-0 w-full">
                        <SectionTitle
                            title={translate('[Product] Review')}
                            connector
                            className="mb-8"
                        />

                        <div className="flex max-sm:flex-col gap-5 sm:gap-10 md:gap-[64px]">
                            <div className="pr-12 sm:border-r border-primary-14">
                                <h4 className="text-sm mb-9">
                                    {translate('Summary')}
                                </h4>

                                <div className="flex gap-6">
                                    <div>
                                        {isLoading ? (
                                            <div className="skeleton h-9 w-[70px] mb-1.5"></div>
                                        ) : (
                                            <div className="text-3xl mb-1.5">
                                                {productReviews?.summary.average.toFixed(
                                                    2,
                                                )}
                                            </div>
                                        )}
                                        <ThemeRating
                                            readOnly
                                            rating={
                                                productReviews?.summary
                                                    .average || 0
                                            }
                                            totalReviews={
                                                productReviews?.summary.total
                                            }
                                        />
                                    </div>

                                    <div>
                                        {isLoading ? (
                                            <div className="skeleton h-9 w-[70px] mb-1.5"></div>
                                        ) : (
                                            <div className="text-3xl mb-1.5">
                                                {paddedNumber(
                                                    productReviews?.summary
                                                        .total,
                                                )}
                                            </div>
                                        )}
                                        <p className="ek-h4 text-black/70">
                                            {translate('Total Reviews')}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div className="grow space-y-2.5">
                                {starPercentage.map((percentage, index) => (
                                    <div
                                        className="flex items-center gap-2 max-w-[530px] whitespace-nowrap"
                                        key={index}
                                    >
                                        <span className="w-10 text-black/[.6]">
                                            {5 - index} star
                                        </span>
                                        <span className="grow inline-flex bg-black/5 rounded-sm overflow-hidden h-2">
                                            <span
                                                className="bg-theme-yellow h-2 inline-block"
                                                style={{
                                                    width: `${percentage}%`,
                                                }}
                                            ></span>
                                        </span>
                                        <span className="w-10 text-black/[.38]">
                                            {numberFormatter(percentage, {
                                                maximumFractionDigits: 1,
                                            })}
                                            %
                                        </span>
                                    </div>
                                ))}
                            </div>
                        </div>

                        <hr className="mt-9 mb-7" />

                        <div>
                            <h4 className="mb-7 uppercase">
                                {translate('Reviews')}
                            </h4>

                            {productReviews?.allReviews.data.map((review) => (
                                <div key={review.id}>
                                    <ReviewItem review={review} />
                                    <hr className="mt-4 mb-8" />
                                </div>
                            ))}
                            <Pagination
                                pagination={productReviews?.allReviews.meta}
                            />
                        </div>
                    </div>

                    <div>
                        <div className="container !mx-0 w-full text-sm">
                            <SectionTitle
                                connector
                                title={translate('[Shop] Review')}
                                className="mb-8"
                            />

                            <div className="">
                                <div className="grow space-y-2.5">
                                    {impressionPercentage.map((item, index) => (
                                        <div
                                            className="flex items-center gap-2 max-w-[530px] whitespace-nowrap"
                                            key={index}
                                        >
                                            <span className="w-16 text-black/[.6]">
                                                {translate(item.name)}
                                            </span>
                                            <span className="grow inline-flex bg-black/5 rounded-sm overflow-hidden h-2">
                                                <span
                                                    className="bg-theme-yellow h-2 inline-block"
                                                    style={{
                                                        width: `${item.percentage}%`,
                                                    }}
                                                ></span>
                                            </span>
                                            <span className="w-10 text-black/[.6]">
                                                {item.count}
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {impressionPercentage.length ? (
                                <hr className="mt-9 mb-7" />
                            ) : null}

                            <div className="">
                                {shopReviews?.allReviews.map((review) => (
                                    <>
                                        <div className="flex items-center justify-between">
                                            <div className="flex gap-4 items-center">
                                                <div>
                                                    <Avatar
                                                        name={review.user.name}
                                                        avatar={
                                                            review.user.avatar
                                                        }
                                                    />
                                                </div>

                                                <div>
                                                    <h5 className="text-sm mb-0.5">
                                                        {review.user.name}
                                                    </h5>
                                                    <time className="text-black/70 text-sm">
                                                        {review.createdDate}
                                                    </time>
                                                </div>
                                            </div>

                                            <div className="text-right flex items-center gap-1">
                                                <span className="text-2xl leading-none text-theme-orange">
                                                    {
                                                        impressions.find(
                                                            (item) =>
                                                                item.value ===
                                                                review.impression,
                                                        )?.icon
                                                    }
                                                </span>
                                                <span className="text-black">
                                                    {review.impression}
                                                </span>
                                            </div>
                                        </div>

                                        <hr className="my-5" />
                                    </>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section className="my-8">
                <div className="container">
                    <SectionTitle
                        connector
                        title={translate('[Best] Sellers')}
                        className="mb-8"
                        link={`/shops/${params.shopSlug}/products`}
                    />

                    <ProductsSlider>
                        {bestSellingProducts?.map((product) => (
                            <SwiperSlide key={product.id}>
                                <ProductCard product={product} />
                            </SwiperSlide>
                        ))}
                    </ProductsSlider>
                </div>
            </section>
        </>
    );
};

export default ShopProfile;
