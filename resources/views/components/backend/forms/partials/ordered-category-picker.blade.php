@php
    $selectedCategories = $selectedCategories ?? collect();
    $langKey = $langKey ?? config('app.default_language');
    $label = $label ?? 'Featured Categories';
    $categoryOptions = collect();
    $selectedCategories = collect($selectedCategories)->map(fn ($id) => (int) $id);

    $appendCategory = function ($category, $prefix = '') use (&$appendCategory, &$categoryOptions, $langKey) {
        $label = trim($prefix . ' ' . $category->collectTranslation('name', $langKey));

        $categoryOptions->push([
            'id' => (int) $category->id,
            'label' => $label,
        ]);

        foreach ($category->childrenCategories as $childCategory) {
            $appendCategory($childCategory, trim($prefix . ' >'));
        }
    };

    foreach ($categories as $category) {
        $appendCategory($category);
    }

    $categoriesById = $categoryOptions->keyBy('id');
@endphp

<div class="ordered-product-picker ordered-category-picker" data-input-name="{{ $inputName }}">
    <x-backend.inputs.select :labelInline="false" label="{{ $label }}"
        name="{{ $inputName }}Picker" class="ordered-product-picker-select"
        data-selection-css-class="input-select2"
        data-placeholder="{{ translate('Select categories') }}" data-no-add-new="true"
        :isRequired="false">
        <option></option>
        @foreach ($categoryOptions as $categoryOption)
            <x-backend.inputs.select-option
                name="{{ $categoryOption['label'] }}"
                value="{{ $categoryOption['id'] }}" />
        @endforeach
    </x-backend.inputs.select>

    <input type="hidden" name="{{ $inputName }}" value="[]"
        class="ordered-product-picker-empty-input" disabled>

    <div class="ordered-product-picker-list mt-3 space-y-2">
        @foreach ($selectedCategories as $categoryId)
            @php
                $categoryOption = $categoriesById->get((int) $categoryId);
            @endphp

            @if ($categoryOption)
                <div class="ordered-product-picker-item flex items-center gap-2 rounded border border-border bg-background p-2"
                    draggable="true" data-value="{{ $categoryOption['id'] }}">
                    <button type="button"
                        class="ordered-product-picker-drag-handle h-8 w-8 shrink-0 cursor-move rounded border border-border text-muted-foreground"
                        title="{{ translate('Drag to reorder') }}">
                        <i class="fa-solid fa-grip-vertical"></i>
                    </button>
                    <input type="hidden" name="{{ $inputName }}[]" value="{{ $categoryOption['id'] }}">
                    <span class="min-w-0 flex-1 truncate text-sm">
                        {{ $categoryOption['label'] }}
                    </span>
                    <button type="button"
                        class="ordered-product-picker-move-up h-8 w-8 shrink-0 rounded border border-border"
                        title="{{ translate('Move up') }}">
                        <i class="fa-solid fa-chevron-up"></i>
                    </button>
                    <button type="button"
                        class="ordered-product-picker-move-down h-8 w-8 shrink-0 rounded border border-border"
                        title="{{ translate('Move down') }}">
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <button type="button"
                        class="ordered-product-picker-remove h-8 w-8 shrink-0 rounded border border-border text-red-500"
                        title="{{ translate('Remove') }}">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            @endif
        @endforeach
    </div>
</div>
