<?php

use App\Http\Controllers\Backend\Api\Campaign\CampaignProductController;
use App\Http\Controllers\Backend\Api\Chat\ChatController;
use App\Http\Controllers\Backend\Api\Dashboard\DashboardController;
use App\Http\Controllers\Backend\Api\Goal\GoalController;
use App\Http\Controllers\Backend\Api\POS\PosController;
use App\Http\Controllers\Frontend\Api\UserController;
use App\Http\Controllers\Backend\Api\Order\OrderController;
use App\Http\Controllers\Backend\Admin\Orders\OrderController as AdminOrderController;
use App\Http\Controllers\Backend\Admin\Products\ProductController;
use App\Http\Controllers\Backend\Admin\Products\VariationValueController;
use App\Http\Controllers\Backend\Admin\Bizana\DashboardController as BizanaDashboardController;
use App\Http\Controllers\Backend\Admin\Bizana\OrderProfitController;
use App\Http\Controllers\Backend\Admin\BizanaController;
use App\Http\Controllers\Backend\Admin\Inventory\DamageStockController as AdminDamageStockController;
use App\Http\Controllers\Backend\Admin\Bizana\DailyExpenseController as BizanaDailyExpenseController;
use App\Http\Controllers\Backend\Admin\Bizana\MonthlyExpenseController as BizanaMonthlyExpenseController;
use App\Http\Controllers\Backend\Admin\Bizana\RecurringExpenseController as BizanaRecurringExpenseController;
use App\Http\Controllers\Backend\Admin\Bizana\SalesReportController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\ThemeSelectionController;
use App\Http\Controllers\Backend\Admin\WebsiteSetup\MenuController;
use App\Http\Controllers\Backend\Api\FraudCheckController;
use App\Http\Controllers\Backend\Admin\PreOrderController;
use App\Http\Controllers\Backend\Admin\CartController;
use App\Http\Controllers\Backend\Admin\AgentCommissionController;
use App\Http\Controllers\Backend\Admin\Security\IpBlockerController;
use App\Http\Controllers\Backend\Admin\Support\TicketController as SupportTicketController;

use Illuminate\Support\Facades\Route;


Route::group(['prefix' => 'admin/api', 'middleware' => ['auth', 'admin', 'unbanned', 'xss']], function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/new', [DashboardController::class, 'new']);
        Route::get('/total-orders', [DashboardController::class, 'totalOrders']);
        Route::get('/total-sales', [DashboardController::class, 'totalSales']);
        Route::get('/total-sellers', [DashboardController::class, 'totalSellers']);
        Route::get('/total-customers', [DashboardController::class, 'totalCustomers']);
        Route::get('/sales-amount', [DashboardController::class, 'SalesAmount']);
        Route::get('/top-categories', [DashboardController::class, 'topCategories']);
        Route::get('/top-brands', [DashboardController::class, 'topBrands']);
        Route::get('/recent-orders', [DashboardController::class, 'recentOrders'])->name('admin.dashboard.recentOrders');
        Route::get('/order-updates', [DashboardController::class, 'orderUpdates'])->name('admin.dashboard.orderUpdates');
        Route::get('/recent-products', [DashboardController::class, 'recentProducts'])->name('admin.dashboard.recentProducts');
        Route::get('/most-selling-products', [DashboardController::class, 'mostSellingProducts'])->name('admin.dashboard.mostSellingProducts');
        Route::get('/earning-from-sellers', [DashboardController::class, 'earningFromSellers'])->name('admin.dashboard.earningFromSellers');
        Route::get('/top-rated-sellers', [DashboardController::class, 'topRatedSellers'])->name('admin.dashboard.topRatedSellers');
        Route::get('/top-sellers', [DashboardController::class, 'topSellers'])->name('admin.dashboard.topSellers');
    });

    /*
    |--------------------------------------------------------------------------
    | Bizana Business Analytics Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'bizana'], function () {
        // Dashboard API Routes
        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/quick', [BizanaDashboardController::class, 'quickData']);
            Route::get('/detailed', [BizanaDashboardController::class, 'detailedData']);
            Route::get('/delivery-partners', [BizanaDashboardController::class, 'deliveryPartnerData']);
        });

        // Products Analytics Routes
        Route::group(['prefix' => 'products'], function () {
            // Note: analyticsData and priceCalculatorData methods need to be added to BizanaController if needed
            Route::get('/analytics', [BizanaController::class, 'analyticsData']);
            Route::get('/price-calculator', [BizanaController::class, 'priceCalculatorData']);
            Route::patch('/{product}/packaging-cost', [BizanaController::class, 'updatePackagingCost']);
            Route::patch('/{product}/additional-cost', [BizanaController::class, 'updateAdditionalCost']);
            Route::patch('/bulk-packaging-cost', [BizanaController::class, 'bulkUpdatePackagingCost']);
            Route::patch('/bulk-additional-cost', [BizanaController::class, 'bulkUpdateAdditionalCost']);
        });

        // Price Calculator Routes
        Route::group(['prefix' => 'price-calculator'], function () {
            Route::get('/products', [BizanaController::class, 'getProductsForPriceCalculator']);
            Route::get('/filter-options', [BizanaController::class, 'getFilterOptions']);
        });

        // Daily Expenses Routes
        Route::group(['prefix' => 'expenses'], function () {
            Route::get('/data', [BizanaDailyExpenseController::class, 'getExpensesData']);
            Route::get('/categories', [BizanaDailyExpenseController::class, 'getCategories']);
            Route::post('/expenses', [BizanaDailyExpenseController::class, 'store'])->name('admin.bizana.daily-expenses.expenses.store');
            Route::put('/expenses/{id}', [BizanaDailyExpenseController::class, 'update'])->name('admin.bizana.daily-expenses.expenses.update');
            Route::delete('/expenses/{id}', [BizanaDailyExpenseController::class, 'destroy'])->name('admin.bizana.daily-expenses.expenses.destroy');
        });

        // Monthly Expenses Routes
        Route::group(['prefix' => 'monthly-expenses'], function () {
            Route::get('/data', [\App\Http\Controllers\Backend\Admin\Bizana\MonthlyExpenseController::class, 'getMonthlyExpensesData']);
            Route::post('/expenses', [\App\Http\Controllers\Backend\Admin\Bizana\MonthlyExpenseController::class, 'storeExpense'])->name('admin.bizana.monthly-expense.expenses.store');
            Route::put('/expenses/{id}', [\App\Http\Controllers\Backend\Admin\Bizana\MonthlyExpenseController::class, 'updateExpense'])->name('admin.bizana.monthly-expense.expenses.update');
            Route::delete('/expenses/{id}', [\App\Http\Controllers\Backend\Admin\Bizana\MonthlyExpenseController::class, 'destroyExpense'])->name('admin.bizana.monthly-expense.expenses.destroy');
        });

        // Recurring Expenses Routes
        Route::group(['prefix' => 'recurring-expenses'], function () {
            Route::get('/', [BizanaRecurringExpenseController::class, 'index']);
            Route::post('/', [BizanaRecurringExpenseController::class, 'store']);
            Route::put('/{id}', [BizanaRecurringExpenseController::class, 'update']);
            Route::delete('/{id}', [BizanaRecurringExpenseController::class, 'destroy']);
            Route::post('/{id}/toggle-status', [BizanaRecurringExpenseController::class, 'toggleStatus']);
            Route::post('/{id}/generate-now', [BizanaRecurringExpenseController::class, 'generateNow']);
        });

        // Sales Report Routes
        Route::group(['prefix' => 'sales-report'], function () {
            Route::get('/data', [SalesReportController::class, 'getData']);
            Route::get('/export', [SalesReportController::class, 'export']);
        });

        // Order Profit Routes
        Route::group(['prefix' => 'order-profits'], function () {
            Route::get('/', [OrderProfitController::class, 'index']);
            Route::get('/summary', [OrderProfitController::class, 'summary']);
            Route::get('/export', [OrderProfitController::class, 'export']);
            Route::get('/{orderId}', [OrderProfitController::class, 'show']);
        });

        // Shops list for dropdowns
        Route::get('/shops/list', [SalesReportController::class, 'getShops']);
    });

    Route::get('/orders',[OrderController::class, 'index'])->name('admin.allorders');
    Route::get('/orders/export',[OrderController::class, 'export'])->name('admin.orders.export');
    // get single order details
    Route::get('/orders/{orderId}',[AdminOrderController::class, 'getOrderDetails'])->name('admin.orders.show');
    // get return details
    Route::get('/return-details',[OrderController::class, 'getReturnDetails'])->name('admin.return-details');
    // get order items
    Route::get('/orders/{orderId}/items',[OrderController::class, 'getOrderItems'])->name('admin.orders.items');
    // quick order status update
    Route::post('/orders/update-order-status',[OrderController::class, 'updateOrderStatus'])->name('admin.updateOrderStatus');
    // bulk order status update
    Route::post('/orders/bulk-update-status',[OrderController::class, 'bulkUpdateOrderStatus'])->name('admin.bulkUpdateOrderStatus');
    // update order
    Route::put('/orders/{orderId}',[OrderController::class, 'updateOrder'])->name('admin.orders.update');
    // delete order
    Route::delete('/orders/{orderId}',[OrderController::class, 'destroy'])->name('admin.orders.destroy');

    //variation value store
    Route::post('/variation-values/store', [VariationValueController::class, 'store_from_ajax'])->name('admin.store-variation-values');

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
    | Pre-Order Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'pre-orders'], function () {
        Route::get('/', [PreOrderController::class, 'index']);
        Route::get('/summary', [PreOrderController::class, 'summary']);
        Route::get('/products', [PreOrderController::class, 'products']);
        Route::get('/products/{product}', [PreOrderController::class, 'show']);
        Route::post('/products/{product}/settings', [PreOrderController::class, 'updateSettings']);
        Route::post('/products/{product}/variations/{variation}/settings', [PreOrderController::class, 'updateVariationSettings']);
        Route::get('/products/{product}/orders', [PreOrderController::class, 'orders']);
        Route::get('/customer-orders', [PreOrderController::class, 'customerOrders']);
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
    | pos Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'pos'], function () {
        Route::post('/add-to-cart', [PosController::class, 'addToPosCart']);
        Route::post('/cart/from-abandoned', [PosController::class, 'transferAbandonedCartToPos']);
        Route::post('/cart/update', [PosController::class, 'updatePosCart']);
        Route::get('/cart/hold', [PosController::class, 'indexHoldPosCartGroup']);
        Route::post('/cart/hold', [PosController::class, 'holdPosCartGroup']);
        Route::post('/cart/hold/delete/{id}', [PosController::class, 'deleteHoldPosCartGroup']);
        Route::post('/cart/submit-order', [PosController::class, 'storeOrder']);

        Route::get('/filter-data', [PosController::class, 'filterData']);
        Route::get('/product-variations', [PosController::class, 'index']);
        Route::get('/orders/recent', [PosController::class, 'recentOrders']);
        Route::get('/orders/{orderId}/invoice', [PosController::class, 'getOrderInvoice']);
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

    /*
    |--------------------------------------------------------------------------
    | Theme Selection Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'theme-selection'], function () {
        Route::get('/', [ThemeSelectionController::class, 'index']);
        Route::put('/', [ThemeSelectionController::class, 'update']);

        // Header configuration routes
        Route::get('/header/{headerId}/config', [ThemeSelectionController::class, 'getHeaderConfig']);
        Route::put('/header/{headerId}/config', [ThemeSelectionController::class, 'updateHeaderConfig']);
        Route::post('/upload-icon', [ThemeSelectionController::class, 'uploadHeaderIcon']);

        // Footer configuration routes
        Route::get('/footer/config', [ThemeSelectionController::class, 'getFooterConfig']);
        Route::put('/footer/config', [ThemeSelectionController::class, 'updateFooterConfig']);
        Route::post('/footer/upload-image', [ThemeSelectionController::class, 'uploadFooterImage']);
    });

    /*
    |--------------------------------------------------------------------------
    | Menu Builder Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'theme/menu'], function () {
        Route::get('/config', [MenuController::class, 'getMenuConfig']);
        Route::post('/config', [MenuController::class, 'saveMenuConfig']);
        Route::get('/pages', [MenuController::class, 'getPages']);
        Route::get('/categories', [MenuController::class, 'getCategories']);
    });

    /*
    |--------------------------------------------------------------------------
    | Fraud Check Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'fraud-check'], function () {
        Route::post('/courier-info', [FraudCheckController::class, 'checkCourierInfo']);
    });

    /*
    |--------------------------------------------------------------------------
    | Landing Pages Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'landing-pages'], function () {
        Route::get('/', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'store']);
        Route::put('/{id}/status', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'updateStatus']);
        Route::delete('/{id}', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'destroy']);

        // Get media files by IDs
        Route::get('/media-files', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'getMediaFiles']);

        // Get products for selection
        Route::get('/products', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'getProducts']);

        // Section management routes
        Route::post('/{landingId}/sections', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'storeSection']);
        Route::put('/{landingId}/sections/{sectionId}', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'updateSection']);
        Route::delete('/{landingId}/sections/{sectionId}', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'deleteSection']);

        // Reorder sections
        Route::post('/{landingId}/sections/reorder', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'reorderSections']);

        // Update selected products
        Route::put('/{landingId}/selected-products', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'updateSelectedProducts']);

        // Update page configs (colors, global styles)
        Route::put('/{landingId}/page-configs', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'updatePageConfigs']);

        // Get available coupons for selection
        Route::get('/coupons', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'getCoupons']);

        // Update landing page coupon
        Route::put('/{landingId}/coupon', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'updateCoupon']);

        // Get globally enabled payment methods for selection
        Route::get('/payment-methods', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\LandingPageController::class, 'getPaymentMethods']);
    });

    /*
    |--------------------------------------------------------------------------
    | Floating Chat Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'floating-chat'], function () {
        Route::get('/config', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\FloatingChatController::class, 'getConfig']);
        Route::put('/config', [\App\Http\Controllers\Backend\Admin\WebsiteSetup\FloatingChatController::class, 'updateConfig']);
    });

    /*
    |--------------------------------------------------------------------------
    | Cart Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'carts'], function () {
        Route::get('/', [CartController::class, 'index'])->name('admin.carts.index');
        Route::get('/filter-data', [CartController::class, 'getFilterData'])->name('admin.carts.filter-data');
        Route::get('/agents', [CartController::class, 'getAgents'])->name('admin.carts.agents');
        Route::get('/countries', [CartController::class, 'getCountries'])->name('admin.carts.countries');
        Route::post('/validate-coupon', [CartController::class, 'validateCoupon'])->name('admin.carts.validate-coupon');
        Route::get('/get-states/{countryId}', [UserController::class, 'states']);
        Route::get('/get-cities/{stateId}', [UserController::class, 'cities']);
        Route::post('/{id}/confirm-order', [CartController::class, 'confirmOrder'])->name('admin.carts.confirm-order');
        Route::put('/{id}/assign', [CartController::class, 'assignAgent'])->name('admin.carts.assign-agent');
        Route::get('/{id}', [CartController::class, 'show'])->name('admin.carts.show');
    });

    /*
    |--------------------------------------------------------------------------
    | File Manager Routes (Media Manager API)
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'file-manager'], function () {
        Route::post('/upload', [\App\Http\Controllers\Backend\Admin\FileManagerController::class, 'upload']);
        Route::get('/get-uploaded-files', [\App\Http\Controllers\Backend\Admin\FileManagerController::class, 'getUploadedFiles'])->name('api.get_uploaded_files');
        Route::delete('/destroy/{id}', [\App\Http\Controllers\Backend\Admin\FileManagerController::class, 'destroy'])->name('api.destroy_file');
        Route::post('/get-file-by-ids', [\App\Http\Controllers\Backend\Admin\FileManagerController::class, 'getPreviewFiles'])->name('api.get_file_by_ids');
        Route::get('/download/{id}', [\App\Http\Controllers\Backend\Admin\FileManagerController::class, 'attachmentDownload'])->name('api.download_attachment');
        Route::post('/bg-remove/{id}', [\App\Http\Controllers\Backend\Admin\FileManagerController::class, 'backgroundRemove'])->name('api.bg_remove');
        Route::post('/alt-text/{id}', [\App\Http\Controllers\Backend\Admin\FileManagerController::class, 'altText'])->name('api.alt_text');
    });

    /*
    |--------------------------------------------------------------------------
    | Purchase Order Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'purchase-orders'], function () {
        Route::get('/products', [\App\Http\Controllers\Backend\Admin\Inventory\PurchaseOrderController::class, 'getParentProducts']);
        Route::get('/products/{productId}/variations', [\App\Http\Controllers\Backend\Admin\Inventory\PurchaseOrderController::class, 'getProductVariations']);
    });

    /*
    |--------------------------------------------------------------------------
    | Products Quick Update Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'products', 'middleware' => ['permission:edit_products']], function () {
        Route::get('/{id}/quick-edit-data', [ProductController::class, 'getQuickEditData'])->name('admin.api.products.quick-edit-data');
        Route::patch('/{id}/quick-update', [ProductController::class, 'quickUpdate'])->name('admin.api.products.quick-update');
    });

    /*
    |--------------------------------------------------------------------------
    | Damage Stock Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'damage-stocks'], function () {
        Route::get('/', [AdminDamageStockController::class, 'apiIndex']);
        Route::post('/', [AdminDamageStockController::class, 'apiStore']);
        Route::delete('/{id}', [AdminDamageStockController::class, 'apiDestroy']);
    });

    /*
    |--------------------------------------------------------------------------
    | IP Blocker Routes
    |--------------------------------------------------------------------------
    */
    Route::resource('/blocked-ips', IpBlockerController::class, ['names' => 'admin.blocked-ips']);
    Route::post('/blocked-ips/{id}/toggle-status', [IpBlockerController::class, 'toggleStatus'])->name('admin.blocked-ips.toggle-status');

    /*
    |--------------------------------------------------------------------------
    | Support Ticket Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'support'], function () {
        Route::get('/tickets', [SupportTicketController::class, 'index'])->name('admin.tickets.index');
        Route::get('/tickets/{id}', [SupportTicketController::class, 'show'])->name('admin.tickets.show');
        Route::post('/tickets/{id}/reply', [SupportTicketController::class, 'reply'])->name('admin.tickets.reply');
        Route::put('/tickets/{id}/status', [SupportTicketController::class, 'updateStatus'])->name('admin.tickets.update-status');
        Route::put('/tickets/{id}/priority', [SupportTicketController::class, 'updatePriority'])->name('admin.tickets.update-priority');
        Route::put('/tickets/{id}/assign', [SupportTicketController::class, 'assignToAdmin'])->name('admin.tickets.assign');
        Route::delete('/tickets/{id}/attachments/{attachment_id}', [SupportTicketController::class, 'deleteAttachment'])->name('admin.tickets.delete-attachment');
    });

    /*
    |--------------------------------------------------------------------------
    | Agent Commission Routes
    |--------------------------------------------------------------------------
    */
    Route::group(['prefix' => 'agent-commissions'], function () {
        Route::get('/my-balance', [AgentCommissionController::class, 'myBalance'])->name('admin.agent-commissions.my-balance');
        Route::get('/my-commissions', [AgentCommissionController::class, 'myCommissions'])->name('admin.agent-commissions.my-commissions');
        Route::get('/', [AgentCommissionController::class, 'index'])->name('admin.agent-commissions.index');
        Route::get('/{id}', [AgentCommissionController::class, 'show'])->name('admin.agent-commissions.show');
        Route::post('/{id}/pay', [AgentCommissionController::class, 'pay'])->name('admin.agent-commissions.pay');
        Route::post('/pay-bulk', [AgentCommissionController::class, 'payBulk'])->name('admin.agent-commissions.pay-bulk');
        Route::get('/agents/{agentId}', [AgentCommissionController::class, 'agentCommissions'])->name('admin.agent-commissions.agent');
        Route::get('/agents/{agentId}/balance', [AgentCommissionController::class, 'agentBalance'])->name('admin.agent-commissions.agent-balance');
        Route::put('/agents/{agentId}/commission-rate', [AgentCommissionController::class, 'updateRate'])->name('admin.agent-commissions.update-rate');
    });
});
