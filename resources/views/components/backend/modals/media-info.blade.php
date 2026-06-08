<div class="modal micromodal-slide z-[4]" id="media-info-modal" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close></div>
    <div class="modal__container card z-[1] p-0 w-full max-w-[400px]" role="dialog" aria-modal="true"
        aria-labelledby="media-info">
        <div class="card__title flex items-center justify-between px-4 py-4">
            <h3>{{ translate('Image Details') }}</h3>
            <button class="text-lg toggle-media-manager" data-micromodal-close="media-info-modal">
                <i class="fal fa-times"></i>
            </button>
        </div>

        <form class="p-5" id="media-info-form">

        </form>
    </div>
</div>
