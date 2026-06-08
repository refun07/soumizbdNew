import { ButtonHTMLAttributes, DetailedHTMLProps } from 'react';
import { IoArrowBack, IoArrowForward } from 'react-icons/io5';
import { Link, LinkProps as ReactLinkProps } from 'react-router-dom';
import { Size } from '../../types';
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
    variant?: 'primary' | 'secondary' | 'outline' | 'link';
    isLoading?: boolean;
    size?: Size;
    arrow?: false | 'left' | 'right';
    textColor?: string;
    backgroundColor?: string;
    classNames?: {
        container?: string;
        button?: string;
        spinner?: string;
        arrow?: string;
    };
};

type Props = CommonProps & (ButtonProps | LinkProps);

const Button = (props: Props) => {
    const { isLoading } = props;

    const variantClasses = {
        primary:
            'bg-gradient-to-r from-primary from-[50%] to-secondary to-[50%] bg-left bg-[200%_auto] text-white hover:bg-right px-5 md:px-8 text-[15px]',
        secondary:
            'bg-gradient-to-r from-secondary from-[50%] to-primary to-[50%] bg-left bg-[200%_auto] text-white hover:bg-right px-5 md:px-8 text-[15px]',
        outline: cn(
            'text-primary border border-secondary bg-gradient-to-r from-white from-[50%] to-secondary to-[50%] bg-[200%_auto] hover:bg-right hover:bg-right hover:text-white',
            {
                'pl-5 pr-3 md:pl-6 md:pr-5':
                    props.arrow === 'right' || props.arrow === undefined,
                'pl-3 pr-5 md:pl-5 md:pr-6': props.arrow === 'left',
                'px-5': props.arrow === false,
            },
        ),
        link: '',
    }[props.variant || 'primary'];

    const sizeClasses = {
        md: `h-9 md:h-[42px]`,
        lg: `h-10 md:h-[50px]`,
    }[props.size || 'md'];

    const disabledClass = cn({
        'disabled:opacity-50 disabled:cursor-not-allowed': true,
        'disabled:bg-gray-300 disabled:text-gray-500': props.variant === 'primary' || props.variant === 'secondary',
        'disabled:bg-gray-100 disabled:text-gray-400 disabled:border-gray-200': props.variant === 'outline',
    });

    const defaultClassName = `group font-semibold leading-none uppercase inline-flex justify-center items-center gap-2 rounded-button transition-all duration-[300ms] ease-in-out ${disabledClass}`;

    if (props.as === 'link') {
        const {
            className,
            classNames,
            children,
            arrow = 'right',
            textColor,
            backgroundColor,
            ...rest
        } = props;

        return (
            <Link
                className={cn(
                    defaultClassName,
                    variantClasses,
                    sizeClasses,
                    classNames?.button,
                    className,
                )}
                style={
                    backgroundColor
                        ? {
                              background: backgroundColor,
                              backgroundColor: backgroundColor,
                          }
                        : undefined
                }
                {...rest}
            >
                {isLoading ? (
                    <span
                        className={cn(
                            'inline-flex items-center gap-2',
                            classNames?.spinner,
                        )}
                    >
                        <Spinner /> {translate('Loading')}...
                    </span>
                ) : (
                    <>
                        {arrow === 'left' ? (
                            <span
                                className={cn(
                                    'text-xl group-hover:-translate-x-2 transition-all duration-300',
                                    classNames?.arrow,
                                    {
                                        'text-secondary group-hover:text-white':
                                            rest.variant === 'outline',
                                    },
                                )}
                                style={
                                    textColor
                                        ? { color: textColor }
                                        : rest.variant === 'outline'
                                          ? undefined
                                          : {}
                                }
                            >
                                <IoArrowBack />
                            </span>
                        ) : null}
                        <span
                            style={textColor ? { color: textColor } : undefined}
                        >
                            {children}
                        </span>
                        {arrow === 'right' ? (
                            <span
                                className={cn(
                                    'text-xl group-hover:translate-x-2 transition-all duration-300',
                                    classNames?.arrow,
                                    {
                                        'text-secondary group-hover:text-white':
                                            rest.variant === 'outline',
                                    },
                                )}
                                style={
                                    textColor
                                        ? { color: textColor }
                                        : rest.variant === 'outline'
                                          ? undefined
                                          : {}
                                }
                            >
                                <IoArrowForward />
                            </span>
                        ) : null}
                    </>
                )}
            </Link>
        );
    } else {
        const {
            className,
            classNames,
            isLoading,
            arrow = 'right',
            textColor,
            backgroundColor,
            children,
            ...rest
        } = props as CommonProps & ButtonProps;
        return (
            <button
                className={cn(
                    defaultClassName,
                    variantClasses,
                    sizeClasses,
                    classNames?.button,
                    className,
                )}
                style={
                    backgroundColor
                        ? {
                              background: backgroundColor,
                              backgroundColor: backgroundColor,
                          }
                        : undefined
                }
                disabled={isLoading}
                {...rest}
            >
                {isLoading ? (
                    <span
                        className={cn(
                            'inline-flex items-center gap-2',
                            classNames?.spinner,
                        )}
                    >
                        <Spinner /> {translate('Loading')}...
                    </span>
                ) : (
                    <>
                        {arrow === 'left' ? (
                            <span
                                className={cn(
                                    'text-xl group-hover:-translate-x-2 transition-all duration-300',
                                    classNames?.arrow,
                                    {
                                        'text-secondary group-hover:text-white':
                                            rest.variant === 'outline',
                                    },
                                )}
                                style={
                                    textColor
                                        ? { color: textColor }
                                        : rest.variant === 'outline'
                                          ? undefined
                                          : {}
                                }
                            >
                                <IoArrowBack />
                            </span>
                        ) : null}
                        <span
                            style={textColor ? { color: textColor } : undefined}
                        >
                            {children}
                        </span>
                        {arrow === 'right' ? (
                            <span
                                className={cn(
                                    'text-xl group-hover:translate-x-2 transition-all duration-300',
                                    classNames?.arrow,
                                    {
                                        'text-secondary group-hover:text-white':
                                            rest.variant === 'outline',
                                    },
                                )}
                                style={
                                    textColor
                                        ? { color: textColor }
                                        : rest.variant === 'outline'
                                          ? undefined
                                          : {}
                                }
                            >
                                <IoArrowForward />
                            </span>
                        ) : null}
                    </>
                )}
            </button>
        );
    }
};

export default Button;
