<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscribeController extends Controller
{
    # subscribe to newsletter
    public function subscribe(Request $request)
    {
        Subscriber::updateOrCreate([
            'email' => $request->email,
        ]);
        return response()->json([
            'success'   => true,
            'status'    => 200,
            'message'   => translate('You have subscribed successfully.'),
            'result'    => null
        ]);
    }
}
