<div class="modal micromodal-slide" id="purchase-order-payment-histories-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true"
        aria-labelledby="purchase-order-payment-histories-modal-title">

        <input id="purchase-order-payment-modal-method" type="hidden" name="_method" value="">
        <header class="modal__header">
            <h2 class="modal__title">
                {{ translate('Show Payments') }}
            </h2>
            <button type="button" class="modal__close ms-2" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content min-h-[180px] min-w-[220px] lg:min-w-[400px]"
            id="purchase-order-payment-modal-content">
            <div id="purchase-order-payment-histories">

            </div>
            <div class="flex items-center justify-center h-full mt-[100px] loader">
                <!-- Inner Ring -->
                <div
                    class="w-[14px] h-[14px] rounded-full animate-spin
            border-2 border-solid border-black border-t-transparent">
                </div>
            </div>
        </main>
    </div>
</div>
