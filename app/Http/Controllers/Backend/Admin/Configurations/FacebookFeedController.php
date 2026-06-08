<?php

namespace App\Http\Controllers\Backend\Admin\Configurations;

use App\Http\Controllers\Controller;
use Artisan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class FacebookFeedController extends Controller
{
    # constructor
    public function __construct()
    {
        $this->middleware(['permission:view_products']);
    }

    # show feed status
    public function index()
    {
        $feedPath = storage_path('app/feed/products.csv');
        $feedExists = file_exists($feedPath);
        
        $feedInfo = [
            'exists' => $feedExists,
            'lastGenerated' => null,
            'productCount' => 0,
            'fileSize' => 0,
        ];

        if ($feedExists) {
            $feedInfo['lastGenerated'] = date('Y-m-d H:i:s', filemtime($feedPath));
            $feedInfo['fileSize'] = $this->formatBytes(filesize($feedPath));
            
            try {
                $csv = Reader::createFromPath($feedPath, 'r');
                $csv->setHeaderOffset(0);
                $feedInfo['productCount'] = count($csv);
            } catch (\Exception $e) {
                Log::error('Error reading Facebook feed CSV', ['error' => $e->getMessage()]);
            }
        }

        return view('backend.admin.configurations.facebook-feed.index', compact('feedInfo'));
    }

    # generate feed
    public function generate(Request $request)
    {
        try {
            $exitCode = Artisan::call('feed:facebook');
            
            if ($exitCode === 0) {
                flash(translate('Facebook feed generated successfully'))->success();
            } else {
                flash(translate('Failed to generate Facebook feed. Please check logs.'))->error();
            }
        } catch (\Exception $e) {
            Log::error('Error generating Facebook feed', ['error' => $e->getMessage()]);
            flash(translate('An error occurred while generating the feed'))->error();
        }

        return redirect()->route('admin.facebook-feed.index');
    }

    # download feed
    public function download()
    {
        $feedPath = storage_path('app/feed/products.csv');
        
        if (!file_exists($feedPath)) {
            flash(translate('Feed not found. Please generate it first.'))->error();
            return redirect()->route('admin.facebook-feed.index');
        }

        $filename = 'facebook_products_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        return response()->download($feedPath, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    # helper to format bytes
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
