import { FaAngleDown, FaAngleLeft } from 'react-icons/fa';
import { Link } from 'react-router-dom';
import { ICategoryShort } from '../../../types';
import { translate } from '../../../utils/translate';
import FilterCategory from '../FilterCategory';

interface Props {
    rootCategories?: ICategoryShort[];
    selectedCategory?: ICategoryShort | null;
    categorySlug?: string;
}

const FilterCategoryCard = ({
    categorySlug,
    rootCategories,
    selectedCategory,
}: Props) => {
    const allCategoryDOM = (
        <Link
            to={`/products`}
            className="flex items-center gap-2 text-black/70 py-2 hover:text-black"
        >
            <span>
                <FaAngleLeft />
            </span>
            {translate('All Categories')}
        </Link>
    );
    const parent_categoryDOM = (
        <Link
            to={`/categories/${selectedCategory?.parent_category?.slug}`}
            className={`flex items-center gap-2 text-black/70 py-2 hover:text-black`}
        >
            <span>
                <FaAngleLeft />
            </span>
            {selectedCategory?.parent_category?.name}
        </Link>
    );
    const selectedCategoryDOM = (
        <Link
            to={`/categories/${selectedCategory?.slug}`}
            className={`flex items-center gap-2 text-black py-2 ${
                !selectedCategory?.children_categories?.length && 'pl-3'
            }`}
        >
            <span>
                {selectedCategory?.children_categories?.length ? (
                    <span className="">
                        <FaAngleDown />
                    </span>
                ) : (
                    <></>
                )}
            </span>
            {selectedCategory?.name}
        </Link>
    );
    const children_categoriesDOM = selectedCategory?.children_categories?.map(
        (item) => (
            <div className="pl-4" key={item.id}>
                <FilterCategory
                    category={item}
                    selected={item.slug === categorySlug}
                    key={item.id}
                />
            </div>
        ),
    );

    return (
        <div className="border border-primary-14 py-3 md:py-5 px-4 md:px-8 rounded-card">
            <h5 className="text-sm pb-2 border-b border-primary-14 text-zinc-900 uppercase">
                {translate('Categories')}
            </h5>

            <div className="mt-3 flex flex-col gap-3">
                <ul className="leading-none">
                    {selectedCategory ? (
                        <>
                            {allCategoryDOM}
                            {selectedCategory?.parent_category ? (
                                <>{parent_categoryDOM}</>
                            ) : (
                                <> </>
                            )}
                            {selectedCategoryDOM}
                            {selectedCategory?.children_categories?.length ? (
                                <>{children_categoriesDOM}</>
                            ) : (
                                <></>
                            )}
                        </>
                    ) : (
                        rootCategories?.map((item) => (
                            <FilterCategory
                                category={item}
                                selected={item.slug === categorySlug}
                                key={item.id}
                            />
                        ))
                    )}
                </ul>
            </div>
        </div>
    );
};

export default FilterCategoryCard;
