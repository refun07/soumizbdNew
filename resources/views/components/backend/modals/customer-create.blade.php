<div class="modal micromodal-slide" id="customer-create-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="customer-create-modal-title">

        <header class="modal__header">
            <h2 class="modal__title">
                {{ translate('Add New Customer') }}
            </h2>
            <button type="button" class="modal__close ms-2" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content min-h-[180px] w-full">
            <div id="customer-create">
                <form action="{{ route('admin.customers.store') }}" method="POST" class="customer-create-form-modal">
                    @csrf

                    <div id="customer-create-error" class="hidden mb-3 p-3 bg-red-100 text-red-700 rounded-lg text-sm"></div>

                    <div class="space-y-3">
                        <x-backend.inputs.text label="Name" name="name" placeholder="Type customer name"
                            value="" :isRequired="true" />

                        <x-backend.inputs.text label="Email" name="email" placeholder="Type email address"
                            value="" :isRequired="false" />

                        <x-backend.inputs.text label="Phone" name="phone" placeholder="Type phone number"
                            value="" :isRequired="false" />

                        <x-backend.inputs.text label="Password" name="password" placeholder="Type password (default: 123456)"
                            value="" type="password" :isRequired="false" />

                        <div class="flex justify-end">
                            <x-backend.inputs.button buttonText="Save Customer" type="submit" />
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
