import axios from 'axios';
import React, { useState } from 'react';
import { toast } from 'react-hot-toast';
import { currencyFormatter } from '../../../../frontend/utils/numberFormatter';

export interface BkashPersonalSubmission {
    id: number;
    senderNumber: string;
    trxId: string;
    amount: number;
    status: 'pending' | 'verified' | 'rejected';
    adminNote: string | null;
    verifiedBy: string | null;
    verifiedAt: string | null;
    submittedAt: string | null;
}

export interface BkashPersonalInfo {
    receiverNumber: string | null;
    expectedAmount: number;
    latest: BkashPersonalSubmission | null;
    history: BkashPersonalSubmission[];
}

interface Props {
    orderId: number;
    info: BkashPersonalInfo;
    onUpdated: (info: BkashPersonalInfo, paymentStatus: 'paid' | 'unpaid') => void;
}

const statusStyles: Record<string, string> = {
    pending: 'bg-amber-100 text-amber-700',
    verified: 'bg-green-100 text-green-700',
    rejected: 'bg-red-100 text-red-700',
};

const statusLabels: Record<string, string> = {
    pending: 'Awaiting verification',
    verified: 'Verified',
    rejected: 'Rejected',
};

const copy = (value: string) => {
    navigator.clipboard?.writeText(value);
    toast.success('Copied');
};

const BkashPersonalPaymentCard: React.FC<Props> = ({ orderId, info, onUpdated }) => {
    const [submitting, setSubmitting] = useState<'verify' | 'reject' | null>(null);
    const [showRejectForm, setShowRejectForm] = useState(false);
    const [rejectNote, setRejectNote] = useState('');

    const latest = info.latest;
    const amountMismatch = latest && Math.abs(latest.amount - info.expectedAmount) > 0.009;

    const submit = async (action: 'verify' | 'reject') => {
        if (action === 'verify' && !window.confirm('Mark this bKash payment as verified and the order as paid?')) {
            return;
        }

        setSubmitting(action);
        try {
            const response = await axios.post(`/admin/api/orders/${orderId}/bkash-personal/${action}`, {
                note: action === 'reject' ? rejectNote : undefined,
            });
            toast.success(response.data.message);
            setShowRejectForm(false);
            setRejectNote('');
            onUpdated(response.data.result, action === 'verify' ? 'paid' : 'unpaid');
        } catch (err: any) {
            toast.error(err?.response?.data?.message || 'Something went wrong');
        } finally {
            setSubmitting(null);
        }
    };

    return (
        <div className="card rounded-lg border border-border bg-card p-4 text-foreground shadow-sm">
            <div className="flex items-center justify-between mb-4 gap-2">
                <h2 className="font-semibold text-lg text-foreground flex items-center gap-2">
                    <img src="/images/payment/bkash_personal.png" alt="bKash" className="h-5 object-contain" />
                    bKash Personal Payment
                </h2>
                {latest ? (
                    <span className={`px-2.5 py-1 rounded text-xs font-semibold ${statusStyles[latest.status]}`}>
                        {statusLabels[latest.status]}
                    </span>
                ) : (
                    <span className="px-2.5 py-1 rounded text-xs font-semibold bg-gray-100 text-gray-700">
                        Not submitted
                    </span>
                )}
            </div>

            {!latest ? (
                <p className="text-sm text-muted">
                    The customer has not submitted a bKash transaction ID yet. Expected amount{' '}
                    <span className="font-semibold text-foreground">{currencyFormatter(info.expectedAmount)}</span>
                    {info.receiverNumber ? <> to <span className="font-semibold text-foreground">{info.receiverNumber}</span></> : null}.
                </p>
            ) : (
                <>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <div className="text-xs text-muted uppercase mb-1">Transaction ID</div>
                            <button
                                type="button"
                                onClick={() => copy(latest.trxId)}
                                className="font-mono font-semibold hover:text-sky-500"
                                title="Copy"
                            >
                                {latest.trxId} <i className="fa-regular fa-copy text-xs"></i>
                            </button>
                        </div>
                        <div>
                            <div className="text-xs text-muted uppercase mb-1">Sender Number</div>
                            <button
                                type="button"
                                onClick={() => copy(latest.senderNumber)}
                                className="font-medium hover:text-sky-500"
                                title="Copy"
                            >
                                {latest.senderNumber} <i className="fa-regular fa-copy text-xs"></i>
                            </button>
                        </div>
                        <div>
                            <div className="text-xs text-muted uppercase mb-1">Expected Amount</div>
                            <div className="font-medium">{currencyFormatter(latest.amount)}</div>
                            {amountMismatch && (
                                <div className="text-xs text-amber-600">
                                    Order total is now {currencyFormatter(info.expectedAmount)}
                                </div>
                            )}
                        </div>
                        <div>
                            <div className="text-xs text-muted uppercase mb-1">Submitted</div>
                            <div className="font-medium">{latest.submittedAt}</div>
                        </div>
                    </div>

                    {info.receiverNumber && (
                        <div className="mt-3 text-sm text-muted">
                            Check the bKash app for number <span className="font-semibold text-foreground">{info.receiverNumber}</span>: look for a Send Money of the expected amount with this TrxID.
                        </div>
                    )}

                    {latest.status !== 'pending' && (
                        <div className="mt-3 text-sm text-muted">
                            {latest.status === 'verified' ? 'Verified' : 'Rejected'}
                            {latest.verifiedBy ? ` by ${latest.verifiedBy}` : ''}
                            {latest.verifiedAt ? ` on ${latest.verifiedAt}` : ''}
                            {latest.adminNote ? ` · ${latest.adminNote}` : ''}
                        </div>
                    )}

                    {showRejectForm && (
                        <div className="mt-4 space-y-2">
                            <textarea
                                className="theme-input w-full"
                                rows={2}
                                placeholder="Reason (the customer will see this and can submit again)"
                                value={rejectNote}
                                onChange={(e) => setRejectNote(e.target.value)}
                            />
                        </div>
                    )}

                    <div className="mt-4 flex flex-wrap justify-end gap-2">
                        {latest.status === 'pending' && !showRejectForm && (
                            <button
                                type="button"
                                disabled={submitting !== null}
                                onClick={() => submit('verify')}
                                className="btn btn-sm !bg-green-600 !text-white flex items-center gap-1.5 disabled:opacity-60"
                            >
                                <i className="fa-solid fa-circle-check"></i>
                                {submitting === 'verify' ? 'Verifying...' : 'Verify & Mark Paid'}
                            </button>
                        )}
                        {latest.status !== 'rejected' && (
                            showRejectForm ? (
                                <>
                                    <button
                                        type="button"
                                        onClick={() => setShowRejectForm(false)}
                                        className="btn btn-sm btn-outline-primary"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        type="button"
                                        disabled={submitting !== null}
                                        onClick={() => submit('reject')}
                                        className="btn btn-sm !bg-red-600 !text-white disabled:opacity-60"
                                    >
                                        {submitting === 'reject' ? 'Rejecting...' : 'Confirm Reject'}
                                    </button>
                                </>
                            ) : (
                                <button
                                    type="button"
                                    onClick={() => setShowRejectForm(true)}
                                    className="btn btn-sm btn-outline-primary !text-red-600 flex items-center gap-1.5"
                                >
                                    <i className="fa-solid fa-circle-xmark"></i>
                                    Reject
                                </button>
                            )
                        )}
                    </div>
                </>
            )}

            {info.history.length > 0 && (
                <div className="mt-4 pt-4 border-t border-border">
                    <div className="text-xs text-muted uppercase mb-2">Previous Submissions</div>
                    <div className="space-y-1 text-sm">
                        {info.history.map((item) => (
                            <div key={item.id} className="flex flex-wrap items-center gap-x-3 gap-y-1">
                                <span className="font-mono">{item.trxId}</span>
                                <span className="text-muted">{item.senderNumber}</span>
                                <span className={`px-1.5 py-0.5 rounded text-[11px] font-semibold ${statusStyles[item.status]}`}>
                                    {statusLabels[item.status]}
                                </span>
                                <span className="text-muted text-xs">{item.submittedAt}</span>
                                {item.adminNote && <span className="text-muted text-xs">· {item.adminNote}</span>}
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
};

export default BkashPersonalPaymentCard;
