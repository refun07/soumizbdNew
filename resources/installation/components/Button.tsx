import React, {
    ButtonHTMLAttributes,
    DetailedHTMLProps,
    MouseEvent,
} from 'react';
import { useNavigate } from 'react-router-dom';
import Spinner from './Spinner';

interface Props
    extends DetailedHTMLProps<
        ButtonHTMLAttributes<HTMLButtonElement>,
        HTMLButtonElement
    > {
    as?: 'button' | 'a';
    href?: string;
    isLoading?: boolean;
    reloadDocument?: boolean;
}

const Button = ({
    as = 'button',
    href,
    className,
    children,
    isLoading,
    reloadDocument,
    onClick,
    ...rest
}: Props) => {
    const navigate = useNavigate();

    const handleClick = (e: MouseEvent<HTMLButtonElement>) => {
        if (as === 'a') {
            e.preventDefault();

            if (reloadDocument) {
                window.location.href = href!;
            } else {
                navigate(href!);
            }
        }

        onClick?.(e);
    };

    return (
        <button
            className={`px-6 py-2.5 flex items-center justify-center gap-2 bg-primary text-white text-sm rounded disabled:bg-gray-300 ${className}`}
            onClick={handleClick}
            {...rest}
        >
            {isLoading ? <Spinner /> : null}
            {children}
        </button>
    );
};

export default Button;
