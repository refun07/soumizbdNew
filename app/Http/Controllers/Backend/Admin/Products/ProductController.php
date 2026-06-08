<?php

namespace App\Http\Controllers\Backend\Admin\Products;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ProductService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    # construct
    public function __construct()
    {
        $this->middleware(['permission:view_products'])->only(['index']);
        $this->middleware(['permission:create_products'])->only(['create', 'store', 'import', 'sampleCsv']);
        $this->middleware(['permission:edit_products'])->only(['edit', 'update', 'updateStatus', 'duplicate']);
        $this->middleware(['permission:delete_products'])->only(['destroy']);
    }

    # resources list
    public function index(Request $request)
    {
        $response = ProductService::index($request);

        if ($response['status'] == 200) {
            return view('backend.admin.products.index', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.dashboard');
    }

    # return view of create form
    public function create()
    {
        $response = ProductService::create();
        return view('backend.admin.products.create', $response['result']);
    }

    # generate variation combinations
    public function generateVariationCombinations(Request $request)
    {
        $response = ProductService::generateVariationCombinations($request);
        return view('backend.admin.products.new-variation-combinations', $response['result'])->render();
    }

    # get variation values to add new product
    public function getVariationValues(Request $request)
    {
        $response = ProductService::getVariationValues($request);
        return view('backend.admin.products.new-variation-values', $response['result']);
    }

    # new chosen variation
    public function getNewVariation(Request $request)
    {
        $response = ProductService::getNewVariation($request);
        $variations = $response['result']['variations'];
        $count      = $response['result']['count'];
        $chosenCounter      = $response['result']['chosenCounter'];

        if ($count == -1) {
            return array(
                'count' => $count,
                'chosenCounter' => $chosenCounter,
                'view' => view('backend.admin.products.new-variation', compact('variations', 'chosenCounter'))->render(),
            );
        } else if ($count > 0) {
            return array(
                'count' => $count,
                'chosenCounter' => $chosenCounter,
                'view'  => view('backend.admin.products.new-variation', compact('variations', 'chosenCounter'))->render(),
            );
        } else {
            return false;
        }
    }

    # add new data
    public function store(Request $request)
    {
        $response = ProductService::store($request);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.products.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # import products from CSV
    public function import(Request $request)
    {
        $response = ProductService::import($request);

        if ($response['status'] == 200) {
            flash($response['message'])->success();
        } else {
            flash($response['message'] ?? translate('Something went wrong'))->error();
        }

        return redirect()->route('admin.products.index');
    }

    # download product import sample CSV
    public function sampleCsv()
    {
        return ProductService::sampleCsv();
    }

    # return view of edit form
    public function edit(Request $request, $id)
    {
        $response = ProductService::edit($request, $id);
        if ($response['status'] == 200) {
            return view('backend.admin.products.edit', $response['result']);
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.products.index');
    }

    # update data
    public function update(Request $request, $id)
    {
        $response = ProductService::update($request, $id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return back();
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return back();
    }

    # update status
    public function updateStatus(Request $request)
    {
        $response = ProductService::updateStatus($request);
        return $response;
    }

    # duplicate data
    public function duplicate($id)
    {
        $response = ProductService::duplicate($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.products.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();
        return redirect()->route('admin.products.index');
    }

    # delete data
    public function destroy($id)
    {
        $response = ProductService::destroy($id);
        if ($response['status'] == 200) {
            flash($response['message'])->success();
            return redirect()->route('admin.products.index');
        }

        flash($response['message'] ?? translate('Something went wrong'))->error();

        return redirect()->route('admin.products.index');
    }

    # show QR code page for product
    public function showQr($id)
    {
        if (getSetting('productSettingQrCodeEnabled', '1') === '0') {
            abort(404);
        }

        $product = Product::with([
            'productTranslations',
            'variations'
        ])->find($id);

        if (!$product) {
            \Log::error('Product QR view failed - product not found', ['id' => $id]);
            abort(404);
        }

        return view('frontend.product-qr', compact('product'));
    }

    # generate QR image for download
    public function generateQrImage($id)
    {
        if (getSetting('productSettingQrCodeEnabled', '1') === '0') {
            abort(404);
        }

        $product = Product::find($id);

        if (!$product) {
            \Log::error('Product QR image generation failed - product not found', ['id' => $id]);
            abort(404);
        }

        $qrUrl = route('products.qr', ['id' => $id]);
        
        # Get custom color from qr_data or default to black
        $qrData = $product->qr_data ?? [];
        $color = $qrData['color'] ?? '#000000';
        
        # Parse hex color to RGB
        $color = ltrim($color, '#');
        if (strlen($color) == 3) {
            $color = $color[0] . $color[0] . $color[1] . $color[1] . $color[2] . $color[2];
        }
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));

        $qrCode = new QrCode(
            data: $qrUrl,
            size: 300,
            margin: 10,
            foregroundColor: new \Endroid\QrCode\Color\Color($r, $g, $b)
        );

        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return response($result->getString(), 200)
            ->header('Content-Type', $result->getMimeType())
            ->header('Content-Disposition', 'attachment; filename="product-qr-' . $id . '.png"');
    }

    # get quick edit data for modal
    public function getQuickEditData($id)
    {
        $response = ProductService::getQuickEditData($id);
        return response()->json($response);
    }

    # quick update product data
    public function quickUpdate(Request $request, $id)
    {
        $response = ProductService::quickUpdate($request, $id);
        return response()->json($response);
    }
}
