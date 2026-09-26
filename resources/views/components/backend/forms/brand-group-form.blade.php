<form action="{{ $brandGroup ? route('admin.brand-groups.update', $brandGroup->id) : route('admin.brand-groups.store') }}"
    method="POST">
    @csrf

    @if ($brandGroup)
        @method('PUT')
    @endif

    <div class="space-y-3">
        <x-backend.inputs.text label="Name" name="name" placeholder="Type group name"
            value="{{ old('name', $brandGroup?->name) }}" />

        <x-backend.inputs.file label="Image" name="image" value="{{ $brandGroup?->image }}" />

        <x-backend.inputs.number label="Sort Order" name="sort_order" placeholder="0"
            value="{{ old('sort_order', $brandGroup?->sort_order ?? 0) }}" :isRequired="false" />

        @if ($brandGroup)
            <x-backend.inputs.text label="Slug" name="slug" placeholder="Type custom slug"
                value="{{ $brandGroup->slug }}" :isRequired="false" />
        @endif

        <div class="flex justify-end">
            <x-backend.inputs.button buttonText="Save Group" type="submit" />
        </div>
    </div>
</form>
