import { useSearchParams } from 'react-router-dom';
import BlogCard from '../../../components/card/BlogCard';
import Pagination from '../../../components/pagination/Pagination';
import BlogSkeleton from '../../../components/skeletons/BlogSkeleton';
import { useGetBlogsQuery } from '../../../store/features/api/BlogSlice';
import { translate } from '../../../utils/translate';

const Blogs = () => {
    const [searchParams] = useSearchParams();

    const { data: blogRes, isLoading } = useGetBlogsQuery({
        searchKey: searchParams.get('searchKey') || undefined,
        blogCategoryId: searchParams.get('blogCategoryId') || undefined,
        limit: searchParams.get('limit') || 20,
        page: searchParams.get('page') || 1,
    });

    return (
        <>
            <div className="bg-gradient-to-t from-primary/40 to-transparent">
                <div className="container h-[258px] flex items-center justify-center relative">
                    <div className="text-center">
                        <p className="text-primary text-base mb-2">
                            {translate('Learn More From')}
                        </p>
                        <h1 className="text-4xl">{translate('Our Blogs')}</h1>
                    </div>

                    <div className="max-md:hidden absolute bottom-0 right-0">
                        <img
                            src="/images/icons/blogs.svg"
                            alt="hero-blogs"
                            className="max-h-full"
                        />
                    </div>
                </div>
            </div>

            <section className="my-20">
                <div className="container">
                    <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-7">
                        {isLoading ? (
                            Array(6)
                                .fill(0)
                                .map((_, idx) => <BlogSkeleton key={idx} />)
                        ) : blogRes?.blogs.data.length === 0 ? (
                            <div className="text-center col-span-full">
                                No blogs found
                            </div>
                        ) : (
                            blogRes?.blogs.data.map((blog) => (
                                <BlogCard key={blog.id} blog={blog} />
                            ))
                        )}
                    </div>

                    <Pagination
                        className="mt-10"
                        pagination={blogRes?.blogs.meta!}
                    />
                </div>
            </section>
        </>
    );
};

export default Blogs;
