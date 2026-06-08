<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    # constructor
    public function __construct()
    {
        $this->middleware(['permission:conversation'])->only(['index']);
    }

    # chat page
    public function index(Request $request)
    {
        return view('backend.admin.chat.index');
    }
}
