import { Link } from 'react-router-dom';
import { ICategoryCardProps } from '../../types/props';
import Image from '../Image';

interface Props extends ICategoryCardProps {}

const CategoryCardThemeOne = ({ title, to, image }: Props) => {
    return (
        <Link
            to={to}
            className="group relative px-3 flex flex-col justify-center text-center rounded-card transition-all w-full aspect-square"
            title={title}
        >
            <div className="absolute inset-0 scale-0 pointer-events-none bg-secondary/20 group-hover:scale-100 z-[-1] transition-all duration-300 rounded-card"></div>
            <Image
                src={image}
                alt={title}
                className="mx-auto max-w-[70%] group-hover:scale-110 transition-all duration-300"
            />

            <h3 className="mt-4 md:mt-5 line-clamp-1">{title}</h3>
        </Link>
    );
};

export default CategoryCardThemeOne;
