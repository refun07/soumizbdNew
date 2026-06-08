import {
    AccordionItemProps,
    AccordionItem as Item,
} from '@szhsin/react-accordion';
import { AiOutlineMinus, AiOutlinePlus } from 'react-icons/ai';
import { cn } from '../../utils/cn';

/**
 * @type {React.ExoticComponent<import('@szhsin/react-accordion').AccordionItemProps>}
 */
const AccordionItem = ({
    header,
    panelProps,
    contentProps,
    buttonProps,
    ...rest
}: AccordionItemProps) => (
    <Item
        {...rest}
        header={({ state: { isEnter } }) => (
            <>
                {header}

                <span
                    className={`text-lg transition-transform duration-200 ease-out  ${
                        isEnter && 'rotate-180'
                    }`}
                >
                    {isEnter ? <AiOutlineMinus /> : <AiOutlinePlus />}
                </span>
            </>
        )}
        headingProps={{
            className: '',
        }}
        className="bg-white rounded-card overflow-hidden"
        buttonProps={{
            ...buttonProps,
            className: (state) =>
                cn(
                    `bg-secondary text-white w-full flex items-center justify-between py-[14px] px-3 sm:px-6 md:px-12`,
                    {
                        '!rounded-b-none': state.isEnter,
                    },
                    buttonProps?.className === 'string'
                        ? buttonProps?.className
                        : buttonProps?.className?.(state),
                ),
        }}
        contentProps={{
            ...contentProps,
            className: cn(
                `transition-height duration-200 ease-out`,
                contentProps?.className,
            ),
        }}
        panelProps={{
            ...panelProps,
            className: cn(
                `border-zinc-100 border-t-none pt-3 sm:pt-5 pb-3 sm:pb-10 px-3 sm:px-6 md:px-12`,
                panelProps?.className,
            ),
        }}
    />
);

export default AccordionItem;
