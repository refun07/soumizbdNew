import {
    FaBox,
    FaCheck,
    FaClipboardCheck,
    FaHome,
    FaTruck,
} from 'react-icons/fa';
import SvgOrderSuccess from '../icons/OrderSuccess';
import { IOrderSuccess } from '../../types/checkout';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

interface Props {
    data?: IOrderSuccess;
}

const fallbackSteps = [
    { label: 'Placed', icon: FaClipboardCheck, active: true },
    { label: 'Accepted', icon: FaCheck, active: false },
    { label: 'Packed', icon: FaBox, active: false },
    { label: 'Shipped', icon: FaTruck, active: false },
    { label: 'Delivered', icon: FaHome, active: false },
];

const applyTemplate = (
    template: string | undefined,
    replacements: Record<string, string>,
) => {
    return Object.entries(replacements).reduce(
        (text, [key, value]) => text.replaceAll(`{${key}}`, value),
        template || '',
    );
};

const ConfigurableOrderSuccessHero = ({ data }: Props) => {
    const config = data?.successPage;
    const summary = data?.summary;

    if (!config) {
        return (
            <section className="mx-auto max-w-[860px] py-10 text-center">
                <div className="mx-auto h-8 w-2/3 max-w-[520px] animate-pulse rounded bg-slate-100" />
                <div className="mx-auto mt-3 h-4 w-1/2 max-w-[420px] animate-pulse rounded bg-slate-100" />
                <div className="mx-auto mt-8 h-16 w-16 animate-pulse rounded-full bg-slate-100" />
                <div className="mx-auto mt-7 h-12 w-full max-w-[760px] animate-pulse rounded bg-slate-100" />
            </section>
        );
    }

    if (!config?.customEnabled) {
        return (
            <div className="flex flex-col items-center mx-auto py-10 max-w-[480px]">
                <div className="w-full">
                    <SvgOrderSuccess className="w-full max-w-[470px] mx-auto" />
                </div>
                <h2 className="ek-h2 text-center">
                    {translate('Thank you. Your order has been received.')}
                </h2>
                <p className="mt-2 text-center">
                    {translate(
                        'We have emailed your order confirmation, and will send you an update when your order has shipped.',
                    )}
                </p>
            </div>
        );
    }

    const orderTotal = currencyFormatter(summary?.totalAmount || 0);
    const orderCode = summary?.orderCode || '';
    const orderNumberText = applyTemplate(config.orderNumberTemplate, {
        order_code: orderCode,
        order_total: orderTotal,
    });
    const orderTotalText = applyTemplate(config.orderTotalTemplate, {
        order_code: orderCode,
        order_total: orderTotal,
    });

    return (
        <section className="mx-auto max-w-[860px] py-8 text-center">
            <h1
                className="text-2xl font-extrabold leading-tight sm:text-3xl md:text-4xl"
                style={{ color: config.headlineColor }}
            >
                {config.headline}
            </h1>

            <p
                className="mt-1 text-sm font-semibold sm:text-base"
                style={{ color: config.subtitleColor }}
            >
                {config.subtitle}
            </p>

            <div className="mt-8 flex justify-center">
                {config.centerImage ? (
                    <img
                        src={config.centerImage}
                        alt=""
                        className="h-16 w-16 object-contain sm:h-20 sm:w-20"
                    />
                ) : (
                    <div className="flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-2xl text-emerald-600 sm:h-20 sm:w-20">
                        <FaCheck />
                    </div>
                )}
            </div>

            <div className="mt-5 space-y-2 text-sm font-semibold sm:text-base">
                <p className="text-slate-700">{orderNumberText}</p>
                <p style={{ color: config.orderTotalColor }}>
                    {orderTotalText}
                </p>
            </div>

            <div className="mt-7">
                {config.stepsImage ? (
                    <img
                        src={config.stepsImage}
                        alt=""
                        className="mx-auto w-full max-w-[760px] object-contain"
                    />
                ) : (
                    <div className="mx-auto grid max-w-[760px] grid-cols-5 items-start gap-1">
                        {fallbackSteps.map(
                            ({ label, icon: Icon, active }, index) => (
                                <div
                                    className="relative flex flex-col items-center gap-1 text-[10px] font-semibold text-slate-500 sm:text-xs"
                                    key={label}
                                >
                                    {index > 0 && (
                                        <span className="absolute right-1/2 top-4 h-px w-full bg-slate-200" />
                                    )}
                                    <span
                                        className={
                                            active
                                                ? 'relative z-[1] flex h-8 w-8 items-center justify-center rounded-full bg-teal-100 text-teal-600'
                                                : 'relative z-[1] flex h-8 w-8 items-center justify-center rounded-full bg-slate-200 text-slate-500'
                                        }
                                    >
                                        <Icon />
                                    </span>
                                    <span>{translate(label)}</span>
                                </div>
                            ),
                        )}
                    </div>
                )}
            </div>
        </section>
    );
};

export default ConfigurableOrderSuccessHero;
