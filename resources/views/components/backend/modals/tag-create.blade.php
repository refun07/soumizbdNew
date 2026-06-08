<div class="modal micromodal-slide" id="tag-create-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="tag-create-modal-title">

        <header class="modal__header">
            <h2 class="modal__title">
                {{ translate('Add New Tag') }}
            </h2>
            <button type="button" class="modal__close ms-2" aria-label="Close modal" data-micromodal-close></button>
        </header>
        <main class="modal__content min-h-[100px] w-full">
            <div id="tag-create">
                <form action="" method="POST" class="tag-form-modal">
                    @csrf
                    @php
                        $langKey = config('app.default_language');
                    @endphp
                    <div class="space-y-3">
                        <x-backend.inputs.text label="Name" name="name" placeholder="Type tag name"
                            value="" />

                        <div class="flex justify-end">
                            <x-backend.inputs.button buttonText="Save Tag" type="submit" />
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
