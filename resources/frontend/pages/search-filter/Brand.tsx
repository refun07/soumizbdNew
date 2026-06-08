import { useParams } from 'react-router-dom';
import SearchAndFilter from '../../components/search-filter/SearchAndFilter';

const Brand = () => {
    const params = useParams<{ brandSlug: string }>();

    return <SearchAndFilter brandSlug={params.brandSlug} />;
};

export default Brand;
