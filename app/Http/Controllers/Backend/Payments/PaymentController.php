<?php

namespace App\Http\Controllers\Backend\Payments;

use App\Http\Controllers\Backend\Payments\Bkash\BkashPaymentController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Frontend\Api\CheckoutController;
use App\Http\Controllers\Backend\Payments\Paypal\PaypalController;
use App\Http\Controllers\Backend\Payments\Stripe\StripePaymentController;
use App\Http\Controllers\Backend\Payments\Flutterwave\FlutterwaveController;
use App\Http\Controllers\Backend\Payments\Paystack\PaystackPaymentController;
use App\Http\Controllers\Backend\Payments\Paytm\PaytmPaymentController;
use App\Http\Controllers\Backend\Payments\CoinGate\CoinGateController;
use App\Http\Controllers\Backend\Payments\Instamojo\InstamojoController;
use App\Http\Controllers\Backend\Payments\IyZico\IyZicoController;
use App\Http\Controllers\Backend\Payments\Razorpay\RazorpayController;
use App\Http\Controllers\Backend\Payments\SSLCommerz\SSLCommerzPaymentController;
use App\Models\OrderGroup;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    # init payment gateway
    public function initPayment(Request $request)
    {
        $orderGroup = OrderGroup::where('code', $request->code)->first();
        if (is_null($orderGroup)) {
            return CheckoutController::paymentFailed();
        }

        $user           = $orderGroup->user;
        $transaction    = $orderGroup->transaction;
        $currency       = getSetting('currencyCode') ?? 'USD';
        $payment_method = $transaction->payment_method;

        $request->session()->put('order_group_id', $orderGroup->id);
        $request->session()->put('amount', $transaction->total_amount);
        $request->session()->put('currency', $currency);
        $request->session()->put('temp_user', $user);

        if ($payment_method == 'paypal') {
            return (new PaypalController())->initPayment();
        } else if ($payment_method == 'stripe') {
            return (new StripePaymentController())->initPayment();
        } else if ($payment_method == 'flutterwave') {
            return (new FlutterwaveController)->initPayment();
        } else if ($payment_method == 'paytm') {
            return (new PaytmPaymentController)->initPayment();
        } else if ($payment_method == 'paystack') {
            return (new PaystackPaymentController)->initPayment($request);
        } else if ($payment_method == 'sslcommerz') {
            return (new SSLCommerzPaymentController)->initPayment();
        } else if ($payment_method == 'bkash') {
            return (new BkashPaymentController)->initPayment();
        } else if ($payment_method == 'coingate') {
            return (new CoinGateController)->initPayment();
        } else if ($payment_method == 'iyzico') {
            return (new IyZicoController)->initPayment();
        } else if ($payment_method == 'razorpay') {
            return (new RazorpayController)->initPayment();
        } else if ($payment_method == 'instamojo') {
            return (new InstamojoController)->initPayment();
        }

        # todo::[update versions] more gateways

    }

    # payment successful
    public function payment_success($payment_details = null, $orderGroupId = null)
    {
        if ($orderGroupId == null) {
            $orderGroupId = session('order_group_id');
        }
        return CheckoutController::paymentSuccess($payment_details, $orderGroupId);
    }

    # payment failed
    public function payment_failed($orderGroupId = null)
    {
        if ($orderGroupId == null) {
            $orderGroupId = session('order_group_id');
        }
        return CheckoutController::paymentFailed();
    }
}
