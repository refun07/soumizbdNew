const ProductCardSkeleton = () => {
    return (
        <div className="relative flex flex-col border border-zinc-100 rounded-card">
            <div className="p-5 pb-0">
                <div className="skeleton w-full aspect-square h-auto"></div>
            </div>

            <div className="grow px-5 pb-5 pt-3 flex flex-col">
                <div className="skeleton w-20"></div>

                <div className="skeleton mt-3"></div>
                <div className="skeleton w-5/6 mt-1"></div>
                <div className="skeleton w-4/12 mt-1"></div>

                <div className="skeleton h-6 grow mt-3"></div>
            </div>
        </div>
    );
};

export default ProductCardSkeleton;
