import React, { useState } from 'react';
import toast from 'react-hot-toast';
import { debounce } from '../../../frontend/utils/debounce';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import Label from '../../react/components/inputs/Label';
import SelectInput from '../../react/components/inputs/SelectInput';
import { translate } from '../../react/utils/translate';
import { ICampaignProduct } from '../types';
import { removeCampaignProduct, updateCampaignProduct } from '../utils';

interface Props {
    campaignProduct: ICampaignProduct;
    onDelete: (id: number) => void;
}

const debouncedAction = debounce((fn: Function) => fn(), 600);

const CampaignProduct = ({ campaignProduct, onDelete }: Props) => {
    const [isDeleting, setIsDeleting] = useState(false);

    const [state, setState] = useState({
        id: campaignProduct.id,
        discountValue: campaignProduct.discountValue,
        discountType: campaignProduct.discountType,
    });

    const handleUpdateState = (key: string, value: any) => {
        const newState = {
            ...state,
            [key]: value,
        };
        setState(newState);

        // Update campaign product
        debouncedAction(async () => {
            try {
                await updateCampaignProduct(newState);
                toast.success(translate('Discount updated successfully'));
            } catch (err: any) {
                toast.error(err.message);
            }
        });
    };

    const handleDeleteProduct = async () => {
        try {
            setIsDeleting(true);
            await removeCampaignProduct(campaignProduct.id);
            onDelete(campaignProduct.id);

            toast.success(translate('Product removed successfully'));
        } catch (err: any) {
            toast.error(err.message);
        } finally {
            setIsDeleting(false);
        }
    };

    return (
        <tr>
            <td className="!pl-3 sm:!pl-10 !inline-flex items-center gap-2.5">
                <div className="inline-flex items-center gap-4">
                    <div className="">
                        <p className="max-w-[250px] text-justify">
                            {campaignProduct.product.name}
                        </p>

                        {campaignProduct.product.hasVariation ? (
                            <div className="flex flex-wrap gap-2 mt-2 text-[13px]">
                                <button key={campaignProduct.variation.id}>
                                    <Label className="bg-secondary-light">
                                        {campaignProduct.variation.name}
                                    </Label>
                                </button>
                            </div>
                        ) : null}
                    </div>
                </div>
            </td>
            <td>{currencyFormatter(campaignProduct.variation.basePrice)}</td>
            <td>
                <input
                    className="inline-block h-10 px-5 text-center border border-border rounded w-[105px] bg-background text-foreground"
                    name="discountValue"
                    value={state.discountValue}
                    onChange={(e) => {
                        handleUpdateState('discountValue', e.target.value);
                    }}
                />
            </td>

            <td>
                <div className="max-w-[140px]">
                    <SelectInput
                        name="discountType"
                        placeholder={translate('Discount Type')}
                        value={state.discountType}
                        options={[
                            {
                                label: translate('Flat'),
                                value: 'flat',
                            },
                            {
                                label: translate('Percentage'),
                                value: 'percentage',
                            },
                        ]}
                        classNames={{
                            control: () => 'py-2 min-w-[120px] h-10',
                            singleValue: () => '!text-secondary',
                        }}
                        onChange={(option) => {
                            handleUpdateState('discountType', option.value);
                        }}
                    />
                </div>
            </td>

            <td>
                <button
                    className="text-lg text-red-500 disabled:opacity-50"
                    disabled={isDeleting}
                    onClick={handleDeleteProduct}
                >
                    <i className="fas fa-trash-can"></i>
                </button>
            </td>
        </tr>
    );
};

export default CampaignProduct;
