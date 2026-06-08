<?php

namespace App\Http\Controllers\Backend\Admin\WebsiteSetup;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FloatingChatController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:homepage']);
    }

    public function configure()
    {
        return view('backend.admin.website-setup.floating-chat-configuration');
    }

    public function getConfig()
    {
        try {
            $config = [
                'call_enabled' => filter_var(getSetting('floating_chat_call_enabled', false), FILTER_VALIDATE_BOOLEAN),
                'whatsapp_enabled' => filter_var(getSetting('floating_chat_whatsapp_enabled', false), FILTER_VALIDATE_BOOLEAN),
                'whatsapp_number' => getSetting('floating_chat_whatsapp_number', ''),
                'secondary_whatsapp_number' => getSetting('floating_chat_secondary_whatsapp_number', ''),
                'messenger_enabled' => filter_var(getSetting('floating_chat_messenger_enabled', false), FILTER_VALIDATE_BOOLEAN),
                'messenger_url' => getSetting('floating_chat_messenger_url', ''),
                'tiktok_enabled' => filter_var(getSetting('floating_chat_tiktok_enabled', false), FILTER_VALIDATE_BOOLEAN),
                'tiktok_url' => getSetting('floating_chat_tiktok_url', ''),
            ];

            return response()->json([
                'success' => true,
                'data' => $config
            ]);
        } catch (\Exception $e) {
            \Log::error('Floating chat config fetch failed', [
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get floating chat configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateConfig(Request $request)
    {
        try {
            $rules = [
                'call_enabled' => 'nullable|boolean',
                'whatsapp_enabled' => 'nullable|boolean',
                'whatsapp_number' => 'nullable|string|max:20',
                'secondary_whatsapp_number' => 'nullable|string|max:20',
                'messenger_enabled' => 'nullable|boolean',
                'messenger_url' => 'nullable|url|max:255',
                'tiktok_enabled' => 'nullable|boolean',
                'tiktok_url' => 'nullable|url|max:255',
            ];

            $validator = validator($request->all(), $rules);

            if ($validator->fails()) {
                throw new \Illuminate\Validation\ValidationException($validator);
            }

            $this->updateSystemSetting('floating_chat_call_enabled', $request->boolean('call_enabled', false));
            $this->updateSystemSetting('floating_chat_whatsapp_enabled', $request->boolean('whatsapp_enabled', false));
            $this->updateSystemSetting('floating_chat_whatsapp_number', $request->input('whatsapp_number', ''));
            $this->updateSystemSetting('floating_chat_secondary_whatsapp_number', $request->input('secondary_whatsapp_number', ''));
            $this->updateSystemSetting('floating_chat_messenger_enabled', $request->boolean('messenger_enabled', false));
            $this->updateSystemSetting('floating_chat_messenger_url', $request->input('messenger_url', ''));
            $this->updateSystemSetting('floating_chat_tiktok_enabled', $request->boolean('tiktok_enabled', false));
            $this->updateSystemSetting('floating_chat_tiktok_url', $request->input('tiktok_url', ''));

            Cache::forget('settings');

            return response()->json([
                'success' => true,
                'message' => 'Floating chat configuration updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Floating chat configuration validation failed', [
                'user_id' => auth()->id(),
                'validation_errors' => $e->errors(),
                'request_data' => $request->all()
            ]);

            throw $e;
        } catch (\Exception $e) {
            \Log::error('Floating chat configuration save failed', [
                'user_id' => auth()->id(),
                'error_message' => $e->getMessage(),
                'request_data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update floating chat configuration: ' . $e->getMessage()
            ], 500);
        }
    }

    private function updateSystemSetting($key, $value)
    {
        SystemSetting::updateOrCreate(
            ['type' => $key],
            ['value' => $value]
        );
    }
}
