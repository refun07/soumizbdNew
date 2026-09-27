<div class="modal micromodal-slide" id="brand-create-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="brand-create-modal-title">

        <input id="purchase-order-payment-modal-method" type="hidden" name="_method" value="">
        <header class="modal__header">
            <h2 class="modal__title">
                {{ translate('Add New Brand') }}
            </h2>
            <button type="button" class="modal__close ms-2" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content min-h-[180px] w-full">
            <div id="brand-create">
                <form action="" method="POST" class="brand-form-modal">
                    @csrf
                    @php
                        $langKey = config('app.default_language');
                        $categories = \App\Models\Category::isRoot()
                            ->orderBy('sorting_order_level', 'ASC')
                            ->get();
                        $selectedCategories = [];
                    @endphp

                    <div class="space-y-3">
                        <x-backend.inputs.text label="Name" name="name" placeholder="Type brand name"
                            value="" />

                        <x-backend.inputs.file label="Thumbnail Image" name="thumbnail_image"
                            filesHint="This image will be used as thumbnail of the brand. Recommended size: 300*300" />

                        <x-backend.inputs.select label="Brand Categories" name="category_ids[]" multiple
                            data-selection-css-class="multi-select2"
                            data-placeholder="{{ translate('Select categories for this brand') }}" :isRequired="false">
                            @foreach ($categories as $category)
                                <x-backend.inputs.select-option name="{{ $category->collectTranslation('name', $langKey) }}"
                                    value="{{ $category->id }}" />

                                @foreach ($category->childrenCategories as $childCategory)
                                    @include('backend.admin.categories.subCategory', [
                                        'subCategory' => $childCategory,
                                        'langKey' => $langKey,
                                        'category' => null,
                                        'selectedCategories' => $selectedCategories,
                                    ])
                                @endforeach
                            @endforeach
                        </x-backend.inputs.select>

                        <x-backend.inputs.text label="Meta Title" name="meta_title" placeholder="Type meta title"
                            value="" :isRequired="false" />

                        <x-backend.inputs.textarea label="Meta Description" name="meta_description"
                            placeholder="Type meta description" value="" :isRequired="false" />

                        <div class="flex justify-end">
                            <x-backend.inputs.button buttonText="Save Brand" type="submit" />
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
