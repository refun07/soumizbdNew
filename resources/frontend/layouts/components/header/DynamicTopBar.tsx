import { FaTag } from 'react-icons/fa6';
import { Link } from 'react-router-dom';
import { translate } from '../../../utils/translate';

interface DynamicTopBarProps {
    config?: {
        enabled: boolean;
        hotline_number: string;
        hotline?: string;
        promo_text_icon: string;
        promo_text: string;
        bg_color: string;
        text_color: string;
    };
}

const DynamicTopBar: React.FC<DynamicTopBarProps> = ({ config }) => {

    // If topbar is disabled, don't render anything
    if (!config?.enabled) {
        return null;
    }

    const containerStyle: React.CSSProperties = {
        backgroundColor: config.bg_color || '#1a1a2e',
        color: config.text_color || '#ffffff'
    };

    // Extract phone number from hotline_number (full line) for tel link
    const extractPhoneNumber = (text: string): string => {
        const phoneMatch = text.match(/[\d\s\+]+$/);
        if (phoneMatch) {
            return phoneMatch[0].replace(/\s+/g, '');
        }
        return text.replace(/\D/g, '');
    };

    const phoneNumber = config.hotline || extractPhoneNumber(config.hotline_number);

    return (
        <div className="max-lg:hidden h-[46px] flex items-center justify-center" style={containerStyle}>
            <div className="container flex justify-between items-center gap-10">
                {/* Left Section: Hotline */}
                {config.hotline_number ? (
                    <a href={`tel:${phoneNumber}`} className="font-medium">
                        <span style={{ color: config.text_color }}>{translate(config.hotline_number)}</span>
                    </a>
                ) : (
                    <div></div>
                )}

                {/* Center Section: Promo Text */}
                {config.promo_text ? (
                    <a href="#" className="flex gap-2 items-center">
                        <span className="text-secondary">
                            {config.promo_text_icon ? (
                                <img
                                    src={`/${config.promo_text_icon}`}
                                    alt="Promo Icon"
                                    className="w-4 h-4 inline"
                                />
                            ) : (
                                <FaTag />
                            )}
                        </span>
                        <span>
                            {translate(config.promo_text)}
                        </span>
                    </a>
                ) : (
                    <div></div> // Spacer to maintain layout
                )}

                {/* Right Section: Track Order */}
                <div className="flex items-center justify-center gap-5">
                    <Link to={'/orders/track'} className="">
                        {translate('Track Order')}
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default DynamicTopBar;