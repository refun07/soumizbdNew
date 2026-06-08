import React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { LandingPageSection } from '../../hooks/useBuilderState';
import { SECTION_TYPES } from './SectionLibraryPanel';

interface SectionWrapperProps {
    section: LandingPageSection;
    index: number;
    totalSections: number;
    isHovered: boolean;
    isEditing: boolean;
    onHover: (id: number | null) => void;
    onEdit: () => void;
    onDelete: () => void;
    onDuplicate: () => void;
    onMoveUp: () => void;
    onMoveDown: () => void;
    children: React.ReactNode;
}

const SectionWrapper: React.FC<SectionWrapperProps> = ({
    section,
    index,
    totalSections,
    isHovered,
    isEditing,
    onHover,
    onEdit,
    onDelete,
    onDuplicate,
    onMoveUp,
    onMoveDown,
    children,
}) => {
    const isFirst = index === 0;
    const isLast = index === totalSections - 1;

    const getSectionLabel = (type: string) => {
        const sectionType = SECTION_TYPES.find(s => s.value === type);
        return sectionType?.label || type;
    };

    return (
        <motion.div
            className="relative"
            style={{
                boxShadow: isEditing ? '0 0 0 2px var(--builder-accent), 0 0 24px var(--section-glow)' : 'none',
            }}
            onMouseEnter={() => onHover(section.id)}
            onMouseLeave={() => onHover(null)}
            initial={{ opacity: 0, y: 16, scale: 0.98 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: -16, scale: 0.98 }}
            transition={{ 
                duration: 0.3, 
                delay: index * 0.04,
                ease: [0.25, 0.46, 0.45, 0.94]
            }}
        >
            {/* Editing Indicator */}
            <AnimatePresence>
                {isEditing && (
                    <motion.div
                        initial={{ opacity: 0, y: -10 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -10 }}
                        className="section-label-badge"
                        style={{ background: 'var(--builder-accent)' }}
                    >
                        Editing: {getSectionLabel(section.type)}
                    </motion.div>
                )}
            </AnimatePresence>

            {/* Hover Overlay */}
            <AnimatePresence>
                {isHovered && !isEditing && (
                    <>
                        {/* Border overlay */}
                        <motion.div 
                            className="section-hover-overlay"
                            initial={{ opacity: 0 }}
                            animate={{ opacity: 1 }}
                            exit={{ opacity: 0 }}
                            transition={{ duration: 0.15 }}
                        />
                        
                        {/* Section label */}
                        <motion.div 
                            className="section-label-badge"
                            initial={{ opacity: 0, y: -10 }}
                            animate={{ opacity: 1, y: 0 }}
                            exit={{ opacity: 0, y: -10 }}
                            transition={{ duration: 0.15, delay: 0.05 }}
                        >
                            {getSectionLabel(section.type)}
                        </motion.div>
                        
                        {/* Floating action bar */}
                        <motion.div
                            className="section-action-bar"
                            initial={{ opacity: 0, scale: 0.95, y: -8 }}
                            animate={{ opacity: 1, scale: 1, y: 0 }}
                            exit={{ opacity: 0, scale: 0.95, y: -8 }}
                            transition={{ duration: 0.15, delay: 0.05 }}
                        >
                            {/* Move Up */}
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    onMoveUp();
                                }}
                                disabled={isFirst}
                                className="section-action-btn"
                                title={isFirst ? 'Already at top' : 'Move up'}
                            >
                                <i className="fa-solid fa-arrow-up text-sm"></i>
                            </button>
                            
                            {/* Move Down */}
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    onMoveDown();
                                }}
                                disabled={isLast}
                                className="section-action-btn"
                                title={isLast ? 'Already at bottom' : 'Move down'}
                            >
                                <i className="fa-solid fa-arrow-down text-sm"></i>
                            </button>
                            
                            <div className="section-action-divider"></div>

                            {/* Duplicate */}
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    onDuplicate();
                                }}
                                className="section-action-btn"
                                title="Duplicate section"
                                style={{ color: 'var(--builder-info)' }}
                            >
                                <i className="fa-solid fa-copy text-sm"></i>
                            </button>
                            
                            {/* Edit */}
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    onEdit();
                                }}
                                className="section-action-btn edit"
                                title="Edit section"
                            >
                                <i className="fa-solid fa-pencil text-sm"></i>
                            </button>
                            
                            <div className="section-action-divider"></div>
                            
                            {/* Delete */}
                            <button
                                onClick={(e) => {
                                    e.stopPropagation();
                                    if (confirm('Are you sure you want to delete this section?')) {
                                        onDelete();
                                    }
                                }}
                                className="section-action-btn delete"
                                title="Delete section"
                            >
                                <i className="fa-solid fa-trash text-sm"></i>
                            </button>
                        </motion.div>
                    </>
                )}
            </AnimatePresence>
            
            {/* Section content */}
            <div>
                {children}
            </div>
        </motion.div>
    );
};

export default SectionWrapper;
