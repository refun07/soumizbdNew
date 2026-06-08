@php
    if (isset($productCategories)) {
        $selected = $productCategories->contains($subCategory->id) ? $subCategory->id : null;
    } else {
        $selected = null;
    }
@endphp

<li class="category {{ count($subCategory->childrenCategories) > 0 ? 'has-subcategory' : '' }}">
    <label class="flex gap-3 items-center">
        <x-backend.inputs.checkbox class="category-selector" name="category_ids[]" value="{{ $subCategory->id }}"
            isChecked="{{ $productCategories->contains($subCategory->id) }}" />
        {{ $subCategory->collectTranslation('name', $langKey) }}
    </label>


    @if (count($subCategory->childrenCategories) > 0)
        <button type="button" class="category-toggler">
            <i class="fa-solid fa-chevron-down"></i>
        </button>

        <ul class="pl-5 mt-4 space-y-4">
            @foreach ($subCategory->childrenCategories()->orderBy('sorting_order_level', 'desc')->where('id', '!=', $category->id)->get() as $childCategory)
                @php
                    $data = [
                        'subCategory' => $childCategory,
                        'langKey' => $langKey,
                    ];

                    if (isset($productCategories)) {
                        $data['productCategories'] = $productCategories;
                    }
                @endphp

                @include('backend.admin.products.subCategory', $data)
            @endforeach
        </ul>
    @endif
</li>
