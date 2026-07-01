<?php

namespace App\Http\Controllers\Backend\Admin\Configurations;

use App\Http\Controllers\Controller;
use App\Mail\EmailManager;
use App\Models\SystemSetting;
use Artisan;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{

    # constructor
    public function __construct()
    {
        $this->middleware(['permission:general_settings'])->only('generalSetting');
        $this->middleware(['permission:order_settings'])->only(['orderSetting']);
        $this->middleware(['permission:smtp_settings'])->only(['smtpSettings', 'testEmail']);
        $this->middleware(['permission:payment_methods'])->only(['payment_method', 'payment_method_update']);
        $this->middleware(['permission:social_media_login'])->only(['social_login']);
    }

    # general settings
    public function generalSetting()
    {
        return view('backend.admin.configurations.general.index');
    }

    # general settings
    public function orderSetting()
    {
        return view('backend.admin.configurations.orders.index');
    }

    # update general settings
    public function update(Request $request)
    {
        // Handle dynamic sections actions
        $isDynamicSectionAction = false;

        if ($request->has('action')) {
            $action = $request->action;

            try {
                if ($action == 'add_section' && $request->has('new_section_name') && $request->has('new_section_type')) {
                    $sectionsData = json_decode(getSetting('homepage_dynamic_sections'), true) ?: ['sections' => []];

                    $newSection = [
                        'id' => 'slider_' . uniqid(),
                        'type' => $request->new_section_type,
                        'name' => $request->new_section_name,
                        'enabled' => true,
                        'order' => count($sectionsData['sections']) + 1,
                        'config' => [
                            'title' => '',
                            'subtitle' => '',
                            'images' => [],
                            'settings' => [
                                'slidesPerView' => 1,
                                'spaceBetween' => 15,
                                'autoplay' => [
                                    'delay' => 2000,
                                    'pauseOnMouseEnter' => true
                                ]
                            ]
                        ]
                    ];

                    $sectionsData['sections'][] = $newSection;

                    SystemSetting::updateOrCreate(
                        ['type' => 'homepage_dynamic_sections'],
                        ['value' => json_encode($sectionsData)]
                    );

                    cacheClear();
                    $isDynamicSectionAction = true;

                    flash(translate('Section created successfully'))->success();
                    return back();

                } elseif ($action == 'update_section' && $request->has('section_index')) {
                    $index = $request->section_index;

                    $sectionsData = json_decode(getSetting('homepage_dynamic_sections'), true) ?: ['sections' => []];

                    if (isset($sectionsData['sections'][$index])) {
                        // Preserve current enabled status - only update if checkbox is present
                        // (checkbox is only in the separate toggle form, not the edit modal)
                        if ($request->has("section_enabled_{$index}")) {
                            $sectionsData['sections'][$index]['enabled'] = $request->boolean("section_enabled_{$index}");
                        }

                        // Update slider images if it's a slider
                        if ($sectionsData['sections'][$index]['type'] == 'slider') {
                            // Update title and subtitle
                            if ($request->has("slider_title_{$index}")) {
                                $sectionsData['sections'][$index]['config']['title'] = $request->input("slider_title_{$index}");
                            }
                            if ($request->has("slider_subtitle_{$index}")) {
                                $sectionsData['sections'][$index]['config']['subtitle'] = $request->input("slider_subtitle_{$index}");
                            }

                            // Handle image uploads from media manager (array of file paths)
                            $imageUrls = $request->input("slider_image_urls.{$index}", []);

                            if (!empty($imageUrls)) {
                                $images = [];
                                foreach ($imageUrls as $i => $urlOrId) {
                                    if (!empty($urlOrId)) {
                                        // Convert ID to relative path (without host)
                                        $imagePath = is_numeric($urlOrId)
                                            ? \App\Models\MediaFile::find($urlOrId)?->media_file ?? $urlOrId
                                            : $urlOrId;

                                        $images[] = [
                                            'url' => $imagePath,
                                            'alt' => '',
                                            'order' => $i + 1
                                        ];
                                    }
                                }
                                $sectionsData['sections'][$index]['config']['images'] = $images;
                            }
                        }

                        SystemSetting::updateOrCreate(
                            ['type' => 'homepage_dynamic_sections'],
                            ['value' => json_encode($sectionsData)]
                        );

                        cacheClear();
                        $isDynamicSectionAction = true;

                        flash(translate('Section updated successfully'))->success();
                        return back();
                    } else {
                        flash(translate('Section not found'))->error();
                        return back();
                    }
                } elseif ($action == 'delete_section' && $request->has('section_index')) {
                    $index = $request->section_index;

                    $sectionsData = json_decode(getSetting('homepage_dynamic_sections'), true) ?: ['sections' => []];

                    if (isset($sectionsData['sections'][$index])) {
                        array_splice($sectionsData['sections'], $index, 1);

                        SystemSetting::updateOrCreate(
                            ['type' => 'homepage_dynamic_sections'],
                            ['value' => json_encode($sectionsData)]
                        );

                        cacheClear();
                        $isDynamicSectionAction = true;

                        flash(translate('Section deleted successfully'))->success();
                        return back();
                    } else {
                        flash(translate('Section not found'))->error();
                        return back();
                    }
                }
            } catch (\Exception $e) {
                flash(translate('Error: ') . $e->getMessage())->error();
                return back();
            }
        }

        // Only process regular types if NOT a dynamic section action
        if ($isDynamicSectionAction) {
            return back();
        }

        // Check if types exist before processing
        if (!$request->has('types') || empty($request->types)) {
            flash(translate('Settings updated successfully'))->success();
            return back();
        }

        foreach ($request->types as $key => $type) {
            if ($type == 'systemName') {
                overWriteEnvFile('APP_NAME', $request[$type]);
            }
            if ($type == 'timezone') {
                overWriteEnvFile('APP_TIMEZONE', $request[$type]);
            }
            if ($type == 'appMode') {
                overWriteEnvFile('APP_MODE', $request[$type]);
            } elseif ($type == 'cartVisibilityToggle') {
                $value = $request->has('cartVisibilityToggle') ? '1' : '0';
                $business_settings = SystemSetting::where('type', $type)->first();
                if ($business_settings != null) {
                    $business_settings->value = $value;
                    $business_settings->save();
                } else {
                    $business_settings = new SystemSetting;
                    $business_settings->type = $type;
                    $business_settings->value = $value;
                    $business_settings->save();
                }
            } elseif ($type == 'storefrontDesktopSideSpacing') {
                $value = (int) $request->input($type, 14);
                $value = max(0, min(800, $value));

                SystemSetting::updateOrCreate(
                    ['type' => $type],
                    ['value' => $value]
                );
            } elseif ($type == 'showCouponOnCart') {
                $value = $request->has('showCouponOnCart') ? '1' : '0';
                $business_settings = SystemSetting::where('type', $type)->first();
                if ($business_settings != null) {
                    $business_settings->value = $value;
                    $business_settings->save();
                } else {
                    $business_settings = new SystemSetting;
                    $business_settings->type = $type;
                    $business_settings->value = $value;
                    $business_settings->save();
                }
            } elseif ($type == 'skipCart') {
                $value = $request->has('skipCart') ? '1' : '0';
                $business_settings = SystemSetting::where('type', $type)->first();
                if ($business_settings != null) {
                    $business_settings->value = $value;
                    $business_settings->save();
                } else {
                    $business_settings = new SystemSetting;
                    $business_settings->type = $type;
                    $business_settings->value = $value;
                    $business_settings->save();
                }
            } elseif ($type == 'productInfiniteScroll') {
                $value = $request->has('productInfiniteScroll') ? '1' : '0';
                $business_settings = SystemSetting::where('type', $type)->first();
                if ($business_settings != null) {
                    $business_settings->value = $value;
                    $business_settings->save();
                } else {
                    $business_settings = new SystemSetting;
                    $business_settings->type = $type;
                    $business_settings->value = $value;
                    $business_settings->save();
                }
            } elseif ($type == 'invoiceQrCodeEnabled') {
                $value = $request->has('invoiceQrCodeEnabled') ? '1' : '0';
                $business_settings = SystemSetting::where('type', $type)->first();
                if ($business_settings != null) {
                    $business_settings->value = $value;
                    $business_settings->save();
                } else {
                    $business_settings = new SystemSetting;
                    $business_settings->type = $type;
                    $business_settings->value = $value;
                    $business_settings->save();
                }
            } elseif ($type == 'mediaImageQuality') {
                $value = $request->input($type, 90);
                $value = is_numeric($value) ? (int) $value : 90;
                $value = max(10, min(100, $value));

                SystemSetting::updateOrCreate(
                    ['type' => $type],
                    ['value' => $value]
                );
            } elseif ($type == 'imageToWebp') {
                $value = $request->has('imageToWebp') ? '1' : '0';
                $business_settings = SystemSetting::where('type', $type)->first();
                if ($business_settings != null) {
                    $business_settings->value = $value;
                    $business_settings->save();
                } else {
                    $business_settings = new SystemSetting;
                    $business_settings->type = $type;
                    $business_settings->value = $value;
                    $business_settings->save();
                }
            } elseif (in_array($type, ['posMemoDateEnabled', 'posMemoNoEnabled', 'posMushakChallanNoEnabled'])) {
                $value = $request->input($type) == '1' ? '1' : '0';
                SystemSetting::updateOrCreate(
                    ['type' => $type],
                    ['value' => $value]
                );
            } elseif ($type == 'showThemeOneSecondaryOnSaleProducts') {
                $value = $request->input($type) == '1' ? '1' : '0';
                SystemSetting::updateOrCreate(
                    ['type' => $type],
                    ['value' => $value]
                );
            } else {
                $lang = null;
                if (gettype($type) == 'array') {
                    $lang = array_key_first($type);
                    $type = $type[$lang];
                    $business_settings = SystemSetting::where('type', $type)->where('lang_key', $lang)->first();
                } else {
                    $business_settings = SystemSetting::where('type', $type)->first();
                }

                if ($business_settings != null) {
                    if (gettype($request[$type]) == 'array') {
                        $business_settings->value = json_encode($request[$type]);
                    } else {
                        $business_settings->value = $request[$type];
                    }
                    $business_settings->save();
                } else {
                    $business_settings = new SystemSetting;
                    $business_settings->type = $type;
                    if (gettype($request[$type]) == 'array') {
                        $business_settings->value = json_encode($request[$type]);
                    } else {
                        $business_settings->value = $request[$type];
                    }
                    # $business_settings->lang_key  = $lang;
                    $business_settings->save();
                }
            }
        }
        cacheClear();
        if ($request->ajax()) {
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => null
            ];
        } else {
            flash(translate("Settings updated successfully"))->success();
            return back();
        }
    }

    # delete quick link
    public function deleteQuickLink(Request $request, $index)
    {
        try {
            // Get current quick links from settings
            $links_setting = SystemSetting::where('type', 'homeFooterQuickLinks')->first();
            $titles_setting = SystemSetting::where('type', 'homeFooterQuickLinkTitles')->first();

            // Handle links
            if ($links_setting) {
                $links = json_decode($links_setting->value, true) ?: [];
                if (isset($links[$index])) {
                    // Remove the specific link
                    array_splice($links, $index, 1);
                    $links_setting->value = json_encode($links);
                    $links_setting->save();
                }
            }

            // Handle titles
            if ($titles_setting) {
                $titles = json_decode($titles_setting->value, true) ?: [];
                if (isset($titles[$index])) {
                    // Remove the specific title
                    array_splice($titles, $index, 1);
                    $titles_setting->value = json_encode($titles);
                    $titles_setting->save();
                }
            }

            cacheClear();

            return response()->json([
                'success' => true,
                'message' => translate('Quick link deleted successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => translate('Error deleting quick link')
            ], 500);
        }
    }

    # smtp settings
    public function smtpSettings()
    {
        return view('backend.admin.configurations.smtp.index');
    }

    # test email
    public function testEmail(Request $request)
    {
        $array['view'] = 'emails.newsletter';
        $array['subject'] = "SMTP Test";
        $array['from'] = config('app.mail_from_address)');
        $array['content'] = "This is a test email.";
        try {
            Mail::to($request->email)->queue(new EmailManager($array));
        } catch (\Exception $e) {
            dd($e);
        }

        flash(translate('An email has been sent.'))->success();
        return back();
    }

    # update env values
    public function envKeyUpdate(Request $request)
    {
        foreach ($request->types as $key => $type) {
            overWriteEnvFile($type, $request[$type]);
        }
        cacheClear();
        flash(translate("Settings updated successfully"))->success();
        return back();
    }

    # return form of payment methods
    public function payment_method(Request $request)
    {
        return view('backend.admin.configurations.payment.payment_method');
    }

    # update payment methods
    public function payment_method_update(Request $request)
    {
        if ($request->types) {
            foreach ($request->types as $key => $type) {
                overWriteEnvFile($type, $request[$type]);
            }
        }

        $business_settings_sandbox = SystemSetting::where('type', $request->payment_method . '_sandbox')->first();

        if ($business_settings_sandbox != null) {
            if ($request->has($request->payment_method . '_sandbox')) {
                $business_settings_sandbox->value = 1;
                $business_settings_sandbox->save();
            } else {
                $business_settings_sandbox->value = 0;
                $business_settings_sandbox->save();
            }
        } else {
            $business_settings_sandbox = new SystemSetting;
            $business_settings_sandbox->type = $request->payment_method . '_sandbox';
            if ($request->has($request->payment_method . '_sandbox')) {
                $business_settings_sandbox->value = 1;
                $business_settings_sandbox->save();
            } else {
                $business_settings_sandbox->value = 0;
                $business_settings_sandbox->save();
            }
        }

        # active / inactive
        $business_settings = SystemSetting::where('type', $request->payment_method . '_activation')->first();
        if ($business_settings == null) {
            $business_settings = new SystemSetting;
            $business_settings->type = $request->payment_method . '_activation';
            if ($request->has($request->payment_method . '_activation')) {
                $business_settings->value = 1;
                $business_settings->save();
            } else {
                $business_settings->value = 0;
                $business_settings->save();
            }
        } else {
            $business_settings->type = $request->payment_method . '_activation';
            if ($request->has($request->payment_method . '_activation')) {
                $business_settings->value = 1;
                $business_settings->save();
            } else {
                $business_settings->value = 0;
                $business_settings->save();
            }
        }

        cacheClear();
        flash(translate("Settings updated successfully"))->success();
        return back();
    }

    # social login view
    public function social_login()
    {
        return view('backend.admin.configurations.social.social');
    }
    public function analytics()
    {
        return view('backend.admin.configurations.analytics.index');
    }
    public function otp_settings()
    {
        return view('backend.admin.configurations.otp.index');
    }
    public function delivery_partner_settings(){
        return view('backend.admin.configurations.delivery-partner.index');
    }
}
