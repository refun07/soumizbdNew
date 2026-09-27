<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Backend\Admin\Blogs\BlogCategoryController;
use App\Http\Controllers\Backend\Admin\Blogs\BlogController;

use App\Http\Controllers\Backend\Admin\ChatController;
use App\Http\Controllers\Backend\Admin\Configurations\FacebookFeedController;
use App\Http\Controllers\Backend\Admin\Configurations\LanguageController;
use App\Http\Controllers\Backend\Admin\Configurations\DatabaseBackupController;
use App\Http\Controllers\Backend\Admin\Staffs\RoleController;
use App\Http\Controllers\Backend\Admin\Support\TicketController;
use App\Http\Controllers\Backend\Admin\Configurations\SettingController;
use App\Http\Controllers\Backend\Admin\DashboardController;
use App\Http\Controllers\Backend\Admin\FileManagerController;
use App\Http\Controllers\Backend\Admin\Inventory\PurchaseOrderController;
use App\Http\Controllers\Backend\Admin\Inventory\PurchasePaymentController;
use App\Http\Controllers\Backend\Admin\Inventory\StockAdjustmentController;
use App\Http\Controllers\Backend\Admin\Inventory\StockTransferController;
use App\Http\Controllers\Backend\Admin\Inventory\StockReportController;
use App\Http\Controllers\Backend\Admin\Inventory\SupplierDueReportController;
use App\Http\Controllers\Backend\Admin\Inventory\ProductExpiryReportController;
use App\Http\Controllers\Backend\Admin\Inventory\DamageStockController;
use App\Http\Controllers\Backend\Admin\Inventory\StockRequestController;
use App\Http\Controllers\Backend\Admin\Products\BadgeController;
use App\Http\Controllers\Backend\Admin\Products\BrandController;
use App\Http\Controllers\Backend\Admin\Products\ProductController;
use App\Http\Controllers\Backend\Admin\Products\UnitController;
use App\Http\Controllers\Backend\Admin\Products\CategoryController;
use App\Http\Controllers\Backend\Admin\Products\TaxController;
use App\Http\Controllers\Backend\Admin\Products\VariationController;
use App\Http\Controllers\Backend\Admin\Products\VariationValueController;
use App\Http\Controllers\Backend\Admin\Promotions\CouponController;
use App\Http\Controllers\Backend\Admin\Promotions\CampaignController;
use App\Http\Controllers\Backend\Admin\Inventory\SupplierController;
use App\Http\Controllers\Backend\Admin\Orders\OrderController;
use App\Http\Controllers\Backend\Admin\PosController;
use App\Http\Controllers\Backend\Admin\Products\ExchangeController;
use App\Http\Controllers\Backend\Admin\Products\ReturnRequestController;
use App\Http\Controllers\Backend\Admin\Promotions\NewsletterController;
use App\Http\Controllers\Backend\Admin\Promotions\SubscriberController;
use App\Http\Controllers\Backend\Admin\Resources\TagController;
use App\Http\Controllers\Backend\Admin\Shipping\AreaController;
use App\Http\Controllers\Backend\Admin\Shipping\CityController;
use App\Http\Controllers\Backend\Admin\Shipping\CountryController;
use App\Http\Controllers\Backend\Admin\Shipping\DeliveryChargeController;
use App\Http\Controllers\Backend\Admin\Shipping\ShippingClassController;
use App\Http\Controllers\Backend\Admin\Shipping\StateController;
use App\Http\Controllers\Backend\Admin\Shipping\WarehouseController;
use App\Http\Controllers\Backend\Admin\Shipping\ZoneController;
use App\Http\Controllers\Backend\Admin\ShopSettings\ShopSectionController;
use App\Http\Controllers\Backend\Admin\Staffs\StaffController;
use App\Http\Controllers\Backend\Admin\Users\CustomerController;
use App\Http\Controllers\Backend\Admin\Security\IpBlockerController;
use App\Http\Controllers\Backend\Admin\Users\SellerController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\ColorBrandingController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\PageController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\ProductSettingsController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\ThemeController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\MenuController;
use App\Http\Controllers\Backend\Admin\BizanaController;
use App\Http\Controllers\Backend\Admin\OfferManagementController;

/*
|----------------------------------------------------------------------------------------------------------------------
| file manager - uploader, file selector routes
|----------------------------------------------------------------------------------------------------------------------
*/

Route::post('/file-manager/upload', [FileManagerController::class, 'upload']);
Route::get('/file-manager/get-uploaded-files', [FileManagerController::class, 'getUploadedFiles'])->name('get_uploaded_files');
Route::delete('/file-manager/destroy/{id}', [FileManagerController::class, 'destroy'])->name('destroy_file');
Route::post('/file-manager/get-file-by-ids', [FileManagerController::class, 'getPreviewFiles'])->name('get_file_by_ids');
Route::get('/file-manager/download/{id}', [FileManagerController::class, 'attachmentDownload'])->name('download_attachment');
Route::post('/file-manager/bg-remove/{id}', [FileManagerController::class, 'backgroundRemove'])->name('bg_remove');
Route::post('/file-manager/alt-text/{id}', [FileManagerController::class, 'altText'])->name('alt_text');
#common route admin+seller
Route::post('shipping-classes/get-products', [ShippingClassController::class, 'getProducts'])->name('shipping-classes.get-products');
Route::post('return-refund/get-order-invoice', [ReturnRequestController::class, 'getOrderInvoice'])->name('return-refund.get-order-invoice');
# admin routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin', 'unbanned', 'xss']], function () {

    # cache clear
    Route::get('/clear-cache', function () {
        cacheClear();
        flash(translate('Cache cleared successfully'))->success();
        return back();
    })->name('admin.clearCache');

    # notification
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('admin.notifications');
    Route::get('/notifications/mark-as-read', [DashboardController::class, 'markRead'])->name('admin.notifications.markRead');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | dashboard routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/chats', [ChatController::class, 'index'])->name('admin.chat');
    Route::post('/navbar-search', [DashboardController::class, 'getNavbarSearchData'])->name('admin.getNavbarSearchData');
    Route::post('/change-language', [LanguageController::class, 'changeLanguage'])->name('admin.changeLanguage');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | offer management routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/offer-management', [OfferManagementController::class, 'index'])->name('admin.offerManagement');
    Route::post('/offer-management', [OfferManagementController::class, 'store'])->name('admin.storeOfferManagement');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | POS routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/pos', [PosController::class, 'index'])->name('admin.pos');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Bizana Business Analytics routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'bizana'], function () {
        Route::get('/', [BizanaController::class, 'index'])->name('admin.bizana.index');
        Route::get('/products', [BizanaController::class, 'products'])->name('admin.bizana.products');
        Route::get('/orders', [BizanaController::class, 'orders'])->name('admin.bizana.orders');
        Route::get('/expenses', [BizanaController::class, 'expenses'])->name('admin.bizana.expenses');
        Route::get('/sales', [BizanaController::class, 'sales'])->name('admin.bizana.sales');
        Route::get('/price-calculator', [BizanaController::class, 'priceCalculator'])->name('admin.bizana.price-calculator');
        Route::get('/delivery-partners', [BizanaController::class, 'deliveryPartners'])->name('admin.bizana.delivery-partners');
        Route::get('/daily-expenses', [BizanaController::class, 'dailyExpenses'])->name('admin.bizana.daily-expenses');
        Route::get('/monthly-expenses', [BizanaController::class, 'monthlyExpenses'])->name('admin.bizana.monthly-expenses');
        Route::get('/recurring-expenses', [BizanaController::class, 'recurringExpenses'])->name('admin.bizana.recurring-expenses');
        Route::get('/order-profits', [BizanaController::class, 'orderProfits'])->name('admin.bizana.order-profits');
    });

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Bizana API routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'api/bizana'], function () {
        // Monthly expenses API routes
        Route::get('/monthly-expenses/data', [BizanaController::class, 'getMonthlyExpensesData']);
        Route::post('/monthly-expenses/expenses', [BizanaController::class, 'storeMonthlyExpense']);
        Route::put('/monthly-expenses/expenses/{id}', [BizanaController::class, 'updateMonthlyExpense']);
        Route::delete('/monthly-expenses/expenses/{id}', [BizanaController::class, 'deleteMonthlyExpense']);

        // Daily expenses API routes (to be implemented)
        Route::get('/daily-expenses/data', [BizanaController::class, 'getDailyExpensesData']);
        Route::post('/daily-expenses/expenses', [BizanaController::class, 'storeDailyExpense']);
        Route::put('/daily-expenses/expenses/{id}', [BizanaController::class, 'updateDailyExpense']);
        Route::delete('/daily-expenses/expenses/{id}', [BizanaController::class, 'deleteDailyExpense']);
    });

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | role permissions
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::resource('/staffs', StaffController::class, ['names' => 'admin.staffs']);
    Route::post('/staffs/toggle-ban', [StaffController::class, 'toggleBan'])->name('admin.staffs.toggleBan');

    Route::resource('/roles', RoleController::class, ['names' => 'admin.roles']);
    Route::post('/roles/update-status', [RoleController::class, 'updateStatus'])->name('admin.roles.status');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | order routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/orders', [OrderController::class, 'index'])->name('admin.orders.index');
    Route::get('/carts', [\App\Http\Controllers\Backend\Admin\CartController::class, 'indexPage'])->name('admin.carts.index');
    Route::get('/agent-commissions', [\App\Http\Controllers\Backend\Admin\AgentCommissionController::class, 'indexPage'])->name('admin.agent-commissions.index');
    Route::get('/return-details', [OrderController::class, 'returnDetails'])->name('admin.return-details.index');
    Route::post('/update-payment-status', [OrderController::class, 'updatePaymentStatus'])->name('admin.orders.updatePaymentStatus');
    Route::post('/update-delivery-status', [OrderController::class, 'updateDeliveryStatus'])->name('admin.orders.updateDeliveryStatus');
    Route::post('/update-order-address', [OrderController::class, 'updateOrderAddress'])->name('admin.orders.updateOrderAddress');
    Route::post('/orders/track', [OrderController::class, 'updateOrderTracking'])->name('admin.orders.updateOrderTracking');
    Route::post('/orders/note', [OrderController::class, 'storeOrderUpdates'])->name('admin.orders.storeOrderUpdates');
    Route::post('/orders/note/delete/{id}', [OrderController::class, 'deleteOrderUpdate'])->name('admin.orders.deleteOrderUpdate');
    Route::get('/orders/{code}', [OrderController::class, 'show'])->name('admin.orders.show');
    Route::get('/orders/{code}/edit', [OrderController::class, 'edit'])->name('admin.orders.edit');
    Route::post('/orders/{code}/update', [OrderController::class, 'updateOrder'])->name('admin.orders.updateOrder');
    Route::get('/api/products/search', [OrderController::class, 'searchProducts'])->name('admin.orders.searchProducts');
    Route::get('/invoice-download/{id}', [OrderController::class, 'downloadInvoice'])->name('admin.orders.downloadInvoice');
    Route::get('/challan-download/{id}', [OrderController::class, 'downloadChallan'])->name('admin.orders.downloadChallan');
    Route::get('/multiple-invoice-download/{ids}', [OrderController::class, 'downloadMultipleInvoice'])->name('admin.orders.downloadMultipleInvoice');
    Route::get('/single-page-invoice-download/{ids}', [OrderController::class, 'downloadSinglePageInvoice'])->name('admin.orders.downloadSinglePageInvoice');
    Route::post('/orders/payment', [OrderController::class, 'orderPayment'])->name('admin.orders.payment');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | customers routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # customers
    Route::get('/customers/export', [CustomerController::class, 'export'])->name('admin.customers.export');
    Route::get('/customers/sample-csv', [CustomerController::class, 'sampleCsv'])->name('admin.customers.sampleCsv');
    Route::post('/customers/import', [CustomerController::class, 'import'])->name('admin.customers.import');
    Route::resource('/customers', CustomerController::class, ['names' => 'admin.customers']);
    Route::post('/customers/store', [CustomerController::class, 'store'])->name('admin.customers.store');
    Route::post('/customer-logs', [CustomerController::class, 'storeCustomerLog'])->name('admin.customer-logs.store');
    Route::post('/customers/toggle-ban', [CustomerController::class, 'toggleBan'])->name('admin.customers.toggleBan');

    # customer segment
    Route::post('/customer-segments', [CustomerController::class, 'storeCustomerSegment'])->name('admin.customers.storeCustomerSegment');
    Route::post('/customer-segments/remove', [CustomerController::class, 'removeCustomerSegment'])->name('admin.customers.removeCustomerSegment');
    Route::post('/customer-segments/update', [CustomerController::class, 'updateCustomerSegment'])->name('admin.customers.updateCustomerSegment');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | sellers routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # seller payments
    Route::get('/sellers/payouts', [SellerController::class, 'payouts'])->name('admin.sellers.payouts');
    Route::get('/sellers/payout-requests', [SellerController::class, 'payoutRequests'])->name('admin.sellers.payoutRequests');
    Route::post('/sellers/payout-requests', [SellerController::class, 'makePayment'])->name('admin.sellers.makePayment');
    Route::get('/sellers/earnings', [SellerController::class, 'earnings'])->name('admin.sellers.earnings');

    # sellers
    Route::resource('/sellers', SellerController::class, ['names' => 'admin.sellers']);
    Route::post('/sellers/toggle-ban', [SellerController::class, 'toggleBan'])->name('admin.sellers.toggleBan');
    Route::post('/sellers/toggle-approval', [SellerController::class, 'toggleApproval'])->name('admin.sellers.toggleApproval');
    Route::post('/sellers/toggle-verified', [SellerController::class, 'toggleVerified'])->name('admin.sellers.toggleVerified');
    Route::post('/sellers/toggle-published', [SellerController::class, 'togglePublished'])->name('admin.sellers.togglePublished');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | product routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # products
    Route::get('/products/sample-csv', [ProductController::class, 'sampleCsv'])->name('admin.products.sampleCsv');
    Route::post('/products/import', [ProductController::class, 'import'])->name('admin.products.import');
    Route::resource('/products', ProductController::class, ['names' => 'admin.products']);
    Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::get('/products/duplicate/{id}', [ProductController::class, 'duplicate'])->name('admin.products.duplicate');
    Route::post('/update-status-products', [ProductController::class, 'updateStatus'])->name('admin.products.status');

    Route::post('/get-variation-values', [ProductController::class, 'getVariationValues'])->name('admin.products.getVariationValues');
    Route::post('/new-variation', [ProductController::class, 'getNewVariation'])->name('admin.products.newVariation');
    Route::post('/variation-combination', [ProductController::class, 'generateVariationCombinations'])->name('admin.products.generateVariationCombinations');

    # categories
    Route::resource('/categories', CategoryController::class, ['names' => 'admin.categories']);
    Route::get('/categories/create/modal', [CategoryController::class, 'initCreateModal'])->name('admin.categories.initCreateModal');


    # variations
    Route::resource('/variations', VariationController::class, ['names' => 'admin.variations']);
    Route::post('/variations/update-status', [VariationController::class, 'updateStatus'])->name('admin.variations.status');

    # variation values
    Route::get('/variation-values/{variation_id}', [VariationValueController::class, 'index'])->name('admin.variation-values.index');

    Route::get('/variation-values/{variation_id}/create', [VariationValueController::class, 'create'])->name('admin.variation-values.create');
    Route::post('/variation-values/{variation_id}', [VariationValueController::class, 'store'])->name('admin.variation-values.store');

    Route::get('/variation-values/{id}/edit', [VariationValueController::class, 'edit'])->name('admin.variation-values.edit');
    Route::put('/variation-values/{id}/update', [VariationValueController::class, 'update'])->name('admin.variation-values.update');

    Route::post('/variation-values/status/update', [VariationValueController::class, 'updateStatus'])->name('admin.variation-values.status');
    Route::delete('/variation-values/{id}/delete', [VariationValueController::class, 'destroy'])->name('admin.variation-values.destroy');

    # brands
    Route::get('/brands/categories/manage', [BrandController::class, 'categories'])->name('admin.brands.categories.index');
    Route::post('/brands/categories/manage', [BrandController::class, 'updateCategories'])->name('admin.brands.categories.update');
    Route::resource('/brands', BrandController::class, ['names' => 'admin.brands']);

    # units
    Route::resource('/units', UnitController::class, ['names' => 'admin.units']);

    # taxes
    Route::resource('/taxes', TaxController::class, ['names' => 'admin.taxes']);
    Route::post('/update-status-tax', [TaxController::class, 'updateStatus'])->name('admin.taxes.status');

    # badges
    Route::resource('/badges', BadgeController::class, ['names' => 'admin.badges']);
    Route::post('/update-status-badge', [BadgeController::class, 'updateStatus'])->name('admin.badges.status');

    # pre-orders
    Route::get('/pre-orders', function () {
        $symbolAlignMent = [
            'symbol_first',
            'amount_first',
            'symbol_space',
            'amount_space',
        ];

        $settings = [
            'currency' => [
                'code' => getSetting('currencyCode') ?? 'usd',
                'symbol' => [
                    'position' => getSetting('currencySymbolAlignment') ? $symbolAlignMent[getSetting('currencySymbolAlignment') ? getSetting('currencySymbolAlignment') - 1 : 0] : 'symbol_first',
                    'show' => getSetting('currencySymbol') ?? '$'
                ],
                'thousandSeparator' => getSetting('thousandSeparator') ?? null,
                'numOfDecimals' => getSetting('numOfDecimals') ?? 0,
                'decimalSeparator' => getSetting('decimalSeparator') ?? '.',
            ],
        ];

        return view('backend.admin.pre-orders.index', compact('settings'));
    })->name('admin.pre-orders.index');

    # support tickets - react app
    Route::get('/support/tickets/{any?}', [TicketController::class, 'app'])
        ->where('any', '.*')
        ->name('admin.support.tickets.index');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Inventory routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # purchases routes
    Route::resource('/purchase-orders', PurchaseOrderController::class, ['names' => 'admin.purchase-orders']);
    Route::post('/purchase-orders/get-products', [PurchaseOrderController::class, 'getProducts'])->name('admin.purchase-orders.getProducts');
    Route::get('/purchase-orders/invoice-download/{id}', [PurchaseOrderController::class, 'downloadInvoice'])->name('admin.purchase-orders.downloadInvoice');
    Route::get('/purchase-return/list', [PurchaseOrderController::class, 'returnIndex'])->name('admin.purchase-return.index');
    Route::get('/purchase-return/{id}', [PurchaseOrderController::class, 'returnCreate'])->name('admin.purchase-return.create');
    Route::post('/purchase-return/{id}', [PurchaseOrderController::class, 'returnStore'])->name('admin.purchase-return.store');

    # payments
    Route::post('/purchases/payments', [PurchasePaymentController::class, 'index'])->name('admin.purchase-payments.index');
    Route::post('/purchases/payment', [PurchasePaymentController::class, 'store'])->name('admin.purchase-payments.store');

    # stock adjustments
    Route::resource('/stocks/adjustment', StockAdjustmentController::class, ['names' => 'admin.stockAdjustments']);
    Route::post('/stocks/adjustment/get-products', [StockAdjustmentController::class, 'getProducts'])->name('admin.stockAdjustments.getProducts');

    # damage stocks
    Route::resource('/stocks/damage', DamageStockController::class, ['names' => 'admin.damageStocks']);
    Route::post('/stocks/damage/get-products', [DamageStockController::class, 'getProducts'])->name('admin.damageStocks.getProducts');

    # stock transfer
    Route::resource('/stocks/transfer', StockTransferController::class, ['names' => 'admin.stockTransfers']);
    Route::post('/stocks/transfer/get-products', [StockTransferController::class, 'getProducts'])->name('admin.stockTransfers.getProducts');

    # stock reports
    Route::get('/stocks/reports', [StockReportController::class, 'index'])->name('admin.stockReports.index');
    Route::post('/stocks/reports/export', [StockReportController::class, 'export'])->name('admin.stockReports.export');

    # stock requests
    Route::resource('/stock-requests', StockRequestController::class, ['names' => 'admin.stock-requests'])
        ->except(['create', 'store', 'edit']);
    Route::put('/stock-requests/{stockRequest}/update-status', [StockRequestController::class, 'updateStatus'])
        ->name('admin.stock-requests.updateStatus');

    # supplier due reports
    Route::get('/suppliers/due-reports', [SupplierDueReportController::class, 'index'])->name('admin.supplierDueReports.index');
    Route::get('/suppliers/due-reports/{supplier}', [SupplierDueReportController::class, 'details'])->name('admin.supplierDueReports.details');
    Route::post('/suppliers/due-reports/export', [SupplierDueReportController::class, 'export'])->name('admin.supplierDueReports.export');

    # product expiry reports
    Route::get('/product-expiry-reports', [ProductExpiryReportController::class, 'index'])->name('admin.productExpiryReports.index');
    Route::post('/product-expiry-reports/export', [ProductExpiryReportController::class, 'export'])->name('admin.productExpiryReports.export');
    Route::get('/product-expiry-reports/ajax', [ProductExpiryReportController::class, 'ajaxData'])->name('admin.productExpiryReports.ajax');

    # suppliers routes
    Route::resource('/suppliers', SupplierController::class, ['names' => 'admin.suppliers']);
    /*
    |----------------------------------------------------------------------------------------------------------------------
    | resources routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # tags routes
    Route::resource('/tags', TagController::class, ['names' => 'admin.tags']);

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | promotions routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # coupons routes
    Route::resource('/coupons', CouponController::class, ['names' => 'admin.coupons']);

    # campaign routes
    Route::resource('/campaigns', CampaignController::class, ['names' => 'admin.campaigns']);
    Route::post('/campaigns/update-campaign-status', [CampaignController::class, 'updateStatus'])->name('admin.campaigns.status');

    # subscribers
    Route::get('/subscribers', [SubscriberController::class, 'index'])->name('admin.subscribers.index');
    Route::delete('/subscribers/{id}', [SubscriberController::class, 'destroy'])->name('admin.subscribers.destroy');

    # newsletters
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('admin.newsletters.index');
    Route::post('/newsletters', [NewsletterController::class, 'store'])->name('admin.newsletters.store');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | sale routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::get('/sales', [DashboardController::class, 'index'])->name('admin.sales');
    Route::get('/refunds', [DashboardController::class, 'index'])->name('admin.refunds');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Shipping routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # shipping countries
    Route::get('/shipping/countries', [CountryController::class, 'index'])->name('admin.countries');
    Route::post('/shipping/update-country-status', [CountryController::class, 'updateStatus'])->name('admin.countries.status');

    # shipping states
    Route::resource('/shipping/states', StateController::class, ['names' => 'admin.states']);
    Route::post('/shipping/update-status-state', [StateController::class, 'updateStatus'])->name('admin.states.status');

    # shipping cities
    Route::resource('/shipping/cities', CityController::class, ['names' => 'admin.cities']);
    Route::post('/shipping/update-status-cities', [CityController::class, 'updateStatus'])->name('admin.cities.status');
    Route::post('/shipping/cities/reset-default', [CityController::class, 'resetToDefault'])->name('admin.cities.reset');

    # shipping areas
    Route::resource('/shipping/areas', AreaController::class, ['names' => 'admin.areas']);
    Route::post('/shipping/update-status-areas', [AreaController::class, 'updateStatus'])->name('admin.areas.status');

    # shipping zones
    Route::resource('/shipping/zones', ZoneController::class, ['names' => 'admin.zones']);
    Route::post('/shipping/update-status-zones', [ZoneController::class, 'updateStatus'])->name('admin.zones.status');

    # shipping warehouses
    Route::resource('/shipping/warehouses', WarehouseController::class, ['names' => 'admin.warehouses']);
    Route::post('/shipping/update-status-warehouse', [WarehouseController::class, 'updateStatus'])->name('admin.warehouses.status');
    Route::post('/shipping/update-default-warehouse', [WarehouseController::class, 'updateDefault'])->name('admin.warehouses.default');


    # shipping class
    Route::resource('shipping-classes', ShippingClassController::class, ['names' => 'admin.shipping-classes']);


    # shipping delivery charges
    Route::get('/shipping/delivery-charges', [DeliveryChargeController::class, 'index'])->name('admin.delivery-charges.index');
    Route::post('/shipping/delivery-charges', [DeliveryChargeController::class, 'store'])->name('admin.delivery-charges.store');
    Route::get('/shipping/delivery-charges/{zone_id}', [DeliveryChargeController::class, 'show'])->name('admin.delivery-charges.show');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | file manager - admin
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::resource('/file-manager', FileManagerController::class);


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | blogs
    |----------------------------------------------------------------------------------------------------------------------
    */
    # blogs 
    Route::resource('/blogs', BlogController::class, ['names' => 'admin.blogs']);
    Route::post('/blogs/update-status', [BlogController::class, 'updateStatus'])->name('admin.blogs.status');
    # blog categories 
    Route::resource('/blog-categories', BlogCategoryController::class, ['names' => 'admin.blogCategories']);

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | shop settings routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # section routes
    Route::resource('/shop/sections', ShopSectionController::class, ['names' => 'admin.shop-sections']);

    # update profile
    Route::get('/shop/profile', [ShopSectionController::class, 'profile'])->name('admin.shops.profile');
    Route::post('/shop/profile', [ShopSectionController::class, 'updateProfile'])->name('admin.shops.updateProfile');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | website setup routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    # pages routes
    Route::get('/color-and-branding', [ColorBrandingController::class, 'colorBranding'])->name('admin.colorBranding.index');
    Route::get('/product-settings', [ProductSettingsController::class, 'index'])->name('admin.product-settings.index');
    Route::resource('/pages', PageController::class, ['names' => 'admin.pages']);
    Route::get('/pages/configure/{id}', [PageController::class, 'configure'])->name('admin.homepage.configure');

    # landing pages routes
    Route::get('/landing-pages', [LandingPageController::class, 'index'])->name('admin.landing-pages.index');
    Route::get('/landing-pages/configure/{id}', [LandingPageController::class, 'configure'])->name('admin.landing-pages.configure');
    Route::get('/theme/selection', [ThemeController::class, 'themeSelection'])->name('admin.theme.selection');
    Route::get('/theme/header/{headerId}/configure', [ThemeController::class, 'headerConfiguration'])->name('admin.theme.header.configure');
    Route::get('/theme/footer/configure', [ThemeController::class, 'footerConfiguration'])->name('admin.theme.footer.configure');

    # menu builder routes
    Route::get('/theme/menu-builder', [MenuController::class, 'index'])->name('admin.theme.menu-builder');

    # floating chat routes
    Route::get('/floating-chat/configuration', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\FloatingChatController::class, 'configure'])->name('admin.floating-chat.configuration');


    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Configurations routes
    |----------------------------------------------------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'configurations'], function () {

        # env key update
        Route::post('/env-key-update', [SettingController::class, 'envKeyUpdate'])->name('admin.env-key.update');

        # general settings
        Route::get('/general-settings', [SettingController::class, 'generalSetting'])->name('admin.general-settings.index');
        Route::post('/general-settings', [SettingController::class, 'update'])->name('admin.general-settings.update');
        Route::post('/quick-links/delete/{index}', [SettingController::class, 'deleteQuickLink'])->name('admin.quick-links.delete');

        # order settings
        Route::get('/order-settings', [SettingController::class, 'orderSetting'])->name('admin.order-settings.index');

        # language settings
        Route::resource('/languages', LanguageController::class, ['names' => 'admin.languages']);
        Route::get('/languages/{code}', [LanguageController::class, 'show'])->name('admin.languages.show');
        Route::post('/update-update_rtl_status', [LanguageController::class, 'updateRtlStatus'])->name('admin.languages.updateRtlStatus');
        Route::post('/languages/keyValueStore', [LanguageController::class, 'keyValueStore'])->name('admin.languages.keyValueStore');
        Route::get('/delete-language/{id}', [LanguageController::class, 'destroy'])->name('admin.languages.destroy');

        # smtp settings
        Route::get('/smtp-settings', [SettingController::class, 'smtpSettings'])->name('admin.smtp-settings.index');
        Route::post('/smtp/test', [SettingController::class, 'testEmail'])->name('test.smtp');

        # social login settings
        Route::get('/social-login', [SettingController::class, 'social_login'])->name('admin.social-login.index');

        # payment method settings
        Route::get('/payment-method', [SettingController::class, 'payment_method'])->name('admin.payment-method.index');
        Route::post('/payment_method_update', [SettingController::class, 'payment_method_update'])->name('admin.payment-method.update');

        #analytics method settings
        Route::get('/analytics', [SettingController::class, 'analytics'])->name('admin.analytics.index');

        #sms api settings

        #delivery partner setings
        Route::get('/delivery-partner-settings', [SettingController::class, 'delivery_partner_settings'])->name('admin.delivery-partner.index');


        Route::get('/otp-settings', [SettingController::class, 'otp_settings'])->name('admin.otp_settings.index');

        # facebook feed
        Route::get('/facebook-feed', [FacebookFeedController::class, 'index'])->name('admin.facebook-feed.index');
        Route::post('/facebook-feed/generate', [FacebookFeedController::class, 'generate'])->name('admin.facebook-feed.generate');
        Route::get('/facebook-feed/download', [FacebookFeedController::class, 'download'])->name('admin.facebook-feed.download');

        # ip blocker
        Route::get('/ip-blocker', [IpBlockerController::class, 'indexView'])->name('admin.ip-blocker.index');

        # database backups
        Route::get('/database-backups', [DatabaseBackupController::class, 'index'])->name('admin.database-backups.index');
        Route::post('/database-backups/create', [DatabaseBackupController::class, 'create'])->name('admin.database-backups.create');
        Route::get('/database-backups/download/{filename}', [DatabaseBackupController::class, 'download'])->name('admin.database-backups.download');
        Route::delete('/database-backups/delete/{filename}', [DatabaseBackupController::class, 'delete'])->name('admin.database-backups.delete');
    });

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Return Request routes
    |----------------------------------------------------------------------------------------------------------------------
    */

    Route::resource('return-requests', ReturnRequestController::class, ['names' => 'admin.return-requests']);
    Route::post('return-requests/refund', [ReturnRequestController::class, 'refundPayment'])->name('admin.return-requests.refund');
    Route::get('return-requests/product-variations/{productId}', [ReturnRequestController::class, 'getProductVariations'])->name('admin.return-requests.product-variations');

    /*
    |----------------------------------------------------------------------------------------------------------------------
    | Exchange routes
    |----------------------------------------------------------------------------------------------------------------------
    */

    Route::get('exchanges/search-products', [ExchangeController::class, 'searchProducts'])->name('admin.exchanges.search-products');
    Route::post('exchanges/order-search', [ExchangeController::class, 'orderSearch'])->name('admin.exchanges.order-search');
    Route::get('exchanges/product-variations/{productId}', [ExchangeController::class, 'getProductVariations'])->name('admin.exchanges.product-variations');
    Route::resource('exchanges', ExchangeController::class, ['names' => 'admin.exchanges']);
    Route::post('exchanges/{id}/process', [ExchangeController::class, 'process'])->name('admin.exchanges.process');
    Route::post('exchanges/{id}/cancel', [ExchangeController::class, 'cancel'])->name('admin.exchanges.cancel');
});
