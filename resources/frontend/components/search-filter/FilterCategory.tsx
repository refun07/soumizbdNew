import { Link } from 'react-router-dom';
import { ICategoryShort } from '../../types';
import Image from '../Image';

interface Props {
    category: ICategoryShort;
    selected?: boolean;
    path?: string;
}

const FilterCategory = ({ category, selected, path }: Props) => {
    return (
        <li>
            <Link
                to={path || `/categories/${category.slug}`}
                className={`group flex items-center justify-between py-2 w-full text-black/70 hover:text-black ${
                    selected && 'text-primary'
                }`}
            >
                <span className="flex items-center leading-none gap-2">
                    {!category.parent_category ? (
                        <Image
                            src={category.thumbnailImage}
                            title={category.name}
                            className="max-w-[20px] aspect-square rounded-full"
                        />
                    ) : (
                        <span
                            className={`w-2 h-2 rounded-full transition-all group-hover:bg-secondary ${
                                selected ? 'bg-secondary' : 'bg-zinc-300'
                            }`}
                        ></span>
                    )}

                    {category.name}
                </span>
            </Link>
        </li>
    );
};

export default FilterCategory;
