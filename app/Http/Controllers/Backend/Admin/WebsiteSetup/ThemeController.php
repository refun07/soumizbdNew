<?php

namespace App\Http\Controllers\Backend\Admin\WebsiteSetup;

use App\Http\Controllers\Controller;

class ThemeController extends Controller
{
    # constructor
    public function __construct()
    {
        $this->middleware(['permission:homepage'])->only(['configureTheme', 'themeSelection', 'headerConfiguration', 'footerConfiguration']);
    }

    # theme
    public function configureTheme()
    {
        return view('backend.admin.website-setup.theme');
    }

    # improved theme selection
    public function themeSelection()
    {
        return view('backend.admin.website-setup.theme-selection');
    }

    # header configuration
    public function headerConfiguration($headerId)
    {
        // Validate header ID
        if (!in_array($headerId, ['header-one', 'header-two', 'header-three'])) {
            abort(404);
        }

        return view('backend.admin.website-setup.header-configuration', [
            'headerId' => $headerId
        ]);
    }

    # footer configuration
    public function footerConfiguration()
    {
        return view('backend.admin.website-setup.footer-configuration');
    }
}
