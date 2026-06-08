<?php

namespace App\Http\Controllers\Backend\Payments\SSLCommerz;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Backend\Payments\PaymentController;
use App\Models\OrderGroup;
use App\Utility\SSLCommerz;

class SSLCommerzPaymentController extends Controller
{

    public function initPayment()
    {
        # Here you have to receive all the order data to initiate the payment.
        $orderGroupId   = session('order_group_id');
        $orderGroup     = OrderGroup::where('id', $orderGroupId)->first();
        $amount         = session('amount');

        $post_data = array();
        $post_data['total_amount']  = $amount < 10 ? 10 : $amount; # You cant not pay less than 10
        $post_data['currency']      = "BDT";
        $post_data['tran_id']       = substr(md5($orderGroup->code), 0, 10); // tran_id must be unique  
        $post_data['value_c']       = $orderGroupId;

        $post_data['success_url']   = route('sslcommerz.success');
        $post_data['fail_url']      = route('sslcommerz.fail');
        $post_data['cancel_url']    = route('sslcommerz.cancel');

        $sslc = new SSLCommerz();
        # initiate(Transaction Data , false: Redirect to SSLCOMMERZ gateway/ true: Show all the Payement gateway here )
        $payment_options = $sslc->initiate($post_data, false);

        if (!is_array($payment_options)) {
            print_r($payment_options);
            $payment_options = array();
        }
    }

    public function success(Request $request)
    {
        return (new PaymentController)->payment_success($request->all(), $request->value_c); // value_c = orderGroupId
    }

    public function fail(Request $request)
    {
        return (new PaymentController)->payment_failed($request->value_c); // value_c = orderGroupId
    }

    public function cancel(Request $request)
    {
        return (new PaymentController)->payment_failed($request->value_c); // value_c = orderGroupId
    }
}
