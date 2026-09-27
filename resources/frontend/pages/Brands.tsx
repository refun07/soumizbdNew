import { FaHome } from 'react-icons/fa';
import { Link, useSearchParams } from 'react-router-dom';
import Breadcrumb from '../components/Breadcrumb';
import CategoryCard from '../components/category-card';
import SearchForm from '../components/inputs/SearchForm';
import Pagination from '../components/pagination/Pagination';
import { CategorySkeleton } from '../components/skeletons/from-svg';
import {
    useGetBrandGroupsQuery,
    useGetBrandsQuery,
} from '../store/features/api/brandApi';
import { translate } from '../utils/translate';

const Brands = () => {
    const [searchParams] = useSearchParams();
    const activeGroup = searchParams.get('group') || '';
    const query = searchParams.get('query') || '';

    const { data: groups } = useGetBrandGroupsQuery();
    const { data, isLoading, isFetching } = useGetBrandsQuery({
        query,
        group: activeGroup,
        page: Number(searchParams.get('page')) || 1,
    });

    // keep the search keyword when switching groups, reset the page
    const groupLink = (slug: string) => {
        const params = new URLSearchParams();
        if (slug) params.set('group', slug);
        if (query) params.set('query', query);
        const qs = params.toString();
        return `/brands${qs ? `?${qs}` : ''}`;
    };

    const groupTabs = [
        { slug: '', name: translate('All'), brandsCount: undefined },
        ...(groups || []),
    ];

    const brands = data?.result?.data;
    const pagination = data?.result?.meta;

    return (
        <>
            <Breadcrumb
                title={translate('All Brands')}
                navigation={[
                    { link: '/', name: translate('Home'), icon: <FaHome /> },
                    { link: '/brands', name: translate('Brands') },
                ]}
            />

            <div className="container no-style flex items-center justify-between my-6">
                <div className="w-full max-w-[280px]">
                    <SearchForm
                        suggestions={false}
                        path="/brands"
                        placeholder={translate('Search Brands')}
                    />
                </div>

                <div></div>
            </div>

            {!!groups?.length && (
                <div className="container no-style">
                    <div className="flex gap-2 overflow-x-auto pb-1 max-md:px-4 [scrollbar-width:none]">
                        {groupTabs.map((group) => {
                            const isActive = group.slug === activeGroup;

                            return (
                                <Link
                                    key={group.slug || 'all'}
                                    to={groupLink(group.slug)}
                                    aria-current={isActive ? 'page' : undefined}
                                    className={`shrink-0 whitespace-nowrap rounded-full border px-4 py-1.5 text-sm transition ${
                                        isActive
                                            ? 'border-primary bg-primary text-white'
                                            : 'border-zinc-200 bg-white text-black/70 hover:border-primary hover:text-primary'
                                    }`}
                                >
                                    {group.name}
                                    {typeof group.brandsCount === 'number' && (
                                        <span
                                            className={`ml-1.5 text-xs ${
                                                isActive
                                                    ? 'text-white/80'
                                                    : 'text-black/40'
                                            }`}
                                        >
                                            {group.brandsCount}
                                        </span>
                                    )}
                                </Link>
                            );
                        })}
                    </div>
                </div>
            )}

            <div
                className={`container ${
                    (pagination?.last_page || 1) < 2 ? '!pb-11' : ''
                } ${groups?.length ? '!pt-6' : '!pt-11'}`}
            >
                <div className="grid grid-cols-3 xs:grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8">
                    {isLoading || isFetching ? (
                        [...Array(12)].map((_, i) => (
                            <CategorySkeleton
                                key={i}
                                className="animate-pulse"
                            />
                        ))
                    ) : !brands?.length ? (
                        <div className="col-span-8 text-center text-black/70  ">
                            <p className="my-10">
                                {translate('No brand found')}
                            </p>
                        </div>
                    ) : (
                        brands?.map((brand) => (
                            <CategoryCard
                                title={brand.name}
                                to={`/brands/${brand.slug}`}
                                image={brand.thumbnailImage}
                                key={brand.id}
                            />
                        ))
                    )}
                </div>

                {pagination && pagination.last_page > 1 && (
                    <Pagination pagination={pagination} />
                )}
            </div>
        </>
    );
};

export default Brands;
