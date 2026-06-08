<?php

namespace App\Http\Controllers\Backend\Payments\Bkash;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Backend\Payments\PaymentController;
use App\Http\Controllers\Controller;
use App\Models\OrderGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use URL;

class BkashPaymentController extends Controller
{
    private $base_url;
    private $username;
    private $password;
    private $app_key;
    private $app_secret;

    // config / helpers starts
    public function __construct()
    {
        if (getSetting('bkash_sandbox', 1)) {
            $this->base_url = 'https://tokenized.sandbox.bka.sh/v1.2.0-beta';
        } else {
            $this->base_url = 'https://tokenized.pay.bka.sh/v1.2.0-beta';
        }
        $this->username = env('BKASH_CHECKOUT_USER_NAME');
        $this->password = env('BKASH_CHECKOUT_PASSWORD');
        $this->app_key = env('BKASH_CHECKOUT_APP_KEY');
        $this->app_secret = env('BKASH_CHECKOUT_APP_SECRET');
    }
    public function authHeaders()
    {
        return array(
            'Content-Type:application/json',
            'Authorization:' . $this->grant(),
            'X-APP-Key:' . $this->app_key
        );
    }

    public function curlWithBody($url, $header, $method, $body_data)
    {
        $curl = curl_init($this->base_url . $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $body_data);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    // public function grant()
    // {
    //     $token = Cache::get('token');

    //     if (!is_null($token)) {
    //         return $token;
    //     }

    //     $header = array(
    //         'Content-Type:application/json',
    //         'username:' . $this->username,
    //         'password:' . $this->password
    //     );

    //     $body_data = array('app_key' => $this->app_key, 'app_secret' => $this->app_secret);

    //     $response = $this->curlWithBody('/tokenized/checkout/token/grant', $header, 'POST', json_encode($body_data));

    //     $token = json_decode($response)->id_token;
    //     Cache::put('token', $token, 3600);
    //     return $token;
    // }
    function getIdTokenFromRefreshToken($refresh_token)
    {

        $header = array(
            'Content-Type:application/json',
            'username:' . $this->username,
            'password:' . $this->password
        );

        $body_data = array('app_key' => $this->app_key, 'app_secret' => $this->app_secret, 'refresh_token' => $refresh_token);

        $response = $this->curlWithBody('/tokenized/checkout/token/refresh', $header, 'POST', json_encode($body_data));

        $idToken = json_decode($response)->id_token;

        return $idToken;
    }



    public function grant()
    {

        if (!Schema::hasTable('bkash_token')) {
            // DB::beginTransaction();
            Schema::create('bkash_token', function ($table) {
                $table->boolean('sandbox_mode')->notNullable();
                $table->bigInteger('id_expiry')->notNullable();
                $table->string('id_token', 2048)->notNullable();
                $table->bigInteger('refresh_expiry')->notNullable();
                $table->string('refresh_token', 2048)->notNullable();
            });
            $insertedRows = DB::table('bkash_token')->insert([
                'sandbox_mode' => 1,
                'id_expiry' => 0,
                'id_token' => 'id_token',
                'refresh_expiry' => 0,
                'refresh_token' => 'refresh_token',
            ]);

            if ($insertedRows > 0) {

                // echo 'Row inserted successfully.';
            } else {
                echo 'Error inserting row.';
            }



            $insertedRows = DB::table('bkash_token')->insert([
                'sandbox_mode' => 0,
                'id_expiry' => 0,
                'id_token' => 'id_token',
                'refresh_expiry' => 0,
                'refresh_token' => 'refresh_token',
            ]);

            if ($insertedRows > 0) {
                // echo 'Row inserted successfully.';

            } else {
                echo 'Error inserting row.';
            }
            // DB::commit();
        }


        // DB::beginTransaction();

        $sandbox = getSetting('bkash_sandbox', 1);

        $tokenData = DB::table('bkash_token')->where('sandbox_mode', $sandbox)->first();

        if ($tokenData) {
            // Access the token data
            $idExpiry = $tokenData->id_expiry;
            $idToken = $tokenData->id_token;
            $refreshExpiry = $tokenData->refresh_expiry;
            $refreshToken = $tokenData->refresh_token;

            if ($idExpiry > time()) {
                // dd("Id token from db: ".$idToken);
                return $idToken;
            }
            if ($refreshExpiry > time()) {
                $idToken = $this->getIdTokenFromRefreshToken($refreshToken);
                $updatedRows = DB::table('bkash_token')
                    ->where('sandbox_mode', $sandbox)
                    ->update([
                        'id_expiry' => time() + 3600, // Set new expiry time
                        'id_token' => $idToken,
                    ]);

                if ($updatedRows > 0) {
                    // DB::commit();
                    //echo 'Rows updated successfully.';
                } else {
                    //echo 'Error updating rows.';
                }
                // dd("Id token from refresh api: ".$idToken);
                return $idToken;
            }
            // Do something with the token data
        } else {
            echo 'Token not found.';
        }


        $header = array(
            'Content-Type:application/json',
            'username:' . $this->username,
            'password:' . $this->password
        );

        $body_data = array('app_key' => $this->app_key, 'app_secret' => $this->app_secret);

        $response = $this->curlWithBody('/tokenized/checkout/token/grant', $header, 'POST', json_encode($body_data));

        $idToken = json_decode($response)->id_token;
        $refreshToken = json_decode($response)->refresh_token;

        $updatedRows = DB::table('bkash_token')
            ->where('sandbox_mode', $sandbox)
            ->update([
                'id_expiry' => time() + 3600, // Set new expiry time
                'id_token' => $idToken,
                'refresh_expiry' => time() + 864000,
                'refresh_token' => $refreshToken,
            ]);

        if ($updatedRows > 0) {
            // DB::commit();
            //echo 'Rows updated successfully.';
        } else {
            //echo 'Error updating rows.';
        }
        // dd("Id token from grant api: ".$idToken);
        return $idToken;
    }
    // config / helpers ends

    // public function payment(Request $request)
    // {
    //     return view('bkash.pay');
    // }

    public function initPayment()
    {

        # order data to initiate the payment.
        $orderGroupId   = session('order_group_id');
        $orderGroup     = OrderGroup::where('id', $orderGroupId)->first();
        $amount         = session('amount');

        if (!$amount || $amount < 1) {
            return response()->json(['error' => 'You should pay greater than 1 TK !!'], 400);
        }

        $header = $this->authHeaders();


        $body_data = array(
            'mode' => '0011',
            'payerReference' => $orderGroup->code, // pass oderId or anything 
            'callbackURL' => route('url-callback'),
            'amount' => $amount,
            'currency' => 'BDT',
            'intent' => 'sale',
            'merchantInvoiceNumber' => $orderGroup->code ?? "Inv_" . Str::random(6)
        );

        $response = $this->curlWithBody('/tokenized/checkout/create', $header, 'POST', json_encode($body_data));

        return redirect((json_decode($response)->bkashURL));
    }

    public function executePayment($paymentID)
    {

        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID
        );


        $response = $this->curlWithBody('/tokenized/checkout/execute', $header, 'POST', json_encode($body_data));

        return $response;
    }

    public function queryPayment($paymentID)
    {
        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $paymentID,
        );

        $response = $this->curlWithBody('/tokenized/checkout/payment/status', $header, 'POST', json_encode($body_data));

        return $response;
    }

    public function callback(Request $request)
    {
        $allRequest = $request->all();
        if (isset($allRequest['status']) && $allRequest['status'] == 'success') {
            $response = $this->executePayment($allRequest['paymentID']);
            if (is_null($response)) {
                sleep(1);
                $response = $this->queryPayment($allRequest['paymentID']);
            }

            $res_array = json_decode($response, true);

            if (array_key_exists("statusCode", $res_array) && $res_array['statusCode'] == '0000' && array_key_exists("transactionStatus", $res_array) && $res_array['transactionStatus'] == 'Completed') {
                session()->forget('invoice_number');
                return (new PaymentController)->payment_success($res_array);
            }

            return (new PaymentController)->payment_failed();
        } else {
            return (new PaymentController)->payment_failed();
        }
    }

    public function getRefund(Request $request)
    {
        return view('bkash.refund');
    }

    public function refundPayment(Request $request)
    {
        $header = $this->authHeaders();

        $body_data = array(
            'paymentID' => $request->paymentID,
            'trxID' => $request->trxID
        );

        $response = $this->curlWithBody('/tokenized/checkout/payment/refund', $header, 'POST', json_encode($body_data));

        $res_array = json_decode($response, true);

        $message = "Refund Failed !!";

        if (!isset($res_array['refundTrxID'])) {

            $body_data = array(
                'paymentID' => $request->paymentID,
                'amount' => $request->amount,
                'trxID' => $request->trxID,
                'sku' => 'sku',
                'reason' => 'Quality issue'
            );

            $response = $this->curlWithBody('/tokenized/checkout/payment/refund', $header, 'POST', json_encode($body_data));

            $res_array = json_decode($response, true);

            if (isset($res_array['refundTrxID'])) {
                // your database insert operation    
                $message = "Refund successful !!.Your Refund TrxID : " . $res_array['refundTrxID'];
            }
        } else {
            $message = "Already Refunded !!.Your Refund TrxID : " . $res_array['refundTrxID'];
        }

        return view('bkash.refund')->with([
            'response' => $message,
        ]);
    }

    public function getSearchTransaction(Request $request)
    {
        return view('bkash.search');
    }

    public function searchTransaction(Request $request)
    {

        $header = $this->authHeaders();
        $body_data = array(
            'trxID' => $request->trxID,
        );

        $response = $this->curlWithBody('/tokenized/checkout/general/searchTransaction', $header, 'POST', json_encode($body_data));


        return view('bkash.search')->with([
            'response' => $response,
        ]);
    }
}
