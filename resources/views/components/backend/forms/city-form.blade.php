<form action="{{ $city ? route('admin.cities.update', $city->id) : route('admin.cities.store') }}" method="POST">
    @csrf

    @if ($city)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type city name" value="{{ $city?->name }}" />

        <x-backend.inputs.select label="District" name="state_id" class="">
            @foreach ($states as $state)
                <x-backend.inputs.select-option name="{{ $state->name }}" value="{{ $state->id }}"
                    selected="{{ $city?->state_id == $state->id ? $state->id : 0 }}" />
            @endforeach
        </x-backend.inputs.select>

        <x-backend.inputs.select label="Zone" name="zone_id" class="">
            @foreach ($zones as $zone)
                <x-backend.inputs.select-option name="{{ $zone->name }}" value="{{ $zone->id }}"
                    selected="{{ $city?->zone_id == $zone->id ? $zone->id : 0 }}" />
            @endforeach
        </x-backend.inputs.select>

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save City" type="submit" />
        </div>
    </div>
</form>
