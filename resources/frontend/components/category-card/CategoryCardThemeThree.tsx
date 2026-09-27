import { FaArrowRight } from 'react-icons/fa6';
import { Link } from 'react-router-dom';
import { ICategoryCardProps } from '../../types/props';
import { translate } from '../../utils/translate';
import Image from '../Image';

const CategoryCardThemeThree = ({
    image,
    quantity,
    title,
    to,
    showFullTitle,
}: ICategoryCardProps) => {
    const shouldShowQuantity =
        quantity !== undefined && quantity !== null && quantity !== '';

    return (
        <Link
            to={to}
            className="group relative flex flex-col justify-center transition-all w-full aspect-square overflow-hidden"
            title={title}
        >
            {/* bottom black gradient */}
            <div className="absolute inset-0 z-[1] bg-gradient-to-t from-black/30 to-transparent to-[30%]"></div>

            <div className="absolute inset-2 z-[1] scale-0 pointer-events-none bg-secondary/80 group-hover:scale-100  transition-all duration-300">
                <div className="absolute inset-1/2 -translate-x-1/2 -translate-y-1/2 bg-primary/80 text-white size-11 flex items-center justify-center rounded-full">
                    <FaArrowRight />
                </div>
            </div>

            <Image src={image} alt={title} className="w-full h-full" />

            <div className="absolute bottom-5 left-5 right-5 z-[1]">
                <h3
                    className={`${showFullTitle ? 'whitespace-normal break-words text-sm leading-snug md:text-base' : 'line-clamp-2'} font-semibold text-white`}
                >
                    {title}
                </h3>
                {shouldShowQuantity ? (
                    <p className="mt-1 text-sm text-white/85">
                        <span className="text-[#B3052A]">{quantity}</span>{' '}
                        <span className="lowercase">
                            {translate('items')}
                        </span>
                    </p>
                ) : null}
            </div>
        </Link>
    );
};

export default CategoryCardThemeThree;
