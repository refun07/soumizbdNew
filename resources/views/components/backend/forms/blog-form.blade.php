<form action="{{ $blog ? route('admin.blogs.update', $blog->id) : route('admin.blogs.store') }}" method="POST">
    @csrf

    @if ($blog)
        @method('PUT')
        <input type="hidden" name="lang_key" value="{{ $langKey }}">
    @else
        @php
            $langKey = config('app.default_language');
        @endphp
    @endif

    <div class="space-y-3">

        <x-backend.inputs.text label="Title" name="title" placeholder="Type blog title"
            value="{{ $blog?->collectTranslation('title', $langKey) }}" />

        @if ($langKey == config('app.default_language'))
            @if ($blog) 
                <x-backend.inputs.text label="Slug" name="slug" placeholder="Type custom slug"
                    value="{{ $blog?->slug }}" />
            @endif

            <x-backend.inputs.select label="Category" name="blog_category_id" class="">
                @foreach ($blogCategories as $blogCategory)
                    <x-backend.inputs.select-option name="{{ $blogCategory->collectTranslation('name') }}"
                        value="{{ $blogCategory->id }}"
                        selected="{{ $blog?->blog_category_id == $blogCategory->id ? $blogCategory->id : 0 }}" />
                @endforeach
            </x-backend.inputs.select>

            <x-backend.inputs.file :multiple="false" label="Thumbnail Image" name="thumbnail_image"
                value="{{ $blog?->thumbnail_image }}" />

            <x-backend.inputs.file :multiple="false" label="Banner" name="banner" value="{{ $blog?->banner }}" />

        @endif

        <x-backend.inputs.textarea rich="true" label="Description" name="description"
            value="{{ $blog?->collectTranslation('description', $langKey) }}" placeholder="Type description" />


        @if (!$blog || ($blog && $langKey == config('app.default_language')))
            <x-backend.inputs.seo metaTitle="{{ $blog?->meta_title }}"
                metaDescription="{{ $blog?->meta_description }}" metaKeywords="{{ $blog?->meta_keywords }}"
                metaImage="{{ $blog?->meta_image }}" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Blog" type="submit" />
        </div>
    </div>
</form>
