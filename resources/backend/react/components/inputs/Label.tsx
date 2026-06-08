import React from 'react';
import { cn } from '../../utils/cn';

interface Props {
    className?: string;
    children: React.ReactNode;
}

const Label = ({ className, children }: Props) => {
    return (
        <span
            className={cn(
                'px-3 py-0.5 inline-block rounded text-white bg-secondary',
                className,
            )}
        >
            {children}
        </span>
    );
};

export default Label;
