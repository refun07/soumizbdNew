<?php

namespace App\Http\Controllers\Backend\Payments\Paystack;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Payments\PaymentController;
use Illuminate\Http\Request;
use Paystack;

class PaystackPaymentController extends Controller
{
    public function initPayment(Request $request)
    {

        $user               = session('temp_user');
        $amount             = session('amount');
        $request->currency  = env('PAYSTACK_CURRENCY_CODE', 'NGN');
        $request->email     = $user?->email ?? 'customer@example.com';
        $request->amount    = round($amount * 100);

        $request->reference = Paystack::genTranxRef();
        return Paystack::getAuthorizationUrl()->redirectNow();
    }


    /**
     * Obtain Paystack payment information
     * @return void
     */
    public function return()
    {
        // Now you have the payment details,
        // you can store the authorization_code in your db to allow for recurrent subscriptions
        // you can then redirect or do whatever you want

        try {
            $payment = Paystack::getPaymentData();
            $payment_details = $payment;
            if (!empty($payment['data']) && $payment['data']['status'] == 'success') {
                return (new PaymentController)->payment_success($payment_details);
            } else {
                return (new PaymentController)->payment_failed();
            }
        } catch (\Exception $e) {
            return (new PaymentController)->payment_failed();
        }
    }
}
