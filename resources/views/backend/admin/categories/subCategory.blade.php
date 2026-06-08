@php
    $itemPrefix = null;
    for ($i = 0; $i < $subCategory->level; $i++) {
        $itemPrefix .= '>';
    }
    if (isset($couponCategories)) {
        $selected = $couponCategories->contains($subCategory->id) ? $subCategory->id : null;
    } elseif (isset($featuredCategories)) {
        $selected = $featuredCategories->contains($subCategory->id) ? $subCategory->id : null;
    } else {
        if ($category) {
            $selected = $subCategory->id == $category->parent_id ? $subCategory->id : null;
        } else {
            $selected = Request::get('categoryId') ?? null;
        }
    }
@endphp

@if ($subCategory)

    @if (isset($homeCategories))
        @if (!$homeCategories->contains($subCategory->id))
            <x-backend.inputs.select-option
                name="{{ $itemPrefix . ' ' . $subCategory->collectTranslation('name', $langKey) }}"
                value="{{ $subCategory->id }}" selected="{{ $selected }}" />
        @endif
    @else
        <x-backend.inputs.select-option
            name="{{ $itemPrefix . ' ' . $subCategory->collectTranslation('name', $langKey) }}"
            value="{{ $subCategory->id }}" selected="{{ $selected }}" />
    @endif

    @foreach ($subCategory->childrenCategories()->orderBy('sorting_order_level', 'desc')->get() as $childCategory)
        @php
            $data = [
                'category' => isset($category) ? $category : null,
                'subCategory' => $childCategory,
                'langKey' => $langKey,
            ];

            if (isset($couponCategories)) {
                $data['couponCategories'] = $couponCategories;
            }
            if (isset($featuredCategories)) {
                $data['featuredCategories'] = $featuredCategories;
            }
            if (isset($homeCategories)) {
                $data['homeCategories'] = $homeCategories;
            }
        @endphp

        @include('backend.admin.categories.subCategory', $data)
    @endforeach
@endif
