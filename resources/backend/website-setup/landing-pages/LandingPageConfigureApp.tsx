import React, { useEffect, useState, useCallback } from 'react';
import { DndProvider, useDrop } from 'react-dnd';
import { HTML5Backend } from 'react-dnd-html5-backend';
import { AnimatePresence } from 'framer-motion';
import { useBuilderState } from './hooks/useBuilderState';
import FloatingToolbar from './components/builder/FloatingToolbar';
import SectionLibraryPanel from './components/builder/SectionLibraryPanel';
import SectionEditorPanel from './components/builder/SectionEditorPanel';
import SettingsPanel from './components/builder/SettingsPanel';
import SectionWrapper from './components/builder/SectionWrapper';
import SectionRenderer from './components/builder/SectionRenderer';
import './styles/builder-styles.css';

// Drop zone component for between sections
interface DropZoneProps {
    index: number;
    onDrop: (type: string, index: number) => void;
    isActive: boolean;
}

const DropZone: React.FC<DropZoneProps> = ({ index, onDrop, isActive }) => {
    const [{ isOver, canDrop }, drop] = useDrop(() => ({
        accept: 'SECTION',
        drop: (item: { sectionType: { value: string } }) => {
            onDrop(item.sectionType.value, index);
        },
        collect: (monitor) => ({
            isOver: monitor.isOver(),
            canDrop: monitor.canDrop(),
        }),
    }));

    const isActiveDropZone = isActive && isOver && canDrop;

    return (
        <div
            ref={drop}
            className="drop-zone"
            style={{
                height: isActiveDropZone ? '48px' : '8px',
                border: isActiveDropZone ? '2px dashed var(--drop-indicator-color)' : '2px dashed transparent',
                backgroundColor: isActiveDropZone ? 'var(--drop-indicator-bg)' : 'transparent',
                margin: '4px 0',
            }}
        >
            {isActiveDropZone && (
                <div className="drop-indicator-content">
                    <span className="drop-indicator-label">Drop here</span>
                </div>
            )}
        </div>
    );
};

// Canvas drop zone for adding to end
interface CanvasDropZoneProps {
    onDrop: (type: string) => void;
    isActive: boolean;
}

const CanvasDropZone: React.FC<CanvasDropZoneProps> = ({ onDrop, isActive }) => {
    const [{ isOver, canDrop }, drop] = useDrop(() => ({
        accept: 'SECTION',
        drop: (item: { sectionType: { value: string } }) => {
            onDrop(item.sectionType.value);
        },
        collect: (monitor) => ({
            isOver: monitor.isOver(),
            canDrop: monitor.canDrop(),
        }),
    }));

    const isActiveDropZone = isActive && isOver && canDrop;

    return (
        <div
            ref={drop}
            className="drop-zone"
            style={{
                minHeight: isActiveDropZone ? '120px' : '40px',
                display: 'flex',
                alignItems: 'center',
                justifyContent: 'center',
                border: isActiveDropZone ? '2px dashed var(--drop-indicator-color)' : '2px dashed transparent',
                backgroundColor: isActiveDropZone ? 'var(--drop-indicator-bg)' : 'transparent',
                borderRadius: '0.75rem',
                marginTop: '1.5rem',
            }}
        >
            {isActiveDropZone ? (
                <span style={{ color: 'var(--builder-accent)', fontWeight: 600, fontSize: '0.9375rem' }}>
                    Drop to add section
                </span>
            ) : isActive ? (
                <span style={{ color: 'var(--builder-text-muted)', fontSize: '0.875rem' }}>
                    Drag sections here
                </span>
            ) : null}
        </div>
    );
};

// Device Frame Component
interface DeviceFrameProps {
    deviceType: 'mobile' | 'tablet' | 'desktop';
    children: React.ReactNode;
}

const DeviceFrame: React.FC<DeviceFrameProps> = ({ deviceType, children }) => {
    if (deviceType === 'mobile') {
        return (
            <div className="device-frame-container device-frame-mobile">
                <div className="device-frame-mobile-inner">
                    <div className="device-frame-mobile-notch"></div>
                    <div className="device-frame-mobile-content">
                        {children}
                    </div>
                </div>
            </div>
        );
    }

    if (deviceType === 'tablet') {
        return (
            <div className="device-frame-container device-frame-tablet">
                <div className="device-frame-tablet-inner">
                    <div className="device-frame-tablet-content">
                        {children}
                    </div>
                </div>
            </div>
        );
    }

    // Desktop
    return (
        <div className="device-frame-container device-frame-desktop">
            <div className="device-frame-desktop-inner">
                <div className="device-frame-desktop-chrome">
                    <div className="device-frame-desktop-dots">
                        <div className="device-frame-desktop-dot red"></div>
                        <div className="device-frame-desktop-dot yellow"></div>
                        <div className="device-frame-desktop-dot green"></div>
                    </div>
                </div>
                <div className="device-frame-desktop-content">
                    {children}
                </div>
            </div>
        </div>
    );
};

const LandingPageConfigureApp: React.FC = () => {
    const {
        landingPage,
        loading,
        saving,
        loadingProducts,
        langKey,
        activePanel,
        editingSectionId,
        deviceType,
        hoveredSectionId,
        sections,
        pageConfigs,
        setActivePanel,
        setEditingSectionId,
        setDeviceType,
        setHoveredSectionId,
        setPageConfigs,
        setSelectedProducts,
        setSelectedCouponId,
        fetchLandingPage,
        addSection,
        deleteSection,
        duplicateSection,
        reorderSection,
        savePageConfigs,
        saveSelectedProducts,
        saveCoupon,
        selectedProducts,
        selectedCouponId,
        products,
        coupons,
        fetchProducts,
        fetchCoupons,
        fetchPaymentMethods,
        facebookPixelActive,
        metaPixelId,
        availablePaymentMethods,
        selectedPaymentMethods,
        setSelectedPaymentMethods,
        savePaymentMethods,
        currency,
    } = useBuilderState();

    const [recentlyUsed, setRecentlyUsed] = useState<string[]>([]);
    const [isDragging, setIsDragging] = useState(false);

    useEffect(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const id = window.location.pathname.split('/').pop();
        const lang = urlParams.get('lang_key') || 'en-US';

        if (id) {
            fetchLandingPage(id, lang);
        }
    }, [fetchLandingPage]);

    // Fetch products, coupons, and payment methods
    useEffect(() => {
        fetchProducts();
        fetchCoupons();
        fetchPaymentMethods();
    }, [fetchProducts, fetchCoupons, fetchPaymentMethods]);

    // Load recently used from localStorage
    useEffect(() => {
        const saved = localStorage.getItem('recentlyUsedSections');
        if (saved) {
            setRecentlyUsed(JSON.parse(saved));
        }
    }, []);

    const handleToggleLibrary = () => {
        setActivePanel(activePanel === 'library' ? null : 'library');
    };

    const handleToggleSettings = () => {
        setActivePanel(activePanel === 'settings' ? null : 'settings');
    };

    const handleSave = async () => {
        await savePageConfigs();
    };

    const handleEditSection = (sectionId: number) => {
        setEditingSectionId(sectionId);
    };

    const handleCloseEditor = () => {
        setEditingSectionId(null);
    };

    const handleAddSection = useCallback((type: string) => {
        addSection(type);
        // Update recently used
        setRecentlyUsed(prev => {
            const updated = [type, ...prev.filter(t => t !== type)].slice(0, 5);
            localStorage.setItem('recentlyUsedSections', JSON.stringify(updated));
            return updated;
        });
        // Close library panel after drop
        setActivePanel(null);
        setIsDragging(false);
    }, [addSection]);

    const handleDropBetween = useCallback((type: string, index: number) => {
        // Add section at specific index
        addSection(type, index);
        // Update recently used
        setRecentlyUsed(prev => {
            const updated = [type, ...prev.filter(t => t !== type)].slice(0, 5);
            localStorage.setItem('recentlyUsedSections', JSON.stringify(updated));
            return updated;
        });
        // Close library panel after drop
        setActivePanel(null);
        setIsDragging(false);
    }, [addSection]);

    const getEditingSection = () => {
        if (!editingSectionId || !landingPage) return null;
        return sections.find(s => s.id === editingSectionId) || null;
    };

    const getExistingSectionsCount = () => {
        const counts: Record<string, number> = {};
        sections.forEach(s => {
            counts[s.type] = (counts[s.type] || 0) + 1;
        });
        return Object.entries(counts).map(([type, count]) => ({ type, count }));
    };

    if (loading) {
        return (
            <div className="landing-page-builder min-h-screen flex items-center justify-center">
                <div className="text-center">
                    <div className="spinner mb-4" style={{ width: '48px', height: '48px' }}></div>
                    <p style={{ color: 'var(--builder-text-muted)' }}>Loading landing page...</p>
                </div>
            </div>
        );
    }

    if (!landingPage) {
        return (
            <div className="landing-page-builder min-h-screen flex items-center justify-center">
                <div className="text-center">
                    <i className="fa-solid fa-circle-exclamation text-4xl mb-4" style={{ color: 'var(--builder-error)' }}></i>
                    <p style={{ color: 'var(--builder-text-muted)' }}>Failed to load landing page</p>
                </div>
            </div>
        );
    }

    return (
        <DndProvider backend={HTML5Backend}>
            <div className="landing-page-builder min-h-screen" onDragEnter={() => setIsDragging(true)}>
                {/* Top Toolbar */}
                <FloatingToolbar
                    activePanel={activePanel}
                    deviceType={deviceType}
                    onToggleLibrary={handleToggleLibrary}
                    onToggleSettings={handleToggleSettings}
                    onDeviceChange={setDeviceType}
                    onSave={handleSave}
                    saving={saving}
                    landingPageTitle={landingPage.title}
                />

                {/* Main Canvas Area */}
                <div className="pt-[64px] min-h-screen">
                    <div className="p-6 md:p-10">
                        {/* Canvas Container with Device Frame */}
                        <div 
                            className="mx-auto transition-all duration-500"
                            style={{ 
                                maxWidth: deviceType === 'mobile' ? '400px' : 
                                         deviceType === 'tablet' ? '800px' : '100%'
                            }}
                        >
                            <DeviceFrame deviceType={deviceType}>
                                {sections.length === 0 ? (
                                    <div className="empty-state rounded-xl p-16 text-center">
                                        <i 
                                            className="fa-solid fa-layer-group text-5xl mb-4 block"
                                            style={{ color: 'var(--builder-text-muted)' }}
                                        ></i>
                                        <h3 className="text-xl font-semibold mb-2" style={{ color: 'var(--builder-text)' }}>
                                            No sections yet
                                        </h3>
                                        <p className="mb-6" style={{ color: 'var(--builder-text-muted)' }}>
                                            Add your first section to start building your landing page
                                        </p>
                                        <button
                                            onClick={handleToggleLibrary}
                                            className="builder-btn builder-btn-primary"
                                        >
                                            <i className="fa-solid fa-plus mr-2"></i>
                                            Add Section
                                        </button>
                                        
                                        {/* Canvas Drop Zone */}
                                        <CanvasDropZone 
                                            onDrop={handleAddSection}
                                            isActive={isDragging || activePanel === 'library'}
                                        />
                                    </div>
                                ) : (
                                    <div>
                                        {/* Drop zone before first section */}
                                        <DropZone 
                                            index={0}
                                            onDrop={handleDropBetween}
                                            isActive={isDragging || activePanel === 'library'}
                                        />
                                        
                                        <AnimatePresence mode="popLayout">
                                            {sections.map((section, index) => (
                                                <React.Fragment key={section.id}>
                                                    <SectionWrapper
                                                        section={section}
                                                        index={index}
                                                        totalSections={sections.length}
                                                        isHovered={hoveredSectionId === section.id}
                                                        isEditing={editingSectionId === section.id}
                                                        onHover={setHoveredSectionId}
                                                        onEdit={() => handleEditSection(section.id)}
                                                        onDelete={() => deleteSection(section.id)}
                                                        onDuplicate={() => duplicateSection(section.id)}
                                                        onMoveUp={() => reorderSection(section.id, 'up')}
                                                        onMoveDown={() => reorderSection(section.id, 'down')}
                                                    >
                                                        <SectionRenderer
                                                            section={section}
                                                            pageConfigs={pageConfigs}
                                                            deviceType={deviceType}
                                                        />
                                                    </SectionWrapper>
                                                    
                                                    {/* Drop zone after each section */}
                                                    <DropZone 
                                                        index={index + 1}
                                                        onDrop={handleDropBetween}
                                                        isActive={isDragging || activePanel === 'library'}
                                                    />
                                                </React.Fragment>
                                            ))}
                                        </AnimatePresence>
                                        
                                        {/* Canvas Drop Zone at end */}
                                        <CanvasDropZone 
                                            onDrop={handleAddSection}
                                            isActive={isDragging || activePanel === 'library'}
                                        />
                                    </div>
                                )}
                            </DeviceFrame>
                        </div>
                    </div>
                </div>

                {/* Section Library Panel */}
                <SectionLibraryPanel
                    isOpen={activePanel === 'library'}
                    onClose={() => {
                        setActivePanel(null);
                        setIsDragging(false);
                    }}
                    onAddSection={handleAddSection}
                    existingSections={getExistingSectionsCount()}
                    recentlyUsed={recentlyUsed}
                    isDragging={isDragging}
                />

                {/* Section Editor Panel */}
                <SectionEditorPanel
                    isOpen={editingSectionId !== null}
                    onClose={handleCloseEditor}
                    section={getEditingSection()}
                    landingPageId={landingPage.id}
                    onUpdate={() => fetchLandingPage(landingPage.id.toString(), langKey)}
                    pageConfigs={pageConfigs}
                />

                {/* Settings Panel */}
                <SettingsPanel
                    isOpen={activePanel === 'settings'}
                    onClose={() => setActivePanel(null)}
                    pageConfigs={pageConfigs}
                    selectedProducts={selectedProducts}
                    selectedCouponId={selectedCouponId}
                    products={products}
                    coupons={coupons}
                    landingPageId={landingPage.id}
                    onUpdatePageConfigs={setPageConfigs}
                    onUpdateSelectedProducts={setSelectedProducts}
                    onUpdateCoupon={setSelectedCouponId}
                    onSaveConfigs={savePageConfigs}
                    onSaveProducts={saveSelectedProducts}
                    onSaveCoupon={saveCoupon}
                    saving={saving}
                    loadingProducts={loadingProducts}
                    fetchProducts={fetchProducts}
                    facebookPixelActive={facebookPixelActive}
                    metaPixelId={metaPixelId}
                    availablePaymentMethods={availablePaymentMethods}
                    selectedPaymentMethods={selectedPaymentMethods}
                    onUpdatePaymentMethods={setSelectedPaymentMethods}
                    onSavePaymentMethods={savePaymentMethods}
                    currency={currency}
                />
            </div>
        </DndProvider>
    );
};

export default LandingPageConfigureApp;
