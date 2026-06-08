<?php

namespace App\Http\Controllers\Backend\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        return view('backend.seller.chat.index');
    }
}
