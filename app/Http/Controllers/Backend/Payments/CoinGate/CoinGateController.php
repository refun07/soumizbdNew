<?php

namespace App\Http\Controllers\Backend\Payments\CoinGate;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Payments\PaymentController;
use CoinGate\Client;
use Illuminate\Http\Request;

class CoinGateController extends Controller
{
    public function initPayment()
    {
        $order_id       = session('order_group_id');
        $description    = "Payment for order #$order_id";
        $amount         = session('amount');
        $currency       = strtoupper(session('currency'));

        // if you can secure the api key then get from .env
        $client = new Client(config('app.COINGATE_API_KEY'), getSetting('coingate_sandbox'));
        $token = hash('sha512', 'coingate' . rand());

        $params = array(
            'order_id' => $order_id,
            'price_amount' => $amount,
            'price_currency' => $currency,
            'receive_currency' => $currency,
            'callback_url' => route('coingate.callback', $token),
            'cancel_url' => route('coingate.cancel'),
            'success_url' => route('coingate.success'),
            'title' => "Order #$order_id",
            'description' => $description,
        );
        $order = $client->order->create($params);
        return redirect($order->payment_url);
    }

    public function callback(Request $request)
    {
        if ($request['status'] == 'paid') {
            (new PaymentController)->payment_success($request->all, $request['order_id']);
            return response()->json([
                'message' => 'Payment Successful',
            ]);
        } else {
            return response()->json([
                'message' => 'Payment Failed',
            ]);
        }
    }

    public function success(Request $request)
    {
        return (new PaymentController)->payment_success($request->all);
    }

    public function cancel(Request $request)
    {
        return (new PaymentController)->payment_failed();
    }
}
