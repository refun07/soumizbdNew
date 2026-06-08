import React, { useState, useEffect } from 'react';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import CenteredProductOfferHeroForm from './sections/CenteredProductOfferHeroForm';
import CenteredVideoHeroForm from './sections/CenteredVideoHeroForm';
import CarouselSectionForm from './sections/CarouselSectionForm';
import ProductHighlightsForm from './sections/ProductHighlightsForm';
import FaqSectionForm from './sections/FaqSectionForm';
import FeaturedProductsForm from './sections/FeaturedProductsForm';
import QualityProductsForm from './sections/QualityProductsForm';
import QualityAboutForm from './sections/QualityAboutForm';
import MultipleProductsForm from './sections/MultipleProductsForm';
import SectionPreview from './sections/SectionPreview';
import Button from '../../../react/components/inputs/Button';
import ColorPicker from '../../header/components/ColorPicker';

interface LandingPage {
    id: number;
    title: string;
    slug: string;
    status: 'draft' | 'published';
    sections: LandingPageSection[];
    selected_products?: number[];
    page_configs?: PageConfigs;
    coupon_id?: number;
    coupon?: Coupon;
}

interface PageConfigs {
    theme_color?: string;
    primary_color?: string;
}

interface Product {
    id: number;
    name: string;
    sku: string;
    image: string;
    price: number;
    variation_id: number;
}

interface Coupon {
    id: number;
    code: string;
    info: string;
    discount_type: string;
    discount_value: number;
    is_free_shipping: boolean;
    start_date: string;
    end_date: string;
}

interface LandingPageSection {
    id: number;
    type: string;
    section_data: any;
    order: number;
    is_visible: boolean;
}

export interface SectionFormData {
    type: string;
    image_url: string;
    // Layout settings
    background_color?: string | null;
    // Three column section fields
    column1_image?: string;
    column1_title?: string;
    column1_description?: string;
    column2_image?: string;
    column2_title?: string;
    column2_description?: string;
    column3_image?: string;
    column3_title?: string;
    column3_description?: string;
    // Three column icon section fields
    column1_icon?: string;
    column1_icon_title?: string;
    column1_icon_description?: string;
    column2_icon?: string;
    column2_icon_title?: string;
    column2_icon_description?: string;
    column3_icon?: string;
    column3_icon_title?: string;
    column3_icon_description?: string;
    // Full width video section fields
    video_url?: string;
    video_caption?: string;
    // Centered product offer hero fields
    brand_logo_url?: string;
    product_image_url?: string;
    product_video_url?: string;
    product_offer_badge_text?: string;
    product_title?: string;
    product_subtitle?: string;
    product_old_price?: string;
    product_new_price?: string;
    product_button_text?: string;
    // Quality products section fields
    quality_image_url?: string;
    quality_title?: string;
    item1_icon?: string;
    item1_text?: string;
    item2_icon?: string;
    item2_text?: string;
    item3_icon?: string;
    item3_text?: string;
    item4_icon?: string;
    item4_text?: string;
    button_url?: string;
    // Quality about section fields
    quality_about_image_url?: string;
    quality_about_title?: string;
    quality_about_description?: string;
    quality_about_button_text?: string;
    quality_about_button_url?: string;
    // Multiple products section fields
    product1_image?: string;
    product1_title?: string;
    product1_subtitle?: string;
    product1_button_text?: string;
    product1_button_url?: string;
    product2_image?: string;
    product2_title?: string;
    product2_subtitle?: string;
    product2_button_text?: string;
    product2_button_url?: string;
    product3_image?: string;
    product3_title?: string;
    product3_subtitle?: string;
    product3_button_text?: string;
    product3_button_url?: string;
    // Product benefits section fields
    benefits_title?: string;
    benefits_items?: Array<{text: string}>;
    benefits_button_text?: string;
    benefits_button_url?: string;
    // Centered offer hero section fields
    offer_title?: string;
    offer_subtitle?: string;
    offer_badge_text?: string;
    old_price?: string;
    new_price?: string;
    button_text?: string;
    // Carousel image section fields
    carousel_title?: string;
    carousel_image_urls?: string[];
    // FAQ section fields
    faq_title?: string;
    faq_items?: FaqItem[];
    // Featured products fields
    section_title?: string;
    featured_products?: Array<{
        id: string;
        title: string;
        description: string;
        image: string;
        button_text: string;
    }>;
}

interface FaqItem {
    id: string;
    question: string;
    answer: string;
}

interface Currency {
    symbol: string;
    code: string;
}

const SECTION_TYPES = [
    { value: 'centered_product_offer_hero', label: 'Centered Product Offer Hero' },
    { value: 'centered_video_hero', label: 'Centered Video Hero with Offer' },
    { value: 'centered_offer_hero', label: 'Centered Offer Hero' },
    { value: 'quality_products', label: 'Quality Products' },
    { value: 'quality_about', label: 'Quality Products About' },
    { value: 'multiple_products', label: 'Multiple Products' },
    { value: 'product_benefits', label: 'Product Benefits' },
    { value: 'product_highlights', label: 'Product Highlights' },
    { value: 'carousel_images', label: 'Carousel Images' },
    { value: 'faq_section', label: 'FAQ Section' },
    { value: 'featured_products', label: 'Featured Products' },
];

const BUTTON_TYPES = [
    { value: 'primary', label: 'Primary' },
    { value: 'secondary', label: 'Secondary' },
    { value: 'outline', label: 'Outline' },
];

const ALIGNMENT_OPTIONS = [
    { value: 'left', label: 'Left' },
    { value: 'center', label: 'Center' },
    { value: 'right', label: 'Right' },
];

const HEIGHT_OPTIONS = [
    { value: 'small', label: 'Small (384px)' },
    { value: 'medium', label: 'Medium (500px)' },
    { value: 'large', label: 'Large (600px)' },
    { value: 'fullscreen', label: 'Fullscreen' },
];

const LandingPageConfigureApp: React.FC = () => {
    const [landingPage, setLandingPage] = useState<LandingPage | null>(null);
    const [loading, setLoading] = useState(true);
    const [theme, setTheme] = useState('theme-one');
    const [langKey, setLangKey] = useState('en-US');
    const [showSectionList, setShowSectionList] = useState(false);
    const [expandedSections, setExpandedSections] = useState<Set<number>>(new Set());
    const [saving, setSaving] = useState(false);
    const [sectionFormDataMap, setSectionFormDataMap] = useState<Record<number, SectionFormData>>({});
    const [products, setProducts] = useState<Product[]>([]);
    const [selectedProducts, setSelectedProducts] = useState<number[]>([]);
    const [showProductSelection, setShowProductSelection] = useState(false);
    const [productSearch, setProductSearch] = useState('');
    const [previewKey, setPreviewKey] = useState(0);
    const [showSectionPreview, setShowSectionPreview] = useState(false);
    const [previewSectionType, setPreviewSectionType] = useState('');
    const [pageConfigs, setPageConfigs] = useState<PageConfigs>({
        theme_color: '#1a472a',
        primary_color: '#E68A3D'
    });
    const [savingConfigs, setSavingConfigs] = useState(false);
    const [coupons, setCoupons] = useState<Coupon[]>([]);
    const [selectedCouponId, setSelectedCouponId] = useState<number | null>(null);
    const [couponSearch, setCouponSearch] = useState('');
    const [savingCoupon, setSavingCoupon] = useState(false);
    const [currency, setCurrency] = useState<Currency>({
        symbol: '$',
        code: 'USD',
    });

    useEffect(() => {
        // Get URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const id = window.location.pathname.split('/').pop();
        const lang = urlParams.get('lang_key') || 'en-US';
        setLangKey(lang);

        if (id) {
            fetchLandingPage(id, lang);
        }

        // Wait for mediaManager to be available
        const checkMediaManager = setInterval(() => {
            if ((window as any).mediaManager) {
                clearInterval(checkMediaManager);
            }
        }, 100);

        // Watch for changes to hidden inputs (media manager updates)
        const handleInputChange = (event: Event) => {
            const target = event.target as HTMLInputElement;
            if (!target || !target.name) return;

            // Handle main banner image
            if (target.name.startsWith('image_url_')) {
                const sectionId = parseInt(target.name.replace('image_url_', ''));
                const newUrl = target.value;

                if (sectionId && newUrl) {
                    const updatePreview = (imageUrl: string) => {
                        setSectionFormDataMap(prevMap => ({
                            ...prevMap,
                            [sectionId]: {
                                ...prevMap[sectionId],
                                image_url: imageUrl
                            }
                        }));

                        const countSpan = target.nextElementSibling as HTMLElement;
                        if (countSpan && countSpan.classList.contains('media-count')) {
                            countSpan.textContent = '1 file selected';
                        }
                    };

                    // If it's a file path (starts with /), use it directly
                    if (newUrl.startsWith('/')) {
                        updatePreview(newUrl);
                    } else {
                        // It's a media ID, fetch the actual file path
                        const mediaId = parseInt(newUrl);
                        if (mediaId) {
                            fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result && result.length > 0) {
                                    let path = result[0].media_file;
                                    if (path && !path.startsWith('/')) {
                                        path = '/' + path;
                                    }
                                    updatePreview(path);
                                }
                            })
                            .catch(err => console.error('Error fetching file:', err));
                        }
                    }
                }
            }

            // Handle brand logo for centered product offer hero
            if (target.name.startsWith('brand_logo_url_')) {
                const sectionId = parseInt(target.name.replace('brand_logo_url_', ''));
                const newUrl = target.value;

                if (sectionId && newUrl) {
                    const updatePreview = (imageUrl: string) => {
                        setSectionFormDataMap(prevMap => ({
                            ...prevMap,
                            [sectionId]: {
                                ...prevMap[sectionId],
                                brand_logo_url: imageUrl
                            }
                        }));

                        const countSpan = target.nextElementSibling as HTMLElement;
                        if (countSpan && countSpan.classList.contains('media-count')) {
                            countSpan.textContent = '1 file selected';
                        }
                    };

                    // If it's a file path (starts with /), use it directly
                    if (newUrl.startsWith('/')) {
                        updatePreview(newUrl);
                    } else {
                        // It's a media ID, fetch the actual file path
                        const mediaId = parseInt(newUrl);
                        if (mediaId) {
                            fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result && result.length > 0) {
                                    let path = result[0].media_file;
                                    if (path && !path.startsWith('/')) {
                                        path = '/' + path;
                                    }
                                    updatePreview(path);
                                }
                            })
                            .catch(err => console.error('Error fetching file:', err));
                        }
                    }
                }
            }

            // Handle product image for centered product offer hero
            if (target.name.startsWith('product_image_url_')) {
                const sectionId = parseInt(target.name.replace('product_image_url_', ''));
                const newUrl = target.value;

                if (sectionId && newUrl) {
                    const updatePreview = (imageUrl: string) => {
                        setSectionFormDataMap(prevMap => ({
                            ...prevMap,
                            [sectionId]: {
                                ...prevMap[sectionId],
                                product_image_url: imageUrl
                            }
                        }));

                        const countSpan = target.nextElementSibling as HTMLElement;
                        if (countSpan && countSpan.classList.contains('media-count')) {
                            countSpan.textContent = '1 file selected';
                        }
                    };

                    // If it's a file path (starts with /), use it directly
                    if (newUrl.startsWith('/')) {
                        updatePreview(newUrl);
                    } else {
                        // It's a media ID, fetch the actual file path
                        const mediaId = parseInt(newUrl);
                        if (mediaId) {
                            fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result && result.length > 0) {
                                    let path = result[0].media_file;
                                    if (path && !path.startsWith('/')) {
                                        path = '/' + path;
                                    }
                                    updatePreview(path);
                                }
                            })
                            .catch(err => console.error('Error fetching file:', err));
                        }
                    }
                }
            }

            // Handle three column images
            if (target.name.startsWith('column1_image_') || target.name.startsWith('column2_image_') || target.name.startsWith('column3_image_')) {
                const sectionId = parseInt(target.name.split('_').pop() || '0');
                const newUrl = target.value;

                if (sectionId && newUrl) {
                    const columnMatch = target.name.match(/^(column[1-3]_image)_/);
                    if (columnMatch) {
                        const columnKey = columnMatch[1] as keyof SectionFormData;

                        const updatePreview = (imageUrl: string) => {
                            setSectionFormDataMap(prevMap => ({
                                ...prevMap,
                                [sectionId]: {
                                    ...prevMap[sectionId],
                                    [columnKey]: imageUrl
                                }
                            }));

                            const countSpan = target.nextElementSibling as HTMLElement;
                            if (countSpan && countSpan.classList.contains('media-count')) {
                                countSpan.textContent = '1 file';
                            }
                        };

                        // If it's a file path (starts with /), use it directly
                        if (newUrl.startsWith('/')) {
                            updatePreview(newUrl);
                        } else {
                            // It's a media ID, fetch the actual file path
                            const mediaId = parseInt(newUrl);
                            if (mediaId) {
                                fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                    method: 'GET',
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                    },
                                })
                                .then(response => response.json())
                                .then(result => {
                                    if (result && result.length > 0) {
                                        let path = result[0].media_file;
                                        if (path && !path.startsWith('/')) {
                                            path = '/' + path;
                                        }
                                        updatePreview(path);
                                    }
                                })
                                .catch(err => console.error('Error fetching file:', err));
                            }
                        }
                    }
                }
            }

            // Handle quality image for quality products section
            if (target.name.startsWith('quality_image_url_')) {
                const sectionId = parseInt(target.name.replace('quality_image_url_', ''));
                const newUrl = target.value;

                if (sectionId && newUrl) {
                    const updatePreview = (imageUrl: string) => {
                        setSectionFormDataMap(prevMap => ({
                            ...prevMap,
                            [sectionId]: {
                                ...prevMap[sectionId],
                                quality_image_url: imageUrl
                            }
                        }));

                        const countSpan = target.nextElementSibling as HTMLElement;
                        if (countSpan && countSpan.classList.contains('media-count')) {
                            countSpan.textContent = '1 file selected';
                        }
                    };

                    // If it's a file path (starts with /), use it directly
                    if (newUrl.startsWith('/')) {
                        updatePreview(newUrl);
                    } else {
                        // It's a media ID, fetch the actual file path
                        const mediaId = parseInt(newUrl);
                        if (mediaId) {
                            fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result && result.length > 0) {
                                    let path = result[0].media_file;
                                    if (path && !path.startsWith('/')) {
                                        path = '/' + path;
                                    }
                                    updatePreview(path);
                                }
                            })
                            .catch(err => console.error('Error fetching file:', err));
                        }
                    }
                }
            }

            // Handle quality about image for quality about section
            if (target.name.startsWith('quality_about_image_url_')) {
                const sectionId = parseInt(target.name.replace('quality_about_image_url_', ''));
                const newUrl = target.value;

                if (sectionId && newUrl) {
                    const updatePreview = (imageUrl: string) => {
                        setSectionFormDataMap(prevMap => ({
                            ...prevMap,
                            [sectionId]: {
                                ...prevMap[sectionId],
                                quality_about_image_url: imageUrl
                            }
                        }));

                        const countSpan = target.nextElementSibling as HTMLElement;
                        if (countSpan && countSpan.classList.contains('media-count')) {
                            countSpan.textContent = '1 file selected';
                        }
                    };

                    // If it's a file path (starts with /), use it directly
                    if (newUrl.startsWith('/')) {
                        updatePreview(newUrl);
                    } else {
                        // It's a media ID, fetch the actual file path
                        const mediaId = parseInt(newUrl);
                        if (mediaId) {
                            fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result && result.length > 0) {
                                    let path = result[0].media_file;
                                    if (path && !path.startsWith('/')) {
                                        path = '/' + path;
                                    }
                                    updatePreview(path);
                                }
                            })
                            .catch(err => console.error('Error fetching file:', err));
                        }
                    }
                }
            }

            // Handle product1 image for multiple products section
            if (target.name.startsWith('product1_image_')) {
                const sectionId = parseInt(target.name.replace('product1_image_', ''));
                const newUrl = target.value;

                if (sectionId && newUrl) {
                    const updatePreview = (imageUrl: string) => {
                        setSectionFormDataMap(prevMap => ({
                            ...prevMap,
                            [sectionId]: {
                                ...prevMap[sectionId],
                                product1_image: imageUrl
                            }
                        }));

                        const countSpan = target.nextElementSibling as HTMLElement;
                        if (countSpan && countSpan.classList.contains('media-count')) {
                            countSpan.textContent = '1 file selected';
                        }
                    };

                    if (newUrl.startsWith('/')) {
                        updatePreview(newUrl);
                    } else {
                        const mediaId = parseInt(newUrl);
                        if (mediaId) {
                            fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result && result.length > 0) {
                                    let path = result[0].media_file;
                                    if (path && !path.startsWith('/')) {
                                        path = '/' + path;
                                    }
                                    updatePreview(path);
                                }
                            })
                            .catch(err => console.error('Error fetching file:', err));
                        }
                    }
                }
            }

            // Handle product2 image for multiple products section
            if (target.name.startsWith('product2_image_')) {
                const sectionId = parseInt(target.name.replace('product2_image_', ''));
                const newUrl = target.value;

                if (sectionId && newUrl) {
                    const updatePreview = (imageUrl: string) => {
                        setSectionFormDataMap(prevMap => ({
                            ...prevMap,
                            [sectionId]: {
                                ...prevMap[sectionId],
                                product2_image: imageUrl
                            }
                        }));

                        const countSpan = target.nextElementSibling as HTMLElement;
                        if (countSpan && countSpan.classList.contains('media-count')) {
                            countSpan.textContent = '1 file selected';
                        }
                    };

                    if (newUrl.startsWith('/')) {
                        updatePreview(newUrl);
                    } else {
                        const mediaId = parseInt(newUrl);
                        if (mediaId) {
                            fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result && result.length > 0) {
                                    let path = result[0].media_file;
                                    if (path && !path.startsWith('/')) {
                                        path = '/' + path;
                                    }
                                    updatePreview(path);
                                }
                            })
                            .catch(err => console.error('Error fetching file:', err));
                        }
                    }
                }
            }

            // Handle product3 image for multiple products section
            if (target.name.startsWith('product3_image_')) {
                const sectionId = parseInt(target.name.replace('product3_image_', ''));
                const newUrl = target.value;

                if (sectionId && newUrl) {
                    const updatePreview = (imageUrl: string) => {
                        setSectionFormDataMap(prevMap => ({
                            ...prevMap,
                            [sectionId]: {
                                ...prevMap[sectionId],
                                product3_image: imageUrl
                            }
                        }));

                        const countSpan = target.nextElementSibling as HTMLElement;
                        if (countSpan && countSpan.classList.contains('media-count')) {
                            countSpan.textContent = '1 file selected';
                        }
                    };

                    if (newUrl.startsWith('/')) {
                        updatePreview(newUrl);
                    } else {
                        const mediaId = parseInt(newUrl);
                        if (mediaId) {
                            fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            })
                            .then(response => response.json())
                            .then(result => {
                                if (result && result.length > 0) {
                                    let path = result[0].media_file;
                                    if (path && !path.startsWith('/')) {
                                        path = '/' + path;
                                    }
                                    updatePreview(path);
                                }
                            })
                            .catch(err => console.error('Error fetching file:', err));
                        }
                    }
                }
            }
        };

        // Use MutationObserver to watch for input changes
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'value') {
                    const target = mutation.target as HTMLInputElement;
                    if (target.name && (
                        target.name.startsWith('image_url_') ||
                        target.name.startsWith('brand_logo_url_') ||
                        target.name.startsWith('product_image_url_') ||
                        target.name.startsWith('column1_image_') ||
                        target.name.startsWith('column2_image_') ||
                        target.name.startsWith('column3_image_') ||
                        target.name.startsWith('quality_image_url_') ||
                        target.name.startsWith('quality_about_image_url_') ||
                        target.name.startsWith('product1_image_') ||
                        target.name.startsWith('product2_image_') ||
                        target.name.startsWith('product3_image_')
                    )) {
                        handleInputChange(new Event('change'));
                    }
                }
            });
        });

        // Also listen for "Set Files" button click which is used by media manager
        const handleSetFilesClick = () => {
            // Give the media manager a moment to update the input
            setTimeout(() => {
                const inputs = document.querySelectorAll('input[name^="image_url_"], input[name^="brand_logo_url_"], input[name^="product_image_url_"], input[name^="column1_image_"], input[name^="column2_image_"], input[name^="column3_image_"], input[name^="quality_image_url_"], input[name^="quality_about_image_url_"], input[name^="product1_image_"], input[name^="product2_image_"], input[name^="product3_image_"]');
                inputs.forEach((input) => {
                    const htmlInput = input as HTMLInputElement;
                    if (htmlInput.value) {
                        handleInputChange({ target: htmlInput } as unknown as Event);
                    }
                });
            }, 100);
        };

        // Attach click listener to "Set Files" button
        document.addEventListener('click', (e) => {
            const target = e.target as HTMLElement;
            if (target && (target.classList.contains('set-files') || target.closest('.set-files'))) {
                handleSetFilesClick();
            }
        });

        // Start observing
        observer.observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['value']
        });

        return () => {
            clearInterval(checkMediaManager);
            observer.disconnect();
        };
    }, []);

    const fetchLandingPage = async (id: string, lang: string) => {
        try {
            setLoading(true);
            const response = await fetch(`/admin/landing-pages/configure/${id}?lang_key=${lang}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to fetch landing page');
            }

            const data = await response.json();
            setLandingPage(data.landingPage);
            setTheme(data.theme || 'theme-one');

            // Set selected products if available
            if (data.landingPage.selected_products && Array.isArray(data.landingPage.selected_products)) {
                // Convert string IDs to numbers
                const productIds = data.landingPage.selected_products.map((id: string) => parseInt(id));
                setSelectedProducts(productIds);
            }

            // Set page configs if available
            if (data.landingPage.page_configs) {
                setPageConfigs({
                    theme_color: data.landingPage.page_configs.theme_color || '#1a472a',
                    primary_color: data.landingPage.page_configs.primary_color || '#E68A3D'
                });
            }

            // Set selected coupon if available
            if (data.landingPage.coupon_id) {
                setSelectedCouponId(data.landingPage.coupon_id);
            }

            // Set currency from system settings
            if (data.currency) {
                setCurrency(data.currency);
            }

            // Fetch available coupons
            fetchCoupons();

            return data.landingPage;
        } catch (err) {
            console.error('Error fetching landing page:', err);
            return null;
        } finally {
            setLoading(false);
        }
    };

    const fetchCoupons = async () => {
        try {
            const response = await fetch('/admin/api/landing-pages/coupons', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to fetch coupons');
            }

            const data = await response.json();
            if (data.success) {
                setCoupons(data.data);
            }
        } catch (err) {
            console.error('Error fetching coupons:', err);
        }
    };

    const handleCouponSearch = async (search: string) => {
        setCouponSearch(search);
        try {
            const response = await fetch(`/admin/api/landing-pages/coupons?search=${encodeURIComponent(search)}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to fetch coupons');
            }

            const data = await response.json();
            if (data.success) {
                setCoupons(data.data);
            }
        } catch (err) {
            console.error('Error searching coupons:', err);
        }
    };

    const handleSaveCoupon = async () => {
        if (!landingPage) return;

        try {
            setSavingCoupon(true);

            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/coupon`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    coupon_id: selectedCouponId,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to save coupon');
            }

            const result = await response.json();

            if (window.flash) {
                window.flash('Landing page coupon updated successfully', 'success');
            }
        } catch (err) {
            console.error('Error saving coupon:', err);
            if (window.flash) {
                window.flash('Failed to save coupon', 'error');
            }
        } finally {
            setSavingCoupon(false);
        }
    };

    const handleAddSection = async (sectionType: string) => {
        if (!landingPage) return;

        try {
            setSaving(true);

            // Initialize section_data based on section type
            let sectionData: Record<string, any> = {};

            if (sectionType === 'centered_product_offer_hero') {
                sectionData = {
                    background_color: null,
                    brand_logo_url: '',
                    product_image_url: '',
                    product_video_url: '',
                    product_offer_badge_text: '',
                    product_title: '',
                    product_subtitle: '',
                    product_old_price: '',
                    product_new_price: '',
                    product_button_text: '',
                };
            } else if (sectionType === 'centered_video_hero') {
                sectionData = {
                    background_color: null,
                    brand_logo_url: '',
                    product_image_url: '',
                    product_video_url: '',
                    product_offer_badge_text: '',
                    product_title: '',
                    product_subtitle: '',
                    product_old_price: '',
                    product_new_price: '',
                    product_button_text: '',
                };
            } else if (sectionType === 'quality_products') {
                sectionData = {
                    background_color: null,
                    quality_image_url: '',
                    quality_title: 'Best Quality Products',
                    item1_icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#E68A36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                    item1_text: 'premium cotton fabrics',
                    item2_icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#E68A36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                    item2_text: 'sustainable and eco-friendly materials',
                    item3_icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#E68A36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                    item3_text: 'handcrafted by local artisans',
                    item4_icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#E68A36" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>',
                    item4_text: 'unique, limited-edition designs',
                    button_text: 'BUY WITH CASH ON DELIVERY',
                    button_url: '#',
                };
            } else if (sectionType === 'quality_about') {
                sectionData = {
                    background_color: null,
                    quality_about_image_url: '',
                    quality_about_title: 'Best Quality Products',
                    quality_about_description: 'From the verdant valleys of Valoria to your doorstep, we bring nature\'s purest touch. Our organic journey began with a simple belief: that what you wear should be as wholesome as what you eat. Every thread tells a tale of sustainability, comfort, and a deep respect for our planet. Join us in weaving a better world, one garment at a time.',
                    quality_about_button_text: 'BUY WITH CASH ON DELIVERY',
                    quality_about_button_url: '#',
                };
            } else if (sectionType === 'multiple_products') {
                sectionData = {
                    background_color: null,
                    product1_image: '',
                    product1_title: 'Durable Denim Jeans',
                    product1_subtitle: 'We have prepared special discounts for you on',
                    product1_button_text: 'BUY WITH CASH ON DELIVERY',
                    product1_button_url: '#',
                    product2_image: '',
                    product2_title: 'Cozy Cashmere Sweater',
                    product2_subtitle: 'We have prepared special discounts for you on',
                    product2_button_text: 'BUY WITH CASH ON DELIVERY',
                    product2_button_url: '#',
                    product3_image: '',
                    product3_title: 'Elegant Evening Gown',
                    product3_subtitle: 'We have prepared special discounts for you on',
                    product3_button_text: 'BUY WITH CASH ON DELIVERY',
                    product3_button_url: '#',
                };
            } else if (sectionType === 'product_benefits') {
                sectionData = {
                    background_color: null,
                    benefits_title: 'Best Quality Products',
                    benefits_items: [
                        { text: 'Gentle on the skin, reducing irritation and allergies. Allows the skin to breathe and preventing moisture buildup.' },
                        { text: 'Environmentally friendly, as it\'s biodegradable and sustainable. Durable and long lasting, reducing the need for frequent replacements.' },
                        { text: 'Naturally soft and comfortable, enhancing overall well-being. Free from chemicals, promoting better health.' },
                        { text: 'Excellent moisture absorption, keeping you dry and comfortable. Provides natural UV protection, shielding your skin from the sun.' },
                    ],
                    benefits_button_text: 'BUY WITH CASH ON DELIVERY',
                    benefits_button_url: '#',
                };
            } else if (sectionType === 'centered_offer_hero') {
                sectionData = {
                    background_color: null,
                    offer_title: 'Best Quality Products',
                    offer_subtitle: 'We have prepared special discounts for you on',
                    offer_badge_text: 'Limited Time Offer',
                    old_price: '3000',
                    new_price: '2500',
                    button_text: 'BUY WITH CASH ON DELIVERY',
                };
            } else if (sectionType === 'carousel_images') {
                sectionData = {
                    background_color: null,
                    carousel_title: 'Image Gallery',
                    carousel_image_urls: [],
                };
            } else if (sectionType === 'product_highlights') {
                sectionData = {
                    background_color: null,
                    benefits_title: 'Product Highlights',
                    button_text: 'SHOP NOW',
                    benefits_items: [
                        { id: 'benefit-1', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', title: 'Premium Quality', description: 'Made with the finest materials for lasting durability and comfort.' },
                        { id: 'benefit-2', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', title: 'Durable', description: 'Built to withstand daily use and maintain its shape over time.' },
                        { id: 'benefit-3', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>', title: 'Fast Delivery', description: 'Quick and reliable shipping to get your order to you fast.' },
                        { id: 'benefit-4', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>', title: '24/7 Support', description: 'Our team is always here to help you with any questions.' },
                        { id: 'benefit-5', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', title: 'Best Value', description: 'Competitive pricing without compromising on quality.' },
                        { id: 'benefit-6', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>', title: 'Customer First', description: 'Your satisfaction is our top priority.' },
                    ],
                };
            } else if (sectionType === 'faq_section') {
                sectionData = {
                    background_color: null,
                    faq_title: 'Frequently Asked Questions',
                    button_text: 'BUY WITH CASH ON DELIVERY',
                    faq_items: [
                        { id: 'faq-1', question: 'What makes your products sustainable?', answer: 'We use only eco-friendly materials and ethical manufacturing processes to ensure minimal environmental impact.' },
                        { id: 'faq-2', question: 'How do you ensure quality?', answer: 'Every product undergoes rigorous quality testing before reaching our customers.' },
                        { id: 'faq-3', question: 'What is your return policy?', answer: 'We offer a 30-day hassle-free return policy on all products.' },
                    ],
                };
            } else if (sectionType === 'featured_products') {
                sectionData = {
                    background_color: null,
                    section_title: 'Featured Products',
                    featured_products: [
                        { id: 'product-1', title: 'Durable Denim Jeans', description: 'We have prepared special discounts for you on', image: '', button_text: 'BUY WITH CASH ON DELIVERY' },
                        { id: 'product-2', title: 'Cozy Cashmere Sweater', description: 'We have prepared special discounts for you on', image: '', button_text: 'BUY WITH CASH ON DELIVERY' },
                    ],
                };
            }

            const payload = {
                type: sectionType,
                section_data: sectionData,
                order: landingPage.sections.length + 1,
                is_visible: true,
            };

            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/sections`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error('Failed to add section');
            }

            const result = await response.json();

            // Refresh the landing page data
            fetchLandingPage(landingPage.id.toString(), langKey);

            // Auto-expand the newly added section
            setExpandedSections(prev => new Set([...prev, result.section.id]));

            setShowSectionList(false);

            if (window.flash) {
                window.flash('Section added successfully', 'success');
            }
        } catch (err) {
            console.error('Error adding section:', err);
            if (window.flash) {
                window.flash('Failed to add section', 'error');
            }
        } finally {
            setSaving(false);
        }
    };

    const handleSaveSection = async (sectionId: number, sectionData: SectionFormData) => {
        if (!landingPage) return;

        try {
            setSaving(true);

            // Get the actual section type from the landing page data, not from form data
            const currentSection = landingPage.sections.find(s => s.id === sectionId);
            const sectionType = currentSection?.type || sectionData.type || 'three_column_image_text';
            let sectionPayloadData: Record<string, any> = {};

            if (sectionType === 'centered_product_offer_hero') {
                // Get image paths
                const getImagePath = async (inputName: string, fallback: string): Promise<string> => {
                    const input = document.querySelector(`input[name="${inputName}"]`) as HTMLInputElement;
                    if (input && input.value) {
                        const mediaId = parseInt(input.value);
                        try {
                            const response = await fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            const result = await response.json();
                            if (result && result.length > 0) {
                                let path = result[0].media_file;
                                if (path && !path.startsWith('/')) {
                                    path = '/' + path;
                                }
                                return path;
                            }
                        } catch (err) {
                            console.error('Error fetching file:', err);
                        }
                    }
                    return fallback;
                };

                const brandLogoUrl = await getImagePath(`brand_logo_url_${sectionId}`, sectionData.brand_logo_url || '');
                const productImageUrl = await getImagePath(`product_image_url_${sectionId}`, sectionData.product_image_url || '');

                sectionPayloadData = {
                    brand_logo_url: brandLogoUrl,
                    product_image_url: productImageUrl,
                    product_video_url: sectionData.product_video_url || '',
                    offer_badge_text: sectionData.product_offer_badge_text || '',
                    product_title: sectionData.product_title || '',
                    product_subtitle: sectionData.product_subtitle || '',
                    old_price: sectionData.product_old_price || '',
                    new_price: sectionData.product_new_price || '',
                    button_text: sectionData.product_button_text || '',
                    background_color: sectionData.background_color || null,
                };
            } else if (sectionType === 'centered_video_hero') {
                // Get image paths
                const getImagePath = async (inputName: string, fallback: string): Promise<string> => {
                    const input = document.querySelector(`input[name="${inputName}"]`) as HTMLInputElement;
                    if (input && input.value) {
                        const mediaId = parseInt(input.value);
                        try {
                            const response = await fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            const result = await response.json();
                            if (result && result.length > 0) {
                                let path = result[0].media_file;
                                if (path && !path.startsWith('/')) {
                                    path = '/' + path;
                                }
                                return path;
                            }
                        } catch (err) {
                            console.error('Error fetching file:', err);
                        }
                    }
                    return fallback;
                };

                const brandLogoUrl = await getImagePath(`brand_logo_url_${sectionId}`, sectionData.brand_logo_url || '');
                const productImageUrl = await getImagePath(`product_image_url_${sectionId}`, sectionData.product_image_url || '');

                sectionPayloadData = {
                    brand_logo_url: brandLogoUrl,
                    product_image_url: productImageUrl,
                    product_video_url: sectionData.product_video_url || '',
                    offer_badge_text: sectionData.offer_badge_text || '',
                    product_title: sectionData.product_title || '',
                    product_subtitle: sectionData.product_subtitle || '',
                    old_price: sectionData.old_price || '',
                    new_price: sectionData.new_price || '',
                    button_text: sectionData.button_text || '',
                    background_color: sectionData.background_color || null,
                };
            } else if (sectionType === 'quality_products') {
                // Get image path for quality image
                const getImagePath = async (inputName: string, fallback: string): Promise<string> => {
                    const input = document.querySelector(`input[name="${inputName}"]`) as HTMLInputElement;
                    if (input && input.value) {
                        const mediaId = parseInt(input.value);
                        try {
                            const response = await fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            const result = await response.json();
                            if (result && result.length > 0) {
                                let path = result[0].media_file;
                                if (path && !path.startsWith('/')) {
                                    path = '/' + path;
                                }
                                return path;
                            }
                        } catch (err) {
                            console.error('Error fetching file:', err);
                        }
                    }
                    return fallback;
                };

                const qualityImageUrl = await getImagePath(`quality_image_url_${sectionId}`, sectionData.quality_image_url || '');

                sectionPayloadData = {
                    quality_image_url: qualityImageUrl,
                    quality_title: sectionData.quality_title || '',
                    item1_icon: sectionData.item1_icon || '',
                    item1_text: sectionData.item1_text || '',
                    item2_icon: sectionData.item2_icon || '',
                    item2_text: sectionData.item2_text || '',
                    item3_icon: sectionData.item3_icon || '',
                    item3_text: sectionData.item3_text || '',
                    item4_icon: sectionData.item4_icon || '',
                    item4_text: sectionData.item4_text || '',
                    button_text: sectionData.button_text || '',
                    button_url: sectionData.button_url || '#',
                    background_color: sectionData.background_color || null,
                };
            } else if (sectionType === 'quality_about') {
                // Get image path for quality about image
                const getImagePath = async (inputName: string, fallback: string): Promise<string> => {
                    const input = document.querySelector(`input[name="${inputName}"]`) as HTMLInputElement;
                    if (input && input.value) {
                        const mediaId = parseInt(input.value);
                        try {
                            const response = await fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            const result = await response.json();
                            if (result && result.length > 0) {
                                let path = result[0].media_file;
                                if (path && !path.startsWith('/')) {
                                    path = '/' + path;
                                }
                                return path;
                            }
                        } catch (err) {
                            console.error('Error fetching file:', err);
                        }
                    }
                    return fallback;
                };

                const qualityAboutImageUrl = await getImagePath(`quality_about_image_url_${sectionId}`, sectionData.quality_about_image_url || '');

                sectionPayloadData = {
                    quality_about_image_url: qualityAboutImageUrl,
                    quality_about_title: sectionData.quality_about_title || '',
                    quality_about_description: sectionData.quality_about_description || '',
                    quality_about_button_text: sectionData.quality_about_button_text || '',
                    quality_about_button_url: sectionData.quality_about_button_url || '#',
                    background_color: sectionData.background_color || null,
                };
            } else if (sectionType === 'multiple_products') {
                // Get image paths for product images
                const getImagePath = async (inputName: string, fallback: string): Promise<string> => {
                    const input = document.querySelector(`input[name="${inputName}"]`) as HTMLInputElement;
                    if (input && input.value) {
                        const mediaId = parseInt(input.value);
                        try {
                            const response = await fetch(`/admin/api/landing-pages/media-files?ids=${mediaId}`, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                },
                            });
                            const result = await response.json();
                            if (result && result.length > 0) {
                                let path = result[0].media_file;
                                if (path && !path.startsWith('/')) {
                                    path = '/' + path;
                                }
                                return path;
                            }
                        } catch (err) {
                            console.error('Error fetching file:', err);
                        }
                    }
                    return fallback;
                };

                const product1Image = await getImagePath(`product1_image_${sectionId}`, sectionData.product1_image || '');
                const product2Image = await getImagePath(`product2_image_${sectionId}`, sectionData.product2_image || '');
                const product3Image = await getImagePath(`product3_image_${sectionId}`, sectionData.product3_image || '');

                sectionPayloadData = {
                    product1_image: product1Image,
                    product1_title: sectionData.product1_title || '',
                    product1_subtitle: sectionData.product1_subtitle || '',
                    product1_button_text: sectionData.product1_button_text || '',
                    product1_button_url: sectionData.product1_button_url || '#',
                    product2_image: product2Image,
                    product2_title: sectionData.product2_title || '',
                    product2_subtitle: sectionData.product2_subtitle || '',
                    product2_button_text: sectionData.product2_button_text || '',
                    product2_button_url: sectionData.product2_button_url || '#',
                    product3_image: product3Image,
                    product3_title: sectionData.product3_title || '',
                    product3_subtitle: sectionData.product3_subtitle || '',
                    product3_button_text: sectionData.product3_button_text || '',
                    product3_button_url: sectionData.product3_button_url || '#',
                    background_color: sectionData.background_color || null,
                };
            } else if (sectionType === 'product_benefits') {
                sectionPayloadData = {
                    benefits_title: sectionData.benefits_title || '',
                    benefits_items: sectionData.benefits_items || [],
                    benefits_button_text: sectionData.benefits_button_text || '',
                    benefits_button_url: sectionData.benefits_button_url || '#',
                };
            } else if (sectionType === 'centered_offer_hero') {
                sectionPayloadData = {
                    offer_title: sectionData.offer_title || '',
                    offer_subtitle: sectionData.offer_subtitle || '',
                    offer_badge_text: sectionData.offer_badge_text || '',
                    old_price: sectionData.old_price || '',
                    new_price: sectionData.new_price || '',
                    button_text: sectionData.button_text || '',
                };
            } else if (sectionType === 'carousel_images') {
                sectionPayloadData = {
                    carousel_title: sectionData.carousel_title || '',
                    carousel_image_urls: sectionData.carousel_image_urls || [],
                };
            } else if (sectionType === 'featured_products') {
                sectionPayloadData = {
                    section_title: sectionData.section_title || '',
                    featured_products: sectionData.featured_products || [],
                    background_color: sectionData.background_color || null,
                };
            }

            const payload = {
                type: sectionType,
                section_data: sectionPayloadData,
                order: landingPage.sections.find(s => s.id === sectionId)?.order || 1,
                is_visible: true,
            };

            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/sections/${sectionId}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                throw new Error('Failed to save section');
            }

            // Refresh the landing page data and sync sectionFormDataMap with fresh server data
            const freshLandingPage = await fetchLandingPage(landingPage.id.toString(), langKey);

            // Update sectionFormDataMap with fresh server data for this section
            // This ensures the form stays in sync with server after save
            const updatedSection = freshLandingPage?.sections.find(s => s.id === sectionId);
            if (updatedSection) {
                setSectionFormDataMap(prevMap => ({
                    ...prevMap,
                    [sectionId]: getInitialFormData(updatedSection)
                }));
            }

            if (window.flash) {
                window.flash('Section updated successfully', 'success');
            }
        } catch (err) {
            console.error('Error saving section:', err);
            if (window.flash) {
                window.flash('Failed to save section', 'error');
            }
        } finally {
            setSaving(false);
        }
    };

    const handleDeleteSection = async (sectionId: number) => {
        if (!landingPage || !confirm('Are you sure you want to delete this section?')) return;

        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/sections/${sectionId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to delete section');
            }

            fetchLandingPage(landingPage.id.toString(), langKey);

            if (window.flash) {
                window.flash('Section deleted successfully', 'success');
            }
        } catch (err) {
            console.error('Error deleting section:', err);
            if (window.flash) {
                window.flash('Failed to delete section', 'error');
            }
        }
    };

    const handleToggleVisibility = async (section: LandingPageSection) => {
        if (!landingPage) return;

        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/sections/${section.id}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    ...section,
                    is_visible: !section.is_visible,
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to update section visibility');
            }

            fetchLandingPage(landingPage.id.toString(), langKey);
        } catch (err) {
            console.error('Error updating section visibility:', err);
            if (window.flash) {
                window.flash('Failed to update section visibility', 'error');
            }
        }
    };

    const handleToggleExpand = (sectionId: number) => {
        setExpandedSections(prev => {
            const newSet = new Set(prev);
            if (newSet.has(sectionId)) {
                newSet.delete(sectionId);
            } else {
                newSet.add(sectionId);
                // Initialize form data when expanding - always load fresh from server
                const section = landingPage?.sections.find(s => s.id === sectionId);
                if (section) {
                    setSectionFormDataMap(prevMap => ({
                        ...prevMap,
                        [sectionId]: getInitialFormData(section)
                    }));
                }
            }
            return newSet;
        });
    };

    const handleDragEnd = async (result: any) => {
        if (!result.destination || !landingPage) return;

        const items = Array.from(landingPage.sections);
        const [reorderedItem] = items.splice(result.source.index, 1);
        items.splice(result.destination.index, 0, reorderedItem);

        // Update local state immediately
        const updatedSections = items.map((section, index) => ({
            ...section,
            order: index + 1
        }));

        setLandingPage({
            ...landingPage,
            sections: updatedSections
        });

        // Save the new order to the server
        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/sections/reorder`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    sections: updatedSections.map(s => ({ id: s.id, order: s.order }))
                }),
            });

            if (!response.ok) {
                throw new Error('Failed to reorder sections');
            }

            // Refresh the landing page data
            fetchLandingPage(landingPage.id.toString(), langKey);

            if (window.flash) {
                window.flash('Sections reordered successfully', 'success');
            }
        } catch (err) {
            console.error('Error reordering sections:', err);
            if (window.flash) {
                window.flash('Failed to reorder sections', 'error');
            }
            // Revert on error
            fetchLandingPage(landingPage.id.toString(), langKey);
        }
    };

    const fetchProducts = async (search: string = '') => {
        try {
            const params = new URLSearchParams();
            if (search) {
                params.append('search', search);
            } else {
                params.append('search', '');
            }
            params.append('limit', '10');

            const response = await fetch(`/admin/api/landing-pages/products?${params.toString()}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) {
                throw new Error('Failed to fetch products');
            }

            const data = await response.json();
            let fetchedProducts = data.data || [];

            // If we have selected products that aren't in the fetched list, fetch them separately
            if (selectedProducts.length > 0) {
                const selectedProductIds = selectedProducts;
                const fetchedProductIds = fetchedProducts.map((p: Product) => p.id);
                const missingProductIds = selectedProductIds.filter(id => !fetchedProductIds.includes(id));

                if (missingProductIds.length > 0) {
                    try {
                        const selectedResponse = await fetch(`/admin/api/landing-pages/products?ids=${missingProductIds.join(',')}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });

                        if (selectedResponse.ok) {
                            const selectedData = await selectedResponse.json();
                            const selectedProductsData = selectedData.data || [];

                            // Merge and deduplicate
                            const allProducts = [...fetchedProducts, ...selectedProductsData];
                            const uniqueProducts = allProducts.filter((product: Product, index: number, self: Product[]) =>
                                index === self.findIndex((p: Product) => p.id === product.id)
                            );

                            fetchedProducts = uniqueProducts;
                        }
                    } catch (err) {
                        console.error('Error fetching selected products:', err);
                    }
                }
            }

            setProducts(fetchedProducts);
        } catch (err) {
            console.error('Error fetching products:', err);
        }
    };

    const handleSaveSelectedProducts = async () => {
        if (!landingPage) return;

        try {
            setSaving(true);

            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/selected-products`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    selected_products: selectedProducts,
                }),
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to save selected products');
            }

            const result = await response.json();

            // Refresh the landing page data
            fetchLandingPage(landingPage.id.toString(), langKey);

            if (window.flash) {
                window.flash(result.message || 'Selected products updated successfully', 'success');
            }

            setShowProductSelection(false);
        } catch (err) {
            console.error('Error saving selected products:', err);
            if (window.flash) {
                window.flash('Failed to save selected products', 'error');
            }
        } finally {
            setSaving(false);
        }
    };

    const handleSavePageConfigs = async () => {
        if (!landingPage) return;

        try {
            setSavingConfigs(true);

            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/page-configs`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    page_configs: pageConfigs,
                }),
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Failed to save page configuration');
            }

            const result = await response.json();

            if (window.flash) {
                window.flash(result.message || 'Page configuration updated successfully', 'success');
            }
        } catch (err) {
            console.error('Error saving page configs:', err);
            if (window.flash) {
                window.flash('Failed to save page configuration', 'error');
            }
        } finally {
            setSavingConfigs(false);
        }
    };

    const toggleProductSelection = (productId: number) => {
        setSelectedProducts(prev => {
            const isSelected = prev.includes(productId);
            if (isSelected) {
                return prev.filter(id => id !== productId);
            } else {
                return [...prev, productId];
            }
        });
    };

    useEffect(() => {
        if (showProductSelection) {
            fetchProducts(productSearch);
        }
    }, [showProductSelection, productSearch]);

    const getInitialFormData = (section?: LandingPageSection): SectionFormData => {
        const sectionData = section?.section_data || {};
        return {
            type: section?.type || 'three_column_image_text',
            image_url: sectionData.image_url || '',
            // Three column fields
            column1_image: sectionData.column1_image || '',
            column1_title: sectionData.column1_title || '',
            column1_description: sectionData.column1_description || '',
            column2_image: sectionData.column2_image || '',
            column2_title: sectionData.column2_title || '',
            column2_description: sectionData.column2_description || '',
            column3_image: sectionData.column3_image || '',
            column3_title: sectionData.column3_title || '',
            column3_description: sectionData.column3_description || '',
            // Three column icon fields
            column1_icon: sectionData.column1_icon || '',
            column1_icon_title: sectionData.column1_icon_title || '',
            column1_icon_description: sectionData.column1_icon_description || '',
            column2_icon: sectionData.column2_icon || '',
            column2_icon_title: sectionData.column2_icon_title || '',
            column2_icon_description: sectionData.column2_icon_description || '',
            column3_icon: sectionData.column3_icon || '',
            column3_icon_title: sectionData.column3_icon_title || '',
            column3_icon_description: sectionData.column3_icon_description || '',
            // Video fields
            video_url: sectionData.video_url || '',
            video_caption: sectionData.video_caption || '',
            // Centered product offer hero fields
            brand_logo_url: sectionData.brand_logo_url || '',
            product_image_url: sectionData.product_image_url || '',
            product_video_url: sectionData.product_video_url || '',
            product_offer_badge_text: sectionData.offer_badge_text || '',
            product_title: sectionData.product_title || '',
            product_subtitle: sectionData.product_subtitle || '',
            product_old_price: sectionData.old_price || '',
            product_new_price: sectionData.new_price || '',
            product_button_text: sectionData.product_button_text || sectionData.button_text || '',
            // Quality products fields
            quality_image_url: sectionData.quality_image_url || '',
            quality_title: sectionData.quality_title || '',
            item1_icon: sectionData.item1_icon || '',
            item1_text: sectionData.item1_text || '',
            item2_icon: sectionData.item2_icon || '',
            item2_text: sectionData.item2_text || '',
            item3_icon: sectionData.item3_icon || '',
            item3_text: sectionData.item3_text || '',
            item4_icon: sectionData.item4_icon || '',
            item4_text: sectionData.item4_text || '',
            button_url: sectionData.button_url || '#',
            // Quality about fields
            quality_about_image_url: sectionData.quality_about_image_url || '',
            quality_about_title: sectionData.quality_about_title || '',
            quality_about_description: sectionData.quality_about_description || '',
            quality_about_button_text: sectionData.quality_about_button_text || '',
            quality_about_button_url: sectionData.quality_about_button_url || '#',
            // Multiple products fields
            product1_image: sectionData.product1_image || '',
            product1_title: sectionData.product1_title || '',
            product1_subtitle: sectionData.product1_subtitle || '',
            product1_button_text: sectionData.product1_button_text || '',
            product1_button_url: sectionData.product1_button_url || '#',
            product2_image: sectionData.product2_image || '',
            product2_title: sectionData.product2_title || '',
            product2_subtitle: sectionData.product2_subtitle || '',
            product2_button_text: sectionData.product2_button_text || '',
            product2_button_url: sectionData.product2_button_url || '#',
            product3_image: sectionData.product3_image || '',
            product3_title: sectionData.product3_title || '',
            product3_subtitle: sectionData.product3_subtitle || '',
            product3_button_text: sectionData.product3_button_text || '',
            product3_button_url: sectionData.product3_button_url || '#',
            // Product benefits fields
            benefits_title: sectionData.benefits_title || '',
            benefits_items: sectionData.benefits_items || [],
            benefits_button_text: sectionData.benefits_button_text || '',
            benefits_button_url: sectionData.benefits_button_url || '#',
            // Centered offer hero fields
            offer_title: sectionData.offer_title || '',
            offer_subtitle: sectionData.offer_subtitle || '',
            offer_badge_text: sectionData.offer_badge_text || '',
            old_price: sectionData.old_price || '',
            new_price: sectionData.new_price || '',
            button_text: sectionData.button_text || '',
            // Carousel image fields
            carousel_title: sectionData.carousel_title || '',
            carousel_image_urls: sectionData.carousel_image_urls || [],
            // FAQ section fields
            faq_title: sectionData.faq_title || '',
            faq_items: sectionData.faq_items || [],
            // Featured products fields
            section_title: sectionData.section_title || '',
            featured_products: sectionData.featured_products || [],
            // Layout settings fields
            background_color: sectionData.background_color || null,
        };
    };

    if (loading) {
        return (
            <div className="flex justify-center items-center h-64">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
        );
    }

    if (!landingPage) {
        return (
            <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                Landing page not found
            </div>
        );
    }

    return (
        <div className="landing-page-configure px-4 sm:px-6 lg:px-8">
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
                <div className="lg:col-span-4 xl:col-span-3 space-y-4 lg:space-y-6">
                    {/* Page Color Configuration */}
                    <div className="card">
                        <div className="bg-primary text-primary-foreground rounded-t py-4 px-6 text-md font-semibold">
                            <i className="fa-solid fa-palette mr-2"></i>
                            Page Colors
                        </div>
                        <div className="p-4 space-y-4">
                            <p className="text-sm text-gray-600">
                                Customize the theme colors for this landing page.
                            </p>
                            
                            {/* Theme Color */}
                            <div>
                                <ColorPicker
                                    label="Theme Color (Backgrounds)"
                                    value={pageConfigs.theme_color || '#1a472a'}
                                    onChange={(color) => setPageConfigs(prev => ({ ...prev, theme_color: color }))}
                                />
                                <p className="text-xs text-gray-500 mt-1">
                                    Used for section backgrounds, cards, and headers
                                </p>
                            </div>

                            {/* Primary Color */}
                            <div>
                                <ColorPicker
                                    label="Primary Color (Buttons & Accents)"
                                    value={pageConfigs.primary_color || '#E68A3D'}
                                    onChange={(color) => setPageConfigs(prev => ({ ...prev, primary_color: color }))}
                                />
                                <p className="text-xs text-gray-500 mt-1">
                                    Used for buttons, badges, and accent elements
                                </p>
                            </div>

                            {/* Save Button */}
                            <button
                                type="button"
                                onClick={handleSavePageConfigs}
                                disabled={savingConfigs}
                                className="w-full bg-primary text-white px-4 py-2 rounded-lg font-semibold hover:bg-primary/90 text-sm flex items-center gap-2 justify-center disabled:opacity-50"
                            >
                                {savingConfigs ? (
                                    <>
                                        <i className="fa-solid fa-spinner fa-spin"></i>
                                        Saving...
                                    </>
                                ) : (
                                    <>
                                        <i className="fa-solid fa-save"></i>
                                        Save Colors
                                    </>
                                )}
                            </button>
                        </div>
                    </div>

                    {/* Coupon Selection Section */}
                    <div className="card">
                        <div className="bg-info text-white rounded-t py-4 px-6 text-md font-semibold">
                            <i className="fa-solid fa-ticket mr-2"></i>
                            Landing Page Coupon
                        </div>
                        <div className="p-4 space-y-3">
                            <p className="text-sm text-gray-600">
                                Select a coupon that will be automatically applied to orders from this landing page.
                            </p>
                            
                            {/* Coupon Search */}
                            <div>
                                <input
                                    type="text"
                                    placeholder="Search coupons..."
                                    value={couponSearch}
                                    onChange={(e) => handleCouponSearch(e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-info"
                                />
                            </div>

                            {/* Coupon Dropdown */}
                            <div>
                                <select
                                    value={selectedCouponId || ''}
                                    onChange={(e) => setSelectedCouponId(e.target.value ? parseInt(e.target.value) : null)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-info"
                                >
                                    <option value="">-- No Coupon --</option>
                                    {coupons.map((coupon) => (
                                        <option key={coupon.id} value={coupon.id}>
                                            {coupon.code} - {coupon.discount_type === 'flat' ? currency.symbol : ''}{coupon.discount_value}{coupon.discount_type === 'percentage' ? '%' : ''} off
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Selected Coupon Info */}
                            {selectedCouponId && (
                                <div className="bg-gray-50 p-3 rounded-lg text-sm">
                                    {(() => {
                                        const coupon = coupons.find(c => c.id === selectedCouponId);
                                        return coupon ? (
                                            <div>
                                                <div className="font-semibold">{coupon.code}</div>
                                                <div 
                                                    className="text-gray-600"
                                                    dangerouslySetInnerHTML={{ __html: coupon.info }}
                                                />
                                                <div className="text-info mt-1">
                                                    {coupon.discount_type === 'flat' ? currency.symbol : ''}{coupon.discount_value}{coupon.discount_type === 'percentage' ? '%' : ''} off
                                                    {coupon.is_free_shipping && ' + Free Shipping'}
                                                </div>
                                            </div>
                                        ) : null;
                                    })()}
                                </div>
                            )}

                            {/* Save Button */}
                            <button
                                type="button"
                                onClick={handleSaveCoupon}
                                disabled={savingCoupon}
                                className="w-full px-4 py-2 rounded-lg font-semibold text-sm flex items-center gap-2 justify-center disabled:opacity-50 bg-primary text-white hover:bg-primary/90"
                            >
                                {savingCoupon ? (
                                    <>
                                        <i className="fa-solid fa-spinner fa-spin"></i>
                                        Saving...
                                    </>
                                ) : (
                                    <>
                                        <i className="fa-solid fa-save"></i>
                                        Save Coupon
                                    </>
                                )}
                            </button>
                        </div>
                    </div>

                    {/* Product Selection Section - Dedicated */}
                    <div className="card">
                        <div className="bg-success text-white rounded-t py-4 px-6 text-md font-semibold">
                            <i className="fa-solid fa-box mr-2"></i>
                            Select Products for Landing Page
                        </div>
                        <div className="p-4">
                            <p className="text-sm text-gray-600 mb-3">
                                Select the products you want to display and sell on this landing page.
                            </p>
                            <button
                                type="button"
                                onClick={() => {
                                    setProductSearch('');
                                    setShowProductSelection(true);
                                }}
                                className="w-full bg-success text-gray-900 px-4 py-3 rounded-lg font-semibold hover:bg-success/90 text-sm flex items-center gap-2 justify-center"
                            >
                                <i className="fa-solid fa-box"></i>
                                Select Products ({selectedProducts.length} selected)
                            </button>
                            {selectedProducts.length === 0 && (
                                <p className="text-xs text-red-500 mt-2">
                                    <i className="fa-solid fa-exclamation-circle mr-1"></i>
                                    At least one product must be selected
                                </p>
                            )}

                            {/* Selected Products List with Remove Option */}
                            {selectedProducts.length > 0 && products.length > 0 && (
                                <div className="mt-4 border-t pt-4">
                                    <h4 className="text-xs font-semibold text-gray-700 mb-2">
                                        Selected Products ({selectedProducts.length})
                                    </h4>
                                    <div className="space-y-2 max-h-48 overflow-y-auto">
                                        {products
                                            .filter(p => selectedProducts.includes(p.id))
                                            .map((product) => (
                                                <div
                                                    key={product.id}
                                                    className="flex items-center gap-2 p-2 bg-gray-50 border border-gray-200 rounded-lg"
                                                >
                                                    {product.image && (
                                                        <img
                                                            src={product.image}
                                                            alt={product.name}
                                                            className="w-10 h-10 object-cover rounded flex-shrink-0"
                                                        />
                                                    )}
                                                    <div className="flex-1 min-w-0">
                                                        <h5 className="font-medium text-xs truncate">{product.name}</h5>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            setSelectedProducts(prev => prev.filter(id => id !== product.id));
                                                        }}
                                                        className="text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded transition-colors"
                                                        title="Remove product"
                                                    >
                                                        <i className="fa-solid fa-times text-sm"></i>
                                                    </button>
                                                </div>
                                            ))}
                                    </div>
                                    <p className="text-xs text-gray-500 mt-2">
                                        <i className="fa-solid fa-info-circle mr-1"></i>
                                        Click the X to remove a product, then click "Save Changes" below
                                    </p>
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Sections Management */}
                    <div className="card">
                        <div className="bg-primary text-primary-foreground rounded-t py-5 px-8 text-md">
                            Page Sections
                        </div>
                        <div className="init-accordion accordion-container border-collapse">
                            {/* Landing Sections Management */}
                            <div className="p-4 border-t">
                                {/* Sections List */}
                                <DragDropContext onDragEnd={handleDragEnd}>
                                    <Droppable droppableId="sections">
                                        {(provided) => (
                                            <div {...provided.droppableProps} ref={provided.innerRef} className="space-y-2 mb-3">
                                                {landingPage.sections.map((section, index) => {
                                                    const isExpanded = expandedSections.has(section.id);
                                                    // Use local form data if available, otherwise fresh server data
                                                    const sectionFormData = sectionFormDataMap[section.id]
                                                        || getInitialFormData(section);

                                                    return (
                                                        <Draggable key={section.id} draggableId={section.id.toString()} index={index}>
                                                            {(provided) => (
                                                                <div
                                                                    ref={provided.innerRef}
                                                                    {...provided.draggableProps}
                                                                    className="bg-gray-50 rounded-lg overflow-hidden"
                                                                >
                                                                    {/* Section Header */}
                                                                    <div className="p-3">
                                                                        <div className="flex items-center justify-between">
                                                                            <div className="flex items-center gap-2 flex-1">
                                                                                <div {...provided.dragHandleProps} className="cursor-move text-gray-400 hover:text-gray-600" title="Drag to reorder">
                                                                                    <i className="fa-solid fa-grip-vertical"></i>
                                                                                </div>
                                                                                <div className="flex-1">
                                                                                    <div className="font-medium text-sm">
                                                                                        {SECTION_TYPES.find(t => t.value === section.type)?.label || section.type}
                                                                                    </div>
                                                                                    <div className="text-xs text-muted">
                                                                                        Order: {section.order}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div className="flex items-center gap-2">
                                                                                {/* Check if section type is valid */}
                                                                                {SECTION_TYPES.find(t => t.value === section.type) ? (
                                                                                    <>
                                                                                        <button
                                                                                            onClick={() => handleToggleVisibility(section)}
                                                                                            className={`text-xs px-2 py-1 rounded ${section.is_visible ? 'bg-success text-white' : 'bg-warning text-gray-900'}`}
                                                                                            title={section.is_visible ? 'Hide section' : 'Show section'}
                                                                                        >
                                                                                            {section.is_visible ? 'Visible' : 'Hidden'}
                                                                                        </button>
                                                                                        <button
                                                                                            onClick={() => handleToggleExpand(section.id)}
                                                                                            className="text-gray-600 hover:text-gray-900"
                                                                                            title={isExpanded ? 'Collapse' : 'Expand'}
                                                                                        >
                                                                                            <i className={`fa-solid ${isExpanded ? 'fa-chevron-down' : 'fa-chevron-right'}`}></i>
                                                                                        </button>
                                                                                    </>
                                                                                ) : (
                                                                                    <>
                                                                                        {/* Orphaned section - not implemented/removed */}
                                                                                        <span className="text-xs px-2 py-1 rounded bg-danger text-white" title="This section type is no longer available">
                                                                                            Not Available
                                                                                        </span>
                                                                                    </>
                                                                                )}
                                                                            </div>
                                                                        </div>
                                                                        {/* Warning message for orphaned sections */}
                                                                        {!SECTION_TYPES.find(t => t.value === section.type) && (
                                                                            <div className="mt-2 flex items-center gap-2 p-2 bg-warning/20 border border-warning rounded text-xs text-warning-dark">
                                                                                <i className="fa-solid fa-triangle-exclamation flex-shrink-0"></i>
                                                                                <span className="flex-1">This section was previously added but is no longer available in the builder. Please delete it to continue.</span>
                                                                                <button
                                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                                    className="flex-shrink-0 px-3 py-1.5 bg-red-600 text-white rounded hover:bg-red-700 transition-colors font-medium"
                                                                                    title="Delete this section"
                                                                                >
                                                                                    <i className="fa-solid fa-trash mr-1"></i>
                                                                                    Delete
                                                                                </button>
                                                                            </div>
                                                                        )}
                                                                    </div>

                                                {/* Inline Section Form - Only show for valid section types */}
                                                {isExpanded && SECTION_TYPES.find(t => t.value === section.type) && (
                                                    <div className="border-t bg-white p-4 space-y-4">
                                                        {/* Centered Product Offer Hero Form */}
                                                        {section.type === 'centered_product_offer_hero' && (
                                                            <CenteredProductOfferHeroForm
                                                                sectionId={section.id}
                                                                sectionFormData={sectionFormData}
                                                                pageConfigs={pageConfigs}
                                                                onChange={(data) => {
                                                                    setSectionFormDataMap(prev => ({
                                                                        ...prev,
                                                                        [section.id]: data
                                                                    }));
                                                                }}
                                                                onSave={() => handleSaveSection(section.id, sectionFormDataMap[section.id])}
                                                                saving={saving}
                                                            />
                                                        )}

                                                        {/* Centered Video Hero Form */}
                                                        {section.type === 'centered_video_hero' && (
                                                            <CenteredVideoHeroForm
                                                                sectionId={section.id}
                                                                sectionFormData={sectionFormData}
                                                                pageConfigs={pageConfigs}
                                                                onChange={(data) => {
                                                                    setSectionFormDataMap(prev => ({
                                                                        ...prev,
                                                                        [section.id]: data
                                                                    }));
                                                                }}
                                                                onSave={() => handleSaveSection(section.id, sectionFormData)}
                                                                saving={saving}
                                                            />
                                                        )}

                                                        {/* Quality Products Section Form */}
                                                        {section.type === 'quality_products' && (
                                                            <QualityProductsForm
                                                                sectionId={section.id}
                                                                sectionFormData={sectionFormData}
                                                                pageConfigs={pageConfigs}
                                                                onChange={(data) => {
                                                                    setSectionFormDataMap(prev => ({
                                                                        ...prev,
                                                                        [section.id]: data
                                                                    }));
                                                                }}
                                                                onSave={() => handleSaveSection(section.id, sectionFormDataMap[section.id])}
                                                                saving={saving}
                                                            />
                                                        )}

                                                        {/* Quality About Section Form */}
                                                        {section.type === 'quality_about' && (
                                                            <div className="space-y-6">
                                                                {/* Section Title */}
                                                                <div>
                                                                    <label className="theme-input-label pt-0">Section Title</label>
                                                                    <input
                                                                        type="text"
                                                                        className="theme-input"
                                                                        value={sectionFormData.quality_about_title || ''}
                                                                        onChange={(e) => {
                                                                            setSectionFormDataMap(prev => ({
                                                                                ...prev,
                                                                                [section.id]: {
                                                                                    ...prev[section.id],
                                                                                    type: section.type,
                                                                                    quality_about_title: e.target.value
                                                                                }
                                                                            }));
                                                                        }}
                                                                        placeholder="Best Quality Products"
                                                                    />
                                                                </div>

                                                                {/* Description */}
                                                                <div>
                                                                    <label className="theme-input-label pt-0">Description</label>
                                                                    <textarea
                                                                        className="theme-input"
                                                                        rows={4}
                                                                        value={sectionFormData.quality_about_description || ''}
                                                                        onChange={(e) => {
                                                                            setSectionFormDataMap(prev => ({
                                                                                ...prev,
                                                                                [section.id]: {
                                                                                    ...prev[section.id],
                                                                                    type: section.type,
                                                                                    quality_about_description: e.target.value
                                                                                }
                                                                            }));
                                                                        }}
                                                                        placeholder="Enter description about your products..."
                                                                    />
                                                                </div>

                                                                {/* Product Image */}
                                                                <div>
                                                                    <label className="theme-input-label pt-0">
                                                                        Product Image <span className="text-red-500">*</span>
                                                                    </label>
                                                                    <div className="theme-input-wrapper">
                                                                        <div className="flex items-center gap-3">
                                                                            {sectionFormData.quality_about_image_url && (
                                                                                <img
                                                                                    src={sectionFormData.quality_about_image_url as string}
                                                                                    alt="Preview"
                                                                                    className="h-20 w-20 object-cover rounded"
                                                                                />
                                                                            )}
                                                                            <div
                                                                                className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                                                                                tabIndex={0}
                                                                                onClick={(e) => {
                                                                                    e.preventDefault();
                                                                                    (window as any).currentSectionId = section.id;
                                                                                    (window as any).currentFieldId = `quality-about-image-${section.id}`;
                                                                                    if ((window as any).MicroModal && (window as any).mediaManager) {
                                                                                        (window as any).MicroModal.show('media-manager-modal', {
                                                                                            onShow: (modal: any, triggeredBy: any) => {
                                                                                                if (modal.id === 'media-manager-modal' && (window as any).mediaManager) {
                                                                                                    (window as any).mediaManager.init(triggeredBy);
                                                                                                }
                                                                                            }
                                                                                        });
                                                                                    }
                                                                                }}
                                                                            >
                                                                                <div className="px-4 py-2 bg-primary text-white rounded-md rounded-e-none text-sm">
                                                                                    Browse
                                                                                </div>
                                                                                <span className="media-count py-2 flex items-center text-muted text-sm">
                                                                                    {sectionFormData.quality_about_image_url ? '1 file' : 'No file'}
                                                                                </span>
                                                                                <input
                                                                                    type="hidden"
                                                                                    id={`quality-about-image-${section.id}`}
                                                                                    name={`quality_about_image_url_${section.id}`}
                                                                                    className="hidden"
                                                                                    accept="image/*"
                                                                                    value={(sectionFormData.quality_about_image_url as string) || ''}
                                                                                />
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <p className="text-xs text-gray-500 mt-1">
                                                                    Recommended: 500x500 pixels
                                                                </p>

                                                                {/* Button Settings */}
                                                                <div className="border-t pt-4">
                                                                    <h4 className="font-medium text-sm mb-4">Button Settings</h4>
                                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Button Text</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.quality_about_button_text || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            quality_about_button_text: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="BUY WITH CASH ON DELIVERY"
                                                                            />
                                                                        </div>
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Button URL</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.quality_about_button_url || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            quality_about_button_url: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="#"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        )}

                                                        {/* Multiple Products Section Form */}
                                                        {section.type === 'multiple_products' && (
                                                            <div className="space-y-6">
                                                                {/* Product 1 */}
                                                                <div className="border-t pt-4">
                                                                    <h4 className="font-medium text-sm mb-4">Product 1</h4>
                                                                    <div>
                                                                        <label className="theme-input-label pt-0">Product 1 Image <span className="text-red-500">*</span></label>
                                                                        <div className="theme-input-wrapper">
                                                                            <div className="flex items-center gap-3">
                                                                                {sectionFormData.product1_image && (
                                                                                    <img
                                                                                        src={sectionFormData.product1_image as string}
                                                                                        alt="Preview"
                                                                                        className="h-20 w-20 object-cover rounded"
                                                                                    />
                                                                                )}
                                                                                <div
                                                                                    className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                                                                                    tabIndex={0}
                                                                                    onClick={(e) => {
                                                                                        e.preventDefault();
                                                                                        (window as any).currentSectionId = section.id;
                                                                                        (window as any).currentFieldId = `product1-image-${section.id}`;
                                                                                        if ((window as any).MicroModal && (window as any).mediaManager) {
                                                                                            (window as any).MicroModal.show('media-manager-modal', {
                                                                                                onShow: (modal: any, triggeredBy: any) => {
                                                                                                    if (modal.id === 'media-manager-modal' && (window as any).mediaManager) {
                                                                                                        (window as any).mediaManager.init(triggeredBy);
                                                                                                    }
                                                                                                }
                                                                                            });
                                                                                        }
                                                                                    }}
                                                                                >
                                                                                    <div className="px-4 py-2 bg-primary text-white rounded-md rounded-e-none text-sm">
                                                                                        Browse
                                                                                    </div>
                                                                                    <span className="media-count py-2 flex items-center text-muted text-sm">
                                                                                        {sectionFormData.product1_image ? '1 file' : 'No file'}
                                                                                    </span>
                                                                                    <input
                                                                                        type="hidden"
                                                                                        id={`product1-image-${section.id}`}
                                                                                        name={`product1_image_${section.id}`}
                                                                                        className="hidden"
                                                                                        accept="image/*"
                                                                                        value={(sectionFormData.product1_image as string) || ''}
                                                                                    />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <p className="text-xs text-gray-500 mt-1">
                                                                        Recommended: 300x300 pixels
                                                                    </p>
                                                                    <div className="mt-4">
                                                                        <label className="theme-input-label pt-0">Product 1 Title</label>
                                                                        <input
                                                                            type="text"
                                                                            className="theme-input"
                                                                            value={sectionFormData.product1_title || ''}
                                                                            onChange={(e) => {
                                                                                setSectionFormDataMap(prev => ({
                                                                                    ...prev,
                                                                                    [section.id]: {
                                                                                        ...prev[section.id],
                                                                                        type: section.type,
                                                                                        product1_title: e.target.value
                                                                                    }
                                                                                }));
                                                                            }}
                                                                            placeholder="Product 1 Title"
                                                                        />
                                                                    </div>
                                                                    <div className="mt-4">
                                                                        <label className="theme-input-label pt-0">Product 1 Subtitle</label>
                                                                        <input
                                                                            type="text"
                                                                            className="theme-input"
                                                                            value={sectionFormData.product1_subtitle || ''}
                                                                            onChange={(e) => {
                                                                                setSectionFormDataMap(prev => ({
                                                                                    ...prev,
                                                                                    [section.id]: {
                                                                                        ...prev[section.id],
                                                                                        type: section.type,
                                                                                        product1_subtitle: e.target.value
                                                                                    }
                                                                                }));
                                                                            }}
                                                                            placeholder="Product 1 Subtitle"
                                                                        />
                                                                    </div>
                                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Product 1 Button Text</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.product1_button_text || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            product1_button_text: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="BUY WITH CASH ON DELIVERY"
                                                                            />
                                                                        </div>
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Product 1 Button URL</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.product1_button_url || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            product1_button_url: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="#"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {/* Product 2 */}
                                                                <div className="border-t pt-4">
                                                                    <h4 className="font-medium text-sm mb-4">Product 2</h4>
                                                                    <div>
                                                                        <label className="theme-input-label pt-0">Product 2 Image <span className="text-red-500">*</span></label>
                                                                        <div className="theme-input-wrapper">
                                                                            <div className="flex items-center gap-3">
                                                                                {sectionFormData.product2_image && (
                                                                                    <img
                                                                                        src={sectionFormData.product2_image as string}
                                                                                        alt="Preview"
                                                                                        className="h-20 w-20 object-cover rounded"
                                                                                    />
                                                                                )}
                                                                                <div
                                                                                    className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                                                                                    tabIndex={0}
                                                                                    onClick={(e) => {
                                                                                        e.preventDefault();
                                                                                        (window as any).currentSectionId = section.id;
                                                                                        (window as any).currentFieldId = `product2-image-${section.id}`;
                                                                                        if ((window as any).MicroModal && (window as any).mediaManager) {
                                                                                            (window as any).MicroModal.show('media-manager-modal', {
                                                                                                onShow: (modal: any, triggeredBy: any) => {
                                                                                                    if (modal.id === 'media-manager-modal' && (window as any).mediaManager) {
                                                                                                        (window as any).mediaManager.init(triggeredBy);
                                                                                                    }
                                                                                                }
                                                                                            });
                                                                                        }
                                                                                    }}
                                                                                >
                                                                                    <div className="px-4 py-2 bg-primary text-white rounded-md rounded-e-none text-sm">
                                                                                        Browse
                                                                                    </div>
                                                                                    <span className="media-count py-2 flex items-center text-muted text-sm">
                                                                                        {sectionFormData.product2_image ? '1 file' : 'No file'}
                                                                                    </span>
                                                                                    <input
                                                                                        type="hidden"
                                                                                        id={`product2-image-${section.id}`}
                                                                                        name={`product2_image_${section.id}`}
                                                                                        className="hidden"
                                                                                        accept="image/*"
                                                                                        value={(sectionFormData.product2_image as string) || ''}
                                                                                    />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <p className="text-xs text-gray-500 mt-1">
                                                                        Recommended: 300x300 pixels
                                                                    </p>
                                                                    <div className="mt-4">
                                                                        <label className="theme-input-label pt-0">Product 2 Title</label>
                                                                        <input
                                                                            type="text"
                                                                            className="theme-input"
                                                                            value={sectionFormData.product2_title || ''}
                                                                            onChange={(e) => {
                                                                                setSectionFormDataMap(prev => ({
                                                                                    ...prev,
                                                                                    [section.id]: {
                                                                                        ...prev[section.id],
                                                                                        type: section.type,
                                                                                        product2_title: e.target.value
                                                                                    }
                                                                                }));
                                                                            }}
                                                                            placeholder="Product 2 Title"
                                                                        />
                                                                    </div>
                                                                    <div className="mt-4">
                                                                        <label className="theme-input-label pt-0">Product 2 Subtitle</label>
                                                                        <input
                                                                            type="text"
                                                                            className="theme-input"
                                                                            value={sectionFormData.product2_subtitle || ''}
                                                                            onChange={(e) => {
                                                                                setSectionFormDataMap(prev => ({
                                                                                    ...prev,
                                                                                    [section.id]: {
                                                                                        ...prev[section.id],
                                                                                        type: section.type,
                                                                                        product2_subtitle: e.target.value
                                                                                    }
                                                                                }));
                                                                            }}
                                                                            placeholder="Product 2 Subtitle"
                                                                        />
                                                                    </div>
                                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Product 2 Button Text</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.product2_button_text || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            product2_button_text: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="BUY WITH CASH ON DELIVERY"
                                                                            />
                                                                        </div>
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Product 2 Button URL</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.product2_button_url || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            product2_button_url: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="#"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {/* Product 3 */}
                                                                <div className="border-t pt-4">
                                                                    <h4 className="font-medium text-sm mb-4">Product 3</h4>
                                                                    <div>
                                                                        <label className="theme-input-label pt-0">Product 3 Image <span className="text-red-500">*</span></label>
                                                                        <div className="theme-input-wrapper">
                                                                            <div className="flex items-center gap-3">
                                                                                {sectionFormData.product3_image && (
                                                                                    <img
                                                                                        src={sectionFormData.product3_image as string}
                                                                                        alt="Preview"
                                                                                        className="h-20 w-20 object-cover rounded"
                                                                                    />
                                                                                )}
                                                                                <div
                                                                                    className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                                                                                    tabIndex={0}
                                                                                    onClick={(e) => {
                                                                                        e.preventDefault();
                                                                                        (window as any).currentSectionId = section.id;
                                                                                        (window as any).currentFieldId = `product3-image-${section.id}`;
                                                                                        if ((window as any).MicroModal && (window as any).mediaManager) {
                                                                                            (window as any).MicroModal.show('media-manager-modal', {
                                                                                                onShow: (modal: any, triggeredBy: any) => {
                                                                                                    if (modal.id === 'media-manager-modal' && (window as any).mediaManager) {
                                                                                                        (window as any).mediaManager.init(triggeredBy);
                                                                                                    }
                                                                                                }
                                                                                            });
                                                                                        }
                                                                                    }}
                                                                                >
                                                                                    <div className="px-4 py-2 bg-primary text-white rounded-md rounded-e-none text-sm">
                                                                                        Browse
                                                                                    </div>
                                                                                    <span className="media-count py-2 flex items-center text-muted text-sm">
                                                                                        {sectionFormData.product3_image ? '1 file' : 'No file'}
                                                                                    </span>
                                                                                    <input
                                                                                        type="hidden"
                                                                                        id={`product3-image-${section.id}`}
                                                                                        name={`product3_image_${section.id}`}
                                                                                        className="hidden"
                                                                                        accept="image/*"
                                                                                        value={(sectionFormData.product3_image as string) || ''}
                                                                                    />
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <p className="text-xs text-gray-500 mt-1">
                                                                        Recommended: 300x300 pixels
                                                                    </p>
                                                                    <div className="mt-4">
                                                                        <label className="theme-input-label pt-0">Product 3 Title</label>
                                                                        <input
                                                                            type="text"
                                                                            className="theme-input"
                                                                            value={sectionFormData.product3_title || ''}
                                                                            onChange={(e) => {
                                                                                setSectionFormDataMap(prev => ({
                                                                                    ...prev,
                                                                                    [section.id]: {
                                                                                        ...prev[section.id],
                                                                                        type: section.type,
                                                                                        product3_title: e.target.value
                                                                                    }
                                                                                }));
                                                                            }}
                                                                            placeholder="Product 3 Title"
                                                                        />
                                                                    </div>
                                                                    <div className="mt-4">
                                                                        <label className="theme-input-label pt-0">Product 3 Subtitle</label>
                                                                        <input
                                                                            type="text"
                                                                            className="theme-input"
                                                                            value={sectionFormData.product3_subtitle || ''}
                                                                            onChange={(e) => {
                                                                                setSectionFormDataMap(prev => ({
                                                                                    ...prev,
                                                                                    [section.id]: {
                                                                                        ...prev[section.id],
                                                                                        type: section.type,
                                                                                        product3_subtitle: e.target.value
                                                                                    }
                                                                                }));
                                                                            }}
                                                                            placeholder="Product 3 Subtitle"
                                                                        />
                                                                    </div>
                                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Product 3 Button Text</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.product3_button_text || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            product3_button_text: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="BUY WITH CASH ON DELIVERY"
                                                                            />
                                                                        </div>
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Product 3 Button URL</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.product3_button_url || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            product3_button_url: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="#"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        )}

                                                        {/* Product Benefits Section Form */}
                                                        {section.type === 'product_benefits' && (
                                                            <div className="space-y-6">
                                                                {/* Section Title */}
                                                                <div>
                                                                    <label className="theme-input-label pt-0">Section Title</label>
                                                                    <input
                                                                        type="text"
                                                                        className="theme-input"
                                                                        value={sectionFormData.benefits_title || ''}
                                                                        onChange={(e) => {
                                                                            setSectionFormDataMap(prev => ({
                                                                                ...prev,
                                                                                [section.id]: {
                                                                                    ...prev[section.id],
                                                                                    type: section.type,
                                                                                    benefits_title: e.target.value
                                                                                }
                                                                            }));
                                                                        }}
                                                                        placeholder="Best Quality Products"
                                                                    />
                                                                </div>

                                                                {/* Benefit Items */}
                                                                <div className="space-y-4">
                                                                    <h4 className="font-medium text-sm">Benefit Items</h4>
                                                                    {sectionFormData.benefits_items && sectionFormData.benefits_items.map((item: any, index: number) => (
                                                                        <div key={index} className="border rounded-lg p-4 bg-gray-50">
                                                                            <h5 className="font-medium text-sm mb-3">Benefit {index + 1}</h5>
                                                                            <div>
                                                                                <label className="theme-input-label pt-0">Benefit Text</label>
                                                                                <textarea
                                                                                    className="theme-input"
                                                                                    rows={3}
                                                                                    value={item.text || ''}
                                                                                    onChange={(e) => {
                                                                                        const newItems = [...(sectionFormData.benefits_items || [])];
                                                                                        newItems[index] = { ...newItems[index], text: e.target.value };
                                                                                        setSectionFormDataMap(prev => ({
                                                                                            ...prev,
                                                                                            [section.id]: {
                                                                                                ...prev[section.id],
                                                                                                type: section.type,
                                                                                                benefits_items: newItems
                                                                                            }
                                                                                        }));
                                                                                    }}
                                                                                    placeholder="Enter benefit description..."
                                                                                />
                                                                            </div>
                                                                        </div>
                                                                    ))}
                                                                    <button
                                                                        type="button"
                                                                        onClick={() => {
                                                                            const newItems = [...(sectionFormData.benefits_items || []), { text: '' }];
                                                                            setSectionFormDataMap(prev => ({
                                                                                ...prev,
                                                                                [section.id]: {
                                                                                    ...prev[section.id],
                                                                                    type: section.type,
                                                                                    benefits_items: newItems
                                                                                }
                                                                            }));
                                                                        }}
                                                                        className="btn btn-secondary btn-sm"
                                                                    >
                                                                        <i className="fa-solid fa-plus mr-2"></i>
                                                                        Add Benefit Item
                                                                    </button>
                                                                </div>

                                                                {/* Button Settings */}
                                                                <div className="border-t pt-4">
                                                                    <h4 className="font-medium text-sm mb-4">Button Settings</h4>
                                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Button Text</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.benefits_button_text || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            benefits_button_text: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="BUY WITH CASH ON DELIVERY"
                                                                            />
                                                                        </div>
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Button URL</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.benefits_button_url || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            benefits_button_url: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="#"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        )}

                                                        {/* Centered Offer Hero Section Form */}
                                                        {section.type === 'centered_offer_hero' && (
                                                            <div className="space-y-6">
                                                                {/* Section Title */}
                                                                <div>
                                                                    <label className="theme-input-label pt-0">Offer Title</label>
                                                                    <input
                                                                        type="text"
                                                                        className="theme-input"
                                                                        value={sectionFormData.offer_title || ''}
                                                                        onChange={(e) => {
                                                                            setSectionFormDataMap(prev => ({
                                                                                ...prev,
                                                                                [section.id]: {
                                                                                    ...prev[section.id],
                                                                                    type: section.type,
                                                                                    offer_title: e.target.value
                                                                                }
                                                                            }));
                                                                        }}
                                                                        placeholder="Best Quality Products"
                                                                    />
                                                                </div>

                                                                {/* Subtitle */}
                                                                <div>
                                                                    <label className="theme-input-label pt-0">Subtitle</label>
                                                                    <input
                                                                        type="text"
                                                                        className="theme-input"
                                                                        value={sectionFormData.offer_subtitle || ''}
                                                                        onChange={(e) => {
                                                                            setSectionFormDataMap(prev => ({
                                                                                ...prev,
                                                                                [section.id]: {
                                                                                    ...prev[section.id],
                                                                                    type: section.type,
                                                                                    offer_subtitle: e.target.value
                                                                                }
                                                                            }));
                                                                        }}
                                                                        placeholder="We have prepared special discounts for you on"
                                                                    />
                                                                </div>

                                                                {/* Offer Badge */}
                                                                <div>
                                                                    <label className="theme-input-label pt-0">Offer Badge Text</label>
                                                                    <input
                                                                        type="text"
                                                                        className="theme-input"
                                                                        value={sectionFormData.offer_badge_text || ''}
                                                                        onChange={(e) => {
                                                                            setSectionFormDataMap(prev => ({
                                                                                ...prev,
                                                                                [section.id]: {
                                                                                    ...prev[section.id],
                                                                                    type: section.type,
                                                                                    offer_badge_text: e.target.value
                                                                                }
                                                                            }));
                                                                        }}
                                                                        placeholder="Limited Time Offer"
                                                                    />
                                                                </div>

                                                                {/* Price Settings */}
                                                                <div className="border-t pt-4">
                                                                    <h4 className="font-medium text-sm mb-4">Price Settings</h4>
                                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">Old Price</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.old_price || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            old_price: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="3000"
                                                                            />
                                                                        </div>
                                                                        <div>
                                                                            <label className="theme-input-label pt-0">New Price</label>
                                                                            <input
                                                                                type="text"
                                                                                className="theme-input"
                                                                                value={sectionFormData.new_price || ''}
                                                                                onChange={(e) => {
                                                                                    setSectionFormDataMap(prev => ({
                                                                                        ...prev,
                                                                                        [section.id]: {
                                                                                            ...prev[section.id],
                                                                                            type: section.type,
                                                                                            new_price: e.target.value
                                                                                        }
                                                                                    }));
                                                                                }}
                                                                                placeholder="2500"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {/* Button Settings */}
                                                                <div className="border-t pt-4">
                                                                    <h4 className="font-medium text-sm mb-4">Button Settings</h4>
                                                                    <div>
                                                                        <label className="theme-input-label pt-0">Button Text</label>
                                                                        <input
                                                                            type="text"
                                                                            className="theme-input"
                                                                            value={sectionFormData.button_text || ''}
                                                                            onChange={(e) => {
                                                                                setSectionFormDataMap(prev => ({
                                                                                    ...prev,
                                                                                    [section.id]: {
                                                                                        ...prev[section.id],
                                                                                        type: section.type,
                                                                                        button_text: e.target.value
                                                                                    }
                                                                                }));
                                                                            }}
                                                                            placeholder="BUY WITH CASH ON DELIVERY"
                                                                        />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        )}

                                                        {/* Carousel Images Section Form */}
                                                        {section.type === 'carousel_images' && (
                                                            <CarouselSectionForm
                                                                sectionId={section.id}
                                                                landingPageId={landingPage.id}
                                                                sectionFormData={sectionFormData}
                                                                pageConfigs={pageConfigs}
                                                                onUpdate={() => setPreviewKey(k => k + 1)}
                                                            />
                                                        )}

                                                        {/* Product Highlights Section Form */}
                                                        {section.type === 'product_highlights' && (
                                                            <ProductHighlightsForm
                                                                sectionId={section.id}
                                                                landingPageId={landingPage.id}
                                                                sectionFormData={sectionFormData}
                                                                pageConfigs={pageConfigs}
                                                                onUpdate={() => setPreviewKey(k => k + 1)}
                                                            />
                                                        )}

                                                        {/* FAQ Section Form */}
                                                        {section.type === 'faq_section' && (
                                                            <FaqSectionForm
                                                                sectionId={section.id}
                                                                landingPageId={landingPage.id}
                                                                sectionFormData={sectionFormData}
                                                                pageConfigs={pageConfigs}
                                                                onUpdate={() => setPreviewKey(k => k + 1)}
                                                            />
                                                        )}

                                                        {/* Featured Products Form */}
                                                        {section.type === 'featured_products' && (
                                                            <FeaturedProductsForm
                                                                sectionId={section.id}
                                                                landingPageId={landingPage.id}
                                                                sectionFormData={sectionFormData}
                                                                pageConfigs={pageConfigs}
                                                                onChange={(data) => {
                                                                    setSectionFormDataMap(prev => ({
                                                                        ...prev,
                                                                        [section.id]: data
                                                                    }));
                                                                }}
                                                                onSave={() => handleSaveSection(section.id, sectionFormDataMap[section.id])}
                                                                saving={saving}
                                                            />
                                                        )}

                                                        {/* Save Button - For unknown section types */}
                                                        {section.type !== 'centered_product_offer_hero' && section.type !== 'centered_video_hero' && section.type !== 'quality_products' && section.type !== 'quality_about' && section.type !== 'multiple_products' && section.type !== 'product_benefits' && section.type !== 'centered_offer_hero' && section.type !== 'carousel_images' && section.type !== 'product_highlights' && section.type !== 'faq_section' && section.type !== 'featured_products' && (
                                                            <div className="flex justify-end">
                                                                <Button variant="primary" type="button" onClick={() => handleSaveSection(section.id, sectionFormDataMap[section.id])} isLoading={saving}>
                                                                    Save Section
                                                                </Button>
                                                            </div>
                                                        )}

                                                        {/* Save Button for Quality Products Section */}
                                                        {section.type === 'quality_products' && (
                                                            <div className="flex justify-end">
                                                                <Button variant="primary" type="button" onClick={() => handleSaveSection(section.id, sectionFormDataMap[section.id])} isLoading={saving}>
                                                                    Save Section
                                                                </Button>
                                                            </div>
                                                        )}

                                                        {/* Save Button for Quality About Section */}
                                                        {section.type === 'quality_about' && (
                                                            <div className="flex justify-end">
                                                                <Button variant="primary" type="button" onClick={() => handleSaveSection(section.id, sectionFormDataMap[section.id])} isLoading={saving}>
                                                                    Save Section
                                                                </Button>
                                                            </div>
                                                        )}

                                                        {/* Save Button for Multiple Products Section */}
                                                        {section.type === 'multiple_products' && (
                                                            <div className="flex justify-end">
                                                                <Button variant="primary" type="button" onClick={() => handleSaveSection(section.id, sectionFormDataMap[section.id])} isLoading={saving}>
                                                                    Save Section
                                                                </Button>
                                                            </div>
                                                        )}

                                                        {/* Save Button for Product Benefits Section */}
                                                        {section.type === 'product_benefits' && (
                                                            <div className="flex justify-end">
                                                                <Button variant="primary" type="button" onClick={() => handleSaveSection(section.id, sectionFormDataMap[section.id])} isLoading={saving}>
                                                                    Save Section
                                                                </Button>
                                                            </div>
                                                        )}

                                                        {/* Save Button for Centered Offer Hero Section */}
                                                        {section.type === 'centered_offer_hero' && (
                                                            <div className="flex justify-end">
                                                                <Button variant="primary" type="button" onClick={() => handleSaveSection(section.id, sectionFormDataMap[section.id])} isLoading={saving}>
                                                                    Save Section
                                                                </Button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button - For unknown section types */}
                                                        {section.type !== 'centered_product_offer_hero' && section.type !== 'centered_video_hero' && section.type !== 'quality_products' && section.type !== 'quality_about' && section.type !== 'multiple_products' && section.type !== 'product_benefits' && section.type !== 'centered_offer_hero' && section.type !== 'carousel_images' && section.type !== 'product_highlights' && section.type !== 'faq_section' && section.type !== 'featured_products' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Quality Products */}
                                                        {section.type === 'quality_products' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Quality About */}
                                                        {section.type === 'quality_about' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Multiple Products */}
                                                        {section.type === 'multiple_products' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Centered Offer Hero */}
                                                        {section.type === 'centered_offer_hero' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Centered Product Offer Hero */}
                                                        {section.type === 'centered_product_offer_hero' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Centered Video Hero */}
                                                        {section.type === 'centered_video_hero' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Product Benefits */}
                                                        {section.type === 'product_benefits' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Carousel Images */}
                                                        {section.type === 'carousel_images' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Product Highlights */}
                                                        {section.type === 'product_highlights' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for FAQ Section */}
                                                        {section.type === 'faq_section' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}

                                                        {/* Delete Section Button for Featured Products */}
                                                        {section.type === 'featured_products' && (
                                                            <div className="flex justify-end pt-4 border-t">
                                                                <button
                                                                    type="button"
                                                                    onClick={() => handleDeleteSection(section.id)}
                                                                    className="text-red-500 hover:text-red-700 flex items-center gap-2 text-sm"
                                                                    title="Delete section"
                                                                >
                                                                    <i className="fa-solid fa-trash"></i>
                                                                    <span>Delete Section</span>
                                                                </button>
                                                            </div>
                                                        )}
                                                    </div>
                                                )}
                                                                </div>
                                                            )}
                                                        </Draggable>
                                                    );
                                                })}

                                                {landingPage.sections.length === 0 && (
                                                    <p className="text-muted text-sm text-center py-4">
                                                        No sections configured yet.
                                                    </p>
                                                )}
                                            </div>
                                        )}
                                    </Droppable>
                                </DragDropContext>

                                {/* Add Section Button */}
                                <div className="flex items-center justify-center">
                                    <button
                                        type="button"
                                        onClick={() => setShowSectionList(!showSectionList)}
                                        className="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-primary/90 text-sm flex items-center gap-2"
                                    >
                                        <i className="fa-solid fa-plus"></i>
                                        Add Section
                                    </button>
                                </div>

                                {/* Expandable Section List */}
                                {showSectionList && (
                                    <div className="mt-3 border rounded-lg overflow-hidden">
                                        {SECTION_TYPES.map((sectionType) => {
                                            const sectionCount = landingPage.sections.filter(s => s.type === sectionType.value).length;
                                            const isAdded = sectionCount > 0;

                                            return (
                                                <div
                                                    key={sectionType.value}
                                                    className={`flex items-center justify-between p-3 border-b last:border-b-0 relative ${
                                                        isAdded
                                                            ? 'bg-blue-50 border-2 border-blue-500 m-1 rounded-lg'
                                                            : 'bg-gray-50 hover:bg-gray-100'
                                                    }`}
                                                >
                                                    {isAdded && (
                                                        <span className="absolute -top-2 -right-2 bg-blue-600 text-white text-xs font-bold px-2 py-0.5 rounded-full shadow-sm border-2 border-white">
                                                            {sectionCount}x
                                                        </span>
                                                    )}
                                                    <span className="text-sm font-medium">
                                                        {sectionType.label}
                                                    </span>
                                                    <div className="flex items-center gap-2">
                                                        <button
                                                            onClick={() => {
                                                                setPreviewSectionType(sectionType.value);
                                                                setShowSectionPreview(true);
                                                            }}
                                                            className="text-gray-500 hover:text-primary p-1"
                                                            title={`Preview ${sectionType.label}`}
                                                        >
                                                            <i className="fa-solid fa-eye"></i>
                                                        </button>
                                                        <button
                                                            onClick={() => handleAddSection(sectionType.value)}
                                                            className="text-primary hover:text-primary/80 p-1"
                                                            title={`Add ${sectionType.label}`}
                                                        >
                                                            <i className="fa-solid fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
                
                <div className="lg:col-span-8 xl:col-span-9">
                    {/* Preview Header */}
                    <div className="dashboard-nav py-4 flex items-center justify-between">
                        <div className="flex items-center">
                            <span className="text-sm sm:text-base font-bold">
                                Preview
                            </span>
                        </div>
                    </div>
                    
                    {/* Preview Iframe */}
                    <div className="card h-[calc(100vh-200px)] sticky top-3">
                        <iframe
                            key={previewKey}
                            src={`/landing/${landingPage.slug}?t=${previewKey}`}
                            className="w-full h-full"
                            id="landing-iframe"
                            title="Embedded Landing Page"
                        />
                    </div>
                </div>
            </div>

            {/* Product Selection Modal */}
            {showProductSelection && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-0 sm:p-4">
                    <div className="bg-white w-full h-full sm:h-auto sm:rounded-lg sm:max-w-4xl sm:max-h-[90vh] flex flex-col">
                        {/* Modal Header */}
                        <div className="p-4 sm:p-6 border-b flex items-center justify-between">
                            <div>
                                <h3 className="text-base sm:text-lg font-semibold">Select Products for Landing Page</h3>
                                <p className="text-xs sm:text-sm text-gray-500 mt-1">
                                    Search and select products to display on your landing page
                                </p>
                            </div>
                            <button
                                onClick={() => setShowProductSelection(false)}
                                className="p-2 text-gray-500 hover:text-gray-700 min-w-[44px] min-h-[44px] flex items-center justify-center"
                                aria-label="Close modal"
                            >
                                <i className="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>

                        {/* Modal Body */}
                        <div className="p-4 sm:p-6 flex-1 overflow-y-auto">
                            {/* Search */}
                            <div className="mb-4">
                                <div className="relative">
                                    <input
                                        type="text"
                                        placeholder="Search products by name or SKU..."
                                        className="theme-input w-full pl-10"
                                        value={productSearch}
                                        onChange={(e) => setProductSearch(e.target.value)}
                                    />
                                    <i className="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                </div>
                            </div>

                            {/* Selected Products */}
                            {selectedProducts.length > 0 && (
                                <div className="mb-6">
                                    <h4 className="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                        <i className="fa-solid fa-check-circle text-success"></i>
                                        Selected Products ({selectedProducts.length})
                                    </h4>
                                    <div className="space-y-2 max-h-48 overflow-y-auto">
                                        {products
                                            .filter(p => selectedProducts.includes(p.id))
                                            .map((product) => (
                                                <div
                                                    key={product.id}
                                                    className="flex items-center gap-3 p-3 bg-success/5 border border-success/20 rounded-lg"
                                                >
                                                    {product.image && (
                                                        <img
                                                            src={product.image}
                                                            alt={product.name}
                                                            className="w-12 h-12 object-cover rounded"
                                                        />
                                                    )}
                                                    <div className="flex-1 min-w-0">
                                                        <h5 className="font-medium text-sm truncate">{product.name}</h5>
                                                    </div>
                                                    <div className="text-right">
                                                        <p className="text-sm font-semibold text-primary">{currency.symbol}{product.price.toFixed(2)}</p>
                                                    </div>
                                                    <button
                                                        onClick={() => toggleProductSelection(product.id)}
                                                        className="text-red-500 hover:text-red-700 ml-2"
                                                        title="Remove from selection"
                                                    >
                                                        <i className="fa-solid fa-times"></i>
                                                    </button>
                                                </div>
                                            ))}
                                    </div>
                                </div>
                            )}

                            {/* Search Results */}
                            <div>
                                <h4 className="text-sm font-semibold text-gray-700 mb-3">
                                    {productSearch ? 'Search Results' : 'All Products'}
                                </h4>
                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    {products.map((product) => {
                                        const isSelected = selectedProducts.includes(product.id);
                                        return (
                                            <div
                                                key={product.id}
                                                className={`border rounded-lg p-4 cursor-pointer transition-all ${
                                                    isSelected
                                                        ? 'border-primary bg-primary/5 ring-2 ring-primary/20'
                                                        : 'border-gray-200 hover:border-gray-300'
                                                }`}
                                                onClick={() => toggleProductSelection(product.id)}
                                            >
                                                <div className="flex items-start gap-3">
                                                    {product.image && (
                                                        <img
                                                            src={product.image}
                                                            alt={product.name}
                                                            className="w-16 h-16 object-cover rounded"
                                                        />
                                                    )}
                                                    <div className="flex-1 min-w-0">
                                                        <h5 className="font-medium text-sm mb-1 truncate">
                                                            {product.name}
                                                        </h5>
                                                        <p className="text-sm font-semibold text-primary">
                                                            {currency.symbol}{product.price.toFixed(2)}
                                                        </p>
                                                    </div>
                                                    <div className="flex-shrink-0">
                                                        {isSelected ? (
                                                            <i className="fa-solid fa-check-circle text-primary"></i>
                                                        ) : (
                                                            <i className="fa-regular fa-circle text-gray-300"></i>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>

                                {products.length === 0 && (
                                    <p className="text-center text-gray-500 py-8">
                                        {productSearch
                                            ? `No products found matching "${productSearch}"`
                                            : 'No products available'}
                                    </p>
                                )}
                            </div>
                        </div>

                        {/* Modal Footer */}
                        <div className="p-4 sm:p-6 border-t bg-gray-50 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                            <p className="text-sm text-gray-600 text-center sm:text-left">
                                {selectedProducts.length} product(s) selected
                            </p>
                            <div className="flex items-center justify-center sm:justify-end gap-3">
                                <button
                                    type="button"
                                    onClick={() => setShowProductSelection(false)}
                                    className="flex-1 sm:flex-none px-4 sm:px-6 py-3 min-h-[44px] text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 font-medium"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    onClick={handleSaveSelectedProducts}
                                    disabled={saving || selectedProducts.length === 0}
                                    className="flex-1 sm:flex-none px-6 sm:px-8 py-3 min-h-[44px] bg-primary text-white rounded-lg hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                                >
                                    {saving ? (
                                        <>
                                            <i className="fa-solid fa-spinner fa-spin mr-2"></i>
                                            Saving...
                                        </>
                                    ) : (
                                        'Save Products'
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {showSectionPreview && (
                <SectionPreview
                    type={previewSectionType}
                    onClose={() => setShowSectionPreview(false)}
                />
            )}
        </div>
    );
};

export default LandingPageConfigureApp;