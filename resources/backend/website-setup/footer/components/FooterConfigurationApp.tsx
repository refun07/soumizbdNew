import React, { useState, useEffect, useRef, useCallback } from 'react';
import { FaXmark } from 'react-icons/fa6';
import ToastContainer from '../../theme/components/Toast';
import useToast from '../../theme/hooks/useToast';

interface FooterConfigurationAppProps {
    apiPrefix: string;
}

interface QuickLink {
    id: number;
    title: string;
    url: string;
}

interface ImportantLink {
    id: number;
    title: string;
    link: string;
    is_enabled: boolean;
}

interface FooterInfoItem {
    title: string;
    description: string;
}

type CompanyInfoAlignment = 'left' | 'center' | 'right' | 'justify';
type CopyrightAlignment = 'left' | 'center' | 'right';

const defaultFooterInfoItems: FooterInfoItem[] = [
    {
        title: 'Return Policy',
        description: 'If a product is damaged, faulty, or different than ordered, we offer a return for that product.'
    },
    {
        title: 'Delivery System',
        description: 'We provide cash on delivery. You can check and try the product at the delivery point before receiving.'
    },
    {
        title: 'Delivery Charges',
        description: 'Delivery charges are as follows:\nInside Dhaka: 70TK\nOutside Dhaka: 150TK'
    },
    {
        title: 'Delivery Time',
        description: 'Delivery Time is as follows:\nInside Dhaka: 2-3 Days\nOutside Dhaka: 4-5 Days'
    }
];

const FooterConfigurationApp: React.FC<FooterConfigurationAppProps> = ({
    apiPrefix
}) => {
    // Company Info
    const [logo, setLogo] = useState('');
    const [logoSize, setLogoSize] = useState<'small' | 'medium' | 'large'>('medium');
    const [description, setDescription] = useState('');
    const [companyInfoAlignment, setCompanyInfoAlignment] = useState<CompanyInfoAlignment>('left');
    const [customerSupport, setCustomerSupport] = useState('');
    const [customerSupportSecondary, setCustomerSupportSecondary] = useState('');
    const [address, setAddress] = useState('');
    const [email, setEmail] = useState('');

    // Footer Two Info Row
    const [showInfoBar, setShowInfoBar] = useState(true);
    const [infoItems, setInfoItems] = useState<FooterInfoItem[]>(defaultFooterInfoItems);
    const [infoBgColor, setInfoBgColor] = useState('#ffffff');
    const [infoIconColor, setInfoIconColor] = useState('#8f5847');
    const [infoTitleColor, setInfoTitleColor] = useState('#111827');
    const [infoTextColor, setInfoTextColor] = useState('#0f172a');

    // Social Links
    const [showFacebook, setShowFacebook] = useState(true);
    const [facebookLink, setFacebookLink] = useState('');
    const [showTwitter, setShowTwitter] = useState(true);
    const [twitterLink, setTwitterLink] = useState('');
    const [showInstagram, setShowInstagram] = useState(true);
    const [instagramLink, setInstagramLink] = useState('');
    const [showYoutube, setShowYoutube] = useState(true);
    const [youtubeLink, setYoutubeLink] = useState('');
    const [showLinkedIn, setShowLinkedIn] = useState(true);
    const [linkedInLink, setLinkedInLink] = useState('');
    const [showTikTok, setShowTikTok] = useState(true);
    const [tikTokLink, setTikTokLink] = useState('');
    const [showPinterest, setShowPinterest] = useState(true);
    const [pinterestLink, setPinterestLink] = useState('');

    // Quick Links
    const [quickLinksTitles, setQuickLinksTitles] = useState<string[]>([]);
    const [quickLinksUrls, setQuickLinksUrls] = useState<string[]>([]);
    const [quickLinks, setQuickLinks] = useState<QuickLink[]>([]);

    // Important Links
    const [importantLinks, setImportantLinks] = useState<ImportantLink[]>([]);

    // App Links
    const [showPlayStore, setShowPlayStore] = useState(true);
    const [playStoreLink, setPlayStoreLink] = useState('');
    const [showAppStore, setShowAppStore] = useState(true);
    const [appStoreLink, setAppStoreLink] = useState('');

    // Bottom
    const [copyrightText, setCopyrightText] = useState('');
    const [copyrightAlignment, setCopyrightAlignment] = useState<CopyrightAlignment>('center');
    const [securedPayments, setSecuredPayments] = useState('');
    const [securedPaymentsSize, setSecuredPaymentsSize] = useState<'small' | 'medium' | 'large'>('medium');

    // Colors
    const [footerBgColor, setFooterBgColor] = useState('#1a1a2e');
    const [footerTextColor, setFooterTextColor] = useState('#ffffff');
    const [copyrightBgColor, setCopyrightBgColor] = useState('#22c55e');
    const [copyrightTextColor, setCopyrightTextColor] = useState('#ffffff');

    // Google Maps
    const [showGoogleMaps, setShowGoogleMaps] = useState(true);
    const [googleMapsEnabled, setGoogleMapsEnabled] = useState(true);
    const [googleMapsType, setGoogleMapsType] = useState<'embed' | 'link'>('embed');
    const [googleMapsValue, setGoogleMapsValue] = useState('');

    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const pollingIntervalRef = useRef<number | null>(null);
    const { toasts, removeToast, success, error } = useToast();

    // Load configuration
    useEffect(() => {
        const loadConfig = async () => {
            try {
                const response = await fetch(`${apiPrefix}/footer/config`);
                const result = await response.json();

                if (result.success) {
                    // Company Info
                    setLogo(result.data.logo || '');
                    setLogoSize(result.data.logo_size || 'medium');
                    setDescription(result.data.description || '');
                    setCompanyInfoAlignment(result.data.company_info_alignment || 'left');
                    setCustomerSupport(result.data.customer_support || '');
                    setCustomerSupportSecondary(result.data.customer_support_secondary || '');
                    setAddress(result.data.address || '');
                    setEmail(result.data.email || '');

                    // Footer Two Info Row
                    setShowInfoBar(result.data.show_info_bar ?? true);
                    setInfoItems(
                        Array.isArray(result.data.info_items) && result.data.info_items.length > 0
                            ? result.data.info_items
                            : defaultFooterInfoItems
                    );
                    setInfoBgColor(result.data.info_bg_color || '#ffffff');
                    setInfoIconColor(result.data.info_icon_color || '#8f5847');
                    setInfoTitleColor(result.data.info_title_color || '#111827');
                    setInfoTextColor(result.data.info_text_color || '#0f172a');

                    // Social Links
                    setShowFacebook(result.data.show_facebook ?? true);
                    setFacebookLink(result.data.facebook_link || '');
                    setShowTwitter(result.data.show_twitter ?? true);
                    setTwitterLink(result.data.twitter_link || '');
                    setShowInstagram(result.data.show_instagram ?? true);
                    setInstagramLink(result.data.instagram_link || '');
                    setShowYoutube(result.data.show_youtube ?? true);
                    setYoutubeLink(result.data.youtube_link || '');
                    setShowLinkedIn(result.data.show_linkedin ?? true);
                    setLinkedInLink(result.data.linkedin_link || '');
                    setShowTikTok(result.data.show_tiktok ?? true);
                    setTikTokLink(result.data.tiktok_link || '');
                    setShowPinterest(result.data.show_pinterest ?? true);
                    setPinterestLink(result.data.pinterest_link || '');

                    // Quick Links
                    setQuickLinksTitles(result.data.quick_links_titles || []);
                    setQuickLinksUrls(result.data.quick_links || []);
                    setQuickLinks(
                        (result.data.quick_links_titles || []).map((title: string, index: number) => ({
                            id: index + 1,
                            title: title,
                            url: result.data.quick_links[index] || ''
                        }))
                    );

                    // Important Links
                    const savedImportantLinks = result.data.important_links || [];
                    setImportantLinks(
                        savedImportantLinks.length > 0
                            ? savedImportantLinks.map((link: any, index: number) => ({
                                id: index + 1,
                                title: link.title || '',
                                link: link.link || '',
                                is_enabled: link.is_enabled !== undefined ? link.is_enabled : true
                            }))
                            : [
                                { id: 1, title: 'Home', link: '/', is_enabled: true },
                                { id: 2, title: 'Shop', link: '/shops', is_enabled: true },
                                { id: 3, title: 'All Products', link: '/product', is_enabled: true },
                                { id: 4, title: 'About Us', link: '/pages/about-us', is_enabled: true },
                                { id: 5, title: 'All Brands', link: '/brands', is_enabled: true }
                            ]
                    );

                    // App Links
                    setShowPlayStore(result.data.show_playstore ?? true);
                    setPlayStoreLink(result.data.playstore_link || '');
                    setShowAppStore(result.data.show_appstore ?? true);
                    setAppStoreLink(result.data.appstore_link || '');

                    // Bottom
                    setCopyrightText(result.data.copyright_text || '');
                    setCopyrightAlignment(result.data.copyright_alignment || 'center');
                    setSecuredPayments(result.data.secured_payments || '');
                    setSecuredPaymentsSize(result.data.secured_payments_size || 'medium');

                    // Colors
                    setFooterBgColor(result.data.footer_bg_color || '#1a1a2e');
                    setFooterTextColor(result.data.footer_text_color || '#ffffff');
                    setCopyrightBgColor(result.data.copyright_bg_color || '#22c55e');
                    setCopyrightTextColor(result.data.copyright_text_color || '#ffffff');

                    // Google Maps
                    setShowGoogleMaps(result.data.show_google_maps ?? true);
                    setGoogleMapsEnabled(result.data.google_maps_enabled ?? true);
                    setGoogleMapsType(result.data.google_maps_type || 'embed');
                    setGoogleMapsValue(result.data.google_maps_value || '');
                } else {
                    error('Failed to load footer configuration');
                }
            } catch (err) {
                error('Error loading footer configuration');
            } finally {
                setLoading(false);
            }
        };

        loadConfig();
    }, [apiPrefix]);

    // Polling to detect input value changes from media manager
    useEffect(() => {
        const checkForNewImage = async () => {
            const logoInput = document.querySelector('#footer-logo-input') as HTMLInputElement;
            const securedPaymentsInput = document.querySelector('#footer-secured-payments-input') as HTMLInputElement;

            if (logoInput) {
                const lastLogoValue = (logoInput as any)._lastValue;
                const currentLogoValue = logoInput.value;

                if (currentLogoValue !== lastLogoValue && currentLogoValue) {
                    (logoInput as any)._lastValue = currentLogoValue;

                    if (currentLogoValue.startsWith('/')) {
                        setLogo(currentLogoValue);
                    } else if (!currentLogoValue.startsWith('http')) {
                        try {
                            const response = await fetch(`/admin/api/landing-pages/media-files?ids=${currentLogoValue}`);
                            const result = await response.json();
                            if (result && result.length > 0) {
                                let path = result[0].media_file;
                                if (path && !path.startsWith('/')) {
                                    path = '/' + path;
                                }
                                setLogo(path);
                            }
                        } catch (err) {
                            console.error('Error fetching logo file:', err);
                        }
                    }
                }
            }

            if (securedPaymentsInput) {
                const lastSecuredValue = (securedPaymentsInput as any)._lastValue;
                const currentSecuredValue = securedPaymentsInput.value;

                if (currentSecuredValue !== lastSecuredValue && currentSecuredValue) {
                    (securedPaymentsInput as any)._lastValue = currentSecuredValue;

                    if (currentSecuredValue.startsWith('/')) {
                        setSecuredPayments(currentSecuredValue);
                    } else if (!currentSecuredValue.startsWith('http')) {
                        try {
                            const response = await fetch(`/admin/api/landing-pages/media-files?ids=${currentSecuredValue}`);
                            const result = await response.json();
                            if (result && result.length > 0) {
                                let path = result[0].media_file;
                                if (path && !path.startsWith('/')) {
                                    path = '/' + path;
                                }
                                setSecuredPayments(path);
                            }
                        } catch (err) {
                            console.error('Error fetching secured payments file:', err);
                        }
                    }
                }
            }
        };

        pollingIntervalRef.current = window.setInterval(checkForNewImage, 300);

        return () => {
            if (pollingIntervalRef.current) {
                clearInterval(pollingIntervalRef.current);
            }
        };
    }, []);

    // Toggle Component
    const Toggle = ({ enabled, onChange }: { enabled: boolean; onChange: (value: boolean) => void }) => (
        <button
            type="button"
            onClick={() => onChange(!enabled)}
            className={`relative inline-flex h-6 w-11 items-center rounded-full transition-colors ${
                enabled ? 'bg-blue-600' : 'bg-gray-200'
            }`}
        >
            <span
                className={`inline-block h-4 w-4 transform rounded-full bg-white transition-transform ${
                    enabled ? 'translate-x-6' : 'translate-x-1'
                }`}
            />
        </button>
    );

    // Open Media Manager
    const openMediaManager = useCallback(() => {
        if ((window as any).MicroModal && (window as any).mediaManager) {
            (window as any).MicroModal.show('media-manager-modal', {
                onShow: (modal: any, triggeredBy: any) => {
                    if (modal.id === 'media-manager-modal' && (window as any).mediaManager) {
                        (window as any).mediaManager.init(triggeredBy);
                    }
                }
            });
        }
    }, []);

    const clearLogo = () => {
        setLogo('');
        const logoInput = document.querySelector(
            '#footer-logo-input',
        ) as HTMLInputElement | null;

        if (logoInput) {
            logoInput.value = '';
            (logoInput as any)._lastValue = '';
        }
    };

    const clearSecuredPayments = () => {
        setSecuredPayments('');
        const securedPaymentsInput = document.querySelector(
            '#footer-secured-payments-input',
        ) as HTMLInputElement | null;

        if (securedPaymentsInput) {
            securedPaymentsInput.value = '';
            (securedPaymentsInput as any)._lastValue = '';
        }
    };

    // Handle file upload - kept for fallback but media manager is preferred
    const handleFileUpload = async (file: File, field: 'logo' | 'securedPayments') => {
        const formData = new FormData();
        formData.append('image', file);

        try {
            const response = await fetch(`${apiPrefix}/footer/upload-image`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                if (field === 'logo') {
                    setLogo(result.data.file_path);
                } else {
                    setSecuredPayments(result.data.file_path);
                }
                success('Image uploaded successfully');
            } else {
                error(result.message || 'Failed to upload image');
            }
        } catch (err) {
            error('Error uploading image');
        }
    };

    // Add Quick Link
    const addQuickLink = () => {
        const newId = quickLinks.length > 0 ? Math.max(...quickLinks.map(l => l.id)) + 1 : 1;
        setQuickLinks([...quickLinks, { id: newId, title: '', url: '' }]);
    };

    // Remove Quick Link
    const removeQuickLink = (id: number) => {
        setQuickLinks(quickLinks.filter(link => link.id !== id));
    };

    // Update Quick Link
    const updateQuickLink = (id: number, field: 'title' | 'url', value: string) => {
        setQuickLinks(quickLinks.map(link => link.id === id ? { ...link, [field]: value } : link));
    };

    // Add Important Link
    const addImportantLink = () => {
        const newId = importantLinks.length > 0 ? Math.max(...importantLinks.map(l => l.id)) + 1 : 1;
        setImportantLinks([...importantLinks, { id: newId, title: '', link: '', is_enabled: true }]);
    };

    // Remove Important Link
    const removeImportantLink = (id: number) => {
        setImportantLinks(importantLinks.filter(link => link.id !== id));
    };

    // Update Important Link
    const updateImportantLink = (id: number, field: 'title' | 'link' | 'is_enabled', value: string | boolean) => {
        setImportantLinks(importantLinks.map(link => link.id === id ? { ...link, [field]: value } : link));
    };

    // Update footer-two info row item
    const updateInfoItem = (index: number, field: 'title' | 'description', value: string) => {
        setInfoItems(infoItems.map((item, itemIndex) => (
            itemIndex === index ? { ...item, [field]: value } : item
        )));
    };

    // Save configuration
    const handleSave = async () => {
        setSaving(true);
        try {
            const config = {
                logo,
                logo_size: logoSize,
                description,
                company_info_alignment: companyInfoAlignment,
                customer_support: customerSupport,
                customer_support_secondary: customerSupportSecondary,
                address,
                email,
                show_info_bar: showInfoBar,
                info_items: infoItems,
                info_bg_color: infoBgColor,
                info_icon_color: infoIconColor,
                info_title_color: infoTitleColor,
                info_text_color: infoTextColor,
                show_facebook: showFacebook,
                facebook_link: facebookLink,
                show_twitter: showTwitter,
                twitter_link: twitterLink,
                show_instagram: showInstagram,
                instagram_link: instagramLink,
                show_youtube: showYoutube,
                youtube_link: youtubeLink,
                show_linkedin: showLinkedIn,
                linkedin_link: linkedInLink,
                show_tiktok: showTikTok,
                tiktok_link: tikTokLink,
                show_pinterest: showPinterest,
                pinterest_link: pinterestLink,
                quick_links_titles: quickLinks.map(link => link.title),
                quick_links: quickLinks.map(link => link.url),
                important_links: importantLinks.map(link => ({
                    title: link.title,
                    link: link.link,
                    is_enabled: link.is_enabled
                })),
                show_playstore: showPlayStore,
                playstore_link: playStoreLink,
                show_appstore: showAppStore,
                appstore_link: appStoreLink,
                copyright_text: copyrightText,
                copyright_alignment: copyrightAlignment,
                secured_payments: securedPayments,
                secured_payments_size: securedPaymentsSize,
                footer_bg_color: footerBgColor,
                footer_text_color: footerTextColor,
                copyright_bg_color: copyrightBgColor,
                copyright_text_color: copyrightTextColor,
                show_google_maps: showGoogleMaps,
                google_maps_enabled: googleMapsEnabled,
                google_maps_type: googleMapsType,
                google_maps_value: googleMapsValue
            };

            const response = await fetch(`${apiPrefix}/footer/config`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
                },
                body: JSON.stringify(config)
            });

            const result = await response.json();

            if (result.success) {
                success('Footer configuration saved successfully');
            } else {
                error(result.message || 'Failed to save configuration');
            }
        } catch (err) {
            error('Error saving footer configuration');
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center p-12">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    return (
        <>
            <div className="">
                {/* Main Card */}
                <div className="bg-white rounded-lg shadow-sm border border-gray-100">
                    {/* Header */}
                    <div className="px-6 py-4 border-b border-gray-100">
                        <h2 className="text-base font-medium text-[#1a1a2e]">Footer Configuration</h2>
                    </div>

                    <div className="p-6 space-y-8">
                        {/* Company Info Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">Company Information</h3>
                        </div>

                        <div className="space-y-6">
                            {/* Logo */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Logo
                                </label>
                                <div className="flex items-center gap-4">
                                    {logo && (
                                        <div className="relative inline-flex rounded-md border border-gray-200 bg-white p-2">
                                            <img src={logo.startsWith('http') || logo.startsWith('/') ? logo : `/${logo}`} alt="Logo" className="h-12 w-auto" />
                                            <button
                                                type="button"
                                                onClick={clearLogo}
                                                className="absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full border border-red-200 bg-white text-red-500 shadow-sm transition-colors hover:bg-red-50"
                                                aria-label="Clear footer logo"
                                                title="Clear logo"
                                            >
                                                <FaXmark className="h-3 w-3" />
                                            </button>
                                        </div>
                                    )}
                                    <div
                                        className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                                        tabIndex={0}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            openMediaManager();
                                        }}
                                    >
                                        <div className="px-6 py-3 bg-[#4f5bd5] text-white rounded-md rounded-e-none">BROWSE</div>
                                        <span className="media-count py-2 flex items-center text-muted">{logo ? '1 file' : 'No Selected Item'}</span>
                                        <input
                                            type="hidden"
                                            id="footer-logo-input"
                                            name="footer_logo"
                                            className="hidden"
                                            accept="image/*"
                                            value=""
                                            readOnly
                                        />
                                    </div>
                                    <select
                                        value={logoSize}
                                        onChange={(e) => setLogoSize(e.target.value as 'small' | 'medium' | 'large')}
                                        className="px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300 bg-white ml-4"
                                    >
                                        <option value="small">Small (48px)</option>
                                        <option value="medium">Medium (72px)</option>
                                        <option value="large">Large (96px)</option>
                                    </select>
                                </div>
                            </div>
                            <p className="text-xs text-gray-500 ml-56">
                                Recommended: 200x48 pixels
                            </p>
                        </div>

                            {/* Description */}
                            <div className="flex items-start">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide pt-2">
                                    Description
                                </label>
                                <textarea
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    rows={4}
                                    className="flex-1 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                    placeholder="Enter footer description"
                                />
                            </div>

                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Company Text Style
                                </label>
                                <select
                                    value={companyInfoAlignment}
                                    onChange={(e) => setCompanyInfoAlignment(e.target.value as CompanyInfoAlignment)}
                                    className="w-56 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300 bg-white"
                                >
                                    <option value="left">Left</option>
                                    <option value="center">Center</option>
                                    <option value="right">Right</option>
                                    <option value="justify">Full Width</option>
                                </select>
                            </div>

                            {/* Customer Support */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Customer Support
                                </label>
                                <input
                                    type="text"
                                    value={customerSupport}
                                    onChange={(e) => setCustomerSupport(e.target.value)}
                                    className="w-56 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                    placeholder="+88 01234 567890"
                                />
                            </div>

                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Customer Support 2
                                </label>
                                <input
                                    type="text"
                                    value={customerSupportSecondary}
                                    onChange={(e) => setCustomerSupportSecondary(e.target.value)}
                                    className="w-56 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                    placeholder="+88 01234 567891"
                                />
                            </div>

                            {/* Address */}
                            <div className="flex items-start">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide pt-2">
                                    Address
                                </label>
                                <textarea
                                    value={address}
                                    onChange={(e) => setAddress(e.target.value)}
                                    rows={3}
                                    className="flex-1 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                    placeholder="Enter address"
                                />
                            </div>

                            {/* Email */}
                            <div className="flex items-start">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide pt-2">
                                    Email
                                </label>
                                <div className="flex-1">
                                    <input
                                        type="text"
                                        value={email}
                                        onChange={(e) => setEmail(e.target.value)}
                                        className="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="info@example.com, support@example.com"
                                    />
                                    <p className="text-xs text-gray-500 mt-1">
                                        Separate multiple emails with commas
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Footer Two Info Row Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">Footer Two Feature Row</h3>
                        </div>

                        <div className="space-y-6 p-4">
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show Feature Row
                                </label>
                                <Toggle enabled={showInfoBar} onChange={setShowInfoBar} />
                            </div>

                            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                {[
                                    { label: 'Row Background', value: infoBgColor, setter: setInfoBgColor },
                                    { label: 'Icon Color', value: infoIconColor, setter: setInfoIconColor },
                                    { label: 'Title Color', value: infoTitleColor, setter: setInfoTitleColor },
                                    { label: 'Text Color', value: infoTextColor, setter: setInfoTextColor }
                                ].map((field) => (
                                    <div key={field.label} className="flex items-center">
                                        <label className="w-40 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                            {field.label}
                                        </label>
                                        <div className="flex items-center gap-4">
                                            <input
                                                type="color"
                                                value={field.value}
                                                onChange={(e) => field.setter(e.target.value)}
                                                className="h-10 w-20 rounded border border-gray-200 cursor-pointer"
                                            />
                                            <input
                                                type="text"
                                                value={field.value}
                                                onChange={(e) => field.setter(e.target.value)}
                                                className="w-40 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                            />
                                        </div>
                                    </div>
                                ))}
                            </div>

                            <div className="grid grid-cols-1 xl:grid-cols-2 gap-4">
                                {infoItems.map((item, index) => (
                                    <div key={index} className="rounded-lg border border-gray-200 p-4 space-y-3">
                                        <p className="text-xs font-semibold text-[#1a1a2e] uppercase tracking-wide">
                                            Feature {index + 1}
                                        </p>
                                        <input
                                            type="text"
                                            value={item.title}
                                            onChange={(e) => updateInfoItem(index, 'title', e.target.value)}
                                            className="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                            placeholder="Feature title"
                                        />
                                        <textarea
                                            value={item.description}
                                            onChange={(e) => updateInfoItem(index, 'description', e.target.value)}
                                            rows={4}
                                            className="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                            placeholder="Feature description"
                                        />
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Social Links Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">Social Media Links</h3>
                        </div>

                        <div className="space-y-6 p-4">
                            {/* Facebook */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show Facebook
                                </label>
                                <Toggle enabled={showFacebook} onChange={setShowFacebook} />
                                {showFacebook && (
                                    <input
                                        type="url"
                                        value={facebookLink}
                                        onChange={(e) => setFacebookLink(e.target.value)}
                                        className="w-80 ml-4 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="Facebook URL"
                                    />
                                )}
                            </div>

                            {/* Twitter */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show Twitter
                                </label>
                                <Toggle enabled={showTwitter} onChange={setShowTwitter} />
                                {showTwitter && (
                                    <input
                                        type="url"
                                        value={twitterLink}
                                        onChange={(e) => setTwitterLink(e.target.value)}
                                        className="w-80 ml-4 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="Twitter URL"
                                    />
                                )}
                            </div>

                            {/* Instagram */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show Instagram
                                </label>
                                <Toggle enabled={showInstagram} onChange={setShowInstagram} />
                                {showInstagram && (
                                    <input
                                        type="url"
                                        value={instagramLink}
                                        onChange={(e) => setInstagramLink(e.target.value)}
                                        className="w-80 ml-4 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="Instagram URL"
                                    />
                                )}
                            </div>

                            {/* YouTube */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show YouTube
                                </label>
                                <Toggle enabled={showYoutube} onChange={setShowYoutube} />
                                {showYoutube && (
                                    <input
                                        type="url"
                                        value={youtubeLink}
                                        onChange={(e) => setYoutubeLink(e.target.value)}
                                        className="w-80 ml-4 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="YouTube URL"
                                    />
                                )}
                            </div>

                            {/* LinkedIn */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show LinkedIn
                                </label>
                                <Toggle enabled={showLinkedIn} onChange={setShowLinkedIn} />
                                {showLinkedIn && (
                                    <input
                                        type="url"
                                        value={linkedInLink}
                                        onChange={(e) => setLinkedInLink(e.target.value)}
                                        className="w-80 ml-4 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="LinkedIn URL"
                                    />
                                )}
                            </div>

                            {/* TikTok */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show TikTok
                                </label>
                                <Toggle enabled={showTikTok} onChange={setShowTikTok} />
                                {showTikTok && (
                                    <input
                                        type="url"
                                        value={tikTokLink}
                                        onChange={(e) => setTikTokLink(e.target.value)}
                                        className="w-80 ml-4 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="TikTok URL"
                                    />
                                )}
                            </div>

                            {/* Pinterest */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show Pinterest
                                </label>
                                <Toggle enabled={showPinterest} onChange={setShowPinterest} />
                                {showPinterest && (
                                    <input
                                        type="url"
                                        value={pinterestLink}
                                        onChange={(e) => setPinterestLink(e.target.value)}
                                        className="w-80 ml-4 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="Pinterest URL"
                                    />
                                )}
                            </div>
                        </div>

                        {/* Important Links Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">Important Links</h3>
                        </div>

                        <div className="space-y-4 p-4">
                            {importantLinks.map((link) => (
                                <div key={link.id} className="flex items-center gap-4">
                                    <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                        Link {link.id}
                                    </label>
                                    <input
                                        type="text"
                                        value={link.title}
                                        onChange={(e) => updateImportantLink(link.id, 'title', e.target.value)}
                                        className="w-48 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="Title"
                                    />
                                    <input
                                        type="url"
                                        value={link.link}
                                        onChange={(e) => updateImportantLink(link.id, 'link', e.target.value)}
                                        className="flex-1 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="URL"
                                    />
                                    <Toggle enabled={link.is_enabled} onChange={(value) => updateImportantLink(link.id, 'is_enabled', value)} />
                                    <button
                                        onClick={() => removeImportantLink(link.id)}
                                        className="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600"
                                    >
                                        Remove
                                    </button>
                                </div>
                            ))}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                </label>
                                <button
                                    onClick={addImportantLink}
                                    className="px-6 py-2.5 bg-[#4f5bd5] text-white text-sm font-medium rounded-md hover:bg-[#4450c0]"
                                >
                                    Add Link
                                </button>
                            </div>
                        </div>

                        {/* Quick Links Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">Quick Links</h3>
                        </div>

                        <div className="space-y-4 p-4">
                            {quickLinks.map((link) => (
                                <div key={link.id} className="flex items-center gap-4">
                                    <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                        Link {link.id}
                                    </label>
                                    <input
                                        type="text"
                                        value={link.title}
                                        onChange={(e) => updateQuickLink(link.id, 'title', e.target.value)}
                                        className="w-64 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="Title"
                                    />
                                    <input
                                        type="url"
                                        value={link.url}
                                        onChange={(e) => updateQuickLink(link.id, 'url', e.target.value)}
                                        className="flex-1 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="URL"
                                    />
                                    <button
                                        onClick={() => removeQuickLink(link.id)}
                                        className="px-4 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600"
                                    >
                                        Remove
                                    </button>
                                </div>
                            ))}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                </label>
                                <button
                                    onClick={addQuickLink}
                                    className="px-6 py-2.5 bg-[#4f5bd5] text-white text-sm font-medium rounded-md hover:bg-[#4450c0]"
                                >
                                    Add Link
                                </button>
                            </div>
                        </div>

                        {/* App Links Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">App Store Links</h3>
                        </div>

                        <div className="space-y-6 p-4">
                            {/* Play Store */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show Play Store
                                </label>
                                <Toggle enabled={showPlayStore} onChange={setShowPlayStore} />
                                {showPlayStore && (
                                    <input
                                        type="url"
                                        value={playStoreLink}
                                        onChange={(e) => setPlayStoreLink(e.target.value)}
                                        className="w-80 ml-4 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="Play Store URL"
                                    />
                                )}
                            </div>

                            {/* App Store */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Show App Store
                                </label>
                                <Toggle enabled={showAppStore} onChange={setShowAppStore} />
                                {showAppStore && (
                                    <input
                                        type="url"
                                        value={appStoreLink}
                                        onChange={(e) => setAppStoreLink(e.target.value)}
                                        className="w-80 ml-4 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="App Store URL"
                                    />
                                )}
                            </div>
                        </div>

                        {/* Secured Payments Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">Secured Payments</h3>
                        </div>

                        <div className="space-y-6 p-4">
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Secured Payments Image
                                </label>
                                <div className="flex items-center gap-4">
                                    {securedPayments && (
                                        <img src={securedPayments.startsWith('http') || securedPayments.startsWith('/') ? securedPayments : `/${securedPayments}`} alt="Secured Payments" className="h-12 w-auto" />
                                    )}
                                    <div
                                        className="theme-input p-[3px] py-0 flex gap-3 cursor-pointer !ps-0"
                                        tabIndex={0}
                                        onClick={(e) => {
                                            e.preventDefault();
                                            openMediaManager();
                                        }}
                                    >
                                        <div className="px-6 py-3 bg-[#4f5bd5] text-white rounded-md rounded-e-none">BROWSE</div>
                                        <span className="media-count py-2 flex items-center text-muted">{securedPayments ? '1 file' : 'No Selected Item'}</span>
                                        <input
                                            type="hidden"
                                            id="footer-secured-payments-input"
                                            name="footer_secured_payments"
                                            className="hidden"
                                            accept="image/*"
                                            value=""
                                            readOnly
                                        />
                                    </div>
                                    <select
                                        value={securedPaymentsSize}
                                        onChange={(e) => setSecuredPaymentsSize(e.target.value as 'small' | 'medium' | 'large')}
                                        className="px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300 bg-white"
                                        aria-label="Secured payments image size"
                                    >
                                        <option value="small">Small (20px)</option>
                                        <option value="medium">Mid (32px)</option>
                                        <option value="large">Large (40px)</option>
                                    </select>
                                    {securedPayments && (
                                        <button
                                            type="button"
                                            onClick={clearSecuredPayments}
                                            className="h-10 w-10 rounded-md border border-red-200 text-red-500 hover:bg-red-50 transition-colors"
                                            aria-label="Clear secured payments image"
                                            title="Clear image"
                                        >
                                            <FaXmark className="mx-auto h-3 w-3" />
                                        </button>
                                    )}
                                </div>
                            </div>
                            <p className="text-xs text-gray-500 ml-56">
                                Recommended: 400x40 pixels
                            </p>
                        </div>

                        {/* Bottom Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">Bottom Section</h3>
                        </div>

                        <div className="space-y-6 p-4">
                            {/* Copyright Text */}
                            <div className="flex items-start">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide pt-2">
                                    Copyright Text
                                </label>
                                <textarea
                                    value={copyrightText}
                                    onChange={(e) => setCopyrightText(e.target.value)}
                                    rows={2}
                                    className="flex-1 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                    placeholder="© 2024 Your Company. All rights reserved."
                                />
                            </div>

                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Copyright Text Style
                                </label>
                                <select
                                    value={copyrightAlignment}
                                    onChange={(e) => setCopyrightAlignment(e.target.value as CopyrightAlignment)}
                                    className="w-56 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300 bg-white"
                                >
                                    <option value="left">Left</option>
                                    <option value="center">Center</option>
                                    <option value="right">Right</option>
                                </select>
                            </div>
                        </div>

                        {/* Colors Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">Colors</h3>
                        </div>

                        <div className="space-y-6 p-4">
                            {/* Footer Background Color */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Footer Background Color
                                </label>
                                <div className="flex items-center gap-4">
                                    <input
                                        type="color"
                                        value={footerBgColor}
                                        onChange={(e) => setFooterBgColor(e.target.value)}
                                        className="h-10 w-20 rounded border border-gray-200 cursor-pointer"
                                    />
                                    <input
                                        type="text"
                                        value={footerBgColor}
                                        onChange={(e) => setFooterBgColor(e.target.value)}
                                        className="w-40 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="#1a1a2e"
                                    />
                                </div>
                            </div>

                            {/* Footer Text Color */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Footer Text Color
                                </label>
                                <div className="flex items-center gap-4">
                                    <input
                                        type="color"
                                        value={footerTextColor}
                                        onChange={(e) => setFooterTextColor(e.target.value)}
                                        className="h-10 w-20 rounded border border-gray-200 cursor-pointer"
                                    />
                                    <input
                                        type="text"
                                        value={footerTextColor}
                                        onChange={(e) => setFooterTextColor(e.target.value)}
                                        className="w-40 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="#ffffff"
                                    />
                                </div>
                            </div>

                            {/* Copyright Background Color */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Copyright Background Color
                                </label>
                                <div className="flex items-center gap-4">
                                    <input
                                        type="color"
                                        value={copyrightBgColor}
                                        onChange={(e) => setCopyrightBgColor(e.target.value)}
                                        className="h-10 w-20 rounded border border-gray-200 cursor-pointer"
                                    />
                                    <input
                                        type="text"
                                        value={copyrightBgColor}
                                        onChange={(e) => setCopyrightBgColor(e.target.value)}
                                        className="w-40 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="#22c55e"
                                    />
                                </div>
                            </div>

                            {/* Copyright Text Color */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Copyright Text Color
                                </label>
                                <div className="flex items-center gap-4">
                                    <input
                                        type="color"
                                        value={copyrightTextColor}
                                        onChange={(e) => setCopyrightTextColor(e.target.value)}
                                        className="h-10 w-20 rounded border border-gray-200 cursor-pointer"
                                    />
                                    <input
                                        type="text"
                                        value={copyrightTextColor}
                                        onChange={(e) => setCopyrightTextColor(e.target.value)}
                                        className="w-40 px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                        placeholder="#ffffff"
                                        />
                                    </div>
                                </div>
                            </div>

                        {/* Google Maps Section */}
                        <div className="bg-[#f8f9fa] rounded-lg p-4">
                            <h3 className="text-sm font-semibold text-[#1a1a2e]">Google Maps</h3>
                        </div>

                        <div className="space-y-6 p-4">
                            {/* Show Google Maps Toggle */}
                            <div className="flex items-center">
                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                    Enable Google Maps
                                </label>
                                <Toggle enabled={showGoogleMaps} onChange={setShowGoogleMaps} />
                            </div>

                            {showGoogleMaps && (
                                <>
                                    {/* Google Maps Enabled Toggle */}
                                    <div className="flex items-center">
                                        <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide">
                                            Display Maps
                                        </label>
                                        <Toggle enabled={googleMapsEnabled} onChange={setGoogleMapsEnabled} />
                                    </div>

                                    {googleMapsEnabled && (
                                        <>
                                            {/* Display Type Selection */}
                                            <div className="flex items-start">
                                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide pt-2">
                                                    Display Mode
                                                </label>
                                                <div className="flex-1 space-y-3">
                                                    <div className="flex items-center gap-3">
                                                        <input
                                                            type="radio"
                                                            id="embed"
                                                            name="googleMapsType"
                                                            value="embed"
                                                            checked={googleMapsType === 'embed'}
                                                            onChange={(e) => setGoogleMapsType(e.target.value as 'embed' | 'link')}
                                                            className="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                                        />
                                                        <label htmlFor="embed" className="text-sm text-gray-600">
                                                            Embed Map (iframe)
                                                        </label>
                                                    </div>
                                                    <div className="flex items-center gap-3">
                                                        <input
                                                            type="radio"
                                                            id="link"
                                                            name="googleMapsType"
                                                            value="link"
                                                            checked={googleMapsType === 'link'}
                                                            onChange={(e) => setGoogleMapsType(e.target.value as 'embed' | 'link')}
                                                            className="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                                        />
                                                        <label htmlFor="link" className="text-sm text-gray-600">
                                                            Share Link (button)
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Maps Value Input */}
                                            <div className="flex items-start">
                                                <label className="w-56 text-xs font-medium text-[#6b7280] uppercase tracking-wide pt-2">
                                                    {googleMapsType === 'embed' ? 'Embed Code' : 'Map URL'}
                                                </label>
                                                <div className="flex-1">
                                                    {googleMapsType === 'embed' ? (
                                                        <textarea
                                                            value={googleMapsValue}
                                                            onChange={(e) => setGoogleMapsValue(e.target.value)}
                                                            rows={4}
                                                            className="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                                            placeholder="Paste Google Maps embed code here"
                                                        />
                                                    ) : (
                                                        <input
                                                            type="url"
                                                            value={googleMapsValue}
                                                            onChange={(e) => setGoogleMapsValue(e.target.value)}
                                                            className="w-full px-4 py-2.5 border border-gray-200 rounded-md text-sm text-gray-600 focus:outline-none focus:border-gray-300"
                                                            placeholder="https://share.google/..."
                                                        />
                                                    )}
                                                    <p className="text-xs text-gray-500 mt-2">
                                                        💡 Tip: Go to Google Maps → Share → Embed a map to get the embed code, or Copy link to get the share URL
                                                    </p>
                                                </div>
                                            </div>
                                        </>
                                    )}
                                </>
                            )}
                        </div>
                    </div>

                    {/* Save Button */}
                    <div className="px-6 py-4 border-t border-gray-100 flex justify-end">
                        <button
                            onClick={handleSave}
                            disabled={saving}
                            className="px-8 py-3 bg-[#22c55e] text-white text-sm font-semibold rounded-lg hover:bg-[#16a34a] disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2"
                        >
                            {saving ? (
                                <>
                                    <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-white"></div>
                                    Saving...
                                </>
                            ) : (
                                <>
                                    <i className="fa fa-save"></i>
                                    Save Changes
                                </>
                            )}
                        </button>
                    </div>
                </div>

                <ToastContainer toasts={toasts} onRemove={removeToast} />
        </>
    );
};

export default FooterConfigurationApp;
