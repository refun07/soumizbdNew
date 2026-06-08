import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';

// Import header configuration components
import HeaderConfigurationApp from './header/components/HeaderConfigurationApp.tsx';

interface HeaderConfigurationProps {
    headerId: string;
    apiPrefix: string;
}

const HeaderConfiguration: React.FC<HeaderConfigurationProps> = ({
    headerId,
    apiPrefix,
}) => {
    const [config, setConfig] = useState({
        topbar: {
            enabled: true,
            hotline_number: '',
            promo_text_icon: '',
            promo_text: '',
            bg_color: '#4267b2',
            text_color: '#ffffff'
        },
        navbar: {
            custom_links_enabled: false,
            custom_links: []
        }
    });

    const [loading, setLoading] = useState(true);

    useEffect(() => {
        // Load current header configuration
        console.log('Loading header configuration from:', apiPrefix, 'for header:', headerId);
        setLoading(false);
    }, [apiPrefix, headerId]);

    if (loading) {
        return (
            <div className="pt-4 min-h-screen">
                <div className="bg-white py-4 rounded-md">
                    <div className="flex items-center justify-center p-8">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="pt-4 min-h-screen">
            <div className="bg-white py-4 rounded-md">
                <HeaderConfigurationApp
                    headerId={headerId}
                    apiPrefix={apiPrefix}
                />
            </div>
        </div>
    );
};

// Create root element and render the app
const container = document.getElementById('header-configuration-app');
if (container) {
    const root = createRoot(container);
    root.render(
        <HeaderConfiguration
            headerId={window.headerConfigurationConfig?.headerId || 'header-one'}
            apiPrefix={window.headerConfigurationConfig?.apiPrefix || '/admin/api/theme-selection'}
        />
    );
}