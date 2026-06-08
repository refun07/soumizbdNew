import { Link } from 'react-router-dom';
import { ICampaign } from '../../types/campaign';
import CountDown from '../CountDown';
import Image from '../Image';

interface Props {
    campaign: ICampaign;
}

const CampaignCard = ({ campaign }: Props) => {
    return (
        <div>
            <Link to={`/campaigns/${campaign.slug}`} className="block">
                <Image
                    src={campaign.thumbnailImage}
                    alt={campaign.name}
                    className="rounded-card w-full"
                />
            </Link>
            <div className="flex max-lg:flex-col gap-3 justify-between lg:items-center mt-3">
                <CountDown
                    label="This Deal Ends In"
                    date={new Date(Number(campaign.endDate) * 1000)}
                    className="max-lg:order-2 max-lg:flex-col max-lg:items-start max-lg:gap-1 mt-0"
                />

                {campaign.shop && (
                    <Link
                        to={`/shops/${campaign.shop.slug}`}
                        className="max-lg:order-1 text-primary inline-block max-lg:text-base"
                    >
                        {campaign.shop.name}
                    </Link>
                )}
            </div>
        </div>
    );
};

export default CampaignCard;
