<nav class="h-[75px] 2xl:h-[103px] flex items-center">
    <div class="px-[15px] lg:px-[50px] flex-grow flex items-center justify-between gap-10">
        <div class="flex items-center grow gap-14">
            <div class="flex items-center gap-7">
                <button
                    class="sidebar-toggler text-lg sm:text-2xl w-10 h-10 flex items-center justify-center rounded-md text-secondary">
                    <i class="fa-regular fa-bars"></i>
                </button>
                <a href="{{ route('seller.dashboard') }}">
                    <img src="{{ uploadedAsset(getSetting('sellerDashboardLogo')) }}" class="max-h-[60px]" alt="Epik Cart"
                        onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                </a>
            </div>

            <div class="flex items-center grow gap-5">
                <a href="{{ url('/shops\/') . shop()->slug }}" target="_blank"
                    class="whitespace-nowrap h-10 max-lg:hidden inline-flex items-center gap-2 bg-primary text-white rounded-md px-4 font-bold">
                    <span>
                        <i class="fa-light fa-globe-pointer"></i>
                    </span>
                    <span>{{ translate('Browse') }}</span>
                </a>

                <!-- start::search box and suggestions -->
                <div class="group relative max-xl:hidden w-full max-lg:max-w-[250px] max-w-[420px]" tabindex="0">
                    <x-backend.inputs.search-input class="sm:!max-w-none navbar-search-input" name="search"
                        placeholder="Search" value="{{ Request::get('search') }}" />

                    <div
                        class="absolute z-[1] bg-popover text-popover-foreground rounded-md right-0 top-[calc(100%+38px)] w-full min-w-[300px] shadow-theme max-h-[440px] overflow-y-auto p-[2px] divide-y divide-border opacity-0 invisible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-300 navbar-search">
                        @include('components.backend.inc.navbar-search', [
                            'products' => collect(),
                            'orders' => collect(),
                        ])
                    </div>
                </div>
                <!-- start::search box and suggestions -->
            </div>
        </div>

        <div class="flex items-center flex-shrink-0 gap-4">
            <div class="option-dropdown max-xl:hidden" tabindex="0">
                <div class="option-dropdown__toggler">
                    <span class="text-xl">
                        <i class="fal fa-plus"></i>
                    </span>
                    <span>{{ translate('ADD NEW') }}</span>
                </div>

                <div class="option-dropdown__options">
                    <ul>
                        <li>
                            <a href="{{ route('seller.products.create') }}"
                                class="option-dropdown__option">{{ translate('Product') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('seller.purchase-orders.create') }}"
                                class="option-dropdown__option">{{ translate('Purchase Order') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('seller.stockAdjustments.create') }}"
                                class="option-dropdown__option">{{ translate('Stock Adjustment') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('seller.stockTransfers.create') }}"
                                class="option-dropdown__option">{{ translate('Stock Transfer') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('seller.suppliers.create') }}"
                                class="option-dropdown__option">{{ translate('Supplier') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('seller.coupons.create') }}"
                                class="option-dropdown__option">{{ translate('Coupon') }}</a>
                        </li>
                        <li>
                            <a href="{{ route('seller.campaigns.create') }}"
                                class="option-dropdown__option">{{ translate('Campaign') }}</a>
                        </li>
                    </ul>
                </div>
            </div>

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

            <div class="option-dropdown max-xl:hidden" tabindex="0">
                <div class="option-dropdown__toggler bg-background text-muted">
                    <span>
                        <img src="{{ asset('images/flags/' . $currentLanguage->flag . '.png') }}" alt=""
                            class="w-[20px]" />
                    </span>
                    <span>{{ $currentLanguage->name }}</span>
                </div>

                <div class="option-dropdown__options">
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

            <div class="button button--light h-12 w-12 text-foreground text-xl justify-center toggle-theme-btn">
                <i class="fa-light fa-sun-bright"></i>
            </div>

            <div class="group relative max-md:hidden" tabindex="0">
                <button class="text-primary text-xl relative w-12 h-12 flex items-center justify-center">
                    <i class="fa-regular fa-bell"></i>
                    <span
                        class="absolute top-6 left-7 w-[17px] h-[17px] flex items-center justify-center bg-red-500 text-white text-[9px] rounded-full">
                        {{ \App\Models\Notification::where('for', 'shop')->where('shop_id', shopId())->where('is_read', 0)->count() }}</span>
                </button>

                <!-- notifications -->
                <div
                    class="absolute z-[1] bg-background rounded-md right-0 top-[calc(100%+35px)] min-w-[300px] shadow-theme divide-y divide-[#78787829] max-h-[440px] overflow-y-auto opacity-0 invisible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-300">
                    <div class="flex items-center justify-between px-7 py-[30px]">
                        <span class="font-bold">{{ translate('Notifications') }}</span>
                        <a href="{{ route('seller.notifications.markRead') }}"
                            class="text-secondary">{{ translate('Mark all as read') }}</a>
                    </div>

                    @php
                        $notifications = \App\Models\Notification::where('for', 'shop')
                            ->where('shop_id', shopId())
                            ->latest()
                            ->take(4)
                            ->get();
                    @endphp
                    <ul class="divide-y divide-[#78787829]">
                        @if (count($notifications) > 0)
                            @foreach ($notifications as $notification)
                                <li>
                                    <a href="{{ getNotificationLink($notification, 'seller') }}"
                                        class="text-[13px] flex items-center px-[25px] py-[15px] gap-[14px] {{ $notification->is_read ? '' : 'bg-orange-50/50' }}">
                                        <div>
                                            <span
                                                class="w-[27px] aspect-square {{ getNotificationIcon($notification, 'class') }} inline-flex items-center justify-center rounded-full">
                                                <i class="{{ getNotificationIcon($notification) }}"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <h5 class="truncate">
                                                {{ getNotificationText($notification) }}
                                            </h5>
                                            <div class="flex items-center text-muted">
                                                <span>{{ $notification->created_at->diffForHumans() }}</span>
                                                <span
                                                    class="ml-[9px] mr-[7px] h-[5px] w-[5px] rounded-full bg-secondary"></span>
                                                <span class="capitalize">{{ $notification->type }}</span>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                {{ translate('No Notifications') }}
                            </div>
                        @endif
                    </ul>

                    <div class="px-[22px] py-[18px] text-right">
                        <a href="{{ route('seller.notifications') }}"
                            class="text-secondary inline-block">{{ translate('View All') }}</a>
                    </div>
                </div>
            </div>

            <div class="group relative" tabindex="0">
                <div class="flex items-center gap-2 cursor-pointer">
                    @php
                        $user = user();
                    @endphp
                    <div class="text-right leading-[1.3] hidden xl:block">
                        <h4 class="text-foreground font-bold">
                            {{ $user->name }}
                        </h4>
                        <p class="text-[13px] text-muted">
                            {{ $user->user_type }}
                        </p>
                    </div>
                    <div>
                        <img src="{{ uploadedAsset($user->avatar) }}" alt="" class=" w-10 h-10 rounded-full"
                            onerror="this.onerror=null;this.src='{{ asset('images/image-error.png') }}';" />
                    </div>
                </div>

                <ul
                    class="absolute z-[1] bg-background rounded-md right-0 top-[calc(100%+38px)] min-w-[150px] shadow-theme overflow-y-auto p-[2px] divide-y divide-[#78787829] opacity-0 invisible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-300">
                    <li>
                        <a href="{{ route('profile.edit') }}"
                            class="group/item px-5 py-1 flex gap-3 items-center text-xs font-medium hover:bg-secondary/10 transition-all rounded-md">
                            <span class="text-base text-secondary">
                                <i class="fa-regular fa-pen-to-square"></i>
                            </span>
                            <span class="group-hover/item:text-secondary">{{ translate('Profile') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('logout') }}"
                            class="group/item px-5 py-1 flex gap-3 items-center text-xs font-medium hover:bg-red-500/5 transition-all rounded-md">
                            <span class="text-base text-red-500">
                                <i class="fa-regular fa-power-off"></i>
                            </span>
                            <span class="group-hover/item:text-red-500">{{ translate('Log Out') }}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
