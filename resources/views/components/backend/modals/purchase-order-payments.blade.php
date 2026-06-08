<div class="modal micromodal-slide" id="purchase-order-payment-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="purchase-order-payment-modal-title">
        <form action="{{ route(routePrefix() . '.purchase-payments.store') }}" method="POST">
            @csrf
            <input id="purchase-order-payment-modal-method" type="hidden" name="_method" value="">
            <header class="modal__header">
                <h2 class="modal__title">
                    {{ translate('Make Payment') }}
                </h2>
                <button type="button" class="modal__close ms-2" aria-label="Close modal"
                    data-micromodal-close></button>
            </header>
            <main class="modal__content" id="purchase-order-payment-modal-content">
                <p id="purchase-order-payment-modal-text">
                    <input type="hidden" name="payable_id" value="">
                    <input type="hidden" name="payable_type" value="">

                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-12 md:col-span-6">
                        <x-backend.inputs.datepicker label="Date" :labelInline="false" name="date"
                            placeholder="Pick a date" value="" />
                    </div>

                    <div class="col-span-12 md:col-span-6">
                        <label class="theme-input-label pt-0 input-required">{{ translate('Payment Method') }}</label>
                        <div class="theme-input-wrapper">
                            <select class="theme-input h-auto p-3" name="payment_method">
                                <option value="cash">{{ translate('Cash') }}</option>
                                <option value="card">{{ translate('Card') }}</option>
                                <option value="bank_transfer">{{ translate('Bank Transfer') }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-span-12">
                        <x-backend.inputs.number label="Payable Amount" :labelInline="false" name="payable_amount"
                            placeholder="" value="" :isRequired="false" :isDisabled="true" />
                    </div>

                    <div class="col-span-12">
                        <x-backend.inputs.number label="Paid Amount" :labelInline="false" name="paid_amount" placeholder=""
                            value="" step="0.001" />
                    </div>

                    <div class="col-span-12">
                        <x-backend.inputs.textarea label="Note" :labelInline="false" name="note"
                            placeholder="Type few words..." :isRequired="false" />
                    </div>

                </div>
                </p>
            </main>
            <footer class="modal__footer">
                <button type="submit"
                    class="modal__btn modal__btn-success button button--primary">{{ translate('Continue') }}</button>
                <button type="button" class="modal__btn ms-3" data-micromodal-close
                    aria-label="Close this dialog window">{{ translate('Close') }}</button>
            </footer>
        </form>
    </div>
</div>
