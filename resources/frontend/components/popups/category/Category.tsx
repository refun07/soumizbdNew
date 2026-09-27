import { useState } from 'react';
import { MdChevronLeft, MdChevronRight } from 'react-icons/md';
import { Link } from 'react-router-dom';
import { ICategoryShort } from '../../../types';
import { cn } from '../../../utils/cn';
import Image from '../../Image';

interface Props {
    category: ICategoryShort;
    label: number;
}

const Category = ({ category, label }: Props) => {
    const [isOpen, setIsOpen] = useState(false);

    if (label === 0 && category.parent_category) {
        return <></>;
    }

    if (category.children_categories?.length) {
        return (
            <li>
                <button
                    className={`flex items-center justify-between h-12 px-9 w-full text-zinc-900 hover:bg-primary/5 hover:text-black`}
                    onClick={() => setIsOpen(true)}
                >
                    <span className="flex items-center gap-4">
                        <Image
                            src={category.thumbnailImage}
                            title={category.name}
                            className="max-w-[20px] aspect-square rounded-full"
                        />
                        {category.name}
                    </span>

                    <span className="text-secondary text-lg">
                        <MdChevronRight />
                    </span>
                </button>

                <div
                    className={cn('absolute inset-0 bg-white text-zinc-900 transition-all', {
                        'translate-x-full': !isOpen,
                    })}
                >
                    <button
                        className="flex items-center justify-between gap-2 w-full h-12 px-8 border-b border-neutral-200 text-zinc-900 hover:bg-gray-100"
                        onClick={() => setIsOpen(false)}
                    >
                        <p className="">{category.name}</p>
                        <span className="text-secondary text-lg">
                            <MdChevronLeft />
                        </span>
                    </button>

                    <ul className={`divide-y divide-zinc-100`}>
                        {category.children_categories.map(
                            (childrenCategory) => (
                                <Category
                                    key={childrenCategory.name}
                                    category={childrenCategory}
                                    label={label + 1}
                                />
                            ),
                        )}
                    </ul>
                </div>
            </li>
        );
    }

    return (
        <li>
            <Link
                to={`/categories/${category.slug}`}
                className={`flex items-center justify-between h-12 px-9 w-full text-zinc-900 hover:bg-primary/5 hover:text-black`}
            >
                <span className="flex items-center gap-4">
                    <Image
                        src={category.thumbnailImage}
                        title={category.name}
                        className="max-w-[20px] aspect-square rounded-full"
                    />
                    {category.name}
                </span>
            </Link>
        </li>
    );
};

export default Category;
