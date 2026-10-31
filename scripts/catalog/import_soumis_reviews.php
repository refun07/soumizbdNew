<?php
// Import the testimonial entries already published on the original Soumi's homepage.
require __DIR__.'/../../vendor/autoload.php';
$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (!app()->environment('local')) throw new RuntimeException('This import is local-only.');
$rows=json_decode(file_get_contents(base_path('database/imports/soumis/reviews.json')),true,512,JSON_THROW_ON_ERROR);
$existing=json_decode(getSetting('themeOneReviewNames','[]'),true) ?: [];
if (array_filter($existing)) {
    echo json_encode(['skipped_existing_reviews' => count(array_filter($existing)), 'admin_changes_preserved' => true]);
    exit(0);
}
$backup=storage_path('app/catalog-imports/soumis/reviews-before-import.json');
if (!is_file($backup)) {
    file_put_contents($backup, json_encode(App\Models\SystemSetting::whereIn('type', ['themeOneReviewAvatars','themeOneReviewNames','themeOneReviewDesignations','themeOneReviewTexts','themeOneReviewRatings','showThemeOneReviewSection','botanicalHomeContent'])->pluck('value','type'),JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
    chmod($backup,0600);
}
$owner=App\Models\User::where('user_type','admin')->firstOrFail();
Illuminate\Support\Facades\DB::transaction(function() use($rows,$owner) {
    $avatars=[];
    foreach($rows as $index=>$row){
        $path=$row['local_avatar'];
        if(!is_file(public_path($path))) throw new RuntimeException('Missing testimonial portrait');
        $media=App\Models\MediaFile::firstOrCreate(['media_file'=>$path],[
            'media_name'=>'Soumis testimonial - '.$row['name'],'media_type'=>'image','media_extension'=>pathinfo($path,PATHINFO_EXTENSION),
            'media_size'=>filesize(public_path($path)),'user_id'=>$owner->id,
        ]);
        $avatars[]=$media->id;
    }
    foreach(['themeOneReviewAvatars'=>$avatars,'themeOneReviewNames'=>array_column($rows,'name'),'themeOneReviewDesignations'=>array_column($rows,'designation'),'themeOneReviewTexts'=>array_column($rows,'text'),'themeOneReviewRatings'=>array_column($rows,'rating')] as $key=>$value){
        App\Models\SystemSetting::updateOrCreate(['type'=>$key],['value'=>json_encode($value,JSON_UNESCAPED_UNICODE)]);
    }
    App\Models\SystemSetting::updateOrCreate(['type'=>'showThemeOneReviewSection'],['value'=>'1']);
    $content=json_decode(getSetting('botanicalHomeContent','{}'),true) ?: [];
    $content['reviews']=array_merge($content['reviews'] ?? [],['enabled'=>true,'eyebrow'=>'Testimonials','title'=>'Customer','accent'=>'Review','limit'=>8,'link_label'=>'']);
    App\Models\SystemSetting::updateOrCreate(['type'=>'botanicalHomeContent'],['value'=>json_encode($content,JSON_UNESCAPED_UNICODE)]);
});
Illuminate\Support\Facades\Cache::forget('settings');
echo json_encode(['imported_reviews'=>count($rows),'unrelated_template_review_excluded'=>true]);
