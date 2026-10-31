<?php

namespace App\Http\Controllers\Backend\Admin\WebsiteSetup;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\SystemSetting;
use App\Services\BotanicalHomeContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BotanicalHomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:homepage');
    }

    public function edit()
    {
        $selectedCategories = BotanicalHomeContent::content(false)['categories']['selection'];

        return view('backend.admin.website-setup.pages.botanical', [
            'schema' => BotanicalHomeContent::schema(),
            'content' => BotanicalHomeContent::content(false),
            'products' => Product::isPublished()->orderBy('name')->get(['id', 'name']),
            'categories' => Category::orderBy('name')->get(['id', 'name'])
                ->sortBy(fn ($category) => ($position = array_search((int) $category->id, $selectedCategories, true)) === false ? PHP_INT_MAX : $position)
                ->values(),
        ]);
    }

    public function reviews()
    {
        return view('backend.admin.website-setup.pages.botanical-reviews');
    }

    public function update(Request $request)
    {
        $rules = ['content' => ['required', 'array']];
        foreach (BotanicalHomeContent::schema() as $section => $definition) {
            foreach ($definition['fields'] as $key => $field) {
                $path = "content.$section.$key";
                $rules[$path] = match ($field['type']) {
                    'boolean' => ['required', 'boolean'],
                    'number' => ['required', 'integer', 'min:1', 'max:'.$field['max']],
                    'datetime' => ['nullable', 'date_format:Y-m-d\TH:i'],
                    'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
                    'products', 'categories' => ['nullable', 'array', 'max:24'],
                    'link' => ['nullable', 'string', 'max:2048', function ($attribute, $value, $fail) {
                        if ($value && !preg_match('~^(?:/(?!/)|\#|https?://)~i', $value)) {
                            $fail('Use a site path starting with / or an http(s) URL.');
                        }
                    }],
                    'image' => ['nullable', 'string', 'max:2048', function ($attribute, $value, $fail) {
                        if ($value && !is_numeric($value) && !preg_match('~^/(?!/)~', $value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                            $fail('Select an image from the media library or enter its site path.');
                        }
                        if ($value && !is_numeric($value) && preg_match('~^[a-z]+:~i', $value) && !preg_match('~^https?://~i', $value)) {
                            $fail('Image URLs must use http(s).');
                        }
                    }],
                    default => ['nullable', 'string', 'max:'.($field['type'] === 'textarea' ? 2000 : 300)],
                };
                if (in_array($field['type'], ['products', 'categories'])) {
                    $table = $field['type'] === 'products' ? 'products' : 'categories';
                    $rules[$path.'.*'] = ['integer', 'distinct', "exists:$table,id"];
                }
            }
        }
        $validated = Validator::make($request->all(), $rules)->validate();
        $content = [];
        foreach (BotanicalHomeContent::schema() as $section => $definition) {
            foreach ($definition['fields'] as $key => $field) {
                $value = data_get($validated, "content.$section.$key");
                $content[$section][$key] = match ($field['type']) {
                    'boolean' => (bool) $value,
                    'number' => (int) $value,
                    'products', 'categories' => array_map('intval', $value ?? []),
                    default => $value ?? '',
                };
            }
        }
        DB::transaction(function () use ($content) {
            SystemSetting::updateOrCreate(['type' => 'botanicalHomeContent'], ['value' => json_encode($content, JSON_UNESCAPED_UNICODE)]);
            SystemSetting::updateOrCreate(['type' => 'showThemeOneReviewSection'], ['value' => $content['reviews']['enabled'] ? '1' : '0']);
        });
        Cache::forget('settings');
        flash('Homepage content saved. The storefront now uses these settings.')->success();
        return redirect()->route('admin.botanical-home.edit');
    }
}
