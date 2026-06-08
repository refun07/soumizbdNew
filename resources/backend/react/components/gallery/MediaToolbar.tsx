import { FaSliders, FaXmark, FaMagnifyingGlass } from 'react-icons/fa6';
import type { MediaFilter } from '../../types/media';
import { translate } from '../../utils/translate';
import { cn } from '../../utils/cn';
import SelectInput from '../inputs/SelectInput';
import { useState } from 'react';

interface MediaToolbarProps {
    filter: MediaFilter;
    onFilterChange: (filter: Partial<MediaFilter>) => void;
}

const sortOptions = [
    { value: 'newest', label: translate('Newest to Oldest') },
    { value: 'oldest', label: translate('Oldest to Newest') },
    { value: 'smallest', label: translate('Smallest to Largest') },
    { value: 'largest', label: translate('Largest to Smallest') },
];

const MediaToolbar = ({ filter, onFilterChange }: MediaToolbarProps) => {
    const [isMobileFiltersOpen, setIsMobileFiltersOpen] = useState(false);

    return (
        <form className="media-manager__filters" id="media-manager__filters">
            {/* Mobile Filter Toggle */}
            <div className="md:hidden bg-primary text-white px-5 py-4 rounded-t-md flex justify-between items-center">
                <span className="font-medium">{translate('FILTER')}</span>
                <button
                    type="button"
                    onClick={() => setIsMobileFiltersOpen(!isMobileFiltersOpen)}
                    className="text-white"
                >
                    {isMobileFiltersOpen ? <FaXmark /> : <FaSliders />}
                </button>
            </div>

            {/* Filter Content */}
            <div
                className={cn(
                    "flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 px-5 py-4 border-b border-border",
                    isMobileFiltersOpen ? "block" : "hidden md:flex"
                )}
            >
                {/* Search Input */}
                <div className="grow lg:max-w-[250px] relative">
                    <input
                        type="text"
                        name="media_name"
                        value={filter.search}
                        onChange={(e) => onFilterChange({ search: e.target.value })}
                        placeholder={translate('Search by name')}
                        className="w-full h-10 px-4 pr-10 block border border-border rounded-md focus:border-secondary-light focus:outline-none bg-background text-foreground"
                    />
                    <span className="text-primary dark:text-muted absolute top-0 right-0 h-full flex items-center justify-center px-3 pointer-events-none">
                        <FaMagnifyingGlass />
                    </span>
                </div>

                {/* Sort Dropdown */}
                <div className="flex lg:justify-end max-lg:flex-col lg:items-center gap-4 2xl:gap-10">
                    <div className="min-w-[160px]">
                        <SelectInput
                            name="order"
                            label={translate('Order')}
                            options={sortOptions}
                            value={filter.sort}
                            onChange={(selected) => onFilterChange({ sort: selected?.value })}
                            isSearchable={false}
                        />
                    </div>

                    {/* Type Filter - Hidden for now */}
                    <div className="lg:max-w-[180px] hidden">
                        <SelectInput
                            name="file-type"
                            label={translate('Type')}
                            options={[{ value: 'all', label: translate('All') }]}
                            value="all"
                            onChange={() => {}}
                            isSearchable={false}
                        />
                    </div>
                </div>
            </div>
        </form>
    );
};

export default MediaToolbar;
