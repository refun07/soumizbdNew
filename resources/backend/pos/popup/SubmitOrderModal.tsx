import { useFormik } from 'formik';
import { useEffect, useState, useCallback } from 'react';
import toast from 'react-hot-toast';
import { LiaTimesSolid } from 'react-icons/lia';
import { FiPlus, FiTrash2 } from 'react-icons/fi';
import { number, object, array } from 'yup';
import { Drawer } from 'vaul';
import {
    currencyFormatter,
    paddedNumber,
} from '../../../frontend/utils/numberFormatter';
import Image from '../../react/components/Image';
import Button from '../../react/components/inputs/Button';
import Input from '../../react/components/inputs/Input';
import SelectInput from '../../react/components/inputs/SelectInput';
import Textarea from '../../react/components/inputs/Textarea';
import { objectToFormData } from '../../react/utils/ObjectFormData';
import { translate } from '../../react/utils/translate';
import { IAddress, ICustomer, IPosCartGroup, IPaymentSplit, InvoiceData } from '../types';
import { submitOrder } from '../utils/actions';
import ModalWrapper from './ModalWrapper';

interface Props {
    discount: number;
    shippingCharge: number;
    posCartGroup?: IPosCartGroup;
    stockOverride?: boolean;

    shippingAddress?: IAddress;
    customer?: ICustomer;

    onSuccess: (invoiceData?: InvoiceData) => void;
    isMobile?: boolean;
}

interface PaymentSplitRow {
    id: number;
    method: 'cash' | 'card' | 'cash_on_delivery' | 'card_on_delivery';
    amount: number | '';
}

interface FormValues {
    posCartGroupId: number | null;
    discount: number;
    shippingCharge: number;

    shippingAddressId?: number;
    customerId?: number;
    memo_date?: string;
    memo_no?: string;
    mushak_challan_no?: string;
    note?: string;
    source?: string;

    payingAmount: number;
    paymentSplits: PaymentSplitRow[];
}

const paymentMethods = [
    { label: 'Cash', value: 'cash' },
    { label: 'Card', value: 'card' },
    { label: 'Cash on Delivery', value: 'cash_on_delivery' },
    { label: 'Card on Delivery', value: 'card_on_delivery' },
];

const sourceOptions = [
    { label: 'Facebook', value: 'Facebook' },
    { label: 'Phone Call', value: 'Phone Call' },
    { label: 'Google ads', value: 'Google ads' },
    { label: 'Offline', value: 'Offline' },
    { label: 'Others', value: 'Others' },
];

const MAX_PAYMENT_METHODS = 3;

const isCodMethod = (method: string): boolean =>
    method === 'cash_on_delivery' || method === 'card_on_delivery';

const getInitialSplit = (id: number): PaymentSplitRow => ({
    id,
    method: 'cash',
    amount: '',
});

const SubmitOrderModal = ({
    discount,
    posCartGroup,
    shippingCharge,
    stockOverride = false,
    customer,
    shippingAddress,
    onSuccess,
    isMobile = false,
}: Props) => {
    const [isActive, setIsActive] = useState(false);

    const handleClose = () => setIsActive(false);

    const calculatePayingAmount = useCallback(() => {
        if (!posCartGroup) return 0;
        return (
            posCartGroup.posCarts.reduce(
                (total, cart) =>
                    total + cart.qty * cart.variation.discountedBasePrice,
                0,
            ) -
            discount +
            shippingCharge
        );
    }, [posCartGroup, discount, shippingCharge]);

    const calculateSplitTotals = (splits: PaymentSplitRow[]) => {
        const nonCodTotalPaid = splits.reduce((sum, split) => {
            if (isCodMethod(split.method)) return sum;
            const amount = typeof split.amount === 'number' ? split.amount : 0;
            return sum + amount;
        }, 0);
        const hasCod = splits.some((s) => isCodMethod(s.method));
        const payingAmount = calculatePayingAmount();
        const codCovers = hasCod ? Math.max(0, payingAmount - nonCodTotalPaid) : 0;
        const totalPaid = nonCodTotalPaid + codCovers;
        const remaining = payingAmount - nonCodTotalPaid;
        const changeReturn = nonCodTotalPaid > payingAmount ? nonCodTotalPaid - payingAmount : 0;
        return { nonCodTotalPaid, codCovers, totalPaid, remaining, changeReturn };
    };

    const initialValues: FormValues = {
        posCartGroupId: null,
        discount: 0,
        shippingCharge: 0,
        memo_date: new Date().toISOString().split('T')[0],
        memo_no: '',
        mushak_challan_no: '',
        note: '',
        source: 'Others',
        payingAmount: 0,
        paymentSplits: [getInitialSplit(1)],
    };

    const validationSchema = object().shape({
        posCartGroupId: number().required('Cart is required'),
        payingAmount: number().required('Paying Amount is required'),
        paymentSplits: array()
            .min(1, 'At least one payment method is required')
            .max(MAX_PAYMENT_METHODS, `Maximum ${MAX_PAYMENT_METHODS} payment methods allowed`)
            .test('valid-splits', 'All payment splits must be valid', (splits) => {
                if (!Array.isArray(splits)) return false;
                return splits.every((split) => {
                    if (!split.method) return false;
                    if (isCodMethod(split.method)) return true;
                    return typeof split.amount === 'number' && split.amount >= 0;
                });
            })
            .test('sufficient-payment', 'Total payment must cover the bill amount', function (splits) {
                const hasCod = (splits as PaymentSplitRow[]).some((s) => isCodMethod(s.method));
                if (hasCod) return true;
                const { payingAmount } = this.parent;
                const totalPaid = (splits as PaymentSplitRow[]).reduce((sum, split) => {
                    const amount = typeof split.amount === 'number' ? split.amount : 0;
                    return sum + amount;
                }, 0);
                return totalPaid >= payingAmount;
            }),
    });

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values, helpers) => {
            try {
                const payingAmount = calculatePayingAmount();

                // Filter out any empty/invalid splits and prepare payment data
                const nonCodTotalPaid = values.paymentSplits
                    .filter((s) => !isCodMethod(s.method))
                    .reduce((sum, s) => sum + (typeof s.amount === 'number' ? s.amount : 0), 0);
                const codRemaining = Math.max(0, payingAmount - nonCodTotalPaid);

                const validSplits = values.paymentSplits
                    .filter((split) => {
                        if (isCodMethod(split.method)) return true;
                        return typeof split.amount === 'number' && split.amount > 0;
                    })
                    .map((split, index) => ({
                        id: index + 1,
                        method: split.method,
                        amount: isCodMethod(split.method)
                            ? codRemaining
                            : typeof split.amount === 'number'
                                ? split.amount
                                : 0,
                    }));

                if (validSplits.length === 0) {
                    toast.error('Please enter at least one valid payment amount');
                    return;
                }

                const paymentData: Record<string, unknown> = {
                    posCartGroupId: values.posCartGroupId,
                    discount: values.discount,
                    shippingCharge: values.shippingCharge,
                    shippingAddressId: values.shippingAddressId,
                    customerId: values.customerId,
                    memo_date: values.memo_date,
                    memo_no: values.memo_no,
                    mushak_challan_no: values.mushak_challan_no,
                    note: values.note,
                    source: values.source,
                    overrideStock: stockOverride ? 1 : 0,
                    paymentSplits: validSplits,
                };

                console.log('Submitting order...', paymentData);
                const response = await submitOrder(objectToFormData(paymentData));
                console.log('Order submitted, response:', response);
                toast.success('Order submitted successfully');
                helpers.resetForm();
                onSuccess(response);
                handleClose();
            } catch (error) {
                console.error('Order submission error:', error);
                toast.error(
                    error instanceof Error
                        ? error.message
                        : 'Failed to submit order',
                );
            }
        },
    });

    useEffect(() => {
        if (!posCartGroup) return;

        const payingAmount = calculatePayingAmount();

        formik.setValues({
            ...formik.values,
            posCartGroupId: posCartGroup.posCartGroupId,
            discount,
            shippingCharge,
            customerId: customer?.id,
            shippingAddressId: shippingAddress?.id,
            payingAmount,
            // Reset payment splits with initial split amount set to paying amount
            paymentSplits: [
                {
                    id: 1,
                    method: 'cash',
                    amount: payingAmount,
                },
            ],
        });
    }, [posCartGroup, customer, discount, shippingCharge, shippingAddress]);

    const addPaymentMethod = () => {
        const currentSplits = formik.values.paymentSplits;
        if (currentSplits.length >= MAX_PAYMENT_METHODS) {
            toast.error(`Maximum ${MAX_PAYMENT_METHODS} payment methods allowed`);
            return;
        }

        const newId = Math.max(...currentSplits.map((s) => s.id), 0) + 1;
        formik.setFieldValue('paymentSplits', [
            ...currentSplits,
            getInitialSplit(newId),
        ]);
    };

    const removePaymentMethod = (id: number) => {
        const currentSplits = formik.values.paymentSplits;
        if (currentSplits.length <= 1) {
            toast.error('At least one payment method is required');
            return;
        }

        formik.setFieldValue(
            'paymentSplits',
            currentSplits.filter((split) => split.id !== id),
        );
    };

    const updateSplitMethod = (id: number, method: PaymentSplitRow['method']) => {
        const currentSplits = formik.values.paymentSplits;
        formik.setFieldValue(
            'paymentSplits',
            currentSplits.map((split) =>
                split.id === id ? { ...split, method } : split,
            ),
        );
    };

    const updateSplitAmount = (id: number, value: string) => {
        const currentSplits = formik.values.paymentSplits;
        const amount = value === '' ? '' : Number(value);

        formik.setFieldValue(
            'paymentSplits',
            currentSplits.map((split) =>
                split.id === id ? { ...split, amount } : split,
            ),
        );
    };

    const { nonCodTotalPaid, codCovers, totalPaid, remaining, changeReturn } = calculateSplitTotals(
        formik.values.paymentSplits,
    );
    const payingAmount = calculatePayingAmount();
    const hasCodMethod = formik.values.paymentSplits.some((s) => isCodMethod(s.method));
    const isValidPayment = hasCodMethod || nonCodTotalPaid >= payingAmount;

    const formContent = (
        <form
            onSubmit={formik.handleSubmit}
            className="flex max-h-[inherit] flex-col"
        >
            <div className="theme-modal__header sticky top-0 z-[1] px-3 py-3 sm:px-5">
                <h5>{translate('Create Payment')}</h5>

                <button
                    type="button"
                    className="text-xl text-white sm:text-secondary-light"
                    onClick={handleClose}
                >
                    <LiaTimesSolid />
                </button>
            </div>

            <div className="theme-modal__body space-y-3 overflow-y-auto px-3 py-3 sm:px-5">
                {customer ? (
                    <h3 className="mb-5 text-center text-xl font-medium sm:text-2xl">
                        {customer.name}
                    </h3>
                ) : null}

                <div className="grid lg:grid-cols-2 gap-8">
                    <div className="max-lg:order-2 space-y-3">
                        {/* Total Amount Display */}
                        <div className="flex flex-col gap-2 sm:flex-row sm:gap-5">
                            <label className="text-sm font-medium sm:min-w-[150px] sm:py-2">
                                {translate('Total Amount')}
                            </label>
                            <div className="grow rounded-md border border-border bg-background-primary-light px-3 py-2 text-right font-semibold text-foreground">
                                {currencyFormatter(payingAmount)}
                            </div>
                        </div>

                        {/* Payment Methods Section */}
                        <div className="space-y-2">
                            <div className="flex items-center justify-between">
                                <label className="text-sm font-medium">
                                    {translate('Payment Methods')}
                                    <span className="text-theme-alert ml-1">*</span>
                                </label>
                                <Button
                                    type="button"
                                    variant="light"
                                    onClick={addPaymentMethod}
                                    disabled={
                                        formik.values.paymentSplits.length >=
                                        MAX_PAYMENT_METHODS
                                    }
                                    className="flex items-center gap-1 px-2 py-1 text-xs"
                                >
                                    <FiPlus /> {translate('Add Method')}
                                </Button>
                            </div>

                            {formik.values.paymentSplits.map(
                                (split, index) => (
                                    <div
                                        key={split.id}
                                        className="flex items-start gap-2 rounded-md border border-border p-2"
                                    >
                                        <div className="flex-1">
                                            <SelectInput
                                                value={split.method}
                                                options={paymentMethods}
                                                onChange={(option) =>
                                                    updateSplitMethod(
                                                        split.id,
                                                        option.value as PaymentSplitRow['method'],
                                                    )
                                                }
                                                groupClassName="mb-2"
                                            />
                                            {!isCodMethod(split.method) && (
                                                <Input
                                                    type="number"
                                                    value={
                                                        split.amount === ''
                                                            ? ''
                                                            : split.amount
                                                    }
                                                    onChange={(event) =>
                                                        updateSplitAmount(
                                                            split.id,
                                                            event.target.value,
                                                        )
                                                    }
                                                    placeholder={translate(
                                                        'Amount',
                                                    )}
                                                    classNames={{
                                                        inputWrapper:
                                                            'max-w-none',
                                                    }}
                                                />
                                            )}
                                        </div>
                                        <button
                                            type="button"
                                            onClick={() =>
                                                removePaymentMethod(split.id)
                                            }
                                            className="rounded-md p-2 text-theme-alert hover:bg-red-50 disabled:opacity-30 disabled:cursor-not-allowed"
                                            title={translate('Remove')}
                                            disabled={formik.values.paymentSplits.length === 1}
                                        >
                                            <FiTrash2 />
                                        </button>
                                    </div>
                                ),
                            )}
                        </div>

                        {/* Payment Summary */}
                        <div className="rounded-md border border-border bg-background-primary-light p-3 space-y-2 text-foreground">
                            <div className="flex justify-between text-sm">
                                <span>{translate('Total Paid:')}</span>
                                <span
                                    className={`font-medium ${
                                        isValidPayment
                                            ? 'text-green-600 dark:text-green-400'
                                            : 'text-theme-alert'
                                    }`}
                                >
                                    {currencyFormatter(nonCodTotalPaid)}
                                </span>
                            </div>
                            {codCovers > 0 && (
                                <div className="flex justify-between text-sm">
                                    <span>{translate('COD:')}</span>
                                    <span className="font-medium text-blue-600 dark:text-blue-400">
                                        {currencyFormatter(codCovers)}
                                    </span>
                                </div>
                            )}
                            <div className="flex justify-between text-sm">
                                <span>{translate('Remaining:')}</span>
                                <span
                                    className={`font-medium ${
                                        remaining <= 0
                                            ? 'text-green-600 dark:text-green-400'
                                            : hasCodMethod
                                                ? 'text-blue-600 dark:text-blue-400'
                                                : 'text-theme-alert'
                                    }`}
                                >
                                    {remaining > 0
                                        ? currencyFormatter(remaining)
                                        : translate('Paid')}
                                </span>
                            </div>
                            {changeReturn > 0 && (
                                <div className="flex justify-between text-sm">
                                    <span>{translate('Change Return:')}</span>
                                    <span className="font-medium text-green-600 dark:text-green-400">
                                        {currencyFormatter(changeReturn)}
                                    </span>
                                </div>
                            )}
                        </div>

                        {formik.errors.paymentSplits &&
                            typeof formik.errors.paymentSplits ===
                                'string' && (
                                <div className="text-sm text-theme-alert">
                                    {formik.errors.paymentSplits}
                                </div>
                            )}

                        {/* Common Fields */}
                        {window.config?.posMemoDateEnabled && (
                        <div className="flex flex-col gap-2 sm:flex-row sm:gap-5">
                            <label className="text-sm font-medium sm:min-w-[150px] sm:py-2">
                                {translate('Memo Date')}
                            </label>
                            <Input
                                {...formik.getFieldProps('memo_date')}
                                type="date"
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.memo_date}
                                error={formik.errors.memo_date}
                            />
                        </div>
                        )}
                        {window.config?.posMemoNoEnabled && (
                        <div className="flex flex-col gap-2 sm:flex-row sm:gap-5">
                            <label className="text-sm font-medium sm:min-w-[150px] sm:py-2">
                                {translate('Memo No')}
                            </label>
                            <Input
                                {...formik.getFieldProps('memo_no')}
                                type="text"
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.memo_no}
                                error={formik.errors.memo_no}
                                placeholder="Type Memo No"
                            />
                        </div>
                        )}
                        {window.config?.posMushakChallanNoEnabled && (
                        <div className="flex flex-col gap-2 sm:flex-row sm:gap-5">
                            <label className="text-sm font-medium sm:min-w-[150px] sm:py-2">
                                {translate('Mushak Challan No')}
                            </label>
                            <Input
                                {...formik.getFieldProps('mushak_challan_no')}
                                type="text"
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.mushak_challan_no}
                                error={formik.errors.mushak_challan_no}
                                placeholder="Type Mushak Challan No"
                            />
                        </div>
                        )}
                        <div className="flex flex-col gap-2 sm:flex-row sm:gap-5">
                            <label className="text-sm font-medium sm:min-w-[150px] sm:py-2">
                                {translate('Source')}
                            </label>
                            <SelectInput
                                value={formik.values.source}
                                options={sourceOptions}
                                onChange={(option) =>
                                    formik.setFieldValue('source', option.value)
                                }
                                groupClassName="grow"
                                placeholder="Select Source"
                            />
                        </div>

                        <div className="flex flex-col gap-2 sm:flex-row sm:gap-5">
                            <label className="text-sm font-medium sm:min-w-[150px] sm:py-2">
                                {translate('Note')}
                            </label>
                            <Textarea
                                {...formik.getFieldProps('note')}
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                rows={4}
                                placeholder={translate('Note')}
                            />
                        </div>
                    </div>

                    <div className="max-lg:order-1">
                        <h5 className="mb-3 text-lg">
                            {translate('Products')} (
                            {paddedNumber(posCartGroup?.posCarts.length)})
                        </h5>
                        <div className="max-h-[280px] space-y-2 overflow-y-auto pr-1">
                            {posCartGroup?.posCarts.map((cart) => (
                                <div
                                    key={cart.id}
                                    className="mb-2 flex gap-3"
                                >
                                    <div>
                                        <Image
                                            src={cart.product.thumbnailImg}
                                            alt={cart.product.name}
                                            width={50}
                                            height={50}
                                            className="min-w-[50px]"
                                        />
                                    </div>
                                    <div>
                                        <span className="line-clamp-2">
                                            {cart.product.name}
                                        </span>
                                        <p className="text-muted">
                                            {currencyFormatter(
                                                cart.variation
                                                    .discountedBasePrice,
                                            )}
                                            <span className="ml-3">
                                                {cart.qty}x
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {shippingAddress ? (
                            <>
                                <h5 className="mt-6 mb-3 text-lg">
                                    {translate('Shipping Address')}
                                </h5>
                                <div className="rounded-md border border-border px-4 py-3 sm:px-5 sm:py-4">
                                    <div>
                                        <p>{shippingAddress.fullAddress}</p>
                                    </div>
                                </div>
                            </>
                        ) : null}
                    </div>
                </div>
            </div>
            <div className="theme-modal__footer sticky bottom-0 flex-col gap-2 px-3 py-3 sm:flex-row sm:justify-end sm:px-5">
                <Button
                    as="button"
                    type="button"
                    variant="light"
                    onClick={handleClose}
                    className="w-full sm:mr-3 sm:w-auto"
                >
                    {translate('Cancel')}
                </Button>
                <Button
                    variant="primary"
                    as="button"
                    type="submit"
                    className="w-full sm:w-auto"
                    disabled={formik.isSubmitting || !isValidPayment}
                >
                    {formik.isSubmitting
                        ? translate('Submitting...')
                        : translate('Submit')}
                </Button>
            </div>
        </form>
    );

    return (
        <>
            <Button
                variant="primary"
                onClick={() => setIsActive(true)}
                disabled={!posCartGroup}
            >
                {translate('Pay Now')}
            </Button>

            {isMobile ? (
                <Drawer.Root open={isActive} onOpenChange={(open) => !open && handleClose()}>
                    <Drawer.Portal>
                        <Drawer.Overlay className="fixed inset-0 z-40 bg-black/40" />
                        <Drawer.Content className="fixed bottom-0 left-0 right-0 z-50 flex max-h-[95vh] flex-col rounded-t-xl bg-white dark:bg-background-secondary outline-none">
                            <div className="mx-auto mt-3 h-1 w-12 shrink-0 rounded-full bg-gray-300 dark:bg-gray-600" />
                            {formContent}
                        </Drawer.Content>
                    </Drawer.Portal>
                </Drawer.Root>
            ) : (
                <ModalWrapper
                    isActive={isActive}
                    size="lg"
                    onClose={handleClose}
                    className="max-sm:w-[calc(100%-16px)] max-sm:max-h-[calc(100%-16px)]"
                >
                    {formContent}
                </ModalWrapper>
            )}
        </>
    );
};

export default SubmitOrderModal;
