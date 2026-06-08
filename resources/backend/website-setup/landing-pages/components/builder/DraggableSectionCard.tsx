import React from 'react';
import { motion } from 'framer-motion';
import { useDrag } from 'react-dnd';
import SectionWireframes from './section-wireframes';

export interface SectionType {
  value: string;
  label: string;
  category: string;
  description: string;
}

interface DraggableSectionCardProps {
  sectionType: SectionType;
  count: number;
  onPreview: (section: SectionType) => void;
  onAddSection: (type: string) => void;
  size: 'large' | 'medium' | 'small';
  searchQuery: string;
}

const DraggableSectionCard: React.FC<DraggableSectionCardProps> = ({
  sectionType,
  count,
  onPreview,
  onAddSection,
  size,
  searchQuery,
}) => {
  const [{ isDragging }, drag] = useDrag(() => ({
    type: 'SECTION',
    item: { sectionType },
    collect: (monitor) => ({
      isDragging: monitor.isDragging(),
    }),
  }));

  const WireframeComponent = SectionWireframes[sectionType.value];

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

  const sizeClasses = {
    large: 'col-span-2',
    medium: 'col-span-1',
    small: 'col-span-1',
  };

  const wireframeHeight = {
    large: 'h-40',
    medium: 'h-32',
    small: 'h-28',
  };

  const accentColor = sectionType.category === 'Hero' ? '#C7782F' :
                     sectionType.category === 'Products' ? '#3D6B5B' : '#8B7355';

  return (
    <motion.div
      ref={drag}
      className={sizeClasses[size]}
      initial={{ opacity: 0, y: 12 }}
      animate={{ opacity: isDragging ? 0.5 : 1, y: 0 }}
      transition={{ duration: 0.25, ease: [0.25, 0.46, 0.45, 0.94] }}
      style={{ cursor: isDragging ? 'grabbing' : 'grab' }}
    >
      <div
        className={`section-card ${isDragging ? 'dragging' : ''}`}
      >
        {/* Wireframe Preview */}
        <div className={`section-card-wireframe ${wireframeHeight[size]}`}>
          {WireframeComponent && <WireframeComponent className="w-full h-full" />}
          
          {/* Hover Overlay */}
          <motion.div
            className="section-card-overlay"
            initial={false}
          >
            <button
              onClick={(e) => {
                e.stopPropagation();
                onPreview(sectionType);
              }}
              className="section-card-action preview"
              title="Preview section"
            >
              <i className="fa-solid fa-eye"></i>
            </button>
            <button
              onClick={(e) => {
                e.stopPropagation();
                onAddSection(sectionType.value);
              }}
              className="section-card-action add"
              title="Add to page"
            >
              <i className="fa-solid fa-plus"></i>
            </button>
            <motion.div
              className="section-card-action"
              title="Drag to add"
            >
              <i className="fa-solid fa-grip-vertical"></i>
            </motion.div>
          </motion.div>
        </div>

        {/* Card Info */}
        <div className="section-card-info">
          <div className="flex items-start justify-between gap-2">
            <h4 className="section-card-title">
              {highlightText(sectionType.label, searchQuery)}
            </h4>
            
            {count > 0 && (
              <span className="section-card-count builder-mono">
                {count}
              </span>
            )}
          </div>
          
          {/* Drag Hint */}
          <p className="section-card-hint">
            <i className="fa-solid fa-hand-pointer text-xs"></i>
            Drag to canvas
          </p>
        </div>

        {/* Category Accent Line */}
        <div 
          className="section-card-accent"
          style={{ backgroundColor: accentColor }}
        />
      </div>
    </motion.div>
  );
};

export default DraggableSectionCard;
