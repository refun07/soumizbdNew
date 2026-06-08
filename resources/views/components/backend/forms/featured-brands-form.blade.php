@php
    $brands = \App\Models\Brand::isActive()->get();
@endphp

{{-- brands to follow --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Brands To Follow') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                @php
                    $featuredBrands =
                        getSetting('homeFeaturedBrands') != null
                            ? collect(json_decode(getSetting('homeFeaturedBrands')))
                            : collect();
                @endphp
                <div class="pt-2">


                    <input type="hidden" name="types[]" value="showHomeFeaturedBrands">
                    <div class="flex items-center justify-between mb-5">
                        <p>{{ translate('Show/Hide Featured Brands') }}</p>
                        <x-backend.inputs.checkbox toggler="true" name="showHomeFeaturedBrands" value="1"
                            isChecked="{{ getSetting('showHomeFeaturedBrands') ?? false }}" />
                    </div>
                    <input type="hidden" name="types[]" value="homeFeaturedBrands">
                    <x-backend.inputs.select :labelInline="false" label="Featured Brands" name="homeFeaturedBrands[]"
                        multiple data-selection-css-class="multi-select2"
                        data-placeholder="{{ translate('Select brands') }}" :isRequired="false">

                        @foreach ($brands as $brand)
                            <x-backend.inputs.select-option name="{{ $brand->collectTranslation('name', $langKey) }}"
                                value="{{ $brand->id }}"
                                selected="{{ $featuredBrands->contains($brand->id) ? $brand->id : 0 }}" />
                        @endforeach
                    </x-backend.inputs.select>

                    <div class="flex justify-end pt-2">
                        <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                            class="submit-btn mt-3" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
