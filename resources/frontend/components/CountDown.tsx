import ReactCountdown, { CountdownRendererFn } from 'react-countdown';
import { cn } from '../utils/cn';
import { translate } from '../utils/translate';

// Renderer callback with condition
const renderer: CountdownRendererFn = ({
    days,
    hours,
    minutes,
    seconds,
    completed,
    props,
}) => {
    if (completed) {
        return null; // Don't render anything when the countdown is completed
    } else {
        return (
            <div className={cn('flex items-start gap-1', props.className)}>
                {days > 1 ? (
                    <div className="md:px-1 flex flex-col items-center justify-center">
                        <span className="text-lg md:text-[34px] md:leading-none font-medium">
                            {days}
                        </span>
                        <span className="max-md:text-sm leading-none text-black/50">
                            {translate('Day')}
                        </span>
                    </div>
                ) : null}

                {days > 1 ? (
                    <span className="max-md:hidden text-secondary text-3xl leading-none">
                        :
                    </span>
                ) : null}

                <div className="md:px-1 flex flex-col items-center justify-center">
                    <span className="text-lg md:text-[34px] md:leading-none font-medium">
                        {hours}
                    </span>
                    <span className="max-md:text-sm leading-none text-black/50">
                        Hour
                    </span>
                </div>

                <span className="max-md:hidden text-secondary text-3xl leading-none">
                    :
                </span>

                <div className="md:px-1 flex flex-col items-center justify-center">
                    <span className="text-lg md:text-[34px] md:leading-none font-medium">
                        {minutes}
                    </span>
                    <span className="max-md:text-sm leading-none text-black/50">
                        Min
                    </span>
                </div>

                <span className="max-md:hidden text-secondary text-3xl leading-none">
                    :
                </span>

                <div className="md:px-1 flex flex-col items-center justify-center">
                    <span className="text-lg md:text-[34px] md:leading-none font-medium">
                        {seconds}
                    </span>
                    <span className="max-md:text-sm leading-none text-black/50">
                        Sec
                    </span>
                </div>
            </div>
        );
    }
};

interface Props {
    date?: Date | string | number | null;
    label?: string;
    className?: string;
    classNames?: {
        container?: string;
        label?: string;
        countDown?: string;
    };
}

const CountDown = ({ label, className, classNames, date }: Props) => {
    if (!date) return null;

    if (!label) {
        return (
            <ReactCountdown
                className={cn(className, classNames?.countDown)}
                date={new Date(date)}
                renderer={renderer}
            />
        );
    }

    return (
        <div
            className={cn(
                'flex items-center gap-4 mt-4',
                className,
                classNames?.container,
            )}
        >
            <span className={cn('ek-h5 text-black', classNames?.label)}>
                {label}
            </span>
            <ReactCountdown
                className={classNames?.countDown}
                date={new Date(date)}
                renderer={renderer}
            />
        </div>
    );
};

export default CountDown;
