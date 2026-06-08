<form action="{{ $state ? route('admin.states.update', $state->id) : route('admin.states.store') }}" method="POST">
    @csrf

    @if ($state)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type state name" value="{{ $state?->name }}" />

        <x-backend.inputs.select label="Country" name="country_id" class="">
            @foreach ($countries as $country)
                <x-backend.inputs.select-option name="{{ $country->name }}" value="{{ $country->id }}"
                    selected="{{ $state?->country_id == $country->id ? $country->id : 0 }}" />
            @endforeach
        </x-backend.inputs.select>

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save State" type="submit" />
        </div>
    </div>
</form>
