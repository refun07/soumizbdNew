import { Rating } from '@smastrom/react-rating';
import '@smastrom/react-rating/style.css';
import React from 'react';
import { HiStar } from 'react-icons/hi';
import { paddedNumber } from '../../../frontend/utils/numberFormatter';
import { cn } from '../utils/cn';

interface Props {
    readOnly?: boolean;
    rating: number;
    totalReviews?: number | string;
    className?: string;
    style?: React.CSSProperties;
    onChange?: (_rating: number) => void;
}

export interface RatingJson {
    total: number;
    count: number;
}

const ThemeRating = ({
    readOnly = true,
    rating,
    className,
    style,
    totalReviews,
    onChange,
}: Props) => {
    return (
        <div className={cn(`flex items-center gap-2`, className)}>
            <Rating
                readOnly={readOnly}
                style={{ maxWidth: 80, ...style }}
                value={Math.round(rating)}
                onChange={onChange}
                items={5}
                itemStyles={{
                    itemShapes: <HiStar />,
                }}
            />
            {typeof totalReviews === 'number' && (
                <span className="text-muted">
                    ({paddedNumber(+totalReviews)})
                </span>
            )}
        </div>
    );
};

export default ThemeRating;
