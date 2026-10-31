const ShopHeaderSkeleton = () => {
    return (
        <>
            <div className="container no-style">
                <div className="relative">
                    <div className="skeleton h-[150px] lg:h-[300px] w-full"></div>

                    <div className="relative z-[1] bg-white px-4 lg:pr-20 lg:pl-28 h-20">
                        <div className="flex min-w-0 items-center gap-4 md:gap-8 h-20">
                            <div className="h-[100px] w-[100px] lg:h-[158px] lg:w-[158px] shrink-0 self-end inline-flex items-center justify-center p-2 bg-white border border-zinc-200 mb-5"></div>

                            <div className="min-w-0 flex-1">
                                <div className="skeleton h-5 w-full max-w-[200px] mb-1"></div>
                                <div className="skeleton h-4 w-full max-w-[300px]"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="flex items-center justify-between py-4">
                    <div className="grid w-full grid-cols-4 gap-3 md:flex md:gap-14">
                        {Array(4)
                            .fill(0)
                            .map((_, i) => (
                                <div
                                    key={i}
                                    className="skeleton h-7 w-full md:w-24"
                                ></div>
                            ))}
                    </div>
                </div>
            </div>
        </>
    );
};

export default ShopHeaderSkeleton;
