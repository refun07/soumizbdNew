<div class="modal micromodal-slide" id="refund-payment-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="refund-payment-modal-title">
        <form action="{{ route('admin.return-requests.refund') }}" method="POST">
            @csrf
            <input id="refund-payment-modal-method" type="hidden" name="_method" value="">
            <input type="hidden" name="id">
            <header class="modal__header">
                <h2 class="modal__title uppercase">
                    {{ translate('Invoice NO') }}: {{ getSetting('orderCodePrefix') }}<span id="invoice-number"></span>
                </h2>
                <button type="button" class="modal__close ms-2" aria-label="Close modal"
                    data-micromodal-close></button>
            </header>
            <main class="modal__content" id="refund-payment-modal-content">
                <table class="refunt-payment-table footable w-full">
                    <thead class="uppercase text-left bg-primary/10">
                        <tr>
                            <th>
                                {{ translate('Total Amount') }}
                            </th>
                            <th>
                                {{ translate('Refund Amount') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p id="total-amount" class="text-lg"></p>
                            </td>
                            <td><x-backend.inputs.number :labelInline="false" name="amount" placeholder=""
                                    value="" /></td>
                        </tr>
                    </tbody>
                </table>

            </main>
            <footer class="modal__footer flex justify-end">
                <button type="submit"
                    class="modal__btn modal__btn-success button button--primary">{{ translate('Confirm Refund') }}</button>
            </footer>
        </form>
    </div>
</div>
