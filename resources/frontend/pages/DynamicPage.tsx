import { useParams } from 'react-router-dom';
import SectionTitle from '../components/titles/SectionTitle';
import { useGetPageQuery } from '../store/features/api/pagesApi';

const DynamicPage = () => {
    const { slug } = useParams<{ slug: string }>();
    const { data: pageContent, isLoading } = useGetPageQuery(slug!);

    if (isLoading || !pageContent) {
        return <div className="container">Loading...</div>;
    }

    return (
        <div className="container">
            <SectionTitle title={pageContent.title} />

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
