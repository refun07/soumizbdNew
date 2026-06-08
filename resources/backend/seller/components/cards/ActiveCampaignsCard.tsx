import React, { useEffect, useState } from 'react';
import { paddedNumber } from '../../../../frontend/utils/numberFormatter';
import Image from '../../../react/components/Image';
import { dateFormatter } from '../../../react/utils/dateFormatter';
import { translate } from '../../../react/utils/translate';
import { ICampaign } from '../../types/response';
import { getActiveCampaigns } from '../../utils';

const ActiveCampaignsCard = () => {
    const [campaigns, setCampaigns] = useState<ICampaign[]>([]);

    useEffect(() => {
        getActiveCampaigns().then(setCampaigns);
    }, []);

    return (
        <div className="xl:col-span-4 bg-background rounded-md pt-4 lg:pt-6 xl:pt-8">
            <h5 className="text-sm sm:text-base font-medium px-4 lg:px-6 xl:px-9 mb-6">
                {translate('Active Campaigns')}
                <span className="text-muted ml-2">
                    ({paddedNumber(campaigns.length)})
                </span>
            </h5>

            <div className="max-h-[450px] overflow-y-auto">
                <table className="w-full min-w-[320px]">
                    <thead className="theme-table__head uppercase">
                        <tr>
                            <th>{translate('Campaign Name')}</th>
                            <th className="min-w-[140px] text-end">
                                {translate('Duration')}
                            </th>
                        </tr>
                    </thead>

                    <tbody className="theme-table__body">
                        {!campaigns.length ? (
                            <tr>
                                <td colSpan={2}>
                                    <div className="border border-border p-5 pb-9 flex flex-col items-center gap-3">
                                        <img
                                            src="/images/folder-with-employee.png"
                                            alt=""
                                        />
                                        <h4 className="text-base font-medium text-foreground">
                                            {translate('No campaigns found')}
                                        </h4>
                                        <p>
                                            {translate(
                                                'Let’s create a campaign',
                                            )}
                                            <a
                                                href="/seller/campaigns/create"
                                                className="text-secondary ml-2"
                                            >
                                                {translate('Create Campaign')}
                                            </a>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        ) : (
                            campaigns.map((campaign) => (
                                <tr key={campaign.slug}>
                                    <td className="inline-flex items-center gap-2.5">
                                        <div className="inline-flex items-center gap-4">
                                            <div className="p-1 border border-border rounded-md">
                                                <Image
                                                    src={
                                                        campaign.thumbnailImage
                                                    }
                                                    alt=""
                                                    className="w-[60px] aspect-square rounded-md"
                                                />
                                            </div>

                                            <div className="text-foreground">
                                                <span className="max-w-[230px] line-clamp-2">
                                                    {campaign.name}
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="text-muted text-end">
                                        {dateFormatter(
                                            Number(campaign.startDate) * 1000,
                                        )}{' '}
                                        -{' '}
                                        {dateFormatter(
                                            Number(campaign.endDate) * 1000,
                                        )}
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default ActiveCampaignsCard;
