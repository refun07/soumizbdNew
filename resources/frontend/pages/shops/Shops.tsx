import { useEffect } from 'react';
import { FaHome } from 'react-icons/fa';
import { useSearchParams } from 'react-router-dom';
import Breadcrumb from '../../components/Breadcrumb';
import CategoryCard from '../../components/category-card';
import SearchForm from '../../components/inputs/SearchForm';
import Pagination from '../../components/pagination/Pagination';
import { CategorySkeleton } from '../../components/skeletons/from-svg';
import { useLazyGetShopsQuery } from '../../store/features/api/shopApi';
import { IGetsQueryParams } from '../../types';
import { translate } from '../../utils/translate';

const Shops = () => {
    const [searchParams] = useSearchParams();
    const [getShops, { data, isLoading }] = useLazyGetShopsQuery();

    const filters: IGetsQueryParams = {
        page: searchParams.get('page') || 1,
        query: searchParams.get('query') || '',
    };

    useEffect(() => {
        getShops(filters, true);
    }, [searchParams]);

    const shops = data?.result.data;

    return (
        <>
            <Breadcrumb
                title={translate('All Shops')}
                navigation={[
                    {
                        icon: <FaHome />,
                        name: translate('Home'),
                        link: '/',
                    },
                    {
                        name: translate('Shops'),
                    },
                ]}
            />

            <div className="container no-style flex items-center justify-between my-6">
                <div className="w-full max-w-[280px]">
                    <SearchForm
                        searchOnType
                        suggestions={false}
                        path="/shops"
                        placeholder={translate('Search The Shops')}
                    />
                </div>

                <div></div>
            </div>

            <section className="mt-6">
                <div className="container">
                    <div className="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 lg:grid-cols-7 gap-[14px]">
                        {isLoading ? (
                            [...Array(18)].map((_, i) => (
                                <CategorySkeleton
                                    key={i}
                                    className="animate-pulse"
                                />
                            ))
                        ) : !shops?.length ? (
                            <div className="col-span-6 text-center text-black/70  ">
                                <p className="my-10">
                                    {translate('No shops found')}
                                </p>
                            </div>
                        ) : (
                            shops?.map((shop) => (
                                <CategoryCard
                                    title={shop.name}
                                    to={`/shops/${shop.slug}`}
                                    image={shop.logo}
                                    key={shop.id}
                                />
                                // <ShopCard shop={shop} key={shop.id} />
                            ))
                        )}
                    </div>

                    {data && data.result.meta.last_page > 1 && (
                        <Pagination pagination={data.result.meta} />
                    )}
                </div>
            </section>
        </>
    );
};

export default Shops;
