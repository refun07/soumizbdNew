import { FaFacebookF, FaLinkedinIn, FaTwitter } from 'react-icons/fa6';
import { IoMdCopy } from 'react-icons/io';
import { Link, useParams } from 'react-router-dom';
import BlogCard from '../../../components/card/BlogCard';
import SectionTitle from '../../../components/titles/SectionTitle';
import { useGetBlogQuery } from '../../../store/features/api/BlogSlice';
import { dateFormatter } from '../../../utils/dateFormatter';
import { translate } from '../../../utils/translate';

const BlogDetails = () => {
    const params = useParams<{ blogSlug: string }>();

    const { data: blogRes, isLoading } = useGetBlogQuery(params.blogSlug!);

    if (!blogRes && !isLoading) {
        return <div>Blog not found</div>;
    }

    function copyToClipboard(text) {
        if (navigator.clipboard) {
            return navigator.clipboard.writeText(text);
        }

        const input = document.createElement('input');
        input.value = text;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
    }

    const blog = blogRes?.blog;
    const shareUrl = `${window.config.generalSettings.rootUrl}/blogs/${blog?.slug}`;

    return (
        <>
            <section
                className="lg:h-[850px] flex flex-col justify-end text-white bg-cover bg-center"
                style={{
                    backgroundImage: `linear-gradient(to top, rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.1)), url(${blog?.banner})`,
                }}
            >
                <div className="container py-28">
                    <p className="text-sm mb-8">{blog?.blogCategoryName}</p>
                    <h1 className="text-2xl lg:text-4xl xl:text-5xl font-bold mb-8">
                        {blog?.title}
                    </h1>

                    <div className="mt-10 text-xs flex justify-between pb-6 border-b border-white/30">
                        <p>{dateFormatter(blog?.createdAt!)}</p>

                        <div className="flex gap-4">
                            <a
                                href={`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`}
                                target="_blank"
                                rel="noreferrer"
                                className="hover:text-primary transition-all"
                            >
                                <FaFacebookF />
                            </a>
                            <a
                                href={`https://twitter.com/intent/tweet?text=${shareUrl}`}
                                target="_blank"
                                rel="noreferrer"
                                className="hover:text-primary transition-all"
                            >
                                <FaTwitter />
                            </a>
                            <a
                                href={`http://www.linkedin.com/shareArticle?mini=true&url=${shareUrl}`}
                                target="_blank"
                                rel="noreferrer"
                                className="hover:text-primary transition-all"
                            >
                                <FaLinkedinIn />
                            </a>
                            <button
                                onClick={async () => {
                                    try {
                                        copyToClipboard(shareUrl);
                                        alert(
                                            translate(
                                                'Link copied to clipboard',
                                            ),
                                        );
                                    } catch {
                                        //
                                    }
                                }}
                                className="hover:text-primary transition-all"
                            >
                                <IoMdCopy className="text-md" />
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <div className="container py-5 hidden sm:flex items-center gap-4">
                <Link to="/" className="hover:text-primary">
                    Home
                </Link>
                <span className="h-1.5 w-1.5 bg-slate-600 rounded-full"></span>
                <Link
                    to={`/blogs?blogCategoryId=${blogRes?.blogCategories[0].id}`}
                    className="hover:text-primary"
                >
                    {blogRes?.blogCategories[0].name}
                </Link>
                <span className="h-1.5 w-1.5 bg-slate-600 rounded-full"></span>
                <p className="text-slate-500">{blog?.title.slice(0, 20)}...</p>
            </div>

            <section className="py-[50px]">
                <div className="container grid lg:grid-cols-4 gap-10">
                    <div className="lg:col-span-3">
                        <div
                            dangerouslySetInnerHTML={{
                                __html: blog?.description!,
                            }}
                            className="leading-relaxed"
                        ></div>

                        <p className="mt-10 mb-2">Share It:</p>

                        <div className="md:flex gap-3">
                            <a
                                href={`https://www.facebook.com/sharer/sharer.php?u=${shareUrl}`}
                                target="_blank"
                                rel="noreferrer"
                                className="flex  mb-2 md:mb-0 gap-3 items-center py-2 px-5 rounded-md transition-all bg-[#1877f2] text-white"
                            >
                                <span className="text-xl">
                                    <FaFacebookF />
                                </span>
                                <span>Facebook</span>
                            </a>
                            <a
                                href={`https://twitter.com/intent/tweet?text=${shareUrl}`}
                                target="_blank"
                                rel="noreferrer"
                                className="flex  mb-2 md:mb-0 gap-3 items-center py-2 px-5 rounded-md transition-all bg-[#000] text-white"
                            >
                                <span className="text-xl">
                                    <FaTwitter />
                                </span>
                                <span>Twitter</span>
                            </a>
                            <a
                                href={`http://www.linkedin.com/shareArticle?mini=true&url=${shareUrl}`}
                                target="_blank"
                                rel="noreferrer"
                                className="flex  mb-2 md:mb-0 gap-3 items-center py-2 px-5 rounded-md transition-all bg-[#0a66c2] text-white"
                            >
                                <span className="text-xl">
                                    <FaLinkedinIn />
                                </span>
                                <span>LinkedIn</span>
                            </a>
                            <button
                                onClick={async () => {
                                    try {
                                        copyToClipboard(shareUrl);
                                        alert(
                                            translate(
                                                'Link copied to clipboard',
                                            ),
                                        );
                                    } catch {
                                        //
                                    }
                                }}
                                className="flex w-full md:w-auto mb-2 md:mb-0 gap-3 items-center py-2 px-5 rounded-md transition-all bg-[#cec004] text-white"
                            >
                                <IoMdCopy className="text-md" />
                                <span>Copy Link</span>
                            </button>
                        </div>
                    </div>

                    <div>
                        <h4 className="mb-4 text-base pb-2 border-b">
                            Categories
                        </h4>
                        <div className="flex gap-2 flex-wrap">
                            {blogRes?.blogCategories.map((category) => (
                                <Link
                                    to={`/blogs?blogCategoryId=${category.id}`}
                                    key={category.id}
                                    className="py-1 px-3 text-sm text-muted-foreground border hover:border-primary hover:text-primary"
                                >
                                    {category.name}
                                </Link>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            <section className="py-20 bg-gray-100">
                <div className="container">
                    <h2 className="text-2xl lg:text-3xl xl:text-4xl mb-10"></h2>
                    <SectionTitle
                        subTitle={translate('You may also like')}
                        title={translate('[Related] Posts')}
                        classNames={{
                            title: 'ek-h3',
                        }}
                        className="mt-10 mb-8"
                    />

                    <div className="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-7">
                        {blogRes?.relatedBlogs.map((blog) => (
                            <BlogCard key={blog.id} blog={blog} />
                        ))}
                    </div>
                </div>
            </section>
        </>
    );
};

export default BlogDetails;
