import { useParams, useSearchParams } from 'react-router-dom';
import CampaignCard from '../../components/card/CampaignCard';
import CouponCard from '../../components/card/CouponCard';
import Pagination from '../../components/pagination/Pagination';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetCampaignsQuery } from '../../store/features/api/campaignApi';
import { useGetCouponsQuery } from '../../store/features/api/shopApi';
import { translate } from '../../utils/translate';

const ShopOffers = () => {
    const [searchParams] = useSearchParams();
    const { shopSlug } = useParams<{ shopSlug: string }>();

    const { data: couponsRes } = useGetCouponsQuery({
        shopSlug: shopSlug!,
        query: searchParams.get('query') || '',
    });
    const { data: campaignRes } = useGetCampaignsQuery({
        shopSlug: shopSlug!,
        query: searchParams.get('query') || '',
    });

    const coupons = couponsRes?.result.data;
    const sellerCampaigns = campaignRes?.sellerCampaigns;

    return (
        <div className="space-y-16">
            <section>
                <div className="container">
                    <SectionTitle
                        connector
                        title={translate('[Campaigns]')}
                        classNames={{
                            container: 'mb-8',
                            title: 'ek-h3',
                        }}
                    />

                    {sellerCampaigns?.data.length ? (
                        <div className="grid sm:grid-cols-2 gap-3">
                            {sellerCampaigns?.data.map((campaign) => (
                                <CampaignCard
                                    campaign={campaign}
                                    key={campaign.id}
                                />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center text-black/70">
                            <p className="py-10">
                                {translate('No campaigns found')}
                            </p>
                        </div>
                    )}

                    <Pagination pagination={sellerCampaigns?.meta} />
                </div>
            </section>

            <section>
                <div className="container">
                    <SectionTitle
                        connector
                        title={translate('[Coupons] For You')}
                        classNames={{
                            container: 'mb-8',
                            title: 'ek-h3',
                        }}
                    />

                    {coupons?.length ? (
                        <div className="grid md:grid-cols-2 xl:grid-cols-3 gap-3 text-sm">
                            {coupons?.map((coupon) => (
                                <CouponCard coupon={coupon} key={coupon.id} />
                            ))}
                        </div>
                    ) : (
                        <div className="text-center text-black/70">
                            <p className="py-10">
                                {translate('No coupons found')}
                            </p>
                        </div>
                    )}

                    <Pagination pagination={couponsRes?.result.meta} />
                </div>
            </section>
        </div>
    );
};

export default ShopOffers;
