import { FaHome } from 'react-icons/fa';
import { useSearchParams } from 'react-router-dom';
import Breadcrumb from '../components/Breadcrumb';
import CategoryCard from '../components/category-card';
import SearchForm from '../components/inputs/SearchForm';
import Pagination from '../components/pagination/Pagination';
import { CategorySkeleton } from '../components/skeletons/from-svg';
import { useGetBrandsQuery } from '../store/features/api/brandApi';
import { translate } from '../utils/translate';

const Brands = () => {
    const [searchParams] = useSearchParams();

    const { data, isLoading } = useGetBrandsQuery({
        query: searchParams.get('query') || '',
        page: Number(searchParams.get('page')) || 1,
    });

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

            <div
                className={`container ${
                    (pagination?.last_page || 1) < 2 ? '!py-11' : '!pt-11'
                }`}
            >
                <div className="grid grid-cols-3 xs:grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8">
                    {isLoading ? (
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
