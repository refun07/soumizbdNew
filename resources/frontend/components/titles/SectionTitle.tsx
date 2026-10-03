import { motion } from 'framer-motion';
import { ReactNode } from 'react';
import { cn } from '../../utils/cn';
import { highlightOnTitle } from '../../utils/highlightOnTitle';
import { translate } from '../../utils/translate';
import Button from '../buttons/Button';
import CountDown from '../CountDown';

interface Props extends React.HTMLAttributes<HTMLDivElement> {
    subTitle?: string;
    title: string;
    titleAside?: ReactNode;
    highlightFirstWord?: boolean;
    highlightClassName?: string;
    text?: string;

    link?: string;
    linkText?: string;

    classNames?: {
        container?: string;
        title?: string;
        titleContainer?: string;
        subTitle?: string;
        text?: string;
        link?: string;
    };

    countTo?: string | number | Date;
    onClick?: () => void;
    connector?: boolean;
    animated?: boolean;
}

const SectionTitle = ({
    title,
    titleAside,
    highlightFirstWord,
    highlightClassName,
    subTitle,
    text,

    link,
    linkText = 'VIEW MORE',
    classNames,

    countTo,
    onClick,
    connector = true,
    animated = false,
    className,
    ...rest
}: Props) => {
    const animation = {
        initial: { opacity: 0, y: 10 },
        whileInView: { opacity: 1, y: 0 },
        viewport: { once: true },
    };

    return (
        <div
            className={cn(
                `flex items-start justify-between gap-3 relative mb-10`,
                className,
                classNames?.container,
            )}
            {...rest}
        >
            <div className="relative z-[1] sm:pr-4 grow min-w-0">
                {subTitle ? (
                    <motion.p
                        {...(animated ? animation : {})}
                        transition={{ duration: 0.5, delay: 0 }}
                        className={cn('ek-headline', classNames?.subTitle)}
                    >
                        {subTitle}
                    </motion.p>
                ) : null}

                <div
                    className={cn(
                        'flex items-center max-sm:justify-between sm:gap-10',
                        titleAside && 'min-w-0 gap-4 sm:gap-6',
                        classNames?.titleContainer,
                    )}
                >
                    <motion.h2
                        {...(animated ? animation : {})}
                        transition={{ duration: 0.5, delay: 0.2 }}
                        className={cn(
                            'ek-h2 capitalize font-light pb-4',
                            titleAside && 'shrink-0',
                            classNames?.title,
                        )}
                        dangerouslySetInnerHTML={{
                            __html: highlightOnTitle(title, {
                                highlightFirstWord,
                                highlightClassName,
                                renderBottomBorder: true,
                            }),
                        }}
                    ></motion.h2>

                    {titleAside && (
                        <div className="min-w-0 flex-1 pb-4">
                            {titleAside}
                        </div>
                    )}

                    {countTo ? (
                        <CountDown
                            className="mt-0 max-sm:hidden"
                            date={countTo}
                        />
                    ) : null}
                </div>
                {connector && <hr className="border-b border-[#EDEDED]" />}

                {/* for small device */}
                {countTo ? (
                    <CountDown className="mt-3 sm:hidden" date={countTo} />
                ) : null}

                {text ? (
                    <p className={cn('mt-4 md:text-lg', classNames?.text)}>
                        {text}
                    </p>
                ) : null}
            </div>

            <motion.div
                {...(animated ? animation : {})}
                transition={{ duration: 0.5, delay: 0.3 }}
            >
                {link ? (
                    <Button
                        as="link"
                        variant="outline"
                        to={link}
                        className={cn(
                            `relative z-[1] mt-16 max-md:hidden`,
                            classNames?.link,
                        )}
                    >
                        {translate(linkText)}
                    </Button>
                ) : onClick ? (
                    <Button
                        variant="outline"
                        onClick={onClick}
                        className={cn(
                            `relative z-[1] mt-16 max-md:hidden`,
                            classNames?.link,
                        )}
                    >
                        {translate(linkText)}
                    </Button>
                ) : null}
            </motion.div>
        </div>
    );
};

export default SectionTitle;
