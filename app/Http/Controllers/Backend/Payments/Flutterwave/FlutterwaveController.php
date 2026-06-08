<?php

namespace App\Http\Controllers\Backend\Payments\Flutterwave;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Payments\PaymentController;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class FlutterwaveController extends Controller
{
    # init payment
    public function initPayment()
    {
        $currencyCode   = 'USD';
        $currency       = strtoupper(session('currency'));
        $amount         = session('amount');

        $supportedCurrency = [
            "USD",
            "NGN"
        ];

        if (in_array($currency, $supportedCurrency)) {
            $currencyCode = $currency;
        }


        //This generates a payment reference
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment  
        $user = session('temp_user');
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => $amount,
            'email' => $user?->email ?? 'customer@example.com',
            'tx_ref' => $reference,
            'currency' => $currencyCode,
            'redirect_url' => route('flutterwave.callback'),
            'customer' => [
                'email' => $user?->email ?? 'customer@example.com',
                "phone_number" => $user?->phone ?? '123456789',
                "name" => $user?->name
            ],

            "customizations" => [
                "title" => 'Payment',
                "description" => "-"
            ]
        ];

        $payment = Flutterwave::initializePayment($data);

        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return;
        }

        return redirect($payment['data']['link']);
    }

    # callback
    public function callback()
    {
        $status = request()->status;
        //if payment is successful
        if ($status ==  'successful') {
            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);
            return (new PaymentController)->payment_success($data);
        } else {
            //Put desired action/code after transaction has failed here 
            return (new PaymentController)->payment_failed();
        }
    }
}
