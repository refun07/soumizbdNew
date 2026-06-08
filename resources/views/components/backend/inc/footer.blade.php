<!-- start::mobile bottom nav -->
<div class="mobile-navbar">
    <ul class="flex items-center">
        <li>
            <a href="{{ route(routePrefix() . '.dashboard') }}" class="text-center leading-none px-3">
                <span class="text-base text-secondary"><i class="fa-regular fa-house"></i></span>
                <br />
                <span class="text-[11px] text-muted">{{ translate('Home') }}</span>
            </a>
        </li>
        @if (user()->user_type == 'seller')
            <li>
                <a href="{{ route(routePrefix() . '.orders.index') }}" class="text-center leading-none px-3">
                    <span class="text-base text-secondary"><i class="fa-regular fa-cart-shopping"></i></span>
                    <br />
                    <span class="text-[11px] text-muted">{{ translate('Orders') }}</span>
                </a>
            </li>
        @else
            @canany(['view_orders', 'manage_assigned_orders', 'manage_orders'])
                <li>
                    <a href="{{ route(routePrefix() . '.orders.index') }}" class="text-center leading-none px-3">
                        <span class="text-base text-secondary"><i class="fa-regular fa-cart-shopping"></i></span>
                        <br />
                        <span class="text-[11px] text-muted">{{ translate('Orders') }}</span>
                    </a>
                </li>
            @endcanany
        @endif

        <li>
            <div class="option-dropdown relative -translate-y-5" tabindex="0">
                <div
                    class="option-dropdown__toggler no-style no-arrow border-4 border-border flex items-center justify-center w-[55px] aspect-square rounded-full bg-primary text-white text-2xl">
                    <i class="fal fa-plus"></i>
                </div>

                <div class="option-dropdown__options bottom-full top-auto left-1/2 -translate-x-1/2 min-w-[180px]"
                    data-placement="">
                    <ul>
                        @if (user()->user_type == 'seller')
                            <li>
                                <a href="{{ route(routePrefix() . '.products.create') }}"
                                    class="option-dropdown__option">{{ translate('Product') }}</a>
                            </li>
                            <li>
                                <a href="{{ route(routePrefix() . '.purchase-orders.create') }}"
                                    class="option-dropdown__option">{{ translate('Purchase Order') }}</a>
                            </li>

                            <li>
                                <a href="{{ route(routePrefix() . '.stockAdjustments.create') }}"
                                    class="option-dropdown__option">{{ translate('Stock Adjustment') }}</a>
                            </li>
                            <li>
                                <a href="{{ route(routePrefix() . '.stockTransfers.create') }}"
                                    class="option-dropdown__option">{{ translate('Stock Transfer') }}</a>
                            </li>
                            <li>
                                <a href="{{ route(routePrefix() . '.suppliers.create') }}"
                                    class="option-dropdown__option">{{ translate('Supplier') }}</a>
                            </li>
                            <li>
                                <a href="{{ route(routePrefix() . '.coupons.create') }}"
                                    class="option-dropdown__option">{{ translate('Coupon') }}</a>
                            </li>
                            <li>
                                <a href="{{ route(routePrefix() . '.campaigns.create') }}"
                                    class="option-dropdown__option">{{ translate('Campaign') }}</a>
                            </li>
                        @else
                            @can('create_products')
                                <li>
                                    <a href="{{ route(routePrefix() . '.products.create') }}"
                                        class="option-dropdown__option">{{ translate('Product') }}</a>
                                </li>
                            @endcan

                            @can('create_purchase_orders')
                                <li>
                                    <a href="{{ route(routePrefix() . '.purchase-orders.create') }}"
                                        class="option-dropdown__option">{{ translate('Purchase Order') }}</a>
                                </li>
                            @endcan

                            @can('create_stock_adjustment')
                                <li>
                                    <a href="{{ route(routePrefix() . '.stockAdjustments.create') }}"
                                        class="option-dropdown__option">{{ translate('Stock Adjustment') }}</a>
                                </li>
                            @endcan

                            @can('create_stock_transfer')
                                <li>
                                    <a href="{{ route(routePrefix() . '.stockTransfers.create') }}"
                                        class="option-dropdown__option">{{ translate('Stock Transfer') }}</a>
                                </li>
                            @endcan

                            @can('create_suppliers')
                                <li>
                                    <a href="{{ route(routePrefix() . '.suppliers.create') }}"
                                        class="option-dropdown__option">{{ translate('Supplier') }}</a>
                                </li>
                            @endcan

                            @can('create_coupons')
                                <li>
                                    <a href="{{ route(routePrefix() . '.coupons.create') }}"
                                        class="option-dropdown__option">{{ translate('Coupon') }}</a>
                                </li>
                            @endcan

                            @can('create_campaigns')
                                <li>
                                    <a href="{{ route(routePrefix() . '.campaigns.create') }}"
                                        class="option-dropdown__option">{{ translate('Campaign') }}</a>
                                </li>
                            @endcan
        @endif

                    </ul>
                </div>
            </div>
        </li>
        <li>
            <button href="#" class="toggle-mobile-search text-center leading-none px-3">
                <span class="text-base text-secondary"><i class="fa-regular fa-search"></i></span>
                <br />
                <span class="text-[11px] text-muted">{{ translate('Search') }}</span>
            </button>
        </li>

        @if (user()->user_type == 'seller')
            <li>
                <a href="{{ route(routePrefix() . '.notifications') }}" class="text-center leading-none px-3">
                    <span class="text-base text-secondary"><i class="fa-regular fa-bell"></i></span>
                    <br />
                    <span class="text-[11px] text-muted">{{ translate('Notifications') }}</span>
                </a>
            </li>
        @else
            @can('show_notifications')
                <li>
                    <a href="{{ route(routePrefix() . '.notifications') }}" class="text-center leading-none px-3">
                        <span class="text-base text-secondary"><i class="fa-regular fa-bell"></i></span>
                        <br />
                        <span class="text-[11px] text-muted">{{ translate('Notifications') }}</span>
                    </a>
                </li>
            @endcan
        @endif
    </ul>
</div>
<!-- end::mobile bottom nav -->
