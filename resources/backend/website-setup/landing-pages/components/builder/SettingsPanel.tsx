import React, { useState, useEffect, useCallback, useRef } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { PageConfigs, Product, Coupon, PaymentMethod, Currency } from '../../hooks/useBuilderState';

interface SettingsPanelProps {
  isOpen: boolean;
  onClose: () => void;
  pageConfigs: PageConfigs;
  selectedProducts: number[];
  selectedCouponId: number | null;
  products: Product[];
  coupons: Coupon[];
  landingPageId: number;
  onUpdatePageConfigs: (configs: PageConfigs) => void;
  onUpdateSelectedProducts: (productIds: number[]) => void;
  onUpdateCoupon: (couponId: number | null) => void;
  onSaveConfigs: (configs?: PageConfigs) => Promise<void>;
  onSaveProducts: (products?: number[]) => Promise<void>;
  onSaveCoupon: (couponId: number | null) => Promise<void>;
  saving: boolean;
  loadingProducts: boolean;
  fetchProducts: (search?: string) => Promise<void>;
  facebookPixelActive: boolean;
  metaPixelId: string;
  availablePaymentMethods: PaymentMethod[];
  selectedPaymentMethods: string[] | null;
  onUpdatePaymentMethods: (methods: string[] | null) => void;
  onSavePaymentMethods: (methods?: string[] | null) => Promise<void>;
  currency: Currency;
}

const SettingsPanel: React.FC<SettingsPanelProps> = ({
  isOpen,
  onClose,
  pageConfigs,
  selectedProducts,
  selectedCouponId,
  products,
  coupons,
  landingPageId,
  onUpdatePageConfigs,
  onUpdateSelectedProducts,
  onUpdateCoupon,
  onSaveConfigs,
  onSaveProducts,
  onSaveCoupon,
  saving,
  loadingProducts,
  fetchProducts,
  facebookPixelActive,
  metaPixelId,
  availablePaymentMethods,
  selectedPaymentMethods,
  onUpdatePaymentMethods,
  onSavePaymentMethods,
  currency,
}) => {
  const [activeTab, setActiveTab] = useState<'appearance' | 'products' | 'payment' | 'coupon'>('appearance');
  const [localConfigs, setLocalConfigs] = useState<PageConfigs>(pageConfigs);
  const [localSelectedProducts, setLocalSelectedProducts] = useState<number[]>(selectedProducts.map(id => Number(id)));
  const [localCouponId, setLocalCouponId] = useState<number | null>(selectedCouponId);
  const [productSearch, setProductSearch] = useState('');
  const [couponSearch, setCouponSearch] = useState('');
  const [removingProductId, setRemovingProductId] = useState<number | null>(null);
  const [clearingAll, setClearingAll] = useState(false);

  useEffect(() => {
    setLocalConfigs(pageConfigs);
  }, [pageConfigs]);

  useEffect(() => {
    setLocalSelectedProducts(selectedProducts.map(id => Number(id)));
  }, [selectedProducts]);

  useEffect(() => {
    setLocalCouponId(selectedCouponId);
  }, [selectedCouponId]);

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (!isOpen) return;
      if (e.key === 'Escape') {
        onClose();
      }
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen, onClose]);

  const handleSaveAppearance = async () => {
    onUpdatePageConfigs(localConfigs);
    await onSaveConfigs(localConfigs);
  };

  const handleSaveProducts = async () => {
    onUpdateSelectedProducts(localSelectedProducts);
    await onSaveProducts(localSelectedProducts);
  };

  const handleSaveCoupon = async () => {
    onUpdateCoupon(localCouponId);
    await onSaveCoupon(localCouponId);
  };

  const handleSavePaymentMethods = async () => {
    await onSavePaymentMethods(selectedPaymentMethods);
  };

  const handleToggleProduct = (productId: number) => {
    setLocalSelectedProducts(prev =>
      prev.includes(productId)
        ? prev.filter(id => id !== productId)
        : [...prev, productId]
    );
  };

  const handleRemoveProduct = async (productId: number) => {
    setRemovingProductId(productId);
    
    // Update local state
    const newSelectedProducts = localSelectedProducts.filter(id => id !== productId);
    setLocalSelectedProducts(newSelectedProducts);
    
    // Immediately save to database
    onUpdateSelectedProducts(newSelectedProducts);
    await onSaveProducts(newSelectedProducts);
    
    setRemovingProductId(null);
  };

  const handleClearAll = async () => {
    setClearingAll(true);
    
    // Update local state
    setLocalSelectedProducts([]);
    
    // Immediately save to database
    onUpdateSelectedProducts([]);
    await onSaveProducts([]);
    
    setClearingAll(false);
  };

  // Debounced product search - server-side
  const searchTimeoutRef = useRef<NodeJS.Timeout | null>(null);

  useEffect(() => {
    if (searchTimeoutRef.current) {
      clearTimeout(searchTimeoutRef.current);
    }

    searchTimeoutRef.current = setTimeout(() => {
      fetchProducts(productSearch || undefined);
    }, 400);

    return () => {
      if (searchTimeoutRef.current) {
        clearTimeout(searchTimeoutRef.current);
      }
    };
  }, [productSearch, fetchProducts]);

  const filteredCoupons = coupons.filter(coupon =>
    (coupon.code?.toLowerCase() || '').includes(couponSearch.toLowerCase())
  );

  const selectedCoupon = coupons.find(c => c.id === localCouponId);

  const renderAppearanceTab = () => (
    <div className="space-y-6">
      <div>
        <label className="builder-form-label">
          Theme Color
        </label>
        <div className="flex items-center gap-3">
          <input
            type="color"
            value={localConfigs.theme_color || '#1a472a'}
            onChange={(e) => setLocalConfigs(prev => ({ ...prev, theme_color: e.target.value }))}
            className="builder-input-color"
          />
          <input
            type="text"
            value={localConfigs.theme_color || '#1a472a'}
            onChange={(e) => setLocalConfigs(prev => ({ ...prev, theme_color: e.target.value }))}
            className="builder-input flex-1"
            placeholder="#1a472a"
          />
        </div>
        <p className="builder-form-hint">
          Primary background color used in sections
        </p>
      </div>

      <div>
        <label className="builder-form-label">
          Accent Color
        </label>
        <div className="flex items-center gap-3">
          <input
            type="color"
            value={localConfigs.primary_color || '#C7782F'}
            onChange={(e) => setLocalConfigs(prev => ({ ...prev, primary_color: e.target.value }))}
            className="builder-input-color"
          />
          <input
            type="text"
            value={localConfigs.primary_color || '#C7782F'}
            onChange={(e) => setLocalConfigs(prev => ({ ...prev, primary_color: e.target.value }))}
            className="builder-input flex-1"
            placeholder="#C7782F"
          />
        </div>
        <p className="builder-form-hint">
          Accent color used for buttons, badges, and highlights
        </p>
      </div>

      {/* Facebook Pixel Toggle - Only show if globally enabled */}
      {facebookPixelActive && (
        <div className="border-t pt-6 mt-6" style={{ borderColor: 'var(--builder-border)' }}>
          <div className="flex items-center justify-between">
            <div>
              <label className="builder-form-label flex items-center gap-2">
                <i className="fa-brands fa-facebook text-blue-600"></i>
                Facebook Pixel
              </label>
              <p className="builder-form-hint">
                Track conversions and user interactions on this landing page
              </p>
              {metaPixelId ? (
                <p className="text-xs mt-1" style={{ color: 'var(--builder-text-muted)' }}>
                  Pixel ID: {metaPixelId}
                </p>
              ) : (
                <p className="text-xs mt-1 text-amber-600">
                  <i className="fa-solid fa-triangle-exclamation mr-1"></i>
                  Meta Pixel ID not configured. <a href="/admin/configurations/analytics" target="_blank" className="underline">Configure now</a>
                </p>
              )}
            </div>
            <label className="relative inline-flex items-center cursor-pointer">
              <input
                type="checkbox"
                checked={localConfigs.facebook_pixel_enabled || false}
                onChange={(e) => setLocalConfigs(prev => ({ ...prev, facebook_pixel_enabled: e.target.checked }))}
                className="sr-only peer"
              />
              <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
            </label>
          </div>
        </div>
      )}

      <div className="pt-4">
        <button
          onClick={handleSaveAppearance}
          disabled={saving}
          className="builder-btn builder-btn-primary w-full"
        >
          {saving ? (
            <>
              <span className="spinner" style={{ width: '16px', height: '16px', borderWidth: '2px' }}></span>
              <span>Saving...</span>
            </>
          ) : (
            <>
              <i className="fa-solid fa-check"></i>
              <span>Save Appearance</span>
            </>
          )}
        </button>
      </div>
    </div>
  );

  const renderProductsTab = () => (
    <div className="space-y-4">
      <div>
        <label className="builder-form-label">
          Order Form Products
        </label>
        <p className="builder-form-hint">
          Select products that will appear in the order form section
        </p>

        {/* Selected Products Section with Remove Option */}
        {localSelectedProducts.length > 0 && (
          <div className="mt-4 mb-4">
            <div className="flex items-center justify-between mb-2">
              <span className="text-sm font-medium" style={{ color: 'var(--builder-text)' }}>
                Selected Products ({localSelectedProducts.length})
              </span>
              <button
                type="button"
                onClick={handleClearAll}
                disabled={clearingAll}
                className="text-xs hover:underline disabled:opacity-50"
                style={{ color: 'var(--builder-danger, #dc2626)' }}
              >
                {clearingAll ? 'Clearing...' : 'Clear all'}
              </button>
            </div>
            <div className="border rounded-lg overflow-hidden max-h-[200px] overflow-y-auto" style={{ borderColor: 'var(--builder-border)' }}>
              {products
                .filter(p => localSelectedProducts.includes(p.id))
                .map(product => (
                  <div
                    key={product.id}
                    className="flex items-center gap-3 p-3 border-b"
                    style={{ borderColor: 'var(--builder-border)', backgroundColor: 'var(--builder-accent-subtle, #fef3e2)' }}
                  >
                    <img
                      src={product.image || '/images/placeholder.png'}
                      alt={product.name || 'Product'}
                      className="w-10 h-10 object-cover rounded flex-shrink-0"
                    />
                    <div className="flex-1 min-w-0">
                      <p className="text-sm font-medium truncate" style={{ color: 'var(--builder-text)' }}>
                        {product.name || 'Unnamed Product'}
                      </p>
                      <p className="text-xs" style={{ color: 'var(--builder-text-muted)' }}>
                        {currency.symbol}{product.price}
                      </p>
                    </div>
                    <button
                      type="button"
                      onClick={(e) => {
                        e.stopPropagation();
                        handleRemoveProduct(product.id);
                      }}
                      disabled={removingProductId === product.id}
                      className={`p-1.5 rounded transition-colors ${
                        removingProductId === product.id 
                          ? 'opacity-50 cursor-not-allowed' 
                          : 'hover:bg-red-100'
                      }`}
                      style={{ color: 'var(--builder-danger, #dc2626)' }}
                      title={removingProductId === product.id ? "Saving..." : "Remove product"}
                    >
                      {removingProductId === product.id ? (
                        <i className="fa-solid fa-spinner fa-spin text-xs"></i>
                      ) : (
                        <i className="fa-solid fa-times"></i>
                      )}
                    </button>
                  </div>
                ))}
            </div>
          </div>
        )}

        <div className="relative mt-3 mb-4">
          <i className="fa-solid fa-search section-library-search-icon"></i>
          <input
            type="text"
            value={productSearch}
            onChange={(e) => setProductSearch(e.target.value)}
            placeholder="Search products..."
            className="builder-input"
            style={{ paddingLeft: '2.75rem' }}
          />
        </div>
      </div>

      <div
        className="border rounded-lg overflow-hidden"
        style={{ borderColor: 'var(--builder-border)' }}
      >
        <div
          className="max-h-[320px] overflow-y-auto"
          style={{ backgroundColor: 'var(--builder-surface)' }}
        >
          {loadingProducts ? (
            <div className="p-6 text-center" style={{ color: 'var(--builder-text-muted)' }}>
              <span className="spinner mb-3 block mx-auto" style={{ width: '24px', height: '24px', borderWidth: '2px' }}></span>
              <p className="text-sm">Searching products...</p>
            </div>
          ) : products.length === 0 ? (
            <div className="p-6 text-center" style={{ color: 'var(--builder-text-muted)' }}>
              <i className="fa-solid fa-box-open text-3xl mb-3 block"></i>
              <p className="text-sm">No products found</p>
            </div>
          ) : (
            products.map(product => (
              <div
                key={product.id}
                onClick={() => handleToggleProduct(product.id)}
                className={`builder-list-item ${localSelectedProducts.includes(product.id) ? 'selected' : ''}`}
              >
                <input
                  type="checkbox"
                  checked={localSelectedProducts.includes(product.id)}
                  onChange={() => {}}
                  className="builder-list-item-checkbox"
                />
                <img
                  src={product.image || '/images/placeholder.png'}
                  alt={product.name || 'Product'}
                  className="builder-list-item-image"
                />
                <div className="builder-list-item-content">
                  <p className="builder-list-item-title">
                    {product.name || 'Unnamed Product'}
                  </p>
                  <p className="builder-list-item-subtitle">
                    {currency.symbol}{product.price}
                  </p>
                </div>
              </div>
            ))
          )}
        </div>
      </div>

      {localSelectedProducts.length > 0 && (
        <div 
          className="flex items-center gap-2 text-sm px-4 py-2.5 rounded-lg"
          style={{ 
            backgroundColor: 'var(--builder-accent-subtle)',
            color: 'var(--builder-accent)',
          }}
        >
          <i className="fa-solid fa-check-circle"></i>
          <span className="font-medium">{localSelectedProducts.length} product{localSelectedProducts.length !== 1 ? 's' : ''} selected</span>
        </div>
      )}

      <div className="pt-2">
        <button
          onClick={handleSaveProducts}
          disabled={saving}
          className="builder-btn builder-btn-primary w-full"
        >
          {saving ? (
            <>
              <span className="spinner" style={{ width: '16px', height: '16px', borderWidth: '2px' }}></span>
              <span>Saving...</span>
            </>
          ) : (
            <>
              <i className="fa-solid fa-check"></i>
              <span>Save Products</span>
            </>
          )}
        </button>
      </div>
    </div>
  );

  const renderCouponTab = () => (
    <div className="space-y-4">
      <div>
        <label className="builder-form-label">
          Landing Page Coupon
        </label>
        <p className="builder-form-hint">
          Select a coupon to apply to this landing page
        </p>
        
        <div className="relative mt-3 mb-4">
          <i className="fa-solid fa-search section-library-search-icon"></i>
          <input
            type="text"
            value={couponSearch}
            onChange={(e) => setCouponSearch(e.target.value)}
            placeholder="Search coupons..."
            className="builder-input"
            style={{ paddingLeft: '2.75rem' }}
          />
        </div>
      </div>

      <div 
        className="border rounded-lg overflow-hidden"
        style={{ borderColor: 'var(--builder-border)' }}
      >
        <div 
          className="max-h-[320px] overflow-y-auto"
          style={{ backgroundColor: 'var(--builder-surface)' }}
        >
          <div
            onClick={() => setLocalCouponId(null)}
            className={`builder-list-item ${localCouponId === null ? 'selected' : ''}`}
          >
            <input
              type="radio"
              checked={localCouponId === null}
              onChange={() => {}}
              className="w-4 h-4 accent-[var(--builder-accent)] cursor-pointer"
            />
            <div className="flex-1">
              <p className="builder-list-item-title">No Coupon</p>
              <p className="builder-list-item-subtitle">Don't apply any coupon to this landing page</p>
            </div>
          </div>

          {filteredCoupons.length === 0 ? (
            <div className="p-6 text-center" style={{ color: 'var(--builder-text-muted)' }}>
              <i className="fa-solid fa-ticket text-3xl mb-3 block"></i>
              <p className="text-sm">No coupons found</p>
            </div>
          ) : (
            filteredCoupons.map(coupon => (
              <div
                key={coupon.id}
                onClick={() => setLocalCouponId(coupon.id)}
                className={`builder-list-item ${localCouponId === coupon.id ? 'selected' : ''}`}
              >
                <input
                  type="radio"
                  checked={localCouponId === coupon.id}
                  onChange={() => {}}
                  className="w-4 h-4 accent-[var(--builder-accent)] cursor-pointer"
                />
                <div className="flex-1 min-w-0">
                  <div className="flex items-center gap-2 mb-1">
                    <p className="builder-list-item-title">{coupon.code}</p>
                    <span className="builder-list-item-badge">
                      {coupon.discount_type === 'percentage'
                        ? `${coupon.discount_value}%`
                        : `${currency.symbol}${coupon.discount_value}`
                      }
                    </span>
                    {coupon.is_free_shipping && (
                      <span 
                        className="text-xs px-2 py-0.5 rounded-full font-medium"
                        style={{ 
                          backgroundColor: 'var(--builder-forest)',
                          color: 'white',
                        }}
                      >
                        Free Shipping
                      </span>
                    )}
                  </div>
                  <p 
                    className="builder-list-item-subtitle truncate"
                    dangerouslySetInnerHTML={{ __html: coupon.info }}
                  />
                </div>
              </div>
            ))
          )}
        </div>
      </div>

      {selectedCoupon && (
        <div 
          className="p-4 rounded-lg border"
          style={{ 
            borderColor: 'var(--builder-accent)',
            backgroundColor: 'var(--builder-accent-subtle)',
          }}
        >
          <div className="flex items-center justify-between">
            <div>
              <p className="font-semibold text-sm" style={{ color: 'var(--builder-accent)' }}>
                Selected: {selectedCoupon.code}
              </p>
              <p className="text-xs mt-0.5" style={{ color: 'var(--builder-text-muted)' }}>
                Valid until {new Date(selectedCoupon.end_date).toLocaleDateString()}
              </p>
            </div>
            <i className="fa-solid fa-ticket text-2xl" style={{ color: 'var(--builder-accent)', opacity: 0.5 }}></i>
          </div>
        </div>
      )}

      <div className="pt-2">
        <button
          onClick={handleSaveCoupon}
          disabled={saving}
          className="builder-btn builder-btn-primary w-full"
        >
          {saving ? (
            <>
              <span className="spinner" style={{ width: '16px', height: '16px', borderWidth: '2px' }}></span>
              <span>Saving...</span>
            </>
          ) : (
            <>
              <i className="fa-solid fa-check"></i>
              <span>Save Coupon</span>
            </>
          )}
        </button>
      </div>
    </div>
  );

  const renderPaymentTab = () => {
    const useGlobalSettings = selectedPaymentMethods === null;
    
    return (
      <div className="space-y-6">
        {/* Global Settings Toggle */}
        <div className="flex items-center justify-between p-4 rounded-lg border"
             style={{ borderColor: 'var(--builder-border)', backgroundColor: 'var(--builder-surface)' }}>
          <div>
            <label className="builder-form-label">Use Global Payment Settings</label>
            <p className="builder-form-hint">
              {useGlobalSettings 
                ? 'Showing all globally enabled payment methods' 
                : 'Using custom payment methods for this landing page'}
            </p>
          </div>
          <label className="relative inline-flex items-center cursor-pointer">
            <input
              type="checkbox"
              checked={useGlobalSettings}
              onChange={(e) => onUpdatePaymentMethods(e.target.checked ? null : [])}
              className="sr-only peer"
            />
            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
          </label>
        </div>

        {/* Payment Method Selection - Only show when NOT using global settings */}
        {!useGlobalSettings && (
          <div className="space-y-3">
            <label className="builder-form-label">Select Payment Methods</label>
            <p className="builder-form-hint">
              Choose which payment methods will be available on this landing page
            </p>

            <div className="space-y-2 mt-3">
              {availablePaymentMethods.map(method => (
                <div
                  key={method.value}
                  onClick={() => {
                    const newMethods = selectedPaymentMethods?.includes(method.value)
                      ? (selectedPaymentMethods || []).filter(m => m !== method.value)
                      : [...(selectedPaymentMethods || []), method.value];
                    onUpdatePaymentMethods(newMethods);
                  }}
                  className={`flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-colors ${
                    selectedPaymentMethods?.includes(method.value)
                      ? 'border-[var(--builder-accent)] bg-[var(--builder-accent-subtle)]'
                      : 'border-[var(--builder-border)] hover:border-[var(--builder-accent)]'
                  }`}
                >
                  <input
                    type="checkbox"
                    checked={selectedPaymentMethods?.includes(method.value) || false}
                    onChange={() => {}}
                    className="w-4 h-4 accent-[var(--builder-accent)]"
                  />
                  <span className="flex-1 text-sm" style={{ color: 'var(--builder-text)' }}>
                    {method.name}
                  </span>
                </div>
              ))}
            </div>

            {availablePaymentMethods.length === 0 && (
              <div className="p-4 text-center rounded-lg border"
                   style={{ borderColor: 'var(--builder-border)', color: 'var(--builder-text-muted)' }}>
                <i className="fa-solid fa-circle-exclamation text-2xl mb-2 block"></i>
                <p className="text-sm">No payment methods are enabled globally.</p>
                <p className="text-xs mt-1">
                  <a href="/admin/configurations/payment" target="_blank" className="underline text-[var(--builder-accent)]">
                    Configure payment methods
                  </a>
                </p>
              </div>
            )}

            {selectedPaymentMethods?.length === 0 && availablePaymentMethods.length > 0 && (
              <div className="p-3 text-center rounded-lg border border-amber-300 bg-amber-50">
                <p className="text-sm text-amber-700">
                  <i className="fa-solid fa-triangle-exclamation mr-1"></i>
                  No payment methods selected. Please select at least one.
                </p>
              </div>
            )}
          </div>
        )}

        {/* Save Button */}
        <div className="pt-4">
          <button
            onClick={handleSavePaymentMethods}
            disabled={saving || (!useGlobalSettings && (selectedPaymentMethods?.length === 0))}
            className="builder-btn builder-btn-primary w-full"
          >
            {saving ? (
              <>
                <span className="spinner" style={{ width: '16px', height: '16px', borderWidth: '2px' }}></span>
                <span>Saving...</span>
              </>
            ) : (
              <>
                <i className="fa-solid fa-check"></i>
                <span>Save Payment Methods</span>
              </>
            )}
          </button>
        </div>
      </div>
    );
  };

  const tabs = [
    { id: 'appearance', label: 'Appearance', icon: 'fa-palette' },
    { id: 'products', label: 'Products', icon: 'fa-box' },
    { id: 'payment', label: 'Payment', icon: 'fa-credit-card' },
    { id: 'coupon', label: 'Coupon', icon: 'fa-ticket' },
  ] as const;

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          <motion.div
            className="fixed inset-0 z-50"
            style={{ backgroundColor: 'rgba(28, 25, 23, 0.5)' }}
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.2 }}
            onClick={onClose}
          />

          <motion.div
            className="fixed left-0 top-0 bottom-0 w-[420px] z-[60] overflow-hidden flex flex-col"
            style={{
              backgroundColor: 'var(--builder-surface)',
              borderRight: '1px solid var(--builder-border)',
            }}
            initial={{ x: '-100%' }}
            animate={{ x: 0 }}
            exit={{ x: '-100%' }}
            transition={{ type: 'spring', damping: 28, stiffness: 220 }}
          >
            {/* Header */}
            <div className="p-5 border-b" style={{ borderColor: 'var(--builder-border)' }}>
              <div className="flex items-center justify-between mb-4">
                <div>
                  <h2 className="text-lg font-semibold" style={{ color: 'var(--builder-text)' }}>
                    Page Settings
                  </h2>
                  <p className="text-sm mt-0.5" style={{ color: 'var(--builder-text-muted)' }}>
                    Configure your landing page
                  </p>
                </div>
                <button
                  onClick={onClose}
                  className="builder-btn builder-btn-icon builder-btn-ghost"
                  title="Close settings"
                >
                  <i className="fa-solid fa-xmark text-lg"></i>
                </button>
              </div>

              {/* Tabs */}
              <div className="builder-tabs">
                {tabs.map(tab => (
                  <button
                    key={tab.id}
                    onClick={() => setActiveTab(tab.id)}
                    className={`builder-tab ${activeTab === tab.id ? 'active' : ''}`}
                  >
                    <i className={`fa-solid ${tab.icon}`}></i>
                    <span>{tab.label}</span>
                  </button>
                ))}
              </div>
            </div>

            {/* Content */}
            <div className="flex-1 overflow-y-auto p-5">
              {activeTab === 'appearance' && renderAppearanceTab()}
              {activeTab === 'products' && renderProductsTab()}
              {activeTab === 'payment' && renderPaymentTab()}
              {activeTab === 'coupon' && renderCouponTab()}
            </div>
          </motion.div>
        </>
      )}
    </AnimatePresence>
  );
};

export default SettingsPanel;
