<form
    action="{{ $variationValue ? route('admin.variation-values.update', ['id' => $variationValue->id]) : route('admin.variation-values.store', ['variation_id' => $variation->id]) }}"
    method="POST"
    enctype="multipart/form-data">
    @csrf

    @if ($variationValue)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type value name"
            value="{{ $variationValue?->collectTranslation('name', $langKey) }}" />

        @if ($variation->is_color)
            <x-backend.inputs.color label="Color Code" name="color_code" value="{{ $variationValue?->color_code }}"
                :isRequired="false" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Value" type="submit" />
        </div>
    </div>
</form>
