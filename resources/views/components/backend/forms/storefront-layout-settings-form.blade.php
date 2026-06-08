@php
    $desktopSideSpacing = (int) getSetting('storefrontDesktopSideSpacing', 14);
    $desktopSideSpacing = max(0, min(800, $desktopSideSpacing));
@endphp

{{-- storefront layout settings --}}
<div class="ac !mt-0">
    <h2 class="ac-header">
        <button type="button" class="ac-trigger !px-8 !rounded-none !bg-background !text-foreground">
            <span class="">{{ translate('Storefront Layout') }}</span>
        </button>
    </h2>
    <div class="ac-panel">
        <div class="pt-0 pb-7 px-3 md:px-6 xl:px-10">
            <form action="" class="space-y-3 homepage-form" method="POST">
                @csrf

                <input type="hidden" name="types[]" value="storefrontDesktopSideSpacing">
                <div>
                    <label for="storefrontDesktopSideSpacing" class="theme-input-label pt-0">
                        {{ translate('Desktop Left & Right Spacing') }}
                    </label>
                    <div class="theme-input-wrapper">
                        <input
                            type="number"
                            id="storefrontDesktopSideSpacing"
                            name="storefrontDesktopSideSpacing"
                            class="theme-input"
                            min="0"
                            max="800"
                            step="1"
                            value="{{ $desktopSideSpacing }}"
                            placeholder="14"
                        />
                    </div>
                    <p class="text-xs text-muted mt-1">
                        {{ translate('Controls the actual left and right screen space on tablet and desktop only. Mobile keeps the default spacing.') }}
                    </p>
                </div>

                <div class="flex justify-end pt-2">
                    <x-backend.inputs.button type="submit" buttonText="{{ translate('Save Layout Settings') }}"
                        class="submit-btn" />
                </div>
            </form>
        </div>
    </div>
</div>
