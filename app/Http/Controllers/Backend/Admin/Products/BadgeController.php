<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use App\Models\BadgeTranslation;
use App\Models\Language;
use App\Models\ProductBadge;
use Illuminate\Http\Request;

class BadgeController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_badges'])->only(['index']);
        $this->middleware(['permission:create_badges'])->only(['store']);
        $this->middleware(['permission:edit_badges'])->only(['edit', 'update', 'updateStatus']);
        $this->middleware(['permission:delete_badges'])->only(['destroy']);
    }

    # Display a listing of the resource.
    public function index(Request $request, $limit = 15)
    {
        $searchKey = null;

        $badges = Badge::latest();
        if ($request->search != null) {
            $badges = $badges->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $badges = $badges->paginate($limit);
        return view('backend.admin.badges.index', compact('badges', 'searchKey'));
    }

    # Show the form for creating a new resource.
    public function create()
    {
        return view('backend.admin.badges.create');
    }

    # Store a newly created resource in storage.
    public function store(Request $request)
    {
        $badge            = new Badge;
        $badge->name      = $request->name;
        $badge->color     = $request->color;
        $badge->bg_color  = $request->bg_color;
        $badge->is_active = 1;
        $badge->save();

        $badgeTranslation = BadgeTranslation::firstOrNew(['lang_key' => config('app.default_language'), 'badge_id' => $badge->id]);
        $badgeTranslation->name  = $badge->name;
        $badgeTranslation->save();

        if ($request->ajax()) {
            $badges       = Badge::isActive()->orderBy('name', 'ASC')->get();
            $productBadges = collect();
            return view('components.backend.inc.products.badge-list', compact('badges', 'productBadges'))->render();
        } else {
            flash(translate('Badge has been added successfully'))->success();
            return redirect()->route('admin.badges.index');
        }
    }

    # Display the specified resource.
    public function show($id)
    {
        //
    }

    # Show the form for editing the specified resource.
    public function edit(Request $request, $id)
    {
        $lang_key = $request->lang_key;
        $language = Language::isActive()->where('code', $lang_key)->first();
        if (!$language) {
            flash(translate('Language you are trying to translate is not available or not active'))->error();
            return redirect()->route('admin.badges.index');
        }
        $badge = Badge::findOrFail($id);

        return view('backend.admin.badges.edit', compact('badge', 'lang_key'));
    }

    # Update the specified resource in storage. 
    public function update(Request $request, $id)
    {
        $badge = Badge::findOrFail($id);

        if ($request->lang_key == config("app.default_language")) {
            $badge->name      = $request->name;
            $badge->color     = $request->color;
            $badge->bg_color  = $request->bg_color;
        }

        $badgeTranslation = BadgeTranslation::firstOrNew(['lang_key' => $request->lang_key, 'badge_id' => $badge->id]);
        $badgeTranslation->name             = $request->name;

        $badge->save();
        $badgeTranslation->save();
        flash(translate('Badge has been updated successfully'))->success();
        return redirect()->route('admin.badges.index');
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $badge = Badge::findOrFail($id);
        try {
            ProductBadge::where('badge_id', $id)->delete();
        } catch (\Throwable $th) {
            //throw $th;
        }
        $badge->delete();
        flash(translate('Badge has been deleted successfully'))->success();
        return redirect()->route('admin.badges.index');
    }

    # update status
    public function updateStatus(Request $request)
    {
        $data = [
            'success'   => true,
            'status'    => 200,
            'message'   => translate('Status updated successfully'),
            'result'    => null
        ];
        $badge = Badge::findOrFail($request->id);
        $badge->is_active = $request->isActive;
        $badge->save();

        return $data;
    }
}
