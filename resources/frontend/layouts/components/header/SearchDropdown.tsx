import SearchForm from '../../../components/inputs/SearchForm';
import { cn } from '../../../utils/cn';

const SearchDropdown = () => {
    return (
        <div
            className={cn(
                'absolute top-[calc(100%+40px)] right-0 px-6 py-5 bg-white w-[580px] max-w-[calc(100vw-30px)] translate-y-3 opacity-0 invisible transition-all duration-300 pointer-events-none z-[2]',
                'group-focus-within:translate-y-0 group-focus-within:opacity-100 group-focus-within:visible group-focus-within:pointer-events-auto',
            )}
            tabIndex={0}
        >
            <SearchForm
                placeholder="Search for products, brands and categories..."
                searchOnType={false}
                submittableButton={false}
                classNames={{
                    suggestionWrapper:
                        'relative opacity-100 visible translate-y-0 mt-3 max-h-[calc(100vh-250px)] overflow-y-auto',
                }}
            />
        </div>
    );
};

export default SearchDropdown;
