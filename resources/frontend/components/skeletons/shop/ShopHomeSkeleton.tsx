import ProductCardSkeleton from '../ProductCardSkeleton';

const ShopHomeSkeleton = () => {
    return (
        <>
            <div className="container no-style grid grid-cols-2">
                <div className="skeleton h-[300px]"></div>
                <div className="skeleton h-[300px]"></div>
            </div>

            <div className="container grid grid-cols-5 mt-5 gap-5">
                {Array(5)
                    .fill(0)
                    .map((_, i) => (
                        <ProductCardSkeleton key={i} />
                    ))}
            </div>
        </>
    );
};

export default ShopHomeSkeleton;
