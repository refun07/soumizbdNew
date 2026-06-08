import {
    FaFacebookF,
    FaInstagram,
    FaLinkedinIn,
    FaPinterestP,
    FaSnapchatGhost,
    FaTelegramPlane,
    FaTiktok,
    FaWhatsapp,
    FaYoutube,
} from 'react-icons/fa';
import { FaXTwitter } from 'react-icons/fa6';
import { cn } from '../../../utils/cn';

const socialIcons = {
    facebook: { label: 'Facebook', icon: <FaFacebookF /> },
    instagram: { label: 'Instagram', icon: <FaInstagram /> },
    tiktok: { label: 'TikTok', icon: <FaTiktok /> },
    youtube: { label: 'YouTube', icon: <FaYoutube /> },
    x: { label: 'X / Twitter', icon: <FaXTwitter /> },
    linkedin: { label: 'LinkedIn', icon: <FaLinkedinIn /> },
    whatsapp: { label: 'WhatsApp', icon: <FaWhatsapp /> },
    pinterest: { label: 'Pinterest', icon: <FaPinterestP /> },
    telegram: { label: 'Telegram', icon: <FaTelegramPlane /> },
    snapchat: { label: 'Snapchat', icon: <FaSnapchatGhost /> },
};

interface Props {
    className?: string;
    linkClassName?: string;
}

const formatSocialUrl = (url: string) => {
    if (/^(https?:|mailto:|tel:)/i.test(url) || url.startsWith('/')) {
        return url;
    }

    return `https://${url}`;
};

const HeaderSocialLinks = ({ className, linkClassName }: Props) => {
    const links = window.config.customerMenu?.socialLinks || [];

    if (!links.length) {
        return null;
    }

    return (
        <div className={cn('flex items-center gap-3', className)}>
            {links.map((link, index) => {
                const social =
                    socialIcons[link.platform as keyof typeof socialIcons];

                if (!social) {
                    return null;
                }

                return (
                    <a
                        key={`${link.platform}-${index}`}
                        href={formatSocialUrl(link.url)}
                        target="_blank"
                        rel="noopener noreferrer"
                        aria-label={social.label}
                        title={social.label}
                        className={cn(
                            'inline-flex size-9 items-center justify-center rounded-full border border-current/20 text-lg transition-colors hover:text-secondary',
                            linkClassName,
                        )}
                    >
                        {social.icon}
                    </a>
                );
            })}
        </div>
    );
};

export default HeaderSocialLinks;
