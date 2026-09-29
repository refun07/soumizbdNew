<form action="{{ $collection ? route('admin.collections.update', $collection->id) : route('admin.collections.store') }}"
    method="POST">
    @csrf

    @if ($collection)
        @method('PUT')
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type collection name"
            value="{{ old('name', $collection?->name) }}" />

        <x-backend.inputs.file label="Image" name="image" value="{{ $collection?->image }}" />

        <x-backend.inputs.number label="Sort Order" name="sort_order" placeholder="0"
            value="{{ old('sort_order', $collection?->sort_order ?? 0) }}" :isRequired="false" />

        @if ($collection)
            <x-backend.inputs.text label="Slug" name="slug" placeholder="Type custom slug"
                value="{{ $collection->slug }}" :isRequired="false" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Collection" type="submit" />
        </div>
    </div>
</form>
