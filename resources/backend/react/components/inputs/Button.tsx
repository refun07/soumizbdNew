import React, { ButtonHTMLAttributes, DetailedHTMLProps } from 'react';
import { Link, LinkProps as ReactLinkProps } from 'react-router-dom';
import { Size, Variant } from '../../types';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';
import Spinner from '../loader/Spinner';

type ButtonProps = {
    as?: 'button';
} & DetailedHTMLProps<
    ButtonHTMLAttributes<HTMLButtonElement>,
    HTMLButtonElement
>;

type LinkProps = {
    as?: 'link';
} & ReactLinkProps;

type CommonProps = {
    variant?: Variant;
    isLoading?: boolean;
    size?: Size;
};

type Props = CommonProps & (ButtonProps | LinkProps);

const Button = (props: Props) => {
    const { isLoading } = props;

    const variantClasses = {
        primary: `bg-primary text-white hover:bg-primary/90`,
        secondary: `bg-secondary-light text-white hover:bg-secondary`,
        success: `bg-theme-green text-white hover:bg-theme-green/70`,
        warning: `bg-theme-orange text-white hover:bg-theme-orange/70`,
        yellow: `bg-theme-yellow text-white hover:bg-theme-yellow/70`,
        danger: `bg-theme-alert text-white hover:bg-theme-alert/70`,
        info: `bg-background-primary-light text-white hover:bg-background-primary-light0/70`,
        light: `bg-background text-foreground hover:bg-zinc-100`,
        dark: `bg-background-invert text-foreground-invert hover:bg-background-invert/80`,
        link: `text-foreground hover:bg-black/10`,
        'no-color': '',
    }[props.variant || 'primary'];

    const sizeClasses = {
        sm: `h-8 text-sm px-3`,
        md: `h-8 md:h-10 px-3`,
        lg: `h-10 md:h-12 px-4 md:px-6`,
        full: 'w-full grow',
    }[props.size || 'md'];

    const defaultClassName = `uppercase leading-none inline-flex justify-center items-center gap-2.5 rounded transition-all disabled:bg-gray-300 disabled:text-white dark:disabled:bg-gray-700 disabled:cursor-not-allowed`;

    if (props.as === 'link') {
        const { className, children, ...rest } = props;

        return (
            <Link
                className={cn(
                    defaultClassName,
                    variantClasses,
                    sizeClasses,
                    className,
                )}
                {...rest}
            >
                {isLoading ? (
                    <span className="inline-flex items-center gap-2">
                        <Spinner /> {translate('Loading')}...
                    </span>
                ) : (
                    children
                )}
            </Link>
        );
    } else {
        const { className, isLoading, children, ...rest } =
            props as CommonProps & ButtonProps;
        return (
            <button
                className={cn(
                    defaultClassName,
                    variantClasses,
                    sizeClasses,
                    className,
                )}
                disabled={isLoading}
                {...rest}
            >
                {isLoading ? (
                    <span className="inline-flex items-center gap-2">
                        <Spinner /> {translate('Loading')}...
                    </span>
                ) : (
                    children
                )}
            </button>
        );
    }
};

export default Button;
