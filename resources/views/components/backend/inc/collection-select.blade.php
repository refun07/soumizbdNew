@php
    $collections = \App\Models\ProductCollection::ordered()->get(['id', 'name']);
    $selectedCollections = $selectedCollections ?? [];
@endphp

<div class="input-select2 theme-input-group">
    <label class="theme-input-label">{{ translate('Collections') }}</label>

    <div class="theme-input-wrapper">
        <select name="collection_ids[]" class="collection-select theme-input" multiple
            data-placeholder="{{ translate('Select or type to create collections') }}">
            @foreach ($collections as $collection)
                <option value="{{ $collection->id }}" @selected(in_array($collection->id, $selectedCollections))>
                    {{ $collection->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
