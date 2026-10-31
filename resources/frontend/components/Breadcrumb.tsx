import { FaHome } from 'react-icons/fa';
import { MdOutlineKeyboardArrowRight } from 'react-icons/md';
import { Link } from 'react-router-dom';
import { IBreadcrumbNavigation } from '../types';
import { cn } from '../utils/cn';

interface Props {
    title?: string;
    headingLevel?: 'h1' | 'p';
    className?: string;
    navigation: IBreadcrumbNavigation[];
    classNames?: {
        container?: string;
        heading?: string;
        navigation?: string;
        item?: string;
    };
}

const Breadcrumb = ({ title, headingLevel = 'h1', className, classNames, navigation }: Props) => {
    const Heading = headingLevel;
    return (
        <div className={cn(`bot-page-heading ${title ? '' : 'bot-page-heading--trail'} bg-secondary/20 mb-5`, className)}>
            <div
                className={cn(
                    'bot-page-heading-inner container flex items-center justify-between',
                    classNames?.container,
                )}
            >
                {title && <Heading className={cn('bot-page-title', classNames?.heading)}>{title}</Heading>}

                <ul aria-label="Breadcrumb"
                    className={cn(
                        'bot-breadcrumb-trail flex items-center text-black/70 text-sm capitalize',
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
                                <span aria-current={index === navigation.length - 1 ? 'page' : undefined}
                                    className={`${
                                        index === navigation.length - 1 &&
                                        'text-black'
                                    }`}
                                >
                                    {nav.name}
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
