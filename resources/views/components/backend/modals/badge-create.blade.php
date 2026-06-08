<div class="modal micromodal-slide" id="badge-create-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="badge-create-modal-title">
 
        <header class="modal__header">
            <h2 class="modal__title">
                {{ translate('Add New Badge') }}
            </h2>
            <button type="button" class="modal__close ms-2" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content min-h-[100px] w-full">
            <div id="badge-create">
                <form action="" method="POST" class="badge-form-modal">
                    @csrf
                    @php
                        $langKey = config('app.default_language');
                    @endphp
                    <div class="space-y-3">
                        <x-backend.inputs.text label="Name" name="name" placeholder="Type badge name"
                            value="" />
                        <x-backend.inputs.color label="Text Color" name="color" value="#ffffff" />
                        <x-backend.inputs.color label="Background Color" name="bg_color" value="#000000"
                            class="py-5" />

                        <div class="flex justify-end">
                            <x-backend.inputs.button buttonText="Save Badge" type="submit" />
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
