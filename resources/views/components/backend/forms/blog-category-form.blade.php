<form
    action="{{ $blogCategory ? route('admin.blogCategories.update', $blogCategory->id) : route('admin.blogCategories.store') }}"
    method="POST">
    @csrf

    @if ($blogCategory)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">

        <x-backend.inputs.text label="Name" name="name" placeholder="Type category name"
            value="{{ $blogCategory?->collectTranslation('name') }}" />

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Category" type="submit" />
        </div>
    </div>
</form>
