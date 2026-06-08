<?php

namespace App\Http\Controllers\Backend\Admin\WebsiteSetup;

use App\Http\Controllers\Controller;

class ProductSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:homepage']);
    }

    public function index()
    {
        return view('backend.admin.website-setup.product-settings');
    }
}
