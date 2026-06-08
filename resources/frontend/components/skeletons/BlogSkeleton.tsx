import Image from '../../components/Image';

const BlogSkeleton = () => {
    return (
        <div className="flex flex-col border rounded-md overflow-hidden group hover:shadow-lg transition-all ease-in-out">
            <Image src={''} alt={''} className="aspect-[6/4] object-cover" />

            <div className="py-8 px-4">
                <div className="skeleton h-3 w-[100px]"></div>

                <div className="my-5 grid gap-2">
                    <div className="skeleton h-4 w-4/5"></div>
                    <div className="skeleton h-4 w-3/5"></div>
                </div>

                <div className="skeleton h-3 w-[100px]"></div>
                <div className="skeleton h-3 w-[50px] mt-2"></div>

                <div className="mt-6 flex gap-4 text-muted-foreground">
                    <div className="skeleton size-6"></div>
                    <div className="skeleton size-6"></div>
                    <div className="skeleton size-6"></div>
                    <div className="skeleton size-6"></div>
                </div>
            </div>
        </div>
    );
};

export default BlogSkeleton;
