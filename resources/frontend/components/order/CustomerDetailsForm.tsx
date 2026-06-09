import { IoArrowBack } from 'react-icons/io5';
import { Link } from 'react-router-dom';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { useAppDispatch } from '../../store/store';
import { translate } from '../../utils/translate';
import Input from '../inputs/Input';
import InputGroup from '../inputs/InputGroup';
import Label from '../inputs/Label';

const CustomerDetailsForm = () => {
    const dispatch = useAppDispatch();
    const { customerDetails } = useCheckoutData();

    const setCustomerDetails = (e: any) => {
        const { name, value } = e.target;

        dispatch(
            setCheckoutData({
                customerDetails: {
                    ...customerDetails,
                    [name]: value,
                },
            }),
        );
    };

    return (
        <div className="rounded-[4px] border border-slate-200 overflow-hidden">
            <div className="flex items-center justify-between gap-3 bg-slate-100 py-3 px-4 sm:px-5">
                <h3 className="text-sm font-bold text-slate-800 uppercase">
                    {translate('Customer Details')}
                </h3>
                <Link
                    to={
                        window.config.generalSettings.skipCart
                            ? '/products'
                            : '/cart'
                    }
                    className="inline-flex items-center gap-1.5 text-xs font-semibold text-primary transition-colors hover:text-secondary sm:text-sm"
                >
                    <IoArrowBack />
                    {window.config.generalSettings.skipCart
                        ? translate('Continue Shopping')
                        : translate('Back To Cart')}
                </Link>
            </div>

            <div className="p-4 sm:p-5 grid sm:grid-cols-2 gap-3">
                <InputGroup>
                    <Label className="!mb-1.5 !normal-case font-semibold text-slate-800">
                        {translate('name')}
                        <span className="text-red-400 text-xs ml-1">
                            {translate('*')}
                        </span>
                    </Label>
                    <Input
                        name="name"
                        type="text"
                        className="!rounded-[3px] !border-slate-200 !bg-slate-50 placeholder:!text-slate-400 focus:!border-primary"
                        placeholder={translate('Type Your Full Name')}
                        value={customerDetails?.name}
                        onChange={setCustomerDetails}
                    />
                </InputGroup>

                <InputGroup>
                    <Label className="!mb-1.5 !normal-case font-semibold text-slate-800">
                        {translate('email')}
                    </Label>
                    <Input
                        name="email"
                        type="email"
                        className="!rounded-[3px] !border-slate-200 !bg-slate-50 placeholder:!text-slate-400 focus:!border-primary"
                        placeholder={translate('Type Your Email')}
                        value={customerDetails?.email}
                        onChange={setCustomerDetails}
                    />
                </InputGroup>

                <InputGroup>
                    <Label className="!mb-1.5 !normal-case font-semibold text-slate-800">
                        {translate('phone')}
                        <span className="text-red-400 text-xs ml-1">
                            {translate('*')}
                        </span>
                    </Label>
                    <Input
                        name="phone"
                        type="text"
                        className="!rounded-[3px] !border-slate-200 !bg-slate-50 placeholder:!text-slate-400 focus:!border-primary"
                        placeholder={translate('Valid Phone No.')}
                        value={customerDetails?.phone}
                        onChange={setCustomerDetails}
                    />
                </InputGroup>

                {/* <InputGroup className="sm:col-span-2">
                    <Label>{translate('note')}</Label>
                    <Textarea
                        name="note"
                        placeholder={translate('Type Your Note')}
                        rows={4}
                        value={customerDetails?.note}
                        onChange={setCustomerDetails}
                    />
                </InputGroup> */}
            </div>
        </div>
    );
};

export default CustomerDetailsForm;
