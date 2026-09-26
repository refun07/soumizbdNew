import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { FaCheckCircle, FaRegCopy } from 'react-icons/fa';
import { useDispatch } from 'react-redux';
import { useNavigate, useParams, useSearchParams } from 'react-router-dom';
import Button from '../../components/buttons/Button';
import Input from '../../components/inputs/Input';
import {
    useGetBkashPersonalPaymentQuery,
    useSubmitBkashPersonalPaymentMutation,
} from '../../store/features/api/orderApi';
import { clearCheckoutData } from '../../store/features/checkout/checkoutSlice';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

const BkashPersonalPayment = () => {
    const { orderCode } = useParams<{ orderCode: string }>();
    const [searchParams] = useSearchParams();
    const navigate = useNavigate();
    const dispatch = useDispatch();

    const { data, isLoading, isError } = useGetBkashPersonalPaymentQuery(
        orderCode!,
    );
    const [submitPayment, { isLoading: isSubmitting }] =
        useSubmitBkashPersonalPaymentMutation();

    const [useOtherNumber, setUseOtherNumber] = useState(false);
    const [senderNumber, setSenderNumber] = useState('');
    const [trxId, setTrxId] = useState('');
    const [isEditing, setIsEditing] = useState(false);

    useEffect(() => {
        dispatch(clearCheckoutData());
    }, []);

    const goToSuccess = () => {
        // landing page orders use the server-rendered success page (it fires the purchase pixel)
        if (searchParams.get('source') === 'landing') {
            window.location.href = `/orders/success/${orderCode}`;
            return;
        }
        navigate(`/orders/success/${orderCode}`);
    };

    const copy = (value: string) => {
        navigator.clipboard?.writeText(value);
        toast.success(translate('Copied'));
    };

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        if (!data) return;

        const sendFromOther = useOtherNumber || !data.hasCheckoutPhone;
        if (sendFromOther && !/^(?:\+?88)?01[3-9]\d{8}$/.test(senderNumber.trim())) {
            toast.error(translate('Please enter a valid bKash number'));
            return;
        }
        if (!trxId.trim()) {
            toast.error(translate('Please enter the transaction ID'));
            return;
        }

        try {
            const res = await submitPayment({
                orderCode: orderCode!,
                useCheckoutPhone: !sendFromOther,
                senderNumber: sendFromOther ? senderNumber.trim() : undefined,
                trxId: trxId.trim(),
            }).unwrap();
            toast.success(res.message);
            goToSuccess();
        } catch (err: any) {
            toast.error(
                err?.data?.message || translate('Something went wrong'),
            );
        }
    };

    if (isLoading) {
        return (
            <div className="container py-16 text-center text-black/70">
                {translate('Loading...')}
            </div>
        );
    }

    if (isError || !data) {
        return (
            <div className="container py-16 text-center">
                <p className="text-black/70 mb-5">
                    {translate('No bKash payment found for this order.')}
                </p>
                <Button as="link" to="/" size="lg">
                    {translate('Back to Home')}
                </Button>
            </div>
        );
    }

    const submission = data.submission;
    const showForm =
        data.needsSubmission || (submission?.status === 'pending' && isEditing);
    const sendFromOther = useOtherNumber || !data.hasCheckoutPhone;

    return (
        <div className="container py-6 md:py-10">
            <div className="max-w-xl mx-auto rounded-card border border-zinc-100 overflow-hidden">
                <div className="bg-[#E2136E] text-white px-5 py-5 text-center">
                    <img
                        src="/images/payment/bkash_personal.png"
                        alt="bKash"
                        className="h-10 mx-auto mb-2 bg-white rounded px-2 py-1"
                    />
                    <h1 className="text-lg font-bold">
                        {translate('Pay with bKash (Send Money)')}
                    </h1>
                    <p className="text-sm text-white/90 mt-1">
                        {translate('Order Number')}: {data.orderCode}
                    </p>
                </div>

                <div className="p-5 sm:p-7 space-y-6">
                    <div className="grid grid-cols-2 gap-3">
                        <div className="rounded-card bg-stone-50 p-4 text-center">
                            <p className="text-xs uppercase text-black/60 mb-1">
                                {translate('Amount')}
                            </p>
                            <p className="text-xl font-bold text-[#E2136E]">
                                {currencyFormatter(data.amount)}
                            </p>
                        </div>
                        <div className="rounded-card bg-stone-50 p-4 text-center">
                            <p className="text-xs uppercase text-black/60 mb-1">
                                {translate('bKash Personal Number')}
                            </p>
                            {data.receiverNumber ? (
                                <button
                                    type="button"
                                    onClick={() => copy(data.receiverNumber)}
                                    className="text-xl font-bold inline-flex items-center gap-2"
                                >
                                    {data.receiverNumber}
                                    <FaRegCopy className="text-sm text-black/50" />
                                </button>
                            ) : (
                                <p className="text-sm text-black/60">
                                    {translate('Not configured')}
                                </p>
                            )}
                        </div>
                    </div>

                    <div>
                        <h3 className="font-semibold mb-2">
                            {translate('How to pay')}
                        </h3>
                        {data.instructions ? (
                            <p className="text-sm text-black/80 whitespace-pre-line">
                                {data.instructions}
                            </p>
                        ) : (
                            <ol className="list-decimal pl-5 space-y-1 text-sm text-black/80">
                                <li>{translate('Open the bKash app or dial *247#')}</li>
                                <li>{translate('Choose "Send Money"')}</li>
                                <li>
                                    {translate('Enter the number')}{' '}
                                    <strong>{data.receiverNumber}</strong>
                                </li>
                                <li>
                                    {translate('Enter the amount')}{' '}
                                    <strong>{currencyFormatter(data.amount)}</strong>
                                </li>
                                <li>{translate('Confirm with your bKash PIN')}</li>
                                <li>
                                    {translate(
                                        'Copy the Transaction ID (TrxID) from the confirmation message and submit it below',
                                    )}
                                </li>
                            </ol>
                        )}
                    </div>

                    {submission && !showForm && (
                        <div className="rounded-card border border-zinc-100 p-4 space-y-2">
                            <div className="flex items-center gap-2 font-semibold">
                                <FaCheckCircle
                                    className={
                                        submission.status === 'verified'
                                            ? 'text-teal-600'
                                            : 'text-amber-500'
                                    }
                                />
                                {submission.status === 'verified'
                                    ? translate('Payment verified')
                                    : translate('Payment submitted, awaiting verification')}
                            </div>
                            <p className="text-sm text-black/70">
                                {translate('TrxID')}: <strong>{submission.trxId}</strong>
                                {' · '}
                                {translate('Sender')}: {submission.senderNumber}
                            </p>
                            {submission.status === 'pending' && (
                                <button
                                    type="button"
                                    className="text-sm text-secondary underline"
                                    onClick={() => setIsEditing(true)}
                                >
                                    {translate('Entered something wrong? Update it')}
                                </button>
                            )}
                        </div>
                    )}

                    {submission?.status === 'rejected' && (
                        <div className="rounded-card bg-red-50 border border-red-100 p-4 text-sm text-red-700">
                            {translate(
                                'Your previous bKash payment could not be verified. Please check and submit again.',
                            )}
                            {submission.adminNote && (
                                <p className="mt-1">
                                    {translate('Reason')}: {submission.adminNote}
                                </p>
                            )}
                        </div>
                    )}

                    {showForm && (
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <p className="text-sm mb-2">
                                    {translate('Sending from')}
                                </p>
                                {data.hasCheckoutPhone && (
                                    <label className="flex items-center gap-2 text-sm mb-2 cursor-pointer">
                                        <input
                                            type="checkbox"
                                            checked={useOtherNumber}
                                            onChange={(e) =>
                                                setUseOtherNumber(e.target.checked)
                                            }
                                        />
                                        {translate(
                                            'I will send money from another bKash number',
                                        )}
                                    </label>
                                )}
                                {sendFromOther ? (
                                    <Input
                                        type="tel"
                                        inputMode="numeric"
                                        name="senderNumber"
                                        placeholder="01XXXXXXXXX"
                                        value={senderNumber}
                                        onChange={(e) =>
                                            setSenderNumber(e.target.value)
                                        }
                                        required
                                    />
                                ) : (
                                    <p className="rounded-card bg-stone-50 px-4 py-2.5 text-sm">
                                        {data.checkoutPhoneMasked}{' '}
                                        <span className="text-black/60">
                                            ({translate('your checkout phone number')})
                                        </span>
                                    </p>
                                )}
                            </div>

                            <div>
                                <label
                                    htmlFor="trxId"
                                    className="text-sm mb-2 block"
                                >
                                    {translate('Transaction ID (TrxID)')}
                                </label>
                                <Input
                                    id="trxId"
                                    name="trxId"
                                    placeholder="e.g. 9FG7H6J5K4"
                                    value={trxId}
                                    onChange={(e) =>
                                        setTrxId(
                                            e.target.value
                                                .toUpperCase()
                                                .replace(/[^A-Z0-9]/g, ''),
                                        )
                                    }
                                    maxLength={50}
                                    autoComplete="off"
                                    required
                                />
                            </div>

                            <Button
                                type="submit"
                                size="lg"
                                isLoading={isSubmitting}
                                disabled={isSubmitting}
                                className="w-full"
                            >
                                {translate('Submit Payment Information')}
                            </Button>
                        </form>
                    )}

                    <div className="text-center">
                        <button
                            type="button"
                            onClick={goToSuccess}
                            className="text-sm text-black/60 underline"
                        >
                            {showForm
                                ? translate('I will pay later')
                                : translate('Continue')}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default BkashPersonalPayment;
