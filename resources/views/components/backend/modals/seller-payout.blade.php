<div class="modal micromodal-slide" id="seller-payout-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="seller-payout-modal-title">
        <form action="{{ route('admin.sellers.makePayment') }}" method="POST">
            @csrf
            <input id="seller-payout-modal-method" type="hidden" name="_method" value="">
            <header class="modal__header">
                <h2 class="modal__title">
                    {{ translate('Make Payment') }}
                </h2>
                <button type="button" class="modal__close ms-2" aria-label="Close modal"
                    data-micromodal-close></button>
            </header>
            <main class="modal__content" id="seller-payout-modal-content">

                <input type="hidden" name="id" value="">
                <input type="hidden" name="shop_id" value="">
                <div>
                    <table class="w-[350px] my-4">
                        <tbody>
                            <tr
                                class="bg-background-hover border-b border-border transition duration-300 ease-in-out hover:bg-background">

                                <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                                    {{ translate('Due To Seller') }}
                                </td>
                                <td
                                    class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap due">
                                    0
                                </td>
                            </tr>
                            <tr
                                class="bg-background-hover border-b border-border transition duration-300 ease-in-out hover:bg-background">

                                <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                                    {{ translate('Requested Amount') }}
                                </td>
                                <td
                                    class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap demanded">
                                    0
                                </td>
                            </tr>
                            <div class="bank-details hidden">
                                <tr
                                    class="bg-background-hover border-b border-border transition duration-300 ease-in-out hover:bg-background ">

                                    <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                                        {{ translate('Bank Name') }}
                                    </td>
                                    <td
                                        class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap bank-name">
                                    </td>
                                </tr>
                                <tr
                                    class="bg-background-hover border-b border-border transition duration-300 ease-in-out hover:bg-background ">

                                    <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                                        {{ translate('Bank Acc Name') }}
                                    </td>
                                    <td
                                        class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap bank-acc-name">
                                    </td>
                                </tr>
                                <tr
                                    class="bg-background-hover border-b border-border transition duration-300 ease-in-out hover:bg-background ">

                                    <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                                        {{ translate('Bank Acc Number') }}
                                    </td>
                                    <td
                                        class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap bank-acc-no">
                                    </td>
                                </tr>
                                <tr
                                    class="bg-background-hover border-b border-border transition duration-300 ease-in-out hover:bg-background ">

                                    <td class="px-2 py-1 whitespace-nowrap text-[13px] font-medium text-foreground">
                                        {{ translate('Bank Routing Number') }}
                                    </td>
                                    <td
                                        class="text-[13px] text-end text-foreground font-light px-2 py-1 whitespace-nowrap bank-routing-no">
                                    </td>
                                </tr>
                            </div>

                        </tbody>
                    </table>
                </div>

                <x-backend.inputs.number label="Amount" :labelInline="false" name="amount" placeholder="" value=""
                    step="0.001" value="" />

                <div class="my-3">
                    <label class="theme-input-label pt-0 input-required">{{ translate('Payment Method') }}</label>
                    <div class="theme-input-wrapper">
                        <select class="theme-input h-auto p-3" name="payment_method" required>
                            <option value="">{{ translate('Select payment method') }}</option>
                            <option value="cash" class="hidden cash">{{ translate('Cash') }}</option>
                            <option value="bank" class="hidden bank">{{ translate('Bank Transfer') }}</option>
                        </select>
                    </div>
                </div>

                <div class="payment-details hidden">
                    <x-backend.inputs.text label="Transaction Id" :labelInline="false" name="payment_details"
                        placeholder="" :isRequired="false" />
                </div>
            </main>
            <footer class="modal__footer">
                <button type="submit"
                    class="modal__btn modal__btn-success button button--primary">{{ translate('Pay Now') }}</button>
            </footer>
        </form>
    </div>
</div>
