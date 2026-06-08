@props([
    'modalId' => 'product-import-modal',
    'formAction',
    'sampleUrl',
])

<div class="modal micromodal-slide" id="{{ $modalId }}" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container w-full max-w-[720px]" role="dialog" aria-modal="true"
        aria-labelledby="{{ $modalId }}-title">
        <header class="modal__header border-b border-border pb-4">
            <div>
                <h2 class="modal__title" id="{{ $modalId }}-title">
                    {{ translate('Import Products') }}
                </h2>
                <p class="text-sm text-muted mt-1">
                    {{ translate('Upload a CSV to create simple catalog products in bulk.') }}
                </p>
            </div>
            <button type="button" class="modal__close ms-2" aria-label="Close modal" data-micromodal-close></button>
        </header>

        <main class="modal__content w-full !mt-5">
            <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data"
                class="product-import-form space-y-5">
                @csrf

                <label
                    class="product-import-dropzone group block cursor-pointer rounded-xl border-2 border-dashed border-primary/25 bg-primary/5 px-5 py-8 text-center transition hover:border-primary/50 hover:bg-primary/10">
                    <span
                        class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-full bg-white text-2xl text-primary shadow-sm ring-1 ring-primary/10">
                        <i class="fa-solid fa-file-csv"></i>
                    </span>
                    <span class="block text-base font-semibold text-foreground">
                        {{ translate('Choose product CSV') }}
                    </span>
                    <span class="product-import-file-name mt-1 block text-sm text-muted">
                        {{ translate('Drag a CSV here, or click to browse') }}
                    </span>
                    <span class="mt-3 inline-flex items-center rounded-full bg-white px-3 py-1 text-xs font-medium text-muted ring-1 ring-border">
                        {{ translate('CSV only') }} · {{ translate('Max 10MB') }}
                    </span>
                    <input type="file" name="csv_file" accept=".csv,text/csv" class="sr-only product-import-file"
                        required>
                </label>

                <div class="grid gap-3 md:grid-cols-2">
                    <div class="rounded-xl border border-border bg-background p-4">
                        <div class="mb-3 flex items-center justify-between gap-3">
                            <h3 class="font-semibold">{{ translate('Required Columns') }}</h3>
                            <a href="{{ $sampleUrl }}" class="text-sm font-medium text-primary hover:underline">
                                <i class="fa-solid fa-download mr-1"></i>{{ translate('Sample CSV') }}
                            </a>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">
                                name
                            </span>
                            <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-600">
                                price
                            </span>
                        </div>
                        <p class="mt-3 text-xs leading-5 text-muted">
                            {{ translate('Optional columns include sku, stock_qty, description, categories, brand, unit, discount fields, tags, badges, SEO fields, and shipping_class.') }}
                        </p>
                        <p class="mt-2 text-xs leading-5 text-muted">
                            {{ translate('Use comma-separated names for multiple categories, tags, or badges, for example: Men,T-Shirts.') }}
                        </p>
                    </div>

                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                        <h3 class="font-semibold">
                            <i class="fa-solid fa-circle-info mr-1"></i>{{ translate('Inventory Behavior') }}
                        </h3>
                        <p class="mt-2 text-xs leading-5">
                            {{ translate('When inventory is enabled, imported products are created with zero stock. Add stock later by receiving a purchase order. When inventory is disabled, stock_qty is added to the default warehouse.') }}
                        </p>
                    </div>
                </div>

                <div class="rounded-xl bg-slate-50 p-4">
                    <h3 class="mb-3 text-sm font-semibold text-slate-800">{{ translate('Import Flow') }}</h3>
                    <div class="grid gap-3 text-sm sm:grid-cols-3">
                        <div class="flex items-start gap-2">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs text-white">1</span>
                            <span>{{ translate('Download sample') }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs text-white">2</span>
                            <span>{{ translate('Fill product rows') }}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-primary text-xs text-white">3</span>
                            <span>{{ translate('Upload and review') }}</span>
                        </div>
                    </div>
                </div>

                <footer class="modal__footer flex flex-wrap items-center justify-end gap-3 pt-1">
                    <button type="button" class="button button--secondary" data-micromodal-close>
                        {{ translate('Cancel') }}
                    </button>
                    <button type="submit" class="button button--primary">
                        <i class="fa-solid fa-upload mr-1"></i>{{ translate('Import Products') }}
                    </button>
                </footer>
            </form>
        </main>
    </div>
</div>

<script>
    document.addEventListener('change', function(event) {
        if (!event.target.classList.contains('product-import-file')) {
            return;
        }

        var dropzone = event.target.closest('.product-import-dropzone');
        var fileName = dropzone ? dropzone.querySelector('.product-import-file-name') : null;
        if (fileName) {
            fileName.textContent = event.target.files.length
                ? event.target.files[0].name
                : '{{ translate('Drag a CSV here, or click to browse') }}';
        }
    });
</script>
