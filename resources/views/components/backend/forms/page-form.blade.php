<form action="{{ $page ? route('admin.pages.update', $page->id) : route('admin.pages.store') }}" method="POST">
    @csrf

    @if ($page)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Title" name="title" placeholder="Type page title"
            value="{{ $page?->collectTranslation('title', $langKey) }}" />

        @if ($langKey == config('app.default_language'))
            @if ($page)
                <div class="theme-input-group w-full">
                    <label for="slug" class="theme-input-label pt-0 input-required">
                        {{ translate('Slug') }}
                    </label>
                    <div class="theme-input-wrapper flex">
                        <div class="border rounded-md !rounded-e-none !border-e-0 p-2 text-sm">{{ url('/pages') . '/' }}
                        </div>
                        <input type="text" id="slug" name="slug" class="theme-input !rounded-s-none"
                            placeholder="{{ translate('Type page slug') }}" value="{{ $page?->slug }}" required
                            @disabled($page->type != 'custom') />
                    </div>
                </div>
            @else
                <div class="theme-input-group w-full">
                    <label for="slug" class="theme-input-label pt-0 input-required">
                        {{ translate('Slug') }}
                    </label>
                    <div class="theme-input-wrapper flex">
                        <div class="border rounded-md !rounded-e-none !border-e-0 p-2 text-sm">{{ url('/pages') . '/' }}
                        </div>
                        <input type="text" id="slug" name="slug" class="theme-input !rounded-s-none"
                            placeholder="{{ translate('Type page slug') }}" value="{{ $page?->slug }}" required />
                    </div>
                </div>
            @endif
        @endif

        <x-backend.inputs.textarea :rich="true" label="Content" name="content" placeholder="Type content"
            value="{{ $page?->collectTranslation('content', $langKey) }}" aiGenerate="true" />

        @if ($langKey == config('app.default_language'))
            <x-backend.inputs.text label="Meta Title" name="meta_title" placeholder="Type meta title"
                value="{{ $page?->collectTranslation('meta_title', $langKey) }}" :isRequired="false" aiGenerate="true" />

            <x-backend.inputs.textarea label="Meta Description" name="meta_description"
                placeholder="Type meta description"
                value="{{ $page?->collectTranslation('meta_description', $langKey) }}" :isRequired="false"
                aiGenerate="true" />

            <x-backend.inputs.textarea label="Meta Keywords" name="meta_keywords"
                placeholder="Type comma separated keywords. e.g. keyword1, keyword2" value="{!! $page?->meta_keywords !!}"
                :isRequired="false" aiGenerate="true" />

            <x-backend.inputs.file label="Meta Image" name="meta_image" value="{{ $page?->meta_image }}"
                filesHint="This image will be used as meta image of the brand. Recommended size: 512*512" />
        @endif


        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Page" type="submit" />
        </div>
    </div>
</form>
