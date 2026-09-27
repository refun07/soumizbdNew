import { HTMLAttributes, useEffect, useRef, useState } from 'react';
import { AiOutlineClose } from 'react-icons/ai';
import { FaMagnifyingGlass } from 'react-icons/fa6';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { useLazyGetSearchSuggestionQuery } from '../../store/features/api/searchApi';
import { closePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { cn } from '../../utils/cn';
import { debounce } from '../../utils/debounce';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';
import Image from '../Image';

const highlightMatch = (suggestion: string, keyword: string) => {
    const index = suggestion.toLowerCase().indexOf(keyword.toLowerCase());

    if (index === -1) {
        return <span className="">{suggestion}</span>;
    }

    return (
        <span>
            {suggestion.substring(0, index)}
            <span className="font-semibold text-primary">
                {suggestion.substring(index, index + keyword.length)}
            </span>
            {suggestion.substring(index + keyword.length)}
        </span>
    );
};

const debouncedSearch = debounce((fn: Function) => fn(), 600);

const defaultSearchSettings = {
    productPriceColor: '#5D7261',
    productDiscountColor: '#EF4444',
};

interface Props extends Omit<HTMLAttributes<HTMLInputElement>, 'onSubmit'> {
    name?: string;
    path?: string;
    placeholder?: string;
    value?: string;
    suggestions?: boolean;
    submittableButton?: boolean;
    searchOnType?: boolean;
    searchPopupCloseBtn?: boolean;
    classNames?: {
        form?: string;
        input?: string;
        icon?: string;

        suggestionWrapper?: string;
        suggestionCategory?: string;
        suggestionList?: string;
        suggestionItem?: string;
    };
    onSubmit?: (e: React.FormEvent<HTMLFormElement>) => void;
}

const SearchForm = ({
    name = 'query',
    path = '/products',
    suggestions = true,
    searchOnType = true,
    submittableButton = false,
    autoFocus = false,
    searchPopupCloseBtn = false,
    classNames,
    onSubmit,
    ...rest
}: Props) => {
    const dispatch = useAppDispatch();
    const navigate = useNavigate();
    const inputEl = useRef<HTMLInputElement>(null);
    const [searchParams] = useSearchParams();
    const [showSuggestions, setShowSuggestions] = useState<boolean>(false);
    const [query, setQuery] = useState<string>(searchParams.get(name) || '');
    const [
        getSearchSuggestion,
        { data: suggestionRes, isFetching: fetchingSuggestions },
    ] = useLazyGetSearchSuggestionQuery();

    const suggestedProducts = suggestionRes?.products || [];
    const suggestedBrands = suggestionRes?.brands || [];
    const suggestedShops = suggestionRes?.shops || [];
    const searchSettings =
        window.config.searchSettings ?? defaultSearchSettings;
    const productPriceColor =
        searchSettings.productPriceColor ||
        defaultSearchSettings.productPriceColor;
    const productDiscountColor =
        searchSettings.productDiscountColor ||
        defaultSearchSettings.productDiscountColor;

    const nothingFound =
        !suggestedProducts.length &&
        !suggestedBrands.length &&
        !suggestedShops.length;

    // add event listener to open suggestions
    // when user starts typing
    // and cose suggestions when user clicks outside
    useEffect(() => {
        const handleClickOutside = (e: MouseEvent) => {
            if (
                inputEl.current &&
                !inputEl.current.contains(e.target as Node)
            ) {
                inputEl.current.blur();
                setShowSuggestions(false);
            }
        };

        const handleKeydown = async (e: KeyboardEvent) => {
            if (e.key === 'Escape') {
                inputEl.current?.blur();
                setShowSuggestions(false);
                // ctrl + k to focus on search input
            } else if (e.ctrlKey && e.key === 'k') {
                e.preventDefault();
                inputEl.current?.focus();
            } else if (e.target === inputEl.current && suggestions) {
                // if `suggestions` is enabled and
                // show suggestions when user starts typing

                setShowSuggestions(true);
            }
        };

        // set search key to state and navigate
        inputEl.current?.addEventListener('input', (e: any) => {
            const value = e.target.value;
            setQuery(value);

            debouncedNavigate(value);
        });

        inputEl.current?.addEventListener('focus', () => {
            if (suggestions) {
                setShowSuggestions(true);
            }
        });

        document.addEventListener('click', handleClickOutside);
        document.addEventListener('keydown', handleKeydown);

        return () => {
            document.removeEventListener('click', handleClickOutside);
            document.removeEventListener('keydown', handleKeydown);
            inputEl.current?.removeEventListener('input', () => {});
        };
    }, []);

    // auto focus the input when autoFocus is true
    useEffect(() => {
        if (autoFocus) {
            setTimeout(() => inputEl.current?.focus(), 100);
        }
    }, [autoFocus]);

    useEffect(() => {
        if (suggestions && showSuggestions) {
            debouncedSearch(() => {
                getSearchSuggestion(query, true);
            });
        }
    }, [query]);

    useEffect(() => {
        getSearchSuggestion(query, true);
    }, []);

    const handleSubmit = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        setShowSuggestions(false);
        if (query) {
            navigate(`${path}?${name}=${query}`);
        }
        onSubmit?.(e);
    };

    const debouncedNavigate = debounce((value: string) => {
        if (searchOnType) {
            navigate(`${path}?${name}=${value}`);
        }
    }, 600);

    return (
        <div className="relative">
            <div className="flex gap-3">
                <form
                    onSubmit={handleSubmit}
                    className={cn(
                        `search-form relative w-full grow`,
                        classNames?.form,
                    )}
                >
                    <input
                        type="text"
                        name={name}
                        className={cn(
                            'theme-input search-form__input border-secondary h-[45px]',
                            classNames?.input,
                        )}
                        autoComplete="off"
                        value={query}
                        ref={inputEl}
                        onChange={() => {}}
                        {...rest}
                    />
                    <button
                        type="submit"
                        className={cn(
                            'text-primary absolute top-0 right-0 h-full flex items-center justify-center px-4 pointer-events-none rounded-r-md',
                            {
                                'pointer-events-auto bg-primary text-white aspect-square':
                                    submittableButton,
                            },
                            classNames?.icon,
                        )}
                    >
                        <FaMagnifyingGlass />
                    </button>
                </form>
                {searchPopupCloseBtn ? (
                    <button
                        className="text-secondary text-xl"
                        onClick={() => dispatch(closePopup())}
                    >
                        <AiOutlineClose />
                    </button>
                ) : null}
            </div>

            <div
                className={cn(
                    `suggestions pb-2 flex flex-col text-sm absolute top-[calc(100%+10px)] left-0 right-0 bg-white shadow-theme [&>*]:px-5 transition-all`,
                    {
                        'opacity-0 invisible translate-y-3': !showSuggestions,
                    },
                    classNames?.suggestionWrapper,
                )}
            >
                {fetchingSuggestions ? (
                    <p className="py-10 text-center">
                        {translate('loading')} ...
                    </p>
                ) : nothingFound ? (
                    <p className="py-10 text-center">
                        {translate('Nothing found')}
                    </p>
                ) : (
                    <>
                        {suggestedProducts.length ? (
                            <>
                                <p
                                    className={cn(
                                        'py-2 bg-gray-200 text-sm font-bold',
                                        classNames?.suggestionCategory,
                                    )}
                                >
                                    {translate('products')}
                                </p>
                                <div
                                    className={cn(
                                        'py-1 divide-y divide-neutral-200',
                                        classNames?.suggestionList,
                                    )}
                                >
                                    {suggestedProducts.map((product) => {
                                        const productBasePrice =
                                            product.basePrice || 0;
                                        const productPrice =
                                            product.discountedBasePrice ??
                                            productBasePrice;
                                        const discountAmount = Math.max(
                                            productBasePrice - productPrice,
                                            0,
                                        );

                                        return (
                                            <Link
                                                to={`/products/${product.slug}`}
                                                className={cn(
                                                    'hover:text-primary focus:text-primary flex items-center gap-3 py-2',
                                                    classNames?.suggestionItem,
                                                )}
                                                key={product.id}
                                            >
                                                <Image
                                                    src={product.thumbnailImg}
                                                    alt={product.name}
                                                    className="w-10 aspect-square rounded-full object-cover shrink-0"
                                                />
                                                <span className="min-w-0 flex-1">
                                                    <span className="line-clamp-1 leading-snug">
                                                        {highlightMatch(
                                                            product.name,
                                                            query,
                                                        )}
                                                    </span>
                                                    <span className="mt-1 flex flex-wrap items-center gap-x-2 gap-y-0.5 text-xs leading-tight">
                                                        <span
                                                            className="font-semibold"
                                                            style={{
                                                                color: productPriceColor,
                                                            }}
                                                        >
                                                            {currencyFormatter(
                                                                productPrice,
                                                            )}
                                                        </span>
                                                        {discountAmount > 0 ? (
                                                            <span
                                                                className="font-medium"
                                                                style={{
                                                                    color: productDiscountColor,
                                                                }}
                                                            >
                                                                {translate(
                                                                    'Discount',
                                                                )}
                                                                :{' '}
                                                                {currencyFormatter(
                                                                    discountAmount,
                                                                )}
                                                            </span>
                                                        ) : null}
                                                    </span>
                                                </span>
                                            </Link>
                                        );
                                    })}
                                </div>
                            </>
                        ) : null}

                        {suggestedBrands.length ? (
                            <>
                                <p
                                    className={cn(
                                        'py-2 bg-gray-200 text-sm font-bold',
                                        classNames?.suggestionCategory,
                                    )}
                                >
                                    {translate('brands')}
                                </p>
                                <div
                                    className={cn(
                                        'py-1 divide-y divide-neutral-200',
                                        classNames?.suggestionList,
                                    )}
                                >
                                    {suggestedBrands.map((brand) => (
                                        <Link
                                            to={`/brands/${brand.slug}`}
                                            className={cn(
                                                'line-clamp-1 leading-loose hover:text-primary flex items-center gap-2 py-1.5',
                                                classNames?.suggestionItem,
                                            )}
                                            key={brand.id}
                                        >
                                            <Image
                                                src={brand.thumbnailImage}
                                                alt={brand.name}
                                                className="w-[30px] aspect-square rounded-full"
                                            />
                                            <span className="min-w-0 truncate">
                                                {highlightMatch(brand.name, query)}
                                            </span>
                                            {brand.brandGroup && (
                                                <span className="ml-auto shrink-0 rounded-full bg-gray-100 px-2 text-xs leading-5 text-black/50">
                                                    {highlightMatch(
                                                        brand.brandGroup.name,
                                                        query,
                                                    )}
                                                </span>
                                            )}
                                        </Link>
                                    ))}
                                </div>
                            </>
                        ) : null}

                        {suggestedShops.length &&
                        window.config.generalSettings.appMode ==
                            'multiVendor' ? (
                            <>
                                <p
                                    className={cn(
                                        'py-2 bg-gray-200 text-sm font-bold',
                                        classNames?.suggestionCategory,
                                    )}
                                >
                                    {translate('shops')}
                                </p>
                                <div
                                    className={cn(
                                        'py-1 divide-y divide-neutral-200',
                                        classNames?.suggestionList,
                                    )}
                                >
                                    {suggestedShops.map((shop) => (
                                        <Link
                                            to={`/shops/${shop.slug}`}
                                            className={cn(
                                                'line-clamp-1 leading-loose hover:text-primary flex items-center gap-2 py-1.5',
                                                classNames?.suggestionItem,
                                            )}
                                            key={shop.id}
                                        >
                                            <Image
                                                src={shop.logo}
                                                alt={shop.name}
                                                className="w-[30px] aspect-square rounded-full"
                                            />

                                            {highlightMatch(shop.name, query)}
                                        </Link>
                                    ))}
                                </div>
                            </>
                        ) : null}
                    </>
                )}
            </div>
        </div>
    );
};

export default SearchForm;
