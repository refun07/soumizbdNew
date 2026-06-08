import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';

// Import theme selection components
import ThemeSelectionApp from './theme/components/ThemeSelectionApp.tsx';

interface ThemeSelectionProps {
    apiPrefix: string;
}

const ThemeSelection: React.FC<ThemeSelectionProps> = ({
    apiPrefix,
}) => {
    const [config, setConfig] = useState({
        theme: 'theme-one',
        header: 'header-one',
        productCard: 'product-card-one',
        categoryCard: 'category-card-one'
    });

    useEffect(() => {
        // Load current theme configuration
        console.log('Loading theme configuration from:', apiPrefix);
    }, [apiPrefix]);

    return (
        <div className="pt-4 min-h-screen">
            <div className="bg-white py-4 rounded-md">
                <ThemeSelectionApp />
            </div>
        </div>
    );
};

// Create root element and render the app
const container = document.getElementById('theme-selection-app');
if (container) {
    const root = createRoot(container);
    root.render(
        <ThemeSelection 
            apiPrefix={window.themeSelectionConfig?.apiPrefix || '/admin/api/theme-selection'}
        />
    );
}
