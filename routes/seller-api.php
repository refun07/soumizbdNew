<?php

use App\Http\Controllers\Backend\Api\Campaign\CampaignProductController;
use App\Http\Controllers\Backend\Api\Chat\ChatController;
use App\Http\Controllers\Backend\Api\Dashboard\SellerDashboardController;
use App\Http\Controllers\Backend\Api\Goal\GoalController;
use App\Http\Controllers\Backend\Api\POS\PosController;
use App\Http\Controllers\Backend\Seller\Inventory\DamageStockController as SellerDamageStockController;
use App\Http\Controllers\Frontend\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'seller/api', 'middleware' => ['auth', 'seller', 'unbanned', 'xss']], function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/new', [SellerDashboardController::class, 'new']);
        Route::get('/total-orders', [SellerDashboardController::class, 'totalOrders']);
        Route::get('/total-sales', [SellerDashboardController::class, 'totalSales']);
        Route::get('/total-earnings', [SellerDashboardController::class, 'totalEarnings']);
        Route::get('/total-products', [SellerDashboardController::class, 'totalProducts']);
        Route::get('/sales-amount', [SellerDashboardController::class, 'salesAmount']);
        Route::get('/order-count', [SellerDashboardController::class, 'orderCount']);
        Route::get('/recent-orders', [SellerDashboardController::class, 'recentOrders'])->name('seller.dashboard.recentOrders');
        Route::get('/order-updates', [SellerDashboardController::class, 'orderUpdates'])->name('seller.dashboard.orderUpdates');
        Route::get('/most-selling-products', [SellerDashboardController::class, 'mostSellingProducts'])->name('seller.dashboard.mostSellingProducts');
        Route::get('/active-campaigns', [SellerDashboardController::class, 'activeCampaigns'])->name('seller.dashboard.activeCampaigns');
    });

    /*
    |--------------------------------------------------------------------------
    | goal Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'goals'], function () {
        Route::get('/', [GoalController::class, 'index']);
        Route::post('/', [GoalController::class, 'store']);
    });


    /*
    |--------------------------------------------------------------------------
    | Campaign product Routes for CRUD.
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'campaigns'], function () {
        Route::get('/filter-data', [CampaignProductController::class, 'getFilterData']);
        Route::get('/products', [CampaignProductController::class, 'index']);
        Route::post('/products', [CampaignProductController::class, 'store']);
        Route::post('/products/update', [CampaignProductController::class, 'update']);
        Route::post('/products/delete', [CampaignProductController::class, 'delete']);
        Route::get('/products/{campaignId}', [CampaignProductController::class, 'getCampaignProducts']);
    });

    /*
    |--------------------------------------------------------------------------
    | chats Routes.
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'chats'], function () {
        // chat 
        Route::get('/', [ChatController::class, 'index']);
        Route::post('/delete', [ChatController::class, 'destroy']);

        Route::get('/messages/{chatId}', [ChatController::class, 'indexMessages']);
        Route::post('/messages', [ChatController::class, 'storeMessages']);
    });

    /*
    |--------------------------------------------------------------------------
    | Damage Stock Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'damage-stocks'], function () {
        Route::get('/', [SellerDamageStockController::class, 'apiIndex']);
        Route::post('/', [SellerDamageStockController::class, 'apiStore']);
        Route::delete('/{id}', [SellerDamageStockController::class, 'apiDestroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | pos Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'pos'], function () {
        Route::post('/add-to-cart', [PosController::class, 'addToPosCart']);
        Route::post('/cart/update', [PosController::class, 'updatePosCart']);
        Route::get('/cart/hold', [PosController::class, 'indexHoldPosCartGroup']);
        Route::post('/cart/hold', [PosController::class, 'holdPosCartGroup']);
        Route::post('/cart/hold/delete/{id}', [PosController::class, 'deleteHoldPosCartGroup']);
        Route::post('/cart/submit-order', [PosController::class, 'storeOrder']);

        Route::get('/filter-data', [PosController::class, 'filterData']);
        Route::get('/product-variations', [PosController::class, 'index']);
        Route::get('/customers', [PosController::class, 'customers']);
        Route::post('/customer', [PosController::class, 'storeCustomer']);
        Route::get('/customers/address/{id}', [PosController::class, 'customerAddresses']);
        Route::post('/address', [PosController::class, 'storeAddress']);
        Route::put('/customer/{id}', [PosController::class, 'updateCustomer']);
        Route::put('/address/{id}', [PosController::class, 'updateAddress']);
        Route::delete('/address/{id}', [PosController::class, 'deleteAddress']);

        Route::get('/get-states/{countryId}', [UserController::class, 'states']);
        Route::get('/get-cities/{stateId}', [UserController::class, 'cities']);
        Route::get('/get-areas/{cityId}', [UserController::class, 'areas']);
    });
});
