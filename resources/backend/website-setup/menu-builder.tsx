import React, { useState, useEffect } from 'react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';

// Import menu builder component
import MenuBuilder from './menu-builder/components/MenuBuilder.tsx';

interface MenuBuilderWrapperProps {
    apiPrefix: string;
}

const MenuBuilderWrapper: React.FC<MenuBuilderWrapperProps> = ({
    apiPrefix,
}) => {
    return (
        <div className="pt-4 min-h-screen">
            <div className="bg-white py-4 rounded-md">
                <MenuBuilder apiPrefix={apiPrefix} />
            </div>
        </div>
    );
};

// Create root element and render the app
const container = document.getElementById('menu-builder-app');
if (container) {
    const root = createRoot(container);
    root.render(
        <MenuBuilderWrapper
            apiPrefix={window.menuBuilderConfig?.apiPrefix || '/admin/api/theme/menu'}
        />
    );
}
