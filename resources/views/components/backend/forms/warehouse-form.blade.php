<form
    action="{{ $warehouse ? route(routePrefix() . '.warehouses.update', $warehouse->id) : route(routePrefix() . '.warehouses.store') }}"
    method="POST">
    @csrf

    @if ($warehouse)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text name="name" label="Name" placeholder="Type warehouse name"
            value="{{ $warehouse?->name }}" />
        <x-backend.inputs.textarea name="address" label="Address" placeholder="Type address"
            value="{{ $warehouse?->address }}" />

        <x-backend.inputs.textarea name="description" label="Description" placeholder="Type description"
            value="{{ $warehouse?->description }}" :isRequired="false" />


        <x-backend.inputs.file label="Thumbnail Image" name="thumbnail_image" value="{{ $warehouse?->thumbnail_image }}"
            filesHint="This image will be used as thumbnail of the warehouse. Recommended size: 300*300"
            class="hidden" />

        <x-backend.inputs.select label="Zones" name="zone_ids[]" multiple data-selection-css-class="multi-select2"
            data-placeholder="{{ translate('Select zones') }}" :isRequired="false">
            @foreach ($zones as $zone)
                <x-backend.inputs.select-option name="{{ $zone->name }}" value="{{ $zone->id }}"
                    selected="{{ in_array($zone->id, $warehouseZoneIds) ? $zone->id : 0 }}" />
            @endforeach
        </x-backend.inputs.select>

        <div class="flex justify-end pt-2">
            <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Warehouse') }}" />
        </div>
    </div>
</form>
