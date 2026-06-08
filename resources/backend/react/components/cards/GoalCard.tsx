import React from 'react';
import { currencyFormatter } from '../../../../frontend/utils/numberFormatter';
import { useAppContext } from '../../Context';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';

interface Props {
    type: 'seller' | 'admin';
}

const GoalCard = ({ type }: Props) => {
    const { goal, setPopup } = useAppContext();

    return (
        <div
            className={cn(
                'relative rounded-md overflow-hidden px-4 py-3 sm:px-8 sm:py-6 xl:py-9 2xl:px-[70px] 2xl:py-[55px]',
                {
                    'bg-sky-200 text-black': type === 'admin',
                    'bg-[#FFD9BD] text-black': type === 'seller',
                },
            )}
        >
            <img
                src={`/images/icons/goal-${type}.png`}
                alt=""
                className="absolute -bottom-2 -right-2 w-1/2"
            />

            {goal ? (
                <div className="flex items-center gap-2">
                    <p className="text-4xl font-semibold text-primary">
                        {currencyFormatter(goal.goalAmount)}
                    </p>
                    <button
                        className="text-secondary"
                        onClick={() =>
                            setPopup({
                                name: 'goal-form',
                                props: { goal },
                            })
                        }
                    >
                        {translate('Edit Goal')}
                    </button>
                </div>
            ) : null}

            <h1 className="text-sm sm:text-lg lg:text-2xl font-medium relative">
                {goal?.title || translate('Oops! You don’t have any Goal Yet!')}
            </h1>
            <p
                className={cn('text-xs md:text-lg font-light mt-1 relative', {
                    'text-[#4E72A4]': type === 'admin',
                    'text-[#A96E38]': type === 'seller',
                })}
            >
                {goal?.text || translate('Having a goal makes you grow faster')}
            </p>

            {goal ? (
                <div>
                    <div className="mt-12 mb-10 relative w-4/6 max-w-[320px]">
                        <div className="bar relative w-full h-2 bg-background rounded-full">
                            {/* progress bar */}
                            <div
                                className="h-full absolute left-0 top-0 rounded-full bg-primary"
                                style={{
                                    width: goal.amountPercentage + '%',
                                }}
                            >
                                <span className="absolute top-full right-0 translate-y-1 translate-x-1/2 text-secondary">
                                    {currencyFormatter(goal.soldAmount)}
                                </span>

                                <span className="absolute bottom-full right-0 -translate-y-4 translate-x-1/2 text-secondary bg-white rounded px-1 py-0.5 text-xs">
                                    {goal.amountPercentage}%<span></span>
                                </span>
                            </div>

                            <span
                                className={cn(
                                    'absolute top-full right-0 translate-y-1 text-secondary',
                                    {
                                        hidden: goal.amountPercentage > 80,
                                    },
                                )}
                            >
                                {currencyFormatter(goal.goalAmount)}
                            </span>
                        </div>
                    </div>

                    <p
                        className={cn({
                            'text-[#4E72A4]': type === 'admin',
                            'text-[#A96E38]': type === 'seller',
                        })}
                    >
                        <strong>{translate('Duration')}</strong>:{' '}
                        {goal.startDate} - {goal.endDate}
                    </p>
                </div>
            ) : null}

            {!goal ? (
                <button
                    className="relative h-6 md:h-10 px-3 md:px-5 text-[10px] md:text-sm font-bold uppercase bg-primary text-white rounded-md mt-4"
                    onClick={() =>
                        setPopup({
                            name: 'goal-form',
                        })
                    }
                >
                    {translate('Set Your Goal')}
                </button>
            ) : null}
        </div>
    );
};

export default GoalCard;
