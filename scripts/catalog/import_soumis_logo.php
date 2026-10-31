<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';
$app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$file = 'images/storefront/soumis-logo.png';
$size = getimagesize(public_path($file));
if (!$size || $size[0] !== 492 || $size[1] !== 314) throw new RuntimeException('Original logo missing or changed');
$report = Illuminate\Support\Facades\DB::transaction(function () use ($file) {
    $media = App\Models\MediaFile::updateOrCreate(['media_file' => $file], ['media_size' => filesize(public_path($file)), 'media_type' => 'image', 'media_name' => 'Original Soumis Bangladesh logo', 'media_extension' => 'png']);
    $before = [];
    foreach (['websiteHeaderLogo', 'homeFooterLogo'] as $key) {
        $setting = App\Models\SystemSetting::firstOrNew(['type' => $key]);
        $before[$key] = $setting->exists ? $setting->value : null;
        $setting->value = $media->id; $setting->save();
    }
    return ['source' => 'https://soumisbangladesh.com/public/assets/img/logo.png', 'local_file' => $file, 'sha256' => hash_file('sha256', public_path($file)), 'media_id' => $media->id, 'previous_settings' => $before, 'dimensions' => [492, 314]];
});
Illuminate\Support\Facades\Cache::forget('settings');
file_put_contents(base_path('database/imports/soumis-contact/logo-import-report.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
