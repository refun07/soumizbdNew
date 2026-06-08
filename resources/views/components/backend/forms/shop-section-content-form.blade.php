<form action="{{ route(routePrefix() . '.shop-sections.update', $shopSection->id) }}" method="POST">
    @csrf
    @method('PUT')
    <div class="space-y-3">
        <x-backend.inputs.select label="Type" name="type" data-search="false" class="section-type" :isDisabled="true">
            <x-backend.inputs.select-option name="{{ ucfirst(str_replace('-', ' ', $shopSection->type)) }}"
                value="{{ $shopSection->type }}" />
        </x-backend.inputs.select>
        @if ($shopSection->type == 'products')
            <x-backend.inputs.text name="title" label="Title" placeholder="Type section title" class="section-title"
                value="{{ $shopSection->title }}" />
        @endif

        <x-backend.inputs.number name="order" label="Order" min="0" value="{{ $shopSection->order }}" />


        @if ($shopSection->type == 'products')
            @php
                $selectedProducts = $shopSection->section_values ? collect(json_decode($shopSection->section_values)->products) : collect();

            @endphp
            <x-backend.inputs.select data-placeholder="{{ translate('Select products') }}" label="Products"
                name="productIds[]" data-selection-css-class="multi-select2" multiple>
                @foreach ($products as $product)
                    @php
                        $selected = $selectedProducts->contains($product->id) ? $product->id : null;
                    @endphp

                    <x-backend.inputs.select-option name="{{ $product->collectTranslation('name') }}"
                        value="{{ $product->id }}" selected="{{ $selected }}" />
                @endforeach
            </x-backend.inputs.select>
        @endif

        @php
            $sectionValues = $shopSection->section_values ? json_decode($shopSection->section_values) : null;
        @endphp

        @if ($shopSection->type == 'full-width-banner')
            <x-backend.inputs.text name="link" label="Link" value="{{ $sectionValues?->link }}"
                :isRequired="false" />
            <x-backend.inputs.file :multiple="true" label="Banners" name="banners"
                value="{{ $sectionValues?->banners }}" />
        @endif

        @if ($shopSection->type == 'boxed-width-banner')
            <x-backend.inputs.text name="box_1_link" label="Box-1-Link" value="{{ $sectionValues?->box_1_link }}"
                :isRequired="false" />
            <x-backend.inputs.file :multiple="true" label="Box-1-Banners" name="box_1_banners"
                value="{{ $sectionValues?->box_1_banners }}" />

            <x-backend.inputs.text name="box_2_link" label="Box-2-Link" value="{{ $sectionValues?->box_2_link }}"
                :isRequired="false" />
            <x-backend.inputs.file :multiple="true" label="Box-2-Banners" name="box_2_banners"
                value="{{ $sectionValues?->box_2_banners }}" />
        @endif


        <div class="flex justify-end pt-2">
            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Section') }}" />
        </div>
    </div>
</form>
