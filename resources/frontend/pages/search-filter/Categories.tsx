import { Link } from 'react-router-dom';
import BotanicalCategoryGrid from '../../components/storefront/BotanicalCategoryGrid';
import '../../components/storefront/homepage-refinement.css';
import { useGetBotanicalCategoriesQuery } from '../../store/features/api/homeApis/botanicalApi';

export default function Categories() {
    const { data, isLoading, isError, refetch } = useGetBotanicalCategoriesQuery();
    return (
        <div className="bot-home bot-home--refined">
            <section className="bot-container bot-category-section">
                <div className="bot-section-heading">
                    <div><p className="bot-eyebrow">Find your everyday favourites</p><h1>All categories</h1></div>
                    <Link to="/product">All products</Link>
                </div>
                {isLoading && <p role="status">Loading categories…</p>}
                {isError && <p>Categories could not load. <button onClick={refetch}>Try again</button></p>}
                {data && <BotanicalCategoryGrid categories={data.categories} />}
            </section>
        </div>
    );
}
