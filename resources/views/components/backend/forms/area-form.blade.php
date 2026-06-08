<form action="{{ $area ? route('admin.areas.update', $area->id) : route('admin.areas.store') }}" method="POST">
    @csrf

    @if ($area)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type area name" value="{{ $area?->name }}" />

        <x-backend.inputs.select label="City" name="city_id" class="">
            @foreach ($cities as $city)
                <x-backend.inputs.select-option name="{{ $city->name }}" value="{{ $city->id }}"
                    selected="{{ $area?->city_id == $city->id ? $city->id : 0 }}" />
            @endforeach
        </x-backend.inputs.select>

        <x-backend.inputs.select label="Zone" name="zone_id" class="" :isRequired="false">

            <x-backend.inputs.select-option name="{{ translate('Select zone') }}" value="" selected="" />
            @foreach ($zones as $zone)
                <x-backend.inputs.select-option name="{{ $zone->name }}" value="{{ $zone->id }}"
                    selected="{{ $area?->zone_id == $zone->id ? $zone->id : 0 }}" />
            @endforeach
        </x-backend.inputs.select>

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Area" type="submit" />
        </div>
    </div>
</form>
