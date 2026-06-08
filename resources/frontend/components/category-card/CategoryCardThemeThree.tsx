import { FaArrowRight } from 'react-icons/fa6';
import { Link } from 'react-router-dom';
import { ICategoryCardProps } from '../../types/props';
import Image from '../Image';

const CategoryCardThemeThree = ({ image, title, to }: ICategoryCardProps) => {
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

            <h3 className="font-semibold text-white absolute bottom-5 left-5 z-[1] line-clamp-2">
                {title}
            </h3>
        </Link>
    );
};

export default CategoryCardThemeThree;
