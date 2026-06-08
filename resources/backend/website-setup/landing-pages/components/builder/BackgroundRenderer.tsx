import React from 'react';

interface BackgroundRendererProps {
  sectionData: any;
  pageConfigs: any;
  children: React.ReactNode;
  className?: string;
  style?: React.CSSProperties;
}

// Pattern definitions (same as blade templates)
const PATTERNS: Record<string, { image: string; size?: string }> = {
  dots: { 
    image: 'radial-gradient(circle, rgba(255,255,255,0.15) 1px, transparent 1px)', 
    size: '20px 20px' 
  },
  grid: { 
    image: 'linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px)', 
    size: '30px 30px' 
  },
  'diagonal-lines': { 
    image: 'repeating-linear-gradient(45deg, rgba(255,255,255,0.1) 0px, rgba(255,255,255,0.1) 2px, transparent 2px, transparent 10px)' 
  },
  waves: { 
    image: 'repeating-radial-gradient(circle at 0 0, transparent 0, rgba(255,255,255,0.05) 10px), repeating-linear-gradient(rgba(255,255,255,0.05), rgba(255,255,255,0.05))' 
  },
  hexagons: { 
    image: 'conic-gradient(from 60deg at 56.25% calc(425%/6), rgba(255,255,255,0.1) 120deg, transparent 0%), conic-gradient(from 180deg at 43.75% calc(425%/6), rgba(255,255,255,0.1) 120deg, transparent 0%), conic-gradient(from 300deg at calc(100%/12) 50%, rgba(255,255,255,0.1) 60deg, transparent 0%)', 
    size: '40px 35px' 
  },
  'dots-dark': { 
    image: 'radial-gradient(circle, rgba(0,0,0,0.15) 1px, transparent 1px)', 
    size: '20px 20px' 
  },
  'grid-dark': { 
    image: 'linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px)', 
    size: '30px 30px' 
  },
};

const POSITION_MAP: Record<string, string> = {
  'top-left': 'left top',
  'top-center': 'center top',
  'top-right': 'right top',
  'center-left': 'left center',
  'center': 'center center',
  'center-right': 'right center',
  'bottom-left': 'left bottom',
  'bottom-center': 'center bottom',
  'bottom-right': 'right bottom',
};

// Common styles for absolute positioned layers
const layerStyles: React.CSSProperties = {
  position: 'absolute',
  top: 0,
  left: 0,
  right: 0,
  bottom: 0,
  width: '100%',
  minHeight: '100%',
};

export const BackgroundRenderer: React.FC<BackgroundRendererProps> = ({
  sectionData,
  pageConfigs,
  children,
  className = '',
  style = {},
}) => {
  const bgColor = sectionData.background_color || pageConfigs.theme_color || '#1a472a';
  const bgImageUrl = sectionData.background_image_url;
  const bgImagePosition = sectionData.background_image_position || 'center';
  const bgImageSize = sectionData.background_image_size || 'cover';
  const bgImageRepeat = sectionData.background_image_repeat || 'no-repeat';
  const bgImageOpacity = (sectionData.background_image_opacity ?? 100) / 100;
  const bgImageOverlayColor = sectionData.background_image_overlay_color;
  const bgImageOverlayOpacity = (sectionData.background_image_overlay_opacity ?? 50) / 100;
  const bgImageParallax = sectionData.background_image_parallax || false;
  const bgPattern = sectionData.background_pattern;

  const sectionStyle: React.CSSProperties = {
    position: 'relative',
    backgroundColor: bgColor,
    ...style,
  };

  if (bgImageParallax) {
    sectionStyle.backgroundAttachment = 'fixed';
  }

  return (
    <section className={className} style={sectionStyle}>
      {/* Background Image Layer */}
      {bgImageUrl && (
        <div
          style={{
            ...layerStyles,
            backgroundImage: `url(${bgImageUrl})`,
            backgroundPosition: POSITION_MAP[bgImagePosition] || 'center center',
            backgroundSize: bgImageSize,
            backgroundRepeat: bgImageRepeat,
            opacity: bgImageOpacity,
            zIndex: 0,
          }}
        />
      )}

      {/* Overlay Layer */}
      {bgImageOverlayColor && (
        <div
          style={{
            ...layerStyles,
            backgroundColor: bgImageOverlayColor,
            opacity: bgImageOverlayOpacity,
            zIndex: 1,
          }}
        />
      )}

      {/* Pattern Layer */}
      {bgPattern && PATTERNS[bgPattern] && (
        <div
          style={{
            ...layerStyles,
            backgroundImage: PATTERNS[bgPattern].image,
            backgroundSize: PATTERNS[bgPattern].size || 'cover',
            zIndex: 2,
            pointerEvents: 'none',
          }}
        />
      )}

      {/* Content Layer */}
      <div style={{ position: 'relative', zIndex: 10, width: '100%', height: '100%' }}>
        {children}
      </div>
    </section>
  );
};

export default BackgroundRenderer;
