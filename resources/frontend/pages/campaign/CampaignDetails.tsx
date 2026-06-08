import { FaHome, FaSpinner } from 'react-icons/fa';
import { useCallback, useEffect, useRef, useState } from 'react';
import { useParams, useSearchParams } from 'react-router-dom';
import Breadcrumb from '../../components/Breadcrumb';
import CountDown from '../../components/CountDown';
import ProductCard from '../../components/card/product-card';
import Pagination from '../../components/pagination/Pagination';
import ProductCardSkeleton from '../../components/skeletons/ProductCardSkeleton';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetCampaignDetailsQuery } from '../../store/features/api/campaignApi';
import { IProductShort } from '../../types/product';
import { translate } from '../../utils/translate';

const CampaignDetails = () => {
    const params = useParams<{ slug: string }>();
    const [searchParams, setSearchParams] = useSearchParams();
    const loadMoreRef = useRef<HTMLDivElement>(null);
    const hasTriggeredRef = useRef(false);

    // Check if infinite scroll is enabled
    const isInfiniteScrollEnabled = window.config.generalSettings.productInfiniteScroll;

    // State for accumulated products (used in infinite scroll mode)
    const [accumulatedProducts, setAccumulatedProducts] = useState<IProductShort[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [isLoadingMore, setIsLoadingMore] = useState(false);

    const { data: campaignRes, isLoading, isFetching } = useGetCampaignDetailsQuery({
        slug: params.slug!,
        page: currentPage,
        limit: 16,
    });

    const campaign = campaignRes?.campaign;
    const products = campaignRes?.products.data;
    const pagination = campaignRes?.products.meta;

    // Reset accumulated products when campaign changes
    useEffect(() => {
        if (isInfiniteScrollEnabled) {
            setAccumulatedProducts([]);
            setCurrentPage(1);
            hasTriggeredRef.current = false;
        }
    }, [params.slug, isInfiniteScrollEnabled]);

    // Update accumulated products when new data arrives
    useEffect(() => {
        if (isInfiniteScrollEnabled && products) {
            if (currentPage === 1) {
                setAccumulatedProducts(products);
            } else {
                setAccumulatedProducts((prev) => {
                    // Filter out duplicates
                    const newProducts = products.filter(
                        (newProduct) => !prev.some((p) => p.id === newProduct.id)
                    );
                    return [...prev, ...newProducts];
                });
            }
            setIsLoadingMore(false);
            hasTriggeredRef.current = false;
        }
    }, [products, isInfiniteScrollEnabled, currentPage]);

    // Load more products for infinite scroll
    const loadMoreProducts = useCallback(() => {
        if (!pagination || isFetching || isLoadingMore) return;
        
        const nextPage = pagination.current_page + 1;
        if (nextPage > pagination.last_page) return;

        setIsLoadingMore(true);
        setCurrentPage(nextPage);

        // Update URL with new page
        const newSearchParams = new URLSearchParams(searchParams);
        newSearchParams.set('page', String(nextPage));
        setSearchParams(newSearchParams, { replace: true });
    }, [pagination, isFetching, isLoadingMore, searchParams, setSearchParams]);

    // Intersection Observer for infinite scroll
    useEffect(() => {
        if (!isInfiniteScrollEnabled || !loadMoreRef.current || !pagination) return;
        
        const hasMore = pagination.current_page < pagination.last_page;
        if (!hasMore || isFetching || isLoadingMore) return;

        const observer = new IntersectionObserver(
            (entries) => {
                const [entry] = entries;
                if (entry.isIntersecting && !hasTriggeredRef.current && !isFetching && !isLoadingMore) {
                    hasTriggeredRef.current = true;
                    loadMoreProducts();
                }
            },
            {
                root: null,
                rootMargin: '200px',
                threshold: 0.1,
            }
        );

        observer.observe(loadMoreRef.current);

        return () => {
            observer.disconnect();
        };
    }, [isInfiniteScrollEnabled, pagination, isFetching, isLoadingMore, loadMoreProducts]);

    // Determine which products to display
    const displayProducts = isInfiniteScrollEnabled ? accumulatedProducts : products;
    const showLoading = (isLoading || isFetching) && !isLoadingMore;
    const hasMore = pagination ? pagination.current_page < pagination.last_page : false;

    return (
        <>
            <Breadcrumb
                navigation={[
                    { name: translate('Home'), link: '/', icon: <FaHome /> },
                    { name: translate('Campaigns'), link: '/campaigns' },
                    { name: campaign?.name || '' },
                ]}
                className="mb-0"
            />

            <section>
                <img
                    src={campaign?.banner}
                    alt={campaign?.name}
                    className="h-[300px] w-full object-cover bg-gray-300"
                />
            </section>

            <section className="mt-10 text-center">
                <div className="container">
                    <h1 className="ek-h2 font-semibold">{campaign?.name}</h1>
                    <p className="my-4 max-w-[700px] mx-auto leading-relaxed">
                        {campaign?.shortDescription}
                    </p>

                    <CountDown
                        label="This Deal Ends In"
                        className="sm:mt-7 justify-center"
                        date={Number(campaign?.endDate) * 1000}
                    />
                </div>
            </section>

            <section className="mt-9">
                <div className="container">
                    <SectionTitle
                        title={campaign?.name || ''}
                        highlightFirstWord
                        className="mb-7"
                    />

                    <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        {showLoading && !displayProducts?.length ? (
                            Array.from({ length: 5 }).map((_, index) => (
                                <ProductCardSkeleton key={index} />
                            ))
                        ) : !displayProducts?.length ? (
                            <div className="col-span-5 py-5 text-center">
                                {translate('No Product Found')}
                            </div>
                        ) : (
                            displayProducts.map((product) => (
                                <div key={product.id} className="mb-4">
                                    <ProductCard product={product} />
                                </div>
                            ))
                        )}
                    </div>

                    {/* Infinite scroll loading indicator */}
                    {isInfiniteScrollEnabled && (
                        <>
                            <div
                                ref={loadMoreRef}
                                className="h-20 flex items-center justify-center"
                            >
                                {(isLoadingMore || isFetching) && displayProducts && displayProducts.length > 0 && (
                                    <div className="flex items-center gap-2 text-gray-500">
                                        <FaSpinner className="animate-spin" />
                                        <span>{translate('Loading more...')}</span>
                                    </div>
                                )}
                            </div>

                        </>
                    )}

                    {/* Pagination (only shown when infinite scroll is disabled) */}
                    {!isInfiniteScrollEnabled && pagination && pagination.last_page > 1 && (
                        <Pagination pagination={pagination} className="mt-6 pt-5" />
                    )}
                </div>
            </section>
        </>
    );
};

export default CampaignDetails;
