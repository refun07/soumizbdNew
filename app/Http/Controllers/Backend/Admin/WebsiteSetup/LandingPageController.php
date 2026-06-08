<?php

namespace App\Http\Controllers\Backend\Admin\WebsiteSetup;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\WebsiteLanding;
use App\Models\WebsiteLandingSection;
use App\Models\MediaFile;
use App\Models\Product;
use Illuminate\Http\Request;
use Str;

class LandingPageController extends Controller
{
    // constructor
    public function __construct()
    {
        $this->middleware(['permission:view_pages'])->only('index');
        $this->middleware(['permission:create_pages'])->only(['create', 'store']);
        $this->middleware(['permission:edit_pages'])->only(['edit', 'update', 'configure']);
        $this->middleware(['permission:delete_pages'])->only('destroy');
    }

    # Display a listing of the resource.
    public function index(Request $request)
    {
        try {
            if ($request->expectsJson()) {
                $landingPages = WebsiteLanding::withCount('sections')->get()->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'status' => $page->status,
                        'sections_count' => $page->sections_count,
                        'created_at' => $page->created_at->format('Y-m-d H:i:s'),
                        'updated_at' => $page->updated_at->format('Y-m-d H:i:s'),
                    ];
                });

                return response()->json([
                    'data' => $landingPages,
                    'message' => 'Landing pages retrieved successfully'
                ]);
            }

            $landingPages = WebsiteLanding::withCount('sections')->get();
            return view('backend.admin.website-setup.landing-pages.index', compact('landingPages'));
        } catch (\Exception $e) {
            \Log::error('Landing page index error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'data' => [],
                    'message' => 'Error loading landing pages'
                ], 500);
            }
            return view('backend.admin.website-setup.landing-pages.index', ['landingPages' => collect()]);
        }
    }

    # create form
    public function create()
    {
        return view('backend.admin.website-setup.landing-pages.create');
    }

    # add new data
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:website_landings,slug',
                'status' => 'required|in:draft,published',
            ]);

            $landingPage = new WebsiteLanding;
            $landingPage->title = $request->title;
            $landingPage->slug = Str::slug($request->slug);
            $landingPage->status = $request->status;
            $landingPage->seo_data = [
                'meta_title' => $request->meta_title,
                'meta_description' => $request->meta_description,
                'meta_keywords' => $request->meta_keywords,
                'meta_image' => $request->meta_image,
            ];
            $landingPage->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => translate('Landing page has been created successfully'),
                    'data' => $landingPage
                ]);
            }

            flash(translate('Landing page has been created successfully'))->success();
            return redirect()->route('admin.landing-pages.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Landing page store error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to create landing page'
                ], 500);
            }
            flash(translate('Failed to create landing page'))->error();
            return back();
        }
    }

    # edit resource
    public function edit(Request $request, $id)
    {
        $landingPage = WebsiteLanding::findOrFail($id);
        return view('backend.admin.website-setup.landing-pages.edit', compact('landingPage'));
    }

    # update resource
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:website_landings,slug,' . $id,
            'status' => 'required|in:draft,published',
        ]);

        $landingPage = WebsiteLanding::findOrFail($id);
        $landingPage->title = $request->title;
        $landingPage->slug = Str::slug($request->slug);
        $landingPage->status = $request->status;
        $landingPage->seo_data = [
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'meta_image' => $request->meta_image,
        ];
        $landingPage->save();

        flash(translate('Landing page has been updated successfully'))->success();
        return back();
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $landingPage = WebsiteLanding::findOrFail($id);
        $landingPage->sections()->delete();
        $landingPage->delete();

        flash(translate('Landing page has been deleted successfully'))->success();
        return redirect()->route('admin.landing-pages.index');
    }

    # Update landing page status
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:draft,published',
            ]);

            $landingPage = WebsiteLanding::findOrFail($id);
            $landingPage->status = $request->status;
            $landingPage->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => translate('Landing page status updated successfully'),
                    'status' => $landingPage->status
                ]);
            }

            flash(translate('Landing page status updated successfully'))->success();
            return back();
        } catch (\Exception $e) {
            \Log::error('Landing page status update error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update landing page status'
                ], 500);
            }
            flash(translate('Failed to update landing page status'))->error();
            return back();
        }
    }

    # configure landing page
    public function configure(Request $request, $id)
    {
        $lang_key = $request->lang_key;
        $language = Language::isActive()->where('code', $lang_key)->first();
        if (!$language) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Language you are trying to translate is not available or not active'
                ], 400);
            }
            flash(translate('Language you are trying to translate is not available or not active'))->error();
            return redirect()->route('admin.landing-pages.index');
        }
        
        $landingPage = WebsiteLanding::with('sections')->findOrFail($id);
        $theme = getSetting('defaultTheme') ?? 'theme-one';
        
        if ($request->expectsJson()) {
            $landingPageData = $landingPage->toArray();
            // Ensure selected_products is included and properly formatted
            $rawSelectedProducts = $landingPage->getRawOriginal('selected_products');
            if ($rawSelectedProducts) {
                $landingPageData['selected_products'] = json_decode($rawSelectedProducts, true);
            } else {
                $landingPageData['selected_products'] = [];
            }

            return response()->json([
                'landingPage' => $landingPageData,
                'theme' => $theme,
                'lang_key' => $lang_key,
                'facebookPixelActive' => config('app.FACEBOOK_PIXEL_ACTIVATION') == '1',
                'metaPixelId' => config('app.META_PIXEL_ID'),
                'currency' => [
                    'symbol' => getSetting('currencySymbol') ?? '$',
                    'code' => getSetting('currencyCode') ?? 'USD',
                ],
            ]);
        }
        
        return view('backend.admin.website-setup.landing-pages.configure', compact('landingPage', 'lang_key', 'theme'));
    }

    /**
     * Store a new section for the landing page
     */
    public function storeSection(Request $request, $landingId)
    {
        try {
            $request->validate([
                'type' => 'required|string',
                'section_data' => 'required|array',
                // Inline text color fields (null = auto contrast)
                'section_data.product_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_offer_badge_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_old_price_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_new_price_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.offer_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.offer_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.offer_badge_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.old_price_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.new_price_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.centered_offer_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.centered_offer_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_question_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_answer_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_section_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_product_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_product_description_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product1_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product1_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product1_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product1_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product2_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product2_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product2_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product2_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product3_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product3_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product3_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product3_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.highlights_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.benefit_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.benefit_description_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.highlights_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.highlights_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.feature_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_about_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_about_description_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_about_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_about_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.carousel_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'order' => 'required|integer',
                'is_visible' => 'required|boolean',
            ]);

            $landingPage = WebsiteLanding::findOrFail($landingId);

            $section = new WebsiteLandingSection;
            $section->landing_id = $landingPage->id;
            $section->type = $request->type;
            $section->section_data = $request->section_data;
            $section->order = $request->order;
            $section->is_visible = $request->is_visible;
            $section->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => translate('Section has been created successfully'),
                    'section' => $section
                ]);
            }

            flash(translate('Section has been created successfully'))->success();
            return back();
        } catch (\Exception $e) {
            \Log::error('Landing page section store error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to create section'
                ], 500);
            }
            flash(translate('Failed to create section'))->error();
            return back();
        }
    }

    /**
     * Update a section for the landing page
     */
    public function updateSection(Request $request, $landingId, $sectionId)
    {
        try {
            $request->validate([
                'type' => 'required|string',
                'section_data' => 'required|array',
                // Inline text color fields (null = auto contrast)
                'section_data.product_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_offer_badge_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_old_price_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_new_price_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.offer_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.offer_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.offer_badge_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.old_price_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.new_price_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.centered_offer_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.centered_offer_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_question_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_answer_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.faq_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_section_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_product_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_product_description_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.featured_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product1_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product1_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product1_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product1_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product2_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product2_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product2_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product2_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product3_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product3_subtitle_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product3_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.product3_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.highlights_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.benefit_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.benefit_description_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.highlights_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.highlights_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.feature_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_about_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_about_description_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_about_button_text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.quality_about_button_bg_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'section_data.carousel_title_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}([0-9A-Fa-f]{2})?$/',
                'order' => 'required|integer',
                'is_visible' => 'required|boolean',
            ]);

            $landingPage = WebsiteLanding::findOrFail($landingId);
            $section = WebsiteLandingSection::where('landing_id', $landingPage->id)->findOrFail($sectionId);

            $section->type = $request->type;
            $section->section_data = $request->section_data;
            $section->order = $request->order;
            $section->is_visible = $request->is_visible;
            $section->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => translate('Section has been updated successfully'),
                    'section' => $section
                ]);
            }

            flash(translate('Section has been updated successfully'))->success();
            return back();
        } catch (\Exception $e) {
            \Log::error('Landing page section update error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update section'
                ], 500);
            }
            flash(translate('Failed to update section'))->error();
            return back();
        }
    }

    /**
     * Delete a section from the landing page
     */
    public function deleteSection(Request $request, $landingId, $sectionId)
    {
        try {
            $landingPage = WebsiteLanding::findOrFail($landingId);
            $section = WebsiteLandingSection::where('landing_id', $landingPage->id)->findOrFail($sectionId);

            $section->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => translate('Section has been deleted successfully')
                ]);
            }

            flash(translate('Section has been deleted successfully'))->success();
            return back();
        } catch (\Exception $e) {
            \Log::error('Landing page section delete error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to delete section'
                ], 500);
            }
            flash(translate('Failed to delete section'))->error();
            return back();
        }
    }

    /**
     * Get media files by IDs (for landing page image selection)
     */
    public function getMediaFiles(Request $request)
    {
        try {
            $ids = explode(',', $request->ids);
            $files = MediaFile::whereIn('id', $ids)->get();

            return response()->json($files);
        } catch (\Exception $e) {
            \Log::error('Get media files error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to get media files'
            ], 500);
        }
    }

    /**
     * Reorder sections for the landing page
     */
    public function reorderSections(Request $request, $landingId)
    {
        try {
            $request->validate([
                'sections' => 'required|array',
                'sections.*.id' => 'required|integer',
                'sections.*.order' => 'required|integer',
            ]);

            $landingPage = WebsiteLanding::findOrFail($landingId);

            foreach ($request->sections as $sectionData) {
                $section = WebsiteLandingSection::where('landing_id', $landingPage->id)
                    ->where('id', $sectionData['id'])
                    ->first();

                if ($section) {
                    $section->order = $sectionData['order'];
                    $section->save();
                }
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => translate('Sections reordered successfully')
                ]);
            }

            flash(translate('Sections reordered successfully'))->success();
            return back();
        } catch (\Exception $e) {
            \Log::error('Landing page sections reorder error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to reorder sections'
                ], 500);
            }
            flash(translate('Failed to reorder sections'))->error();
            return back();
        }
    }

    /**
     * Update selected products for the landing page
     */
    public function updateSelectedProducts(Request $request, $id)
    {
        try {
            $request->validate([
                'selected_products' => 'nullable|array',
                'selected_products.*' => 'integer',
            ]);

            $landingPage = WebsiteLanding::findOrFail($id);

            $landingPage->selected_products = $request->selected_products ?? [];
            $landingPage->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => translate('Selected products updated successfully'),
                    'selected_products' => $landingPage->selected_products
                ]);
            }

            flash(translate('Selected products updated successfully'))->success();
            return back();
        } catch (\Exception $e) {
            \Log::error('Landing page selected products update error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update selected products'
                ], 500);
            }
            flash(translate('Failed to update selected products'))->error();
            return back();
        }
    }

    /**
     * Update page configuration (colors, global styles) for the landing page
     */
    public function updatePageConfigs(Request $request, $id)
    {
        try {
            $request->validate([
                'page_configs' => 'required|array',
                'page_configs.theme_color' => 'nullable|string|max:20',
                'page_configs.primary_color' => 'nullable|string|max:20',
                'page_configs.facebook_pixel_enabled' => 'nullable|boolean',
                'page_configs.payment_methods' => 'nullable|array',
                'page_configs.payment_methods.*' => 'string',
            ]);

            $landingPage = WebsiteLanding::findOrFail($id);

            // Merge with existing page_configs
            $existingConfigs = $landingPage->page_configs ?? [];
            $landingPage->page_configs = array_merge($existingConfigs, $request->page_configs);
            $landingPage->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => translate('Page configuration updated successfully'),
                    'page_configs' => $landingPage->page_configs
                ]);
            }

            flash(translate('Page configuration updated successfully'))->success();
            return back();
        } catch (\Exception $e) {
            \Log::error('Landing page page configs update error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update page configuration'
                ], 500);
            }
            flash(translate('Failed to update page configuration'))->error();
            return back();
        }
    }

    /**
     * Get products for landing page selection
     */
    public function getProducts(Request $request)
    {
        try {
            $search = $request->search ?? '';
            $ids = $request->ids;
            $limit = $request->limit ?? 10;
            $currentLocale = app()->getLocale();

            \Log::info('Get products request', ['search' => $search, 'locale' => $currentLocale, 'ids' => $ids]);

            // If specific IDs are requested, fetch those products
            if ($ids) {
                $productIds = is_string($ids) ? explode(',', $ids) : $ids;
                    $products = Product::isPublished()
                    ->hideOnEcom()
                    ->fromPublishedShops()
                    ->with(['productTranslations', 'variations.productVariationStocks', 'variations.combinations.variationValue.variationValueTranslations'])
                    ->whereIn('id', $productIds)
                    ->orderBy('id', 'desc')
                    ->get()
                    ->map(function ($product) {
                        $firstVariation = $product->variations->first();
                        
                        // Calculate total stock for each variation from all warehouses
                        $variationsWithStock = $product->variations->map(function($v) use ($product) {
                            $totalStock = $v->productVariationStocks->sum('stock_qty');
                            
                            // Build variation name from combinations
                            $variationNames = [];
                            foreach ($v->combinations as $combination) {
                                if ($combination->variationValue) {
                                    $variationNames[] = $combination->variationValue->collectTranslation('name');
                                }
                            }
                            $variationName = !empty($variationNames) ? implode(' - ', $variationNames) : 'Variant #' . $v->id;
                            
                            return [
                                'id' => $v->id,
                                'name' => $variationName,
                                'price' => variationPrice($product, $v),
                                'stock' => $totalStock,
                                'total_stock' => $totalStock,
                            ];
                        });
                        
                        // Filter variations with stock > 0
                        $availableVariations = $variationsWithStock->filter(function($v) {
                            return $v['total_stock'] > 0;
                        })->values()->toArray();
                        
                        return [
                            'id' => $product->id,
                            'name' => $product->collectTranslation('name'),
                            'sku' => $product->sku,
                            'image' => uploadedAsset($product->thumbnail_image),
                            'price' => $firstVariation ? variationPrice($product, $firstVariation) : $product->price,
                            'variation_id' => $firstVariation?->id ?? null,
                            'variations' => $availableVariations,
                            'has_variations' => $product->variations->count() > 1,
                            'shop_id' => $product->shop_id,
                            'shop_name' => $product->shop?->name ?? 'Default Seller',
                        ];
                    });

                return response()->json([
                    'success' => true,
                    'data' => $products
                ]);
            }

            // Regular search/fetch
            $query = Product::isPublished()
                ->hideOnEcom()
                ->fromPublishedShops()
                ->with(['productTranslations', 'variations.productVariationStocks', 'variations.combinations.variationValue.variationValueTranslations']);

            // Use OR conditions for search - don't filter by locale initially
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    // Search in translation name OR direct product name
                    $q->whereHas('productTranslations', function ($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    })
                    ->orWhere('name', 'like', '%' . $search . '%');
                });
            }

            $products = $query->orderBy('id', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($product) {
                    $firstVariation = $product->variations->first();
                    
                    // Calculate total stock for each variation from all warehouses
                    $variationsWithStock = $product->variations->map(function($v) use ($product) {
                        $totalStock = $v->productVariationStocks->sum('stock_qty');
                        
                        // Build variation name from combinations
                        $variationNames = [];
                        foreach ($v->combinations as $combination) {
                            if ($combination->variationValue) {
                                $variationNames[] = $combination->variationValue->collectTranslation('name');
                            }
                        }
                        $variationName = !empty($variationNames) ? implode(' - ', $variationNames) : 'Variant #' . $v->id;
                        
                        return [
                            'id' => $v->id,
                            'name' => $variationName,
                            'price' => variationPrice($product, $v),
                            'stock' => $totalStock,
                            'total_stock' => $totalStock,
                        ];
                    });
                    
                    // Filter variations with stock > 0
                    $availableVariations = $variationsWithStock->filter(function($v) {
                        return $v['total_stock'] > 0;
                    })->values()->toArray();
                    
                    return [
                        'id' => $product->id,
                        'name' => $product->collectTranslation('name'),
                        'sku' => $product->sku,
                        'image' => uploadedAsset($product->thumbnail_image),
                        'price' => $firstVariation ? variationPrice($product, $firstVariation) : $product->price,
                        'variation_id' => $firstVariation?->id ?? null,
                        'variations' => $availableVariations,
                        'has_variations' => $product->variations->count() > 1,
                        'shop_id' => $product->shop_id,
                        'shop_name' => $product->shop?->name ?? 'Default Seller',
                    ];
                });

            \Log::info('Get products response', ['count' => $products->count()]);

            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } catch (\Exception $e) {
            \Log::error('Get products error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get products'
            ], 500);
        }
    }

    /**
     * Get available coupons for landing page selection
     */
    public function getCoupons(Request $request)
    {
        try {
            $search = $request->search ?? '';
            $limit = $request->limit ?? 20;

            $query = \App\Models\Coupon::isActive()
                ->where(function ($q) use ($search) {
                    if (!empty($search)) {
                        $q->where('code', 'like', '%' . $search . '%')
                          ->orWhere('info', 'like', '%' . $search . '%');
                    }
                });

            $coupons = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($coupon) {
                    return [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'info' => $coupon->info,
                        'discount_type' => $coupon->discount_type,
                        'discount_value' => $coupon->discount_value,
                        'is_free_shipping' => $coupon->is_free_shipping,
                        'start_date' => $coupon->start_date,
                        'end_date' => $coupon->end_date,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $coupons
            ]);
        } catch (\Exception $e) {
            \Log::error('Get coupons error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get coupons'
            ], 500);
        }
    }

    /**
     * Update the coupon for a landing page
     */
    public function updateCoupon(Request $request, $id)
    {
        try {
            $request->validate([
                'coupon_id' => ['nullable', 'integer', 'exists:coupons,id'],
            ]);

            $landingPage = WebsiteLanding::findOrFail($id);
            $landingPage->coupon_id = $request->coupon_id ?: null;
            $landingPage->save();

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => translate('Landing page coupon updated successfully'),
                    'coupon_id' => $landingPage->coupon_id
                ]);
            }

            flash(translate('Landing page coupon updated successfully'))->success();
            return back();
        } catch (\Exception $e) {
            \Log::error('Landing page coupon update error: ' . $e->getMessage());
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Failed to update landing page coupon'
                ], 500);
            }
            flash(translate('Failed to update landing page coupon'))->error();
            return back();
        }
    }

    /**
     * Get globally enabled payment methods for landing page configuration
     */
    public function getPaymentMethods()
    {
        try {
            $methods = [];

            if (getSetting('cod_activation') == 1) {
                $methods[] = [
                    'value' => 'cash_on_delivery',
                    'name' => translate('Cash On Delivery'),
                ];
            }

            if (getSetting('card_activation') == 1) {
                $methods[] = [
                    'value' => 'card_on_delivery',
                    'name' => translate('Card On Delivery'),
                ];
            }

            if (getSetting('paypal_activation') == 1) {
                $methods[] = [
                    'value' => 'paypal',
                    'name' => translate('Paypal'),
                ];
            }

            if (getSetting('stripe_activation') == 1) {
                $methods[] = [
                    'value' => 'stripe',
                    'name' => translate('Stripe'),
                ];
            }

            if (getSetting('sslcommerz_activation') == 1) {
                $methods[] = [
                    'value' => 'sslcommerz',
                    'name' => translate('SSLCommerz'),
                ];
            }

            if (getSetting('bkash_activation') == 1) {
                $methods[] = [
                    'value' => 'bkash',
                    'name' => translate('bKash'),
                ];
            }

            if (getSetting('flutterwave_activation') == 1) {
                $methods[] = [
                    'value' => 'flutterwave',
                    'name' => translate('Flutterwave'),
                ];
            }

            if (getSetting('paytm_activation') == 1) {
                $methods[] = [
                    'value' => 'paytm',
                    'name' => translate('Paytm'),
                ];
            }

            if (getSetting('paystack_activation') == 1) {
                $methods[] = [
                    'value' => 'paystack',
                    'name' => translate('Paystack'),
                ];
            }

            if (getSetting('coingate_activation') == 1) {
                $methods[] = [
                    'value' => 'coingate',
                    'name' => translate('CoinGate'),
                ];
            }

            if (getSetting('iyzico_activation') == 1) {
                $methods[] = [
                    'value' => 'iyzico',
                    'name' => translate('Iyzico'),
                ];
            }

            if (getSetting('razorpay_activation') == 1) {
                $methods[] = [
                    'value' => 'razorpay',
                    'name' => translate('Razorpay'),
                ];
            }

            if (getSetting('instamojo_activation') == 1) {
                $methods[] = [
                    'value' => 'instamojo',
                    'name' => translate('Instamojo'),
                ];
            }

            if (getSetting('aamarpay_activation') == 1) {
                $methods[] = [
                    'value' => 'aamarpay',
                    'name' => translate('Aamarpay'),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $methods
            ]);
        } catch (\Exception $e) {
            \Log::error('Get payment methods error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get payment methods'
            ], 500);
        }
    }
}