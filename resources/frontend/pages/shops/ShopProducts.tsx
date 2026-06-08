import { useParams } from 'react-router-dom';
import SearchAndFilter from '../../components/search-filter/SearchAndFilter';

const ShopProducts = () => {
    const params = useParams<{ shopSlug: string }>();

    return (
        <>
            <SearchAndFilter breadcrumb={false} shopSlug={params.shopSlug} />
            <br />
            <br />
            <br />
        </>
    );
};

export default ShopProducts;
