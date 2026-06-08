import React from 'react';
import { motion } from 'framer-motion';
import { DeviceType } from '../../hooks/useBuilderState';

interface FloatingToolbarProps {
    activePanel: 'library' | 'settings' | null;
    deviceType: DeviceType;
    onToggleLibrary: () => void;
    onToggleSettings: () => void;
    onDeviceChange: (device: DeviceType) => void;
    onSave: () => void;
    saving: boolean;
    landingPageTitle?: string;
}

const FloatingToolbar: React.FC<FloatingToolbarProps> = ({
    activePanel,
    deviceType,
    onToggleLibrary,
    onToggleSettings,
    onDeviceChange,
    onSave,
    saving,
    landingPageTitle,
}) => {
    return (
        <motion.header
            className="builder-topbar"
            initial={{ y: -64, opacity: 0 }}
            animate={{ y: 0, opacity: 1 }}
            transition={{ duration: 0.4, ease: [0.25, 0.46, 0.45, 0.94] }}
        >
            {/* Left Section */}
            <div className="builder-topbar-left">
                <a
                    href="/admin/landing-pages"
                    className="builder-back-btn"
                    title="Back to landing pages"
                >
                    <i className="fa-solid fa-arrow-left"></i>
                </a>
                
                <div>
                    <h1 className="builder-page-title">
                        {landingPageTitle || 'Landing Page Builder'}
                    </h1>
                    <p className="builder-page-subtitle builder-mono">
                        Visual Editor
                    </p>
                </div>
            </div>

            {/* Right Section */}
            <div className="builder-topbar-right">
                {/* Device Toggle */}
                <div className="builder-device-toggle">
                    <button
                        onClick={() => onDeviceChange('desktop')}
                        className={`builder-device-btn ${deviceType === 'desktop' ? 'active' : ''}`}
                        title="Desktop view"
                    >
                        <i className="fa-solid fa-desktop"></i>
                    </button>
                    <button
                        onClick={() => onDeviceChange('tablet')}
                        className={`builder-device-btn ${deviceType === 'tablet' ? 'active' : ''}`}
                        title="Tablet view"
                    >
                        <i className="fa-solid fa-tablet-screen-button"></i>
                    </button>
                    <button
                        onClick={() => onDeviceChange('mobile')}
                        className={`builder-device-btn ${deviceType === 'mobile' ? 'active' : ''}`}
                        title="Mobile view"
                    >
                        <i className="fa-solid fa-mobile-screen-button"></i>
                    </button>
                </div>

                <div className="builder-topbar-divider"></div>

                {/* Library Toggle Button */}
                <button
                    onClick={onToggleLibrary}
                    className={`builder-btn builder-btn-secondary builder-btn-sm ${activePanel === 'library' ? 'active' : ''}`}
                    style={{
                        backgroundColor: activePanel === 'library' ? 'var(--builder-accent-light)' : undefined,
                        borderColor: activePanel === 'library' ? 'var(--builder-accent)' : undefined,
                        color: activePanel === 'library' ? 'var(--builder-accent)' : undefined,
                    }}
                >
                    <i className="fa-solid fa-layer-group"></i>
                    <span>Sections</span>
                </button>

                {/* Settings Toggle Button */}
                <button
                    onClick={onToggleSettings}
                    className={`builder-btn builder-btn-secondary builder-btn-sm ${activePanel === 'settings' ? 'active' : ''}`}
                    style={{
                        backgroundColor: activePanel === 'settings' ? 'var(--builder-accent-light)' : undefined,
                        borderColor: activePanel === 'settings' ? 'var(--builder-accent)' : undefined,
                        color: activePanel === 'settings' ? 'var(--builder-accent)' : undefined,
                    }}
                >
                    <i className="fa-solid fa-gear"></i>
                    <span>Settings</span>
                </button>

                <div className="builder-topbar-divider"></div>

                {/* Save Button */}
                <button
                    onClick={onSave}
                    disabled={saving}
                    className="builder-btn builder-btn-primary builder-btn-sm"
                    style={{ minWidth: '120px' }}
                >
                    {saving ? (
                        <>
                            <span className="spinner" style={{ width: '16px', height: '16px', borderWidth: '2px' }}></span>
                            <span>Saving...</span>
                        </>
                    ) : (
                        <>
                            <i className="fa-solid fa-save"></i>
                            <span>Save</span>
                        </>
                    )}
                </button>
            </div>
        </motion.header>
    );
};

export default FloatingToolbar;
