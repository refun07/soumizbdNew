import { useState } from 'react';
import { AiOutlineMinus } from 'react-icons/ai';
import { BsPlusLg } from 'react-icons/bs';
import { useParams } from 'react-router-dom';
import { URLFilterParams } from '../../../types';
import { IBrandShort } from '../../../types/brand';
import { translate } from '../../../utils/translate';
import Checkbox from '../../inputs/Checkbox';

interface Props {
    brands: IBrandShort[];
    filters: URLFilterParams;
    onChange: (e: React.ChangeEvent<HTMLInputElement>) => void;
}

const BrandFilterCard = ({ brands, filters, onChange }: Props) => {
    const params = useParams<{ brandSlug: string }>();
    const [isAllBrandsShown, setShowAllBrands] = useState(false);

    if (!brands?.length) return null;

    return (
        <div
            className={`border border-primary-14 rounded-card mt-2.5 px-4 md:px-8 ${
                brands?.length > 8 ? 'py-3 md:py-5' : 'py-3 md:pt-5 md:pb-7'
            }`}
        >
            <h5 className="text-sm pb-2 border-b border-primary-14 text-zinc-900 uppercase">
                {translate('Brands')}
            </h5>

            <div className="mt-5 flex flex-col gap-3">
                {brands
                    ?.slice(0, isAllBrandsShown ? undefined : 5)
                    .map((item) => (
                        <Checkbox
                            name="brandIds"
                            value={item.id}
                            label={item.name}
                            key={item.id}
                            checked={
                                params.brandSlug === item.slug ||
                                filters.brandIds?.includes(String(item.id))
                            }
                            onChange={onChange}
                        />
                    ))}

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
        </div>
    );
};

export default BrandFilterCard;
