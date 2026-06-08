import { FaMinus, FaPlus } from 'react-icons/fa6';
import { cn } from '../../utils/cn';
import { paddedNumber } from '../../utils/numberFormatter';

export type TCounterOrder = 'decrement' | 'count' | 'increment';

interface Props {
    value: number;
    max?: number;
    min?: number;
    pad?: number;
    size?: 'sm' | 'md' | 'lg';
    order?: [TCounterOrder, TCounterOrder, TCounterOrder];
    orientation?: 'horizontal' | 'vertical';
    classNames?: {
        container?: string;
        button?: string;
    };

    onChange?: (value: number) => void;
    onDecrement?: () => void;
    onIncrement?: () => void;
    onMaxReached?: () => void;
}

const Counter = ({
    value,
    max,
    min = 1,
    pad = 2,
    size = 'md',
    orientation = 'vertical',
    order = ['decrement', 'count', 'increment'],
    classNames,
    onChange,
    onDecrement,
    onIncrement,
    onMaxReached,
}: Props) => {
    const handleIncrement = () => {
        if (typeof max === 'number' && value >= max) {
            onMaxReached?.();
            return;
        }
        onIncrement?.();
        onChange?.(value + 1);
    };

    const handleDecrement = () => {
        if (value <= min) {
            return;
        }
        onDecrement?.();
        onChange?.(value - 1);
    };

    const sizeClasses = {
        sm: 'w-5 aspect-square',
        md: 'w-5 sm:w-[38px] aspect-square',
        lg: 'w-5 xs:w-[38px] sm:w-[50px] aspect-square',
    };

    const buttonClasses = cn(
        `rounded flex items-center justify-center border border-zinc-100 text-primary ${sizeClasses[size]}`,
        classNames?.button,
    );

    return (
        <div
            className={cn(
                `flex items-center text-center gap-1 sm:gap-3 ${
                    orientation === 'horizontal' ? '' : 'flex-col'
                }`,
                classNames?.container,
            )}
        >
            {order.map((el) => {
                switch (el) {
                    case 'decrement':
                        return (
                            <button
                                className={buttonClasses}
                                onClick={handleDecrement}
                                key={el}
                            >
                                <FaMinus />
                            </button>
                        );
                    case 'increment':
                        return (
                            <button
                                className={buttonClasses}
                                onClick={handleIncrement}
                                key={el}
                            >
                                <FaPlus />
                            </button>
                        );
                    case 'count':
                        return (
                            <span className="text-black/70" key={el}>
                                {paddedNumber(value, pad)}
                            </span>
                        );
                    default:
                        return null;
                }
            })}
        </div>
    );
};

export default Counter;
