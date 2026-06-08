import React, { HTMLAttributes, ReactNode } from 'react';
import { PiArrowRightLight } from 'react-icons/pi';
import { Link } from 'react-router-dom';
import { cn } from '../../utils/cn';

interface Props extends HTMLAttributes<HTMLDivElement> {
    title: string;
    icon?: ReactNode;
    linkText?: string;
    link?: string;
    linkClassName?: string;
    connector?: boolean;
}

const SectionTitle = ({
    title,
    icon,
    link,
    linkText,
    className,
    linkClassName,
    connector = true,
    ...rest
}: Props) => {
    return (
        <div
            className={cn(
                `flex items-center justify-between gap-3 relative mb-5`,
                className,
            )}
            {...rest}
        >
            <div className="flex items-center gap-1 sm:gap-3 relative z-[1] bg-background pr-4">
                {icon && (
                    <span className="text-xs sm:text-sm md:text-xl text-primary">
                        {icon}
                    </span>
                )}
                <h2 className="ek-h2 capitalize">
                    {title?.replace(/[_-]/g, ' ')}
                </h2>
            </div>
            {connector && (
                <span className="absolute top-1/2 left-0 right-0 -translate-y-1/2 border-b border-primary-14"></span>
            )}
            {link && linkText && (
                <Link
                    to={link}
                    className={`relative z-[1] inline-flex items-center gap-2 bg-background pl-3 ${linkClassName}`}
                >
                    {linkText}
                    <span className="text-xs sm:text-sm md:text-2xl text-secondary">
                        <PiArrowRightLight />
                    </span>
                </Link>
            )}
        </div>
    );
};

export default SectionTitle;
