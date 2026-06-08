<?php

use App\Http\Controllers\Backend\Admin\Configurations\LanguageController;
use App\Http\Controllers\Frontend\Api\AuthController;
use App\Http\Controllers\Frontend\Api\BrandController;
use App\Http\Controllers\Frontend\Api\CampaignController;
use App\Http\Controllers\Frontend\Api\CartController;
use App\Http\Controllers\Frontend\Api\CategoryController;
use App\Http\Controllers\Frontend\Api\CheckoutController;
use App\Http\Controllers\Frontend\Api\ChatController;
use App\Http\Controllers\Frontend\Api\CouponController;
use App\Http\Controllers\Frontend\Api\HomepageController;
use App\Http\Controllers\Frontend\Api\HomePageThemeOneController;
use App\Http\Controllers\Frontend\Api\HomePageThemeThreeController;
use App\Http\Controllers\Frontend\Api\HomePageThemeTwoController;
use App\Http\Controllers\Frontend\Api\HomepageSectionsController;
use App\Http\Controllers\Frontend\Api\OrderController;
use App\Http\Controllers\Frontend\Api\PasswordResetController;
use App\Http\Controllers\Frontend\Api\ProductController;
use App\Http\Controllers\Frontend\Api\ReviewController;
use App\Http\Controllers\Frontend\Api\SearchController;
use App\Http\Controllers\Frontend\Api\SellerController;
use App\Http\Controllers\Frontend\Api\ShopController;
use App\Http\Controllers\Frontend\Api\SubscribeController;
use App\Http\Controllers\Frontend\Api\UserController;
use App\Http\Controllers\Frontend\Api\WishlistController;
use App\Http\Controllers\Frontend\Api\OTPController;
use App\Http\Controllers\Installation\InstallController;
use App\Http\Controllers\Frontend\Api\BlogController;
use App\Http\Controllers\Frontend\Api\StockRequestApiController;
use App\Http\Controllers\Frontend\Api\TicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\Api\DeliveryPartner\CourierOrderController;
use App\Http\Controllers\Backend\Api\Translation\TranslationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(['prefix' => config('app.api_version')], function () {

    # translate strings APIs
    Route::post('/insert-translations-keys', [LanguageController::class, 'insertTranslationKeysToFile']);

    #OTP routes
    Route::post('/send-otp', [OTPController::class, 'sendOTP']);
    Route::post('/verify-otp', [OTPController::class, 'verifyOtpForGuest']);
    Route::post('/resend-otp', [OTPController::class, 'resendOtp']);

    # installation APIs
    Route::group(['prefix' => 'installation'], function () {
        Route::get('/checklist', [InstallController::class, 'checklist'])->name('installation.checklist');

        Route::post('/database-setup', [InstallController::class, 'storeDatabaseSetup'])->name('installation.storeDbSetup');
        Route::post('/run-db-migration', [InstallController::class, 'runDbMigration'])->name('installation.runMigration');

        Route::post('/admin-configuration', [InstallController::class, 'storeAdmin'])->name('installation.storeAdmin');
    });

    # Auth routes
    Route::group(['prefix' => 'auth'], function () {

        Route::group(['middleware' => ['zone']], function () {
            Route::post('login', [AuthController::class, 'login']);
            Route::post('signup', [AuthController::class, 'signup']);
            Route::post('verify', [AuthController::class, 'verify']);
        });

        Route::post('resend-code', [AuthController::class, 'resend_code']);
        Route::post('verify/code', [AuthController::class, 'verifyCode']);
        Route::post('password/reset', [PasswordResetController::class, 'reset']);

        // Phone-based authentication routes
        Route::post('verify-phone-otp', [AuthController::class, 'verifyPhoneOtp']);
        Route::post('resend-phone-otp', [AuthController::class, 'resendPhoneOtp']);
        Route::post('password/reset-phone-otp', [AuthController::class, 'sendPasswordResetOtp']);
        Route::post('password/reset-phone', [AuthController::class, 'resetPasswordWithPhone']);

        Route::group(['middleware' => ['auth:sanctum', 'zone']], function () {
            Route::post('user', [AuthController::class, 'user']);
        });

        Route::group(['middleware' => ['auth:sanctum', 'zone']], function () {
            Route::get('logout', [AuthController::class, 'logout']);
        });
    });
    // delivery partner
    Route::post('/courier/order', [CourierOrderController::class, 'store']);
    Route::post('/courier/bulk-order', [CourierOrderController::class, 'bulkStore']);
    // Endpoints specifically for courier area data
    Route::get('/courier/pathao/cities', [CourierOrderController::class, 'getCities']);
    Route::get('/courier/pathao/zones/{cityId}', [CourierOrderController::class, 'getZones']);
    Route::get('/courier/pathao/areas/{zoneId}', [CourierOrderController::class, 'getAreas']);
    Route::get('/courier/redx/areas', [CourierOrderController::class, 'getRedxAreas']);
    Route::get('/courier/status', [CourierOrderController::class, 'getDeliveryStatus']);


    #categories
    Route::get('categories', [CategoryController::class, 'categories']);

    # homepage
    Route::group(['prefix' => 'homepage'], function () {
        # theme-one
        Route::group(['prefix' => 'theme-one'], function () {
            Route::get('/banner', [HomePageThemeOneController::class, 'banner']);
            Route::get('/sliders', [HomePageThemeOneController::class, 'sliders']);
            Route::get('/featured-categories', [HomePageThemeOneController::class, 'featuredCategories']);
            Route::get('/top-products', [HomePageThemeOneController::class, 'topProducts'])->middleware('zone');
            Route::get('/single-image-banner', [HomePageThemeOneController::class, 'singleImageBanner']);
            Route::get('/flash-sale-products', [HomePageThemeOneController::class, 'flashSaleProducts'])->middleware('zone');
            Route::get('/four-image-banner', [HomePageThemeOneController::class, 'fourImageBanner']);
            Route::get('/category-products', [HomePageThemeOneController::class, 'categoryProducts'])->middleware('zone');
            Route::get('/bottom-image-banner', [HomePageThemeOneController::class, 'bottomImageBanner']);
        });

        # theme-two
        Route::group(['prefix' => 'theme-two'], function () {
            Route::get('/banner', [HomePageThemeTwoController::class, 'banner']);
            Route::get('/featured-categories', [HomePageThemeTwoController::class, 'featuredCategories']);
            Route::get('/category-products', [HomePageThemeTwoController::class, 'categoryProducts'])->middleware('zone');
            Route::get('/three-image-banner', [HomePageThemeTwoController::class, 'threeImageBanner']);
            Route::get('/flash-sale-products', [HomePageThemeTwoController::class, 'flashSaleProducts'])->middleware('zone');
            Route::get('/four-image-banner', [HomePageThemeTwoController::class, 'fourImageBanner']);
            Route::get('/about-us', [HomePageThemeTwoController::class, 'aboutUsSection']);
            Route::get('/trendy-products', [HomePageThemeTwoController::class, 'trendyProducts'])->middleware('zone');
            Route::get('/reviews', [HomePageThemeTwoController::class, 'reviewSection']);
        });

        # theme-three
        Route::group(['prefix' => 'theme-three'], function () {
            Route::get('/sliders', [HomePageThemeThreeController::class, 'sliders']);
            Route::get('/featured-categories', [HomePageThemeThreeController::class, 'featuredCategories']);
            Route::get('/featured-products', [HomePageThemeThreeController::class, 'featuredProducts'])->middleware('zone');
            Route::get('/popular-categories', [HomePageThemeThreeController::class, 'popularCategories']);
            Route::get('/two-image-banner', [HomePageThemeThreeController::class, 'twoImageBanner']);
            Route::get('/flash-sale-products', [HomePageThemeThreeController::class, 'flashSaleProducts'])->middleware('zone');
            Route::get('/category-products', [HomePageThemeThreeController::class, 'categoryProducts'])->middleware('zone');
            Route::get('/bottom-two-image-banner', [HomePageThemeThreeController::class, 'bottomTwoImageBanner']);
        });
    });

    Route::group(['prefix' => 'homepage'], function () {
        Route::get('/new-arrivals', [HomepageController::class, 'newArrivals'])->middleware('zone');
        Route::get('/best-sellers', [HomepageController::class, 'bestSellers'])->middleware('zone');
        Route::get('/highest-rated', [HomepageController::class, 'highestRated'])->middleware('zone');
        Route::get('/featured-brands', [HomepageController::class, 'featuredBrands']);
        Route::get('/footer', [HomepageController::class, 'footer']);
        Route::get('/homepage-popup', [HomepageController::class, 'homepagePopup']);
    });

    # pages
    Route::get('/pages/{slug}', [HomepageController::class, 'pageDetails'])->name('api.pages.show');

    # seller registration
    Route::post('/seller/signup', [SellerController::class, 'store']);

    # search
    Route::group(['prefix' => 'search', 'middleware' => ['zone']], function () {
        Route::get('/', [SearchController::class, 'index']);
    });

    # products
    Route::group(['prefix' => 'products', 'middleware' => ['zone']], function () {
        Route::get('/', [ProductController::class, 'index']);
        Route::get('/details/{slug}', [ProductController::class, 'show'])->name('api.products.show');
    });

    # stock requests
    Route::group(['prefix' => 'stock-requests'], function () {
        Route::post('/', [StockRequestApiController::class, 'store']);
        Route::get('/check', [StockRequestApiController::class, 'check']);
    });

    # brands
    Route::group(['prefix' => 'brands', 'middleware' => ['zone']], function () {
        Route::get('/', [BrandController::class, 'index']);
    });

    # shops
    Route::group(['prefix' => 'shops'], function () {
        Route::get('/', [ShopController::class, 'index']);
        Route::get('/details/{slug}', [ShopController::class, 'show'])->name('api.shops.show');

        # shop home sections
        Route::get('/sections/{slug}', [ShopController::class, 'shopSections'])->middleware('zone');

        # shop profile info
        Route::get('/profile/{slug}', [ShopController::class, 'profile'])->name('api.shops.profile')->middleware('zone');
    });

    # subscribe
    Route::post('subscribe', [SubscribeController::class, 'subscribe']);

    # coupons
    Route::group(['prefix' => 'coupons'], function () {
        Route::get('/', [CouponController::class, 'index']);
        Route::post('/apply', [CouponController::class, 'apply'])->middleware(['auth:sanctum', 'zone']);
        Route::get('/details/{code}', [CouponController::class, 'show'])->name('coupons.show')->middleware('zone');
    });

    # campaigns
    Route::group(['prefix' => 'campaigns'], function () {
        Route::get('/', [CampaignController::class, 'index']);
        Route::get('/details/{slug}', [CampaignController::class, 'show'])->name('campaigns.show')->middleware('zone');
    });

    # carts
    Route::group(['prefix' => 'carts', 'middleware' => ['zone']], function () {
        Route::get('/', [CartController::class, 'index']);
        Route::post('/create', [CartController::class, 'store']);
        Route::post('/update', [CartController::class, 'update']);
        Route::post('/delete', [CartController::class, 'destroy']);
        Route::get('/check-abandoned', [CartController::class, 'checkAbandonedCart']);
    });

    # wishlists
    Route::group(['prefix' => 'wishlists', 'middleware' => ['auth:sanctum', 'zone']], function () {
        Route::get('/', [WishlistController::class, 'index']);
        Route::post('/create', [WishlistController::class, 'store']);
        Route::post('/delete', [WishlistController::class, 'destroy']);
    });

    # locations
    // Commented out location API routes
    Route::get('/get-states/{countryId}', [UserController::class, 'states']);
    Route::get('/get-cities/{stateId}', [UserController::class, 'cities']);
    // Route::get('/get-areas/{cityId}', [UserController::class, 'areas']);

    # reviews
    Route::group(['prefix' => 'reviews', 'middleware' => ['zone']], function () {
        // get all reviews of a product
        Route::get('/{productId}', [ReviewController::class, 'index']);

        // customer review
        Route::group(['middleware' => 'auth:sanctum'], function () {
            Route::get('/user/all', [ReviewController::class, 'allUserReviews']);
            Route::post('/user/store', [ReviewController::class, 'storeOrUpdate']);
            Route::get('/user/{productId}', [ReviewController::class, 'show']);

            # shop review
            Route::get('/shop/{shopId}', [ReviewController::class, 'showShopReview']);
            Route::post('/shop/store', [ReviewController::class, 'storeShopReview']);
        });
    });

    #guest checkout
    Route::post('/checkout/manual-order/store', [CheckoutController::class, 'store']);


    # blogs
    Route::group(['prefix' => 'blogs'], function () {
        Route::get('/', [BlogController::class, 'index']);
        Route::get('/details/{slug}', [BlogController::class, 'show'])->name('api.blogs.show');
    });


    # customer protected routes
    Route::get('/orders/success/{code}', [OrderController::class, 'success'])->name('orders.success');

    Route::group(['middleware' => 'auth:sanctum'], function () {

        // address
        Route::get('/user/addresses', [UserController::class, 'addresses']);
        Route::post('/user/address', [UserController::class, 'addNewAddress']);
        Route::post('/user/address/update', [UserController::class, 'updateAddress']);
        Route::post('/user/address/update-default', [UserController::class, 'setDefaultAddress']);
        Route::post('/user/address/delete', [UserController::class, 'destroy']);

        // checkout
        Route::group(['prefix' => 'checkout'], function () {

            Route::post('order/store', [CheckoutController::class, 'store'])->middleware('zone');
            Route::post('zone/update', [CheckoutController::class, 'updateZone'])->middleware('zone');
        });

        // orders
        Route::group(['prefix' => 'orders', 'middleware' => ['zone']], function () {
            Route::get('/', [OrderController::class, 'index']);
            // Route::get('/success/{code}', [OrderController::class, 'success'])->name('orders.success');
        });

        // chat
        Route::get('/chats', [ChatController::class, 'index']);
        Route::post('/chats', [ChatController::class, 'store']);
        Route::post('/chats/delete', [ChatController::class, 'destroy']);

        Route::get('/chats/messages/{chatId}', [ChatController::class, 'indexMessages']);
        Route::post('/chats/messages', [ChatController::class, 'storeMessages']);

        // profile
        Route::post('/user/update-info', [AuthController::class, 'updateInfo']);
        Route::post('/user/update-password', [AuthController::class, 'updatePassword']);
    });

    // support tickets (public access)
    Route::group(['prefix' => 'tickets'], function () {
        Route::post('/', [TicketController::class, 'store']);
        Route::get('/{public_id}', [TicketController::class, 'show']);
        Route::post('/{public_id}/reply', [TicketController::class, 'reply']);
        Route::get('/{public_id}/replies', [TicketController::class, 'getReplies']);
    });

    Route::post('/checkout/get-shipping-charge', [CheckoutController::class, 'getShippingCharge']);

    // checkout session management (guest checkout support)
    Route::post('/checkout/save-session', [CheckoutController::class, 'saveSession']);
    Route::get('/checkout/session', [CheckoutController::class, 'getSession']);

    Route::group(['prefix' => 'orders', 'middleware' => ['zone']], function () {
        Route::get('/{code}', [OrderController::class, 'show'])->name('orders.show');
    });

    # Translations API
    Route::group(['prefix' => 'translations'], function () {
        Route::get('/{locale}', [TranslationController::class, 'getTranslations']);
        Route::post('/add', [TranslationController::class, 'addTranslation'])->middleware('throttle:10,1');
    });

});
