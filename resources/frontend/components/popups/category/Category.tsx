import { useState } from 'react';
import { MdChevronLeft, MdChevronRight } from 'react-icons/md';
import { Link } from 'react-router-dom';
import { closePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { ICategoryShort } from '../../../types';
import { cn } from '../../../utils/cn';
import Image from '../../Image';

interface Props {
    category: ICategoryShort;
    label: number;
}

const Category = ({ category, label }: Props) => {
    const [isOpen, setIsOpen] = useState(false);
    const dispatch = useAppDispatch();
    const categoryPath = `/categories/${category.slug}`;

    if (label === 0 && category.parent_category) {
        return <></>;
    }

    if (category.children_categories?.length) {
        return (
            <li>
                <div className="flex items-center h-12 w-full text-zinc-900 hover:bg-primary/5 hover:text-black">
                    <Link
                        to={categoryPath}
                        className="flex min-w-0 grow items-center gap-4 self-stretch px-9 pr-3"
                        onClick={() => dispatch(closePopup())}
                    >
                        <Image
                            src={category.thumbnailImage}
                            title={category.name}
                            className="max-w-[20px] aspect-square rounded-full"
                        />
                        <span className="min-w-0 break-words">
                            {category.name}
                        </span>
                    </Link>

                    <button
                        type="button"
                        className="flex h-full min-w-12 items-center justify-center px-4 text-secondary text-lg"
                        aria-label={`Open ${category.name} subcategories`}
                        onClick={() => setIsOpen(true)}
                    >
                        <MdChevronRight />
                    </button>
                </div>

                <div
                    className={cn('absolute inset-0 bg-white text-zinc-900 transition-all', {
                        'translate-x-full': !isOpen,
                    })}
                >
                    <div className="flex items-center h-12 border-b border-neutral-200">
                        <Link
                            to={categoryPath}
                            className="flex min-w-0 grow items-center gap-4 self-stretch px-8 pr-3 text-zinc-900 hover:bg-primary/5"
                            onClick={() => dispatch(closePopup())}
                        >
                            <Image
                                src={category.thumbnailImage}
                                title={category.name}
                                className="max-w-[20px] aspect-square rounded-full"
                            />
                            <span className="min-w-0 break-words">
                                {category.name}
                            </span>
                        </Link>
                        <button
                            type="button"
                            className="flex h-full min-w-12 items-center justify-center px-4 text-secondary text-lg hover:bg-gray-100"
                            aria-label="Back to previous categories"
                            onClick={() => setIsOpen(false)}
                        >
                            <MdChevronLeft />
                        </button>
                    </div>

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
                to={categoryPath}
                className={`flex items-center justify-between h-12 px-9 w-full text-zinc-900 hover:bg-primary/5 hover:text-black`}
                onClick={() => dispatch(closePopup())}
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
