import { useParams } from 'react-router-dom';
import SectionTitle from '../components/titles/SectionTitle';
import { useGetPageQuery } from '../store/features/api/pagesApi';
import NotFound from './NotFound';

const DynamicPage = () => {
    const { slug } = useParams<{ slug: string }>();
    const { data: pageContent, isLoading, error, isError, refetch } = useGetPageQuery(slug!);

    if (isLoading) {
        return <div className="container py-12" role="status">Loading page…</div>;
    }

    if (isError && !('status' in (error || {}) && (error as { status: number }).status === 404)) {
        return <div className="container py-12" role="alert">
            <h1 className="text-2xl font-semibold">Unable to load this page</h1>
            <button type="button" className="mt-4 min-h-11 rounded-full bg-[#087c3e] px-6 text-white" onClick={() => refetch()}>Try again</button>
        </div>;
    }

    if (!pageContent) return <NotFound />;

    return (
        <div className="container">
            <SectionTitle title={pageContent.title} headingLevel="h1" />

            <div
                className=""
                dangerouslySetInnerHTML={{
                    __html: pageContent.content,
                }}
            ></div>
        </div>
    );
};

export default DynamicPage;
