import { FormEvent, useEffect, useState } from 'react';
import { LuArrowRight } from 'react-icons/lu';
import { Link, useNavigate } from 'react-router-dom';
import { useLazyGetSearchSuggestionQuery } from '../../store/features/api/searchApi';
import { currencyFormatter } from '../../utils/numberFormatter';
import Image from '../Image';

export default function BotanicalSearch({ onClose }: { onClose: () => void }) {
    const [query, setQuery] = useState('');
    const [getSuggestions, result] = useLazyGetSearchSuggestionQuery();
    const navigate = useNavigate();
    const keyword = query.trim();
    const ready = keyword.length >= 2;
    const current = result.originalArgs === keyword;
    const products = current ? result.data?.products ?? [] : [];

    useEffect(() => {
        if (!ready) return;
        const timer = window.setTimeout(() => getSuggestions(keyword, true), 300);
        return () => window.clearTimeout(timer);
    }, [keyword, ready, getSuggestions]);

    function submit(event: FormEvent) {
        event.preventDefault();
        if (!keyword) return;
        navigate(`/product?query=${encodeURIComponent(keyword)}`);
        onClose();
    }

    return (
        <div className="bot-search-wrap" id="bot-search">
            <form className="bot-search" onSubmit={submit} role="search">
                <label htmlFor="bot-search-input" className="bot-sr-only">Search products</label>
                <input id="bot-search-input" autoFocus type="search" autoComplete="off"
                    value={query} onChange={(event) => setQuery(event.target.value)}
                    placeholder="Find your next favourite…" aria-controls={ready ? 'bot-search-results' : undefined}
                    aria-describedby={ready ? 'bot-search-status' : undefined} />
                <button aria-label="Submit search" type="submit"><LuArrowRight /></button>
            </form>
            {ready && (
                <div className="bot-search-results" id="bot-search-results" aria-label="Product search results">
                    <p className="bot-search-status" id="bot-search-status" role="status">
                        {!current || result.isFetching ? 'Searching products…'
                            : result.isError ? 'Search is unavailable. Please try again.'
                            : products.length ? 'Products' : `No products found for “${keyword}”.`}
                    </p>
                    {current && !result.isFetching && !result.isError && products.map((product) => {
                        const price = product.discountedBasePrice ?? product.basePrice;
                        const discount = Math.max(0, product.basePrice - price);
                        const index = product.name.toLowerCase().indexOf(keyword.toLowerCase());
                        return (
                            <Link className="bot-search-result" key={product.id}
                                to={`/product/${product.slug}`} onClick={onClose}>
                                <Image src={product.thumbnailImg} alt={product.name} />
                                <span>
                                    <span className="bot-search-result-name">
                                        {index < 0 ? product.name : <>{product.name.slice(0, index)}<strong>{product.name.slice(index, index + keyword.length)}</strong>{product.name.slice(index + keyword.length)}</>}
                                    </span>
                                    <span className="bot-search-result-price">{currencyFormatter(price)}
                                        {discount > 0 && <span>Discount: {currencyFormatter(discount)}</span>}
                                    </span>
                                </span>
                            </Link>
                        );
                    })}
                    {products.length > 0 && current && !result.isFetching && (
                        <Link className="bot-search-view-all" to={`/product?query=${encodeURIComponent(keyword)}`} onClick={onClose}>
                            View all results <LuArrowRight />
                        </Link>
                    )}
                </div>
            )}
        </div>
    );
}
