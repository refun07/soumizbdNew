<?php

namespace App\Http\Controllers\Backend\Seller\ShopSettings;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ShopSection;
use Illuminate\Http\Request;

class ShopSectionController extends Controller
{
    # constructor
    public function __construct()
    {
    }

    # Display a listing of the resource.
    public function index(Request $request)
    {
        $shopSections  = ShopSection::shop()->orderBy('order', 'ASC')->get();
        return view('backend.seller.shop-settings.sections.index', compact('shopSections'));
    }

    # add new data
    public function store(Request $request)
    {
        $shopSection = new ShopSection;
        $shopSection->shop_id   = shopId();
        $shopSection->type      = $request->type;
        $shopSection->order     = $request->order;
        if ($request->title) {
            $shopSection->title  = $request->title;
        }
        $shopSection->save();

        flash(translate('Section has been added successfully'))->success();
        return redirect()->route('seller.shop-sections.index');
    }

    # edit resource
    public function edit($id)
    {
        $shopSection    = ShopSection::findOrFail($id);
        $products       = collect();
        if ($shopSection->type == "products") {
            $products       = Product::shop()->isPublished()->get(['id', 'name']);
        }
        return view('backend.seller.shop-settings.sections.edit', compact('shopSection', 'products'));
    }

    # update resource
    public function update(Request $request, $id)
    {
        $shopSection         = ShopSection::findOrFail($id);

        $shopSection->order  = $request->order;
        $shopSection->title  = $request->title;

        $tempShopSection    = new ShopSection;

        switch ($shopSection->type) {
            case 'full-width-banner':

                $tempShopSection->link = '#';
                if ($request->link) {
                    $tempShopSection->link = $request->link;
                }

                $tempShopSection->banners = null;
                if ($request->banners) {
                    $tempShopSection->banners = $request->banners;
                }
                break;

            case 'boxed-width-banner':
                $tempShopSection->box_1_link = '#';
                if ($request->box_1_link) {
                    $tempShopSection->box_1_link = $request->box_1_link;
                }
                $tempShopSection->box_1_banners = null;
                if ($request->box_1_banners) {
                    $tempShopSection->box_1_banners = $request->box_1_banners;
                }

                $tempShopSection->box_2_link = '#';
                if ($request->box_2_link) {
                    $tempShopSection->box_2_link = $request->box_2_link;
                }
                $tempShopSection->box_2_banners = null;
                if ($request->box_2_banners) {
                    $tempShopSection->box_2_banners = $request->box_2_banners;
                }
                break;

            default:
                $tempShopSection->products = $request->productIds;
                break;
        }

        $shopSection->section_values = json_encode($tempShopSection);
        $shopSection->save();
        flash(translate('Section has been updated successfully'))->success();
        return redirect()->route('seller.shop-sections.index');
    }

    # Remove the specified resource from storage.
    public function destroy($id)
    {
        $shopSection = ShopSection::findOrFail($id);
        $shopSection->delete();
        flash(translate('Section has been deleted successfully'))->success();
        return redirect()->route('seller.shop-sections.index');
    }

    # profile
    public function profile()
    {
        $shop = shop();
        return view('backend.seller.shop-settings.appearances.profile', compact('shop'));
    }

    # update profile
    public function updateProfile(Request $request)
    {
        $shop = shop();

        $shop->name                     = $request->name;
        $shop->tagline                  = $request->tagline;
        $shop->logo                     = $request->logo;
        $shop->banner                   = $request->banner;
        $shop->manage_stock_by          = $request->manage_stock_by;
        $shop->default_shipping_charge  = $request->default_shipping_charge ?? 0;
        $shop->meta_title               = $request->meta_title;
        $shop->meta_description         = $request->meta_description;
        $shop->meta_keywords            = $request->meta_keywords;
        $shop->meta_image               = $request->meta_image;
        $shop->save();

        flash(translate('Shop profile has been updated successfully'))->success();
        return redirect()->route('seller.shops.profile');
    }
}
