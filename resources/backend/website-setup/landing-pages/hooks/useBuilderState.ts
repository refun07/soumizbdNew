import { useState, useCallback } from 'react';

export interface LandingPage {
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

export interface PageConfigs {
    theme_color?: string;
    primary_color?: string;
    facebook_pixel_enabled?: boolean;
    payment_methods?: string[] | null; // null = use global settings
}

export interface PaymentMethod {
    value: string;
    name: string;
}

export interface Currency {
    symbol: string;
    code: string;
}

export interface Coupon {
    id: number;
    code: string;
    info: string;
    discount_type: string;
    discount_value: number;
    is_free_shipping: boolean;
    start_date: string;
    end_date: string;
}

export interface LandingPageSection {
    id: number;
    type: string;
    section_data: any;
    order: number;
    is_visible: boolean;
}

export interface Product {
    id: number;
    name: string;
    sku: string;
    image: string;
    price: number;
    variation_id: number;
}

export interface SectionFormData {
    type: string;
    background_color?: string | null;
    // Background image fields
    background_image_url?: string | null;
    background_image_position?: 'top-left' | 'top-center' | 'top-right' | 'center-left' | 'center' | 'center-right' | 'bottom-left' | 'bottom-center' | 'bottom-right' | null;
    background_image_size?: 'cover' | 'contain' | 'auto' | '100%' | '100% 100%' | null;
    background_image_repeat?: 'no-repeat' | 'repeat' | 'repeat-x' | 'repeat-y' | null;
    background_image_opacity?: number | null;
    background_image_overlay_color?: string | null;
    background_image_overlay_opacity?: number | null;
    background_image_parallax?: boolean | null;
    background_pattern?: string | null;
    image_url: string;
    // ==========================================
    // INLINE TEXT COLOR FIELDS (null = auto contrast)
    // ==========================================
    // CenteredProductOfferHero / CenteredVideoHero
    product_title_color?: string | null;
    product_subtitle_color?: string | null;
    product_offer_badge_text_color?: string | null;
    product_old_price_color?: string | null;
    product_new_price_color?: string | null;
    product_button_text_color?: string | null;
    product_button_bg_color?: string | null;
    // CenteredOfferHero
    offer_title_color?: string | null;
    offer_subtitle_color?: string | null;
    offer_badge_text_color?: string | null;
    old_price_color?: string | null;
    new_price_color?: string | null;
    centered_offer_button_text_color?: string | null;
    centered_offer_button_bg_color?: string | null;
    // FaqSection
    faq_title_color?: string | null;
    faq_question_color?: string | null;
    faq_answer_color?: string | null;
    faq_button_text_color?: string | null;
    faq_button_bg_color?: string | null;
    // FeaturedProducts
    featured_section_title_color?: string | null;
    featured_product_title_color?: string | null;
    featured_product_description_color?: string | null;
    featured_button_text_color?: string | null;
    featured_button_bg_color?: string | null;
    // MultipleProducts
    product1_title_color?: string | null;
    product1_subtitle_color?: string | null;
    product1_button_text_color?: string | null;
    product1_button_bg_color?: string | null;
    product2_title_color?: string | null;
    product2_subtitle_color?: string | null;
    product2_button_text_color?: string | null;
    product2_button_bg_color?: string | null;
    product3_title_color?: string | null;
    product3_subtitle_color?: string | null;
    product3_button_text_color?: string | null;
    product3_button_bg_color?: string | null;
    // ProductHighlights
    highlights_title_color?: string | null;
    benefit_title_color?: string | null;
    benefit_description_color?: string | null;
    highlights_button_text_color?: string | null;
    highlights_button_bg_color?: string | null;
    // QualityProducts
    quality_title_color?: string | null;
    feature_text_color?: string | null;
    quality_button_text_color?: string | null;
    quality_button_bg_color?: string | null;
    // QualityAbout
    quality_about_title_color?: string | null;
    quality_about_description_color?: string | null;
    quality_about_button_text_color?: string | null;
    quality_about_button_bg_color?: string | null;
    // CarouselSection
    carousel_title_color?: string | null;
    // ProductBenefits
    benefits_title_color?: string | null;
    benefits_item_color?: string | null;
    benefits_button_text_color?: string | null;
    benefits_button_bg_color?: string | null;
    // ==========================================
    column1_image?: string;
    column1_title?: string;
    column1_description?: string;
    column2_image?: string;
    column2_title?: string;
    column2_description?: string;
    column3_image?: string;
    column3_title?: string;
    column3_description?: string;
    column1_icon?: string;
    column1_icon_title?: string;
    column1_icon_description?: string;
    column2_icon?: string;
    column2_icon_title?: string;
    column2_icon_description?: string;
    column3_icon?: string;
    column3_icon_title?: string;
    column3_icon_description?: string;
    video_url?: string;
    video_caption?: string;
    brand_logo_url?: string;
    product_image_url?: string;
    product_video_url?: string;
    product_offer_badge_text?: string;
    product_title?: string;
    product_subtitle?: string;
    product_old_price?: string;
    product_new_price?: string;
    product_button_text?: string;
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
    quality_about_image_url?: string;
    quality_about_title?: string;
    quality_about_description?: string;
    quality_about_button_text?: string;
    quality_about_button_url?: string;
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
    benefits_title?: string;
    benefits_items?: Array<{text: string}>;
    benefits_button_text?: string;
    benefits_button_url?: string;
    offer_title?: string;
    offer_subtitle?: string;
    offer_badge_text?: string;
    old_price?: string;
    new_price?: string;
    button_text?: string;
    carousel_title?: string;
    carousel_image_urls?: string[];
    faq_title?: string;
    faq_items?: Array<{id: string; question: string; answer: string}>;
    section_title?: string;
    featured_products?: Array<{
        id: string;
        title: string;
        description: string;
        image: string;
        button_text: string;
    }>;
}

export type DeviceType = 'desktop' | 'tablet' | 'mobile';

export function useBuilderState() {
    const [landingPage, setLandingPage] = useState<LandingPage | null>(null);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [loadingProducts, setLoadingProducts] = useState(false);
    const [langKey, setLangKey] = useState('en-US');
    
    // UI State
    const [activePanel, setActivePanel] = useState<'library' | 'settings' | null>(null);
    const [editingSectionId, setEditingSectionId] = useState<number | null>(null);
    const [deviceType, setDeviceType] = useState<DeviceType>('desktop');
    const [hoveredSectionId, setHoveredSectionId] = useState<number | null>(null);
    
    // Data State
    const [sections, setSections] = useState<LandingPageSection[]>([]);
    const [sectionFormDataMap, setSectionFormDataMap] = useState<Record<number, SectionFormData>>({});
    const [pageConfigs, setPageConfigs] = useState<PageConfigs>({
        theme_color: '#1a472a',
        primary_color: '#E68A3D'
    });
    const [selectedProducts, setSelectedProducts] = useState<number[]>([]);
    const [selectedCouponId, setSelectedCouponId] = useState<number | null>(null);
    const [products, setProducts] = useState<Product[]>([]);
    const [coupons, setCoupons] = useState<Coupon[]>([]);
    const [facebookPixelActive, setFacebookPixelActive] = useState<boolean>(false);
    const [metaPixelId, setMetaPixelId] = useState<string>('');
    const [availablePaymentMethods, setAvailablePaymentMethods] = useState<PaymentMethod[]>([]);
    const [selectedPaymentMethods, setSelectedPaymentMethods] = useState<string[] | null>(null);
    const [currency, setCurrency] = useState<Currency>({
        symbol: '$',
        code: 'USD',
    });

    const fetchLandingPage = useCallback(async (id: string, lang: string) => {
        try {
            setLoading(true);
            const response = await fetch(`/admin/landing-pages/configure/${id}?lang_key=${lang}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) throw new Error('Failed to fetch landing page');

            const result = await response.json();
            setLandingPage(result.landingPage);
            setSections(result.landingPage.sections || []);
            setPageConfigs(result.landingPage.page_configs || {
                theme_color: '#1a472a',
                primary_color: '#E68A3D'
            });
            setSelectedProducts(result.landingPage.selected_products || []);
            setSelectedCouponId(result.landingPage.coupon_id || null);
            setFacebookPixelActive(result.facebookPixelActive || false);
            setMetaPixelId(result.metaPixelId || '');
            setSelectedPaymentMethods(result.landingPage.page_configs?.payment_methods ?? null);
            setCurrency(result.currency || { symbol: '$', code: 'USD' });
            setLangKey(lang);
        } catch (err) {
            console.error('Error fetching landing page:', err);
            if (window.flash) window.flash('Failed to load landing page', 'error');
        } finally {
            setLoading(false);
        }
    }, []);

    const fetchProducts = useCallback(async (search?: string) => {
        try {
            setLoadingProducts(true);
            const url = search
                ? `/admin/api/landing-pages/products?search=${encodeURIComponent(search)}&limit=20`
                : '/admin/api/landing-pages/products?limit=20';
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!response.ok) throw new Error('Failed to fetch products');
            const result = await response.json();
            setProducts(result.data || []);
        } catch (err) {
            console.error('Error fetching products:', err);
        } finally {
            setLoadingProducts(false);
        }
    }, []);

    const fetchCoupons = useCallback(async () => {
        try {
            const response = await fetch('/admin/api/landing-pages/coupons', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!response.ok) throw new Error('Failed to fetch coupons');
            const result = await response.json();
            setCoupons(result.data || []);
        } catch (err) {
            console.error('Error fetching coupons:', err);
        }
    }, []);

    const fetchPaymentMethods = useCallback(async () => {
        try {
            const response = await fetch('/admin/api/landing-pages/payment-methods', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            if (!response.ok) throw new Error('Failed to fetch payment methods');
            const result = await response.json();
            setAvailablePaymentMethods(result.data || []);
        } catch (err) {
            console.error('Error fetching payment methods:', err);
        }
    }, []);

    const addSection = useCallback(async (sectionType: string, targetIndex?: number) => {
        if (!landingPage) return;

        try {
            setSaving(true);
            
            let sectionData: any = {};
            
            // Default data for each section type
            switch (sectionType) {
                case 'centered_product_offer_hero':
                    sectionData = {
                        background_color: null,
                        brand_logo_url: 'https://picsum.photos/id/180/200/60',
                        product_image_url: 'https://picsum.photos/id/250/800/600',
                        product_video_url: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                        product_offer_badge_text: '50% OFF',
                        product_title: 'Premium Product Name',
                        product_subtitle: 'Best quality guaranteed with premium materials',
                        product_old_price: '1000',
                        product_new_price: '500',
                        product_button_text: 'BUY WITH CASH ON DELIVERY',
                    };
                    break;
                case 'centered_video_hero':
                    sectionData = {
                        background_color: null,
                        brand_logo_url: 'https://picsum.photos/id/180/200/60',
                        product_image_url: 'https://picsum.photos/id/365/800/600',
                        product_video_url: 'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/BigBuckBunny.mp4',
                        product_offer_badge_text: 'Limited Time Offer',
                        product_title: 'Premium Product Name',
                        product_subtitle: 'Best quality guaranteed',
                        product_old_price: '1000',
                        product_new_price: '500',
                        product_button_text: 'BUY WITH CASH ON DELIVERY',
                    };
                    break;
                case 'centered_offer_hero':
                    sectionData = {
                        background_color: null,
                        offer_title: 'Best Quality Products',
                        offer_subtitle: 'We have prepared special discounts for you',
                        offer_badge_text: 'Limited Time Offer',
                        old_price: '3000',
                        new_price: '2500',
                        button_text: 'BUY WITH CASH ON DELIVERY',
                    };
                    break;
                case 'quality_products':
                    sectionData = {
                        background_color: null,
                        quality_image_url: 'https://picsum.photos/id/180/600/800',
                        quality_title: 'Quality Products',
                        item1_icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
                        item1_text: 'Premium Quality Materials',
                        item2_icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>',
                        item2_text: 'Fast Delivery',
                        item3_icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>',
                        item3_text: '24/7 Support',
                        item4_icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>',
                        item4_text: 'Best Value',
                        button_text: 'SHOP NOW',
                        button_url: '#',
                    };
                    break;
                case 'quality_about':
                    sectionData = {
                        background_color: null,
                        quality_about_image_url: 'https://picsum.photos/id/180/600/800',
                        quality_about_title: 'About Our Quality',
                        quality_about_description: 'We provide the best quality products...',
                        quality_about_button_text: 'LEARN MORE',
                        quality_about_button_url: '#',
                    };
                    break;
                case 'multiple_products':
                    sectionData = {
                        background_color: null,
                        product1_image: 'https://picsum.photos/id/10/400/400',
                        product1_title: 'Product 1',
                        product1_subtitle: 'Best seller',
                        product1_button_text: 'BUY NOW',
                        product1_button_url: '#',
                        product2_image: 'https://picsum.photos/id/21/400/400',
                        product2_title: 'Product 2',
                        product2_subtitle: 'New arrival',
                        product2_button_text: 'BUY NOW',
                        product2_button_url: '#',
                        product3_image: 'https://picsum.photos/id/30/400/400',
                        product3_title: 'Product 3',
                        product3_subtitle: 'Popular',
                        product3_button_text: 'BUY NOW',
                        product3_button_url: '#',
                    };
                    break;
                case 'product_benefits':
                    sectionData = {
                        background_color: null,
                        benefits_title: 'Product Benefits',
                        benefits_items: [
                            { text: 'Premium quality materials' },
                            { text: 'Fast and reliable shipping' },
                            { text: '24/7 customer support' },
                        ],
                        benefits_button_text: 'SHOP NOW',
                        benefits_button_url: '#',
                    };
                    break;
                case 'product_highlights':
                    sectionData = {
                        background_color: null,
                        benefits_title: 'Product Highlights',
                        button_text: 'SHOP NOW',
                        benefits_items: [
                            { id: 'benefit-1', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>', title: 'Premium Quality', description: 'Made with finest materials' },
                            { id: 'benefit-2', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>', title: 'Durable', description: 'Built to last' },
                            { id: 'benefit-3', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>', title: 'Fast Delivery', description: 'Quick shipping' },
                            { id: 'benefit-4', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>', title: '24/7 Support', description: 'Always here to help' },
                            { id: 'benefit-5', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>', title: 'Best Value', description: 'Competitive pricing' },
                            { id: 'benefit-6', icon: '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#C7782F" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>', title: 'Customer First', description: 'Your satisfaction matters' },
                        ],
                    };
                    break;
                case 'carousel_images':
                    sectionData = {
                        background_color: null,
                        carousel_title: 'Image Gallery',
                        carousel_image_urls: [
                            'https://picsum.photos/id/1015/800/600',
                            'https://picsum.photos/id/1016/800/600',
                            'https://picsum.photos/id/1018/800/600',
                            'https://picsum.photos/id/1019/800/600',
                            'https://picsum.photos/id/1021/800/600',
                        ],
                    };
                    break;
                case 'faq_section':
                    sectionData = {
                        background_color: null,
                        faq_title: 'Frequently Asked Questions',
                        button_text: 'BUY WITH CASH ON DELIVERY',
                        faq_items: [
                            { id: 'faq-1', question: 'What makes your products sustainable?', answer: 'We use eco-friendly materials.' },
                            { id: 'faq-2', question: 'How do you ensure quality?', answer: 'Rigorous testing process.' },
                            { id: 'faq-3', question: 'What is your return policy?', answer: '30-day hassle-free returns.' },
                        ],
                    };
                    break;
                case 'featured_products':
                    sectionData = {
                        background_color: null,
                        section_title: 'Featured Products',
                        featured_products: [
                            { id: 'product-1', title: 'Product 1', description: 'Special discount', image: 'https://picsum.photos/id/10/400/400', button_text: 'BUY WITH CASH ON DELIVERY' },
                            { id: 'product-2', title: 'Product 2', description: 'Special discount', image: 'https://picsum.photos/id/21/400/400', button_text: 'BUY WITH CASH ON DELIVERY' },
                        ],
                    };
                    break;
            }

            // Calculate order: if targetIndex is provided, use that position, otherwise add at end
            const order = targetIndex !== undefined ? targetIndex + 1 : sections.length + 1;

            const payload = {
                type: sectionType,
                section_data: sectionData,
                order: order,
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

            if (!response.ok) throw new Error('Failed to add section');

            const result = await response.json();
            const newSection = result.section;
            
            // Add section directly to local state without full page reload
            setSections(currentSections => {
                let updatedSections = [...currentSections];
                
                if (targetIndex !== undefined && targetIndex < updatedSections.length) {
                    // Insert at specific position
                    updatedSections.splice(targetIndex, 0, newSection);
                } else {
                    // Add at the end
                    updatedSections.push(newSection);
                }
                
                // Recalculate order numbers
                const reorderedSections = updatedSections.map((s, idx) => ({
                    ...s,
                    order: idx + 1
                }));
                
                // Save reorder to backend in background (don't wait)
                fetch(`/admin/api/landing-pages/${landingPage.id}/sections/reorder`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        sections: reorderedSections.map(s => ({ id: s.id, order: s.order }))
                    }),
                }).catch(err => {
                    console.error('Error reordering sections:', err);
                });
                
                return reorderedSections;
            });
            
            if (window.flash) window.flash('Section added successfully', 'success');
        } catch (err) {
            console.error('Error adding section:', err);
            if (window.flash) window.flash('Failed to add section', 'error');
        } finally {
            setSaving(false);
        }
    }, [landingPage, sections, langKey, fetchLandingPage]);

    const updateSection = useCallback(async (sectionId: number, sectionData: any) => {
        if (!landingPage) return;

        try {
            setSaving(true);
            const section = sections.find(s => s.id === sectionId);
            if (!section) return;

            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/sections/${sectionId}`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    section_data: sectionData,
                    type: section.type,
                    order: section.order,
                    is_visible: section.is_visible,
                }),
            });

            if (!response.ok) throw new Error('Failed to update section');

            // Update local state
            setSections(prev => prev.map(s => 
                s.id === sectionId ? { ...s, section_data: sectionData } : s
            ));

            if (window.flash) window.flash('Section updated successfully', 'success');
        } catch (err) {
            console.error('Error updating section:', err);
            if (window.flash) window.flash('Failed to update section', 'error');
        } finally {
            setSaving(false);
        }
    }, [landingPage, sections]);

    const deleteSection = useCallback(async (sectionId: number) => {
        if (!landingPage) return;

        try {
            setSaving(true);
            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/sections/${sectionId}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (!response.ok) throw new Error('Failed to delete section');

            setSections(prev => prev.filter(s => s.id !== sectionId));
            if (editingSectionId === sectionId) setEditingSectionId(null);

            if (window.flash) window.flash('Section deleted successfully', 'success');
        } catch (err) {
            console.error('Error deleting section:', err);
            if (window.flash) window.flash('Failed to delete section', 'error');
        } finally {
            setSaving(false);
        }
    }, [landingPage, editingSectionId]);

    const duplicateSection = useCallback(async (sectionId: number) => {
        if (!landingPage) return;

        try {
            setSaving(true);
            const section = sections.find(s => s.id === sectionId);
            if (!section) return;

            const payload = {
                type: section.type,
                section_data: section.section_data,
                order: sections.length + 1,
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

            if (!response.ok) throw new Error('Failed to duplicate section');

            const result = await response.json();
            const newSection = result.section;
            
            // Add duplicated section directly to local state without full page reload
            setSections(currentSections => {
                const updatedSections = [...currentSections, newSection];
                
                // Recalculate order numbers
                const reorderedSections = updatedSections.map((s, idx) => ({
                    ...s,
                    order: idx + 1
                }));
                
                return reorderedSections;
            });
            
            if (window.flash) window.flash('Section duplicated successfully', 'success');
        } catch (err) {
            console.error('Error duplicating section:', err);
            if (window.flash) window.flash('Failed to duplicate section', 'error');
        } finally {
            setSaving(false);
        }
    }, [landingPage, sections, langKey, fetchLandingPage]);

    const reorderSection = useCallback(async (sectionId: number, direction: 'up' | 'down') => {
        if (!landingPage) return;

        const currentIndex = sections.findIndex(s => s.id === sectionId);
        if (currentIndex === -1) return;

        const newIndex = direction === 'up' ? currentIndex - 1 : currentIndex + 1;
        if (newIndex < 0 || newIndex >= sections.length) return;

        const newSections = [...sections];
        const [movedSection] = newSections.splice(currentIndex, 1);
        newSections.splice(newIndex, 0, movedSection);

        // Update order numbers
        const reorderedSections = newSections.map((s, idx) => ({
            ...s,
            order: idx + 1
        }));

        setSections(reorderedSections);

        try {
            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/sections/reorder`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    sections: reorderedSections.map(s => ({ id: s.id, order: s.order }))
                }),
            });

            if (!response.ok) throw new Error('Failed to reorder sections');
        } catch (err) {
            console.error('Error reordering sections:', err);
            // Revert on error
            fetchLandingPage(landingPage.id.toString(), langKey);
        }
    }, [landingPage, sections, langKey, fetchLandingPage]);

    const savePageConfigs = useCallback(async (configsToSave?: PageConfigs) => {
        if (!landingPage) return;

        try {
            setSaving(true);
            const configs = configsToSave ?? pageConfigs;
            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/page-configs`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ page_configs: configs }),
            });

            if (!response.ok) throw new Error('Failed to save configs');

            if (window.flash) window.flash('Page settings saved successfully', 'success');
        } catch (err) {
            console.error('Error saving configs:', err);
            if (window.flash) window.flash('Failed to save page settings', 'error');
        } finally {
            setSaving(false);
        }
    }, [landingPage, pageConfigs]);

    const saveSelectedProducts = useCallback(async (productsToSave?: number[]) => {
        if (!landingPage) return;

        try {
            setSaving(true);
            const products = productsToSave ?? selectedProducts;
            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/selected-products`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ selected_products: products }),
            });

            if (!response.ok) throw new Error('Failed to save products');

            if (window.flash) window.flash('Products saved successfully', 'success');
        } catch (err) {
            console.error('Error saving products:', err);
            if (window.flash) window.flash('Failed to save products', 'error');
        } finally {
            setSaving(false);
        }
    }, [landingPage, selectedProducts]);

    const saveCoupon = useCallback(async (couponIdToSave?: number | null) => {
        if (!landingPage) return;

        try {
            setSaving(true);
            const couponId = couponIdToSave !== undefined ? couponIdToSave : selectedCouponId;
            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/coupon`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ coupon_id: couponId }),
            });

            if (!response.ok) throw new Error('Failed to save coupon');

            if (window.flash) window.flash('Coupon saved successfully', 'success');
        } catch (err) {
            console.error('Error saving coupon:', err);
            if (window.flash) window.flash('Failed to save coupon', 'error');
        } finally {
            setSaving(false);
        }
    }, [landingPage, selectedCouponId]);

    const savePaymentMethods = useCallback(async (methodsToSave?: string[] | null) => {
        if (!landingPage) return;

        try {
            setSaving(true);
            const methods = methodsToSave !== undefined ? methodsToSave : selectedPaymentMethods;
            
            // Merge with existing page_configs
            const updatedConfigs = {
                ...pageConfigs,
                payment_methods: methods
            };
            
            const response = await fetch(`/admin/api/landing-pages/${landingPage.id}/page-configs`, {
                method: 'PUT',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ page_configs: updatedConfigs }),
            });

            if (!response.ok) throw new Error('Failed to save payment methods');

            if (window.flash) window.flash('Payment methods saved successfully', 'success');
        } catch (err) {
            console.error('Error saving payment methods:', err);
            if (window.flash) window.flash('Failed to save payment methods', 'error');
        } finally {
            setSaving(false);
        }
    }, [landingPage, selectedPaymentMethods, pageConfigs]);

    return {
        // State
        landingPage,
        loading,
        saving,
        loadingProducts,
        sections,
        sectionFormDataMap,
        pageConfigs,
        selectedProducts,
        selectedCouponId,
        products,
        coupons,
        langKey,
        deviceType,
        activePanel,
        editingSectionId,
        hoveredSectionId,
        facebookPixelActive,
        metaPixelId,
        availablePaymentMethods,
        selectedPaymentMethods,
        currency,

        // Setters
        setActivePanel,
        setEditingSectionId,
        setDeviceType,
        setHoveredSectionId,
        setSectionFormDataMap,
        setPageConfigs,
        setSelectedProducts,
        setSelectedCouponId,
        setSelectedPaymentMethods,

        // Actions
        fetchLandingPage,
        fetchProducts,
        fetchCoupons,
        fetchPaymentMethods,
        addSection,
        updateSection,
        deleteSection,
        duplicateSection,
        reorderSection,
        savePageConfigs,
        saveSelectedProducts,
        saveCoupon,
        savePaymentMethods,
    };
}
