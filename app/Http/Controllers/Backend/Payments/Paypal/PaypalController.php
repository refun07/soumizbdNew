<?php

namespace App\Http\Controllers\Backend\Payments\Paypal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalHttp\HttpException;
use Redirect;
use App\Http\Controllers\Backend\Payments\PaymentController;

class PaypalController extends Controller
{
    # init payment
    public function initPayment()
    {
        $currencyCode   = 'USD';
        $currency       = strtoupper(session('currency'));
        $amount         = session('amount');

        # Creating an environment
        $clientId       = env('PAYPAL_CLIENT_ID');
        $clientSecret   = env('PAYPAL_CLIENT_SECRET');

        if (getSetting('paypal_sandbox') == 1) {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        } else {
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        }
        $client = new PayPalHttpClient($environment);

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
            "ZAR",   # South African Rand
            "AED",   # United Arab Emirates Dirham
            "SAR"    # Saudi Riyal
        ];

        if (in_array($currency, $supportedCurrency)) {
            $currencyCode = $currency;
        }

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            "intent" => "CAPTURE",
            "purchase_units" => [[
                "reference_id" => rand(000000, 999999),
                "amount" => [
                    "value" => number_format($amount, 2, '.', ''),
                    "currency_code" => $currencyCode
                ]
            ]],
            "application_context" => [
                "cancel_url" => route('paypal.cancel'),
                "return_url" => route('paypal.success')
            ]
        ];

        try {
            # Call API with your client and get a response for your call
            $response = $client->execute($request);
            # If call returns body in response, you can get the deserialized version from the result attribute of the response
            return Redirect::to($response->result->links[1]->href);
        } catch (HttpException $ex) {
            dd($ex);
            return (new PaymentController)->payment_failed();
        }
    }

    # order success
    public function success(Request $request)
    {
        # Creating an environment
        $clientId = env('PAYPAL_CLIENT_ID');
        $clientSecret = env('PAYPAL_CLIENT_SECRET');

        if (getSetting('paypal_sandbox') == 1) {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        } else {
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        }

        $client = new PayPalHttpClient($environment);

        # $response->result->id gives the orderId of the order created above 
        $ordersCaptureRequest = new OrdersCaptureRequest($request->token);
        $ordersCaptureRequest->prefer('return=representation');
        try {
            # Call API with your client and get a response for your call
            $response = $client->execute($ordersCaptureRequest);
            # If call returns body in response, you can get the deserialized version from the result attribute of the response
            return (new PaymentController)->payment_success($response);
        } catch (HttpException $ex) {
            return (new PaymentController)->payment_failed();
        }
    }

    # order cancelled
    public function cancel(Request $request)
    {
        # Curse and humiliate the user for cancelling this most sacred payment (yours)
        return (new PaymentController)->payment_failed();
    }
}
