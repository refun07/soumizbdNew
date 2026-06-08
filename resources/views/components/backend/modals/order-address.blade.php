<div class="modal micromodal-slide" id="order-address-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="order-address-modal-title">

        <form action="{{ route(routePrefix() . '.orders.updateOrderAddress') }}" class="space-y-3" method="POST">
            @csrf
            <header class="modal__header">
                <h2 class="modal__title" id="order-address-modal-title"></h2>
                <button type="button" class="modal__close ms-2" aria-label="Close modal"
                    data-micromodal-close></button>
            </header>
            <main class="modal__content pt-4" id="order-address-modal-content">
                <input type="hidden" name="id" value="" class="order-id">
                <input type="hidden" name="type" value="" class="address-type">

                <x-backend.inputs.textarea label="Address" name="address" placeholder="Type full address" value=""
                    class="address" />

                <div class="address-info hidden mt-3">

                    <x-backend.inputs.text label="Phone" name="phone" placeholder="Type phone number" value=""
                        :isRequired="false" class="mb-3" />

                    <x-backend.inputs.textarea label="Direction" name="direction" placeholder="Type direction"
                        value="" :isRequired="false" class="mt-4" />

                </div>

                <div class="text-end mt-4">
                    <x-backend.inputs.button type="submit" variant="warning" class="!bg-[#FF7E06]"
                        buttonText="Update" />
                </div>
            </main>
        </form>
    </div>
</div>
