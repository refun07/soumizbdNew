import { FaFire, FaHome } from 'react-icons/fa';
import Breadcrumb from '../../components/Breadcrumb';
import CampaignCard from '../../components/card/CampaignCard';
import Pagination from '../../components/pagination/Pagination';
import CampaignCardSkeleton from '../../components/skeletons/CampaignCardSkeleton';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetCampaignsQuery } from '../../store/features/api/campaignApi';
import { translate } from '../../utils/translate';

const Campaigns = () => {
    const { data: campaignRes, isLoading } = useGetCampaignsQuery();

    const megaCampaigns = campaignRes?.megaCampaigns;
    const sellerCampaigns = campaignRes?.sellerCampaigns?.data;
    const pagination = campaignRes?.sellerCampaigns?.meta;

    return (
        <>
            <Breadcrumb
                title={translate('Campaigns')}
                navigation={[
                    { name: translate('home'), link: '/', icon: <FaHome /> },
                    { name: translate('Campaigns') },
                ]}
            />

            <section className="mt-5">
                <div className="container">
                    <SectionTitle
                        title={translate('[Mega] Campaigns')}
                        connector
                    />

                    <div className="grid sm:grid-cols-2 gap-x-3 gap-y-9 mt-7">
                        {isLoading ? (
                            Array.from({ length: 2 }).map((_, i) => (
                                <CampaignCardSkeleton key={i} />
                            ))
                        ) : !megaCampaigns?.length ? (
                            <p className="py-2 text-center sm:col-span-2">
                                {translate('No mega campaigns found')}
                            </p>
                        ) : (
                            megaCampaigns?.map((campaign) => (
                                <div key={campaign.id} className="mb-4">
                                    <CampaignCard campaign={campaign} />
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </section>

            {window.config.generalSettings.appMode == 'multiVendor' && (
                <section className="mt-10">
                    <div className="container">
                        <SectionTitle
                            title={translate('[Seller] Campaigns')}
                            connector
                        />

                        <div className="grid sm:grid-cols-2 gap-x-3 gap-y-9 mt-7">
                            {isLoading ? (
                                Array.from({ length: 2 }).map((_, i) => (
                                    <CampaignCardSkeleton key={i} />
                                ))
                            ) : !sellerCampaigns?.length ? (
                                <p className="py-2 text-center sm:col-span-2">
                                    {translate('No campaigns found')}
                                </p>
                            ) : (
                                sellerCampaigns?.map((campaign) => (
                                    <div key={campaign.id} className="mb-4">
                                        <CampaignCard campaign={campaign} />
                                    </div>
                                ))
                            )}
                        </div>

                        <Pagination pagination={pagination} />
                    </div>
                </section>
            )}
        </>
    );
};

export default Campaigns;
