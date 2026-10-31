<?php

namespace App\Console\Commands;

use App\Models\{Blog, BlogCategory, BlogCategoryTranslation, BlogTranslation, MediaFile};
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSoumisBlogs extends Command
{
    protected $signature = 'catalog:import-soumis-blogs {--file=database/imports/soumis-blogs/blogs.json}';
    protected $description = 'Import every verified public Soumis blog article and its local images';

    public function handle(): int
    {
        $path = base_path($this->option('file'));
        if (!is_file($path)) {
            $this->error('Blog manifest missing. Run scripts/catalog/scrape_soumis_blogs.py first.');
            return self::FAILURE;
        }
        $manifest = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (($manifest['source'] ?? '') !== 'https://soumisbangladesh.com' || !empty($manifest['failures']) || empty($manifest['articles'])) {
            $this->error('The blog manifest is incomplete or has unresolved failures.');
            return self::FAILURE;
        }
        foreach ($manifest['images'] as $file) {
            if (!is_file(public_path($file))) {
                $this->error('Missing image: ' . $file);
                return self::FAILURE;
            }
        }
        $report = DB::transaction(function () use ($manifest) {
            $media = []; $categories = []; $created = 0; $updated = 0; $ids = [];
            foreach ($manifest['images'] as $url => $file) {
                $record = MediaFile::updateOrCreate(['media_file' => $file], ['media_size' => filesize(public_path($file)), 'media_type' => 'image', 'media_name' => basename(parse_url($url, PHP_URL_PATH)), 'media_extension' => pathinfo($file, PATHINFO_EXTENSION)]);
                $media[$file] = $record->id;
            }
            foreach ($manifest['categories'] as $name) {
                $category = BlogCategory::where('name', $name)->first() ?? new BlogCategory;
                $category->name = $category->exists ? $category->name : $name;
                $category->save();
                BlogCategoryTranslation::updateOrCreate(['blog_category_id' => $category->id, 'lang_key' => 'en-US'], ['name' => $category->name]);
                $categories[$name] = $category->id;
            }
            foreach ($manifest['articles'] as $source) {
                $blog = Blog::firstOrNew(['slug' => $source['slug']]);
                $new = !$blog->exists;
                $new ? $created++ : $updated++;
                $blog->fill(['title' => $source['title'], 'blog_category_id' => $categories[$source['category']], 'short_description' => $source['short_description'], 'description' => $source['description'], 'thumbnail_image' => $media[$source['local_thumbnail']], 'banner' => $media[$source['local_banner']], 'meta_title' => $source['meta_title'], 'meta_keywords' => $source['meta_keywords'], 'meta_description' => $source['meta_description'], 'meta_image' => $media[$source['local_meta_image']], 'is_published' => 1]);
                // Do not present an import timestamp as the original publication date.
                if ($new || $source['published_at']) {
                    $blog->created_at = $source['published_at'] ? \Carbon\Carbon::parse($source['published_at']) : null;
                }
                $blog->save();
                BlogTranslation::updateOrCreate(['blog_id' => $blog->id, 'lang_key' => 'en-US'], ['title' => $source['title'], 'short_description' => $source['short_description'], 'description' => $source['description']]);
                $ids[$source['source_url']] = $blog->id;
            }
            return ['source' => $manifest['source'], 'imported_at' => now()->toIso8601String(), 'database' => DB::connection()->getDatabaseName(), 'created' => $created, 'updated' => $updated, 'blogs' => count($ids), 'categories' => count(array_unique($categories)), 'images' => count($media), 'blog_id_map' => $ids, 'unavailable_source_urls' => $manifest['unavailable']];
        });
        file_put_contents(base_path('database/imports/soumis-blogs/import-report.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->info(json_encode(array_diff_key($report, ['blog_id_map' => true, 'unavailable_source_urls' => true]), JSON_UNESCAPED_UNICODE));
        return self::SUCCESS;
    }
}
