import { Link, LinkProps as ReactLinkProps } from 'react-router-dom';
import { cn } from '../../utils/cn';
import Spinner from '../loader/Spinner';

type ButtonProps = {
    as?: 'button';
    isLoading?: boolean;
} & React.ButtonHTMLAttributes<HTMLButtonElement>;

type LinkProps = {
    as?: 'link';
} & ReactLinkProps;

type Props = ButtonProps | LinkProps;

const IconButton = (props: Props) => {
    const defaultClassName = `bot-ui-icon-button max-sm:size-8 size-10 inline-flex justify-center items-center rounded-button transition-all bg-white text-black/70 text-sm sm:border border-primary-14 hover:bg-secondary hover:text-white`;

    if (props.as === 'link') {
        const { className, children, ...rest } = props;
        return (
            <Link className={cn(defaultClassName, className)} {...rest}>
                {children}
            </Link>
        );
    } else {
        const { isLoading, ...rest } = props as ButtonProps;
        return (
            <button {...rest} disabled={rest.disabled || isLoading} aria-busy={isLoading || undefined} className={cn(defaultClassName, props.className)}>
                {isLoading ? (
                    <span className="inline-flex items-center gap-2">
                        <Spinner />
                    </span>
                ) : (
                    props.children
                )}
            </button>
        );
    }
};

export default IconButton;
