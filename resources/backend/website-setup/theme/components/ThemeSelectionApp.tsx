import React, { useState, useEffect } from 'react';
import ToastContainer from './Toast';
import useToast from '../hooks/useToast';

interface Theme {
    id: string;
    name: string;
    description: string;
    image: string;
}

interface Header {
    id: string;
    name: string;
    description: string;
    image: string;
}

interface ProductCard {
    id: string;
    name: string;
    description: string;
    image: string;
}

interface Footer {
    id: string;
    name: string;
    description: string;
    image: string;
}

interface ThemeSelectionPayload {
    current_selection: {
        theme: string;
        header: string;
        product_card: string;
        category_card: string;
        footer: string;
    };
    available_themes: Theme[];
    available_headers: Header[];
    available_product_cards: ProductCard[];
    available_footers: Footer[];
}

interface ThemeSelectionResponse {
    success: boolean;
    data: ThemeSelectionPayload;
    message?: string;
}

const ThemeSelectionApp = () => {
    const [selectedTheme, setSelectedTheme] = useState('theme-one');
    const [themes, setThemes] = useState<Theme[]>([]);
    const [selectedHeader, setSelectedHeader] = useState('header-one');
    const [headers, setHeaders] = useState<Header[]>([]);
    const [selectedProductCard, setSelectedProductCard] = useState('product-card-one');
    const [productCards, setProductCards] = useState<ProductCard[]>([]);
    const [selectedFooter, setSelectedFooter] = useState('footer-one');
    const [footers, setFooters] = useState<Footer[]>([]);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [previewTheme, setPreviewTheme] = useState<Theme | null>(null);
    const [previewHeader, setPreviewHeader] = useState<Header | null>(null);
    const [previewProductCard, setPreviewProductCard] = useState<ProductCard | null>(null);
    const [previewFooter, setPreviewFooter] = useState<Footer | null>(null);
    const { toasts, success, error, removeToast } = useToast();

    // Load theme data from API
    useEffect(() => {
        const loadThemeData = async () => {
            try {
                const response = await fetch('/admin/api/theme-selection');
                const data: ThemeSelectionResponse = await response.json();

                if (data.success) {
                    setThemes(data.data.available_themes);
                    setHeaders(data.data.available_headers || []);
                    setProductCards(data.data.available_product_cards || []);
                    setFooters(data.data.available_footers || []);
                    setSelectedTheme(data.data.current_selection.theme);
                    setSelectedHeader(data.data.current_selection.header);
                    setSelectedProductCard(data.data.current_selection.product_card);
                    setSelectedFooter(data.data.current_selection.footer || 'footer-one');
                } else {
                    error('Failed to load theme data');
                }
            } catch (err) {
                error('Error loading theme data');
                // Fallback to default themes if API fails
                setThemes([
                    {
                        id: 'theme-one',
                        name: 'Carrot - Grocery Theme',
                        description: 'Clean and minimalist design with focus on content',
                        image: '/images/backend/theme/theme_1.jpg'
                    },
                    {
                        id: 'theme-two',
                        name: 'Lumen - Home Decor',
                        description: 'Traditional business layout with sophisticated styling',
                        image: '/images/backend/theme/theme_2.jpg'
                    },
                    {
                        id: 'theme-three',
                        name: 'Stripe - Lifestyle Theme',
                        description: 'Vibrant colors and dynamic layouts for creative businesses',
                        image: '/images/backend/theme/theme_3.jpg'
                    }
                ]);
                // Fallback headers
                setHeaders([
                    {
                        id: 'header-one',
                        name: 'Header 1',
                        description: 'Clean and simple header design',
                        image: '/images/backend/header_1.jpg'
                    },
                    {
                        id: 'header-two',
                        name: 'Header 2',
                        description: 'Traditional header with navigation',
                        image: '/images/backend/header_2.jpg'
                    },
                    {
                        id: 'header-three',
                        name: 'Header 3',
                        description: 'Contemporary header with advanced features',
                        image: '/images/backend/header_3.jpg'
                    }
                ]);
                // Fallback product cards
                setProductCards([
                    {
                        id: 'product-card-one',
                        name: 'Product Card 1',
                        description: 'Grid layout product card design',
                        image: '/images/backend/product_card_1.png'
                    },
                    {
                        id: 'product-card-two',
                        name: 'Product Card 2',
                        description: 'List view product card design',
                        image: '/images/backend/product_card_2.png'
                    },
                    {
                        id: 'product-card-three',
                        name: 'Product Card 3',
                        description: 'Minimalist product card design',
                        image: '/images/backend/product_card_3.png'
                    }
                ]);
                setFooters([
                    {
                        id: 'footer-one',
                        name: 'Default Footer',
                        description: 'Classic multi-column ecommerce footer',
                        image: '/images/backend/footer.png'
                    },
                    {
                        id: 'footer-two',
                        name: 'Policy + Contact Footer',
                        description: 'Policy feature row with two-column brand and contact footer',
                        image: '/images/backend/footer_two.svg'
                    }
                ]);
            } finally {
                setLoading(false);
            }
        };

        loadThemeData();
    }, []);

    const handleThemeSelect = async (themeId: string) => {
        setSaving(true);
        setSelectedTheme(themeId);

        try {
            const response = await fetch('/admin/api/theme-selection', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
                },
                body: JSON.stringify({
                    theme: themeId,
                })
            });

            const data = await response.json();

            if (data.success) {
                success('Theme saved successfully!');
            } else {
                error(data.message || 'Failed to save theme');
            }
        } catch (err) {
            error('Error saving theme');
        } finally {
            setSaving(false);
        }
    };

    const handlePreview = (theme: Theme) => {
        // Preview theme in modal
        setPreviewTheme(theme);
    };

    const handleClosePreview = () => {
        setPreviewTheme(null);
    };

    const handleHeaderSelect = async (headerId: string) => {
        setSaving(true);
        setSelectedHeader(headerId);

        try {
            const response = await fetch('/admin/api/theme-selection', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
                },
                body: JSON.stringify({
                    theme: selectedTheme,
                    header: headerId,
                })
            });

            const data = await response.json();

            if (data.success) {
                success('Header saved successfully!');
            } else {
                error(data.message || 'Failed to save header');
            }
        } catch (err) {
            error('Error saving header');
        } finally {
            setSaving(false);
        }
    };

    const handleHeaderPreview = (header: Header) => {
        // Preview header in modal
        setPreviewHeader(header);
    };

    const handleCloseHeaderPreview = () => {
        setPreviewHeader(null);
    };

    const handleProductCardSelect = async (productCardId: string) => {
        setSaving(true);
        setSelectedProductCard(productCardId);

        try {
            const response = await fetch('/admin/api/theme-selection', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
                },
                body: JSON.stringify({
                    theme: selectedTheme,
                    header: selectedHeader,
                    product_card: productCardId,
                })
            });

            const data = await response.json();

            if (data.success) {
                success('Product card saved successfully!');
            } else {
                error(data.message || 'Failed to save product card');
            }
        } catch (err) {
            error('Error saving product card');
        } finally {
            setSaving(false);
        }
    };

    const handleProductCardPreview = (productCard: ProductCard) => {
        // Preview product card in modal
        setPreviewProductCard(productCard);
    };

    const handleCloseProductCardPreview = () => {
        setPreviewProductCard(null);
    };

    const handleFooterSelect = async (footerId: string) => {
        setSaving(true);
        setSelectedFooter(footerId);

        try {
            const response = await fetch('/admin/api/theme-selection', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''
                },
                body: JSON.stringify({
                    theme: selectedTheme,
                    header: selectedHeader,
                    product_card: selectedProductCard,
                    footer: footerId,
                })
            });

            const data = await response.json();

            if (data.success) {
                success('Footer saved successfully!');
            } else {
                error(data.message || 'Failed to save footer');
            }
        } catch (err) {
            error('Error saving footer');
        } finally {
            setSaving(false);
        }
    };

    const handleFooterPreview = (footer: Footer) => {
        setPreviewFooter(footer);
    };

    const handleCloseFooterPreview = () => {
        setPreviewFooter(null);
    };

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-64">
                <div className="text-center">
                    <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                    <p className="text-gray-600">Loading theme options...</p>
                </div>
            </div>
        );
    }

    return (
        <>
            <ToastContainer toasts={toasts} onRemove={removeToast} />
            <div className="py-6 max-w-8xl mx-auto">

            <div className="grid grid-cols-6 gap-6">
                {/* Left Column - Default Theme Text (1 column) */}
                <div className="col-span-6 lg:col-span-1 flex items-start justify-center">
                    <div className="text-center lg:text-left lg:mt-0">
                        <h3 className="text-lg font-semibold text-slate-600">Default Theme</h3>
                    </div>
                </div>

                {/* Right Column - Theme Images (5 columns) */}
                <div className="col-span-6 lg:col-span-5">
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {themes.map((theme) => (
                        <div
                            key={theme.id}
                            className={`relative border-4 rounded-lg overflow-hidden cursor-pointer transition-all duration-300 ${
                                selectedTheme === theme.id
                                    ? 'border-blue-500 shadow-lg ring-4 ring-blue-500 ring-opacity-20'
                                    : 'border-gray-300 hover:border-blue-400 hover:shadow-lg'
                            }`}
                            onClick={() => handleThemeSelect(theme.id)}
                        >
                            <div className="aspect-video">
                                <img
                                    src={theme.image}
                                    alt={theme.name}
                                    className="w-full h-full object-contain"
                                    onError={(e) => {
                                        const target = e.target as HTMLImageElement;
                                        target.src = 'https://picsum.photos/400/300';
                                    }}
                                />
                            </div>

                            {/* Theme Name */}
                            <div className="p-3 bg-white">
                                <h4 className="text-sm font-semibold text-center text-gray-800">
                                    {theme.name}
                                </h4>
                            </div>

                            {/* Selection Indicator */}
                            {selectedTheme === theme.id && (
                                <div className="absolute top-2 left-2 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center">
                                    <i className="fa fa-check text-sm"></i>
                                </div>
                            )}

                            {/* Preview Button */}
                            <div className="absolute top-2 right-2 opacity-0 hover:opacity-100 transition-opacity duration-300">
                                <button
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        handlePreview(theme);
                                    }}
                                    className="bg-blue-500 text-white px-3 py-2 rounded-md hover:bg-blue-600 transition-colors duration-300 flex items-center gap-2"
                                >
                                    <i className="fa fa-eye"></i>
                                    Preview
                                </button>
                            </div>

                            
                            {/* Saving Indicator */}
                            {saving && selectedTheme === theme.id && (
                                <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <div className="bg-white rounded-lg p-4 flex items-center gap-3">
                                        <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                                        <span className="text-gray-700 font-medium">Saving...</span>
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                    </div>
                </div>
            </div>
        </div>

        {/* Header Selection Section */}
        <div className="grid grid-cols-6 gap-6 mt-12">
            {/* Left Column - Default Header Text (1 column) */}
            <div className="col-span-6 lg:col-span-1 flex items-start justify-center">
                <div className="text-center lg:text-left lg:mt-0">
                    <h3 className="text-lg font-semibold text-slate-600">Default Header</h3>
                </div>
            </div>

            {/* Right Column - Header Images (5 columns) */}
            <div className="col-span-6 lg:col-span-5">
                <div className="space-y-6">
                    {headers.map((header) => (
                        <div key={header.id}>
                            {/* Header Name - Above Image */}
                            <div className="mb-2">
                                <h4 className="text-lg font-semibold text-gray-800">
                                    {header.name}
                                </h4>
                            </div>

                            {/* Header Image Container */}
                            <div className="flex gap-3 items-start">
                                {/* Header Image */}
                                <div
                                    className={`relative border-4 rounded-lg overflow-hidden cursor-pointer transition-all duration-300 flex-1 ${
                                        selectedHeader === header.id
                                            ? 'border-blue-500 shadow-lg ring-4 ring-blue-500 ring-opacity-20'
                                            : 'border-gray-300 hover:border-blue-400 hover:shadow-lg'
                                    }`}
                                    onClick={() => handleHeaderSelect(header.id)}
                                >
                                    <img
                                        src={header.image}
                                        alt={header.name}
                                        className="w-full object-contain"
                                        onError={(e) => {
                                            const target = e.target as HTMLImageElement;
                                            target.src = 'https://picsum.photos/400/150';
                                        }}
                                    />

                                    {/* Selection Indicator */}
                                    {selectedHeader === header.id && (
                                        <div className="absolute top-2 right-2 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center">
                                            <i className="fa fa-check text-sm"></i>
                                        </div>
                                    )}

                                    {/* Preview Button */}
                                    <div className="absolute top-2 left-2 opacity-0 hover:opacity-100 transition-opacity duration-300">
                                        <button
                                            onClick={(e) => {
                                                e.stopPropagation();
                                                handleHeaderPreview(header);
                                            }}
                                            className="bg-blue-500 text-white px-3 py-2 rounded-md hover:bg-blue-600 transition-colors duration-300 flex items-center gap-2"
                                        >
                                            <i className="fa fa-eye"></i>
                                            Preview
                                        </button>
                                    </div>

                                    {/* Saving Indicator */}
                                    {saving && selectedHeader === header.id && (
                                        <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                            <div className="bg-white rounded-lg p-4 flex items-center gap-3">
                                                <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                                                <span className="text-gray-700 font-medium">Saving...</span>
                                            </div>
                                        </div>
                                    )}
                                </div>

                                {/* Edit Button (Always Visible - Top Aligned - Right Side) */}
                                <button
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        window.location.href = `/admin/theme/header/${header.id}/configure`;
                                    }}
                                    className="bg-green-500 text-white px-3 py-2 rounded-md hover:bg-green-600 transition-colors duration-300 flex items-center gap-2 h-fit"
                                >
                                    <i className="fa fa-edit"></i>
                                    {header.id === 'header-one' ? 'Edit Header 1' : header.id === 'header-two' ? 'Edit Header 2' : 'Edit Header 3'}
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>

        {/* Configure Menu Button - After Header 3 */}
        <div className="mt-12 flex justify-end">
            <button
                onClick={() => window.location.href = '/admin/theme/menu-builder'}
                className="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg flex items-center gap-2 font-semibold shadow-lg transition-colors duration-300"
            >
                <i className="fa fa-list"></i>
                <span>Configure Main Menu</span>
            </button>
        </div>

        {/* Product Card Selection Section */}
        <div className="grid grid-cols-6 gap-6 mt-16">
            {/* Left Column - Default Product Card Text (1 column) */}
            <div className="col-span-6 lg:col-span-1 flex items-start justify-center">
                <div className="text-center lg:text-left lg:mt-0">
                    <h3 className="text-lg font-semibold text-slate-600">Default Product Card</h3>
                </div>
            </div>

            {/* Right Column - Product Card Images (5 columns) */}
            <div className="col-span-6 lg:col-span-5">
                <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {productCards.map((productCard) => (
                        <div
                            key={productCard.id}
                            className={`relative border-4 rounded-lg overflow-hidden cursor-pointer transition-all duration-300 max-w-xs mx-auto w-full ${
                                selectedProductCard === productCard.id
                                    ? 'border-blue-500 shadow-lg ring-4 ring-blue-500 ring-opacity-20'
                                    : 'border-gray-300 hover:border-blue-400 hover:shadow-lg'
                            }`}
                            onClick={() => handleProductCardSelect(productCard.id)}
                        >
                            <div className="p-2">
                                <img
                                    src={productCard.image}
                                    alt={productCard.name}
                                    className="h-auto max-h-64 w-auto object-contain mx-auto"
                                    onError={(e) => {
                                        const target = e.target as HTMLImageElement;
                                        target.src = 'https://picsum.photos/400/250';
                                    }}
                                />
                            </div>

                            {/* Product Card Name */}
                            <div className="p-3 bg-white">
                                <h4 className="text-sm font-semibold text-center text-gray-800">
                                    {productCard.name}
                                </h4>
                            </div>

                            {/* Selection Indicator */}
                            {selectedProductCard === productCard.id && (
                                <div className="absolute top-2 left-2 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center">
                                    <i className="fa fa-check text-sm"></i>
                                </div>
                            )}

                            {/* Preview Button */}
                            <div className="absolute top-2 right-2 opacity-0 hover:opacity-100 transition-opacity duration-300">
                                <button
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        handleProductCardPreview(productCard);
                                    }}
                                    className="bg-blue-500 text-white px-3 py-2 rounded-md hover:bg-blue-600 transition-colors duration-300 flex items-center gap-2"
                                >
                                    <i className="fa fa-eye"></i>
                                    Preview
                                </button>
                            </div>

                            {/* Saving Indicator */}
                            {saving && selectedProductCard === productCard.id && (
                                <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <div className="bg-white rounded-lg p-4 flex items-center gap-3">
                                        <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                                        <span className="text-gray-700 font-medium">Saving...</span>
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        </div>

        {/* Footer Selection Section */}
        <div className="grid grid-cols-6 gap-6 mt-16">
            {/* Left Column - Default Footer Text (1 column) */}
            <div className="col-span-6 lg:col-span-1 flex items-start justify-center">
                <div className="text-center lg:text-left lg:mt-0">
                    <h3 className="text-lg font-semibold text-slate-600">Default Footer</h3>
                </div>
            </div>

            {/* Right Column - Footer Images (5 columns) */}
            <div className="col-span-6 lg:col-span-5">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {footers.map((footer) => (
                        <div
                            key={footer.id}
                            className={`relative border-4 rounded-lg overflow-hidden cursor-pointer transition-all duration-300 w-full ${
                                selectedFooter === footer.id
                                    ? 'border-blue-500 shadow-lg ring-4 ring-blue-500 ring-opacity-20'
                                    : 'border-gray-300 hover:border-blue-400 hover:shadow-lg'
                            }`}
                            onClick={() => handleFooterSelect(footer.id)}
                        >
                            <div className="p-2">
                                <img
                                    src={footer.image}
                                    alt={footer.name}
                                    className="h-auto max-h-64 w-full object-contain mx-auto"
                                    onError={(e) => {
                                        const target = e.target as HTMLImageElement;
                                        target.src = 'https://picsum.photos/800/300';
                                    }}
                                />
                            </div>

                            <div className="p-3 bg-white">
                                <h4 className="text-sm font-semibold text-center text-gray-800">
                                    {footer.name}
                                </h4>
                            </div>

                            {selectedFooter === footer.id && (
                                <div className="absolute top-2 left-2 w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center">
                                    <i className="fa fa-check text-sm"></i>
                                </div>
                            )}

                            <div className="absolute top-2 right-2 flex gap-2">
                                <button
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        handleFooterPreview(footer);
                                    }}
                                    className="bg-blue-500 text-white px-3 py-2 rounded-md hover:bg-blue-600 transition-colors duration-300 flex items-center gap-2"
                                >
                                    <i className="fa fa-eye"></i>
                                    Preview
                                </button>
                                <button
                                    onClick={(e) => {
                                        e.stopPropagation();
                                        window.location.href = '/admin/theme/footer/configure';
                                    }}
                                    className="bg-green-500 text-white px-3 py-2 rounded-md hover:bg-green-600 transition-colors duration-300 flex items-center gap-2 h-fit"
                                >
                                    <i className="fa fa-edit"></i>
                                    <span>Edit Footer</span>
                                </button>
                            </div>

                            {saving && selectedFooter === footer.id && (
                                <div className="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                    <div className="bg-white rounded-lg p-4 flex items-center gap-3">
                                        <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-500"></div>
                                        <span className="text-gray-700 font-medium">Saving...</span>
                                    </div>
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        </div>

        {/* Preview Modals */}
        {previewTheme && (
            <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div className="bg-white rounded-lg max-w-4xl max-h-[90vh] w-full overflow-hidden">
                    <div className="flex items-center justify-between p-4 border-b">
                        <h3 className="text-xl font-semibold text-gray-800">
                            Theme Preview: {previewTheme.name}
                        </h3>
                        <button
                            onClick={handleClosePreview}
                            className="text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            <i className="fa fa-times text-xl"></i>
                        </button>
                    </div>
                    <div className="p-4">
                        <div className="aspect-video w-full">
                            <img
                                src={previewTheme.image}
                                alt={previewTheme.name}
                                className="w-full h-full object-contain rounded-lg"
                                onError={(e) => {
                                    const target = e.target as HTMLImageElement;
                                    target.src = 'https://picsum.photos/800/450';
                                }}
                            />
                        </div>
                    </div>
                    <div className="p-4 border-t bg-gray-50">
                        <h4 className="font-semibold text-gray-800 mb-2">{previewTheme.name}</h4>
                        <p className="text-gray-600 text-sm">{previewTheme.description}</p>
                    </div>
                </div>
            </div>
        )}

        {/* Header Preview Modal */}
        {previewHeader && (
            <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div className="bg-white rounded-lg max-w-4xl max-h-[90vh] w-full overflow-hidden">
                    <div className="flex items-center justify-between p-4 border-b">
                        <h3 className="text-xl font-semibold text-gray-800">
                            Header Preview: {previewHeader.name}
                        </h3>
                        <button
                            onClick={handleCloseHeaderPreview}
                            className="text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            <i className="fa fa-times text-xl"></i>
                        </button>
                    </div>
                    <div className="p-4">
                        <div className="aspect-video w-full">
                            <img
                                src={previewHeader.image}
                                alt={previewHeader.name}
                                className="w-full h-full object-contain rounded-lg"
                                onError={(e) => {
                                    const target = e.target as HTMLImageElement;
                                    target.src = 'https://picsum.photos/800/400';
                                }}
                            />
                        </div>
                    </div>
                    <div className="p-4 border-t bg-gray-50">
                        <h4 className="font-semibold text-gray-800 mb-2">{previewHeader.name}</h4>
                        <p className="text-gray-600 text-sm">{previewHeader.description}</p>
                    </div>
                </div>
            </div>
        )}

        {/* Product Card Preview Modal */}
        {previewProductCard && (
            <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div className="bg-white rounded-lg max-w-4xl max-h-[90vh] w-full overflow-hidden">
                    <div className="flex items-center justify-between p-4 border-b">
                        <h3 className="text-xl font-semibold text-gray-800">
                            Product Card Preview: {previewProductCard.name}
                        </h3>
                        <button
                            onClick={handleCloseProductCardPreview}
                            className="text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            <i className="fa fa-times text-xl"></i>
                        </button>
                    </div>
                    <div className="p-4">
                        <div className="aspect-video w-full">
                            <img
                                src={previewProductCard.image}
                                alt={previewProductCard.name}
                                className="w-full h-full object-contain rounded-lg"
                                onError={(e) => {
                                    const target = e.target as HTMLImageElement;
                                    target.src = 'https://picsum.photos/800/450';
                                }}
                            />
                        </div>
                    </div>
                    <div className="p-4 border-t bg-gray-50">
                        <h4 className="font-semibold text-gray-800 mb-2">{previewProductCard.name}</h4>
                        <p className="text-gray-600 text-sm">{previewProductCard.description}</p>
                    </div>
                </div>
            </div>
        )}

        {/* Footer Preview Modal */}
        {previewFooter && (
            <div className="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
                <div className="bg-white rounded-lg max-w-4xl max-h-[90vh] w-full overflow-hidden">
                    <div className="flex items-center justify-between p-4 border-b">
                        <h3 className="text-xl font-semibold text-gray-800">
                            Footer Preview: {previewFooter.name}
                        </h3>
                        <button
                            onClick={handleCloseFooterPreview}
                            className="text-gray-500 hover:text-gray-700 transition-colors"
                        >
                            <i className="fa fa-times text-xl"></i>
                        </button>
                    </div>
                    <div className="p-4">
                        <div className="aspect-video w-full">
                            <img
                                src={previewFooter.image}
                                alt={previewFooter.name}
                                className="w-full h-full object-contain rounded-lg"
                                onError={(e) => {
                                    const target = e.target as HTMLImageElement;
                                    target.src = 'https://picsum.photos/900/350';
                                }}
                            />
                        </div>
                    </div>
                    <div className="p-4 border-t bg-gray-50">
                        <h4 className="font-semibold text-gray-800 mb-2">{previewFooter.name}</h4>
                        <p className="text-gray-600 text-sm">{previewFooter.description}</p>
                    </div>
                </div>
            </div>
        )}
        </>
    );
};

export default ThemeSelectionApp;
