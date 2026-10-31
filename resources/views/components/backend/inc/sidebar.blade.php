<aside id="admin-sidebar" aria-label="Admin navigation" class="sidebar h-full hover-scrollbar">
    <button type="button" class="admin-sidebar-close" aria-label="Close navigation"><i class="fa-regular fa-xmark" aria-hidden="true"></i></button>
    <ul class="sidebar__list flex-grow">
        <li class="sidebar__logo hidden pl-[57px] py-10">
            <img src="{{ uploadedAsset(getSetting('websiteHeaderLogo')) }}" alt="" class="admin-brand-image max-w-[200px]" />
        </li>

        <li class="sidebar__li {{ areActiveRoutes(['admin.dashboard']) }}">
            <a href="{{ route('admin.dashboard') }}" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-house"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Dashboard') }}</span>
            </a>
        </li>

        @can('bizana_view')
            {{-- Business Analytics --}}
            @php
                $bizanaActiveRoutes = [
                    'admin.bizana.*',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($bizanaActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-chart-line"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Business Analytics') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">
                    @can('bizana_view')
                        <li class="{{ areActiveRoutes(['admin.bizana.index']) }}">
                            <a href="{{ route('admin.bizana.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Daily Overview') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('bizana_view_sales')
                        <li class="{{ areActiveRoutes(['admin.bizana.sales']) }}">
                            <a href="{{ route('admin.bizana.sales') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Sales Report') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('bizana_view_profits')
                        <li class="{{ areActiveRoutes(['admin.bizana.order-profits']) }}">
                            <a href="{{ route('admin.bizana.order-profits') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Order Profits') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('bizana_view_products')
                        <li class="{{ areActiveRoutes(['admin.bizana.price-calculator']) }}">
                            <a href="{{ route('admin.bizana.price-calculator') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Price Calculator') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('bizana_manage_accounting')
                        <li class="{{ areActiveRoutes(['admin.bizana.daily-expenses']) }}">
                            <a href="{{ route('admin.bizana.daily-expenses') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Daily Expenses') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('bizana_manage_accounting')
                        <li class="{{ areActiveRoutes(['admin.bizana.monthly-expenses']) }}">
                            <a href="{{ route('admin.bizana.monthly-expenses') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Monthly Expenses') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcan

        @canany(["view_customers"])
            @php
                $crmRoutes = [
                    'admin.customers.*',
                    'admin.offerManagement',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($crmRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-brands fa-intercom"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('CRM') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">
                    
                    @can('view_customers')
                        <li class="{{ areActiveRoutes(['admin.customers.index', 'admin.customers.show']) }}">
                            <a href="{{ route('admin.customers.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Customers') }}</span>
                            </a>
                        </li> 
                    @endcan 

                    <li class="{{ areActiveRoutes(['admin.offerManagement']) }}">
                        <a href="{{ route('admin.offerManagement') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Offer Management') }}</span>
                        </a>
                    </li>   

                    <li class="{{ areActiveRoutes(['admin.carts.index']) }}">
                        <a href="{{ route('admin.carts.index', ['abandoned' => true]) }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Abandoned Carts') }}</span>
                        </a>
                    </li>   
                </ul>
            </li>
            
        @endcanany
        
        @canany(['view_orders', 'manage_assigned_orders', 'manage_orders', 'pos_orders'])
            {{-- Orders --}}
            {{-- <li class="sidebar__li {{ areActiveRoutes(['admin.orders.index', 'admin.orders.show']) }}">
                <a href="{{ route('admin.orders.index') }}" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-square-poll-vertical"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Orders') }}</span>
                </a>
            </li> --}}
            @php
                $ordersActiveRoutes = ['admin.orders.*', 'admin.return-requests.*', 'admin.exchanges.*', 'admin.pre-orders.*', 'admin.carts.*', 'admin.support.tickets.*'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($ordersActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-square-poll-vertical"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Orders') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">
                    <li class="{{ areActiveRoutes(['admin.orders.*']) }}">
                        <a href="{{ route('admin.orders.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Orders') }}</span>
                        </a>
                    </li>
                    @can('view_orders')
                        <li class="{{ areActiveRoutes(['admin.carts.*']) }}">
                            <a href="{{ route('admin.carts.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Cart List') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_orders')
                        <li class="{{ areActiveRoutes(['admin.return-details.*']) }}">
                            <a href="{{ route('admin.return-details.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Return Details') }}</span>
                            </a>
                        </li>
                    @endcan
                    @canany(['manage_assigned_orders', 'manage_orders'])
                        <li class="{{ areActiveRoutes(['admin.return-requests.*']) }}">
                            <a href="{{ route('admin.return-requests.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Return Refund') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @canany(['manage_assigned_orders', 'manage_orders'])
                        <li class="{{ areActiveRoutes(['admin.exchanges.*']) }}">
                            <a href="{{ route('admin.exchanges.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Exchanges') }}</span>
                            </a>
                        </li>
                    @endcanany
                    @can('view_orders')
                        <li class="{{ areActiveRoutes(['admin.pre-orders.*']) }}">
                            <a href="{{ route('admin.pre-orders.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Pre Orders') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_tickets')
                        <li class="{{ areActiveRoutes(['admin.support.tickets.*']) }}">
                            <a href="/admin/support/tickets" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Support Tickets') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('pos_orders')
            {{-- pos --}}
            <li class="sidebar__li {{ areActiveRoutes(['admin.pos']) }}">
                <a href="{{ route('admin.pos') }}" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-solid fa-bell-concierge"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Pos Panel') }}</span>
                </a>
            </li>
        @endcan

        @canany(['view_products', 'view_categories', 'view_variations', 'view_brands', 'view_units', 'view_taxes',
            'view_badges', 'view_damage_stocks'])
            {{-- products --}}
            @php
                $productsActiveRoutes = [
                    'admin.products.*',
                    'admin.refunds',
                    'admin.units.*',
                    'admin.taxes.*',
                    'admin.badges.*',
                    'admin.brands.*',
                    'admin.collections.*',
                    'admin.categories.*',
                    'admin.variations.*',
                    'admin.variation-values.*',
                    'admin.stock-requests.*',
                    'admin.damageStocks.*',
                ];
            @endphp
            <li class="sidebar__li {{ user()->can('view_products') ? 'expanded' : 'has-submenu' }} {{ areActiveRoutes($productsActiveRoutes, 'active expanded') }}">
                <a href="{{ user()->can('view_products') ? route('admin.products.index') : 'javascript:void(0);' }}" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-cart-plus"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Products') }}</span>

                    @cannot('view_products')
                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                    @endcannot
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">
                    @can('view_products')
                        <li class="{{ areActiveRoutes(['admin.products.*']) }}">
                            <a href="{{ route('admin.products.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Products') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_categories')
                        <li class="{{ areActiveRoutes(['admin.categories.*']) }}">
                            <a href="{{ route('admin.categories.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Categories') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_variations')
                        <li class="{{ areActiveRoutes(['admin.variations.*', 'admin.variation-values.*']) }}">
                            <a href="{{ route('admin.variations.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Variations') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_brands')
                        <li class="{{ areActiveRoutes(['admin.brands.*', 'admin.collections.*']) }}">
                            <a href="{{ route('admin.brands.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Brands & Collections') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_units')
                        <li class="{{ areActiveRoutes(['admin.units.*']) }}">
                            <a href="{{ route('admin.units.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Units') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_taxes')
                        <li class="{{ areActiveRoutes(['admin.taxes.*']) }}">
                            <a href="{{ route('admin.taxes.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Taxes') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_badges')
                        <li class="{{ areActiveRoutes(['admin.badges.*']) }}">
                            <a href="{{ route('admin.badges.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Badges') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_stock_requests')
                        @php
                            $pendingCount = \App\Models\StockRequest::pending()->count();
                        @endphp
                        <li class="{{ areActiveRoutes(['admin.stock-requests.*']) }}">
                            <a href="{{ route('admin.stock-requests.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Stock Requests') }}</span>
                                @if($pendingCount > 0)
                                    <span class="ms-auto badge bg-danger text-white">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                    @endcan

                    @can('view_damage_stocks')
                        @if (!useInventory())
                            <li class="{{ areActiveRoutes(['admin.damageStocks.*']) }}">
                                <a href="{{ route('admin.damageStocks.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Damage Stock') }}</span>
                                </a>
                            </li>
                        @endif
                    @endcan

                </ul>
            </li>
        @endcanany

        @canany(['view_purchase_orders', 'view_return_purchase_orders', 'view_stock_adjustment', 'view_stock_transfer',
            'view_stock_report', 'view_suppliers', 'view_reports', 'view_damage_stocks'])
            @if (useInventory())
                {{-- Inventory --}}
                @php
                    $inventoryActiveRoutes = [
                        'admin.suppliers.*',
                        'admin.purchase-orders.*',
                        'admin.purchase-return.*',
                        'admin.stockAdjustments.*',
                        'admin.damageStocks.*',
                        'admin.stockTransfers.*',
                        'admin.stockReports.*',
                        'admin.supplierDueReports.*',
                        'admin.productExpiryReports.*',
                    ];
                @endphp
                <li class="sidebar__li has-submenu {{ areActiveRoutes($inventoryActiveRoutes, 'active expanded') }}">
                    <a href="javascript:void(0);" class="sidebar__item">
                        <span class="sidebar__item--icon">
                            <i class="fa-regular  fa-calendar-check"></i>
                        </span>
                        <span class="sidebar__item--text">{{ translate('Inventory') }}</span>

                        <span class="sidebar__list--toggler" aria-hidden="true">
                            <i class="fa-regular fa-chevron-down"></i>
                        </span>
                    </a>

                    <ul class="text-foreground divide-y divide-border bg-primary/5">

                        @can('view_purchase_orders')
                            <li class="{{ areActiveRoutes(['admin.purchase-orders.*']) }}">
                                <a href="{{ route('admin.purchase-orders.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Purchase Orders') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_return_purchase_orders')
                            <li class="{{ areActiveRoutes(['admin.purchase-return.*']) }}">
                                <a href="{{ route('admin.purchase-return.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Purchases Return') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_stock_adjustment')
                            <li class="{{ areActiveRoutes(['admin.stockAdjustments.*']) }}">
                                <a href="{{ route('admin.stockAdjustments.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Stock Adjustment') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_damage_stocks')
                            <li class="{{ areActiveRoutes(['admin.damageStocks.*']) }}">
                                <a href="{{ route('admin.damageStocks.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Damage Stock') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_stock_transfer')
                            <li class="{{ areActiveRoutes(['admin.stockTransfers.*']) }}">
                                <a href="{{ route('admin.stockTransfers.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Stock Transfer') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_stock_report')
                            <li class="{{ areActiveRoutes(['admin.stockReports.*']) }}">
                                <a href="{{ route('admin.stockReports.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Stock Reports') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_reports')
                            <li class="{{ areActiveRoutes(['admin.productExpiryReports.*']) }}">
                                <a href="{{ route('admin.productExpiryReports.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Product Expiry Reports') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_suppliers')
                            <li class="{{ areActiveRoutes(['admin.supplierDueReports.*']) }}">
                                <a href="{{ route('admin.supplierDueReports.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Supplier Due Reports') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('view_suppliers')
                            <li class="{{ areActiveRoutes(['admin.suppliers.*']) }}">
                                <a href="{{ route('admin.suppliers.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Suppliers') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
        @endcanany 


        @if (config('app.app_mode') != 'singleVendor')
            @canany(['view_sellers', 'show_payouts', 'show_payout_requests', 'show_earning_histories'])
                {{-- sellers --}}
                @php
                    $sellersActiveRoutes = ['admin.sellers.*'];
                @endphp
                <li class="sidebar__li has-submenu {{ areActiveRoutes($sellersActiveRoutes, 'active expanded') }}">
                    <a href="javascript:void(0);" class="sidebar__item">
                        <span class="sidebar__item--icon">
                            <i class="fa-regular fa-shop"></i>
                        </span>
                        <span class="sidebar__item--text">{{ translate('Seller Management') }}</span>

                        <span class="sidebar__list--toggler" aria-hidden="true">
                            <i class="fa-regular fa-chevron-down"></i>
                        </span>
                    </a>

                    <ul class="text-foreground divide-y divide-border bg-primary/5">

                        @can('view_sellers')
                            <li class="{{ areActiveRoutes(['admin.sellers.index', 'admin.sellers.edit']) }}">
                                <a href="{{ route('admin.sellers.index') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Sellers') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('show_payouts')
                            <li class="{{ areActiveRoutes(['admin.sellers.payouts']) }}">
                                <a href="{{ route('admin.sellers.payouts') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Payouts') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('show_payout_requests')
                            <li class="{{ areActiveRoutes(['admin.sellers.payoutRequests']) }}">
                                <a href="{{ route('admin.sellers.payoutRequests') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Payout Requests') }}</span>
                                </a>
                            </li>
                        @endcan
                        @can('show_earning_histories')
                            <li class="{{ areActiveRoutes(['admin.sellers.earnings']) }}">
                                <a href="{{ route('admin.sellers.earnings') }}" class="py-3 w-full transition-all">
                                    <span class="sub-item--text">{{ translate('Earning Histories') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcanany
        @endif

        @can('view_tags')
            {{-- resources --}}
            @php
                $resourcesActiveRoutes = ['admin.tags.*'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($resourcesActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-copy"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Resources') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">
                    <li class="{{ areActiveRoutes(['admin.tags.*']) }}">
                        <a href="{{ route('admin.tags.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Tags') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endcan

        @canany(['view_coupons', 'view_campaigns', 'view_subscribers'])
            {{-- promotions --}}
            @php
                $promotionsActiveRoutes = [
                    'admin.coupons.*',
                    'admin.campaigns.*',
                    'admin.subscribers.index',
                    'admin.newsletters.index',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($promotionsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-wand-magic-sparkles"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Promotions') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">
                    @can('view_coupons')
                        <li class="{{ areActiveRoutes(['admin.coupons.*']) }}">
                            <a href="{{ route('admin.coupons.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Coupons') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_campaigns')
                        <li class="{{ areActiveRoutes(['admin.campaigns.*']) }}">
                            <a href="{{ route('admin.campaigns.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Campaign') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_subscribers')
                        <li class="{{ areActiveRoutes(['admin.subscribers.index']) }}">
                            <a href="{{ route('admin.subscribers.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Subscribers') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('send_newsletters')
                        <li class="{{ areActiveRoutes(['admin.newsletters.index']) }}">
                            <a href="{{ route('admin.newsletters.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Newsletters') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['view_countries', 'view_states', 'view_cities', 'view_areas', 'view_zones', 'view_warehouses',
            'delivery_charges'])
            {{-- shipping --}}
            @php
                $shippingActiveRoutes = [
                    'admin.countries.*',
                    'admin.states.*',
                    'admin.cities.*',
                    'admin.areas.*',
                    'admin.zones.*',
                    'admin.warehouses.*',
                    'admin.shipping-classes.*',
                    'admin.delivery-charges.*',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($shippingActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-shipping-fast"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Shipping & Delivery') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                {{-- country, state, city, area, zone, warehouses --}}
                <ul class="text-foreground divide-y divide-border bg-primary/5">

                    @can('view_countries')
                        <li class="{{ areActiveRoutes(['admin.countries.*']) }}">
                            <a href="{{ route('admin.countries') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Countries') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_states')
                        <li class="{{ areActiveRoutes(['admin.states.*']) }}">
                            <a href="{{ route('admin.states.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Districts') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_cities')
                        <li class="{{ areActiveRoutes(['admin.cities.*']) }}">
                            <a href="{{ route('admin.cities.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Cities') }}</span>
                            </a>
                        </li>
                    @endcan

                    {{-- @can('view_areas')
                        <li class="{{ areActiveRoutes(['admin.areas.*']) }}">
                            <a href="{{ route('admin.areas.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Areas') }}</span>
                            </a>
                        </li>
                    @endcan --}}

                    @can('view_zones')
                        <li class="{{ areActiveRoutes(['admin.zones.*']) }}">
                            <a href="{{ route('admin.zones.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Zones') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_warehouses')
                        <li class="{{ areActiveRoutes(['admin.warehouses.*']) }}">
                            <a href="{{ route('admin.warehouses.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Warehouses') }}</span>
                            </a>
                        </li>
                    @endcan

                    <li class="{{ areActiveRoutes(['admin.shipping-classes.*']) }}">
                        <a href="{{ route('admin.shipping-classes.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Shipping Class') }}</span>
                        </a>
                    </li>

                    @can('delivery_charges')
                        <li class="{{ areActiveRoutes(['admin.delivery-charges.*']) }}">
                            <a href="{{ route('admin.delivery-charges.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Delivery Charges') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @can('file_manager')
            {{-- file manager --}}
            <li class="sidebar__li  {{ areActiveRoutes(['file-manager.index']) }}">
                <a href="{{ route('file-manager.index') }}" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-image"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('File Management') }}</span>
                </a>
            </li>
        @endcan

        @can('conversation')
            <li class="sidebar__li  {{ areActiveRoutes(['admin.chat']) }}">
                <a href="{{ route('admin.chat') }}" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-message"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Conversations') }}</span>
                </a>
            </li>
        @endcan 

        @canany(['view_home_sections', 'configurations'])
            {{-- shop settings --}}
            @php
                $shopSettingsActiveRoutes = ['admin.shop-sections.*', 'admin.shops.*'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($shopSettingsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-shop-lock"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Shop Settings') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">
                    @can('view_home_sections')
                        <li class="{{ areActiveRoutes(['admin.shop-sections.*']) }}">
                            <a href="{{ route('admin.shop-sections.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Home Sections') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('configurations')
                        <li class="{{ areActiveRoutes(['admin.shops.profile']) }}">
                            <a href="{{ route('admin.shops.profile') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Configurations') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        
        @canany(['view_blogs', 'view_blog_categories'])
            {{-- blog system --}}
            @php
                $blogsActiveRoutes = ['admin.blogs.*', 'admin.blogCategories.*'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($blogsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-book-open"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Blog System') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">

                    @can('view_blogs')
                        <li class="{{ areActiveRoutes(['admin.blogs.*']) }}">
                            <a href="{{ route('admin.blogs.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('All Blogs') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_blog_categories')
                        <li class="{{ areActiveRoutes(['admin.blogCategories.*']) }}">
                            <a href="{{ route('admin.blogCategories.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Blog Categories') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @if(auth()->check() && auth()->user()->user_type === 'staff')
            <li class="sidebar__li {{ areActiveRoutes(['admin.agent-commissions.*']) }}">
                <a href="{{ route('admin.agent-commissions.index') }}" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-money-check-dollar"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('My Commissions') }}</span>
                </a>
            </li>
        @endif

        @canany(['view_staffs', 'view_roles_and_permissions'])
            {{-- staffs --}}
            @php
                $staffsActiveRoutes = ['admin.staffs.*', 'admin.roles.*'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($staffsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-user-lock"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Staff Management') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">

                    @can('view_staffs')
                        <li class="{{ areActiveRoutes(['admin.staffs.*']) }}">
                            <a href="{{ route('admin.staffs.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('All Staffs') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('view_roles_and_permissions')
                        <li class="{{ areActiveRoutes(['admin.roles.*']) }}">
                            <a href="{{ route('admin.roles.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Roles') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>

        @endcanany

        @canany(['view_pages', 'homepage', 'color_and_branding'])
            {{-- website setup --}}
            @php
                $shopSettingsActiveRoutes = ['admin.pages.*', 'admin.homepage.configure', 'admin.product-settings.*', 'admin.colorBranding.index', 'admin.landing-pages.*', 'admin.floating-chat.*'];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($shopSettingsActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-desktop"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Website Setup') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">
                    @can('homepage')
                        <li class="{{ areActiveRoutes(['admin.theme.selection']) }}">
                            <a href="{{ route('admin.theme.selection') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Improved Theme Selection') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('homepage')
                        <li class="{{ areActiveRoutes(['admin.homepage.configure', 'admin.botanical-home.*']) }}">
                            @php
                                $homepageId = 1;
                            @endphp
                            <a href="{{ route('admin.botanical-home.edit') }}"
                                class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Home Page') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_pages')
                        <li class="{{ areActiveRoutes(['admin.pages.*']) }}">
                            <a href="{{ route('admin.pages.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Pages') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('homepage')
                        <li class="{{ areActiveRoutes(['admin.product-settings.*']) }}">
                            <a href="{{ route('admin.product-settings.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Product Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_pages')
                        <li class="{{ areActiveRoutes(['admin.landing-pages.*']) }}">
                            <a href="{{ route('admin.landing-pages.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Landing Pages') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('color_and_branding')
                        <li class="{{ areActiveRoutes(['admin.colorBranding.index']) }}">
                            <a href="{{ route('admin.colorBranding.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Color & Branding') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('homepage')
                        <li class="{{ areActiveRoutes(['admin.floating-chat.*']) }}">
                            <a href="{{ route('admin.floating-chat.configuration') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Floating Chat') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

        @canany(['general_settings', 'order_settings', 'smtp_settings', 'payment_methods', 'social_media_login',
            'view_languages', 'view_products', 'view_blocked_ips'])
            {{-- configurations --}}
            @php
                $configActiveRoutes = [
                    'admin.general-settings.index',
                    'admin.general-settings.update',
                    'admin.order-settings.index',
                    'admin.smtp-settings.index',
                    'test.smtp',
                    'admin.env-key.update',
                    'admin.payment-method.index',
                    'admin.payment-method.update',
                    'admin.social-login.index',
                    'admin.languages.*',
                    'admin.analytics.index',
                    'admin.otp_settings.index',
                    'admin.delivery-partner.index',
                    'admin.facebook-feed.index',
                    'admin.facebook-feed.generate',
                    'admin.facebook-feed.download',
                    'admin.ip-blocker.index',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($configActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular fa-gear"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('System Setup') }}</span>

                    <span class="sidebar__list--toggler" aria-hidden="true">
                        <i class="fa-regular fa-chevron-down"></i>
                    </span>
                </a>

                <ul class="text-foreground divide-y divide-border bg-primary/5">
                    @can('general_settings')
                        <li class="{{ areActiveRoutes(['admin.general-settings.index']) }}">
                            <a href="{{ route('admin.general-settings.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('General Settings') }}</span>
                            </a>
                        </li>
                        <li class="{{ areActiveRoutes(['admin.database-backups.index']) }}">
                            <a href="{{ route('admin.database-backups.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Database Backups') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('order_settings')
                        <li class="{{ areActiveRoutes(['admin.order-settings.index']) }}">
                            <a href="{{ route('admin.order-settings.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Order Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_languages')
                        <li class="{{ areActiveRoutes(['admin.languages.*']) }}">
                            <a href="{{ route('admin.languages.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Language Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('smtp_settings')
                        <li class="{{ areActiveRoutes(['admin.smtp-settings.index']) }}">
                            <a href="{{ route('admin.smtp-settings.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('SMTP Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('payment_methods')
                        <li class="{{ areActiveRoutes(['admin.payment-method.index']) }}">
                            <a href="{{ route('admin.payment-method.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Payment Methods') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('social_media_login')
                        <li class="{{ areActiveRoutes(['admin.social-login.index']) }}">
                            <a href="{{ route('admin.social-login.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Social Media Login') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('social_media_login')
                        <li class="{{ areActiveRoutes(['admin.otp_settings.index']) }}">
                            <a href="{{ route('admin.otp_settings.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('OTP Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('payment_methods')
                        <li class="{{ areActiveRoutes(['admin.delivery-partner.index']) }}">
                            <a href="{{ route('admin.delivery-partner.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Delivery Partner Settings') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('social_media_login')
                        <li class="{{ areActiveRoutes(['admin.analytics.index']) }}">
                            <a href="{{ route('admin.analytics.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Analytics') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_products')
                        <li class="{{ areActiveRoutes(['admin.facebook-feed.index']) }}">
                            <a href="{{ route('admin.facebook-feed.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('Facebook Feed') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('view_blocked_ips')
                        <li class="{{ areActiveRoutes(['admin.ip-blocker.index']) }}">
                            <a href="{{ route('admin.ip-blocker.index') }}" class="py-3 w-full transition-all">
                                <span class="sub-item--text">{{ translate('IP Blocker') }}</span>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany

    </ul>

    <div
        class="sticky bottom-0 bg-background px-[15px] pb-3 pt-5 border-t border-border grid grid-cols-2 lg:hidden items-center gap-4">
        <div class="option-dropdown" tabindex="0">
            @php
                if (Session::has('locale')) {
                    $locale = Session::get('locale', Config::get('app.locale'));
                } else {
                    $locale = env('DEFAULT_LANGUAGE');
                }

                $currentLanguage = \App\Models\Language::where('code', $locale)->first();

                if (is_null($currentLanguage)) {
                    $currentLanguage = \App\Models\Language::where('code', 'en-US')->first();
                }
            @endphp

            <div class="option-dropdown__toggler option-dropdown__toggler--icon-small bg-background text-foreground">
                <span>
                    <img src="{{ asset('images/flags/' . $currentLanguage->flag . '.png') }}" alt=""
                        class="w-[20px]" />
                </span>
                <span>{{ $currentLanguage->name }}</span>
            </div>

            <div class="option-dropdown__options option-dropdown__options--top">
                <ul>
                    @foreach (\App\Models\Language::where('is_active', 1)->get() as $key => $language)
                        <li>
                            <a href="javascript:void(0);"
                                class="option-dropdown__option @if ($currentLanguage->code == $language->code) active @endif"
                                onclick="changeLocaleLanguage(this)" data-flag="{{ $language->code }}">
                                <span>
                                    <img src="{{ asset('images/flags/' . $language->flag . '.png') }}"
                                        alt="" class="w-[20px]" />
                                </span>
                                <span>{{ $language->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <a href="{{ url('/') }}" target="_blank"
            class="whitespace-nowrap h-10 inline-flex items-center gap-2 bg-primary text-white rounded-md px-4 font-bold border-2 border-transparent hover:bg-transparent hover:border-primary hover:text-primary">
            <span>
                <i class="fa-light fa-globe-pointer"></i>
            </span>
            <span>{{ translate('Browse') }}</span>
        </a>
    </div>

    <div class="copyright pl-[57px] pb-9">
        <p class="text-muted leading-none">
            {{ translate('Powered By') }}
        </p>
        <a href="">
            <img src="{{ uploadedAsset(getSetting('poweredBy')) }}" alt="" />
        </a>
    </div>
</aside>
