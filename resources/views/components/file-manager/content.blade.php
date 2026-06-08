<div class="media-manager--wrapper">
    <div class="media-manager__browser active h-full">
        <div class="card__title flex items-center justify-between">
            <h4 class="text-base font-bold">
                {{ translate('Upload Files') }}
            </h4>

            <div class="flex gap-5 items-center">
                <button class="text-lg text-secondary-light md:hidden media-manager__filters--toggler">
                    <i class="fa-regular fa-sliders"></i>
                </button>

                <button class="text-lg toggle-media-manager" data-micromodal-close="media-manager-modal">
                    <i class="fal fa-times"></i>
                </button>
            </div>
        </div>

        <div class="card__content grow flex flex-col">
            <form class="media-manager__filters" id="media-manager__filters">
                <div class="md:hidden bg-primary text-white px-5 py-4 rounded-t-md flex justify-between">
                    <span>{{ translate('FILTER') }}</span>
                    <button type="button" class="text-white media-manager__filters--toggler">
                        <i class="fa-regular fa-times"></i>
                    </button>
                </div>
                <div class="grow lg:max-w-[250px] px-5 !lg:px-0 xl:px-0">
                    <x-backend.inputs.search-input name="media_name" placeholder="Search by name" />
                </div>

                <div class="flex lg:justify-end max-lg:flex-col lg:items-center gap-4 2xl:gap-10 px-5">
                    <x-backend.inputs.select data-search="false" name="order" label="Order"
                        wrapperClass="min-w-[160px]">
                        <x-backend.inputs.select-option value="newest" name="Newest to Oldest" />
                        <x-backend.inputs.select-option value="oldest" name="Oldest to Newest" />
                        <x-backend.inputs.select-option value="smallest" name="Smallest to Largest" />
                        <x-backend.inputs.select-option value="largest" name="Largest to Smallest" />
                    </x-backend.inputs.select>

                    <x-backend.inputs.select groupClass="lg:max-w-[180px] hidden" data-search="false" name="file-type"
                        label="Type">
                        <x-backend.inputs.select-option value="all" name="All" selected="all" />
                        <x-backend.inputs.select-option value="" name="Images" selected="all" />
                        <x-backend.inputs.select-option value="" name="Videos" selected="all" />
                        <x-backend.inputs.select-option value="" name="Audio" selected="all" />
                        <x-backend.inputs.select-option value="" name="Documents" selected="all" />
                    </x-backend.inputs.select>
                </div>
            </form>

            <div class="media-manager">
            </div>
        </div>

        <div class="flex items-center justify-between px-6 py-2 md:py-5 border-b border-[#7878782a]">
            <div class="flex items-center justify-start gap-5 ">
                <x-backend.inputs.button class="media-manager__pagination--prev" variant="dark" buttonText="Prev" />
                <x-backend.inputs.button class="media-manager__pagination--next" buttonText="Next" />
            </div>

            <div>
                <x-backend.inputs.button class="set-files" buttonText="Confirm" />
            </div>
        </div>
    </div>

    <div class="media-manager__uploader">
        <div class="card__title flex items-center justify-between">
            <h4 class="text-base font-bold">
                {{ translate('Upload Files') }}
            </h4>

            <button class="text-lg toggle-media-uploader">
                <i class="fal fa-times"></i>
            </button>
        </div>

        <div class="card__content grow min-h-[300px]">
            <div id="uppy-upload-files" class="h-full mx-auto"></div>
        </div>
    </div>
</div>
