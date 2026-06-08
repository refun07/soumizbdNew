import { FaHome } from 'react-icons/fa';
import { MdOutlineKeyboardArrowRight } from 'react-icons/md';
import { Link } from 'react-router-dom';
import { IBreadcrumbNavigation } from '../types';
import { cn } from '../utils/cn';

interface Props {
    title?: string;
    className?: string;
    navigation: IBreadcrumbNavigation[];
    classNames?: {
        container?: string;
        heading?: string;
        navigation?: string;
        item?: string;
    };
}

const Breadcrumb = ({ title, className, classNames, navigation }: Props) => {
    return (
        <div className={cn('bg-secondary/20 mb-5', className)}>
            <div
                className={cn(
                    'container flex items-center justify-between h-14 md:h-[73px]',
                    classNames?.container,
                )}
            >
                <h4 className={cn('ek-h5 font-medium', classNames?.heading)}>
                    {title}
                </h4>

                <ul
                    className={cn(
                        'max-md:hidden flex items-center text-black/70 text-sm capitalize',
                        classNames?.navigation,
                    )}
                >
                    {navigation.map((nav, index) => (
                        <li
                            key={index}
                            className={cn(
                                'flex items-center',
                                classNames?.item,
                            )}
                        >
                            {index !== 0 && (
                                <span className="pl-4 pr-2.5 text-lg text-secondary">
                                    <MdOutlineKeyboardArrowRight />
                                </span>
                            )}

                            {nav.icon && (
                                <span className="text-lg mr-2 text-secondary">
                                    <FaHome />
                                </span>
                            )}

                            {index === navigation.length - 1 || !nav.link ? (
                                <span
                                    className={`${
                                        index === navigation.length - 1 &&
                                        'text-black'
                                    }`}
                                >
                                    {nav.name.length > 15
                                        ? nav.name.slice(0, 15) + '...'
                                        : nav.name}
                                </span>
                            ) : (
                                <Link
                                    to={nav.link}
                                    className="hover:text-primary"
                                >
                                    {nav.name}
                                </Link>
                            )}
                        </li>
                    ))}
                </ul>
            </div>
        </div>
    );
};

export default Breadcrumb;
