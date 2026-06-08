import { RiDoubleQuotesL } from 'react-icons/ri';
import { IClientReview } from '../store/features/api/homeApis/commonHomeTypes';
import ThemeRating from './reviews/ThemeRating';

interface Props {
    review: IClientReview;
}

const Testimonial = ({ review }: Props) => {
    return (
        <div className="border border-black/[.03] bg-white rounded-card px-4 md:px-8 lg:px-[50px] py-4 md:py-[40px] relative">
            <span className="text-[50px] md:text-[100px] text-primary/20 absolute top-3 left-4 md:left-8 lg:left-[50px]">
                <RiDoubleQuotesL />
            </span>
            <div className="flex justify-end pt-4">
                <ThemeRating rating={Number(review.rating)} />
            </div>
            <p className="italic text-black/70 mt-5 md:mt-10 mb-5">
                {review.text}
            </p>

            <div className="flex items-center gap-4">
                <div className="">
                    <img
                        src={review.avatar}
                        alt={`${review.name} avatar`}
                        className="w-12 h-12 rounded-full"
                    />
                </div>
                <div>
                    <h3 className="font-semibold text-lg">{review.name}</h3>
                    <p className="text-black/70 text-sm font-secondary">
                        {review.designation}
                    </p>
                </div>
            </div>
        </div>
    );
};

export default Testimonial;
