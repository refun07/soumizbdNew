import React from 'react';

interface WireframeProps {
  className?: string;
}

// Hero: Centered Product Offer Hero
export const CenteredProductOfferHeroWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 300" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="300" fill="var(--builder-surface-hover)"/>
    <rect x="160" y="20" width="80" height="20" rx="10" fill="var(--builder-accent)" opacity="0.3"/>
    <rect x="80" y="55" width="240" height="24" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="100" y="85" width="200" height="16" rx="4" fill="var(--builder-text)" opacity="0.5"/>
    <circle cx="200" cy="160" r="60" fill="var(--builder-surface)" stroke="var(--builder-border)" strokeWidth="2"/>
    <rect x="170" y="140" width="60" height="40" rx="4" fill="var(--builder-border)"/>
    <rect x="160" y="235" width="80" height="20" rx="4" fill="var(--builder-accent)" opacity="0.6"/>
    <rect x="140" y="265" width="120" height="28" rx="14" fill="var(--builder-accent)"/>
  </svg>
);

// Hero: Centered Video Hero with Offer
export const CenteredVideoHeroWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 300" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="300" fill="var(--builder-surface-hover)"/>
    <rect x="40" y="30" width="320" height="180" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)" strokeWidth="2"/>
    <circle cx="200" cy="120" r="30" fill="var(--builder-accent)" opacity="0.8"/>
    <polygon points="190,110 190,130 210,120" fill="white"/>
    <rect x="60" y="185" width="280" height="4" rx="2" fill="var(--builder-border)"/>
    <rect x="60" y="185" width="120" height="4" rx="2" fill="var(--builder-accent)"/>
    <rect x="80" y="230" width="240" height="20" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="100" y="258" width="200" height="14" rx="4" fill="var(--builder-text)" opacity="0.5"/>
  </svg>
);

// Hero: Centered Offer Hero
export const CenteredOfferHeroWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 300" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="300" fill="var(--builder-surface-hover)"/>
    <circle cx="200" cy="60" r="40" fill="var(--builder-accent)"/>
    <rect x="175" y="45" width="50" height="12" rx="2" fill="white" opacity="0.9"/>
    <rect x="170" y="62" width="60" height="16" rx="2" fill="white" opacity="0.9"/>
    <rect x="60" y="120" width="280" height="28" rx="4" fill="var(--builder-text)" opacity="0.9"/>
    <rect x="100" y="155" width="200" height="16" rx="4" fill="var(--builder-text)" opacity="0.5"/>
    <rect x="120" y="190" width="160" height="50" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="135" y="205" width="30" height="20" rx="4" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="175" y="205" width="30" height="20" rx="4" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="215" y="205" width="30" height="20" rx="4" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="255" y="205" width="30" height="20" rx="4" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="140" y="260" width="120" height="32" rx="16" fill="var(--builder-accent)"/>
  </svg>
);

// Products: Quality Products (3-column grid)
export const QualityProductsWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 250" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="250" fill="var(--builder-surface-hover)"/>
    <rect x="140" y="20" width="120" height="20" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="30" y="60" width="100" height="100" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="45" y="80" width="70" height="60" rx="4" fill="var(--builder-border)"/>
    <rect x="40" y="170" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="50" y="188" width="60" height="10" rx="2" fill="var(--builder-accent)" opacity="0.6"/>
    <rect x="150" y="60" width="100" height="100" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="165" y="80" width="70" height="60" rx="4" fill="var(--builder-border)"/>
    <rect x="160" y="170" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="170" y="188" width="60" height="10" rx="2" fill="var(--builder-accent)" opacity="0.6"/>
    <rect x="270" y="60" width="100" height="100" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="285" y="80" width="70" height="60" rx="4" fill="var(--builder-border)"/>
    <rect x="280" y="170" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="290" y="188" width="60" height="10" rx="2" fill="var(--builder-accent)" opacity="0.6"/>
    <rect x="160" y="220" width="80" height="24" rx="12" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
  </svg>
);

// Products: Multiple Products (2x2 grid)
export const MultipleProductsWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 320" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="320" fill="var(--builder-surface-hover)"/>
    <rect x="130" y="20" width="140" height="20" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="40" y="60" width="150" height="110" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="60" y="80" width="110" height="60" rx="4" fill="var(--builder-border)"/>
    <rect x="60" y="150" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="210" y="60" width="150" height="110" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="230" y="80" width="110" height="60" rx="4" fill="var(--builder-border)"/>
    <rect x="230" y="150" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="40" y="190" width="150" height="110" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="60" y="210" width="110" height="60" rx="4" fill="var(--builder-border)"/>
    <rect x="60" y="280" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="210" y="190" width="150" height="110" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="230" y="210" width="110" height="60" rx="4" fill="var(--builder-border)"/>
    <rect x="230" y="280" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.7"/>
  </svg>
);

// Products: Featured Products
export const FeaturedProductsWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 280" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="280" fill="var(--builder-surface-hover)"/>
    <rect x="130" y="20" width="140" height="20" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="30" y="60" width="220" height="200" rx="12" fill="var(--builder-surface)" stroke="var(--builder-border)" strokeWidth="2"/>
    <rect x="60" y="90" width="160" height="120" rx="8" fill="var(--builder-border)"/>
    <rect x="60" y="225" width="120" height="16" rx="2" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="60" y="248" width="80" height="12" rx="2" fill="var(--builder-accent)"/>
    <rect x="270" y="60" width="100" height="90" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="285" y="75" width="70" height="50" rx="4" fill="var(--builder-border)"/>
    <rect x="285" y="133" width="50" height="10" rx="2" fill="var(--builder-text)" opacity="0.6"/>
    <rect x="270" y="170" width="100" height="90" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="285" y="185" width="70" height="50" rx="4" fill="var(--builder-border)"/>
    <rect x="285" y="243" width="50" height="10" rx="2" fill="var(--builder-text)" opacity="0.6"/>
  </svg>
);

// Products: Product Benefits
export const ProductBenefitsWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 200" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="200" fill="var(--builder-surface-hover)"/>
    <rect x="140" y="20" width="120" height="18" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <circle cx="70" cy="90" r="25" fill="var(--builder-accent)" opacity="0.2"/>
    <rect x="55" y="82" width="30" height="16" rx="2" fill="var(--builder-accent)"/>
    <rect x="105" y="82" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="105" y="100" width="100" height="8" rx="2" fill="var(--builder-text)" opacity="0.4"/>
    <circle cx="200" cy="90" r="25" fill="var(--builder-accent)" opacity="0.2"/>
    <rect x="185" y="82" width="30" height="16" rx="2" fill="var(--builder-accent)"/>
    <rect x="235" y="82" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="235" y="100" width="100" height="8" rx="2" fill="var(--builder-text)" opacity="0.4"/>
    <circle cx="330" cy="90" r="25" fill="var(--builder-accent)" opacity="0.2"/>
    <rect x="315" y="82" width="30" height="16" rx="2" fill="var(--builder-accent)"/>
    <rect x="365" y="82" width="80" height="12" rx="2" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="365" y="100" width="100" height="8" rx="2" fill="var(--builder-text)" opacity="0.4"/>
    <rect x="100" y="155" width="200" height="10" rx="2" fill="var(--builder-text)" opacity="0.5"/>
  </svg>
);

// Products: Product Highlights
export const ProductHighlightsWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 280" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="280" fill="var(--builder-surface-hover)"/>
    <rect x="130" y="20" width="140" height="18" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="30" y="55" width="340" height="150" rx="12" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="60" y="80" width="280" height="100" rx="8" fill="var(--builder-border)"/>
    <rect x="50" y="220" width="90" height="40" rx="8" fill="var(--builder-surface)" stroke="var(--builder-accent)" strokeWidth="2"/>
    <rect x="65" y="232" width="60" height="8" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="70" y="245" width="50" height="6" rx="2" fill="var(--builder-text)" opacity="0.4"/>
    <rect x="155" y="220" width="90" height="40" rx="8" fill="var(--builder-surface)" stroke="var(--builder-accent)" strokeWidth="2"/>
    <rect x="170" y="232" width="60" height="8" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="175" y="245" width="50" height="6" rx="2" fill="var(--builder-text)" opacity="0.4"/>
    <rect x="260" y="220" width="90" height="40" rx="8" fill="var(--builder-surface)" stroke="var(--builder-accent)" strokeWidth="2"/>
    <rect x="275" y="232" width="60" height="8" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="280" y="245" width="50" height="6" rx="2" fill="var(--builder-text)" opacity="0.4"/>
  </svg>
);

// Content: Quality About
export const QualityAboutWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 240" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="240" fill="var(--builder-surface-hover)"/>
    <rect x="30" y="40" width="160" height="20" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="30" y="75" width="170" height="10" rx="2" fill="var(--builder-text)" opacity="0.5"/>
    <rect x="30" y="92" width="160" height="10" rx="2" fill="var(--builder-text)" opacity="0.5"/>
    <rect x="30" y="109" width="165" height="10" rx="2" fill="var(--builder-text)" opacity="0.5"/>
    <rect x="30" y="135" width="100" height="12" rx="2" fill="var(--builder-accent)" opacity="0.6"/>
    <rect x="30" y="160" width="120" height="36" rx="18" fill="var(--builder-accent)"/>
    <rect x="220" y="40" width="150" height="170" rx="12" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="240" y="65" width="110" height="120" rx="8" fill="var(--builder-border)"/>
  </svg>
);

// Content: FAQ Section
export const FAQSectionWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 320" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="320" fill="var(--builder-surface-hover)"/>
    <rect x="160" y="25" width="80" height="20" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="30" y="65" width="340" height="80" rx="8" fill="var(--builder-surface)" stroke="var(--builder-accent)" strokeWidth="2"/>
    <rect x="50" y="82" width="200" height="14" rx="2" fill="var(--builder-text)" opacity="0.9"/>
    <rect x="330" y="80" width="20" height="20" rx="4" fill="var(--builder-accent)" opacity="0.2"/>
    <rect x="50" y="110" width="290" height="8" rx="2" fill="var(--builder-text)" opacity="0.4"/>
    <rect x="50" y="123" width="260" height="8" rx="2" fill="var(--builder-text)" opacity="0.4"/>
    <rect x="30" y="160" width="340" height="50" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="50" y="177" width="220" height="14" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="330" y="175" width="20" height="20" rx="4" fill="var(--builder-border)"/>
    <rect x="30" y="225" width="340" height="50" rx="8" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="50" y="242" width="180" height="14" rx="2" fill="var(--builder-text)" opacity="0.7"/>
    <rect x="330" y="240" width="20" height="20" rx="4" fill="var(--builder-border)"/>
  </svg>
);

// Content: Carousel Images
export const CarouselImagesWireframe: React.FC<WireframeProps> = ({ className }) => (
  <svg viewBox="0 0 400 240" className={className} fill="none" xmlns="http://www.w3.org/2000/svg">
    <rect width="400" height="240" fill="var(--builder-surface-hover)"/>
    <rect x="140" y="20" width="120" height="18" rx="4" fill="var(--builder-text)" opacity="0.8"/>
    <rect x="30" y="55" width="340" height="140" rx="12" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <rect x="50" y="70" width="200" height="110" rx="8" fill="var(--builder-border)"/>
    <rect x="265" y="85" width="90" height="80" rx="8" fill="var(--builder-border)" opacity="0.5"/>
    <circle cx="45" cy="125" r="15" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <polygon points="50,125 42,120 42,130" fill="var(--builder-text)" opacity="0.5"/>
    <circle cx="355" cy="125" r="15" fill="var(--builder-surface)" stroke="var(--builder-border)"/>
    <polygon points="350,125 358,120 358,130" fill="var(--builder-text)" opacity="0.5"/>
    <circle cx="170" cy="215" r="5" fill="var(--builder-accent)"/>
    <circle cx="190" cy="215" r="5" fill="var(--builder-border)"/>
    <circle cx="210" cy="215" r="5" fill="var(--builder-border)"/>
    <circle cx="230" cy="215" r="5" fill="var(--builder-border)"/>
  </svg>
);

// Wireframe mapping
const SectionWireframes: Record<string, React.FC<WireframeProps>> = {
  centered_product_offer_hero: CenteredProductOfferHeroWireframe,
  centered_video_hero: CenteredVideoHeroWireframe,
  centered_offer_hero: CenteredOfferHeroWireframe,
  quality_products: QualityProductsWireframe,
  multiple_products: MultipleProductsWireframe,
  featured_products: FeaturedProductsWireframe,
  product_benefits: ProductBenefitsWireframe,
  product_highlights: ProductHighlightsWireframe,
  quality_about: QualityAboutWireframe,
  faq_section: FAQSectionWireframe,
  carousel_images: CarouselImagesWireframe,
};

export default SectionWireframes;
