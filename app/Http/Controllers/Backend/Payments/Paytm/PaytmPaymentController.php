<?php

namespace App\Http\Controllers\Backend\Payments\Paytm;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Payments\PaymentController;
use PaytmWallet;

class PaytmPaymentController extends Controller
{
    # paytm init payment
    public function initPayment()
    {
        $user           = session('temp_user');
        $orderGroupId   = session('order_group_id');
        $amount         = session('amount');

        if ($amount <= 0) {
            return (new PaymentController)->payment_failed();
        }

        $payment = PaytmWallet::with('receive');
        $payment->prepare([
            'order'         => $orderGroupId,
            'user'          => $user?->id ?? 1,
            'mobile_number' => $user?->phone ?? "+912354123123",
            'email'         => $user?->email ?? "customer@example.com",
            'amount'        => $amount,
            'callback_url'  => route('paytm.callback')
        ]);
        return $payment->receive();
    }

    # paytm callback
    public function callback()
    {
        $transaction = PaytmWallet::with('receive');
        $response = $transaction->response(); // To get raw response as array
        //Check out response parameters sent by paytm here -> http://paywithpaytm.com/developer/paytm_api_doc?target=interpreting-response-sent-by-paytm

        if ($transaction->isSuccessful()) {
            return (new PaymentController)->payment_success($response);
        } elseif ($transaction->isFailed()) {
            return (new PaymentController)->payment_failed();
        }
    }
}
