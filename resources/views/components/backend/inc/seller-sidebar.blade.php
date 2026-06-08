<aside class="sidebar h-full hover-scrollbar">
    <div class="flex flex-col items-center text-center pt-6 px-3">
        <img src="{{ uploadedAsset(shop()->logo) }}" alt=""
            class="w-[100px] aspect-square mb-4 rounded-full border border-border"
            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';">
        <p class="line-clamp-2 shop-name">{{ shop()->name }}</p>

        <div class="flex items-center justify-center gap-2 shop-status">
            @if (shop()->is_approved)
                <span class="text-theme-green text-xl">
                    <i class="fa-solid fa-badge-check"></i>
                </span>
                <span class="text-muted">{{ translate('Shop Approved') }}</span>
            @else
                <span class="text-theme-alert text-xl">
                    <i class="fa-solid fa-octagon-xmark"></i>
                </span>
                <span class="text-muted">{{ translate('Shop Approval Pending') }}</span>
            @endif
        </div>
    </div>

    <ul class="sidebar__list flex-grow mb-16">

        <li class="sidebar__li {{ areActiveRoutes(['seller.dashboard']) }}">
            <a href="{{ route('seller.dashboard') }}" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-house"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Dashboard') }}</span>
            </a>
        </li>

        {{-- Orders --}}
        @php
            $ordersActiveRoutes = ['seller.orders.*', 'seller.return-requests.*', 'seller.exchanges.*'];
        @endphp
        <li class="sidebar__li has-submenu {{ areActiveRoutes($ordersActiveRoutes, 'active expanded') }}">
            <a href="javascript:void(0);" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-cart-plus"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Orders') }}</span>

                <button class="sidebar__list--toggler">
                    <i class="fa-regular fa-chevron-down"></i>
                </button>
            </a>

            <ul>
                <li class="{{ areActiveRoutes(['seller.orders.*']) }}">
                    <a href="{{ route('seller.orders.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Orders') }}</span>
                    </a>
                </li>
                <li class="{{ areActiveRoutes(['seller.return-requests.*']) }}">
                    <a href="{{ route('seller.return-requests.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Return Refund') }}</span>
                    </a>
                </li>
                <li class="{{ areActiveRoutes(['seller.exchanges.*']) }}">
                    <a href="{{ route('seller.exchanges.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Exchanges') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- pos --}}
        <li class="sidebar__li {{ areActiveRoutes(['seller.pos']) }}">
            <a href="{{ route('seller.pos') }}" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-solid fa-bell-concierge"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Pos Panel') }}</span>
            </a>
        </li>

        {{-- sellers --}}
        @php
            $earningsActiveRoutes = ['seller.earnings.*'];
        @endphp
        <li class="sidebar__li has-submenu {{ areActiveRoutes($earningsActiveRoutes, 'active expanded') }}">
            <a href="javascript:void(0);" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-money-bill-trend-up"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Earnings') }}</span>

                <button class="sidebar__list--toggler">
                    <i class="fa-regular fa-chevron-down"></i>
                </button>
            </a>

            <ul class="text-foreground divide-y divide-border bg-primary/5">
                <li class="{{ areActiveRoutes(['seller.earnings.payouts']) }}">
                    <a href="{{ route('seller.earnings.payouts') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Payouts') }}</span>
                    </a>
                </li>


                <li class="{{ areActiveRoutes(['seller.earnings.requests']) }}">
                    <a href="{{ route('seller.earnings.requests') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Payout Requests') }}</span>
                    </a>
                </li>

                <li class="{{ areActiveRoutes(['seller.earnings.histories']) }}">
                    <a href="{{ route('seller.earnings.histories') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Earning Histories') }}</span>
                    </a>
                </li>

                <li class="{{ areActiveRoutes(['seller.earnings.payoutSettings']) }}">
                    <a href="{{ route('seller.earnings.payoutSettings') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Payout Settings') }}</span>
                    </a>
                </li>

            </ul>
        </li>


        {{-- products --}}
        @php
            $productsActiveRoutes = ['seller.products.*', 'seller.damageStocks.*'];
        @endphp
        <li class="sidebar__li has-submenu {{ areActiveRoutes($productsActiveRoutes, 'active expanded') }}">
            <a href="javascript:void(0);" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-cart-plus"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Products') }}</span>

                <button class="sidebar__list--toggler">
                    <i class="fa-regular fa-chevron-down"></i>
                </button>
            </a>

            <ul>
                <li class="{{ areActiveRoutes(['seller.products.*']) }}">
                    <a href="{{ route('seller.products.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Products') }}</span>
                    </a>
                </li>

                @if (!useInventory())
                    <li class="{{ areActiveRoutes(['seller.damageStocks.*']) }}">
                        <a href="{{ route('seller.damageStocks.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Damage Stock') }}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>


        @if (useInventory())
            {{-- Inventory --}}
            @php
                $inventoryActiveRoutes = [
                    'seller.suppliers.*',
                    'seller.purchase-orders.*',
                    'seller.purchase-return.*',
                    'seller.stockAdjustments.*',
                    'seller.stockTransfers.*',
                ];
            @endphp
            <li class="sidebar__li has-submenu {{ areActiveRoutes($inventoryActiveRoutes, 'active expanded') }}">
                <a href="javascript:void(0);" class="sidebar__item">
                    <span class="sidebar__item--icon">
                        <i class="fa-regular  fa-calendar-check"></i>
                    </span>
                    <span class="sidebar__item--text">{{ translate('Inventory') }}</span>

                    <button class="sidebar__list--toggler">
                        <i class="fa-regular fa-chevron-down"></i>
                    </button>
                </a>

                <ul class="">

                    <li class="{{ areActiveRoutes(['seller.purchase-orders.*']) }}">
                        <a href="{{ route('seller.purchase-orders.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Purchase Orders') }}</span>
                        </a>
                    </li>

                    <li class="{{ areActiveRoutes(['seller.purchase-return.*']) }}">
                        <a href="{{ route('seller.purchase-return.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Purchases Return') }}</span>
                        </a>
                    </li>

                    <li class="{{ areActiveRoutes(['seller.stockAdjustments.*']) }}">
                        <a href="{{ route('seller.stockAdjustments.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Stock Adjustment') }}</span>
                        </a>
                    </li>

                    <li class="{{ areActiveRoutes(['seller.damageStocks.*']) }}">
                        <a href="{{ route('seller.damageStocks.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Damage Stock') }}</span>
                        </a>
                    </li>

                    <li class="{{ areActiveRoutes(['seller.stockTransfers.*']) }}">
                        <a href="{{ route('seller.stockTransfers.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Stock Transfer') }}</span>
                        </a>
                    </li>

                    <li class="{{ areActiveRoutes(['seller.suppliers.*']) }}">
                        <a href="{{ route('seller.suppliers.index') }}" class="py-3 w-full transition-all">
                            <span class="sub-item--text">{{ translate('Suppliers') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        {{-- promotions --}}
        @php
            $promotionsActiveRoutes = ['seller.coupons.*', 'seller.campaigns.*'];
        @endphp
        <li class="sidebar__li has-submenu {{ areActiveRoutes($promotionsActiveRoutes, 'active expanded') }}">
            <a href="javascript:void(0);" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-wand-magic-sparkles"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Promotions') }}</span>

                <button class="sidebar__list--toggler">
                    <i class="fa-regular fa-chevron-down"></i>
                </button>
            </a>

            <ul class="">
                <li class="{{ areActiveRoutes(['seller.coupons.*']) }}">
                    <a href="{{ route('seller.coupons.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Coupons') }}</span>
                    </a>
                </li>
                <li class="{{ areActiveRoutes(['seller.campaigns.*']) }}">
                    <a href="{{ route('seller.campaigns.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Campaign') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- shipping --}}
        @php
            $shippingActiveRoutes = ['seller.warehouses.*', 'seller.delivery-charges.*', 'admin.shipping-classes.*'];
        @endphp
        <li class="sidebar__li has-submenu {{ areActiveRoutes($shippingActiveRoutes, 'active expanded') }}">
            <a href="javascript:void(0);" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-shipping-fast"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Shipping & Delivery') }}</span>

                <button class="sidebar__list--toggler">
                    <i class="fa-regular fa-chevron-down"></i>
                </button>
            </a>

            {{-- country, state, city, area, zone, warehouses --}}
            <ul class="">
                <li class="{{ areActiveRoutes(['seller.warehouses.*']) }}">
                    <a href="{{ route('seller.warehouses.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Warehouses') }}</span>
                    </a>
                </li>

                <li class="{{ areActiveRoutes(['seller.shipping-classes.*']) }}">
                    <a href="{{ route('seller.shipping-classes.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Shipping Class') }}</span>
                    </a>
                </li>

                <li class="{{ areActiveRoutes(['seller.delivery-charges.*']) }}">
                    <a href="{{ route('seller.delivery-charges.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Delivery Charges') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        <li class="sidebar__li  {{ areActiveRoutes(['seller.fileManagers.index']) }}">
            <a href="{{ route('seller.fileManagers.index') }}" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-image"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('File Manager') }}</span>
            </a>
        </li>

        <li class="sidebar__li  {{ areActiveRoutes(['seller.chat']) }}">
            <a href="{{ route('seller.chat') }}" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-message"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Conversations') }}</span>
            </a>
        </li>

        {{-- shop settings --}}
        @php
            $shopSettingsActiveRoutes = ['seller.shop-sections.*', 'seller.shops.*'];
        @endphp
        <li class="sidebar__li has-submenu {{ areActiveRoutes($shopSettingsActiveRoutes, 'active expanded') }}">
            <a href="javascript:void(0);" class="sidebar__item">
                <span class="sidebar__item--icon">
                    <i class="fa-regular fa-shop-lock"></i>
                </span>
                <span class="sidebar__item--text">{{ translate('Shop Settings') }}</span>

                <button class="sidebar__list--toggler">
                    <i class="fa-regular fa-chevron-down"></i>
                </button>
            </a>

            <ul class="">

                <li class="{{ areActiveRoutes(['seller.shop-sections.*']) }}">
                    <a href="{{ route('seller.shop-sections.index') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Home Sections') }}</span>
                    </a>
                </li>

                <li class="{{ areActiveRoutes(['seller.shops.profile']) }}">
                    <a href="{{ route('seller.shops.profile') }}" class="py-3 w-full transition-all">
                        <span class="sub-item--text">{{ translate('Configurations') }}</span>
                    </a>
                </li>
            </ul>
        </li>
    </ul>

    <div
        class="sticky bottom-0 bg-background px-[15px] pb-3 pt-5 border-t border-[#7878782b] grid grid-cols-2 lg:hidden items-center gap-4">
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

            <div
                class="option-dropdown__toggler option-dropdown__toggler--icon-small bg-background text-foreground border-border">
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
                                    <img src="{{ asset('images/flags/' . $language->flag . '.png') }}" alt=""
                                        class="w-[20px]" />
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
        <a href="#">
            <img src="{{ uploadedAsset(getSetting('poweredBy')) }}" alt="" />
        </a>
    </div>
</aside>
