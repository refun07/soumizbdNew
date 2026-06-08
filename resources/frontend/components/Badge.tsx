import { ReactNode } from 'react';
import { cn } from '../utils/cn';

interface Props {
    children: ReactNode;
    reverse?: boolean;
    textColor?: string;
    backgroundColor?: string;
    classNames?: {
        container?: string;
        text?: string;
        clipPath?: string;
    };
}

const Badge = ({ children, reverse, textColor, backgroundColor, classNames }: Props) => {
    return (
        <div
            className={cn(
                'text-lg font-semibold leading-none h-[45px] inline-flex items-center relative',
                classNames?.container,
                {
                    'pl-6 pr-11': !reverse,
                    'pl-11 pr-6 text-right': reverse,
                },
            )}
        >
            <span
                className={cn('relative z-[1]', classNames?.text)}
                style={textColor ? { color: textColor } : undefined}
            >
                {children}
            </span>

            <span
                className={cn(
                    'absolute inset-0',
                    classNames?.clipPath,
                    {
                        'rotate-180': reverse,
                    },
                )}
                style={{
                    backgroundColor: backgroundColor || '#f97316',
                    clipPath: 'polygon(0px 0px, 100% 0px, calc(100% - 20px) 50%, 100% 100%, 0px 100%)',
                }}
            ></span>

            {/* <span className="absolute z-[1] right-0 top-0 bottom-0 border-t-[22px] border-b-[22px] border-transparent border-r-[22px] border-r-white"></span> */}
        </div>
    );
};

export default Badge;
