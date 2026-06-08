import React, { useState, useEffect, useCallback } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import DraggableSectionCard, { SectionType } from './DraggableSectionCard';
import SectionWireframes from './section-wireframes';

export interface SectionTypeExtended extends SectionType {
  value: string;
  label: string;
  category: string;
  description: string;
}

export const SECTION_TYPES: SectionTypeExtended[] = [
  // Hero Sections
  { 
    value: 'centered_product_offer_hero', 
    label: 'Centered Product Offer Hero', 
    category: 'Hero',
    description: 'A bold hero section with centered product showcase, compelling headline, and prominent call-to-action button.'
  },
  { 
    value: 'centered_video_hero', 
    label: 'Centered Video Hero with Offer', 
    category: 'Hero',
    description: 'Engaging hero section featuring an autoplay video background with overlay text and promotional offer.'
  },
  { 
    value: 'centered_offer_hero', 
    label: 'Centered Offer Hero', 
    category: 'Hero',
    description: 'High-impact hero with large discount badge, countdown timer, and urgency-driven messaging.'
  },
  
  // Product Sections
  { 
    value: 'quality_products', 
    label: 'Quality Products', 
    category: 'Products',
    description: 'Clean 3-column grid showcasing premium products with images, titles, and pricing.'
  },
  { 
    value: 'multiple_products', 
    label: 'Multiple Products', 
    category: 'Products',
    description: 'Versatile 2x2 product grid perfect for featuring multiple items in a balanced layout.'
  },
  { 
    value: 'featured_products', 
    label: 'Featured Products', 
    category: 'Products',
    description: 'Asymmetric layout with one large featured product and two smaller complementary items.'
  },
  { 
    value: 'product_benefits', 
    label: 'Product Benefits', 
    category: 'Products',
    description: 'Horizontal row of benefit icons with descriptions highlighting key product advantages.'
  },
  { 
    value: 'product_highlights', 
    label: 'Product Highlights', 
    category: 'Products',
    description: 'Full-width image showcase with floating feature callouts and detailed specifications.'
  },
  
  // Content Sections
  { 
    value: 'quality_about', 
    label: 'Quality Products About', 
    category: 'Content',
    description: 'Two-column about section with compelling copy on the left and brand imagery on the right.'
  },
  { 
    value: 'faq_section', 
    label: 'FAQ Section', 
    category: 'Content',
    description: 'Expandable accordion-style FAQ list for addressing common customer questions and concerns.'
  },
  { 
    value: 'carousel_images', 
    label: 'Carousel Images', 
    category: 'Content',
    description: 'Interactive image slider with navigation controls for showcasing multiple visuals.'
  },
];

interface SectionLibraryPanelProps {
  isOpen: boolean;
  onClose: () => void;
  onAddSection: (type: string) => void;
  existingSections: { type: string; count: number }[];
  recentlyUsed?: string[];
  isDragging?: boolean;
}

// Category configuration with colors and sizes
const CATEGORY_CONFIG: Record<string, { color: string; size: 'large' | 'medium' | 'small' }> = {
  'Hero': { color: '#C7782F', size: 'large' },
  'Products': { color: '#3D6B5B', size: 'medium' },
  'Content': { color: '#8B7355', size: 'small' },
};

const SectionLibraryPanel: React.FC<SectionLibraryPanelProps> = ({
  isOpen,
  onClose,
  onAddSection,
  existingSections,
  recentlyUsed = [],
  isDragging = false,
}) => {
  const [searchQuery, setSearchQuery] = useState('');
  const [expandedCategories, setExpandedCategories] = useState<string[]>([]);
  const [previewSection, setPreviewSection] = useState<SectionTypeExtended | null>(null);
  const [selectedIndex, setSelectedIndex] = useState<number>(-1);

  const categories = ['Hero', 'Products', 'Content'];

  const getSectionCount = (type: string) => {
    const found = existingSections.find(s => s.type === type);
    return found ? found.count : 0;
  };

  const toggleCategory = (category: string) => {
    setExpandedCategories(prev => 
      prev.includes(category) 
        ? prev.filter(c => c !== category)
        : [...prev, category]
    );
  };

  const filteredSections = SECTION_TYPES.filter(section => {
    const query = searchQuery.toLowerCase();
    return (
      section.label.toLowerCase().includes(query) ||
      section.category.toLowerCase().includes(query) ||
      section.description.toLowerCase().includes(query)
    );
  });

  const getFilteredSectionsByCategory = (category: string) => {
    return filteredSections.filter(s => s.category === category);
  };

  // Keyboard navigation
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (!isOpen) return;

      if (e.key === '/' && !e.metaKey && !e.ctrlKey) {
        e.preventDefault();
        document.getElementById('section-search')?.focus();
      }

      if (e.key === 'Escape') {
        if (previewSection) {
          setPreviewSection(null);
        } else {
          onClose();
        }
      }

      const allSections = filteredSections;
      if (allSections.length === 0) return;

      if (e.key === 'ArrowDown') {
        e.preventDefault();
        setSelectedIndex(prev => (prev + 1) % allSections.length);
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        setSelectedIndex(prev => (prev - 1 + allSections.length) % allSections.length);
      } else if (e.key === 'Enter' && selectedIndex >= 0) {
        e.preventDefault();
        onAddSection(allSections[selectedIndex].value);
      }
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen, filteredSections, selectedIndex, onAddSection, onClose, previewSection]);

  const highlightText = (text: string, query: string) => {
    if (!query) return text;
    const parts = text.split(new RegExp(`(${query})`, 'gi'));
    return parts.map((part, i) => 
      part.toLowerCase() === query.toLowerCase() ? (
        <mark key={i} className="search-highlight">{part}</mark>
      ) : (
        part
      )
    );
  };

  return (
    <AnimatePresence>
      {isOpen && (
        <>
          {/* Backdrop */}
          <motion.div
            className={`fixed inset-0 z-50 ${isDragging ? 'pointer-events-none' : ''}`}
            style={{ backgroundColor: 'rgba(28, 25, 23, 0.5)' }}
            initial={{ opacity: 0 }}
            animate={{ opacity: 1 }}
            exit={{ opacity: 0 }}
            transition={{ duration: 0.2 }}
            onClick={onClose}
          />
            
          {/* Panel */}
          <motion.div
            className="section-library-panel fixed left-0 top-0 bottom-0 w-full sm:w-[420px] z-[60] overflow-hidden flex flex-col"
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
                    Section Library
                  </h2>
                  <p className="text-sm mt-0.5" style={{ color: 'var(--builder-text-muted)' }}>
                    Click + to add, or drag sections to canvas
                  </p>
                </div>
                <button
                  onClick={onClose}
                  className="builder-btn builder-btn-icon builder-btn-ghost"
                  title="Close library"
                >
                  <i className="fa-solid fa-xmark text-lg"></i>
                </button>
              </div>

              {/* Search Bar */}
              <div className="section-library-search">
                <i className="fa-solid fa-search section-library-search-icon"></i>
                <input
                  id="section-search"
                  type="text"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                  placeholder="Search sections..."
                  className="builder-input"
                  style={{ paddingLeft: '2.75rem' }}
                />
                <kbd className="section-library-search-kbd">/</kbd>
              </div>
            </div>

            {/* Recently Used Strip */}
            {recentlyUsed.length > 0 && !searchQuery && (
              <div className="px-5 py-3 border-b" style={{ borderColor: 'var(--builder-border)' }}>
                <h3 className="text-xs font-semibold uppercase tracking-wider mb-2 flex items-center gap-2 builder-mono" style={{ color: 'var(--builder-text-muted)' }}>
                  <i className="fa-solid fa-clock-rotate-left"></i>
                  Recently Used
                </h3>
                <div className="recently-used-strip !px-0 !py-0 !border-0">
                  {recentlyUsed.slice(0, 3).map((type, idx) => {
                    const section = SECTION_TYPES.find(s => s.value === type);
                    if (!section) return null;
                    const WireframeComponent = SectionWireframes[section.value];
                    
                    return (
                      <button
                        key={idx}
                        onClick={() => onAddSection(type)}
                        className="recently-used-item"
                      >
                        <div className="recently-used-preview">
                          {WireframeComponent && <WireframeComponent className="w-full h-full" />}
                        </div>
                        <p className="recently-used-label">
                          {section.label}
                        </p>
                      </button>
                    );
                  })}
                </div>
              </div>
            )}
            
            {/* Content - Accordion Categories */}
            <div className="flex-1 overflow-y-auto">
              {categories.map(category => {
                const categorySections = getFilteredSectionsByCategory(category);
                const isExpanded = expandedCategories.includes(category);
                const config = CATEGORY_CONFIG[category];
                
                if (categorySections.length === 0 && searchQuery) return null;
                
                return (
                  <div 
                    key={category}
                    className="section-library-category"
                  >
                    {/* Category Header */}
                    <button
                      onClick={() => toggleCategory(category)}
                      className="section-library-category-header"
                    >
                      <div className="section-library-category-left">
                        <div 
                          className="section-library-category-dot"
                          style={{ backgroundColor: config.color }}
                        />
                        <h3 className="section-library-category-title">
                          {category} Sections
                        </h3>
                        <span className="section-library-category-count builder-mono">
                          {categorySections.length}
                        </span>
                      </div>
                      <i 
                        className={`fa-solid fa-chevron-down section-library-category-chevron ${isExpanded ? 'expanded' : ''}`}
                      />
                    </button>

                    {/* Category Content */}
                    <AnimatePresence initial={false}>
                      {isExpanded && (
                        <motion.div
                          initial={{ height: 0, opacity: 0 }}
                          animate={{ height: 'auto', opacity: 1 }}
                          exit={{ height: 0, opacity: 0 }}
                          transition={{ duration: 0.25, ease: [0.25, 0.46, 0.45, 0.94] }}
                          className="overflow-hidden"
                        >
                          <div className="p-5">
                            {categorySections.length === 0 ? (
                              <p className="text-center py-8" style={{ color: 'var(--builder-text-muted)' }}>
                                No sections in this category
                              </p>
                            ) : (
                              <div className={`section-library-grid ${config.size}`}>
                                {categorySections.map((sectionType, idx) => (
                                  <DraggableSectionCard
                                    key={sectionType.value}
                                    sectionType={sectionType}
                                    count={getSectionCount(sectionType.value)}
                                    onPreview={setPreviewSection}
                                    onAddSection={onAddSection}
                                    size={config.size}
                                    searchQuery={searchQuery}
                                  />
                                ))}
                              </div>
                            )}
                          </div>
                        </motion.div>
                      )}
                    </AnimatePresence>
                  </div>
                );
              })}

              {/* Empty Search State */}
              {filteredSections.length === 0 && searchQuery && (
                <div className="empty-search-state">
                  <div className="empty-search-state-icon">
                    <i className="fa-solid fa-search"></i>
                  </div>
                  <h3 className="empty-search-state-title">
                    No sections found
                  </h3>
                  <p className="empty-search-state-text">
                    Try adjusting your search terms
                  </p>
                </div>
              )}
            </div>

            {/* Footer */}
            <div className="keyboard-shortcuts">
              <span className="keyboard-shortcut">
                <kbd>↑↓</kbd>
                <span>Navigate</span>
              </span>
              <span className="keyboard-shortcut">
                <kbd>↵</kbd>
                <span>Add</span>
              </span>
              <span className="keyboard-shortcut">
                <kbd>esc</kbd>
                <span>Close</span>
              </span>
            </div>

            {/* Preview Modal */}
            <AnimatePresence>
              {previewSection && (
                <motion.div
                  className="preview-modal-overlay"
                  initial={{ opacity: 0 }}
                  animate={{ opacity: 1 }}
                  exit={{ opacity: 0 }}
                  transition={{ duration: 0.2 }}
                  onClick={() => setPreviewSection(null)}
                >
                  <motion.div
                    className="preview-modal"
                    initial={{ scale: 0.95, opacity: 0, y: 20 }}
                    animate={{ scale: 1, opacity: 1, y: 0 }}
                    exit={{ scale: 0.95, opacity: 0, y: 20 }}
                    transition={{ type: 'spring', damping: 25, stiffness: 300 }}
                    onClick={(e) => e.stopPropagation()}
                  >
                    {/* Preview Header */}
                    <div className="preview-modal-header">
                      <div>
                        <h3 className="text-lg font-semibold" style={{ color: 'var(--builder-text)' }}>
                          {previewSection.label}
                        </h3>
                        <span className="text-sm builder-mono" style={{ color: 'var(--builder-text-muted)' }}>
                          {previewSection.category} Section
                        </span>
                      </div>
                      <button
                        onClick={() => setPreviewSection(null)}
                        className="builder-btn builder-btn-icon builder-btn-ghost"
                      >
                        <i className="fa-solid fa-xmark text-lg"></i>
                      </button>
                    </div>

                    {/* Preview Content */}
                    <div className="preview-modal-content">
                      {/* Wireframe */}
                      <div className="preview-modal-wireframe">
                        {(() => {
                          const WireframeComponent = SectionWireframes[previewSection.value];
                          return WireframeComponent ? (
                            <WireframeComponent className="w-full h-auto" />
                          ) : null;
                        })()}
                      </div>

                      {/* Description */}
                      <p className="preview-modal-description">
                        {previewSection.description}
                      </p>

                      {/* Actions */}
                      <div className="preview-modal-actions">
                        <button
                          onClick={() => {
                            onAddSection(previewSection.value);
                            setPreviewSection(null);
                          }}
                          className="builder-btn builder-btn-primary flex-1"
                        >
                          <i className="fa-solid fa-plus"></i>
                          Add to Page
                        </button>
                        <button
                          onClick={() => setPreviewSection(null)}
                          className="builder-btn builder-btn-secondary"
                        >
                          Cancel
                        </button>
                      </div>
                    </div>
                  </motion.div>
                </motion.div>
              )}
            </AnimatePresence>
          </motion.div>
        </>
      )}
    </AnimatePresence>
  );
};

export default SectionLibraryPanel;
