<div class="modal micromodal-slide" id="variant-conversion-warning-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>

    <div class="modal__container w-full max-w-[500px] !py-5 !px-8" role="dialog" aria-modal="true"
        aria-labelledby="variant-conversion-warning-title">
        <form id="variant-conversion-form" onsubmit="return false;">
            @csrf

            <header class="modal__header pb-4 border-b border-border">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                    <p class="text-foreground text-base font-medium">{{ translate('Warning: Data Loss Risk') }}</p>
                </div>
                <button type="button" class="modal__close ms-2" aria-label="Close modal"
                    data-micromodal-close></button>
            </header>

            <main class="modal__content !mt-5" id="variant-conversion-warning-content">
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <p class="text-red-800 text-sm leading-relaxed">
                        {{ translate('Converting this product to non-variant will permanently delete all existing product variations, their stock data, pricing information, and associated SKU codes.') }}
                    </p>
                </div>
                <p class="text-slate-600 mt-3 text-sm">
                    {{ translate('This action cannot be undone. All variant-specific data including:') }}
                </p>
                <ul class="list-disc list-inside mt-2 text-sm text-slate-600 space-y-1 ml-2">
                    <li>{{ translate('All product variations and combinations') }}</li>
                    <li>{{ translate('Variant-specific pricing and discounts') }}</li>
                    <li>{{ translate('Stock quantities per variant') }}</li>
                    <li>{{ translate('SKU codes for each variant') }}</li>
                </ul>
                <p class="text-slate-800 font-medium mt-4">
                    {{ translate('Are you sure you want to continue?') }}
                </p>
            </main>

            <footer class="modal__footer mb-5 mt-6 flex items-center justify-end gap-3">
                <button type="button"
                    class="modal__btn bg-secondary-light hover:bg-gray-600 leading-none text-white px-6 py-2.5 rounded uppercase transition-colors"
                    data-micromodal-close
                    aria-label="Close this dialog window"
                    id="variant-conversion-cancel">
                    {{ translate('No, Keep Variations') }}
                </button>
                <button type="button" id="variant-conversion-confirm"
                    class="modal__btn bg-red-500 hover:bg-red-600 leading-none text-white px-6 py-2.5 rounded uppercase transition-colors">
                    {{ translate('Yes, Convert') }}
                </button>
            </footer>
        </form>
    </div>
</div>
