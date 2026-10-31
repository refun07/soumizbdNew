<?php
require dirname(__DIR__, 2) . '/vendor/autoload.php';
$app = require dirname(__DIR__, 2) . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$source = json_decode(file_get_contents(base_path('database/imports/soumis-contact/contact.json')), true, 512, JSON_THROW_ON_ERROR);
$values = ['homeFooterCustomerSupport' => $source['phone'], 'homeFooterAddress' => $source['address'], 'homeFooterEmail' => $source['email'], 'topbar_hotline' => 'Our Hotline ' . $source['phone']];
$before = [];
Illuminate\Support\Facades\DB::transaction(function () use ($values, &$before) {
    foreach ($values as $key => $value) {
        $setting = App\Models\SystemSetting::firstOrNew(['type' => $key]);
        $before[$key] = $setting->exists ? $setting->value : null;
        $setting->value = $value; $setting->save();
    }
});
Illuminate\Support\Facades\Cache::forget('settings');
$report = ['source' => $source['contact_page'], 'imported_at' => now()->toIso8601String(), 'before' => $before, 'after' => $values];
file_put_contents(base_path('database/imports/soumis-contact/import-report.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode(['imported' => array_keys($values), 'phone' => getSetting('homeFooterCustomerSupport'), 'address' => getSetting('homeFooterAddress'), 'email' => getSetting('homeFooterEmail')], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
