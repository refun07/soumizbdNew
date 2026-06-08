<?php

namespace App\Services;

use App\Models\PurchaseOrderPayment;

class PurchasePaymentService
{
    # get data
    public static function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $payments = PurchaseOrderPayment::where('payable_id', $request->payable_id)->where('payable_type', $request->payable_type)->get();

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'payments'    => $payments,
            ],
        ];

        return $data;
    }

    # add new data
    public static function store($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {
            $mPayment     = $request->payable_type;
            $mPaymentData = $mPayment::where('id', $request->payable_id)->first();

            if ($request->paid_amount > $mPaymentData->due) {
                $data = [
                    'status'    => 403,
                    'message'   => translate('We could not process the payment.'),
                    'result'    => [],
                ];
                return $data;
            }

            $payment = new PurchaseOrderPayment;
            $payment->payable_id        = $request->payable_id;
            $payment->payable_type      = $request->payable_type;
            $payment->date              = strtotime($request->date);
            $payment->payment_method    = $request->payment_method;
            $payment->paid_amount       = $request->paid_amount;
            $returnAmount               = $request->paid_amount - $mPaymentData->due;
            if ($returnAmount > 0) {
                $payment->return_amount  = $returnAmount;
            }
            $payment->note    = $request->note;
            $payment->save();

            $payments   = $mPaymentData->payments;
            $totalPaid  = $payments->sum('paid_amount');


            if ($totalPaid >= $mPaymentData->due) {
                $mPaymentData->payment_status = 'paid';
            }

            $mPaymentData->paid = $totalPaid;
            $mPaymentData->due  = $mPaymentData->grand_total - $totalPaid;

            $mPaymentData->save();

            $data = [
                'status'    => 200,
                'message'   => translate('Payment has been added successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }
}
