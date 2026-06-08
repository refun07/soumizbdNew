import React from 'react';
import { createRoot } from 'react-dom/client';
import FooterConfigurationApp from './footer/components/FooterConfigurationApp';

const container = document.getElementById('footer-configuration-app');
if (container) {
    const root = createRoot(container);
    const config = (window as any).footerConfigurationConfig || {
        apiPrefix: '/admin/api/theme-selection'
    };
    root.render(<FooterConfigurationApp apiPrefix={config.apiPrefix} />);
}
