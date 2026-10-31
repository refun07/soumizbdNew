import { CSSProperties, useEffect, useState } from 'react';
import { LuArrowRight, LuZap } from 'react-icons/lu';
import { Link } from 'react-router-dom';
import { useGetBotanicalFlashSaleQuery } from '../../store/features/api/homeApis/botanicalApi';
import { saleCountdown } from '../../utils/saleCountdown';
import BotanicalProductSlider from './BotanicalProductSlider';
import { getHomeContent } from './homeContent';

export default function BotanicalFlashSale() {
    const content = getHomeContent().flash;
    const sale = useGetBotanicalFlashSaleQuery(undefined, {
        skip: !content.enabled,
    });
    const [now, setNow] = useState(Date.now());
    const endsAt = sale.data?.endsAt ?? null;
    const countdown = saleCountdown(endsAt, now);
    useEffect(() => {
        setNow(Date.now());
        if (!endsAt) return;
        const interval = window.setInterval(() => setNow(Date.now()), 1000);
        return () => window.clearInterval(interval);
    }, [endsAt]);

    if (
        !content.enabled ||
        countdown?.expired ||
        sale.data?.active === false ||
        (sale.isSuccess && !sale.data?.products.length)
    )
        return null;

    const rgb = content.background
        .match(/[a-f\d]{2}/gi)
        ?.map((value) => parseInt(value, 16)) ?? [179, 5, 42];
    const light = rgb[0] * 0.299 + rgb[1] * 0.587 + rgb[2] * 0.114 > 155;
    const style = {
        backgroundColor: content.background,
        '--bot-sale-ink': light ? '#202839' : '#ffffff',
    } as CSSProperties;
    return (
        <section
            className="bot-container bot-flash-sale-section"
            id="flash-sale"
            aria-labelledby="flash-sale-title"
        >
            <div className="bot-flash-sale" style={style}>
                <div className="bot-flash-sale-heading">
                    <div>
                        <p className="bot-flash-sale-eyebrow">
                            <LuZap aria-hidden="true" />
                            {content.eyebrow}
                        </p>
                        <h2 id="flash-sale-title">{content.title}</h2>
                    </div>
                    {countdown && (
                        <div
                            className="bot-sale-countdown"
                            role="timer"
                            aria-live="off"
                            aria-label={`Sale ends in ${countdown.days} days, ${countdown.hours} hours, ${countdown.minutes} minutes and ${countdown.seconds} seconds`}
                        >
                            {[
                                [countdown.days, 'Days'],
                                [countdown.hours, 'Hours'],
                                [countdown.minutes, 'Min'],
                                [countdown.seconds, 'Sec'],
                            ].map(([value, label]) => (
                                <div key={label}>
                                    <strong>
                                        {String(value).padStart(2, '0')}
                                    </strong>
                                    <span>{label}</span>
                                </div>
                            ))}
                        </div>
                    )}
                    <Link className="bot-sale-more" to={content.link}>
                        {content.button_label}
                        <LuArrowRight aria-hidden="true" />
                    </Link>
                </div>
                <p className="bot-flash-sale-description">
                    {content.description}
                </p>
                {sale.isError ? (
                    <div className="bot-sale-error" role="alert">
                        We couldn’t load these products.{' '}
                        <button onClick={() => sale.refetch()}>
                            Try again
                        </button>
                    </div>
                ) : sale.isLoading ? (
                    <div
                        className="bot-loading-grid"
                        role="status"
                        aria-label="Loading sale products"
                    >
                        {[0, 1, 2].map((i) => (
                            <div className="bot-skeleton" key={i} />
                        ))}
                    </div>
                ) : (
                    <BotanicalProductSlider
                        products={sale.data?.products ?? []}
                        label="Flash sale products slider"
                        desktopSlides={4}
                    />
                )}
            </div>
        </section>
    );
}
