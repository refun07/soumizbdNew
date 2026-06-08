<?php

namespace App\Http\Controllers\Backend\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FileManagerController extends Controller
{
    public function __construct()
    {
        // $this->middleware(['permission:show_uploaded_files'])->only('index');
    }

    # file manager index
    public function index(Request $request)
    {
        return view('backend.seller.file-manager.index');
    }
}
