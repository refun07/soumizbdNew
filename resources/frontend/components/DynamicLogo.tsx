import { useState } from 'react';

interface DynamicLogoProps {
    fallbackSrc?: string;
    alt?: string;
    className?: string;
    onClick?: () => void;
}

/**
 * DynamicLogo component that displays a logo from the database configuration
 * with a static fallback if the database logo is not available
 */
const DynamicLogo = ({
    fallbackSrc = '/images/logo.png',
    alt = '',
    className = '',
    onClick
}: DynamicLogoProps) => {
    const [imageError, setImageError] = useState(false);

    // Get logo from window.config.generalSettings.logo (set by backend)
    const logoUrl = (window as any).config?.generalSettings?.logo;

    // Determine which image source to use
    const src = !imageError && logoUrl ? logoUrl : fallbackSrc;

    const handleError = () => {
        setImageError(true);
    };

    return (
        <img
            src={src}
            alt={alt}
            className={`h-auto w-auto max-w-full max-h-[50px] md:max-h-[60px] xl:max-h-[75px] object-contain ${className}`}
            onError={handleError}
            onClick={onClick}
        />
    );
};

export default DynamicLogo;
