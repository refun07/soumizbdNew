<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Models\BkashPersonalPayment;
use App\Models\OrderGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BkashPersonalPaymentController extends Controller
{
    # payment instructions + current submission state for an order
    public function show($code)
    {
        $orderGroup = $this->findOrderGroup($code);
        if (! $orderGroup) {
            return $this->notFound();
        }

        return [
            'success' => true,
            'status' => 200,
            'message' => '',
            'result' => $this->payload($orderGroup),
        ];
    }

    # record the customer's bKash send-money details
    public function store(Request $request, $code)
    {
        $orderGroup = $this->findOrderGroup($code);
        if (! $orderGroup) {
            return $this->notFound();
        }

        $validator = Validator::make($request->all(), [
            'senderNumber' => 'nullable|string|max:20',
            'trxId' => 'required|string|max:50|regex:/^[A-Za-z0-9]+$/',
        ], [
            'trxId.regex' => translate('Transaction ID can contain only letters and numbers'),
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }

        $useCheckoutPhone = filter_var($request->useCheckoutPhone, FILTER_VALIDATE_BOOLEAN);
        $senderNumber = normalizePhoneToLocal($useCheckoutPhone ? (string) $orderGroup->phone : (string) $request->senderNumber);
        if (! preg_match('/^01[3-9][0-9]{8}$/', $senderNumber)) {
            return $this->error(translate('Please enter a valid bKash number'));
        }

        $trxId = strtoupper(trim($request->trxId));

        $latest = $orderGroup->latestBkashPersonalPayment;
        if ($latest && $latest->status === BkashPersonalPayment::STATUS_VERIFIED) {
            return $this->error(translate('Payment for this order is already verified'));
        }

        $usedElsewhere = BkashPersonalPayment::where('trx_id', $trxId)
            ->where('order_group_id', '!=', $orderGroup->id)
            ->where('status', '!=', BkashPersonalPayment::STATUS_REJECTED)
            ->exists();
        if ($usedElsewhere) {
            return $this->error(translate('This transaction ID has already been used for another order'));
        }

        $data = [
            'transaction_id' => $orderGroup->transaction_id,
            'sender_number' => $senderNumber,
            'trx_id' => $trxId,
            'amount' => $orderGroup->transaction?->total_amount ?? 0,
            'status' => BkashPersonalPayment::STATUS_PENDING,
        ];

        // a pending submission can still be corrected by the customer; after rejection keep history
        if ($latest && $latest->status === BkashPersonalPayment::STATUS_PENDING) {
            $latest->update($data);
        } else {
            $orderGroup->bkashPersonalPayments()->create($data);
        }

        $orderGroup->unsetRelation('latestBkashPersonalPayment');

        return [
            'success' => true,
            'status' => 200,
            'message' => translate('Your bKash payment information has been submitted. We will verify it shortly.'),
            'result' => $this->payload($orderGroup),
        ];
    }

    private function findOrderGroup($code): ?OrderGroup
    {
        $orderGroup = OrderGroup::with(['transaction', 'latestBkashPersonalPayment'])->where('code', $code)->first();

        return $orderGroup && $orderGroup->isBkashPersonal() ? $orderGroup : null;
    }

    private function payload(OrderGroup $orderGroup): array
    {
        $latest = $orderGroup->latestBkashPersonalPayment;
        $phone = normalizePhoneToLocal((string) $orderGroup->phone);

        return [
            'orderCode' => (string) $orderGroup->code,
            'amount' => (float) ($orderGroup->transaction?->total_amount ?? 0),
            'receiverNumber' => getSetting('bkash_personal_number', ''),
            'instructions' => getSetting('bkash_personal_instructions', ''),
            // only a masked hint is exposed; the full checkout phone stays server side
            'checkoutPhoneMasked' => $this->maskPhone($phone),
            'hasCheckoutPhone' => (bool) preg_match('/^01[3-9][0-9]{8}$/', $phone),
            'needsSubmission' => $orderGroup->needsBkashPersonalSubmission(),
            'submission' => $latest ? [
                'senderNumber' => $this->maskPhone($latest->sender_number),
                'trxId' => $latest->trx_id,
                'status' => $latest->status,
                'adminNote' => $latest->status === BkashPersonalPayment::STATUS_REJECTED ? $latest->admin_note : null,
                'submittedAt' => $latest->created_at?->format('d M, Y h:i A'),
            ] : null,
        ];
    }

    private function maskPhone(string $phone): string
    {
        if (strlen($phone) < 7) {
            return $phone;
        }

        return substr($phone, 0, 3).str_repeat('*', strlen($phone) - 6).substr($phone, -3);
    }

    private function error(string $message, int $status = 422)
    {
        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,
            'result' => null,
        ], $status);
    }

    private function notFound()
    {
        return $this->error(translate('Order not found with this code'), 404);
    }
}
