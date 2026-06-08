import { Link } from 'react-router-dom';
import { ICategoryCardProps } from '../../types/props';
import { translate } from '../../utils/translate';
import Image from '../Image';

interface Props extends ICategoryCardProps {}

const CategoryCardThemeTwo = ({ title, quantity, to, image }: Props) => {
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
                className="mx-auto max-w-[65px] group-hover:scale-110 transition-all duration-300"
            />

            <h3 className="mt-4 md:mt-5 line-clamp-1">{title}</h3>
            <p className="text-sm mt-2">
                <span className="text-secondary">{quantity}</span>{' '}
                {translate('Items')}
            </p>
        </Link>
    );
};

export default CategoryCardThemeTwo;
