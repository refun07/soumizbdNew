<form action="{{ $variation ? route('admin.variations.update', $variation->id) : route('admin.variations.store') }}"
    method="POST">
    @csrf

    @if ($variation)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type variation name"
            value="{{ $variation?->collectTranslation('name', $langKey) }}" />

        <input type="hidden" name="is_color" value="0">
        <x-backend.inputs.checkbox label="Is Color Attribute" name="is_color"
            isChecked="{{ $variation?->is_color ?? false }}" value="1" />

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Variation" type="submit" />
        </div>
    </div>
</form>
