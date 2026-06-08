<?php

namespace App\Http\Controllers\Backend\Payments\Instamojo;

use App\Http\Controllers\Backend\Payments\PaymentController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class InstamojoController extends Controller
{
    // init payment
    public function initPayment()
    {
        $user           = session('temp_user');
        $amount         = session('amount');

        if (getSetting('instamojo_sandbox')) {
            // testing_url
            $endPoint = 'https://test.instamojo.com/api/1.1/';
        } else {
            // live_url
            $endPoint = 'https://www.instamojo.com/api/1.1/';
        }

        $api = new \Instamojo\Instamojo(
            env('IM_API_KEY'),
            env('IM_AUTH_TOKEN'),
            $endPoint
        );

        if (preg_match_all('/^(?:(?:\+|0{0,2})91(\s*[\ -]\s*)?|[0]?)?[789]\d{9}|(\d[ -]?){10}\d$/im', $user->phone)) {
            try {
                $response = $api->paymentRequestCreate(array(
                    "purpose"       => translate('Order Payment'),
                    "amount"        => round($amount),
                    "send_email"    => false,
                    "email"         => $user->email ?? 'customer@example.com',
                    "phone"         => $user->phone ?? '123456789',
                    "redirect_url"  => route('instamojo.success')
                ));

                return redirect($response['longurl']);
            } catch (Exception $e) {
                // print('Error: ' . $e->getMessage());
                return (new PaymentController)->payment_failed();
            }
        } else {
            return (new PaymentController)->payment_failed();
        }
    }

    // success response method.
    public function callback(Request $request)
    {
        try {
            if (getSetting('instamojo_sandbox')) {
                $endPoint = 'https://test.instamojo.com/api/1.1/';
            } else {
                $endPoint = 'https://www.instamojo.com/api/1.1/';
            }

            $api = new \Instamojo\Instamojo(
                env('IM_API_KEY'),
                env('IM_AUTH_TOKEN'),
                $endPoint
            );

            $response = $api->paymentRequestStatus(request('payment_request_id'));

            if (!isset($response['payments'][0]['status'])) {
                return (new PaymentController)->payment_failed();
            } else if ($response['payments'][0]['status'] != 'Credit') {
                return (new PaymentController)->payment_failed();
            }
            return (new PaymentController)->payment_success($response);
        } catch (\Exception $e) {
            return (new PaymentController)->payment_failed();
        }
    }
}
