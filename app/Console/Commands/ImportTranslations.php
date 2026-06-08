<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ImportTranslations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:import 
                            {--locale=en-US : The locale to import translations for}
                            {--file=translations.txt : The file to import from}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import translations from translations.txt file into database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $locale = $this->option('locale');
        $file = $this->option('file');
        $path = base_path($file);

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $this->info("Importing translations from {$file} for locale: {$locale}");

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $imported = 0;
        $skipped = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Generate key using shared helper
            $key = generateTranslationKey($line);

            // Skip if key generation failed (e.g., only special characters)
            if (empty($key)) {
                continue;
            }

            // Check if already exists
            $exists = Translation::where('lang_key', $locale)
                ->where('t_key', $key)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            // Create translation
            Translation::create([
                'lang_key' => $locale,
                't_key' => $key,
                't_value' => $line
            ]);

            $imported++;
        }

        // Clear cache
        Cache::forget("translations-{$locale}");

        $this->info("Import complete!");
        $this->info("Imported: {$imported}");
        $this->info("Skipped (already exists): {$skipped}");

        return 0;
    }
}
