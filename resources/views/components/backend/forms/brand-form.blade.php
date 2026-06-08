<form action="{{ $brand ? route('admin.brands.update', $brand->id) : route('admin.brands.store') }}" method="POST">
    @csrf

    @if ($brand)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type brand name"
            value="{{ $brand?->collectTranslation('name', $langKey) }}" />

        <x-backend.inputs.file label="Thumbnail Image" name="thumbnail_image" value="{{ $brand?->thumbnail_image }}"
            filesHint="This image will be used as thumbnail of the brand. Recommended size: 68*68" />

        <x-backend.inputs.text label="Meta Title" name="meta_title" placeholder="Type meta title"
            value="{{ $brand?->collectTranslation('meta_title', $langKey) }}" :isRequired="false" aiGenerate="true" />

        <x-backend.inputs.textarea label="Meta Description" name="meta_description" placeholder="Type meta description"
            value="{{ $brand?->collectTranslation('meta_description', $langKey) }}" :isRequired="false"
            aiGenerate="true" />

        <x-backend.inputs.textarea label="Meta Keywords" name="meta_keywords"
            placeholder="Type comma separated keywords. e.g. keyword1, keyword2" value="{!! $brand?->collectTranslation('meta_keywords', $langKey) !!}"
            :isRequired="false" aiGenerate="true" />

        <x-backend.inputs.file label="Meta Image" name="meta_image" value="{{ $brand?->meta_image }}"
            filesHint="This image will be used as meta image of the brand. Recommended size: 512*512" />

        @if ($brand && $langKey == config('app.default_language'))
            <x-backend.inputs.text label="Slug" name="slug" placeholder="Type custom slug"
                value="{{ $brand->slug }}" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Brand" type="submit" />
        </div>
    </div>
</form>
