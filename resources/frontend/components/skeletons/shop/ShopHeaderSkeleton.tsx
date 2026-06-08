const ShopHeaderSkeleton = () => {
    return (
        <>
            <div className="container no-style">
                <div className="relative">
                    <div className="skeleton h-[300px] w-full"></div>

                    <div className="relative z-[1] bg-white pr-20 pl-28 h-20">
                        <div className="flex items-center gap-8 h-20">
                            <div className="h-[158px] w-[158px] self-end inline-flex items-center justify-center p-2 bg-white border border-zinc-200 mb-5"></div>

                            <div>
                                <div className="skeleton h-5 w-[200px] mb-1"></div>
                                <div className="skeleton h-4 w-[300px]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="flex items-center justify-between py-4">
                    <div className="flex gap-14">
                        {Array(4)
                            .fill(0)
                            .map((_, i) => (
                                <div
                                    key={i}
                                    className="skeleton h-7 w-24"
                                ></div>
                            ))}
                    </div>
                </div>
            </div>
        </>
    );
};

export default ShopHeaderSkeleton;
