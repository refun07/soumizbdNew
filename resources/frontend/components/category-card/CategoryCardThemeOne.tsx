import { Link } from 'react-router-dom';
import { ICategoryCardProps } from '../../types/props';
import { translate } from '../../utils/translate';
import Image from '../Image';

interface Props extends ICategoryCardProps {}

const CategoryCardThemeOne = ({
    title,
    quantity,
    to,
    image,
    showFullTitle,
}: Props) => {
    const shouldShowQuantity =
        quantity !== undefined && quantity !== null && quantity !== '';

    return (
        <Link
            to={to}
            className={`bot-directory-card group relative px-3 flex flex-col text-center rounded-card transition-all w-full ${showFullTitle ? 'justify-start min-h-[150px] py-4' : 'justify-center aspect-square'}`}
            title={title}
        >
            <div className="absolute inset-0 scale-0 pointer-events-none bg-secondary/20 group-hover:scale-100 z-[-1] transition-all duration-300 rounded-card"></div>
            {image && !image.includes('no-media') ? <Image
                src={image}
                alt={title}
                className={`mx-auto max-w-[70%] group-hover:scale-110 transition-all duration-300 ${showFullTitle ? 'w-[70%] aspect-square object-contain shrink-0' : ''}`}
            /> : <span className="bot-directory-initial" aria-hidden="true">{title.split(/\s+/).filter(Boolean).slice(0, 2).map(word => word[0]).join('')}</span>}

            <h3
                className={`mt-4 md:mt-5 ${showFullTitle ? 'w-full min-h-[2.75em] whitespace-normal break-words text-sm leading-snug md:text-base' : 'line-clamp-1'}`}
            >
                {title}
            </h3>
            {shouldShowQuantity ? (
                <p className="mt-2 text-sm text-black/70">
                    <span className="text-[#B3052A]">{quantity}</span>{' '}
                    <span className="lowercase">{translate('items')}</span>
                </p>
            ) : null}
        </Link>
    );
};

export default CategoryCardThemeOne;
