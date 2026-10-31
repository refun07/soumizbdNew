import { useParams } from 'react-router-dom';
import concerns from '../../../storefront/concerns.json';
import SearchAndFilter from '../../components/search-filter/SearchAndFilter';
import NotFound from '../NotFound';

export default function Concern() {
    const { concernSlug } = useParams<{ concernSlug: string }>();
    const concern = concerns.find((item) => item.slug === concernSlug);
    if (!concern) return <NotFound />;
    return <SearchAndFilter key={concern.slug} concernSlug={concern.slug} title={concern.label} />;
}
