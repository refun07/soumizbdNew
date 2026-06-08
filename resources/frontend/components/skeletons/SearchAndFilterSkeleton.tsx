import SectionTitle from '../titles/SectionTitle';
import ProductCardSkeleton from './ProductCardSkeleton';

const SearchAndFilterSkeleton = () => {
    return (
        <div
            className={`container grid grid-cols-4 mb-20`}
            id="search-and-filter"
        >
            <aside>
                <SectionTitle title="Filters" connector className="mb-5" />

                <div className="space-y-2.5">
                    {Array(3)
                        .fill(0)
                        .map((_, i) => (
                            <div
                                className="border border-primary-14 py-3 md:py-5 px-4 md:px-8 rounded-card"
                                key={i}
                            >
                                <div className="skeleton h-6 pb-2 w-1/2 mb-8"></div>

                                <div className="mt-5 flex flex-col gap-3">
                                    <div className="skeleton h-4 w-4/6"></div>
                                    <div className="skeleton h-4 w-5/6"></div>
                                    <div className="skeleton h-4 w-2/6"></div>
                                    <div className="skeleton h-4 w-3/6"></div>
                                    <div className="skeleton h-4 w-5/6"></div>
                                    <div className="skeleton h-4 w-2/6"></div>
                                </div>
                            </div>
                        ))}
                </div>
            </aside>

            <div className="col-span-3">
                <div className="flex items-center gap-4">
                    <SectionTitle title={''} className="grow" connector />

                    <div className="flex items-center gap-3">
                        <span>Sorting</span>
                        <div className="skeleton h-8"></div>
                    </div>
                </div>

                <div
                    className="grid grid-cols-4 mt-6 gap-2.5"
                    id="filtered-products"
                >
                    {Array(8)
                        .fill(0)
                        .map((_, i) => (
                            <ProductCardSkeleton key={i} />
                        ))}
                </div>
            </div>
        </div>
    );
};

export default SearchAndFilterSkeleton;
