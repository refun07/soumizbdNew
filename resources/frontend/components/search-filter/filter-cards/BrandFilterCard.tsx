import { useState } from 'react';
import { AiOutlineMinus } from 'react-icons/ai';
import { BsPlusLg } from 'react-icons/bs';
import { FiChevronDown } from 'react-icons/fi';
import { useParams } from 'react-router-dom';
import { URLFilterParams } from '../../../types';
import { IBrandGroup, IBrandShort } from '../../../types/brand';
import { translate } from '../../../utils/translate';
import Checkbox from '../../inputs/Checkbox';

interface Props {
    brands: IBrandShort[];
    filters: URLFilterParams;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
    onFilter: (filters: URLFilterParams) => void;
}

interface BrandSection {
    group: IBrandGroup | null;
    brands: IBrandShort[];
}

// brands grouped by brand group, ungrouped brands last
const groupBrands = (brands: IBrandShort[]): BrandSection[] => {
    const sections = new Map<number, BrandSection>();
    const ungrouped: IBrandShort[] = [];

    brands.forEach((brand) => {
        const group = brand.brandGroup;
        if (!group) {
            ungrouped.push(brand);
            return;
        }
        if (!sections.has(group.id)) {
            sections.set(group.id, { group, brands: [] });
        }
        sections.get(group.id)!.brands.push(brand);
    });

    const result = [...sections.values()].sort(
        (a, b) =>
            (a.group!.sortOrder ?? 0) - (b.group!.sortOrder ?? 0) ||
            a.group!.name.localeCompare(b.group!.name)
    );
    if (ungrouped.length) result.push({ group: null, brands: ungrouped });

    return result;
};

const BrandFilterCard = ({ brands, filters, onChange, onFilter }: Props) => {
    const params = useParams<{ brandSlug: string }>();
    const [isAllBrandsShown, setShowAllBrands] = useState(false);
    const [openSections, setOpenSections] = useState<Record<string, boolean>>(
        {}
    );

    if (!brands?.length) return null;

    const selectedIds = filters.brandIds || [];
    const isBrandChecked = (brand: IBrandShort) =>
        params.brandSlug === brand.slug ||
        selectedIds.includes(String(brand.id));

    const sections = groupBrands(brands);
    const hasGroups = sections.some((section) => section.group);

    const renderBrand = (item: IBrandShort) => (
        <Checkbox
            name="brandIds"
            value={item.id}
            label={item.name}
            key={item.id}
            checked={isBrandChecked(item)}
            onChange={onChange}
        />
    );

    // select or clear every brand of a group at once
    const toggleGroup = (section: BrandSection, checked: boolean) => {
        const ids = section.brands.map((brand) => String(brand.id));
        const rest = selectedIds.filter((id) => !ids.includes(id));

        onFilter({
            ...filters,
            brandIds: checked ? [...rest, ...ids] : rest,
        });
    };

    return (
        <div
            className={`border border-primary-14 rounded-card mt-2.5 px-4 md:px-8 ${
                brands?.length > 8 ? 'py-3 md:py-5' : 'py-3 md:pt-5 md:pb-7'
            }`}
        >
            <h5 className="text-sm pb-2 border-b border-primary-14 text-zinc-900 uppercase">
                {translate('Brands')}
            </h5>

            {hasGroups ? (
                <div className="mt-4 flex flex-col divide-y divide-primary-14">
                    {sections.map((section) => {
                        const key = section.group
                            ? String(section.group.id)
                            : 'other';
                        const checkedCount =
                            section.brands.filter(isBrandChecked).length;
                        const isOpen = openSections[key] ?? checkedCount > 0;
                        const title = section.group
                            ? section.group.name
                            : translate('Other Brands');
                        const sectionLabel = (
                            <span className="font-medium text-zinc-900">
                                {title}{' '}
                                <span className="text-xs font-normal text-black/40">
                                    (
                                    {checkedCount > 0
                                        ? `${checkedCount}/${section.brands.length}`
                                        : section.brands.length}
                                    )
                                </span>
                            </span>
                        );

                        return (
                            <div key={key} className="py-2.5 first:pt-0">
                                <div className="flex items-start gap-2">
                                    <div className="grow min-w-0">
                                        {section.group ? (
                                            <Checkbox
                                                label={sectionLabel}
                                                checked={
                                                    checkedCount ===
                                                    section.brands.length
                                                }
                                                onChange={(e) =>
                                                    toggleGroup(
                                                        section,
                                                        e.target.checked
                                                    )
                                                }
                                            />
                                        ) : (
                                            <span className="text-sm">
                                                {sectionLabel}
                                            </span>
                                        )}
                                    </div>

                                    <button
                                        type="button"
                                        className="mt-0.5 shrink-0 p-0.5 text-black/50 hover:text-primary"
                                        aria-expanded={isOpen}
                                        aria-label={
                                            isOpen
                                                ? translate('Collapse')
                                                : translate('Expand')
                                        }
                                        onClick={() =>
                                            setOpenSections({
                                                ...openSections,
                                                [key]: !isOpen,
                                            })
                                        }
                                    >
                                        <FiChevronDown
                                            className={`transition ${
                                                isOpen ? 'rotate-180' : ''
                                            }`}
                                        />
                                    </button>
                                </div>

                                {isOpen && (
                                    <div className="mt-3 flex flex-col gap-3 pl-6">
                                        {section.brands.map(renderBrand)}
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>
            ) : (
                <div className="mt-5 flex flex-col gap-3">
                    {brands
                        ?.slice(0, isAllBrandsShown ? undefined : 5)
                        .map(renderBrand)}

                    {brands.length > 8 && (
                        <button
                            className="inline-flex items-center gap-1 text-sm font-bold text-primary !mt-4"
                            onClick={() => setShowAllBrands(!isAllBrandsShown)}
                        >
                            {isAllBrandsShown
                                ? translate('Show Less')
                                : translate('Show More')}
                            <span className="text-secondary">
                                {isAllBrandsShown ? (
                                    <AiOutlineMinus />
                                ) : (
                                    <BsPlusLg />
                                )}
                            </span>
                        </button>
                    )}
                </div>
            )}
        </div>
    );
};

export default BrandFilterCard;
