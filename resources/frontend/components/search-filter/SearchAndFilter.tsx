import qs from 'qs';
import { ReactNode, useCallback, useEffect, useRef, useState } from 'react';
import { FaHome, FaSpinner } from 'react-icons/fa';
import { MdFilterListAlt } from 'react-icons/md';
import { useLocation, useSearchParams } from 'react-router-dom';
import NoDataFound from '../../layouts/components/not-found/NoDataFound';
import NotFound from '../../pages/NotFound';
import { useLazyGetProductsQuery } from '../../store/features/api/productApi';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { URLFilterParams } from '../../types';
import { IProductShort } from '../../types/product';
import { translate } from '../../utils/translate';
import Breadcrumb from '../Breadcrumb';
import Button from '../buttons/Button';
import ProductCard from '../card/product-card';
import SelectInput from '../inputs/SelectInput';
import Pagination from '../pagination/Pagination';
import ProductCardSkeleton from '../skeletons/ProductCardSkeleton';
import SectionTitle from '../titles/SectionTitle';
import FilterSidebar from './FilterSidebar';

interface Props {
    breadcrumb?: boolean;
    categorySlug?: string;
    brandSlug?: string;
    shopSlug?: string;
    discounted?: boolean;
    onSaleProducts?: boolean;
    featuredProducts?: boolean;
    title?: string;
    titleAside?: ReactNode;
}

const SearchAndFilter = ({
    breadcrumb = true,
    categorySlug,
    brandSlug,
    shopSlug,
    discounted = false,
    onSaleProducts = false,
    featuredProducts = false,
    title,
    titleAside,
}: Props) => {
    const dispatch = useAppDispatch();
    const location = useLocation();
    const [searchParams, setSearchParams] = useSearchParams();
    const loadMoreRef = useRef<HTMLDivElement>(null);
    const hasTriggeredRef = useRef(false);

    // Track previous search params (without page) to detect actual filter changes
    const prevSearchRef = useRef(location.search);

    // Track if we've successfully loaded products (to prevent 404 on pagination errors)
    const hasLoadedProductsRef = useRef(false);

    // Track if we ever had a valid category for this slug (to distinguish invalid category vs pagination error)
    const hasValidCategoryRef = useRef(false);

    // Check if infinite scroll is enabled
    const isInfiniteScrollEnabled = window.config.generalSettings.productInfiniteScroll;

    // State for accumulated products (used in infinite scroll mode)
    const [accumulatedProducts, setAccumulatedProducts] = useState<IProductShort[]>([]);
    const [currentPage, setCurrentPage] = useState(1);
    const [isLoadingMore, setIsLoadingMore] = useState(false);

    const [getProducts, { data: productsRes, isLoading, isFetching }] =
        useLazyGetProductsQuery();

    const products = productsRes?.products?.data;
    const pagination = productsRes?.products?.meta;
    const brands = productsRes?.brands;
    const rootCategories = productsRes?.rootCategories;
    const filterAttributes = productsRes?.filterAttributes;
    const selectedCategory = productsRes?.selectedCategory;
    const priceFilter = productsRes?.priceFilter;

    const filters: URLFilterParams = qs.parse(location.search, {
        ignoreQueryPrefix: true,
        parseArrays: true,
        plainObjects: true,
    });
    filters.limit = 16;

    // Reset accumulated products when filters change (not just page)
    useEffect(() => {
        if (!isInfiniteScrollEnabled) return;

        const prevParams = new URLSearchParams(prevSearchRef.current);
        const currentParams = new URLSearchParams(location.search);

        // Extract page numbers
        const prevPage = parseInt(prevParams.get('page') || '1', 10);
        const currentPageFromUrl = parseInt(currentParams.get('page') || '1', 10);

        // Remove page param for comparison
        prevParams.delete('page');
        currentParams.delete('page');

        // Only reset if non-page filters changed
        if (prevParams.toString() !== currentParams.toString()) {
            setAccumulatedProducts([]);
            setCurrentPage(1);
            hasTriggeredRef.current = false;
            hasLoadedProductsRef.current = false;
        } else if (prevPage !== currentPageFromUrl) {
            // Only page changed, sync currentPage state with URL
            setCurrentPage(currentPageFromUrl);
        }

        prevSearchRef.current = location.search;
    }, [location.search, location.pathname, isInfiniteScrollEnabled]);

    // Reset refs and accumulatedProducts when category/brand/shop changes
    // (to properly show 404 for invalid categories and clear old products)
    useEffect(() => {
        hasLoadedProductsRef.current = false;
        hasValidCategoryRef.current = false;
        if (isInfiniteScrollEnabled) {
            setAccumulatedProducts([]);
            setCurrentPage(1);
            hasTriggeredRef.current = false;
        }
    }, [
        categorySlug,
        brandSlug,
        shopSlug,
        discounted,
        onSaleProducts,
        featuredProducts,
        isInfiniteScrollEnabled,
    ]);

    // Track valid category when selectedCategory is available
    useEffect(() => {
        if (selectedCategory) {
            hasValidCategoryRef.current = true;
        }
    }, [selectedCategory]);

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

            // Mark that we've successfully loaded products
            if (products.length > 0) {
                hasLoadedProductsRef.current = true;
            }
        }
    }, [products, isInfiniteScrollEnabled, currentPage]);

    // Track if the URL change was triggered by infinite scroll
    const isInfiniteScrollRef = useRef(false);

    // fetch products on component mount and when the search params (url) changes
    useEffect(() => {
        // Skip if this was triggered by infinite scroll (handled separately)
        if (isInfiniteScrollRef.current) {
            isInfiniteScrollRef.current = false;
            return;
        }

        const scopedFilters: URLFilterParams = {
            ...filters,
            categorySlug,
            brandSlug,
            shopSlug,
            ...(discounted ? { discounted } : {}),
            ...(onSaleProducts ? { onSaleProducts } : {}),
            ...(featuredProducts ? { featuredProducts } : {}),
        };

        // For infinite scroll, only fetch page 1 on initial load or filter change (not page change)
        if (isInfiniteScrollEnabled) {
            // Get page from URL params, default to 1
            const urlPage = parseInt(searchParams.get('page') || '1', 10);
            let validPage = isNaN(urlPage) ? 1 : Math.max(1, urlPage);

            // Check if page exceeds available pages - if so, reset to page 1
            if (pagination && validPage > pagination.last_page && pagination.last_page > 0) {
                validPage = 1;
                // Update URL to page 1 without reload
                const newSearchParams = new URLSearchParams(searchParams);
                newSearchParams.set('page', '1');
                const newUrl = `${window.location.pathname}?${newSearchParams.toString()}`;
                window.history.replaceState(null, '', newUrl);
                prevSearchRef.current = newUrl;
            }

            setCurrentPage(validPage);
            scopedFilters.page = validPage;
        }

        // stringify the filters object
        const queryStr = qs.stringify(
            { ...scopedFilters, searchKey: scopedFilters.query },
            {
                addQueryPrefix: true,
                arrayFormat: 'brackets',
            },
        );
        getProducts(queryStr, true);
    }, [location.search, location.pathname, isInfiniteScrollEnabled, categorySlug, brandSlug, shopSlug, discounted, onSaleProducts, featuredProducts]);

    // Load more products for infinite scroll
    const loadMoreProducts = useCallback(() => {
        if (!pagination || isFetching || isLoadingMore) return;

        const nextPage = pagination.current_page + 1;
        if (nextPage > pagination.last_page) return;

        // Set flag to skip the URL change useEffect
        isInfiniteScrollRef.current = true;

        setIsLoadingMore(true);
        setCurrentPage(nextPage);

        const newFilters = {
            ...filters,
            categorySlug,
            brandSlug,
            shopSlug,
            ...(discounted ? { discounted } : {}),
            ...(onSaleProducts ? { onSaleProducts } : {}),
            ...(featuredProducts ? { featuredProducts } : {}),
            page: nextPage,
        };
        const queryStr = qs.stringify(
            { ...newFilters, searchKey: newFilters.query },
            {
                addQueryPrefix: true,
                arrayFormat: 'brackets',
            },
        );
        getProducts(queryStr, false);

        // Update URL with new page (non-replace to allow browser back button)
        const newSearchParams = new URLSearchParams(searchParams);
        newSearchParams.set('page', String(nextPage));
        // Use history.pushState to update URL without triggering React Router's location change
        const newUrl = `${window.location.pathname}?${newSearchParams.toString()}`;
        window.history.pushState(null, '', newUrl);
    }, [
        pagination,
        isFetching,
        isLoadingMore,
        filters,
        categorySlug,
        brandSlug,
        shopSlug,
        discounted,
        onSaleProducts,
        featuredProducts,
        searchParams,
        getProducts,
    ]);

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

    // set filters to the search params (url)
    const handleFilter = (query: URLFilterParams) => {
        const str = qs.stringify(query, {
            addQueryPrefix: true,
            arrayFormat: 'brackets',
            encode: false,
        });

        setSearchParams(str);
    };

    const handleCheckboxChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const { checked, value } = e.target;
        const name = e.target.name as keyof URLFilterParams;

        if (checked) {
            handleFilter({
                ...filters,
                [name]: [...((filters[name] as string[]) || []), value],
            });
        } else {
            handleFilter({
                ...filters,
                [name]: (filters[name] as string[]).filter(
                    (item) => item !== value,
                ),
            });
        }
    };

    const handlePriceChange = (values: [number, number]) => {
        handleFilter({
            ...filters,
            minPrice: values[0].toString(),
            maxPrice: values[1].toString(),
        });
    };

    const handleSortingChange = (e: {
        label: string;
        value: string | number;
    }) => {
        handleFilter({
            ...filters,
            sortBy: String(e.value),
        });
    };

    const pageTitle =
        title ||
        (categorySlug || brandSlug || shopSlug)?.replace(/[-_]/g, ' ') ||
        (filters.query &&
            `${translate('Search result for')} '${filters.query || ''}'`) ||
        'All Products';

    // Show 404 if category doesn't exist (slug provided but no valid category found)
    // Only show after loading completes and we never got a valid category
    if (!isLoading && !isFetching && categorySlug && !selectedCategory && !hasValidCategoryRef.current) {
        return <NotFound />;
    }

    // Determine which products to display
    const displayProducts = isInfiniteScrollEnabled ? accumulatedProducts : products;
    const showLoading = isFetching && !isLoadingMore;
    const hasMore = pagination ? pagination.current_page < pagination.last_page : false;

    return (
        <>
            {breadcrumb && (
                <Breadcrumb
                    title={translate('Products')}
                    navigation={[
                        {
                            icon: <FaHome />,
                            name: translate('home'),
                            link: '/',
                        },
                        {
                            name: translate('Products'),
                            link: '/products',
                        },
                        {
                            name: pageTitle,
                        },
                    ]}
                />
            )}

            <div
                className={`container grid lg:grid-cols-4 mb-20 ${
                    breadcrumb && 'mt-6'
                }`}
                id="search-and-filter"
            >
                <FilterSidebar
                    isLoading={isLoading}
                    productsRes={productsRes}
                    selectedCategory={selectedCategory}
                    rootCategories={rootCategories}
                    categorySlug={categorySlug}
                    brandSlug={brandSlug}
                    priceFilter={priceFilter}
                    brands={brands}
                    filterAttributes={filterAttributes}
                    filters={filters}
                    handleCheckboxChange={handleCheckboxChange}
                    handlePriceChange={handlePriceChange}
                    handleSortingChange={handleSortingChange}
                    handleFilter={handleFilter}
                />

                <div className="lg:col-span-3">
                    <div className="flex items-end gap-3.5">
                        <SectionTitle
                            title={`[${pageTitle}]`}
                            titleAside={titleAside}
                            classNames={{
                                container: 'grow min-w-0 mb-0',
                                title: 'ek-h3',
                            }}
                        />

                        <div className="max-lg:hidden">
                            <SelectInput
                                placeholder={translate('Sort by')}
                                classNames={{
                                    singleValue: () => '!text-black/70',
                                }}
                                value={filters.sortBy || ''}
                                onChange={(e) =>
                                    handleFilter({
                                        ...filters,
                                        sortBy: e.value,
                                    })
                                }
                                options={[
                                    {
                                        label: translate('Price: Low to High'),
                                        value: 'lowToHigh',
                                    },
                                    {
                                        label: translate('Price: High to Low'),
                                        value: 'highToLow',
                                    },
                                    {
                                        label: translate('Newest'),
                                        value: 'newest',
                                    },
                                    {
                                        label: translate('Oldest'),
                                        value: 'oldest',
                                    },
                                ]}
                            />
                        </div>

                        <Button
                            arrow={false}
                            variant="outline"
                            className="lg:hidden"
                            onClick={() =>
                                dispatch(togglePopup('product-filter'))
                            }
                        >
                            <span className="inline-flex items-center justify-center gap-2 leading-none">
                                <span className="text-secondary text-xl leading-none">
                                    <MdFilterListAlt />
                                </span>
                                <span className="leading-none">
                                    {translate('Filter')}
                                </span>
                            </span>
                        </Button>
                    </div>

                    <div
                        className="grid grid-cols-2 xs:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-4 mt-3 sm:mt-6 gap-5"
                        id="filtered-products"
                    >
                        {showLoading && !displayProducts?.length ? (
                            Array(8)
                                .fill(0)
                                .map((_, i) => <ProductCardSkeleton key={i} />)
                        ) : !displayProducts?.length ? (
                            <div className="col-span-4">
                                <NoDataFound
                                    title="No Products Found"
                                    description="Seems like the product list is empty. Try browsing other categories"
                                    image="/images/no-media.png"
                                />
                            </div>
                        ) : (
                            displayProducts.map((product) => (
                                <ProductCard
                                    key={product.id}
                                    product={product}
                                />
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
                    {!isInfiniteScrollEnabled && productsRes?.products.meta?.last_page! > 1 && (
                        <Pagination
                            className="mt-10"
                            pagination={productsRes?.products.meta!}
                            scrollTo="#search-and-filter"
                        />
                    )}
                </div>
            </div>
        </>
    );
};

export default SearchAndFilter;
