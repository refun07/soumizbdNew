import React, { useState, useEffect } from 'react';

interface AnimatedChartWrapperProps {
    children: React.ReactNode;
    delay?: number;
}

const AnimatedChartWrapper: React.FC<AnimatedChartWrapperProps> = ({
    children,
    delay = 200
}) => {
    const [shouldRender, setShouldRender] = useState(false);

    useEffect(() => {
        // Don't render immediately to trigger animation on mount
        const timer = setTimeout(() => {
            setShouldRender(true);
        }, delay);

        return () => clearTimeout(timer);
    }, [delay]);

    if (!shouldRender) {
        return (
            <div className="w-full flex items-center justify-center animate-pulse">
                <div className="h-32 w-32 bg-gray-200 rounded-full"></div>
            </div>
        );
    }

    return <>{children}</>;
};

export default AnimatedChartWrapper;