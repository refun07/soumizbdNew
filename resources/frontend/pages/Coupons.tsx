import { FaHome } from 'react-icons/fa';
import { useSearchParams } from 'react-router-dom';
import Breadcrumb from '../components/Breadcrumb';
import CouponCard from '../components/card/CouponCard';
import Pagination from '../components/pagination/Pagination';
import { CouponSkeleton } from '../components/skeletons/from-svg';
import { useGetCouponsQuery } from '../store/features/api/shopApi';
import { translate } from '../utils/translate';

const Coupons = () => {
    const [searchParams] = useSearchParams();

    const { data, isLoading } = useGetCouponsQuery({
        query: searchParams.get('query') || '',
        page: Number(searchParams.get('page')) || 1,
    });

    const coupons = data?.result?.data || [];

    return (
        <>
            <Breadcrumb
                title={translate('Coupons')}
                navigation={[
                    { name: translate('home'), link: '/', icon: <FaHome /> },
                    { name: translate('Coupons') },
                ]}
            />

            <section className="mt-5">
                <div className="container">
                    <div className="grid lg:grid-cols-2 xl:grid-cols-3 mt-7">
                        {isLoading ? (
                            [...Array(6)].map((_, i) => (
                                <CouponSkeleton
                                    key={i}
                                    className="animate-pulse"
                                />
                            ))
                        ) : !coupons.length ? (
                            <div className="col-span-4 text-center text-black/70  ">
                                <p className="my-10">
                                    {translate('No coupons found')}
                                </p>
                            </div>
                        ) : (
                            coupons?.map((coupon) => (
                                <CouponCard key={coupon.id} coupon={coupon} />
                            ))
                        )}
                    </div>

                    <Pagination pagination={data?.result?.meta} />
                </div>
            </section>
        </>
    );
};

export default Coupons;
