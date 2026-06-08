<?php

namespace App\Http\Controllers\Backend\Admin\WebsiteSetup;

use App\Http\Controllers\Controller;

class ColorBrandingController extends Controller
{
    # constructor
    public function __construct()
    {
        $this->middleware(['permission:color_and_branding'])->only('colorBranding');
    }

    # color-branding
    public function colorBranding()
    {
        return view('backend.admin.website-setup.color-branding');
    }
}
