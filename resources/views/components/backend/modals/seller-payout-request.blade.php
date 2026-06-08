<div class="modal micromodal-slide" id="seller-payout-request-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="seller-payout-request-modal-title">
        <form action="{{ route('seller.earnings.storeRequest') }}" method="POST">
            @csrf
            <input id="seller-payout-request-modal-method" type="hidden" name="_method" value="">
            <header class="modal__header">
                <h2 class="modal__title">
                    {{ translate('Send Payout Request') }}
                </h2>
                <button type="button" class="modal__close ms-2" aria-label="Close modal"
                    data-micromodal-close></button>
            </header>
            <main class="modal__content" id="seller-payout-request-modal-content">
                <x-backend.inputs.number label="Amount" :labelInline="false" name="amount" placeholder="" value=""
                    step="0.001" max="{{ shop()->current_balance }}"
                    min="{{ getSetting('minWithdrawalAmount') ?? 0.001 }}" />

                <div class="mt-3">
                    <x-backend.inputs.textarea label="Note" :labelInline="false" name="note"
                        placeholder="Type few words..." :isRequired="false" />
                </div>
            </main>
            <footer class="modal__footer">
                <button type="submit"
                    class="modal__btn modal__btn-success button button--primary">{{ translate('Send') }}</button>
            </footer>
        </form>
    </div>
</div>
