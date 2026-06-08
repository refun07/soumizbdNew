<?php

namespace App\Http\Controllers\Backend\Payments\Stripe;

use App\Http\Controllers\Controller;
use Stripe\Stripe;
use App\Http\Controllers\Backend\Payments\PaymentController;

class StripePaymentController extends Controller
{
    # stripe payment view
    public function initPayment()
    {
        return view('payments.stripe');
    }

    # init payment
    public function checkoutSession()
    {
        $currencyCode   = 'USD';
        $currency       = strtoupper(session('currency'));
        $amount         = session('amount');

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $supportedCurrency = [
            "EUR",   # Euro
            "GBP",   # British Pound Sterling
            "CAD",   # Canadian Dollar
            "AUD",   # Australian Dollar
            "JPY",   # Japanese Yen
            "CHF",   # Swiss Franc
            "NZD",   # New Zealand Dollar
            "HKD",   # Hong Kong Dollar
            "SGD",   # Singapore Dollar
            "SEK",   # Swedish Krona
            "DKK",   # Danish Krone
            "PLN",   # Polish Złoty
            "NOK",   # Norwegian Krone
            "CZK",   # Czech Koruna
            "HUF",   # Hungarian Forint
            "ILS",   # Israeli New Shekel
            "MXN",   # Mexican Peso
            "BRL",   # Brazilian Real
            "MYR",   # Malaysian Ringgit
            "PHP",   # Philippine Peso
            "TWD",   # New Taiwan Dollar
            "THB",   # Thai Baht
            "TRY",   # Turkish Lira
            "RUB",   # Russian Ruble
            "INR",   # Indian Rupee
            "ZAR",   # South African Rand
            "AED",   # United Arab Emirates Dirham
            "SAR",   # Saudi Riyal
            "KRW",   # South Korean Won
            "CNY"    # Chinese Yuan
        ];

        if (in_array($currency, $supportedCurrency)) {
            $currencyCode = $currency;
        }

        $amount = number_format((float)$amount, 2, '.', '')  * 100;
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => $currencyCode,
                        'product_data' => [
                            'name' => "Payment"
                        ],
                        'unit_amount' => $amount,
                    ],
                    'quantity' => 1,
                ]
            ],
            'mode' => 'payment',
            'success_url' => route('stripe.success'),
            'cancel_url' => route('stripe.cancel'),
        ]);
        return response()->json(['id' => $session->id, 'status' => 200]);
    }

    # successful payment
    public function success()
    {
        try {
            $payment = ["status" => "Success"];
            return (new PaymentController)->payment_success($payment);
        } catch (\Exception $e) {
            return (new PaymentController)->payment_failed();
        }
    }

    # cancelled
    public function cancel()
    {
        return (new PaymentController)->payment_failed();
    }
}
