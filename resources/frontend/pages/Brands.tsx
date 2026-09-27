import { FaHome } from 'react-icons/fa';
import { Link, useSearchParams } from 'react-router-dom';
import Breadcrumb from '../components/Breadcrumb';
import Image from '../components/Image';
import SearchForm from '../components/inputs/SearchForm';
import { CategorySkeleton } from '../components/skeletons/from-svg';
import { useGetBrandsQuery } from '../store/features/api/brandApi';
import { translate } from '../utils/translate';

const Brands = () => {
    const [searchParams] = useSearchParams();

    const { data, isLoading } = useGetBrandsQuery({
        query: searchParams.get('query') || '',
        page: 1,
        limit: 1000,
    });

    const brands = data?.result?.data;

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

            <div className="container !py-11">
                <div className="grid grid-cols-3 gap-x-4 gap-y-10 xs:grid-cols-4 sm:grid-cols-5 md:grid-cols-6 lg:grid-cols-8">
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
                            <Link
                                key={brand.id}
                                to={`/brands/${brand.slug}`}
                                className="group flex min-h-[170px] flex-col items-center justify-start px-2 text-center"
                                title={brand.name}
                            >
                                <div className="flex h-[104px] w-full items-center justify-center">
                                    <Image
                                        src={brand.thumbnailImage}
                                        alt={brand.name}
                                        className="max-h-full max-w-[92%] object-contain transition duration-300 group-hover:scale-105"
                                    />
                                </div>
                                <h3 className="mt-5 max-w-full whitespace-normal break-words text-sm font-medium leading-snug text-zinc-950 md:text-base">
                                    {brand.name}
                                </h3>
                            </Link>
                        ))
                    )}
                </div>
            </div>
        </>
    );
};

export default Brands;
