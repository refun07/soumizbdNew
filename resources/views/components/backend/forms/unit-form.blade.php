<form action="{{ $unit ? route('admin.units.update', $unit->id) : route('admin.units.store') }}" method="POST">
    @csrf

    @if ($unit)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type unit name"
            value="{{ $unit?->collectTranslation('name', $langKey) }}" />

        @if ($unit && $langKey == config('app.default_language'))
            <x-backend.inputs.text label="Slug" name="slug" placeholder="Type custom slug"
                value="{{ $unit->slug }}" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Unit" type="submit" />
        </div>
    </div>
</form>
