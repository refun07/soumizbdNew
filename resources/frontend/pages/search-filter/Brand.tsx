import { Link, useParams, useSearchParams } from 'react-router-dom';
import Image from '../../components/Image';
import SearchForm from '../../components/inputs/SearchForm';
import SearchAndFilter from '../../components/search-filter/SearchAndFilter';
import ShopHeaderSkeleton from '../../components/skeletons/shop/ShopHeaderSkeleton';
import NotFound from '../NotFound';
import { useGetBrandDetailsQuery } from '../../store/features/api/brandApi';
import { translate } from '../../utils/translate';

const Brand = () => {
    const params = useParams<{ brandSlug: string }>();
    const brandSlug = params.brandSlug!;
    const { data: brand, isLoading, isFetching } =
        useGetBrandDetailsQuery(brandSlug);
    const [searchParams] = useSearchParams();
    const activeCollection = searchParams.get('collection') || '';

    // keep the other filters when switching collections, reset the page
    const collectionLink = (slug: string) => {
        const params = new URLSearchParams(searchParams);
        params.delete('page');
        if (slug) params.set('collection', slug);
        else params.delete('collection');
        const qs = params.toString();
        return `/brands/${brandSlug}${qs ? `?${qs}` : ''}`;
    };

    const collectionTabs = brand?.collections?.length
        ? [
              { slug: '', name: translate('All'), productsCount: brand.productsCount },
              ...brand.collections,
          ]
        : [];

    if (isLoading) {
        return (
            <>
                <ShopHeaderSkeleton />
                <SearchAndFilter
                    breadcrumb={false}
                    brandSlug={brandSlug}
                    title={translate('Products')}
                />
            </>
        );
    }

    if (!isFetching && !brand) {
        return <NotFound />;
    }

    const bannerImage =
        brand?.bannerImage || brand?.metaImage || brand?.thumbnailImage;

    return (
        <>
            <div className="container no-style">
                <div className="relative">
                    <div className="h-[150px] lg:h-[300px] bg-gray-100 overflow-hidden">
                        {bannerImage ? (
                            <Image
                                src={bannerImage}
                                alt={brand?.name || ''}
                                className="h-full w-full object-cover"
                            />
                        ) : (
                            <div className="h-full w-full bg-gradient-to-br from-gray-100 via-white to-gray-200" />
                        )}
                    </div>

                    <div className="bg-gray-50 pl-4 lg:pl-28 py-4 md:h-20 flex max-md:flex-col gap-4 md:items-center justify-between">
                        <div className="flex items-center gap-4 md:gap-8 h-10 md:h-20 min-w-0">
                            <div className="h-[100px] lg:h-[158px] aspect-square self-end inline-flex shrink-0 items-center justify-center p-2 bg-white border border-zinc-100 md:mb-5">
                                <Image
                                    src={brand?.thumbnailImage}
                                    alt={brand?.name || ''}
                                    className="max-h-full max-w-full object-contain"
                                />
                            </div>

                            <div className="min-w-0">
                                <h1 className="md:text-xl text-black truncate">
                                    {brand?.name}
                                </h1>
                                <p className="text-black/70 text-sm">
                                    {translate('Brand Products')}
                                    {typeof brand?.productsCount === 'number'
                                        ? `: ${brand.productsCount}`
                                        : ''}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="flex gap-3 max-md:flex-col items-center justify-between py-4 max-md:px-4">
                    {!collectionTabs.length && (
                        <ul className="max-md:w-full flex max-md:justify-between gap-5 md:gap-14">
                            <li>
                                <span className="border-b-2 pb-1 text-primary border-secondary">
                                    {translate('Products')}
                                </span>
                            </li>
                        </ul>
                    )}

                    <div className="md:ml-auto md:max-w-[310px] w-full">
                        <SearchForm
                            name="query"
                            path={`/brands/${brandSlug}`}
                            suggestions={false}
                            placeholder={translate('Search Products')}
                        />
                    </div>
                </div>
            </div>

            <SearchAndFilter
                breadcrumb={false}
                brandSlug={brandSlug}
                title={brand?.name || translate('Products')}
                titleAside={
                    collectionTabs.length ? (
                        <nav
                            aria-label={translate('Collections')}
                            className="flex min-w-0 gap-2 overflow-x-auto [scrollbar-width:none]"
                        >
                            {collectionTabs.map((collection) => {
                                const isActive = collection.slug === activeCollection;

                                return (
                                    <Link
                                        key={collection.slug || 'all'}
                                        to={collectionLink(collection.slug)}
                                        aria-current={isActive ? 'page' : undefined}
                                        className={`shrink-0 whitespace-nowrap rounded-full border px-4 py-1.5 text-sm transition ${
                                            isActive
                                                ? 'border-primary bg-primary text-white'
                                                : 'border-zinc-200 bg-white text-black/70 hover:border-primary hover:text-primary'
                                        }`}
                                    >
                                        {collection.name}
                                        {typeof collection.productsCount === 'number' && (
                                            <span
                                                className={`ml-1.5 text-xs ${
                                                    isActive ? 'text-white/80' : 'text-black/40'
                                                }`}
                                            >
                                                {collection.productsCount}
                                            </span>
                                        )}
                                    </Link>
                                );
                            })}
                        </nav>
                    ) : undefined
                }
            />
        </>
    );
};

export default Brand;
