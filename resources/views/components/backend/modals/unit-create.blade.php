<div class="modal micromodal-slide" id="unit-create-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="unit-create-modal-title">

        <input id="purchase-order-payment-modal-method" type="hidden" name="_method" value="">
        <header class="modal__header">
            <h2 class="modal__title">
                {{ translate('Add New Unit') }}
            </h2>
            <button type="button" class="modal__close ms-2" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content min-h-[180px] w-full">
            <div id="unit-create">
                <form action="" method="POST" class="unit-form-modal">
                    @csrf
                    @php
                        $langKey = config('app.default_language');
                    @endphp

                    <div class="space-y-3">
                        <x-backend.inputs.text label="Name" name="name" placeholder="Type unit name" />

                        <div class="flex justify-end">
                            <x-backend.inputs.button buttonText="Save Unit" type="submit" />
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
