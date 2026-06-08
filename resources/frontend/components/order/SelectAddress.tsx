import { useState } from 'react';
import { setCheckoutData } from '../../store/features/checkout/checkoutSlice';
import { useAppDispatch } from '../../store/store';
import { translate } from '../../utils/translate';
import Checkbox from '../inputs/Checkbox';
import CheckoutAddresses from './CheckoutAddresses';

interface Props {
    // address?: IAddress;
    isSimpleAddressForm?: boolean;
    // onSuccess?: (address: IAddress) => void;
}
const SelectAddress = ({  isSimpleAddressForm }: Props) => {
    const dispatch = useAppDispatch();
    const [differentBillingAddress, setDifferentBillingAddress] =
        useState(false);

    return (
        <>
            {isSimpleAddressForm && (
                 <CheckoutAddresses
                    addressType="shipping"
                    title={translate('Shipping Address')}
                />
            )}


            <Checkbox
                name="differentBillingAddress"
                label={
                    <span className="text-sm">
                        {translate('Different Billing Address')}
                    </span>
                }
                className="mt-8"
                checked={differentBillingAddress}
                onChange={() => {
                    dispatch(setCheckoutData({ billingAddress: undefined }));
                    setDifferentBillingAddress(!differentBillingAddress);
                }}
            />

            {differentBillingAddress && (
                <CheckoutAddresses
                    addressType="billing"
                    title={translate('Billing Address')}
                />
            )}
        </>
    );
};

export default SelectAddress;
