<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span>{{ translate('Review Section') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf
                @foreach (['themeOneReviewAvatars', 'themeOneReviewNames', 'themeOneReviewDesignations', 'themeOneReviewTexts', 'themeOneReviewRatings', 'showThemeOneReviewSection'] as $type)
                    <input type="hidden" name="types[]" value="{{ $type }}">
                @endforeach
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Review Section') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showThemeOneReviewSection" value="1"
                        isChecked="{{ getSetting('showThemeOneReviewSection') ?? false }}" />
                </div>
                @php
                    $avatars = json_decode(getSetting('themeOneReviewAvatars') ?: '[]', true) ?: [];
                    $names = json_decode(getSetting('themeOneReviewNames') ?: '[]', true) ?: [];
                    $titles = json_decode(getSetting('themeOneReviewDesignations') ?: '[]', true) ?: [];
                    $texts = json_decode(getSetting('themeOneReviewTexts') ?: '[]', true) ?: [];
                    $ratings = json_decode(getSetting('themeOneReviewRatings') ?: '[]', true) ?: [];
                @endphp
                <div class="space-y-5">
                    @for ($i = 0; $i < max(3, count($avatars)); $i++)
                        <div class="rounded border border-border p-4 space-y-3">
                            <x-backend.inputs.file :labelInline="false" :trashBtn="true" label="Image"
                                name="themeOneReviewAvatars[]" value="{{ $avatars[$i] ?? '' }}" />
                            <x-backend.inputs.text :labelInline="false" label="Name" name="themeOneReviewNames[]"
                                value="{{ $names[$i] ?? '' }}" :isRequired="false" />
                            <x-backend.inputs.text :labelInline="false" label="Title" name="themeOneReviewDesignations[]"
                                value="{{ $titles[$i] ?? '' }}" :isRequired="false" />
                            <x-backend.inputs.text :labelInline="false" label="Review" name="themeOneReviewTexts[]"
                                value="{{ $texts[$i] ?? '' }}" :isRequired="false" />
                            <x-backend.inputs.number min="1" max="5" :labelInline="false" label="Rating"
                                name="themeOneReviewRatings[]" value="{{ $ratings[$i] ?? 5 }}" :isRequired="false" />
                        </div>
                    @endfor
                </div>
                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}" class="submit-btn mt-3" />
                </div>
            </form>
        </div>
    </div>
</div>
