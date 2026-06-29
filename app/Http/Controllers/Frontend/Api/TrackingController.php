<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Controller;
use App\Services\FacebookConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrackingController extends Controller
{
    public function __construct(private FacebookConversionService $facebook)
    {
    }

    public function pageView(Request $request)
    {
        $data = $request->validate([
            'eventId' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:2048'],
            'referrer' => ['nullable', 'string', 'max:2048'],
        ]);

        if (config('app.FACEBOOK_PIXEL_ACTIVATION') != '1') {
            return response()->json([
                'success' => true,
                'status' => 200,
                'message' => translate('Page view tracking skipped.'),
                'result' => null,
            ]);
        }

        try {
            $user = apiUser();

            $this->facebook->sendEvent('PageView', [
                'email' => $user?->email,
                'phone' => $user?->phone,
            ], [
                'event_source_url' => $data['url'],
                'referrer' => $data['referrer'] ?? null,
            ], $data['eventId']);
        } catch (\Throwable $e) {
            Log::error('Facebook PageView tracking failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => translate('Failed to track page view.'),
                'result' => null,
            ], 500);
        }

        return response()->json([
            'success' => true,
            'status' => 200,
            'message' => translate('Page view tracked.'),
            'result' => null,
        ]);
    }
}
