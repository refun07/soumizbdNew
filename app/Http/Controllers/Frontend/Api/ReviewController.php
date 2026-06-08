<?php

namespace App\Http\Controllers\Frontend\Api;

use App\Http\Controllers\Common\FileManagerController;
use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ShopReviewResource;
use App\Models\MediaFile;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\SellerImpression;
use App\Models\ShopImpression;
use Illuminate\Http\Request;
use Storage;
use Validator;

class ReviewController extends Controller
{
    # get resources
    public function index(Request $request, $productId)
    {
        $limit      = $request->limit ?? perPage();
        $reviews    = ProductReview::where('product_id', $productId);

        $reviewCountQuery   = clone $reviews;

        $reviews          = $reviews->paginate($limit);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'summary'   => getShopRatings($reviewCountQuery),
                'reviews'   => ReviewResource::collection($reviews)->response()->getData(true)
            ]
        ];
    }

    # all user reviews
    public function allUserReviews(Request $request)
    {
        $limit       = $request->limit ?? perPage();
        $reviews     = ProductReview::where('user_id', apiUserId());
        $shopReviews = ShopImpression::where('user_id', apiUserId())->latest()->paginate($limit);

        $reviews     = $reviews->paginate($limit);

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'reviews'       => ReviewResource::collection($reviews)->response()->getData(true),
                'shopReviews'   => ShopReviewResource::collection($shopReviews)->response()->getData(true)
            ]
        ];
    }

    # store or update review
    public function storeOrUpdate(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'newImages.*' => 'mimes:jpg,jpeg,png,webp|max:4000'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success'   => false,
                'status'    => 403,
                'message'   => translate('Only jpg, jpeg, png and webp images of 4mb size are allowed'),
                'result'    => null
            ], 403);
        }

        $order = Order::findOrFail((int) $request->orderId);

        if ($order) {
            if ($order->delivery_status != "delivered") {
                return response()->json([
                    'success'   => false,
                    'status'    => 403,
                    'message'   => translate('Only delivered order can have reviews'),
                    'result'    => null
                ], 403);
            }

            $review = ProductReview::where('user_id', apiUserId())->where('product_id', $request->productId)->first();
            if (is_null($review)) {
                $product = Product::where('id', $request->productId)->first();
                $review  = new ProductReview;
                $review->product_id = $request->productId;
                $review->user_id    = apiUserId();
                $review->shop_id    = $product->shop_id;
            }
            $review->rating         = $request->rating;
            $review->description    = $request->description;

            // review images
            if (!$request->oldImages) {
                $review->images = null;
            }

            $images = $request->oldImages;

            if ($review->images != null) {
                $reviewImages   = explode(',', $review->images);
                $oldImages      = explode(',', $request->oldImages);
                $deleteImages   = array_diff($reviewImages, $oldImages);

                // delete files which aren't requested
                foreach ($deleteImages as  $deleteImage) {
                    $mediaFile = MediaFile::findOrFail($deleteImage);
                    try {
                        if (env('FILESYSTEM_DRIVER') == 's3') {
                            Storage::disk('s3')->delete($mediaFile->media_file);
                            if (file_exists(public_path() . '/' . $mediaFile->media_file)) {
                                unlink(public_path() . '/' . $mediaFile->media_file);
                            }
                        } else {
                            unlink(public_path() . '/' . $mediaFile->media_file);
                        }
                        $mediaFile->delete();
                    } catch (\Exception $e) {
                        $mediaFile->delete();
                    }
                }
                $images = $request->oldImages;
            }

            if ($request->hasFile('newImages')) {
                $tempImages = [];
                foreach ($request->newImages as $newImage) {

                    $type = array(
                        "jpg" => "image",
                        "jpeg" => "image",
                        "png" => "image",
                        "svg" => "image",
                        "webp" => "image",
                        "gif" => "image",
                        "mp4" => "video",
                        "mpg" => "video",
                        "mpeg" => "video",
                        "webm" => "video",
                        "ogg" => "video",
                        "avi" => "video",
                        "mov" => "video",
                        "flv" => "video",
                        "swf" => "video",
                        "mkv" => "video",
                        "wmv" => "video",
                        "wma" => "audio",
                        "aac" => "audio",
                        "wav" => "audio",
                        "mp3" => "audio",
                        "zip" => "archive",
                        "rar" => "archive",
                        "7z" => "archive",
                        "doc" => "document",
                        "txt" => "document",
                        "docx" => "document",
                        "pdf" => "document",
                        "csv" => "document",
                        "xml" => "document",
                        "ods" => "document",
                        "xlr" => "document",
                        "xls" => "document",
                        "xlsx" => "document"
                    );


                    $mediaFile = new MediaFile;
                    $mediaFile->media_name = null;

                    $arr = explode('.', $newImage->getClientOriginalName());

                    for ($i = 0; $i < count($arr) - 1; $i++) {
                        if ($i == 0) {
                            $mediaFile->media_name .= $arr[$i];
                        } else {
                            $mediaFile->media_name .= "." . $arr[$i];
                        }
                    }

                    $mediaFile->media_file = $newImage->store('uploads/all');
                    $mediaFile->user_id = apiUserId();
                    $mediaFile->media_extension = $newImage->getClientOriginalExtension();
                    if (isset($type[$mediaFile->media_extension])) {
                        $mediaFile->media_type = $type[$mediaFile->media_extension];
                    } else {
                        $mediaFile->media_type = "others";
                    }
                    $mediaFile->media_size = $newImage->getSize();
                    $mediaFile->save();
                    array_push($tempImages, $mediaFile->id);
                }

                $images = explode(',', $images);
                $images = array_merge($tempImages, $images);
                $images = rtrim(implode(',', $images), ',');
            }

            $review->images = $images;
            $review->save();

            return response()->json([
                'success'   => true,
                'status'    => 200,
                'message'   => translate('Review has been submitted successfully'),
                'result'    => [
                    'review'        => new ReviewResource($review),
                ]
            ], 200);
        }
    }

    # show review
    public function show($productId)
    {
        $review     = ProductReview::where('user_id', apiUserId())->where('product_id', $productId)->first();

        if (!is_null($review)) {
            return [
                'success'   => true,
                'status'    => 200,
                'message'   => '',
                'result'    => [
                    'review'        => new ReviewResource($review)
                ]
            ];
        } else {
            return response()->json([
                'success'   => false,
                'status'    => 200,
                'message'   => translate('You did not give any review yet'),
                'result'    => null
            ], 200);
        }

        return response()->json([
            'success'   => false,
            'status'    => 500,
            'message'   => translate('Something went wrong'),
            'result'    => null
        ], 500);
    }

    # show shop review
    public function showShopReview($shopId)
    {

        $shopReview = ShopImpression::where('user_id', apiUserId())->where('shop_id', $shopId)->first();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'shopReview'    => $shopReview ? new ShopReviewResource($shopReview) : null
            ]
        ];
    }

    # storeShopReview
    function storeShopReview(Request $request)
    {
        $review = ShopImpression::where('user_id', apiUserId())->where('shop_id', $request->shopId)->first();
        if (is_null($review)) {
            $review = new ShopImpression;
            $review->user_id = apiUserId();
            $review->shop_id = $request->shopId;
        }
        $review->impression = $request->impression;
        $review->save();

        return [
            'success'   => true,
            'status'    => 200,
            'message'   => '',
            'result'    =>  new ShopReviewResource($review)
        ];
    }
}
