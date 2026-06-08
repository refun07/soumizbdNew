const CampaignCardSkeleton = () => {
    return (
        <div>
            <div className="skeleton w-full aspect-video h-auto"></div>
            <div className="flex gap-3 justify-between lg:items-center mt-3">
                <div className="flex gap-2">
                    <div className="skeleton w-8 md:w-10 h-8 md:h-10"></div>
                    <div className="skeleton w-8 md:w-10 h-8 md:h-10"></div>
                    <div className="skeleton w-8 md:w-10 h-8 md:h-10"></div>
                </div>

                <div className="skeleton w-[100px] md:w-[150px] h-6"></div>
            </div>
        </div>
    );
};

export default CampaignCardSkeleton;
