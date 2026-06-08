import { Rating } from '@smastrom/react-rating';
import '@smastrom/react-rating/style.css';
import { HiStar } from 'react-icons/hi';
import { cn } from '../../utils/cn';
import { paddedNumber } from '../../utils/numberFormatter';

interface Props {
    readOnly?: boolean;
    rating: number;
    totalReviews?: number | string;
    ratingClasses?: string;
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
    ratingClasses,
    style,
    totalReviews,
    onChange,
}: Props) => {
    return (
        <div className={cn(`flex items-center gap-2`, className)}>
            <Rating
                readOnly={readOnly}
                className={cn('max-w-[80px]', ratingClasses)}
                style={{ ...style }}
                value={Math.round(rating)}
                onChange={onChange}
                items={5}
                itemStyles={{
                    itemShapes: <HiStar />,
                }}
            />
            {totalReviews !== undefined ? (
                <span className="text-black/70">
                    ({paddedNumber(+totalReviews)})
                </span>
            ) : null}
        </div>
    );
};

export default ThemeRating;
