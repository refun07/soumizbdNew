import { FaFacebookF, FaLinkedinIn, FaTwitter } from 'react-icons/fa6';
import { Link } from 'react-router-dom';
import { translate } from '../..//utils/translate';
import { IBlogShort } from '../../types/blog';
import { dateFormatter } from '../../utils/dateFormatter';

interface Props {
    blog: IBlogShort;
}

const BlogCard = ({ blog }: Props) => {
    const shareUrl = `${window.config.generalSettings.rootUrl}/blogs/${blog.slug}`;

    return (
        <div className="flex flex-col border rounded-md overflow-hidden group hover:shadow-lg transition-all ease-in-out">
            <Link to={`/blogs/${blog.slug}`} className="w-full relative">
                <img
                    src={blog.thumbnailImage}
                    alt={blog.title}
                    className="w-full aspect-[6/4] object-cover"
                />
                {blog.blogCategoryName != '' &&
                blog.blogCategoryName != null ? (
                    <div className="absolute bottom-[-10px] w-full">
                        <div className="flex items-center justify-center">
                            <span className="bg-primary px-3 py-1 text-white">
                                {blog.blogCategoryName}
                            </span>
                        </div>
                    </div>
                ) : (
                    ''
                )}
            </Link>

            <div className="py-8 px-4 text-center">
                <h4 className="text-lg mb-3 group-hover:text-primary transition-all">
                    <Link to={`/blogs/${blog.slug}`} className="line-clamp-2">
                        {blog.title}
                    </Link>
                </h4>

                <p>
                    {translate('Posted By:')} {blog.userName}
                </p>
                <div className="flex items-center justify-center gap-4 text-muted-foreground">
                    {dateFormatter(blog.createdAt, 'MMM DD, YYYY')}
                </div>

                <div className="mt-4 flex gap-4 text-muted-foreground justify-center">
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
                </div>
                <div className="mt-6">
                    <Link
                        to={`/blogs/${blog.slug}`}
                        className="text-primary font-semibold uppercase"
                    >
                        {translate('Continue Reading')}
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default BlogCard;
