import React from 'react';
import { createRoot } from 'react-dom/client';
import '../react/i18next';
import '../css/app.css';
import LandingPageListingApp from './landing-pages/components/LandingPageListingApp';
import LandingPageCreateApp from './landing-pages/components/LandingPageCreateApp';
import LandingPageConfigureApp from './landing-pages/LandingPageConfigureApp';

// Try each container - only one will exist per page
const listingContainer = document.getElementById('landing-page-listing');
const createContainer = document.getElementById('landing-page-create');
const configureContainer = document.getElementById('landing-page-configure');

if (listingContainer) {
    createRoot(listingContainer).render(<LandingPageListingApp />);
} else if (createContainer) {
    createRoot(createContainer).render(<LandingPageCreateApp />);
} else if (configureContainer) {
    createRoot(configureContainer).render(<LandingPageConfigureApp />);
}
