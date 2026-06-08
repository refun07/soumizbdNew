<?php

namespace App\Http\Controllers\Backend\Admin\Configurations;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use Cache;
use File;
use Illuminate\Http\Request;
use Session;
use Storage;

class LanguageController extends Controller
{
    // constructor
    public function __construct()
    {
        $this->middleware(['permission:view_languages'])->only('index');
        $this->middleware(['permission:create_languages'])->only(['create', 'store']);
        $this->middleware(['permission:edit_languages'])->only(['edit', 'show', 'update', 'keyValueStore', 'updateRtlStatus', 'saveTranslationsAsJsonFile']);
        $this->middleware(['permission:delete_languages'])->only('destroy');
    }

    # change language from navbar
    public function changeLanguage(Request $request)
    {
        $request->session()->put('locale', $request->locale);
        $language = Language::where('code', $request->locale)->first();
        flash(translate('Language changed to') . ' ' . $language->name)->success();
        return true;
    }

    # get all resources with pagination
    public function index(Request $request)
    {
        $languages = Language::paginate(10);
        return view('backend.admin.configurations.languages.index', compact('languages'));
    }

    # create new resource form
    public function create()
    {
        // 
    }

    # store new resource
    public function store(Request $request)
    {
        $language       = new Language;
        $language->name = $request->name;
        $language->flag = $request->flag;
        $language->code = strtolower($request->code);
        $language->save();

        $this->saveTranslationsAsJsonFile($language->code);

        Cache::forget('languages');

        flash(translate('Language has been inserted successfully'))->success();
        return redirect()->route('admin.languages.index');
    }

    # show translation page for a language 
    public function show(Request $request, $code)
    {
        $searchKey    = null;
        $limit  = $request->limit ?? 50;
        $language       = Language::where('code', $code)->first();
        if (is_null($language)) {
            abort(404);
        }
        $langKeys      = Translation::where('lang_key', config('app.default_language'));
        if ($request->has('search')) {
            $searchKey      = $request->search;
            $langKeys       = $langKeys->where('t_value', 'like', '%' . $searchKey . '%');
        }

        $langKeys = $langKeys->paginate($limit);
        return view('backend.admin.configurations.languages.show', compact('language', 'langKeys', 'searchKey'));
    }

    # edit form of resource 
    public function edit($id)
    {
        $language = Language::findOrFail($id);
        return view('backend.admin.configurations.languages.edit', compact('language'));
    }

    # update resource
    public function update(Request $request, $id)
    {
        $language = Language::findOrFail($id);
        $language->name = $request->name;
        $language->flag = $request->flag;
        if ($language->id != 1) {
            $language->code = strtolower($request->code);
        }
        if ($language->save()) {

            Cache::forget('languages');

            flash(translate('Language has been updated successfully'))->success();
            return redirect()->route('admin.languages.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return redirect()->route('admin.languages.index');
        }
    }

    # update translations for a resource
    public function keyValueStore(Request $request)
    {
        $language = Language::findOrFail($request->id);
        foreach ($request->values as $key => $value) {
            $translation_def    = Translation::where('t_key', $key)->where('lang_key', $language->code)->latest()->first();
            if ($translation_def == null) {
                $translation_def            = new Translation;
                $translation_def->lang_key  = $language->code;
                $translation_def->t_key     = $key;
                $translation_def->t_value   = $value;
                $translation_def->save();
            } else {
                $translation_def->t_value = $value;
                $translation_def->save();
            }
        }
        Cache::forget('translations-' . $language->code);
        $this->saveTranslationsAsJsonFile($language->code);
        flash(translate('Translations updated for ') . $language->name)->success();
        return back();
    }

    # update rtl status for a resource
    public function updateRtlStatus(Request $request)
    {
        $language = Language::findOrFail($request->id);
        $language->is_rtl = $request->status;
        if ($language->save()) {
            Cache::forget('languages');
            return 1;
        }
        return 0;
    }

    # delete a resource
    public function destroy($id)
    {
        $language = Language::findOrFail($id);
        if (config('app.default_language') == $language->code) {
            flash(translate('Default language can not be deleted'))->error();
        } else {
            if ($language->code == Session::get('locale')) {
                Session::put('locale', config('app.default_language'));
            }
            Language::destroy($id);

            Cache::forget('languages');
            flash(translate('Language has been deleted successfully'))->success();
        }
        return redirect()->route('admin.languages.index');
    }

    # save translations in json file
    public function saveTranslationsAsJsonFile($code)
    {
        try {
            // Write into the json file

            $destinationPath = resource_path('/lang/' . $code . 'json');
            if (!File::exists($destinationPath)) {
                Storage::disk('lang')->put("$code.json", "[]");
            }

            // Get the translation data.
            $translations = Translation::where('lang_key', $code)->pluck('t_value', 't_key');

            // Convert the translation data to JSON.
            $json = json_encode($translations, JSON_PRETTY_PRINT);

            // Save the JSON file.
            file_put_contents(resource_path("lang/{$code}.json"), $json);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    # insert frontend translation keys into translations.txt file
    public function insertTranslationKeysToFile(Request $request)
    {
        $path = base_path('translations.txt');
        if (file_exists($path)) {
            if (is_numeric(strpos(file_get_contents($path), $request->value)) && strpos(file_get_contents($path), $request->value) >= 0) {
                // skip if already added
            } else {
                file_put_contents($path, file_get_contents($path) . "\r\n" . $request->value);
            }
        }

        return response()->json([
            'key' => $request->key,
            'value' => $request->value
        ]);
    }

    # insert translation keys into database
    public function insertTranslationKeysToDatabase()
    {
        $fn = fopen(base_path('translations.txt'), "r");

        while (!feof($fn)) {
            $newLine = fgets($fn);
            translate(trim($newLine));
        }

        fclose($fn);

        $this->saveTranslationsAsJsonFile('en-US');

        return;
    }
}
