<?php

namespace App\Http\Controllers\Backend\Admin\WebsiteSetup;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Page;
use App\Models\PageTranslation;
use Illuminate\Http\Request;
use Str;

class PageController extends Controller
{
    // constructor
    public function __construct()
    {
        $this->middleware(['permission:view_pages'])->only('index');
        $this->middleware(['permission:create_pages'])->only(['create', 'store']);
        $this->middleware(['permission:edit_pages'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_pages'])->only('destroy');
        $this->middleware(['permission:homepage'])->only('configure');
    }

    # Display a listing of the resource.
    public function index(Request $request)
    {
        $pages  = Page::where('type', '!=', 'special')->get();
        return view('backend.admin.website-setup.pages.index', compact('pages'));
    }

    # create form
    public function create()
    {
        return view('backend.admin.website-setup.pages.create');
    }

    # add new data
    public function store(Request $request)
    {
        $page = new Page;
        $page->title            = $request->title;
        $page->slug             = Str::slug($request->slug);
        $page->content          = $request->content;
        $page->meta_title       = $request->meta_title;
        $page->meta_description = $request->meta_description;
        $page->meta_keywords    = $request->meta_keywords;
        $page->meta_image       = $request->meta_image;
        $page->save();

        $pageTranslation           = PageTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'page_id' => $page->id]);
        $pageTranslation->title    = $request->title;
        $pageTranslation->content  = $request->content;
        $pageTranslation->save();

        flash(translate('Page has been created successfully'))->success();
        return redirect()->route('admin.pages.index');
    }

    # edit resource
    public function edit(Request $request, $id)
    {
        $lang_key = $request->lang_key;
        $language = Language::isActive()->where('code', $lang_key)->first();
        if (!$language) {
            flash(translate('Language you are trying to translate is not available or not active'))->error();
            return redirect()->route('admin.pages.index');
        }
        $page = Page::findOrFail($id);
        return view('backend.admin.website-setup.pages.edit', compact('page', 'lang_key'));
    }

    # update resource
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);

        if ($request->lang_key == config("app.default_language")) {
            $page->title = $request->title;
            $page->content  = $request->content;
            $page->slug             = $request->slug ? Str::slug($request->slug) : $page->slug;
            $page->content          = $request->content;
            $page->meta_title       = $request->meta_title;
            $page->meta_description = $request->meta_description;
            $page->meta_keywords    = $request->meta_keywords;
            $page->meta_image       = $request->meta_image;
        }


        $pageTranslation           = PageTranslation::firstOrNew(['lang_key' => $request->lang_key, 'page_id' => $page->id]);
        $pageTranslation->title    = $request->title;
        $pageTranslation->content  = $request->content;
        $pageTranslation->save();

        $page->save();
        $pageTranslation->save();
        flash(translate('Page has been updated successfully'))->success();
        return back();
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();
        flash(translate('Page has been deleted successfully'))->success();
        return redirect()->route('admin.pages.index');
    }

    # configure special page
    public function configure(Request $request, $id)
    {
        $lang_key = $request->lang_key;
        $language = Language::isActive()->where('code', $lang_key)->first();
        if (!$language) {
            flash(translate('Language you are trying to translate is not available or not active'))->error();
            return redirect()->route('admin.pages.index');
        }
        $page = Page::findOrFail($id);
        if ($page->type == "special") {
            $theme = getSetting('defaultTheme') ?? 'theme-one';
            return view('backend.admin.website-setup.pages.configure', compact('page', 'lang_key', 'theme'));
        }

        flash(translate('You are trying something unusual'))->error();
        return redirect()->route('admin.pages.index');
    }
}
