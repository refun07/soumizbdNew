<form
    action="{{ $campaign ? route(routePrefix() . '.campaigns.update', $campaign->id) : route(routePrefix() . '.campaigns.store') }}"
    method="POST" id="product-form">
    @csrf
    @if ($campaign)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="grid md:grid-cols-12 gap-3">
        <div class="md:col-span-6 xl:col-span-8 md:row-span-2">
            {{-- code --}}
            <div class="card">
                <div class="card__title flex justify-between items-center">
                    {{ translate('General Informations') }}
                </div>

                <div class="card__content">
                    <div class="space-y-3">

                        @if (user()->user_type != 'seller')
                            <x-backend.inputs.select label="Type" name="type" data-search="false"
                                groupClass="hidden">
                                <x-backend.inputs.select-option name="{{ translate('Private') }}" value="private"
                                    selected="{{ $campaign?->type }}" />

                                <x-backend.inputs.select-option name="{{ translate('Mega') }}" value="mega"
                                    selected="{{ $campaign?->type }}" />
                            </x-backend.inputs.select>
                        @endif

                        <x-backend.inputs.text label="Name" name="name" placeholder="Write the campaign name"
                            value="{{ $campaign?->name }}" />


                        @if ($campaign && $langKey == config('app.default_language'))
                            <x-backend.inputs.text label="Slug" name="slug" placeholder="Type custom slug"
                                value="{{ $campaign->slug }}" />
                        @endif

                        <x-backend.inputs.textarea name="short_description" label="About Campaign"
                            value="{{ $campaign?->short_description }}" placeholder='Write a short description...' />


                        @php
                            $start_date = date('m/d/Y');
                            $end_date = date('m/d/Y');

                            if ($campaign) {
                                $start_date = $campaign->start_date
                                    ? date('m/d/Y', $campaign->start_date)
                                    : date('m/d/Y');
                                $end_date = $campaign->end_date ? date('m/d/Y', $campaign->end_date) : date('m/d/Y');
                            }
                        @endphp

                        <x-backend.inputs.datepicker label="Discount Date" placeholder="Start date - End date"
                            name="date_range" type="range" value="{{ $start_date . ' - ' . $end_date }}" />

                        <x-backend.inputs.number label="Default Discount" name="default_discount_value" placeholder=""
                            value="{{ $campaign?->default_discount_value ?? 0 }}" min="0" step="0.001" />


                        <x-backend.inputs.select label="Default Discount Type" name="default_discount_type"
                            data-search="false">
                            <x-backend.inputs.select-option name="{{ translate('Flat') }}" value="flat"
                                selected="{{ $campaign?->default_discount_type }}" />

                            <x-backend.inputs.select-option name="{{ translate('Percentage') }}" value="percentage"
                                selected="{{ $campaign?->default_discount_type }}" />
                        </x-backend.inputs.select>

                        <x-backend.inputs.file label="Thumbnail Image" name="thumbnail_image"
                            value="{{ $campaign?->thumbnail_image }}" class="grow"
                            placeholder="Select File (jpg, png, gif, webp)" />

                        <x-backend.inputs.file label="Banner" name="banner" value="{{ $campaign?->banner }}"
                            class="grow" placeholder="Select File (jpg, png, gif, webp)" />

                        {{-- submit button --}}
                        <div class="flex justify-end mt-6">
                            <x-backend.inputs.button buttonText="Save Campaign" type="submit" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($campaign)
            <div class="md:col-span-6 xl:col-span-4 card">
                <h4 class="card__title">{{ translate('Campaign Summary') }}</h4>

                <div class="card__content space-y-7">
                    <div>
                        <p class="text-muted text-xs mb-2.5">{{ translate('Campaign Name') }}</p>
                        <p>{{ $campaign->name }}</p>
                    </div>

                    <div class="flex justify-between">
                        <div>
                            <p class="text-muted text-xs mb-2.5">{{ translate('Total Products') }}</p>
                            <p>{{ $campaign->campaignProducts()->count() }} {{ translate('Products') }}</p>
                        </div>
                        <div>
                            <p class="text-muted text-xs mb-2.5">{{ translate('Max Discount') }}</p>
                            <p>{{ $campaign->campaignProducts()->max('discount_value') }} {{ translate('Discount') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <h6 class="text-muted text-xs mb-2.5">{{ translate('Offer Counter') }}</h6>

                        <div class="count-down-time" data-ends-at="{{ $end_date }}"></div>
                    </div>

                    <div class="border-y border-border py-6 flex justify-between">
                        <span>{{ translate('Publish Campaign') }}</span>
                        <x-backend.inputs.checkbox toggler="true"
                            data-route="{{ route(routePrefix() . '.campaigns.status') }}" name="isActiveCheckbox"
                            value="{{ $campaign->id }}" data-status="{{ $campaign->is_published }}"
                            isChecked="{{ (int) $campaign->is_published == 1 }}" />
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <x-backend.inputs.link target="_blank" href="{{ url('/campaigns') . '/' . $campaign->slug }}"
                            variant="secondary" buttonText="">
                            {{ translate('VIEW CAMPAIGN') }}
                        </x-backend.inputs.link>

                        <a href="javascript:void(0);"
                            data-href="{{ route(routePrefix() . '.campaigns.destroy', $campaign->id) }}"
                            data-title="{{ translate('Are you sure want to delete this item?') }}"
                            data-text="{{ translate('All data related to this may get deleted.') }}"
                            data-method="DELETE" data-micromodal-trigger="confirm-modal"
                            class="text-rose-400 font-medium confirm-modal">
                            {{ translate('DELETE CAMPAIGN') }}
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</form>
