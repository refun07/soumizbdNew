<?php

use App\Http\Controllers\Backend\Admin\Configurations\LanguageController;
use App\Http\Controllers\Backend\Admin\Products\ExchangeController;
use App\Http\Controllers\Backend\Admin\Products\ReturnRequestController;
use App\Http\Controllers\Backend\Admin\Shipping\ShippingClassController;
use App\Http\Controllers\Backend\Seller\ChatController;
use App\Http\Controllers\Backend\Seller\DashboardController;
use App\Http\Controllers\Backend\Seller\Earnings\EarningController;
use App\Http\Controllers\Backend\Seller\FileManagerController;
use App\Http\Controllers\Backend\Seller\Inventory\PurchaseOrderController;
use App\Http\Controllers\Backend\Seller\Inventory\PurchasePaymentController;
use App\Http\Controllers\Backend\Seller\Inventory\DamageStockController;
use App\Http\Controllers\Backend\Seller\Inventory\StockAdjustmentController;
use App\Http\Controllers\Backend\Seller\Inventory\StockTransferController;
use App\Http\Controllers\Backend\Seller\Inventory\SupplierController;
use App\Http\Controllers\Backend\Seller\Orders\OrderController;
use App\Http\Controllers\Backend\Seller\PosController;
use App\Http\Controllers\Backend\Seller\Products\ProductController;
use App\Http\Controllers\Backend\Seller\Promotions\CampaignController;
use App\Http\Controllers\Backend\Seller\Promotions\CouponController;
use App\Http\Controllers\Backend\Seller\Shipping\DeliveryChargeController;
use App\Http\Controllers\Backend\Seller\Shipping\WarehouseController;
use App\Http\Controllers\Backend\Seller\ShopSettings\ShopSectionController;
use Illuminate\Support\Facades\Route;

# seller routes
Route::group(['prefix' => 'seller', 'middleware' => ['auth', 'seller', 'unbanned', 'xss']], function () {


    # notification
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('seller.notifications');
    Route::get('/chats', [ChatController::class, 'index'])->name('seller.chat');
    Route::get('/notifications/mark-as-read', [DashboardController::class, 'markRead'])->name('seller.notifications.markRead');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | dashboard routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/', [DashboardController::class, 'index'])->name('seller.dashboard');
    Route::post('/navbar-search', [DashboardController::class, 'getNavbarSearchData'])->name('seller.getNavbarSearchData');
    Route::post('/change-language', [LanguageController::class, 'changeLanguage'])->name('seller.changeLanguage');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | POS routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/pos', [PosController::class, 'index'])->name('seller.pos');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | order routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/orders', [OrderController::class, 'index'])->name('seller.orders.index');
    Route::post('/update-payment-status', [OrderController::class, 'updatePaymentStatus'])->name('seller.orders.updatePaymentStatus');
    Route::post('/update-delivery-status', [OrderController::class, 'updateDeliveryStatus'])->name('seller.orders.updateDeliveryStatus');
    Route::post('/update-order-address', [OrderController::class, 'updateOrderAddress'])->name('seller.orders.updateOrderAddress');
    Route::post('/orders/track', [OrderController::class, 'updateOrderTracking'])->name('seller.orders.updateOrderTracking');
    Route::post('/orders/note', [OrderController::class, 'storeOrderUpdates'])->name('seller.orders.storeOrderUpdates');
    Route::post('/orders/note/delete/{id}', [OrderController::class, 'deleteOrderUpdate'])->name('seller.orders.deleteOrderUpdate');
    Route::get('/orders/{code}', [OrderController::class, 'show'])->name('seller.orders.show');
    Route::get('/invoice-download/{id}', [OrderController::class, 'downloadInvoice'])->name('seller.orders.downloadInvoice');
    Route::get('/challan-download/{id}', [OrderController::class, 'downloadChallan'])->name('seller.orders.downloadChallan');
    Route::post('/orders/payment', [OrderController::class, 'orderPayment'])->name('seller.orders.payment');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | earnings routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/earnings/payout-settings', [EarningController::class, 'payoutSettings'])->name('seller.earnings.payoutSettings');
    Route::post('/earnings/payout-settings', [EarningController::class, 'updatePayoutSettings'])->name('seller.earnings.updatePayoutSettings');
    Route::get('/earnings/payouts', [EarningController::class, 'payouts'])->name('seller.earnings.payouts');
    Route::get('/earnings/payout-requests', [EarningController::class, 'requests'])->name('seller.earnings.requests');
    Route::post('/earnings/payout-requests', [EarningController::class, 'storeRequest'])->name('seller.earnings.storeRequest');
    Route::get('/earnings/histories', [EarningController::class, 'histories'])->name('seller.earnings.histories');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | product routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # products 
    Route::get('/products/sample-csv', [ProductController::class, 'sampleCsv'])->name('seller.products.sampleCsv');
    Route::post('/products/import', [ProductController::class, 'import'])->name('seller.products.import');
    Route::resource('/products', ProductController::class, ['names' => 'seller.products']);
    Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('seller.products.update');
    Route::get('/products/duplicate/{id}', [ProductController::class, 'duplicate'])->name('seller.products.duplicate');
    Route::post('/update-status-products', [ProductController::class, 'updateStatus'])->name('seller.products.status');

    Route::post('/get-variation-values', [ProductController::class, 'getVariationValues'])->name('seller.products.getVariationValues');
    Route::post('/new-variation', [ProductController::class, 'getNewVariation'])->name('seller.products.newVariation');
    Route::post('/variation-combination', [ProductController::class, 'generateVariationCombinations'])->name('seller.products.generateVariationCombinations');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Inventory routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # purchases routes
    Route::resource('/purchase-orders', PurchaseOrderController::class, ['names' => 'seller.purchase-orders']);
    Route::post('/purchase-orders/get-products', [PurchaseOrderController::class, 'getProducts'])->name('seller.purchase-orders.getProducts');
    Route::get('/purchase-orders/invoice-download/{id}', [PurchaseOrderController::class, 'downloadInvoice'])->name('seller.purchase-orders.downloadInvoice');
    Route::get('/purchase-return/list', [PurchaseOrderController::class, 'returnIndex'])->name('seller.purchase-return.index');
    Route::get('/purchase-return/{id}', [PurchaseOrderController::class, 'returnCreate'])->name('seller.purchase-return.create');
    Route::post('/purchase-return/{id}', [PurchaseOrderController::class, 'returnStore'])->name('seller.purchase-return.store');

    # payments
    Route::post('/purchases/payments', [PurchasePaymentController::class, 'index'])->name('seller.purchase-payments.index');
    Route::post('/purchases/payment', [PurchasePaymentController::class, 'store'])->name('seller.purchase-payments.store');

    # stock adjustments
    Route::resource('/stocks/adjustment', StockAdjustmentController::class, ['names' => 'seller.stockAdjustments']);
    Route::post('/stocks/adjustment/get-products', [StockAdjustmentController::class, 'getProducts'])->name('seller.stockAdjustments.getProducts');

    # damage stocks
    Route::resource('/stocks/damage', DamageStockController::class, ['names' => 'seller.damageStocks']);
    Route::post('/stocks/damage/get-products', [DamageStockController::class, 'getProducts'])->name('seller.damageStocks.getProducts');

    # stock transfer
    Route::resource('/stocks/transfer', StockTransferController::class, ['names' => 'seller.stockTransfers']);
    Route::post('/stocks/transfer/get-products', [StockTransferController::class, 'getProducts'])->name('seller.stockTransfers.getProducts');

    # suppliers routes
    Route::resource('/suppliers', SupplierController::class, ['names' => 'seller.suppliers']);

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | promotions routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # coupons routes
    Route::resource('/coupons', CouponController::class, ['names' => 'seller.coupons']);

    # campaign routes
    Route::resource('/campaigns', CampaignController::class, ['names' => 'seller.campaigns']);
    Route::post('/campaigns/update-campaign-status', [CampaignController::class, 'updateStatus'])->name('seller.campaigns.status');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Shipping routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # shipping warehouses
    Route::resource('/shipping/warehouses', WarehouseController::class, ['names' => 'seller.warehouses']);
    Route::post('/shipping/update-status-warehouse', [WarehouseController::class, 'updateStatus'])->name('seller.warehouses.status');
    Route::post('/shipping/update-default-warehouse', [WarehouseController::class, 'updateDefault'])->name('seller.warehouses.default');

    # shipping delivery charges
    Route::get('/shipping/delivery-charges', [DeliveryChargeController::class, 'index'])->name('seller.delivery-charges.index');
    Route::post('/shipping/delivery-charges', [DeliveryChargeController::class, 'store'])->name('seller.delivery-charges.store');
    Route::get('/shipping/delivery-charges/{zone_id}', [DeliveryChargeController::class, 'show'])->name('seller.delivery-charges.show');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | file manager - admin
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::resource('/file-manager', FileManagerController::class, ['names' => 'seller.fileManagers']);

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | shop settings routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # section routes
    Route::resource('/shop/sections', ShopSectionController::class, ['names' => 'seller.shop-sections']);

    # update profile
    Route::get('/shop/profile', [ShopSectionController::class, 'profile'])->name('seller.shops.profile');
    Route::post('/shop/profile', [ShopSectionController::class, 'updateProfile'])->name('seller.shops.updateProfile');

    #shipping class route
    Route::resource('shipping-classes', ShippingClassController::class, ['names' => 'seller.shipping-classes']);

    #return refund route
    Route::resource('return-requests', ReturnRequestController::class, ['names' => 'seller.return-requests']);
    Route::post('return-requests/refund', [ReturnRequestController::class, 'refundPayment'])->name('seller.return-requests.refund');
    Route::get('return-requests/product-variations/{productId}', [ReturnRequestController::class, 'getProductVariations'])->name('seller.return-requests.product-variations');

    #exchange route
    Route::get('exchanges/search-products', [ExchangeController::class, 'searchProducts'])->name('seller.exchanges.search-products');
    Route::post('exchanges/order-search', [ExchangeController::class, 'orderSearch'])->name('seller.exchanges.order-search');
    Route::get('exchanges/product-variations/{productId}', [ExchangeController::class, 'getProductVariations'])->name('seller.exchanges.product-variations');
    Route::resource('exchanges', ExchangeController::class, ['names' => 'seller.exchanges']);
    Route::post('exchanges/{id}/process', [ExchangeController::class, 'process'])->name('seller.exchanges.process');
    Route::post('exchanges/{id}/cancel', [ExchangeController::class, 'cancel'])->name('seller.exchanges.cancel');
});
