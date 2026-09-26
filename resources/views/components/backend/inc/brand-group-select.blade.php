@php
    $brandGroups = \App\Models\BrandGroup::ordered()->get(['id', 'name']);
    $selectedBrandGroup = $selectedBrandGroup ?? null;
@endphp

<div class="input-select2 theme-input-group">
    <label class="theme-input-label">{{ translate('Brand Group') }}</label>

    <div class="theme-input-wrapper">
        <select name="brand_group_id" class="brand-group-select theme-input"
            data-placeholder="{{ translate('Select or type to create a group') }}">
            <option value="">{{ translate('No Group') }}</option>
            @foreach ($brandGroups as $brandGroup)
                <option value="{{ $brandGroup->id }}" @selected($selectedBrandGroup == $brandGroup->id)>
                    {{ $brandGroup->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
