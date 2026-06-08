import React from 'react';

interface Props {
    className?: string;
    size?: 'small' | 'medium' | 'large' | 'xlarge';
}

const Spinner = ({ className = '', size = 'medium' }: Props) => {
    const sizeClass = {
        small: 'w-[10px] h-[10px]',
        medium: 'w-[16px] h-[16px]',
        large: 'w-[22px] h-[22px]',
        xlarge: 'w-[30px] h-[30px]',
    }[size];

    return (
        <span
            className={`${sizeClass} inline-flex rounded-full animate-spin border-2 border-solid border-muted !border-t-transparent ${className}`}
        />
    );
};

export default Spinner;
