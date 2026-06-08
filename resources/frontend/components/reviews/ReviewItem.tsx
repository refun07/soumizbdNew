import { Link } from 'react-router-dom';
import { IReview } from '../../types/product';
import { translate } from '../../utils/translate';
import Avatar from '../Avatar';
import Image from '../Image';
import ThemeRating from './ThemeRating';

interface Props extends React.HTMLAttributes<HTMLDivElement> {
    review: IReview;
    showProductDetails?: boolean;
    // reply?: boolean;
}

const ReviewItem = ({ review, showProductDetails = false, ...rest }: Props) => {
    return (
        <div {...rest}>
            <div className="flex justify-between">
                {showProductDetails ? (
                    <div>
                        <h5 className="text-sm text-zinc-900 mb-0.5 line-clamp-2 max-w-[350px]">
                            <Link to={`/products/${review.product.slug}`}>
                                {review.product.name}
                            </Link>
                        </h5>
                        <time className="text-black/70 text-sm">
                            {review.createdDate}
                        </time>
                    </div>
                ) : (
                    <div className="flex gap-4 items-center">
                        <div>
                            <Avatar
                                name={review.user.name}
                                avatar={review.user.avatar}
                            />
                        </div>

                        <div>
                            <h5 className=" text-sm text-zinc-900 mb-0.5">
                                {review.user.name}
                            </h5>
                            <time className="text-black/70 text-sm">
                                {review.createdDate}
                            </time>
                        </div>
                    </div>
                )}

                <div className="text-right">
                    <ThemeRating readOnly rating={review.rating} />
                    <p className="mt-2">
                        {review.rating} {translate('out of 5')}
                    </p>
                </div>
            </div>

            <p className="mt-2.5">{review.description}</p>

            {review.images.length > 0 ? (
                <div className="flex flex-wrap gap-2 mb-6 mt-4">
                    {review.images.map((item) => (
                        <a href={item.image} key={item.image}>
                            <Image
                                src={item.image}
                                alt=""
                                className="aspect-square h-[72px]"
                            />
                        </a>
                    ))}
                </div>
            ) : null}
        </div>
    );
};

export default ReviewItem;
