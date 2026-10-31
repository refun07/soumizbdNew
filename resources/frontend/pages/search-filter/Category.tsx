import { useParams } from 'react-router-dom';
import SearchAndFilter from '../../components/search-filter/SearchAndFilter';

const Category = () => {
    const params = useParams<{ categorySlug: string }>();

    return <SearchAndFilter key={`category-${params.categorySlug}`} categorySlug={params.categorySlug} />;
};

export default Category;
