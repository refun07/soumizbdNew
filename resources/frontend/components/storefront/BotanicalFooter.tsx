import { LuClock3, LuGem, LuHeadphones, LuTruck, LuCreditCard, LuLeaf } from 'react-icons/lu';
import {
    FaFacebookF,
    FaInstagram,
    FaPinterestP,
    FaYoutube,
} from 'react-icons/fa';
import { Link, useLocation } from 'react-router-dom';
import { useGetFooterQuery } from '../../store/features/api/homeApis/commonHomeApi';
import { getHomeContent } from './homeContent';
import { BotanicalLogo } from './BotanicalHeader';

export default function BotanicalFooter() {
    const { data } = useGetFooterQuery();
    const content = getHomeContent();
    const isHomepage = useLocation().pathname === '/';
    const accountLinks = [
        content.account1,
        content.account2,
        content.account3,
        content.account4,
    ].filter((link) => link.enabled);
    const exploreLinks = [
        content.explore1,
        content.explore2,
        content.explore3,
        content.explore4,
    ].filter((link) => link.enabled);
    const social = [
        {
            Icon: FaFacebookF,
            title: 'Facebook',
            enabled: data?.showHomeFacebookLink,
            href: data?.homeFooterFacebookLink,
        },
        {
            Icon: FaInstagram,
            title: 'Instagram',
            enabled: data?.showHomeInstagramLink,
            href: data?.homeFooterInstagramLink,
        },
        {
            Icon: FaPinterestP,
            title: 'Pinterest',
            enabled: data?.showHomePinterestLink,
            href: data?.homeFooterPinterestLink,
        },
        {
            Icon: FaYoutube,
            title: 'YouTube',
            enabled: data?.showHomeYoutubeLink,
            href: data?.homeFooterYoutubeLink,
        },
    ].filter((link) => link.enabled === '1' && link.href);
    return (
        <>
            {content.footer.services_enabled && (
                <section
                    className="bot-services bot-container"
                    aria-label="Shopping with us"
                >
                    {[
                        {
                            Icon: LuTruck,
                            title: 'Delivered with care',
                            description: 'Your favourites, right to your door.',
                        },
                        {
                            Icon: LuHeadphones,
                            title: 'Here to help',
                            description:
                                'A little guidance for your daily ritual.',
                        },
                        {
                            Icon: LuClock3,
                            title: 'Shop at your pace',
                            description: 'Discover something you’ll love.',
                        },
                        {
                            Icon: LuGem,
                            title: 'Secure checkout',
                            description:
                                'A little peace of mind with every order.',
                        },
                    ].map(({ Icon, title, description }, index) => (
                        <div key={title}>
                            <Icon aria-hidden="true" />
                            <h3>
                                {data?.homeFooterInfoItems?.[index]?.title ||
                                    title}
                            </h3>
                            <p>
                                {data?.homeFooterInfoItems?.[index]
                                    ?.description || description}
                            </p>
                        </div>
                    ))}
                </section>
            )}
            {isHomepage && content.benefits.enabled && (
                <section className="bot-home--refined">
                <section className="bot-values" aria-label="Why shop with us">
                    <div className="bot-container bot-values-inner">
                        {[
                            { Icon: LuTruck, ...content.benefit1 },
                            { Icon: LuCreditCard, ...content.benefit2 },
                            { Icon: LuLeaf, ...content.benefit3 },
                        ].map(({ Icon, title, text }) => (
                            <div className="bot-value" key={title}>
                                <span className="bot-value-icon">
                                    <Icon aria-hidden="true" />
                                </span>
                                <div>
                                    <h2>{title}</h2>
                                    <p>{text}</p>
                                </div>
                            </div>
                        ))}
                    </div>
                </section>
                </section>
            )}
            <footer className="bot-footer" id="contact">
                <div className="bot-container bot-footer-grid">
                    <div className="bot-footer-about">
                        <BotanicalLogo footer />
                        <p>
                            {data?.homeFooterDescription ||
                                'A little care. A little confidence. Discover everyday beauty, inspired by nature and made for you.'}
                        </p>
                        {data?.homeFooterEmail && (
                            <a href={`mailto:${data.homeFooterEmail}`}>
                                {data.homeFooterEmail}
                            </a>
                        )}
                        {data?.homeFooterCustomerSupport && (
                            <a
                                href={`tel:${data.homeFooterCustomerSupport.replace(
                                    /[^+\d]/g,
                                    '',
                                )}`}
                            >
                                {data.homeFooterCustomerSupport}
                            </a>
                        )}
                        {data?.homeFooterAddress && (
                            <p>{data.homeFooterAddress}</p>
                        )}
                        {social.length > 0 && (
                            <div className="bot-socials">
                                {social.map(({ Icon, title, href }) => (
                                    <a
                                        href={href}
                                        key={title}
                                        aria-label={title}
                                        target="_blank"
                                        rel="noreferrer"
                                    >
                                        <Icon />
                                    </a>
                                ))}
                            </div>
                        )}
                    </div>
                    <div>
                        <h2>{content.footer.account_heading}</h2>
                        {accountLinks.map((link, index) => (
                            <Link key={index} to={link.link}>
                                {link.label}
                            </Link>
                        ))}
                    </div>
                    <div>
                        <h2>{content.footer.explore_heading}</h2>
                        {exploreLinks.map((link, index) => (
                            <Link key={index} to={link.link}>
                                {link.label}
                            </Link>
                        ))}
                        {data?.defaultPages?.map((page) => (
                            <Link key={page.id} to={`/pages/${page.slug}`}>
                                {page.title}
                            </Link>
                        ))}
                        {data?.quickLinkPages?.map((page) => (
                            <Link key={page.url} to={page.url}>
                                {page.title}
                            </Link>
                        ))}
                    </div>
                </div>
                <div className="bot-container bot-copyright">
                    <span>
                        {data?.homeFooterCopyrightText ||
                            `© ${new Date().getFullYear()} ${
                                window.config.generalSettings.appName ||
                                'Shomiz'
                            }. All rights reserved.`}
                    </span>
                    {data?.homeFooterSecuredPayments && (
                        <img
                            src={data.homeFooterSecuredPayments}
                            alt="Accepted payment methods"
                            loading="lazy"
                            width="180"
                            height="26"
                        />
                    )}
                </div>
            </footer>
        </>
    );
}
