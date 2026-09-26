import { Link } from 'react-router-dom';
import { TBkashPersonalStatus } from '../../types/checkout';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Props {
    orderCode: string;
    status: TBkashPersonalStatus;
    needsSubmission: boolean;
    className?: string;
}

const BkashPersonalNotice = ({
    orderCode,
    status,
    needsSubmission,
    className,
}: Props) => {
    if (needsSubmission) {
        return (
            <div
                className={cn(
                    'rounded-card border border-[#E2136E]/30 bg-[#E2136E]/5 p-4 sm:p-5 my-4 md:my-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3',
                    className,
                )}
            >
                <div>
                    <p className="font-semibold text-[#E2136E]">
                        {status === 'rejected'
                            ? translate('Your bKash payment could not be verified')
                            : translate('Complete your bKash payment')}
                    </p>
                    <p className="text-sm text-black/70">
                        {translate(
                            'Send money via bKash and submit the transaction ID so we can verify your payment.',
                        )}
                    </p>
                </div>
                <Link
                    to={`/orders/bkash-payment/${orderCode}`}
                    className="shrink-0 inline-flex items-center justify-center rounded-button bg-[#E2136E] text-white font-semibold px-5 h-10 text-sm"
                >
                    {translate('Submit bKash Payment')}
                </Link>
            </div>
        );
    }

    return (
        <div
            className={cn(
                'rounded-card border border-zinc-100 bg-stone-50 p-4 my-4 md:my-6 text-sm',
                className,
            )}
        >
            {status === 'verified'
                ? translate('Your bKash payment has been verified.')
                : translate(
                      'Your bKash payment information has been received and is awaiting verification.',
                  )}
        </div>
    );
};

export default BkashPersonalNotice;
