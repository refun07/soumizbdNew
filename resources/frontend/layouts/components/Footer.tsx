import { FormEvent, useState } from 'react';
import toast from 'react-hot-toast';
import { FaArrowRight } from 'react-icons/fa';
import {
    FaArrowRotateLeft,
    FaClock,
    FaCoins,
    FaLocationDot,
    FaTruckFast,
} from 'react-icons/fa6';
import {
    RiFacebookCircleLine,
    RiInstagramLine,
    RiLinkedinLine,
    RiPinterestLine,
    RiTiktokLine,
    RiTwitterXLine,
    RiYoutubeLine,
} from 'react-icons/ri';
import { Link } from 'react-router-dom';
import { SwiperSlide } from 'swiper/react';
import { object, string } from 'yup';
import Button from '../../components/buttons/Button';
import ThemeSlider from '../../components/slider/ThemeSlider';
import SectionTitle from '../../components/titles/SectionTitle';
import {
    useGetFeaturedBrandsQuery,
    useGetFooterQuery,
} from '../../store/features/api/homeApis/commonHomeApi';
import { useSubscribeMutation } from '../../store/features/api/userApi';
import { translate } from '../../utils/translate';

const DEMO_ONE_LINKS = [
    { title: 'Home', url: '/' },
    { title: 'Shop', url: '/shops' },
    { title: 'All Products', url: '/products' },
    { title: 'About Us', url: '/pages/about-us' },
    { title: 'All Brands', url: '/brands' },
    { title: 'All Sellers', url: '/shops' },
];

const FooterTitle = ({
    title,
    style,
}: {
    title: string;
    style: React.CSSProperties;
}) => (
    <div>
        <p className="text-[16px] font-semibold whitespace-nowrap" style={style}>
            {title}
        </p>
        <div className="mt-3 h-px w-full bg-white/15">
            <span className="block h-[3px] w-[54px] bg-[#ff2718]" />
        </div>
    </div>
);

const Footer = () => {
    const { data: footerData } = useGetFooterQuery();
    const [subscribe] = useSubscribeMutation();

    const featuredBrandsRes = useGetFeaturedBrandsQuery();

    const [email, setEmail] = useState('');
    const importantLinks = DEMO_ONE_LINKS;
    const quickLinks = DEMO_ONE_LINKS;
    const footerEmails = (footerData?.homeFooterEmail || 'maininfo@ultraz.com')
        .split(',')
        .map((item: string) => item.trim())
        .filter(Boolean);
    const supportNumbers = [
        footerData?.homeFooterCustomerSupport,
        footerData?.homeFooterCustomerSupportSecondary,
    ].filter((number): number is string => Boolean(number?.trim())).length
        ? [
              footerData?.homeFooterCustomerSupport,
              footerData?.homeFooterCustomerSupportSecondary,
          ].filter((number): number is string => Boolean(number?.trim()))
        : ['+88 017 00 000 0000'];
    const defaultFooterInfoItems = [
        {
            title: translate('Return Policy'),
            description: translate(
                'If a product is damaged, faulty, or different than ordered, we offer a return for that product.',
            ),
        },
        {
            title: translate('Delivery System'),
            description: translate(
                'We provide cash on delivery. You can check and try the product at the delivery point before receiving.',
            ),
        },
        {
            title: translate('Delivery Charges'),
            description: `${translate('Delivery charges are as follows:')}\n${translate('Inside Dhaka: 90TK')}\n${translate('Outside Dhaka: 150TK')}`,
        },
        {
            title: translate('Delivery Time'),
            description: `${translate('Delivery Time is as follows:')}\n${translate('Inside Dhaka: 2-3 Days')}\n${translate('Outside Dhaka: 4-5 Days')}`,
        },
    ];
    const infoItems =
        footerData?.homeFooterInfoItems?.length
            ? footerData.homeFooterInfoItems
            : defaultFooterInfoItems;
    const infoIcons = [FaArrowRotateLeft, FaTruckFast, FaCoins, FaClock];
    const isModernFooter = footerData?.defaultFooter === 'footer-two';
    const settingIsEnabled = (value: unknown) =>
        value === '1' || value === 1 || value === true;
    const footerAddress =
        footerData?.homeFooterAddress ||
        '123 Maple Street Springfield, IL 627010 United States of America';
    const footerLogo =
        footerData?.homeFooterLogo || window.config.generalSettings.logo;
    const paymentImage = footerData?.homeFooterSecuredPayments;
    const paymentImageSizeClass =
        footerData?.homeFooterSecuredPaymentsSize === 'small'
            ? 'h-[20px]'
            : footerData?.homeFooterSecuredPaymentsSize === 'large'
              ? 'h-[40px]'
              : 'h-[32px]';
    const footerCompanyInfoAlignment =
        footerData?.homeFooterCompanyInfoAlignment === 'center' ||
        footerData?.homeFooterCompanyInfoAlignment === 'right' ||
        footerData?.homeFooterCompanyInfoAlignment === 'justify'
            ? footerData.homeFooterCompanyInfoAlignment
            : 'left';

    const validationSchema = object().shape({
        email: string().email().required('Email is required'),
    });

    const handleSubscribe = async (e: FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        const isValid = validationSchema.isValidSync({ email });

        if (!isValid) {
            toast.error(translate('Invalid email'));
            return;
        }

        try {
            await subscribe(email);
            toast.success(translate('Subscribed'));
            setEmail('');
        } catch (error: any) {
            toast.error(error.data.message);
        }
    };

    return (
        <>
            {/* start::featured-brands */}
            {featuredBrandsRes.data?.showFeaturedBrands === '1' ? (
                <>
                    <section className="featured-brands-section bg-secondary/20 py-6 md:py-8">
                        <div className="container lg:flex lg:items-center gap-10 max-lg:space-y-6">
                            <div>
                                <SectionTitle
                                    animated
                                    title={translate('[Brands] To Follow')}
                                    subTitle={translate('Popular')}
                                    connector={false}
                                    className="mb-5 whitespace-nowrap"
                                />
                                <Button
                                    as="link"
                                    to={'/brands'}
                                    variant="outline"
                                    className="from-transparent"
                                >
                                    {translate('View More')}
                                </Button>
                            </div>

                            <ThemeSlider
                                containerClass="grow"
                                wrapperClass="items-start"
                                spaceBetween={20}
                                slidesPerView={2}
                                breakpoints={{
                                    680: {
                                        slidesPerView: 4,
                                    },
                                    1024: {
                                        slidesPerView: 5,
                                    },
                                    1280: {
                                        slidesPerView: 6,
                                    },
                                }}
                            >
                                {featuredBrandsRes.data?.brands.map((brand) => (
                                    <SwiperSlide
                                        key={brand.id}
                                        className="inline-flex justify-center"
                                    >
                                        <Link
                                            to={`/brands/${brand.slug}`}
                                            className="flex w-full min-w-0 flex-col items-center gap-3 px-1 text-center"
                                            aria-label={brand.name}
                                        >
                                            <img
                                                src={brand.thumbnailImage}
                                                alt={brand.name}
                                                width={112}
                                                height={112}
                                                loading="lazy"
                                                className="h-20 w-20 object-contain sm:h-24 sm:w-24 lg:h-28 lg:w-28"
                                            />
                                            <span className="w-full whitespace-normal break-words text-sm font-medium leading-5 text-foreground">
                                                {brand.name}
                                            </span>
                                        </Link>
                                    </SwiperSlide>
                                ))}
                            </ThemeSlider>
                        </div>
                    </section>
                </>
            ) : null}
            {/* start::featured-brands */}

            {isModernFooter ? (
                <>
                    {settingIsEnabled(footerData?.showHomeFooterInfoBar) && (
                        <section
                            className="py-14"
                            style={{
                                backgroundColor:
                                    footerData?.homeFooterInfoBgColor ||
                                    '#ffffff',
                            }}
                        >
                            <div className="container grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-10">
                                {infoItems.slice(0, 4).map((item, index) => {
                                    const Icon = infoIcons[index] || FaClock;

                                    return (
                                        <div
                                            key={`${item.title}-${index}`}
                                            className="text-center"
                                        >
                                            <Icon
                                                className="text-4xl mx-auto mb-5"
                                                style={{
                                                    color:
                                                        footerData?.homeFooterInfoIconColor ||
                                                        '#8f5847',
                                                }}
                                            />
                                            <h3
                                                className="text-xl font-semibold mb-3"
                                                style={{
                                                    color:
                                                        footerData?.homeFooterInfoTitleColor ||
                                                        '#111827',
                                                }}
                                            >
                                                {item.title}
                                            </h3>
                                            <p
                                                className="text-sm leading-6 whitespace-pre-line max-w-[320px] mx-auto"
                                                style={{
                                                    color:
                                                        footerData?.homeFooterInfoTextColor ||
                                                        '#0f172a',
                                                }}
                                            >
                                                {item.description}
                                            </p>
                                        </div>
                                    );
                                })}
                            </div>
                        </section>
                    )}

                    <footer
                        className="py-16 lg:py-20"
                        style={{
                            backgroundColor:
                                footerData?.footer_bg_color || '#9b7655',
                            color: footerData?.footer_text_color || '#ffffff',
                        }}
                    >
                        <div className="container grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-start">
                            <div className="max-w-2xl">
                                {footerData?.homeFooterLogo && (
                                    <img
                                        src={footerData.homeFooterLogo}
                                        alt=""
                                        className={
                                            footerData?.homeFooterLogoSize ===
                                            'small'
                                                ? 'h-12'
                                                : footerData?.homeFooterLogoSize ===
                                                  'large'
                                                ? 'h-24'
                                                : 'h-[72px]'
                                        }
                                    />
                                )}

                                {footerData?.homeFooterDescription && (
                                    <p
                                        className={
                                            footerData?.homeFooterLogo
                                                ? 'mt-8 text-base leading-8 whitespace-pre-line'
                                                : 'text-base leading-8 whitespace-pre-line'
                                        }
                                        style={{
                                            textAlign:
                                                footerCompanyInfoAlignment,
                                        }}
                                    >
                                        {footerData.homeFooterDescription}
                                    </p>
                                )}
                            </div>

                            <div className="max-w-2xl">
                                <h3 className="text-xl font-semibold mb-8">
                                    {translate('Contact')}
                                </h3>

                                {footerData?.homeFooterAddress && (
                                    <p className="text-base leading-8 mb-5">
                                        <span className="font-medium">
                                            {translate('Address')}:
                                        </span>{' '}
                                        {footerData.homeFooterAddress}
                                    </p>
                                )}

                                {footerData?.homeFooterEmail && (
                                    <div className="space-y-2 mb-5">
                                        {footerData.homeFooterEmail
                                            .split(',')
                                            .map((emailAddress: string) =>
                                                emailAddress.trim(),
                                            )
                                            .filter(
                                                (emailAddress: string) =>
                                                    emailAddress.length > 0,
                                            )
                                            .map(
                                                (
                                                    emailAddress: string,
                                                    index: number,
                                                ) => (
                                                    <a
                                                        key={index}
                                                        href={`mailto:${emailAddress}`}
                                                        className="block hover:opacity-80 transition-opacity"
                                                    >
                                                        <span className="font-medium">
                                                            {translate('Email')}:
                                                        </span>{' '}
                                                        {emailAddress}
                                                    </a>
                                                ),
                                            )}
                                    </div>
                                )}

                                {supportNumbers.length > 0 && (
                                    <div className="space-y-2 mb-8">
                                        {supportNumbers.map(
                                            (phoneNumber, index) => (
                                                <a
                                                    key={index}
                                                    href={`tel:${phoneNumber}`}
                                                    className="block hover:opacity-80 transition-opacity"
                                                >
                                                    <span className="font-medium">
                                                        {translate('Call us')}:
                                                    </span>{' '}
                                                    {phoneNumber}
                                                </a>
                                            ),
                                        )}
                                    </div>
                                )}

                                <div className="flex gap-5 items-center text-2xl">
                                    {settingIsEnabled(
                                        footerData?.showHomeFacebookLink,
                                    ) && (
                                        <a
                                            href={
                                                footerData.homeFooterFacebookLink
                                            }
                                            target="_blank"
                                            className="hover:opacity-80 transition-opacity"
                                        >
                                            <RiFacebookCircleLine />
                                        </a>
                                    )}
                                    {settingIsEnabled(
                                        footerData?.showHomeInstagramLink,
                                    ) && (
                                        <a
                                            href={
                                                footerData.homeFooterInstagramLink
                                            }
                                            target="_blank"
                                            className="hover:opacity-80 transition-opacity"
                                        >
                                            <RiInstagramLine />
                                        </a>
                                    )}
                                    {settingIsEnabled(
                                        footerData?.showHomeTwitterLink,
                                    ) && (
                                        <a
                                            href={
                                                footerData.homeFooterTwitterLink
                                            }
                                            target="_blank"
                                            className="hover:opacity-80 transition-opacity"
                                        >
                                            <RiTwitterXLine />
                                        </a>
                                    )}
                                    {settingIsEnabled(
                                        footerData?.showHomeYoutubeLink,
                                    ) && (
                                        <a
                                            href={
                                                footerData.homeFooterYoutubeLink
                                            }
                                            target="_blank"
                                            className="hover:opacity-80 transition-opacity"
                                        >
                                            <RiYoutubeLine />
                                        </a>
                                    )}
                                    {settingIsEnabled(
                                        footerData?.showHomeLinkedInLink,
                                    ) && (
                                        <a
                                            href={
                                                footerData.homeFooterLinkedInLink
                                            }
                                            target="_blank"
                                            className="hover:opacity-80 transition-opacity"
                                        >
                                            <RiLinkedinLine />
                                        </a>
                                    )}
                                    {settingIsEnabled(
                                        footerData?.showHomeTikTokLink,
                                    ) && (
                                        <a
                                            href={
                                                footerData.homeFooterTikTokLink
                                            }
                                            target="_blank"
                                            className="hover:opacity-80 transition-opacity"
                                        >
                                            <RiTiktokLine />
                                        </a>
                                    )}
                                    {settingIsEnabled(
                                        footerData?.showHomePinterestLink,
                                    ) && (
                                        <a
                                            href={
                                                footerData.homeFooterPinterestLink
                                            }
                                            target="_blank"
                                            className="hover:opacity-80 transition-opacity"
                                        >
                                            <RiPinterestLine />
                                        </a>
                                    )}
                                </div>
                            </div>
                        </div>
                    </footer>
                </>
            ) : (
            <footer
                className="pt-14 pb-16 lg:pt-[62px] lg:pb-[82px]"
                style={{
                    backgroundColor: footerData?.footer_bg_color || '#0d0d0d',
                    color: footerData?.footer_text_color || '#ffffff',
                }}
            >
                <div className="container">
                    <div className="grid gap-10 lg:grid-cols-[minmax(420px,1.5fr)_minmax(170px,0.55fr)_minmax(170px,0.55fr)_minmax(300px,0.85fr)] xl:gap-12 2xl:gap-16">
                        <div className="w-full">
                            {footerLogo && (
                                <img
                                    src={footerLogo}
                                    alt="Raisa Mart"
                                    className={
                                        footerData?.homeFooterLogoSize ===
                                        'small'
                                            ? 'w-[140px]'
                                            : footerData?.homeFooterLogoSize ===
                                              'large'
                                            ? 'w-[170px]'
                                            : 'w-[150px]'
                                    }
                                />
                            )}

                            <p
                                className="mt-7 max-w-[430px] whitespace-pre-line text-[13px] leading-[23px] text-white/70"
                                style={{
                                    color:
                                        footerData?.footer_text_color ||
                                        '#ffffff',
                                    textAlign: footerCompanyInfoAlignment,
                                }}
                            >
                                {footerData?.homeFooterDescription}
                            </p>

                            <p className="mt-5 text-[16px] font-semibold capitalize whitespace-nowrap">
                                <span>{translate('Subscribe')}</span>
                            </p>
                            <div className="mt-3 h-px max-w-[430px] bg-white/15">
                                <span className="block h-[3px] w-[54px] bg-[#ff2718]" />
                            </div>

                            <form
                                className="mt-5 flex max-xxs:flex-col max-xxs:gap-2 max-xxs:items-start max-w-[354px] overflow-hidden rounded-[4px] border border-white/20"
                                onSubmit={handleSubscribe}
                            >
                                <input
                                    type="text"
                                    name="email"
                                    value={email}
                                    placeholder={translate('Your email...')}
                                    onChange={(e) => setEmail(e.target.value)}
                                    className="h-9 grow bg-transparent pl-5 text-[12px] text-white placeholder:text-white/70"
                                />
                                <button
                                    type="submit"
                                    className="h-9 min-w-[102px] bg-[#ff2718] text-[10px] font-semibold uppercase text-white transition hover:bg-[#d61d11]"
                                >
                                    {translate('Submit')}
                                </button>
                            </form>
                        </div>
                        <div className="">
                            <FooterTitle
                                title={translate('Quick Links')}
                                style={{
                                    color:
                                        footerData?.footer_text_color ||
                                        '#ffffff',
                                }}
                            />
                            <ul className="mt-7 space-y-[18px] list-disc marker:text-[#d8c5a2] pl-10 whitespace-nowrap text-[13px]">
                                {quickLinks.map((link, idx) => (
                                    <li key={idx}>
                                        <Link
                                            to={link.url}
                                            className="flex items-center gap-3 transition-all hover:text-[#ff2718] group"
                                            style={{
                                                color:
                                                    footerData?.footer_text_color ||
                                                    '#ffffff',
                                            }}
                                        >
                                            {link.title}
                                            {idx === 0 ? (
                                            <span className="text-[#d8c5a2] transition group-hover:translate-x-1">
                                                <FaArrowRight />
                                            </span>
                                            ) : null}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                        <div className="">
                            <FooterTitle
                                title={translate('Important Links')}
                                style={{
                                    color:
                                        footerData?.footer_text_color ||
                                        '#ffffff',
                                }}
                            />
                            <ul className="mt-7 space-y-[18px] list-disc marker:text-[#d8c5a2] pl-10 whitespace-nowrap text-[13px]">
                                {importantLinks.map((link, idx) => (
                                    <li key={idx}>
                                        <Link
                                            to={link.url}
                                            className="flex items-center gap-3 transition-all hover:text-[#ff2718] group"
                                            style={{
                                                color:
                                                    footerData?.footer_text_color ||
                                                    '#ffffff',
                                            }}
                                        >
                                            {link.title}
                                            {idx === 0 ? (
                                            <span className="text-[#d8c5a2] transition group-hover:translate-x-1">
                                                <FaArrowRight />
                                            </span>
                                            ) : null}
                                        </Link>
                                    </li>
                                ))}
                            </ul>
                        </div>
                        <div className="">
                            <FooterTitle
                                title={translate('Contact Us')}
                                style={{
                                    color:
                                        footerData?.footer_text_color ||
                                        '#ffffff',
                                }}
                            />
                            {footerEmails.length > 0 && (
                                <ul className="mt-7 space-y-[16px] text-[13px] leading-[24px]">
                                    {footerEmails.map((email: string, index: number) => (
                                        <li key={index}>
                                            <a
                                                href={`mailto:${email}`}
                                                className="transition-all hover:text-[#ff2718]"
                                                style={{
                                                    color:
                                                        footerData?.footer_text_color ||
                                                        '#ffffff',
                                                }}
                                            >
                                                {email}
                                            </a>
                                        </li>
                                    ))}
                                </ul>
                            )}
                            <ul className="mt-[14px] space-y-[16px] text-[13px] leading-[24px]">
                                {supportNumbers.map((phoneNumber, index) => (
                                    <li key={index}>
                                        <a
                                            href={`tel:${phoneNumber}`}
                                            className="transition-all hover:text-[#ff2718]"
                                            style={{
                                                color:
                                                    footerData?.footer_text_color ||
                                                    '#ffffff',
                                            }}
                                        >
                                            {phoneNumber}
                                        </a>
                                    </li>
                                ))}
                                <li>
                                    <a
                                        href={`https://www.google.com/search?q=${encodeURIComponent(
                                            footerAddress,
                                        )}&oq=${encodeURIComponent(footerAddress)}`}
                                        target="_blank"
                                        referrerPolicy="no-referrer"
                                        className="block max-w-[300px] transition-all hover:text-[#ff2718]"
                                        style={{
                                            color:
                                                footerData?.footer_text_color ||
                                                '#ffffff',
                                        }}
                                    >
                                        <span className="whitespace-normal break-words">
                                            {footerAddress}
                                        </span>
                                    </a>
                                </li>
                            </ul>

                            {/* Google Maps Section */}
                            {footerData?.showHomeFooterGoogleMaps === '1' &&
                                footerData?.homeFooterGoogleMapsEnabled ===
                                    '1' && (
                                    <div className="mt-6">
                                        {footerData.homeFooterGoogleMapsType ===
                                        'embed' ? (
                                            <div
                                                className="map-embed-container"
                                                dangerouslySetInnerHTML={{
                                                    __html: footerData.homeFooterGoogleMapsValue,
                                                }}
                                            />
                                        ) : (
                                            <a
                                                href={
                                                    footerData.homeFooterGoogleMapsValue
                                                }
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90 transition-all"
                                            >
                                                <FaLocationDot />
                                                View on Google Maps
                                            </a>
                                        )}
                                    </div>
                                )}

                            {paymentImage && (
                                <img
                                    src={paymentImage}
                                    alt="Accepted payments"
                                    className={`mt-7 w-auto ${paymentImageSizeClass}`}
                                />
                            )}
                        </div>
                    </div>
                </div>
            </footer>
            )}
        </>
    );
};

export default Footer;
