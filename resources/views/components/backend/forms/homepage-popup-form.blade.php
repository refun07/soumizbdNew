{{-- homepage popup (theme-independent) --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Homepage Popup') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="types[]" value="showHomepagePopup">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Show Homepage Popup') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="showHomepagePopup" value="1"
                        isChecked="{{ getSetting('showHomepagePopup') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="homepagePopupImage">
                <x-backend.inputs.file :labelInline="false" label="Popup Image" name="homepagePopupImage"
                    value="{{ getSetting('homepagePopupImage') }}" class="mb-3" />
                <p class="text-xs text-gray-500 mt-1">Recommended: 600x600 pixels</p>

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homepagePopupLink">
                <x-backend.inputs.text :labelInline="false" label="Popup Link (Optional)" name="homepagePopupLink"
                    value="{{ getSetting('homepagePopupLink') ?? '' }}" :isRequired="false"
                    placeholder="https://www.example.com" />

                <div class="mt-5"></div>
                <input type="hidden" name="types[]" value="homepagePopupFullSize">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Full Popup Size') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="homepagePopupFullSize" value="1"
                        isChecked="{{ getSetting('homepagePopupFullSize') ?? false }}" />
                </div>

                <input type="hidden" name="types[]" value="homepagePopupAutoHideEnabled">
                <div class="flex items-center justify-between mb-5">
                    <p>{{ translate('Enable Auto Hide') }}</p>
                    <x-backend.inputs.checkbox toggler="true" name="homepagePopupAutoHideEnabled" value="1"
                        isChecked="{{ getSetting('homepagePopupAutoHideEnabled') ?? true }}" />
                </div>

                <input type="hidden" name="types[]" value="homepagePopupDuration">
                <x-backend.inputs.number :labelInline="false" label="Popup Duration (Seconds)" name="homepagePopupDuration"
                    value="{{ getSetting('homepagePopupDuration') ?? 5 }}" :isRequired="false" min="1"
                    placeholder="5" />

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Changes') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>
