<?php

namespace App\Http\Controllers\Backend\Api\Translation;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    /**
     * Get all translations for a specific locale
     * Supports dynamic languages created from admin panel
     */
    public function getTranslations(Request $request, $locale)
    {
        // Get all valid locales from Language model
        $validLocales = Language::isActive()->pluck('code')->toArray();
        
        // If locale is not valid, fall back to en-US
        if (!in_array($locale, $validLocales)) {
            $locale = 'en-US';
        }

        // Get translations from cache or database
        $translations = Cache::rememberForever("translations-{$locale}", function () use ($locale) {
            return Translation::where('lang_key', $locale)
                ->pluck('t_value', 't_key')
                ->toArray();
        });

        // For English locale, auto-import from translations.txt if empty
        if (empty($translations) && $locale === 'en-US') {
            $this->importFromTranslationsFile();
            // Refresh from database
            $translations = Translation::where('lang_key', $locale)
                ->pluck('t_value', 't_key')
                ->toArray();
            Cache::put("translations-{$locale}", $translations);
        }

        return response()->json($translations);
    }

    /**
     * Import translations from translations.txt file
     * This runs once when translations are empty
     */
    private function importFromTranslationsFile()
    {
        $path = base_path('translations.txt');
        
        if (!file_exists($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Generate key using shared helper
            $t_key = generateTranslationKey($line);
            
            // Skip if key generation failed (e.g., only special characters)
            if (empty($t_key)) {
                continue;
            }
            
            // Check if already exists
            $exists = Translation::where('lang_key', 'en-US')
                ->where('t_key', $t_key)
                ->exists();

            if (!$exists) {
                Translation::create([
                    'lang_key' => 'en-US',
                    't_key' => $t_key,
                    't_value' => $line
                ]);
            }
        }
    }

    /**
     * Add a new translation key (called when frontend encounters new text)
     */
    public function addTranslation(Request $request)
    {
        // Get valid locales dynamically
        $validLocales = Language::isActive()->pluck('code')->toArray();
        
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|string',
            'locale' => ['required', 'string', function ($attribute, $value, $fail) use ($validLocales) {
                if (!in_array($value, $validLocales)) {
                    $fail('The selected locale is not valid.');
                }
            }]
        ]);

        $key = $request->input('key');
        $value = $request->input('value');
        $locale = $request->input('locale');

        // Check if already exists
        $existing = Translation::where('lang_key', $locale)
            ->where('t_key', $key)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => true,
                'message' => 'Translation already exists',
                'data' => $existing
            ]);
        }

        // Create new translation
        $translation = Translation::create([
            'lang_key' => $locale,
            't_key' => $key,
            't_value' => $value
        ]);

        // Clear cache
        Cache::forget("translations-{$locale}");

        return response()->json([
            'success' => true,
            'message' => 'Translation added successfully',
            'data' => $translation
        ]);
    }
}
