<form action="{{ $zone ? route('admin.zones.update', $zone->id) : route('admin.zones.store') }}" method="POST">
    @csrf

    @if ($zone)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type zone name" value="{{ $zone?->name }}" />

        <x-backend.inputs.file label="Banner Image" name="banner" value="{{ $zone?->banner }}" class="hidden" />

        @if ($zone && $langKey == config('app.default_language'))
            <x-backend.inputs.text label="Slug" name="slug" placeholder="Type custom slug"
                value="{{ $zone->slug }}" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Zone" type="submit" />
        </div>
    </div>
</form>
