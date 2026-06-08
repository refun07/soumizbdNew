import React from 'react';
import CenteredProductOfferHeroPreview from './previews/CenteredProductOfferHeroPreview';
import CenteredVideoHeroPreview from './previews/CenteredVideoHeroPreview';
import CenteredOfferHeroPreview from './previews/CenteredOfferHeroPreview';
import QualityProductsPreview from './previews/QualityProductsPreview';
import QualityAboutPreview from './previews/QualityAboutPreview';
import MultipleProductsPreview from './previews/MultipleProductsPreview';
import ProductBenefitsPreview from './previews/ProductBenefitsPreview';
import ProductHighlightsPreview from './previews/ProductHighlightsPreview';
import CarouselImagesPreview from './previews/CarouselImagesPreview';
import FaqSectionPreview from './previews/FaqSectionPreview';
import FeaturedProductsPreview from './previews/FeaturedProductsPreview';

const SECTION_TYPE_LABELS: Record<string, string> = {
    centered_product_offer_hero: 'Centered Product Offer Hero',
    centered_video_hero: 'Centered Video Hero with Offer',
    centered_offer_hero: 'Centered Offer Hero',
    quality_products: 'Quality Products',
    quality_about: 'Quality Products About',
    multiple_products: 'Multiple Products',
    product_benefits: 'Product Benefits',
    product_highlights: 'Product Highlights',
    carousel_images: 'Carousel Images',
    faq_section: 'FAQ Section',
    featured_products: 'Featured Products',
};

interface SectionPreviewProps {
    type: string;
    onClose: () => void;
}

const SectionPreview: React.FC<SectionPreviewProps> = ({ type, onClose }) => {
    const renderPreview = () => {
        switch (type) {
            case 'centered_product_offer_hero':
                return <CenteredProductOfferHeroPreview />;
            case 'centered_video_hero':
                return <CenteredVideoHeroPreview />;
            case 'centered_offer_hero':
                return <CenteredOfferHeroPreview />;
            case 'quality_products':
                return <QualityProductsPreview />;
            case 'quality_about':
                return <QualityAboutPreview />;
            case 'multiple_products':
                return <MultipleProductsPreview />;
            case 'product_benefits':
                return <ProductBenefitsPreview />;
            case 'product_highlights':
                return <ProductHighlightsPreview />;
            case 'carousel_images':
                return <CarouselImagesPreview />;
            case 'faq_section':
                return <FaqSectionPreview />;
            case 'featured_products':
                return <FeaturedProductsPreview />;
            default:
                return (
                    <div className="flex items-center justify-center py-20">
                        <p className="text-gray-500">Preview not available for this section type</p>
                    </div>
                );
        }
    };

    return (
        <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" onClick={onClose}>
            <div
                className="bg-white rounded-lg w-full max-w-6xl max-h-[90vh] overflow-hidden"
                onClick={(e) => e.stopPropagation()}
            >
                <div className="p-4 border-b flex items-center justify-between">
                    <h3 className="text-lg font-semibold">{SECTION_TYPE_LABELS[type] || 'Section Preview'}</h3>
                    <button
                        onClick={onClose}
                        className="text-gray-500 hover:text-gray-700 p-1"
                    >
                        <i className="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <div className="overflow-y-auto max-h-[calc(90vh-60px)]">
                    {renderPreview()}
                </div>
            </div>
        </div>
    );
};

export default SectionPreview;
