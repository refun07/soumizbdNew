<div class="modal micromodal-slide" id="confirm-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>

    <div class="modal__container w-full max-w-[400px] !py-5 !px-8" role="dialog" aria-modal="true"
        aria-labelledby="confirm-modal-title">
        <form action="" method="POST">
            @csrf
            <input id="confirm-modal-method" type="hidden" name="_method" value="">

            <header class="modal__header pb-4 border-b border-border">
                <p class="text-foreground text-base font-medium">Confirmation</p>
                <button type="button" class="modal__close ms-2" aria-label="Close modal"
                    data-micromodal-close></button>
            </header>

            <main class="modal__content !mt-5" id="confirm-modal-content">
                <h2 class="modal__title !text-lg md:!text-2xl !font-medium !text-foreground" id="confirm-modal-title">
                </h2>
                <p id="confirm-modal-text" class="text-slate-400 mt-2"></p>
            </main>

            <footer class="modal__footer mb-5">
                <button type="submit" id="confirm-modal-submit"
                    class="modal__btn modal__btn-success bg-red-500 leading-none text-white px-10 py-2.5 rounded uppercase">{{ translate('Delete') }}</button>
                <button
                    class="modal__btn bg-secondary-light leading-none text-white px-10 py-2.5 rounded uppercase ml-2"
                    type="button" data-micromodal-close
                    aria-label="Close this dialog window">{{ translate('Cancel') }}</button>
            </footer>
        </form>
    </div>
</div>
