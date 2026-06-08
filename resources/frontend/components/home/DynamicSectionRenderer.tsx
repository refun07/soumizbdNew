import React from 'react';
import DynamicSlider from './DynamicSlider';

export interface DynamicSection {
    id: string;
    type: 'slider';
    name: string;
    enabled: boolean;
    order: number;
    config: {
        title?: string;
        subtitle?: string;
        images: Array<{
            url: string;
            alt: string;
            order: number;
        }>;
        settings: {
            slidesPerView: number;
            spaceBetween: number;
            autoplay: {
                delay: number;
                pauseOnMouseEnter: boolean;
            };
        };
    };
}

interface DynamicSectionRendererProps {
    sections: DynamicSection[];
    theme: string;
}

const DynamicSectionRenderer: React.FC<DynamicSectionRendererProps> = ({
    sections,
    theme
}) => {
    if (!sections || sections.length === 0) {
        return null;
    }

    return (
        <>
            {sections.map((section) => {
                if (section.type === 'slider') {
                    return (
                        <DynamicSlider
                            key={section.id}
                            config={section.config}
                            theme={theme}
                        />
                    );
                }
                return null;
            })}
        </>
    );
};

export default DynamicSectionRenderer;
